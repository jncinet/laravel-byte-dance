<?php

namespace Jncinet\LaravelByteDance;

use Illuminate\Support\Str;
use Jncinet\LaravelByteDance\Exceptions\GatewayException;

/**
 * Class ByteDance
 * @method static Gateways\DouYin\Application DouYin()
 * @package Jncinet\LaravelByteDance
 */
class ByteDance
{
    /**
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws GatewayException
     */
    public static function __callStatic($name, $arguments)
    {
        return self::make($name);
    }

    /**
     * @param $name
     * @return mixed
     * @throws GatewayException
     */
    protected static function make($name)
    {
        $application = __NAMESPACE__ . '\\GateWays\\' . Str::studly($name) . '\\Application';

        if (class_exists($application)) {
            return new $application();
        }

        throw new GatewayException('not_exists', ['name' => $name]);
    }
}