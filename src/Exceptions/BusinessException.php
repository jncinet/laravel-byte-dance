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
        parent::__construct('ERROR_BUSINESS: '.$message, $raw, self::ERROR_BUSINESS);
    }
}