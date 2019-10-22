<?php
namespace GuojiangClub\Activity\Server\Services;

use Carbon\Carbon;
use DNS2D;
use ElementVip\Activity\Core\Models\ActivityGoods;
use ElementVip\Activity\Core\Models\Answer;
use ElementVip\Activity\Core\Models\Like;
use ElementVip\Component\Product\Models\Goods;
use ElementVip\Component\Product\Models\Specification;
use iBrand\Shoppingcart\Item;
use Illuminate\Support\Collection;

class ActivityService
{

    /**
     * @param $activity
     * @param $user_id
     * @return mixed
     */
    public function getMember($activity, $user_id)
    {
        if ($member = $activity->members()->where('user_id', $user_id)->where('role', 'user')->whereIn('status', [1, 2, 4])->orderBy('created_at', 'desc')->first()) {
            return $member;
        }
        return false;
    }

    /**
     * @param $user
     * @return bool
     */
    public function isCoach($user)
    {
        if (in_array('coach', $user->roles()->pluck('name')->toArray())) {
            return true;
        }
        return false;
    }

    /**
     * @param $activity
     * @param $user
     * @return bool
     */
    public function belongsToCoach($activity, $user)
    {
        if ($activity->members()->where('user_id', $user->id)->where('role', 'coach')->first()) {
            return true;
        }
        return false;
    }

    /**
     * @param $activityId
     * @return string
     */
    public function getActCode($activityId, $type = 'wechat')
    {
        $string = env('APP_KEY') . '-' . $activityId;
        $code = md5($string);
        if ($type == 'mini_program') {
            $code = substr($code, 7, 16);
        }
        return $code;

    }

    /**
     * 获取签到URL
     * @param $activityId
     * @return string
     */
    public function getActSignUrl($activityId)
    {
        $code = $this->getActCode($activityId);
        $url = settings('mobile_activity_domain') . 'user/sign?code=' . $code . '&id=' . $activityId;
        return $url;
    }

    /**
     * @param $activityId
     * @return string
     */
    public function getActQrCode($activityId)
    {
        $code = $this->getActSignUrl($activityId);
        return DNS2D::getBarcodePNG($code, "QRCODE,M", "12", "12");
    }

    /**
     * @param $activity
     * @param $user
     * @return bool
     */
    public function canSign($activity, $user)
    {
        /*if ($activity->status != 2) {
            return false;
        }*/

        if (!$activity->can_sign) {
            return false;
        }

        if (!$member = $this->getMember($activity, $user->id)) {
            return false;
        }
        if ($member->status != 1) {
            return false;
        }
        return true;
    }

    public function isLiked($user_id, $activity_id)
    {
        if (Like::where('user_id', $user_id)->where('favoriteable_id', $activity_id)->where('favoriteable_type', 'activity')->first())
            return 1;
        return 0;
    }

    /**
     * 获取活动关联的商品
     * @param $activity
     * @return array
     */
    public function getActivityGoods($activity)
    {
        $activityGoods = $activity->goods;
        $goodsList = [];

        if (count($activityGoods) > 0) {
            foreach ($activityGoods as $ak => $goods) {
                $specs = [];
                $stores = [];
                $skuPhoto = collect();

                if (count($products = $goods->products()->with('photo')->get())) {
                    $grouped = $goods->specificationValue->groupBy('spec_id');
                    foreach ($products as $key => $val) {
                        $specArray = $val->specID;
                        asort($specArray);

                        $spec_id = implode('-', $specArray);
                        $stores[$spec_id]['id'] = $val->id;
                        $stores[$spec_id]['store'] = $val->is_show == 1 ? $val->store_nums : 0;

                        $stores[$spec_id]['price'] = $goods->pivot->price;

                        $stores[$spec_id]['sku'] = $val->sku;
                        $stores[$spec_id]['ids'] = $val->specID;
                        $stores[$spec_id]['redeem_point'] = $goods->redeem_point;

                        //产品图片
                        if ($photo = $val->photo) {
                            $skuPhotoData['spec_value_id'] = $spec_id;
                            $skuPhotoData['photo'] = $photo->url;
                            $skuPhoto->push($skuPhotoData);
                        }
                    }

                    $i = 1;
                    foreach ($grouped as $key => $item) {

                        $keys = $grouped->keys()->toArray();
                        if (in_array(2, $keys)) {   //如果有颜色规格，因为颜色ID=2，为了保证 颜色排前面，需要这样处理sort
                            $sort = $key == 1 ? $key + 2 : $key;
                        } else {
                            $sort = $key;
                        }

                        $specs[$sort]['id'] = $key;

                        $spec = Specification::find($key);

                        if (count($grouped) == 1) {  //如果是单规格
                            if ($key == 2) {  //如果是颜色
                                $specs[$sort]['label_key'] = 'color';
                            } else {  //否则是其他规格
                                $specs[$sort]['label_key'] = 'size';
                            }
                        } else {  //如果是双规格
                            if (in_array(2, $keys)) { //如果有颜色规格
                                if ($key == 2) {  //如果是颜色
                                    $specs[$sort]['label_key'] = 'color';
                                } else {  //否则是其他规格
                                    $specs[$sort]['label_key'] = 'size';
                                }
                            } else {  //没有颜色规格
                                if ($i == 1) {
                                    $specs[$sort]['label_key'] = 'color';
                                } else {
                                    $specs[$sort]['label_key'] = 'size';
                                }
                            }
                        }
                        $i++;


                        $specs[$sort]['label'] = $spec->name;
                        $specs[$sort]['list'] = [];
                        $item = $item->sortBy('pivot.sort')->values();
                        foreach ($item as $k => $value) {
                            $list = [];
                            $list['id'] = $value->id;
                            $list['value'] = $value->name;

                            if ($value->spec_id == 2)    //颜色
                            {
                                $list['color'] = '#' . $value->rgb;
                                $list['img'] = $this->getImageCdnUrl($value->pivot->img);

                                $list['spec_img'] = $this->getImageCdnUrl($value->pivot->img);
                            }
                            $list['alias'] = $value->pivot->alias;
                            array_push($specs[$sort]['list'], $list);
                        }
                    }
                }

                $acGoods = ActivityGoods::where('activity_id', $activity->id)->where('goods_id', $goods->id)->first();
                $goodsInfo['id'] = $goods->id;
                $goodsInfo['name'] = $goods->name;
                $goodsInfo['market_price'] = $goods->market_price;
                $goodsInfo['img'] = $goods->img;
                $goodsInfo['sell_price'] = $acGoods->price;
                $goodsInfo['required'] = $acGoods->required;
                $goodsInfo['rate'] = $acGoods->rate;
                $goodsInfo['store_nums'] = $goods->store_nums;

                $goodsList[$ak]['goods'] = $goodsInfo;
                $goodsList[$ak]['specs'] = $specs;
                $goodsList[$ak]['stores'] = $stores;

            }
        }
        return $goodsList;
    }

