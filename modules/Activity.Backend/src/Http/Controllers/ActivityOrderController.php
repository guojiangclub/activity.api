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
use ElementVip\Component\User\Models\User;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use Excel;
use GuoJiangClub\Activity\Backend\Models\Activity;
use GuoJiangClub\Activity\Backend\Models\DiscountCoupon;
use GuoJiangClub\Activity\Core\Models\Answer;
use GuoJiangClub\Activity\Core\Models\Member;
use GuoJiangClub\Activity\Core\Models\Payment;
use GuoJiangClub\Activity\Core\Notifications\Join;
use GuoJiangClub\Activity\Core\Notifications\Signed;
use GuoJiangClub\Activity\Core\Repository\CouponRepository;
use GuoJiangClub\Activity\Core\Repository\DiscountRepository;
use GuoJiangClub\Activity\Server\Services\ActivityService;
use iBrand\Backend\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Response;

class ActivityOrderController extends Controller
{
    protected $activityService;
    protected $discountRepository;
    protected $couponRepository;

    public function __construct(ActivityService $activityService, DiscountRepository $discountRepository, CouponRepository $couponRepository)
    {
        $this->activityService = $activityService;
        $this->discountRepository = $discountRepository;
        $this->couponRepository = $couponRepository;
    }

    public function activityOrderDetail($id)
    {
        $member = Member::find($id);
        $coach = Member::where('activity_id', $member->activity_id)
            ->where('role', 'coach')
            ->get()
            ->toArray();

        $user = array_column($coach, 'user');
        $user = array_column($user, 'name');
        $coach = implode(' ,', $user);

        return Admin::content(function (Content $content) use ($member, $coach) {
            $content->description('活动管理');

            $content->breadcrumb(
                ['text' => '活动管理', 'url' => 'activity', 'no-pjax' => 1],
                ['text' => '订单详情', 'no-pjax' => 1, 'left-menu-active' => '订单列表']
            );

            $view = view('activity::activityOrder.activityOrderDetail', compact('member', 'coach'))->render();
            $content->row($view);
        });
    }

