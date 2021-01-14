<?php

namespace Jncinet\LaravelByteDance;

use Illuminate\Support\Str;
use Jncinet\LaravelByteDance\Exceptions\InvalidGatewayException;

/**
 * Class ByteDance
 * @method static Gateways\DouYin\Application DouYin()
 * @package Jncinet\LaravelByteDance
 */
class ByteDance
{
    /**
     * @param $name
     * @return mixed
     * @throws InvalidGatewayException
     */
    public static function __callStatic($name)
    {
        return self::make($name);
    }

    /**
     * @param $name
     * @return mixed
     * @throws InvalidGatewayException
     */
    protected static function make($name)
    {
        $application = __NAMESPACE__ . '\\GateWays\\' . Str::studly($name) . '\\Application';

        if (class_exists($application)) {
            return new $application();
        }

        throw new InvalidGatewayException("Gateway [{$name}] Not Exists");
    }
}