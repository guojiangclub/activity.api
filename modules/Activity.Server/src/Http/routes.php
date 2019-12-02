<?php

/*
 * This file is part of guojiangclub/activity-server.
 *
 * (c) 果酱社区 <https://guojiang.club>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
$router->post('oauth/MiniProgramLogin', 'MiniProgramLoginController@login')->name('api.oauth.miniprogram.login');
$router->post('oauth/MiniProgramMobileLogin', 'MiniProgramLoginController@MiniProgramMobileLogin')->name('api.oauth.MiniProgramMobileLogin');
$router->get('wx_lite/open_id', 'MiniProgramLoginController@getOpenIdByCode');

$router->group(['prefix' => 'activity'], function () use ($router) {
    $router->get('list/{id}', 'ActivityController@index')->name('api.act.list');
    $router->get('show/{id}', 'ActivityController@show')->name('api.act.show');
    $router->post('image/upload', 'PublishController@upload');

    $router->post('share', 'ActivityShareController@index');
    $router->get('share/template', 'ActivityShareController@template');
});

$router->group(['prefix' => 'city'], function () use ($router) {
    $router->get('/', 'CityController@index')->name('api.act.city');
    $router->get('count', 'CityController@countCity')->name('api.act.city.count');
});

$router->group(['prefix' => 'category'], function () use ($router) {
    $router->get('/', 'CategoryController@index')->name('api.act.category');
});

$router->post('activity/wechat/notify', 'WechatPayNotifyController@notify');

$router->group(config('dmp-api.routeAuthAttributes'), function ($router) {
    $router->get('/me', 'UserController@me')->name('api.me');
    $router->get('user/bindUserMiniInfo', 'UserController@bindUserMiniInfo')->name('api.user.bindUserMiniInfo');
    $router->post('users/update/info', 'UserController@updateInfo')->name('api.update.info');

    $router->group(['prefix' => 'activity'], function () use ($router) {
        $router->post('bind/activity-wx', 'AuthController@bindActivityWx')->name('api.act.bindWx');

        $router->get('show/check/{id}', 'MemberController@isMember')->name('api.act.check');
        $router->get('show/{id}/check', 'MemberController@isMember');

        $router->get('isCoach', 'MemberController@isCoach')->name('api.act.isCoach');

        $router->get('checkout/{id}', 'PurchaseController@index')->name('api.act.purchase');
        $router->post('checkout/{id}', 'PurchaseController@checkout')->name('api.act.purchase.checkout');

        $router->post('cancel/{id}', 'PurchaseController@cancel')->name('api.act.purchase.cancel');

        $router->get('myList', 'ActivityController@myActivities')->name('api.act.myList');
        $router->get('myActivity/{id}', 'ActivityController@myActivity')->name('api.act.myActivity');
        $router->get('myCollection/', 'ActivityController@myCollection')->name('api.act.myCollection');

        $router->get('coachActList', 'ActivityController@coachActList')->name('api.act.coachActList');
        $router->get('coachAct/{id}', 'ActivityController@coachAct')->name('api.act.coachAct');

        $router->get('memberList/{id}', 'MemberController@memberList')->name('api.act.memberList');
        $router->get('memberInfo/{id}', 'MemberController@memberInfo')->name('api.act.memberInfo');
        $router->post('rewards/{id}', 'MemberController@rewards')->name('api.act.rewards');

        $router->post('sign/{id}', 'MemberController@sign')->name('api.act.sign');
        $router->post('like/{id}', 'MemberController@like')->name('api.act.like');
        $router->post('dislike/', 'MemberController@dislike')->name('api.act.dislike');

        $router->get('coupon/list', 'CouponController@index')->name('api.act.coupon.list');
        $router->get('coupon/show/{id}', 'CouponController@show')->name('api.act.coupon.show');

        $router->get('coupon/wxCheckCoupon/{code}', 'CouponController@wxCheckCoupon')->name('api.act.coupon.wxCheckCoupon');
        $router->post('coupon/wxGetCoupon', 'CouponController@wxGetCoupon')->name('api.act.coupon.wxGetCoupon');

        $router->post('create/charge', 'ActivityPaymentController@createCharge')->name('api.act.create.charge');
        $router->post('mini/create/charge', 'WechatPayController@createCharge')->name('api.act.mini.create.charge');
        $router->post('payment/webhooks', 'ActivityPaymentController@webhooks')->name('api.act.payment.webhooks');
        $router->get('form/fields/{id}', 'ActivityController@formFields')->name('api.activity.form.fields');

        $router->get('order/info/{order_no}', 'ActivityController@getOrderInfo')->name('api.activity.order.info');

        $router->post('order/paid', 'ActivityPaymentController@paidSuccess')->name('api.activity.order.paid');
        $router->post('order/freePaidSuccess', 'ActivityPaymentController@freePaidSuccess')->name('api.activity.order.freePaidSuccess');

        $router->post('mini/order/checkout', 'ShoppingController@checkout')->name('api.activity.order.checkout');
        $router->post('mini/order/confirm', 'ShoppingController@confirm')->name('api.activity.order.confirm');
        $router->post('mini/order/paid', 'ActivityPaymentController@miniPaidSuccess')->name('api.activity.order.mini.paid');

        //活动发布
        $router->get('publish/init', 'PublishController@init')->name('api.activity.publish.init');
        $router->post('publish/store', 'PublishController@store')->name('api.activity.publish.store');
    });
});

$router->get('wechat/get/jsconfig', 'WechatController@getJsConfig');

$router->any('wechat', 'WechatController@serve');
