<?php

/*
 * This file is part of guojiangclub/activity-server.
 *
 * (c) 果酱社区 <https://guojiang.club>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GuoJiangClub\Activity\Server\Providers;

use Dingo\Api\Transformer\Adapter\Fractal;
use Event;
use GuoJiangClub\Activity\Server\Serializer\DataArraySerializer;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use League\Fractal\Manager;
use Route;

class ServerServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'GuoJiangClub\Activity\Server\Http\Controllers';

    /**
     * 要注册的订阅者类。
     *
     * @var array
     */
    protected $subscribe = [
        'GuoJiangClub\Activity\Server\Listeners\ActivityPointEventListener',
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

        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'activity-server');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config.php' => config_path('dmp-api.php'),
            ]);
        }
    }

    public function register()
    {
    }

    public function map()
    {
        $api = app('Dingo\Api\Routing\Router');
        $api->version('v1',
            ['middleware' => ['api', 'cors'], 'namespace' => $this->namespace], function ($router) {
                require __DIR__.'/../Http/routes.php';
            });

        Route::group([
            'middleware' => 'web',
            'namespace' => $this->namespace,
            'prefix' => 'activity',
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
