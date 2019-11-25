<?php

/*
 * This file is part of guojiangclub/activity-core.
 *
 * (c) 果酱社区 <https://guojiang.club>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GuoJiangClub\Activity\Core\Discount\Services;

use Cache;
use Carbon\Carbon;
use DB;
use Exception;
use iBrand\Component\Discount\Applicators\DiscountApplicator;
use iBrand\Component\Discount\Checkers\CouponEligibilityChecker;
use iBrand\Component\Discount\Checkers\DatesEligibilityChecker;
use iBrand\Component\Discount\Checkers\DiscountEligibilityChecker;
use iBrand\Component\Discount\Contracts\DiscountSubjectContract;
use iBrand\Component\Discount\Models\Coupon;
use iBrand\Component\Discount\Models\Discount;
use iBrand\Component\Discount\Repositories\CouponRepository;
use iBrand\Component\Discount\Repositories\DiscountRepository;
use Illuminate\Support\Collection;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/4
 * Time: 21:49.
 */
class DiscountService
{
    private $discountRepository;
    private $discountChecker;
    private $couponChecker;
    private $couponRepository;
    protected $applicator;
    protected $datesEligibilityChecker;

    const SINGLE_DISCOUNT_CACHE = 'single_discount_cache';

    public function __construct(DiscountRepository $discountRepository, DiscountEligibilityChecker $discountEligibilityChecker, CouponRepository $couponRepository, CouponEligibilityChecker $couponEligibilityChecker, DiscountApplicator $discountApplicator, DatesEligibilityChecker $datesEligibilityChecker)
    {
        $this->discountRepository = $discountRepository;
        $this->discountChecker = $discountEligibilityChecker;
        $this->couponRepository = $couponRepository;
        $this->couponChecker = $couponEligibilityChecker;
        $this->applicator = $discountApplicator;
        $this->datesEligibilityChecker = $datesEligibilityChecker;
    }

    public function getEligibilityDiscounts(DiscountSubjectContract $subject, $channel = 'ec')
    {
        try {
            $discounts = $this->discountRepository->findActive(0);
            if (0 == count($discounts)) {
                return false;
            }

            $filtered = $discounts->filter(function ($item) use ($subject) {
                return $this->discountChecker->isEligible($subject, $item);
            });

            if (0 == count($filtered)) {
                return false;
            }

            foreach ($filtered as $item) {
                $this->applicator->calculate($subject, $item);
            }

            return $filtered;
        } catch (Exception $e) {
            \Log::info('折扣异常:'.$e->getMessage());

            return false;
        }
    }

    public function getEligibilityCoupons(DiscountSubjectContract $subject, $userId, $channel = 'ec')
    {
        try {
            $coupons = $this->couponRepository->findActiveByUser($userId, false);
            if (0 == count($coupons)) {
                return false;
            }

            $filtered = $coupons->filter(function ($item) use ($subject) {
                return $this->couponChecker->isEligible($subject, $item);
            });

            if (0 == count($filtered)) {
                return false;
            }

            foreach ($filtered as $item) {
                $this->applicator->calculate($subject, $item);
            }

            return $filtered;
        } catch (Exception $e) {
            \Log::info('优惠券异常:'.$e->getMessage());

            return false;
        }
    }

    public function checkDiscount(DiscountSubjectContract $subject, Discount $discount)
    {
        return $this->discountChecker->isEligible($subject, $discount);
    }

    public function checkCoupon(DiscountSubjectContract $subject, Coupon $coupon)
    {
        return $this->couponChecker->isEligible($subject, $coupon);
    }

