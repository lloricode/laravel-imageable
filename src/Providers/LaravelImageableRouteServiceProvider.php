<?php

namespace Lloricode\LaravelImageable\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Route;

class LaravelImageableRouteServiceProvider extends RouteServiceProvider
{
    protected $namespace = 'Lloricode\LaravelImageable\Http\Controllers';

    public function boot()
    {
        parent::boot();
    }

    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();
    }

    protected function mapApiRoutes()
    {
        Route::prefix('api/imageable')->middleware('api')->as('imageable.api.')->namespace($this->namespace)->group(__DIR__.'/../resources/routes/api-route.php');
    }

    protected function mapWebRoutes()
    {
        Route::prefix('imageable')->middleware('web')->as('imageable.web.')->namespace($this->namespace)->group(__DIR__.'/../resources/routes/web-route.php');
    }
}
