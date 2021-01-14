<?php

namespace Jncinet\LaravelByteDance\Gateways\DouYin\IM;

use Jncinet\LaravelByteDance\Kernel\BaseClient;

/**
 * Class Media
 * @package Jncinet\LaravelByteDance\Gateways\DouYin\IM
 */
class Media extends BaseClient
{
    const URL = [
        'forever' => 'https://open.douyin.com/enterprise/media/upload/',
        'temp' => 'https://open.douyin.com/enterprise/media/temp/upload/',
        'list' => 'https://open.douyin.com/enterprise/media/list/',
        'delete' => 'https://open.douyin.com/enterprise/media/delete/',
    ];

    protected $open_id, $access_token;

    /**
     * Media constructor.
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
     * 上传素材
     *
     * @param $filename
     * @param bool $is_forever
     * @return mixed|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    public function upload($filename, $is_forever = false)
    {
        // 路径格式化
        $filename = $this->formatFilename($filename);
        // 上传永久素材或临时素格
        $uri = $is_forever ? self::URL['forever'] : self::URL['temp'];

        if ($this->isLocal($filename)) {
            $file = fopen($filename, 'rb');
            if ($file === false) {
                throw new \Exception('文件无法打开', 1);
            }
            // 文件类型
            $f_info = finfo_open(FILEINFO_MIME);
            $mime = finfo_file($f_info, $filename);
            finfo_close($f_info);
            if (empty($mime) ||
                (substr($mime, 0, 5) != 'image' && substr($mime, -4) != '/pdf')) {
                throw new \Exception('文件必须为图片格式', 1);
            }
            // 文件大小
            $stat = fstat($file);
            $size = $stat['size'];
            if (empty($size)) {
                throw new \Exception('文件大小不正确', 1);
            }
            $file_stream = fread($file, $size);
            fclose($file);
            if ($file_stream === false) {
                throw new \Exception('文件读取失败', 1);
            }
            // 上传到服务器
            return $this->uploading($uri, $file_stream, $filename, $mime);
        } else {
            // 读取数据
            $response = $this->http->request('GET', $filename, ['stream' => true]);
            if ($response->getReasonPhrase() != 'OK') {
                throw new \Exception('远程文件访问失败', 1);
            }
            // 文件类型
            $mime = $response->getHeader('Content-Type');
            if (isset($mime[0]) &&
                (substr($mime[0], 0, 5) == 'image' || substr($mime[0], -4) == '/pdf')) {
                $mime = $mime[0];
            } else {
                throw new \Exception('文件必须为图片或PDF格式', 1);
            }
            // 文件大小
            $size = $response->getHeader('Content-Length');
            if (isset($size[0]) && $size[0] > 0) {
                $size = $size[0];
            } else {
                throw new \Exception('文件大小不正确', 1);
            }
            $file_stream = $response->getBody();
            // 上传到服务器
            return $this->uploading($uri, $file_stream->read($size), $filename, $mime);
        }
    }

    /**
     * 素材列表
     *
     * @param int $page
     * @param int $limit
     * @return mixed|null
     */
    public function paginate(int $page = 0, int $limit = 10)
    {
        $response = $this->http->post(self::URL['list'], [
            'query' => [
                'open_id' => $this->open_id,
                'access_token' => $this->access_token,
                'cursor' => intval($page),
                'count' => intval($limit) ?: 10
            ]
        ]);

        return $this->getResponse($response);
    }

    /**
     * 删除素材
     *
     * @param $media_id
     * @return mixed|null
     */
    public function destroy($media_id)
    {
        $response = $this->http->post(self::URL['delete'], [
            'query' => [
                'open_id' => $this->open_id,
                'access_token' => $this->access_token
            ],
            'json' => [
                'media_id' => $media_id
            ]
        ]);

        return $this->getResponse($response);
    }

    /**
     * 上传
     *
     * @param $fileStream
     * @param $uri
     * @param $filename
     * @param $mime
     * @return mixed|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function uploading($uri, $fileStream, $filename, $mime)
    {
        $response = $this->http->request(
            'POST',
            $uri,
            [
                'query' => [
                    'open_id' => $this->open_id,
                    'access_token' => $this->access_token
                ],
                'headers' => [
                    'Content-Type' => 'multipart/form-data'
                ],
                'multipart' => [
                    [
                        'name' => 'media',
                        'contents' => $fileStream,
                        'headers' => ['Content-Type' => $mime],
                        'filename' => basename($filename)
                    ]
                ]
            ]
        );

        return $this->getResponse($response);
    }
}