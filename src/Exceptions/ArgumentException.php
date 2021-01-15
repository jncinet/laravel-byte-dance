<?php

namespace Jncinet\LaravelByteDance\Exceptions;

class ArgumentException extends Exception
{
    /**
     * InvalidArgumentException constructor.
     * @param $message
     * @param array $raw
     */
    public function __construct($message, $raw = [])
    {
        parent::__construct('argument_' . $message, $raw, self::ARGUMENT);
    }
}