<?php

namespace GuoJiangClub\Activity\Admin\Providers;

use GuoJiangClub\Activity\Admin\ActivityAdmin;
use Illuminate\Support\ServiceProvider;
use Route;

class ActivityAdminProvider extends ServiceProvider
{
	/**
	 * This namespace is applied to your controller routes.
	 *
	 * In addition, it is set as the URL generator's root namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'GuoJiangClub\Activity\Admin\Http\Controllers';

	/**
	 * Define your route model bindings, pattern filters, etc.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->loadViewsFrom(__DIR__ . '/../../resources/views', 'activity');

		$this->publishes([
            __DIR__ . '/../../resources/assets/libs' => public_path('assets/backend/libs'),
			__DIR__ . '/../../resources/assets' => public_path('assets/backend/activity')
		], 'backend-activity');

		$this->registerMigrations();
		$this->map();

		ActivityAdmin::boot();
	}

	public function map()
	{
		Route::group(['middleware' => ['web', 'admin'], 'namespace' => $this->namespace, 'prefix' => 'admin/activity'], function ($router) {
			require __DIR__ . '/../Http/routes.php';
		});
	}

	protected function registerMigrations()
	{
		return $this->loadMigrationsFrom(__DIR__ . '/../../../Core/migrations');
	}
}
