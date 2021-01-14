<?php

namespace Jncinet\LaravelByteDance\Gateways\DouYin\Video;

use Jncinet\LaravelByteDance\Kernel\BaseClient;

/**
 * Class Search
 * @package Jncinet\LaravelByteDance\Gateways\DouYin\Video
 */
class Search extends BaseClient
{
    const URL = [
        'list' => 'https://open.douyin.com/video/list/',
        'data' => 'https://open.douyin.com/video/data/',
    ];

    protected $open_id;
    protected $access_token;

    /**
     * Search constructor.
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
     * 查询授权账号视频数据
     *
     * @param int $page
     * @param int $limit
     * @return mixed|null
     */
    public function paginate($page = 0, $limit = 10)
    {
        $response = $this->http->post(self::URL['list'], [
            'query' => [
                'open_id' => $this->open_id,
                'access_token' => $this->access_token,
                'cursor' => $page,
                'count' => $limit,
            ]
        ]);

        return $this->getResponse($response);
    }

    /**
     * 查询指定视频数据
     *
     * @param array $items
     * @return mixed|null
     */
    public function find(array $items = [])
    {
        $response = $this->http->post(self::URL['data'], [
            'query' => [
                'open_id' => $this->open_id,
                'access_token' => $this->access_token,
            ],
            'json' => [
                'item_ids' => $items
            ]
        ]);

        return $this->getResponse($response);
    }
}