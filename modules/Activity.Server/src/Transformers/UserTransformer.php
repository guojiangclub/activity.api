<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016-08-23
 * Time: 13:25
 */

namespace GuoJiangClub\Activity\Server\Transformers;

use DB;
use ElementVip\Component\Address\Models\Address;
use ElementVip\Component\User\Models\Role;
use ElementVip\Distribution\Core\Models\Agent;

class UserTransformer extends BaseTransformer
{
	public static $excludeable = [
		'password',
		'confirmation_code',
		'remember_token',
	];

	protected $availableIncludes = [
		'group',
		'size',
	];

	public function transformData($model)
	{
		$user                   = array_except($model->toArray(), self::$excludeable);
		$user['user_info_fill'] = 1;
		if (!$user['avatar'] AND !$user['nick_name']) {
			$user['user_info_fill'] = 0;
		}

		if (!$user['avatar']) {
			$user['avatar'] = 'https://ibrand-miniprogram.oss-cn-hangzhou.aliyuncs.com/%E5%B0%8F%E7%A8%8B%E5%BA%8F/%E5%A4%B4%E5%83%8F_%E7%94%BB%E6%9D%BF%201.png';
		}

		$user['wecaht_group'] = false;
		if ($model->hasRole('wechatmanager') AND settings('other_get_gid')) {
			$user['wecaht_group'] = true;
		}

		return $user;
	}

	/**
	 * Include Group
	 *
	 * @param $model
	 *
	 * @return \League\Fractal\Resource\Item|null
	 */
	public function includeGroup($model)
	{
		$group = $model->group;
		if (is_null($group)) {
			return null;
		}

		return $this->item($group, new GroupTransformer(), '');
	}

	/**
	 * Include Size
	 *
	 * @param $model
	 *
	 * @return \League\Fractal\Resource\Item|null
	 */
	public function includeSize($model)
	{
		$size = $model->size;
		if (is_null($size)) {
			return null;
		}

		return $this->item($size, new SizeTransformer(), '');
	}

}

class GroupTransformer extends BaseTransformer
{
	public function transformData($model)
	{
		return $model->toArray();
	}
}

class SizeTransformer extends BaseTransformer
{
	public function transformData($model)
	{
		return $model->toArray();
	}
}