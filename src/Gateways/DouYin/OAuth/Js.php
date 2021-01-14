<?php

namespace Jncinet\LaravelByteDance\Gateways\DouYin\OAuth;

use Jncinet\LaravelByteDance\Kernel\BaseClient;

/**
 * Class Js
 * @api https://open.douyin.com/platform/doc/6848798514798004236
 * @package Jncinet\LaravelByteDance\Gateways\DouYin\OAuth
 */
class Js extends BaseClient
{
    const URL = 'https://open.douyin.com/js/getticket/';

    protected $response;

    /**
     * Js constructor.
     * @param $access_token
     */
    public function __construct($access_token)
    {
        parent::__construct();
        $this->response = $this->http->get(self::URL, [
            'query' => [
                'access_token' => $access_token
            ]
        ]);

        $this->response = $this->isSuccess($this->response) ? $this->getResponse($this->response) : [];
    }

    /**
     * @return array
     */
    public function data()
    {
        return [
            'ticket' => $this->response['data']['ticket'] ?? null,
            'expires_in' => $this->response['data']['expires_in'] ?? 0,
        ];
    }

    /**
     * ticket
     *
     * @return mixed
     */
    public function ticket()
    {
        return $this->data()['ticket'];
    }

    /**
     * 有效期
     *
     * @return mixed
     */
    public function expiresIn()
    {
        return $this->data()['expires_in'];
    }
}