<?php

namespace Jncinet\LaravelByteDance\Exceptions;

class Exception extends \Exception
{
    const UNKNOWN_ERROR = 9999;

    const INVALID_GATEWAY = 1001;

    const INVALID_CONFIG = 1002;

    const INVALID_ARGUMENT = 1003;

    const ERROR_GATEWAY = 1004;

    const INVALID_SIGN = 1005;

    const ERROR_BUSINESS = 1006;

    /**
     * Raw error info.
     *
     * @var array
     */
    public $raw;

    /**
     * Bootstrap.
     *
     * @author yansongda <me@yansonga.cn>
     *
     * @param string       $message
     * @param array|string $raw
     * @param int|string   $code
     */
    public function __construct($message = '', $raw = [], $code = self::UNKNOWN_ERROR)
    {
        $message = '' === $message ? 'Unknown Error' : $message;
        $this->raw = is_array($raw) ? $raw : [$raw];

        parent::__construct($message, intval($code));
    }
}