<?php

namespace Jncinet\LaravelByteDance\Gateways\DouYin\OAuth;

use Jncinet\LaravelByteDance\Kernel\BaseClient;

/**
 * Class AccessToken
 * @package Jncinet\LaravelByteDance\Gateways\DouYin\OAuth
 */
class AccessToken extends BaseClient
{
    const URL = [
        'get' => 'https://open.douyin.com/oauth/access_token/',
        'renew_refresh' => 'https://open.douyin.com/oauth/renew_refresh_token/',
        'client' => 'https://open.douyin.com/oauth/client_token/',
        'refresh' => 'https://open.douyin.com/oauth/refresh_token/',
    ];

    /**
     * 获取授权码(code)
     *
     * @param $code
     * @return mixed|null
     */
    public function get($code)
    {
        $response = $this->http->get(self::URL['get'], [
            'query' => [
                'client_key' => $this->client_key,
                'client_secret' => $this->client_secret,
                'code' => $code,
                'grant_type' => 'authorization_code',
            ]
        ]);

        return $this->getResponse($response);
    }

    /**
     * 刷新access_token
     *
     * @param $refresh_token
     * @return mixed|null
     */
    public function refreshToken($refresh_token)
    {
        $response = $this->http->get(self::URL['refresh'], [
            'query' => [
                'client_key' => $this->client_key,
                'grant_type' => 'refresh_token',
                'refresh_token' => $refresh_token,
            ]
        ]);

        return $this->getResponse($response);
    }

    /**
     * 刷新refresh_token
     *
     * @param $refresh_token
     * @return mixed|null
     */
    public function renewRefreshToken($refresh_token)
    {
        $response = $this->http->get(self::URL['renew_refresh'], [
            'query' => [
                'client_key' => $this->client_key,
                'refresh_token' => $refresh_token,
            ]
        ]);

        return $this->getResponse($response);
    }

    /**
     * 生成client_token
     *
     * @return mixed|null
     */
    public function clientToken()
    {
        $response = $this->http->get(self::URL['client'], [
            'query' => [
                'client_key' => $this->client_key,
                'client_secret' => $this->client_secret,
                'grant_type' => 'client_credential',
            ]
        ]);

        return $this->getResponse($response);
    }
}