    /**
     * 优惠券领取.
     *
     * @param     $couponCode
     * @param     $userId
     * @param int $type
     *
     * @return mixed
     *
     * @throws Exception
     */
//    public function getCouponConvert($couponCode, $userId, $type = 0, $utmCampaign = null, $utmSource = null)
//    {
//        /*if ($coupon = $this->receiveCouponBySecretCode($couponCode, $userId, $type = 0, $utmCampaign = null, $utmSource = null)) {
//            return $coupon;
//        }*/
//
//        if (!$discount = $this->discountRepository->findWhere([
//            'code' => $couponCode,
//            //            'type' => $type
//        ])->first()
//        ) {
//            throw new Exception('您输入的优惠券有误');
//        }
//        $type = $discount->type;
//
//        if (strtotime($discount->starts_at) > Carbon::now()->timestamp || strtotime($discount->ends_at) < strtotime(Carbon::today()) || $discount->status == 0) {
//            throw new Exception('该优惠券不能兑换');
//        }
//
//        if ($utmCampaign) { //如果是通过渠道来领取优惠券
//            if ($this->couponRepository->getCouponCountByUser($discount->id, $userId, $utmCampaign, $utmSource) > 0) {
//                throw new Exception('您已经领取过该优惠券');
//            }
//        } else {
//            if ($discount->per_usage_limit AND $discount->per_usage_limit > 0
//                AND $this->couponRepository->getCouponCountByUser($discount->id, $userId) >= $discount->per_usage_limit
//            ) {
//                throw new Exception('您已经领取过该优惠券');
//            }
//        }
//
//        //领取优惠券
//        $coupon = $this->couponRepository->getCouponsByUser($userId, $discount->id, $type, $utmCampaign, $utmSource);
//
//        if ($coupon) {
//            event('user.get.coupon', [$coupon]);
//        }
//
//        return $coupon;
//    }

    /**
     * 2018新优惠券领取.
     *
     * @param $couponCode
     * @param $user_id
     * @param null $utmCampaign
     * @param null $utmSource
     *
     * @throws Exception
     */
    public function getCouponConvert($couponCode, $user_id, $utmCampaign = null, $utmSource = null)
    {
        $coupont = $this->discountRepository->getCouponByCodeAndUserID($couponCode, $user_id);

        if (!$coupont) {
            throw new Exception('该优惠券码不存在或已过期');
        }

        if ($coupont->has_get) {
            throw new Exception('您已经领取过该优惠券');
        }

        if ($coupont->has_max) {
            throw new Exception('该优惠券已领完库存不足');
        }

        //领取优惠券
        $coupon = $this->couponRepository->getCouponsByUserID($user_id, $coupont->id, $utmCampaign, $utmSource);

        if ($coupon) {
            event('user.get.coupon', [$coupon]);
        }

        return $coupon;
    }

    private function receiveCouponBySecretCode($couponCode, $userId, $type = 0, $utmCampaign = null, $utmSource = null)
    {
        $coupon = $this->couponRepository->findCouponBySecretCode($couponCode)->first();

        if (!$coupon or $coupon->user_id) {
            return false;
        }

        $input['user_id'] = $userId;

        if ($utmCampaign) {
            $input['utm_campaign'] = $utmCampaign;
        }
        if ($utmSource) {
            $input['utm_source'] = $utmSource;
        }
        $coupon->fill($input);
        $coupon->save();

        return $coupon;
    }

    public function getCouponByCode($user_id, $code)
    {
        $discount = $this->discountRepository->getDiscountByCode($code, true);

        $checker = new UsageLimitEligibilityChecker();

        if ($discount and $checker->isEligible($discount)) {
            $data['user_id'] = $user_id;
            $data['discount_id'] = $discount->id;
            $data['code'] = build_order_no('C');
            $coupon = $this->couponRepository->create($data);

            $discount->receiveCoupon();

            return $coupon;
        }

        return false;
    }

    public function getCouponByGoods(DiscountItemContract $item)
    {
        $discountIds = [];

        $rules = Rule::where('type', '<>', ContainsRoleRuleChecker::TYPE)->where(function ($query) {
            $query->where('type', '=', ContainsCategoryRuleChecker::TYPE)
                ->orWhere('type', '=', ContainsProductRuleChecker::TYPE);
        })->get();

        foreach ($rules as $rule) {
            $checker = app($rule->type);
            $configuration = json_decode($rule->configuration, true);
            if ($checker->isEligibleByItem($item, $configuration)) {
                $discountIds[] = $rule->discount_id;
            }
        }
        $discounts = $this->discountRepository->scopeQuery(function ($query) use ($discountIds) {
            return $query->whereIn('id', $discountIds)->where('coupon_based', 1)->where('status', 1);
        })->all();

        return $discounts;
    }

