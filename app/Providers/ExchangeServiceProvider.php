<?php

namespace App\Providers;

use App\Contracts\ExchangeContract;
use App\Services\ExchangeService;
use Illuminate\Support\ServiceProvider;

/**
 * Class ExchangeServiceProvider
 * @package App\Providers
 */
class ExchangeServiceProvider  extends ServiceProvider
{

    public function register()
    {
        $this->app->singleton(ExchangeContract::class, function () {
            return new ExchangeService();
        });
    }

    public function provides()
    {
        return [ExchangeContract::class];
    }
}