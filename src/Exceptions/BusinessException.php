<?php

namespace Jncinet\LaravelByteDance\Exceptions;

class BusinessException extends Exception
{
    /**
     * BusinessException constructor.
     * @param $message
     * @param array $raw
     */
    public function __construct($message, $raw = [])
    {
        parent::__construct('business_' . $message, $raw, self::BUSINESS);
    }
}