    /**
     * 根据商品获取所有的优惠折扣，包含促销活动和优惠券，同时过滤是否显示在前端的数据.
     *
     * @param DiscountItemContract $item
     */
    public function getDiscountsByGoods(DiscountItemContract $discountItemContract, $channel = 'ec')
    {
        $collect = collect();
        /*$discounts = empty_collect_cache('goods_discount_cache', $discountItemContract->id);
        if (!is_null($discounts)) {
            return $discounts;
        }*/

        /*if (Cache::has('goods_discount_cache')) {
            $collect = Cache::get('goods_discount_cache');
            if ($collect->has($discountItemContract->id)) {
                $cache = $collect->get($discountItemContract->id);
                if (!empty($cache) && count($cache) > 0) {
                    return $cache;
                }
            }
        }*/
        $discounts = $this->discountRepository->findActive(2, $channel);
        $discounts = $discounts->filter(function ($item) use ($discountItemContract) {
            return $this->discountChecker->isEligibleItem($discountItemContract, $item) and $item->is_open;
        });

        empty_collect_cache([$discountItemContract->id => $discounts], 'goods_discount_cache', 30);

        if ($discounts instanceof Collection) {
            return $discounts;
        }

        return collect();

        /*if (!empty($discounts) && count($discounts) > 0) {
            $collect = $collect->put($discountItemContract->id, $discounts);
            Cache::put('goods_discount_cache', $collect, Carbon::now()->addMinutes(120));

            return $discounts;
        }*/
        //return collect();
    }

    public function getGoodsByRole($role)
    {
        $spu = [];
        $sku = [];
        $category = [];
        $percentageGroup = [];
        $percentage = 100;

        //1. 找到包含该角色的所有促销活动，因为用户可能设置一个角色有多个促销活动
        $discounts = Discount::where(function ($query) {
            $query->where('status', 1)
                ->where('coupon_based', 0);
        })->whereHas('rules', function ($query) use ($role) {
            $query->where(['type' => 'contains_role', 'configuration' => json_encode(['name' => $role])]);
        })->get();

        //2. 过滤日期不符合要求的
        $discounts = $discounts->filter(function ($item) {
            return $this->datesEligibilityChecker->isEligible($item);
        });

        //3. 获取所有满足条件的SKU 和 SPU ID：
        foreach ($discounts as $discount) {
            $discountSpu = [];
            $discountSku = [];
            $discountCategory = [];

            foreach ($discount->rules as $rule) {
                if ('contains_product' == $rule->type) {
                    $configuration = json_decode($rule->configuration, true);
                    if (!empty($configuration['spu'])) {
                        $discountSpu = array_merge($discountSpu, explode(',', $configuration['spu']));

                        $diffIds = DB::table('el_goods')
                            ->whereIn('goods_no', ['2t7d', '2t7e'])
                            ->select('id')
                            ->get()->pluck('id')->toArray();

                        $discountSku = array_diff($discountSku, $diffIds);
                    }
                    if (!empty($configuration['sku'])) {
                        $discountSku = array_merge($discountSku, explode(',', $configuration['sku']));
                    }
                } elseif ('contains_category' == $rule->type) {
                    $configuration = json_decode($rule->configuration, true);

                    if (count($configuration['items'])) {
                        $discountCategory = array_merge($discountCategory, $configuration['items']);

                        $spuIds = DB::table('el_goods_category')
                            ->whereIn('category_id', $discountCategory)
                            ->select('goods_id')
                            ->distinct()->get()->pluck('goods_id')->toArray();

                        $discountSpu = array_merge($discountSpu, $spuIds);

                        $diffIds = DB::table('el_goods')
                            ->whereIn('goods_no', ['2t7d', '2t7e'])
                            ->select('id')
                            ->get()->pluck('id')->toArray();

                        $discountSpu = array_diff($discountSpu, $diffIds);

                        if (isset($configuration['exclude_spu'])
                            and $excludeSpus = explode(',', $configuration['exclude_spu'])
                            and count($excludeSpus) > 0
                        ) {
                            $discountSpu = array_diff($discountSpu, $excludeSpus);
                        }
                    }
                }
            }

            if ($action = $discount->actions()->first()) {
                $configuration = json_decode($action->configuration, true);
                $percentage = $configuration['percentage'];
                $percentageGroup[$percentage] = $discountSpu;
            }

            $spu = array_merge($spu, $discountSpu);
            $sku = array_merge($sku, $discountSku);
            $category = array_merge($category, $discountCategory);
        }

        /*}*/

        return [
            'spu' => $spu,
            'sku' => $sku,
            'category' => $category,
            'percentage' => $percentage,
            'percentageGroup' => $percentageGroup,
            'discounts' => $discounts,
        ];
    }

