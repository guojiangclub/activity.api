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