    private function getImageCdnUrl($value)
    {
        $replace_url = settings('store_img_replace_url') ? settings('store_img_replace_url') : url('/');
        if (settings('store_img_cdn_status') AND $url = settings('store_img_cdn_url')) {
            $value = str_replace('http://' . $replace_url, $url, $value);
        }
        return $value;
    }

    public function makeCartItems($goods)
    {
        $cartItems = new Collection();

        foreach ($goods as $k => $item) {
            $__raw_id = md5(time() . $k);

            $input = ['__raw_id' => $__raw_id,
                'id' => $item['id'],    //如果是SKU，表示SKU id，否则是SPU ID
                'name' => isset($item['name']) ? $item['name'] : '',
                'img' => isset($item['attributes']['img']) ? $item['attributes']['img'] : '',
                'qty' => $item['qty'],
                'price' => $item['price'],
                'total' => isset($item['total']) ? $item['total'] : '',
            ];

            if (isset($item['attributes']['sku'])) {
                $input['color'] = isset($item['attributes']['color']) ? $item['attributes']['color'] : [];
                $input['size'] = isset($item['attributes']['size']) ? $item['attributes']['size'] : [];
                $input['com_id'] = isset($item['attributes']['com_id']) ? $item['attributes']['com_id'] : [];
                $input['type'] = 'sku';
                $input['__model'] = 'ElementVip\Component\Product\Models\Product';
            } else {
                $input['size'] = isset($item['size']) ? $item['size'] : '';
                $input['color'] = isset($item['color']) ? $item['color'] : '';
                $input['type'] = 'spu';
                $input['__model'] = 'ElementVip\Component\Product\Models\Goods';
                $input['com_id'] = $item['id'];
            }
            $data = new Item(array_merge($input), $item);

            $cartItems->put(md5(time() . $k), $data);
        }
        \Log::info($cartItems);
        return $cartItems;
    }

    public function getActivityGoodsPrice($activity_id, $goods_id)
    {
        if (!$goods = Goods::find($goods_id)) {
            throw new \Exception('商品不存在');
        }

        if (!$activityGoods = ActivityGoods::where('goods_id', $goods_id)->where('activity_id', $activity_id)->first()) {
            throw new \Exception('该活动不存在此商品');
        }

        $price = number_format($goods->market_price * ($activityGoods->rate / 10), 2, ".", "");
        return $price;
    }

    /**
     * 报名成功之后获取填写的表单信息
     * @param $order
     * @return array
     */
    public function getFormData($order)
    {
        $formData = [];
        $activity = $order->activity;

        if (isset($activity->form) AND $activity->form) {
            $fields = json_decode($activity->form->fields, true);
            $answer = Answer::where('user_id', $order->user_id)->where('activity_id', $order->activity_id)->where('order_id', $order->id)->value('answer');
            if ($answer) {
                $answer = json_decode($answer, true);
                foreach ($fields as $field) {
                    if (isset($answer[$field['name']]) AND !empty($answer[$field['name']])) {
                        if ($field['name'] == 'user_name' OR 
                            $field['name'] == 'id_card' OR 
                            $field['name'] == 'mobile' OR 
                            $field['name'] == 'province' OR
                            str_contains($field['title'], '尺码')
                            
                        ) {
                            $value = $answer[$field['name']];
                            if (is_array($answer[$field['name']])) {
                                $value = implode(',', $answer[$field['name']]);
                            }
                            $formData[] = [
                                'key' => $field['title'],
                                'value' => $value
                            ];
                        }
                    }

                }
            }
        }
        return $formData;

    }

}