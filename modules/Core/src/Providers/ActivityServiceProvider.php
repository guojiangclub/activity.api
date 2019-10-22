<?php

namespace GuojiangClub\Activity\Core\Providers;

use GuojiangClub\Activity\Core\Console\ActivityCityCommand;
use GuojiangClub\Activity\Core\Console\ActivityCommand;
use GuojiangClub\Activity\Core\Discount\Actions\ActivityAction;
use GuojiangClub\Activity\Core\Discount\Checkers\ContainsActivityRuleChecker;

use GuojiangClub\Activity\Core\Models\Activity;
use GuojiangClub\Activity\Core\Repository\CouponRepository;
use GuojiangClub\Activity\Core\Repository\DiscountRepository;
use GuojiangClub\Activity\Core\Repository\Eloquent\CouponRepositoryEloquent;
use GuojiangClub\Activity\Core\Repository\Eloquent\DiscountRepositoryEloquent;
use GuojiangClub\Activity\Core\Schedule\LateSchedule;
use GuojiangClub\Activity\Core\Schedule\OmsSchedule;
use GuojiangClub\Activity\Core\Schedule\RemindSchedule;
use GuojiangClub\Activity\Server\Transformers\FavoriteTransformer;
use Event;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class ActivityServiceProvider extends ServiceProvider
{

    protected $subscribe = [
        'GuojiangClub\Activity\Core\Listeners\ActivityEventListener',
    ];

    public function boot()
    {
        $this->registerMigrations();

        $this->commands([
            ActivityCommand::class,
            ActivityCityCommand::class
        ]);

        $this->publishes([
            __DIR__ . '/../../factories/ActivityFactory.php' => database_path('factories/ActivityFactory.php')
        ], 'ActivityFactory');

        foreach ($this->subscribe as $subscriber) {
            Event::subscribe($subscriber);
        }
    }

    protected function registerMigrations()
    {
        return $this->loadMigrationsFrom(__DIR__ . '/../../migrations');
    }

    public function register()
    {
        $this->app->make('ElementVip\ScheduleList')->add(LateSchedule::class);
        //取消这种定时执行方式
        $this->app->make('ElementVip\ScheduleList')->add(RemindSchedule::class);

        //$this->app->make('ElementVip\ScheduleList')->add(OmsSchedule::class);

        $this->app->bind(
            ActivityAction::class,
            ActivityAction::class
        );

        $this->app->alias(ActivityAction::class, ActivityAction::TYPE);

        $this->app->bind(
            ContainsActivityRuleChecker::class,
            ContainsActivityRuleChecker::class
        );

        $this->app->alias(ContainsActivityRuleChecker::class, ContainsActivityRuleChecker::TYPE);

        $this->app->bind(DiscountRepository::class, DiscountRepositoryEloquent::class);
        /*$this->app->bind(CouponRepository::class, CouponRepositoryEloquent::class);*/

        $this->app->singleton(CouponRepository::class , CouponRepositoryEloquent::class);

        $this->app->alias(CouponRepository::class, 'coupon.repository');

        Relation::morphMap([
            'activity' => Activity::class,
        ]);

        $this->app->singleton('activity_favorite_transformer', function () {
            return new FavoriteTransformer();
        });
    }

}
