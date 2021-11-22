<?php

namespace App\Providers;

use App\Services\BigQuery;
use App\Services\ParserService;
use App\Services\ReplayService;
use DB;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\ServiceProvider;
use Log;
use Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->singleton(ParserService::class, fn() => new ParserService());

        $this->app->singleton(ReplayService::class, fn() => new ReplayService(new ParserService()));

        $this->app->singleton(BigQuery::class, fn() => new BigQuery());

        \Illuminate\Http\Resources\Json\JsonResource::withoutWrapping();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
