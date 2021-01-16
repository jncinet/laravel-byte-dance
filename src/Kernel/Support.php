<?php

namespace Jncinet\LaravelByteDance\Kernel;

use SimpleSoftwareIO\QrCode\Facades\QrCode;

/**
 * Class Support
 * @package Jncinet\LaravelByteDance\Kernel
 */
class Support
{
    /**
     * @param string $text
     * @param array $params
     * @return mixed
     */
    public static function makeQrCode($text, $params = [])
    {
        $qrCode = QrCode::format($params['format'] ?? 'png');

        if (array_key_exists('margin', $params)) {
            $qrCode = $qrCode->margin($params['margin']);
        }

        if (array_key_exists('color', $params) &&
            array_key_exists('r', $params['color']) &&
            array_key_exists('g', $params['color']) &&
            array_key_exists('b', $params['color'])) {
            $qrCode = $qrCode->color($params['color']['r'], $params['color']['g'], $params['color']['b']);
        }

        if (array_key_exists('backgroundColor', $params)
            && array_key_exists('r', $params['backgroundColor'])
            && array_key_exists('g', $params['backgroundColor'])
            && array_key_exists('b', $params['backgroundColor'])) {
            $qrCode = $qrCode->backgroundColor($params['backgroundColor']['r'], $params['backgroundColor']['g'],
                $params['backgroundColor']['b']);
        }

        if (array_key_exists('merge', $params)) {
            $qrCode = $qrCode->merge($params['merge']);
        }

        return $qrCode->size($params['size'] ?? 360)->generate($text, $params['filename'] ?? null);
    }

    /**
     * @param $str
     * @return mixed
     */
    private static function escapeQuotes($str)
    {
        $find = array("\\", "\"");
        $replace = array("\\\\", "\\\"");
        return str_replace($find, $replace, $str);
    }

    /**
     * 获取文件mimeType
     *
     * @param $filename
     * @return mixed
     */
    public static function getFileMimeType($filename)
    {
        $f = finfo_open(FILEINFO_MIME);
        $mimeType = finfo_file($f, $filename);
        finfo_close($f);
        return $mimeType;
    }
}