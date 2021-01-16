<?php

namespace Jncinet\LaravelByteDance\Gateways\DouYin\OAuth;

use Jncinet\LaravelByteDance\Kernel\Support;
use Jncinet\LaravelByteDance\Kernel\BaseClient;

/**
 * Class Authorize
 * @package Jncinet\LaravelByteDance\Gateways\DouYin\OAuth
 */
class Authorize extends BaseClient
{
    const URL = [
        'https://open.douyin.com/platform/oauth/connect/',
        'https://aweme.snssdk.com/oauth/authorize/v2/'
    ];

    protected $url;

    /**
     * Authorize constructor.
     * @param $scope
     * @param $redirect_uri
     * @param null $state
     * @param null $optionalScope
     */
    public function __construct($scope, $redirect_uri, $state = null, $optionalScope = null)
    {
        parent::__construct();
        $params = [
            'client_key' => $this->client_key,
            'response_type' => 'code',
            'scope' => $scope,
            'optionalScope' => $optionalScope,
            'redirect_uri' => $redirect_uri,
            'state' => $state,
        ];

        $params = array_filter($params, function ($value) {
            return '' !== $value && !is_null($value);
        });

        if ($params['scope'] === 'login_id') {
            unset($params['optionalScope']);
            $this->url = self::URL[1];
        } else {
            $this->url = self::URL[0];
        }
        $this->url .= '?' . http_build_query($params);
    }

    /**
     * 返回URL址
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * 返回授权二维码
     *
     * @param $params
     * @return mixed
     */
    public function getQrCode($params = [])
    {
        return Support::makeQrCode($this->url, $params);
    }
}