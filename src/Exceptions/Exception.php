<?php

namespace Jncinet\LaravelByteDance\Exceptions;

class Exception extends \Exception
{
    const UNKNOWN_ERROR = 9999;

    const GATEWAY = 1001;

    const CONFIG = 1002;

    const ARGUMENT = 1003;

    const BUSINESS = 1004;

    const UPLOAD = 1005;

    /**
     * Exception constructor.
     * @param string $message
     * @param array $raw
     * @param int $code
     */
    public function __construct($message = '', array $raw = [], $code = self::UNKNOWN_ERROR)
    {
        $message = '' === $message ? 'unknown_error' : $message;
        $message = trans($message, $raw);
        parent::__construct($message, intval($code));
    }
}