    public function index()
    {
        $where[] = ['role', '=', 'user'];
        $status = request('status');
        if (request('field') && request('value')) {
            $where[] = [request('field'), 'like', '%'.request('value').'%'];
        }

        if (request('activity_id')) {
            $where[] = ['activity_id', '=', request('activity_id')];
        }

        if (request('payment_id')) {
            $where[] = ['payment_id', '=', request('payment_id')];
        }

        if (request('etime')) {
            $where[] = ['created_at', '<=', request('etime')];
        }

        if (request('stime')) {
            $where[] = ['created_at', '>=', request('stime')];
        }

        if (1 == request('excel') || 2 == request('excel') || 3 == request('excel')) {
            if (1 == request('excel')) {
                $where[] = ['status', '=', $status];
            }

            $orders = $this->getData(new Member(), $where, 0);
            $data = [];
            if (count($orders)) {
                if (3 == request('excel')) {
                    $activity = Activity::find(request('activity_id'));
                    if ($activity && isset($activity->form) && $activity->form) {
                        $fields = json_decode($activity->form->fields, true);
                    }
                }

                foreach ($orders as $order) {
                    $payment = Payment::find($order->payment_id);
                    $activity = Activity::find($order->activity_id);
                    $statusText = '待支付';
                    if (1 == $order->status) {
                        $statusText = '已报名';
                    } elseif (2 == $order->status) {
                        $statusText = '已签到';
                    } elseif (3 == $order->status) {
                        $statusText = '已取消';
                    } elseif (4 == $order->status) {
                        $statusText = '待审核';
                    }

                    $amount = 0;
                    $point = 0;
                    if ($order->payment_id && $payment) {
                        switch ($payment->type) {
                            case 0:
                                $point = $payment->point;
                                break;
                            case 1:
                                /*$amount = number_format($payment->price, 2, '.', '') . ' 元';*/
                                $amount = $payment->price;
                                break;
                            case 2:
                                $point = $payment->point;
                                /*$amount = number_format($payment->price, 2, '.', '') . ' 元';*/
                                $amount = $payment->price;
                                break;
                        }
                    }

                    $tmpArr = [
                        'activity_id' => $order->activity_id,
                        'activity_title' => $activity->title,
                        'status' => $statusText,
                        'joined_at' => $order->joined_at,
                        'signed_at' => $order->signed_at,
                        'cancel_at' => $order->cancel_at,
                        'payment_title' => isset($payment->title) ? $payment->title : '',
                        'amount' => $amount,
                        'point' => $point,
                        'order_no' => $order->order_no,
                    ];

                    if (3 == request('excel') && !empty($fields)) {
                        $answer = Answer::where('user_id', $order->user_id)->where('activity_id', $order->activity_id)->where('order_id', $order->id)->value('answer');
                        if ($answer) {
                            $answer = json_decode($answer, true);
                            foreach ($fields as $field) {
                                if (0 == $field['status']) {
                                    continue;
                                }

                                if (!isset($answer[$field['name']])) {
                                    $tmpArr[rand(9999, 999999)] = '';
                                } else {
                                    if (is_array($answer[$field['name']])) {
                                        $tmpArr[$field['name']] = implode(',', $answer[$field['name']]);
                                    } elseif ('id_card' == $field['name'] || preg_match("/^([\d]{17}[xX\d]|[\d]{15})$/", $answer[$field['name']])) {
                                        $tmpArr[$field['name']] = $answer[$field['name']]."\t";
                                    } else {
                                        $tmpArr[$field['name']] = $this->removeEmoJi($answer[$field['name']]);
                                    }
                                }
                            }
                        }
                    }

                    $data[] = $tmpArr;
                }
            }

            $title = ['活动id', '活动标题', '活动状态', '活动报名时间', '活动签到时间', '活动取消时间', '电子票名称', '金额', '积分', '活动订单号'];
            $width = ['A' => 10, 'B' => 20, 'C' => 10, 'D' => 20, 'E' => 20, 'F' => 20, 'G' => 20, 'H' => 20, 'I' => 20, 'J' => 30];
            if (3 == request('excel') && !empty($fields)) {
                $offset = ord('J');
                foreach ($fields as $field) {
                    if (0 == $field['status']) {
                        continue;
                    }

                    if (90 == $offset) {
                        $offset = 96;
                    }

                    array_push($title, $field['title']);
                    $width[chr(++$offset)] = 30;
                }
            }

            $name = 'activity_apply_'.date('Y_m_d_H_i_s', time());
            Excel::create($name, function ($excel) use ($data, $title, $width) {
                $excel->sheet('活动报名列表', function ($sheet) use ($data, $title, $width) {
                    $sheet->prependRow(1, $title);
                    $sheet->rows($data);
                    $sheet->setWidth($width);
                });
            })->store('xls');

            return Response::download(storage_path().'/exports/'.$name.'.xls');
        }
        if (isset($status) && $status) {
            $where[] = ['status', '=', $status];
        } else {
            $status = 'all';
        }

        $orders = $this->getData(new Member(), $where, 25);
        $activities = Activity::all(['id', 'title']);

        return Admin::content(function (Content $content) use ($orders, $activities, $status) {
            $content->description('活动订单管理');

            $content->breadcrumb(
                    ['text' => '活动管理', 'url' => 'activity', 'no-pjax' => 1],
                    ['text' => '订单列表', 'no-pjax' => 1, 'left-menu-active' => '订单列表']
                );

            $view = view('activity::activityOrder.activityOrderList', compact('orders', 'activities', 'status'))->render();
            $content->row($view);
        });
    }

    public function removeEmoJi($nickname)
    {
        $string = preg_replace_callback('/./u', function (array $match) {
            return strlen($match[0]) >= 4 ? '' : $match[0];
        }, $nickname);

        $regexEmoticons = '/[\x{1F600}-\x{1F64F}]/u';
        $string = preg_replace($regexEmoticons, '', $string);

        $regexSymbols = '/[\x{1F300}-\x{1F5FF}]/u';
        $string = preg_replace($regexSymbols, '', $string);

        $regexTransport = '/[\x{1F680}-\x{1F6FF}]/u';
        $string = preg_replace($regexTransport, '', $string);

        $regexMisc = '/[\x{2600}-\x{26FF}]/u';
        $string = preg_replace($regexMisc, '', $string);

        $regexDingbats = '/[\x{2700}-\x{27BF}]/u';
        $string = preg_replace($regexDingbats, '', $string);

        $regex = "/\/|\~|\!|\#|\\$|\%|\^|\&|\*|\(|\)|\_|\+|\{|\}|\:|\<|\>|\?|\[|\]|\,|\.|\/|\;|\'|\`|\-|\=|\\\|\|/";
        $string = preg_replace($regex, '', $string);

        return $string;
    }

    public function exportExcel($orders)
    {
    }

