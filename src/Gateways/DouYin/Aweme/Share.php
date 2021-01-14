<?php

namespace Jncinet\LaravelByteDance\Gateways\DouYin\Aweme;

use Jncinet\LaravelByteDance\Kernel\BaseClient;

/**
 * Class Share
 * @api https://open.douyin.com/platform/doc/6848798622172121099
 * @package Jncinet\LaravelByteDance\Gateways\DouYin\Aweme
 */
class Share extends BaseClient
{
    const URL = 'https://open.douyin.com/share-id/';

    protected $access_token;

    /**
     * Share constructor.
     * @param $access_token
     */
    public function __construct($access_token)
    {
        parent::__construct();

        $this->access_token = $access_token;
    }

    /**
     * 获取share-id
     *
     * @param bool $need_callback 如果需要知道视频分享成功的结果，need_callback设置为true
     * @param string $default_hashtag 追踪分享默认hashtag
     * @param string $source_style_id 多来源样式id（暂未开放）
     * @param string $link_param 分享来源url附加参数（暂未开放）
     * @return mixed|null
     */
    public function getShareId($need_callback = true, $default_hashtag = null,
                               $source_style_id = null, $link_param = null)
    {
        $response = $this->http->get(self::URL, [
            'query' => array_filter([
                'access_token' => $this->access_token,
                'need_callback' => $need_callback,
                'source_style_id' => $source_style_id,
                'default_hashtag' => $default_hashtag,
                'link_param' => $link_param
            ], function ($video) {
                return '' != $video && !is_null($video);
            })
        ]);

        return $this->getResponse($response);
    }
}