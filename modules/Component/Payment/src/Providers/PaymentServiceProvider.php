<?php

namespace GuoJiangClub\Component\Payment\Providers;

use GuoJiangClub\Component\Payment\Charges\DefaultCharge;
use GuoJiangClub\Component\Payment\Charges\PingxxCharge;
use GuoJiangClub\Component\Payment\Contracts\PaymentChargeContract;
use Illuminate\Support\ServiceProvider;

class PaymentServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->registerMigrations();
        }

        $this->publishes([$this->configPath() => config_path('payment.php')]);

        $this->publishes([$this->configPay() => config_path('pay.php')], 'pay');
    }

    protected function registerMigrations()
    {
        return $this->loadMigrationsFrom(__DIR__ . '/../../migrations');
    }

    public function register()
    {
        $this->mergeConfigFrom($this->configPath(), 'payment');

        $this->app->singleton(PaymentChargeContract::class, function ($app) {

            if (settings('enabled_pingxx_pay')) {
                return new PingxxCharge('pingxx');
            }

            return new DefaultCharge('default');
        });
    }


    protected function configPath()
    {
        return __DIR__ . '/../../config/payment.php';
    }

    protected function configPay()
    {
        return __DIR__ . '/../../config/pay.php';
    }
}