    public function getDiscountsByActionType($actionType)
    {
        return $this->discountRepository->getDiscountsByActionType($actionType);
    }

    /**
     * 计算出优惠组合，把优惠的可能情况都计算出来给到前端.
     *
     * @param $discounts
     * @param $coupons
     */
    public function getOrderDiscountGroup($order, $discounts, $coupons)
    {
        $order = Order::find($order->id);

        $groups = new Collection();

        $exclusiveDiscounts = $discounts->where('exclusive', 1);
        $exclusiveCoupons = $coupons->where('discount.exclusive', 1);

        $normalDiscounts = $discounts->where('exclusive', 0);
        $normalCoupons = $coupons->where('discount.exclusive', 0);

        $exclusiveDiscounts->each(function ($item, $key) use ($groups) {
            $groups->push(['discount' => $item->id, 'coupon' => 0]);
        });

        $exclusiveCoupons->each(function ($item, $key) use ($groups) {
            $groups->push(['discount' => 0, 'coupon' => $item->id]);
        });

        $normalDiscounts->each(function ($item, $key) use ($groups) {
            $groups->push(['discount' => $item->id, 'coupon' => 0]);
        });

        $normalCoupons->each(function ($item, $key) use ($groups) {
            $groups->push(['discount' => 0, 'coupon' => $item->id]);
        });

        foreach ($normalDiscounts as $discount) {
            foreach ($normalCoupons as $coupon) {
                $groups->push(['discount' => $discount->id, 'coupon' => $coupon->id]);
            }
        }

        $groups = $groups->unique();

        $result = new Collection();

        foreach ($groups as $group) {
            $discount = Discount::find($group['discount']);
            $coupon = Coupon::find($group['coupon']);

            list($discountAdjustment, $couponAdjustment, $adjustmentTotal) = $this->calculateDiscounts($order, $discount, $coupon);

            if (0 == $adjustmentTotal) {
                continue;
            }

            $group['discountAdjustment'] = $discountAdjustment;
            $group['couponAdjustment'] = $couponAdjustment;
            $group['adjustmentTotal'] = $adjustmentTotal;

            $result->push($group);
            //dd($group);
        }
        $result = $result->unique()->sortBy('adjustmentTotal');

        return collect_to_array($result);
    }

    public function calculateDiscounts($order, ...$discounts)
    {
        $adjustmentTotal = 0;
        $discountAdjustment = 0;
        $couponAdjustment = 0;

        $tempOrder = clone $order;

        foreach ($discounts as $discount) {
            if (is_null($discount)) {
                continue;
            }

            if ($discount->isCouponBased()) {
                if ($this->couponChecker->isEligible($tempOrder, $discount)) {
                    $this->applicator->calculate($tempOrder, $discount);
                    $adjustmentTotal = $adjustmentTotal + $discount->adjustmentTotal;
                    $couponAdjustment = $discount->adjustmentTotal;
                    $tempOrder->total = $tempOrder->total + $discount->adjustmentTotal;
                } else {
                    return 0;
                }
            } else {
                if ($this->discountChecker->isEligible($tempOrder, $discount)) {
                    $this->applicator->calculate($tempOrder, $discount);
                    $adjustmentTotal = $adjustmentTotal + $discount->adjustmentTotal;
                    $tempOrder->total = $tempOrder->total + $discount->adjustmentTotal;
                    $discountAdjustment = $discount->adjustmentTotal;
                }
            }
        }
        //dd($tempOrder);
        //\Log::info(json_encode($tempOrder));

        return [$discountAdjustment, $couponAdjustment, $adjustmentTotal];
    }

