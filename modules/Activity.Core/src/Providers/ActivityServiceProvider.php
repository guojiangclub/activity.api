<?php

/*
 * This file is part of guojiangclub/activity-core.
 *
 * (c) 果酱社区 <https://guojiang.club>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GuoJiangClub\Activity\Core\Providers;

use GuoJiangClub\Activity\Core\Console\ActivityCityCommand;
use GuoJiangClub\Activity\Core\Console\ActivityCommand;
use GuoJiangClub\Activity\Core\Discount\Actions\ActivityAction;
use GuoJiangClub\Activity\Core\Discount\Checkers\ContainsActivityRuleChecker;
use GuoJiangClub\Activity\Core\Models\Activity;
use GuoJiangClub\Activity\Core\Repository\CouponRepository;
use GuoJiangClub\Activity\Core\Repository\DiscountRepository;
use GuoJiangClub\Activity\Core\Repository\Eloquent\CouponRepositoryEloquent;
use GuoJiangClub\Activity\Core\Repository\Eloquent\DiscountRepositoryEloquent;
use GuoJiangClub\Activity\Server\Transformers\FavoriteTransformer;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class ActivityServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->registerMigrations();

        $this->commands([
            ActivityCommand::class,
            ActivityCityCommand::class,
        ]);

        $this->publishes([
            __DIR__.'/../../factories/ActivityFactory.php' => database_path('factories/ActivityFactory.php'),
        ], 'ActivityFactory');

        if (!class_exists('EntrustSetupTables')) {
            $timestamp = date('Y_m_d_His', time() + 100);
            $this->publishes([
                __DIR__.'/../../migrations/entrust_setup_tables.php.stub' => database_path()."/migrations/{$timestamp}_entrust_setup_tables.php",
            ], 'migrations');
        }
    }

    protected function registerMigrations()
    {
        return $this->loadMigrationsFrom(__DIR__.'/../../migrations');
    }

    public function register()
    {
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

        $this->app->singleton(CouponRepository::class, CouponRepositoryEloquent::class);

        $this->app->alias(CouponRepository::class, 'coupon.repository');

        Relation::morphMap([
            'activity' => Activity::class,
        ]);

        $this->app->singleton('activity_favorite_transformer', function () {
            return new FavoriteTransformer();
        });
    }
}
