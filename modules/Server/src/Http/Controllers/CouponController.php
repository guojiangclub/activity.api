<?php

namespace GuojiangClub\Activity\Server\Http\Controllers;

use Carbon\Carbon;
use ElementVip\Activity\Core\Models\Discount\Coupon;
use ElementVip\Activity\Core\Repository\CouponRepository;
use ElementVip\Activity\Core\Repository\DiscountRepository;
use GuojiangClub\Activity\Server\Transformers\CouponTransformer;
use ElementVip\Component\User\Models\User;
use ElementVip\Component\User\Models\UserBind;
use Illuminate\Http\Request;

class CouponController extends Controller
{

    protected $couponRepository;
    protected $discountRepository;

    public function __construct(CouponRepository $couponRepository, DiscountRepository $discountRepository)
    {
        $this->couponRepository = $couponRepository;
        $this->discountRepository = $discountRepository;
    }

    public function index()
    {
        $type = request('type') ?: 1;
        $user = request()->user();
        if ($type == 1) {
            $coupons = $this->couponRepository->findActiveByUser($user->id);
        } elseif ($type == 2) {
            $coupons = Coupon::where('user_id', $user->id)->whereNotNull('used_at')->with('discount')->paginate(15);
        } else {
            $coupons = $this->couponRepository->findInvalidByUser($user->id);
        }
        return $this->response()->paginator($coupons, new CouponTransformer());
    }

    public function show($id)
    {
        if (!$coupon = Coupon::find($id)) {
            return $this->api([], false, 500, '活动通行证不存在.');
        }
        $user = request()->user();
        if ($coupon->user_id != $user->id) {
            return $this->api([], false, 500, '无权进行操作.');
        }
        $coupon = Coupon::where('id', $id)->with('discount')->first();
        return $this->response()->item($coupon, new CouponTransformer());
    }

    public function wxGetCoupon(Request $request)
    {
        $user = request()->user();
        $code = $request->input('code');
        if (!$this->checkOpenId($user)) {
            return $this->api([], false, 500, '仅限微信公众号用户领取.');
        }

        if ($discount = $this->discountRepository->getDiscountByCode($code, 1)) {
            if (!$this->couponRepository->canGetCoupon($discount, $user)) {
                return $this->api([], false, 500, '该活动通行证已达到领取上限.');
            }
            /*if ($coupon = Coupon::where('discount_id', $discount->id)->where('user_id', $user->id)->where('created_at', '>', date('Y-m-01 0:00:00'))->first()) {
                return $this->api([], false, 500, '本月已领取过野练PASS.');
            }*/

            if (!$this->couponRepository->canGetCouponByMonth($discount, $user)) {
                return $this->api([], false, 500, '本月已领取过活动通行证.');
            }

            $coupon = [];
            $num = 0;
            for ($i = 1; $i <= 2; $i++) {
                if ($this->couponRepository->canGetCouponByMonth($discount, $user)) {
                    $discount->used += 1;
                    $discount->save();
                    $coupon = Coupon::create([
                        'discount_id' => $discount->id,
                        'user_id' => $user->id
                    ]);
                    $num = $i;
                }
            }

            return $this->api(['coupon' => $coupon, 'user' => $user, 'num' => $num]);
        }
        return $this->api([], false, 500, '领取失败.');
    }

	public function wxCheckCoupon($code)
	{
		$user         = request()->user();
		$wxBind       = 1;
		$hasCoupon    = 0;
		$canGetCoupon = 0;
		$num          = 0;
		if (!$this->checkOpenId($user)) {
			$wxBind = 0;
		}

		$discount = $this->discountRepository->getDiscountByCode($code, 1);
		if ($discount AND $this->couponRepository->canGetCoupon($discount, $user)) {
			$hasCoupon = 1;
			if ($this->couponRepository->canGetCouponByMonth($discount, $user)) {
				$canGetCoupon = 1;
				$num          = $this->couponRepository->canGetCouponNumByMonth($discount, $user);
			}
		}

		return $this->api([
			'user'         => $user,
			'wxBind'       => $wxBind,
			'hasCoupon'    => $hasCoupon,
			'canGetCoupon' => $canGetCoupon,
			'num'          => $num,
		]);
	}

    protected function checkOpenId($user)
    {
        $userBind = UserBind::byUserIdAndType($user->id, 'wechat')->first();
        if (!$userBind OR empty($userBind->open_id)) {
            return false;
        }
        return true;
    }

}
