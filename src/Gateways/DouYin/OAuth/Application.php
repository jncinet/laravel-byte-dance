<?php

namespace Jncinet\LaravelByteDance\Gateways\DouYin\OAuth;

use Illuminate\Support\Str;
use Jncinet\LaravelByteDance\Exceptions\GatewayException;

/**
 * Class Application
 * @method AccessToken access_token()
 * @method Js js($access_token)
 * @method Authorize authorize($scope, $redirect_uri, $state = null, $optionalScope = null)
 * @package Jncinet\LaravelByteDance\Gateways\DouYin\OAuth
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