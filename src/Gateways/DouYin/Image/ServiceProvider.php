<?php

namespace Jncinet\EasyVideo\Kernel\ByteDance\Douyin\Image;

use Pimple\Container;

class ServiceProvider
{
    public function register(Container $app)
    {
        $app['soter'] = function ($app) {
            return new Client($app);
        };
    }
}