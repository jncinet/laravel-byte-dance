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
    /**
     * @param $method
     * @param $config
     * @return mixed
     * @throws \Exception
     */
    public function __call($method, $config)
    {
        $method = __NAMESPACE__ . '\\' . Str::studly($method) . '\\Application';

        if (class_exists($method)) {
            return new $method($config);
        }

        throw new \Exception('232', 1001);
    }
}