<?php

/*
 * This file is part of ibrand/member-backend.
 *
 * (c) iBrand <https://www.ibrand.cc>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$router->group(['prefix' => 'admin/member'], function () use ($router) {

    $router->resource('users', 'UserController', ['except' => ['show'],
        'names' => [
            'index' => 'admin.users.index',
            'create' => 'admin.users.create',
            'store' => 'admin.users.store',
            'edit' => 'admin.users.edit',
            'update' => 'admin.users.update',
            'destroy' => 'admin.users.destroy',
        ],
    ]);

    $router->get('users/banned', 'UserController@banned')->name('admin.users.banned');

    $router->get('users/getUserPointData/{id}', 'UserController@getUserPointData')->name('admin.users.getUserPointList');

    $router->post('users/addPoint', 'UserController@addPoint')->name('admin.users.addPoint');

    $router->group(['prefix' => 'user/{id}', 'where' => ['id' => '[0-9]+']], function () use ($router) {
        $router->get('restore', 'UserController@restore')->name('admin.user.restore');
        $router->get('mark/{status}', 'UserController@mark')->name('admin.user.mark')->where(['status' => '[0,1,2]']);
    });

});

//SVIP管理
$router->group(['prefix' => 'admin/member/svip'], function () use ($router) {
    $router->get("settings", "SvipController@settings")->name('admin.svip.settings');

    $router->group(['prefix' => 'plan'], function () use ($router) {
        $router->get("list", "SvipController@index")->name('admin.svip.plan.list');
        $router->get("create", "SvipController@create")->name('admin.svip.plan.create');
        $router->get("{id}/edit", "SvipController@edit")->name('admin.svip.plan.edit');
        $router->post("store", "SvipController@store")->name('admin.svip.plan.store');
    });

    $router->group(['prefix' => 'member'], function () use ($router) {
        $router->get("list", "SvipMemberController@index")->name('admin.svip.member.list');
        $router->get("getExportData", "SvipMemberController@getExportData")->name('admin.svip.member.getExportData');

    });

});
