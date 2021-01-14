<?php

namespace Jncinet\LaravelByteDance\Gateways\DouYin\Video;

use Illuminate\Support\Str;

/**
 * Class Application
 * @method Comment comment($open_id, $access_token)
 * @method Create create($open_id, $access_token, $filename)
 * @method Delete delete($open_id, $access_token)
 * @method Search search($open_id, $access_token)
 * @package Jncinet\LaravelByteDance\Gateways\DouYin\Video
 */
class Application
{
    public function __call($method, $config)
    {
        $method = __NAMESPACE__ . '\\' . Str::studly($method);

        return new $method(...$config);
    }
}