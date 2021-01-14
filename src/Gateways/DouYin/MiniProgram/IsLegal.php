<?php

namespace Jncinet\LaravelByteDance\Gateways\DouYin\MiniProgram;

use Jncinet\LaravelByteDance\Kernel\BaseClient;

/**
 * Class IsLegal
 * @api https://open.douyin.com/platform/doc/6848798536256014348
 * @package Jncinet\LaravelByteDance\Gateways\DouYin\MiniProgram
 */
class IsLegal extends BaseClient
{
    const URL = 'https://open.douyin.com/devtool/micapp/is_legal/';

    protected $access_token, $micapp_id;

    /**
     * IsLegal constructor.
     * @param $access_token
     * @param $micapp_id
     */
    public function __construct($access_token, $micapp_id)
    {
        parent::__construct();
        $this->access_token = $access_token;
        $this->micapp_id = $micapp_id;
    }

    /**
     * 校验小程序appid是否可挂载到短视频
     *
     * @return bool
     */
    public function check(): bool
    {
        $response = $this->http->get(self::URL, [
            'query' => [
                'access_token' => $this->access_token,
                'micapp_id' => $this->micapp_id,
            ]
        ]);

        return $this->isSuccess($response) ? $this->getResponse($response)['data']['is_legal'] ?? false : false;
    }
}