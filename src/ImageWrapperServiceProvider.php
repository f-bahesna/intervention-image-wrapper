<?php
declare(strict_types=1);

namespace Fbahesna\InterventionImageWrapper;

use Fbahesna\InterventionImageWrapper\Services\ImageWrapperService;
use Illuminate\Support\ServiceProvider;

/**
 * @author frada <fbahezna@gmail.com>
 */
class ImageWrapperServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/Config/imagewrapper.php', 'imagewrapper');

        $this->app->singleton(ImageWrapperService::class, function ($app) {
            return new ImageWrapperService(
                $app['config']->get('imagewrapper')
            );
        });

        $this->app->alias(ImageWrapperService::class, 'imagewrapper');
    }

    public function boot()
    {
        $this->publishes(
            [
                __DIR__.'/Config/imagewrapper.php' => config_path('imagewrapper.php'),
            ],
            'imagewrapper-config'
        );
    }
}