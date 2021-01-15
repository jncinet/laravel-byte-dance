<?php

namespace Jncinet\LaravelByteDance\Exceptions;

class GatewayException extends Exception
{
    /**
     * InvalidGatewayException constructor.
     * @param $message
     * @param array $raw
     */
    public function __construct($message, $raw = [])
    {
        parent::__construct('gateway_' . $message, $raw, self::GATEWAY);
    }
}