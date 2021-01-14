<?php

namespace Jncinet\LaravelByteDance\Gateways\DouYin\User;

use Jncinet\LaravelByteDance\Kernel\BaseClient;

/**
 * Class UserInfo
 * @api https://open.douyin.com/platform/doc/6848806527751489550
 * @package Jncinet\LaravelByteDance\Gateways\DouYin\User
 */
class UserInfo extends BaseClient
{
    const URL = 'https://open.douyin.com/oauth/userinfo/';

    protected $response;

    /**
     * UserInfo constructor.
     * @param $open_id
     * @param $access_token
     */
    public function __construct($open_id, $access_token)
    {
        parent::__construct();

        $response = $this->http->get(self::URL, [
            'query' => [
                'open_id' => $open_id,
                'access_token' => $access_token
            ]
        ]);

        $this->response = $this->isSuccess($response) ? $this->getResponse($response) : [];
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->response['data'] ?? [];
    }

    /**
     * 国家
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->all()['country'] ?? null;
    }

    /**
     * 省份
     *
     * @return string
     */
    public function getProvince()
    {
        return $this->all()['province'] ?? null;
    }

    /**
     * 城市
     *
     * @return string
     */
    public function getCity()
    {
        return $this->all()['city'] ?? null;
    }

    /**
     * UnionId
     *
     * @return string
     */
    public function getUnionId()
    {
        return $this->all()['union_id'] ?? null;
    }

    /**
     * 头像
     *
     * @return string
     */
    public function getAvatar()
    {
        return $this->all()['avatar'] ?? null;
    }

    /**
     * 账户类型
     *
     * @return string
     */
    public function getEAccountRole()
    {
        return $this->all()['e_account_role'] ?? null;
    }

    /**
     * 昵称
     *
     * @return string
     */
    public function getNickname()
    {
        return $this->all()['nickname'] ?? null;
    }
}