    public function getSingleDiscountByGoods($goods)
    {
        /*$discounts = empty_collect_cache(self::SINGLE_DISCOUNT_CACHE, $goods->id);

        if (!is_null($discounts)) {
            return $discounts;
        }*/

        if ($goods instanceof Goods) {
            $skus = $goods->products->pluck('sku')->toArray();
        } else {
            $skus = [$goods->sku];
        }

        $condition = SingleDiscountCondition::whereIn('name', $skus)->whereHas('discount', function ($query) {
            return $query->where('status', 1)
                ->where('starts_at', '<=', Carbon::now()->toDateTimeString())
                ->where('ends_at', '>', Carbon::now()->toDateTimeString());
        })->first();

        if ($condition) {
            $discount = $condition->discount;

            //empty_collect_cache([$goods->id => $discount], self::SINGLE_DISCOUNT_CACHE, 30);
            return $discount;
        }   //说明该商品目前没有单品折扣
        //empty_collect_cache([$goods->id => ''], self::SINGLE_DISCOUNT_CACHE, 30);

        return false;
    }

    public function getProductPriceFromSingleDiscount($product, $singleDiscount)
    {
        if ($product instanceof Goods) {
            return $product->sell_price;
        }

        if (!$singleDiscount) {
            return $product->sell_price;
        }

        $condition = $singleDiscount->conditions->where('name', $product->sku)->first();

        if (!$condition) {
            return $product->sell_price;
        }

        $type = $condition->type;

        $value = $product->sell_price;
        if ('type_discount' == $type) {
            $value = number_format($product->market_price * $condition->price / 10, 2, '.', '');
        }
        if ('type_cash' == $type) {
            $value = number_format($condition->price, 2, '.', '');
        }

        return $value;
    }

    /*private function getSingleDiscountFromCacheByGoods($goods)
    {
        if ($singleDiscounts = \Cache::get(self::SINGLE_DISCOUNT_CACHE) AND $singleDiscounts instanceof Collection) {
            return $singleDiscounts->get($goods->id);
        }

        return false;
    }

    private function putSingleDiscountToCache($goods, $discount)
    {
        $discounts = \Cache::get(self::SINGLE_DISCOUNT_CACHE, collect());

        $discounts->put($goods->id, $discount);

        Cache::put(self::SINGLE_DISCOUNT_CACHE, $discounts, 30);

    }*/

    /**
     * TNF 活动特定方法，请勿在其他客户进行使用.
     *
     * @param     $couponCode
     * @param     $userId
     * @param int $type
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function getConsumerCouponConvert($couponCode, $userId, $type = 0, $utmCampaign = null, $utmSource = null)
    {
        if (!$discount = $this->discountRepository->findWhere([
            'code' => $couponCode,
        ])->first()
        ) {
            throw new Exception('您输入的优惠券有误');
        }
        $type = $discount->type;

        if (strtotime($discount->ends_at) < strtotime(Carbon::today()) || 0 == $discount->status) {
            throw new Exception('该优惠券不能兑换');
        }

        if ($utmCampaign) { //如果是通过渠道来领取优惠券
            if ($this->couponRepository->getCouponCountByUser($discount->id, $userId, $utmCampaign, $utmSource) > 0) {
                throw new Exception('您已经领取过该优惠券');
            }
        } else {
            if ($discount->per_usage_limit and $discount->per_usage_limit > 0
                and $this->couponRepository->getCouponCountByUser($discount->id, $userId) >= $discount->per_usage_limit
            ) {
                throw new Exception('您已经领取过该优惠券');
            }
        }

        //判断是否可以领取
        if ($discount->usage_limit - 1 >= 0) {
            $coupon = $this->couponRepository->userGetCoupon($userId, $discount->id, $type, $utmCampaign, $utmSource);

            if ($coupon) {
                event('user.get.coupon', [$coupon]);
            }

            return $coupon;
        }

        return false;
    }

    public function CouponIsAgentShare($code)
    {
        $coupon = $this->discountRepository->findWhere(['code' => $code, 'is_agent_share' => 1, 'coupon_based' => 1])->first();
        if (isset($coupon->id)) {
            return true;
        }

        return false;
    }
}
