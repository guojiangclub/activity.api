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

$router->group(['prefix' => 'admin/member/RoleManagement'], function () use ($router) {
    $router->get('role/index', 'RoleController@index')->name('admin.RoleManagement.role.index');
    $router->get('role/create', 'RoleController@create')->name('admin.RoleManagement.role.create');
    $router->post('role/store', 'RoleController@store')->name('admin.RoleManagement.role.store');
    $router->post('role/{id}/delete', 'RoleController@delete')->name('admin.RoleManagement.role.delete');
    $router->patch('role/{id}/update', 'RoleController@update')->name('admin.RoleManagement.role.update');
    $router->get('role/{id}/edit', 'RoleController@edit')->name('admin.RoleManagement.role.edit');
    $router->get('roleUser/{id}/edit', 'RoleController@roleUserEdit')->name('admin.RoleManagement.roleUser.edit');
    $router->patch('roleUser/{id}/update', 'RoleController@roleUserUpdate')->name('admin.RoleManagement.userRole.update');

    $router->get('role/userList/{id}', 'RoleController@userList')->name('admin.RoleManagement.role.userList');

    // 批量用户分配角色
    $router->get('role/{id}/userModal', 'RoleController@userModal')->name('admin.RoleManagement.role.userModal');
    $router->post('role/allotRole', 'RoleController@allotAddRole')->name('admin.RoleManagement.role.allotAddRole');
    $router->get('role/UsersSearchList', 'RoleController@UsersSearchList')->name('admin.RoleManagement.role.UsersSearchList');

    $router->post('role/{id}/allotDelRole', 'RoleController@allotDelRole')->name('admin.RoleManagement.role.allotDelRole');
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
