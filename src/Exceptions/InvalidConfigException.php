<?php

namespace Jncinet\LaravelByteDance\Exceptions;


class InvalidConfigException extends Exception
{
    /**
     * InvalidConfigException constructor.
     * @param $message
     * @param array $raw
     */
    public function __construct($message, $raw = [])
    {
        parent::__construct('INVALID_CONFIG: '.$message, $raw, self::INVALID_CONFIG);
    }
}