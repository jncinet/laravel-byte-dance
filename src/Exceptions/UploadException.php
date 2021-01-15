<?php

namespace Jncinet\LaravelByteDance\Exceptions;

class UploadException extends Exception
{
    /**
     * UploadException constructor.
     * @param $message
     * @param array $raw
     */
    public function __construct($message, $raw = [])
    {
        parent::__construct('upload_' . $message, $raw, self::ERROR_UPLOAD);
    }
}