<?php
namespace GuoJiangClub\Component\Favorite\Providers;

use GuoJiangClub\Component\Favorite\Models\Favorite;
use GuoJiangClub\Component\Product\Models\Goods;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class FavoriteServiceProvider  extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->registerMigrations();
        }
        
        Relation::morphMap([
            'goods' => Goods::class,
        ]);
    }

    protected function registerMigrations()
    {
        return $this->loadMigrationsFrom(__DIR__ . '/../../migrations');
    }


}