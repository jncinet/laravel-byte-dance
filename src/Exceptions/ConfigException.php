<?php

namespace Jncinet\LaravelByteDance\Exceptions;

class ConfigException extends Exception
{
    /**
     * InvalidConfigException constructor.
     * @param $message
     * @param array $raw
     */
    public function __construct($message, $raw = [])
    {
        parent::__construct('config_' . $message, $raw, self::CONFIG);
    }
}