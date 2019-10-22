<?php

namespace GuojiangClub\Activity\Server\Providers;

use GuojiangClub\Activity\Server\Http\Middleware\ActivityMiddleware;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use ElementVip\Server\Serializer\DataArraySerializer;
use Dingo\Api\Transformer\Adapter\Fractal;
use League\Fractal\Manager;
use Route;
use Event;

class ServerServiceProvider extends ServiceProvider
{

    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'GuojiangClub\Activity\Server\Http\Controllers';

	/**
	 * 要注册的订阅者类。
	 *
	 * @var array
	 */
	protected $subscribe = [
		'GuojiangClub\Activity\Server\Listeners\ActivityPointEventListener',
	];
    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        $this->app['Dingo\Api\Transformer\Factory']->setAdapter(function ($app) {
            $fractal = new Manager();
            $fractal->setSerializer(new DataArraySerializer());
            return new Fractal($fractal);
        });

	    foreach ($this->subscribe as $item) {
		    Event::subscribe($item);
	    }

        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'activity-server');
    }

    public function register()
    {
        $this->app->register('ElementVip\Activity\Core\Providers\ActivityServiceProvider');
//        $this->app[\Illuminate\Routing\Router::class]->middleware('activity', ActivityMiddleware::class);
    }

    public function map()
    {
        $api = app('Dingo\Api\Routing\Router');
        $api->version('v1',
            ['middleware' => ['api', 'cors'], 'namespace' => $this->namespace], function ($router) {
                require __DIR__ . '/../Http/routes.php';
            });


        Route::group([
            'middleware' => 'web',
            'namespace' => $this->namespace,
            'prefix' => 'activity'
        ], function ($router) {
            $router->get('/oauth/wxlogin', [
                'uses' => 'AuthController@getOpenId',
            ]);

            $router->get('detail/{id}', [
                'uses' => 'ActivityController@detail',
            ]);
        });
    }

}
