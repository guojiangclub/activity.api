<?php

$router->get("/", "ActivityController@index")->name('activity.admin.index');
$router->get("/activity-list", "ActivityController@index")->name('activity.admin.index');
$router->get("/activity-rewards/{id}", "ActivityController@rewards")->name('activity.admin.rewards');
$router->post("/activity-rewards/{id}", "ActivityController@rewardsStore")->name('activity.admin.rewards.store');
$router->get("/activity-create", "ActivityController@create")->name('activity.admin.create');
//免责声明
$router->get("/activity-statement", "ActivityStatementController@index")->name('activity.admin.statement');
$router->get("/activity-statement-curd/{id?}", "ActivityStatementController@curd")->name('activity.admin.statement.curd');
$router->post("/activity-statement-store", "ActivityStatementController@store")->name('activity.admin.statement.store');
$router->get("/activity-statement-delete/{id}", "ActivityStatementController@delete")->name('activity.admin.statement.delete');
//活动分类
$router->resource('activity-category', 'ActivityCategoryController');
//活动表单
$router->get("/activity-form", "ActivityFormController@index")->name('activity.admin.form');
$router->get("/activity-form-curd/{id?}", "ActivityFormController@curd")->name('activity.admin.form.curd');
$router->post("/activity-form-store", "ActivityFormController@store")->name('activity.admin.form.store');
$router->get("/activity-form-delete/{id}", "ActivityFormController@delete")->name('activity.admin.form.delete');
$router->post("/activity-store", "ActivityController@store")->name('activity.admin.store');
$router->get("/activity-edit/{id}/edit", "ActivityController@edit")->name('activity.admin.edit');
$router->patch("/activity-update/{id}/update", "ActivityController@update")->name('activity.admin.update');
$router->delete("/activity-delete/{id}/delete", "ActivityController@delete")->name('activity.admin.delete');
$router->patch("/activity-status-update/{id}/status", "ActivityController@publishActivity")->name('activity.admin.status.update');
$router->get("/activity-coach", "CoachController@index")->name('activity.admin.coach');
$router->get("/activity-coach/{id}", "CoachController@show")->name('activity.admin.coach.show');
$router->post("/activity-coach/{id}/store", "CoachController@store")->name('activity.admin.coach.store');
$router->get("/activityOrder-list", "ActivityOrderController@index")->name('activityOrder.admin.index');
$router->get("/activityOrder-detail/{id}/detail", "ActivityOrderController@activityOrderDetail")->name('activityOrder.admin.detail');

$router->post("changeStatus/{id}", "ActivityOrderController@changeStatus")->name('activityOrder.admin.changeStatus');
$router->post("audit/{id}", "ActivityOrderController@audit")->name('activityOrder.admin.audit');

$router->get("/activity-city", "CityController@index")->name('activity.admin.city');
$router->get("/activity-city/create", "CityController@create")->name('activity.admin.city.create');
$router->get("/activity-city/{id}", "CityController@edit")->name('activity.admin.city.edit');
$router->post("/activity-city/store", "CityController@store")->name('activity.admin.city.store');
$router->post("/activity-city/{id}/delete", "CityController@delete")->name('activity.admin.city.delete');

$router->post("filterFreeDiscount", "ActivityController@filterFreeDiscount")->name('activity.admin.filterFreeDiscount');

$router->group(['prefix' => 'discount'], function () use ($router) {
	$router->get("/", "DiscountController@index")->name('activity.admin.discount.index');
	$router->get("create", "DiscountController@create")->name('activity.admin.discount.create');
	$router->get("edit/{id}", "DiscountController@edit")->name('activity.admin.discount.edit');
	$router->get("{id}/coupon_list", "DiscountController@couponList")->name('activity.admin.discount.coupon_list');

	$router->get("modal/modalActivity", "DiscountController@modalActivity")->name('activity.admin.discount.modal.modalActivity');
	$router->post("modal/getModalActivityData", "DiscountController@getModalActivityData")->name('activity.admin.discount.modal.getModalActivityData');
	$router->post("getSelectedActivity", "DiscountController@getSelectedActivity")->name('activity.admin.discount.getSelectedActivity');

	$router->post("store", "DiscountController@store")->name('activity.admin.discount.store');

	$router->post("switchStatus", "DiscountController@switchStatus")->name('activity.admin.discount.switchStatus');
});

$router->get("/settings/domain", "SettingsController@domain")->name('activity.admin.settings.domain');
$router->post("/settings/domain", "SettingsController@domainStore")->name('activity.admin.settings.domainStore');

$router->post("/activity/payment/select", "ActivityOrderController@paymentOptions")->name('activity.admin.payment.select');

//退款管理
$router->get('/refund', 'ActivityRefundController@index')->name('admin.activity.refund');
$router->get('/refund/show/{id}', 'ActivityRefundController@show')->name('admin.activity.refund.show');
$router->post('/refund/store/', 'ActivityRefundController@store')->name('admin.activity.refund.store');
$router->get('/refund/export', 'ActivityRefundController@export')->name('admin.activity.refund.export');

//活动商品
$router->get('getSpu', 'ActivityGoodsController@getSpu')->name('admin.activity.getSpu');
$router->post('getSpuData', 'ActivityGoodsController@getSpuData')->name('admin.activity.getSpuData');
$router->get('getSelectGoods', 'ActivityGoodsController@getSelectGoods')->name('admin.activity.getSelectGoods');

