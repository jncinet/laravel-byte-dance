<?php

namespace Jncinet\LaravelByteDance\Gateways\DouYin\POI;

use Jncinet\LaravelByteDance\Kernel\BaseClient;

/**
 * Class Search
 * @api https://open.douyin.com/platform/doc/6848806527751555086
 * @package Jncinet\LaravelByteDance\Gateways\DouYin\POI
 */
class Search extends BaseClient
{
    const URL = 'https://open.douyin.com/poi/search/keyword/';

    protected $access_token;

    /**
     * Search constructor.
     * @param $access_token
     */
    public function __construct($access_token)
    {
        parent::__construct();

        $this->access_token = $access_token;
    }

    /**
     * 查询POI信息
     *
     * @param $keyword
     * @param int $page
     * @param int $limit
     * @param null $city
     * @return mixed|null
     */
    public function keyword($keyword, $page = 0, $limit = 10, $city = null)
    {
        $response = $this->http->get(self::URL, [
            'query' => array_filter([
                'access_token' => $this->access_token,
                'cursor' => $page,
                'count' => $limit,
                'keyword' => $keyword,
                'city' => $city
            ], function ($video) {
                return '' != $video && !is_null($video);
            })
        ]);

        return $this->getResponse($response);
    }
}