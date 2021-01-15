<?php

namespace Jncinet\LaravelByteDance\Gateways\DouYin\MiniProgram;

use Illuminate\Support\Str;
use Jncinet\LaravelByteDance\Exceptions\GatewayException;

/**
 * Class Application
 * @method IsLegal is_legal($access_token, $micapp_id)
 * @package Jncinet\LaravelByteDance\Gateways\DouYin\MiniProgram
 */
class Application
{
    /**
     * @param $method
     * @param $arguments
     * @return mixed
     * @throws GatewayException
     */
    public function __call($method, $arguments)
    {
        return $this->make($method, $arguments);
    }

    /**
     * @param $method
     * @param $arguments
     * @return mixed
     * @throws GatewayException
     */
    protected function make($method, $arguments = [])
    {
        $method = __NAMESPACE__ . '\\' . Str::studly($method);

        if (class_exists($method)) {
            return new $method(...$arguments);
        }

        throw new GatewayException('application_not_exists', ['method' => $method]);
    }
}