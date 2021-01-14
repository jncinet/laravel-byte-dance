<?php

namespace Jncinet\LaravelByteDance\Gateways\DouYin\User;

use Jncinet\LaravelByteDance\Kernel\BaseClient;

/**
 * Class Fans
 * @api https://open.douyin.com/platform/doc/6848806544893675533
 * @package Jncinet\LaravelByteDance\Gateways\DouYin\User
 */
class Fans extends BaseClient
{
    const URL = 'https://open.douyin.com/fans/list/';

    protected $open_id, $access_token;

    /**
     * Fans constructor.
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
     * @param int $page
     * @param int $limit
     * @return array
     */
    public function paginate($page = 0, $limit = 10)
    {
        $response = $this->http->get(self::URL, [
            'query' => [
                'open_id' => $this->open_id,
                'access_token' => $this->access_token,
                'cursor' => intval($page),
                'count' => intval($limit) ?: 10,
            ]
        ]);

        return $this->isSuccess($response) ? $this->getResponse($response)['data'] : [];
    }
}