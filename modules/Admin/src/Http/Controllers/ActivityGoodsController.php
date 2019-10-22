<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/11/19
 * Time: 10:35
 */

namespace GuojiangClub\Activity\Admin\Http\Controllers;


use ElementVip\Store\Backend\Repositories\GoodsRepository;
use ElementVip\Store\Backend\Service\GoodsService;
use iBrand\Backend\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ActivityGoodsController extends Controller
{
    protected $goodsRepository;
    protected $goodsService;

    public function __construct(GoodsRepository $goodsRepository,
                                GoodsService $goodsService)
    {
        $this->goodsRepository = $goodsRepository;
        $this->goodsService = $goodsService;
    }

    public function getSpu()
    {
        return view('activity::activityGoods.getSpu');
    }

    public function getSpuData(Request $request)
    {
        $id = $request->input('id') ? $request->input('id') : 0;
        $ids = [];
        if ($request->input('ids')) {
            $ids = explode(',', $request->input('ids'));
        }

        $where = [];
        $where_ = [];

        $where['is_del'] = ['=', 0];
        $where['is_largess'] = ['=', 0];

        if (!empty(request('value'))) {
            $where[request('field')] = ['like', '%' . request('value') . '%'];
        }

        $goods_ids = [];
      
        $goods = $this->goodsRepository->getGoodsPaginated($where, $where_, $goods_ids, 10)->toArray();

        $goods['ids'] = $ids;

        return $this->ajaxJson(true, $goods);
    }

    /**
     * 展示选择的商品
     */
    public function getSelectGoods()
    {
        $num = request('num') + 1;
        $ids = explode(',', request('ids'));
        $selected = explode(',', request('select'));
        $goods_id = array_merge(array_diff($ids, $selected), array_diff($selected, $ids));
        $goods = $this->goodsRepository->getGoodsPaginated([], [], $goods_id, 0);

        return view('activity::activityGoods.select_goods', compact('goods', 'num'));
    }
}