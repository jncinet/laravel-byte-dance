<?php

namespace Jncinet\LaravelByteDance\Gateways\DouYin\Aweme;

use Illuminate\Support\Str;
use Jncinet\LaravelByteDance\Exceptions\InvalidGatewayException;

/**
 * Class Application
 * @method Share share($access_token)
 * @package Jncinet\LaravelByteDance\Gateways\DouYin\Aweme
 */
class Application
{
    /**
     * @param $method
     * @param $arguments
     * @return mixed
     * @throws InvalidGatewayException
     */
    public function __call($method, $arguments)
    {
        return $this->make($method, $arguments);
    }

    /**
     * @param $method
     * @param $arguments
     * @return mixed
     * @throws InvalidGatewayException
     */
    protected function make($method, $arguments = [])
    {
        $method = __NAMESPACE__ . '\\' . Str::studly($method);

        if (class_exists($method)) {
            return new $method(...$arguments);
        }

        throw new InvalidGatewayException("Gateway [{$method}] Not Exists");
    }
}