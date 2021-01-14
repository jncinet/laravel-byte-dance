<?php

namespace Jncinet\LaravelByteDance\Exceptions;


class InvalidArgumentException extends Exception
{
    /**
     * InvalidArgumentException constructor.
     * @param $message
     * @param array $raw
     */
    public function __construct($message, $raw = [])
    {
        parent::__construct('INVALID_ARGUMENT: '.$message, $raw, self::INVALID_ARGUMENT);
    }
}