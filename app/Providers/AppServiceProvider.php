<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\MinecraftLookupService;
use App\Services\SteamLookupService;
use App\Services\XblLookupService;
use App\Contracts\LookupServiceInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(LookupServiceInterface::class, MinecraftLookupService::class);
        $this->app->bind(LookupServiceInterface::class, SteamLookupService::class);
        $this->app->bind(LookupServiceInterface::class, XblLookupService::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
