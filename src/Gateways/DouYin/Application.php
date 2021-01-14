<?php

namespace Jncinet\LaravelByteDance\Gateways\DouYin;

use Illuminate\Support\Str;

/**
 * Class Application
 * @method Video\Application Video()
 * @package Jncinet\LaravelByteDance\Gateways\DouYin
 */
class Application
{
    public function __call($method, $config)
    {
        $method = __NAMESPACE__ . '\\' . Str::studly($method) . '\\Application';

        return new $method($config);
    }
}