<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class QRCodeProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('BaconQrCode\Writer', function($app) {
            return new Writer(new ImageRenderer(
                new RendererStyle(400),
                new ImagickImageBackEnd()
            ));
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(Writer $writer)
    {
        //
    }
}
