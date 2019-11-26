<?php

/*
 * This file is part of guojiangclub/activity-backend.
 *
 * (c) 果酱社区 <https://guojiang.club>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GuoJiangClub\Activity\Backend\Http\Controllers;

use Carbon\Carbon;
use DB;
use GuoJiangClub\Component\Payment\Models\PaymentLog;
use GuoJiangClub\Component\Payment\Services\RefundService;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use GuoJiangClub\Activity\Core\Models\ActivityRefundLog;
use GuoJiangClub\Activity\Core\Models\Refund;
use GuoJiangClub\Activity\Core\Repository\ActivityRefundRepository;
use iBrand\Backend\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Response;

class ActivityRefundController extends Controller
{
    protected $refundRepository;
    protected $paymentRefund;

    public function __construct(ActivityRefundRepository $refundRepository,
                                RefundService $paymentService)
    {
        $this->refundRepository = $refundRepository;
        $this->paymentRefund = $paymentService;
    }

    public function index()
    {
        $view = request('status');
        $where = [];
        if (empty($view)) {
            $view = 0;
        }

        $value = '';
        if (!empty(request('value'))) {
            if ('order_no' == request('field')) {
                $value = request('value');
            } else {
                $where['refund_no'] = ['like', '%'.request('value').'%'];
            }
        }

        $refunds = $this->refundRepository->getRefundsPaginated($view, $where, $value);

        return Admin::content(function (Content $content) use ($refunds) {
            $content->description('退款申请列表');

            $content->breadcrumb(
                ['text' => '活动管理', 'url' => 'activity', 'no-pjax' => 1],
                ['text' => '退款申请列表', 'no-pjax' => 1, 'left-menu-active' => '退款管理']
            );

            $view = view('activity::refund.index', compact('refunds'))->render();
            $content->row($view);
        });
    }

    public function show($id)
    {
        $refund = $this->refundRepository->find($id);

        return Admin::content(function (Content $content) use ($refund) {
            $content->description('退款申请列表');

            $content->breadcrumb(
                ['text' => '活动管理', 'url' => 'activity', 'no-pjax' => 1],
                ['text' => '退款详情', 'no-pjax' => 1, 'left-menu-active' => '退款管理']
            );

            $view = view('activity::refund.show', compact('refund'))->render();
            $content->row($view);
        });
    }

    /**
     * 申请处理.
     */
    public function store()
    {
        $status = request('status');
        $id = request('id');
        $uid = auth()->guard('admin')->user()->id;

        $refund = $this->refundRepository->find($id);

        try {
            DB::beginTransaction();
            //审核处理
            if (0 == $status) {
                $opinion = request('opinion');
                $remarks = request('remarks');
                $this->handleRefund($id, $opinion, $uid, $remarks);
            }

            //商家打款处理
            if (8 == $status) {
                $remarks = request('remarks');
                $this->handlePaid($id, $uid, $remarks);
            }

            DB::commit();

            return response()->json(['status' => true, 'error_code' => 0, 'error' => '', 'data' => $refund->id,
            ]);
        } catch (\Exception $exception) {
            DB::rollBack();

            return $this->ajaxJson(false, [], 404, '提交失败');
        }
    }

    /**
     ** 后台处理退换货操作.
     *
     * @param $id      integer 申请ID
     * @param $opinion integer 1：审核通过 |2：拒绝
     * @param $adminID integer 管理员ID
     * @param $remarks string 处理说明
     */
    public function handleRefund($id, $opinion, $adminID, $remarks)
    {
        $refund = $this->refundRepository->find($id);
        $action = 1 == $opinion ? 'agree' : 'refuse';
        $note = 1 == $opinion ? '同意用户申请' : '拒绝用户申请';

        //更新申请状态
        $refund->status = $opinion;
        $refund->save();

        //写入日志
        $this->refundLog($id, 0, $adminID, $action, $note, $remarks);

        if (1 == $opinion) {
            $refund->status = Refund::STATUS_SHOP_PAID;
            $refund->save();
        }

        //event('refund.service.changed', [$refund, $note]);
    }

    /**
     * 商家确认打款.
     *
     * @param $id
     * @param $uid
     * @param $remarks
     */
    public function handlePaid($id, $uid, $remarks)
    {
        $refund = $this->refundRepository->find($id);

        $order = $refund->order;

        $paymentLog = PaymentLog::where('order_no', $order->order_no)
            ->where(function ($query) {
                $query->where('action', 'result_pay')->orWhere('action', 'query_result_pay');
            })->get()->last();

        if (!$paymentLog) {
            return [];
        }

        $this->paymentRefund->createPaymentRefundLog('apply_refund', Carbon::now(), $refund->refund_no, $order->order_no, '', $refund->amount, $paymentLog->channel, 'activity', 'SUCCESS', []);

        $description = '活动订单：'.$refund->order->order_no.'取消报名退款';
        $result = $this->paymentRefund->createRefund($order->order_no, $paymentLog->transcation_order_no, $refund->refund_no, $paymentLog->amount, $refund->amount, $paymentLog->channel, $description, 'activity');

        if (0 == count($result)) {
            throw new \Exception('退款失败');
        }
        $this->paymentRefund->createPaymentRefundLog('create_refund', Carbon::now(), $result['refund_no'], $result['order_no'], $result['refund_id'], $result['amount'], $result['channel'], $result['type'], 'SUCCESS', $result['meta']);

        $refund->status = 3;    //确认打款,申请完成
        $refund->paid_time = Carbon::now();
        $refund->save();
        $this->refundLog($refund->id, 0, $uid, 'receipt', '已退款，本次申请已完成', $remarks);

        /*event('refund.service.changed', [$refund, '已退款，本次申请已完成']);*/
    }

    public function refundLog($refund_id, $user_id, $admin_id, $action, $note, $remarks = '')
    {
        return ActivityRefundLog::create([
            'refund_id' => $refund_id,
            'user_id' => $user_id,
            'admin_id' => $admin_id,
            'action' => $action,
            'note' => $note,
            'remark' => $remarks,
        ]);
    }

    public function export()
    {
        if ('all' == request('refund_status')) {
            $applies = Refund::orderBy('status', 'asc')->orderBy('id', 'desc')->get();
        } else {
            if (2 == request('refund_status')) {
                $applies = Refund::whereIn('status', [3, 2])->orderBy('status', 'asc')->orderBy('id', 'desc')->get();
            } else {
                $applies = Refund::where('status', 1 == request('refund_status') ? 8 : 0)->orderBy('status', 'asc')->orderBy('id', 'desc')->get();
            }
        }

        $data = [];
        if (count($applies)) {
            foreach ($applies as $apply) {
                $user_info = $apply->user;
                if ($user_info->nick_name) {
                    $user_name = $user_info->nick_name;
                } elseif ($user_info->name) {
                    $user_name = $user_info->name;
                } else {
                    $user_name = $user_info->mobile;
                }

                $data[] = [
                    'refund_no' => isset($apply->refund_no) ? $apply->refund_no : '',
                    'order_no' => isset($apply->order->order_no) ? $apply->order->order_no : '',
                    'user_name' => $user_name,
                    'reason' => $apply->reason,
                    'typeText' => '退款',
                    'amount' => $apply->amount > 0 ? $apply->amount / 100 : 0,
                    'created_at' => $apply->created_at,
                    'status' => $apply->StatusText,
                    'activity' => isset($apply->order->activity->title) ? $apply->order->activity->title : '',
                    'activity_id' => isset($apply->order->activity->id) ? $apply->order->activity->id : '',
                ];
            }
        }

        $title = [
            '申请编号',
            '订单编号',
            '申请用户',
            '申请原因',
            '申请类型',
            '申请金额',
            '申请时间',
            '处理状态',
            '活动名称',
            '活动ID',
        ];
        $name = 'refund_apply_'.date('Y_m_d_H_i_s', time());
        Excel::create($name, function ($excel) use ($data, $title) {
            $excel->sheet('退款申请列表', function ($sheet) use ($data, $title) {
                $sheet->prependRow(1, $title);
                $sheet->rows($data);
                $sheet->setWidth([
                    'A' => 30,
                    'B' => 30,
                    'C' => 10,
                    'D' => 20,
                    'E' => 20,
                    'F' => 10,
                    'G' => 30,
                    'H' => 10,
                ]);
            });
        })->store('xls');

        return Response::download(storage_path().'/exports/'.$name.'.xls');
    }
}
