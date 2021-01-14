<?php

namespace Jncinet\LaravelByteDance\Gateways\DouYin\Image;

use Jncinet\LaravelByteDance\Kernel\BaseClient;

/**
 * Class Delete
 * @package Jncinet\LaravelByteDance\Gateways\DouYin\Image
 */
class Delete extends BaseClient
{
    const URL = 'https://open.douyin.com/image/delete/';

    protected $open_id;
    protected $access_token;

    /**
     * Delete constructor.
     * @param $open_id
     * @param $access_token
     */
    public function __construct($open_id, $access_token)
    {
        parent::__construct();

        $this->open_id = $open_id;
        $this->access_token = $access_token;
    }

    /**
     * 删除图片
     *
     * @param $item_id
     * @return mixed|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function destroy($item_id)
    {
        $response = $this->http->request('POST', self::URL, [
            'query' => [
                'open_id' => $this->open_id,
                'access_token' => $this->access_token,
            ],
            'json' => [
                'item_id' => $item_id
            ]
        ]);

        return $this->getResponse($response);
    }
}