    protected function getData($object, $where, $limit = 15)
    {
        $data = $object->where(function ($query) use ($where) {
            if (is_array($where) && !empty($where)) {
                foreach ($where as $value) {
                    if (is_array($value)) {
                        list($condition, $operate, $va) = $value;
                        if ('like' != $operate || ('order_no' == $condition && 'like' == $operate)) {
                            $query = $query->where('ac_activity_member.'.$condition, $operate, $va);
                        }
                    }
                }
            }

            return $query;
        })->orderBy('ac_activity_member.created_at', 'desc');

        $search = [];
        foreach ($where as $key => $item) {
            if ('order_no' != $item[0] && 'like' == $item[1]) {
                $search = $item;
            }
        }

        if (!empty($search)) {
            $data->join('el_user', function ($join) use ($search) {
                $join->on('ac_activity_member.user_id', '=', 'el_user.id')->where('el_user.'.$search[0], $search[1], $search[2]);
            });
        } else {
            $data->join('el_user', function ($join) use ($search) {
                $join->on('ac_activity_member.user_id', '=', 'el_user.id');
            });
        }

        $data->select('ac_activity_member.*', 'el_user.id as user_id', 'el_user.name', 'el_user.mobile', 'el_user.sex', 'el_user.email');

        if (0 == $limit) {
            return $data->get();
        }

        return $data->paginate($limit);
    }

    protected function jointWhere($column, $operator, $compareVal, &$where)
    {
        if (!empty($compareVal) && isset($column) && !empty($column)) {
            $where[] = [$column, $operator, $compareVal];
        }
    }

    /**
     * 更改活动订单状态 未签到改成已签到.
     *
     * @param $id
     */
    public function changeStatus($id)
    {
        $member = Member::find($id);

        if (!$activity = Activity::find($member->activity_id)) {
            return $this->ajaxJson(false, [], 500, '活动不存在.');
        }

        $time_start = strtotime($activity->starts_at);
        if (($time_start - time()) > 0) {
            return $this->ajaxJson(false, [], 500, '活动未开始，不能签到');
        }

        $user = User::find($member->user_id);
        try {
            DB::beginTransaction();
            $member->signed_at = Carbon::now();
            $member->status = 2;
            $member->save();
            $user->notify(new Signed([
                'activity' => $activity,
                'member' => $member,
            ]));

            $coupon = null;
            if ($discount = $this->discountRepository->getDiscountByCode(settings('activity_coupon_code_sign'), 1) and $this->couponRepository->canGetCoupon($discount, $user)) {
                $coupon = DiscountCoupon::create([
                    'discount_id' => $discount->id,
                    'user_id' => $user->id,
                ]);
            }
            DB::commit();

            return $this->ajaxJson(true);
        } catch (\Exception $exception) {
            DB::rollBack();
            \Log::info($exception);

            return $this->ajaxJson(false, [], 404, '修改失败');
        }
    }

    /**
     * 审核未支付订单.
     *
     * @param $id
     *
     * @return mixed
     */
    public function audit($id)
    {
        $member = Member::find($id);

        if (!$activity = Activity::find($member->activity_id)) {
            return $this->ajaxJson(false, [], 500, '活动不存在.');
        }

        $user = User::find($member->user_id);

        try {
            DB::beginTransaction();
            $member->status = 1;
            $member->save();
            $user->notify(new Join([
                'activity' => $activity,
            ]));
            DB::commit();

            return $this->ajaxJson(true);
        } catch (\Exception $exception) {
            DB::rollBack();
            \Log::info($exception);

            return $this->ajaxJson(false, [], 404, '修改失败');
        }
    }

    public function paymentOptions(Request $request)
    {
        $input = $request->except('_token', 'file');
        if (!$input['activity_id']) {
            return $this->ajaxJson(false, ['html' => ''], 500, '');
        }

        $payments = Payment::where('activity_id', $input['activity_id'])->where('status', 1)->get();
        $html = '<option value="0">请选择</option>';
        if (count($payments) > 0) {
            foreach ($payments as $payment) {
                $title = $payment->title;
                if ('' == $payment->title && 3 == $payment->type) {
                    $title = '通行证活动';
                }

                if ('' == $payment->title && 4 == $payment->type) {
                    $title = '线下互动';
                }

                $html .= '<option value="'.$payment->id.'">'.$title.'</option>';
            }
        }

        return $this->ajaxJson(true, ['html' => $html], 200, '请求成功');
    }
}
