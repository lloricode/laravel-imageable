<?php

namespace Lloricode\LaravelImageable\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider;
use Route;

/**
 * Class LaravelImageableRouteServiceProvider
 *
 * @package Lloricode\LaravelImageable\Providers
 * @author Lloric Mayuga Garcia <lloricode@gmail.com>
 */
class LaravelImageableRouteServiceProvider extends RouteServiceProvider
{
    /**
     * @var string
     */
    const NAMESPAME = 'Lloricode\LaravelImageable\Http\Controllers';

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
        Route::prefix('api/imageable')->middleware('api')->as('imageable.api.')->namespace(self::NAMESPAME)->group(__DIR__ . '/../resources/routes/api-route.php');
    }

    protected function mapWebRoutes()
    {
        Route::prefix('imageable')->middleware('web')->as('imageable.web.')->namespace(self::NAMESPAME)->group(__DIR__ . '/../resources/routes/web-route.php');
    }
}
