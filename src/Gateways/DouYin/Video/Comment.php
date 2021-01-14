<?php

namespace Jncinet\LaravelByteDance\Gateways\DouYin\Video;

use Jncinet\LaravelByteDance\Kernel\BaseClient;

/**
 * Class Comment
 * @package Jncinet\LaravelByteDance\Gateways\DouYin\Video
 */
class Comment extends BaseClient
{
    const URL = [
        'item' => [
            'comments' => 'https://open.douyin.com/item/comment/list/',
            'replies' => 'https://open.douyin.com/item/comment/reply/list/',
            'publish' => 'https://open.douyin.com/item/comment/reply/',
        ],
        'video' => [
            'comments' => 'https://open.douyin.com/video/comment/list/',
            'replies' => 'https://open.douyin.com/video/comment/reply/list/',
            'publish' => 'https://open.douyin.com/video/comment/reply/',
            'top' => 'https://open.douyin.com/video/comment/top/',
        ]
    ];

    protected $open_id, $access_token, $type;

    /**
     * Comment constructor.
     * @param $open_id
     * @param $access_token
     */
    public function __construct($open_id, $access_token)
    {
        parent::__construct();
        $this->open_id = $open_id;
        $this->access_token = $access_token;
        // 账号类型不同，请求路径不同
        $this->type = $this->account_type == 'enterprise' ? 'video' : 'item';
    }

    /**
     * 视频评论列表
     *
     * @param $item_id
     * @param int $page
     * @param int $limit
     * @return mixed|null
     */
    public function comments($item_id, $page = 0, $limit = 10)
    {
        $response = $this->http->get(self::URL[$this->type]['comments'], [
            'query' => [
                'open_id' => $this->open_id,
                'access_token' => $this->access_token,
                'cursor' => $page,
                'count' => $limit,
                'item_id' => urlencode($item_id)
            ]
        ]);

        return $this->getResponse($response);
    }

    /**
     * 评论回复列表
     *
     * @param $item_id
     * @param $comment_id
     * @param int $page
     * @param int $limit
     * @return mixed|null
     */
    public function replies($item_id, $comment_id, $page = 0, $limit = 10)
    {
        $response = $this->http->get(self::URL[$this->type]['replies'], [
            'query' => [
                'open_id' => $this->open_id,
                'access_token' => $this->access_token,
                'cursor' => $page,
                'count' => $limit,
                'item_id' => urlencode($item_id),
                'comment_id' => urlencode($comment_id)
            ]
        ]);

        return $this->getResponse($response);
    }

    /**
     * 发布评论或回复评论
     *
     * @param string $content
     * @param string $item_id 视频ID
     * @param null|string $comment_id 评论ID，评论时无需填写
     * @return mixed|null
     */
    public function publish($content, $item_id, $comment_id = null)
    {
        $response = $this->http->post(self::URL[$this->type]['publish'], [
            'query' => [
                'open_id' => $this->open_id,
                'access_token' => $this->access_token,
            ],
            'json' => array_filter([
                'content' => $content,
                'item_id' => $item_id,
                'comment_id' => $comment_id
            ], function ($video) {
                return '' != $video && !is_null($video);
            })
        ]);

        return $this->getResponse($response);
    }

    /**
     * 置顶视频评论（企业号）
     *
     * @param string $item_id
     * @param null|string $comment_id
     * @param bool $is_top
     * @return mixed|null
     */
    public function top($item_id, $comment_id = null, $is_top = true)
    {
        $response = $this->http->post(self::URL['video']['video_comments'], [
            'query' => [
                'open_id' => $this->open_id,
                'access_token' => $this->access_token,
            ],
            'json' => array_filter([
                'top' => $is_top,
                'item_id' => $item_id,
                'comment_id' => $comment_id
            ], function ($video) {
                return '' != $video && !is_null($video);
            })
        ]);

        return $this->getResponse($response);
    }
}