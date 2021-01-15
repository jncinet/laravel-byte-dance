<?php

namespace Jncinet\LaravelByteDance\Gateways\DouYin;

use Illuminate\Support\Str;
use Jncinet\LaravelByteDance\Exceptions\GatewayException;

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

        throw new GatewayException('application_not_exists', ['method' => $method]);
    }
}