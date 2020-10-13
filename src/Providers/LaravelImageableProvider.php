<?php

namespace Lloricode\LaravelImageable\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Class LaravelImageableProvider
 *
 * @package Lloricode\LaravelImageable\Providers
 * @author Lloric Mayuga Garcia <lloricode@gmail.com>
 */
class LaravelImageableProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if (!class_exists('CreateImageablesTable')) {
            // Publish Image Config
            $this->publishes([__DIR__.'/../../config/imageable.php' => config_path('imageable.php'),]);

            $timestamp = date('Y_m_d_His', time());
            $this->publishes(
                [
                    __DIR__.'/../../database/migrations/migration.stub' =>
                        $this->app->databasePath("/migrations/{$timestamp}_create_imageables_table.php"),
                ],
                'migrations'
            );
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/imageable.php', 'imageable');
    }
}
