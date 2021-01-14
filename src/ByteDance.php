<?php

namespace Jncinet\LaravelByteDance;

use Illuminate\Support\Str;

/**
 * Class ByteDance
 * @method static Gateways\DouYin\Application DouYin()
 * @package Jncinet\LaravelByteDance
 */
class ByteDance
{
    /**
     * Dynamically pass methods to the application.
     *
     * @param string $name
     * @param array $arguments
     *
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        return self::make($name, ...$arguments);
    }

    /**
     * @param $name
     * @param $config
     * @return mixed
     */
    protected static function make($name, ...$config)
    {
        $application = __NAMESPACE__ . '\\GateWays\\' . Str::studly($name) . '\\Application';
        return new $application($config);
    }
}