<?php

namespace Jncinet\LaravelByteDance\Gateways\DouYin\Video;

use Jncinet\LaravelByteDance\Exceptions\UploadException;
use Jncinet\LaravelByteDance\Kernel\BaseClient;

/**
 * Class Create
 * @package Jncinet\LaravelByteDance\Gateways\DouYin\Video
 */
class Create extends BaseClient
{
    const URL = [
        'upload' => 'https://open.douyin.com/video/upload/',                // 整体上传
        'part_init' => 'https://open.douyin.com/video/part/init/',          // 分片初始化
        'part_upload' => 'https://open.douyin.com/video/part/upload/',      // 分片上传
        'part_complete' => 'https://open.douyin.com/video/part/complete/',  // 分片完成
        'create' => 'https://open.douyin.com/video/create/',                // 发布
    ];

    protected $open_id;
    protected $access_token;
    protected $filename;        // 上传文件，可以是网络地址或本地文件路径
    protected $mime;
    protected $size;            // 当前文件大小

    /**
     * Video constructor.
     * @param string $open_id
     * @param string $access_token
     * @param string $filename
     */
    public function __construct($open_id, $access_token, $filename)
    {
        parent::__construct();

        $this->open_id = $open_id;
        $this->access_token = $access_token;
        // 文件地址
        $this->filename = $this->formatFilename($filename);
    }

    /**
     * 创建视频
     *
     * @param array $fields
     * @return mixed|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws UploadException
     */
    public function publish(array $fields = [])
    {
        $upload_response = $this->uploading();

        if (!$this->isSuccess($upload_response)) {
            throw new UploadException('upload_fail');
        }

        $fields['video_id'] = $upload_response['data']['video']['video_id'];

        $response = $this->http->request('POST', self::URL['create'], [
            'query' => [
                'open_id' => $this->open_id,
                'access_token' => $this->access_token,
            ],
            'json' => array_filter($fields, function ($value) {
                return '' !== $value && !is_null($value);
            })
        ]);

        return $this->getResponse($response);
    }

    /**
     * 上传视频
     *
     * @return mixed|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws UploadException
     */
    public function uploading()
    {
        // 判断是否本地文件，不同方式读取视频流
        if ($this->isLocal($this->filename)) {
            $file = fopen($this->filename, 'rb');
            if ($file === false) {
                throw new UploadException('open', ['filename' => $this->filename]);
            }
            // 文件类型
            $f_info = finfo_open(FILEINFO_MIME);
            $this->mime = finfo_file($f_info, $this->filename);
            finfo_close($f_info);
            if (empty($this->mime) || substr($this->mime, 0, 5) != 'video') {
                throw new UploadException('not_video');
            }
            // 文件大小
            $stat = fstat($file);
            $this->size = $stat['size'];
            // 判断是否需要分片上传
            if ($this->size <= $this->block_size) {
                $file_stream = fread($file, $this->size);
                fclose($file);
                if ($file_stream === false) {
                    throw new UploadException('read', ['filename' => $this->filename]);
                }
                // 上传到服务器
                return $this->upload($file_stream);
            } else {
                // 创建分片
                $init = $this->makeBlock();
                if (!$this->isSuccess($init)) {
                    throw new UploadException('block_create');
                }
                // 上传分片
                $uploaded = 0;
                $block_id = 0;
                while ($uploaded < $this->size) {
                    $block_id++;
                    $block_size = $this->blockSize($uploaded);
                    $file_stream = fread($file, $block_size);
                    if ($file_stream === false) {
                        throw new UploadException('read', ['filename' => $this->filename]);
                    }
                    if ($this->isSuccess(
                        $this->uploadBlock($file_stream, $init['data']['upload_id'], $block_id)
                    )) {
                        throw new UploadException('block_upload', ['block_id' => $block_id]);
                    }
                    $uploaded += $block_size;
                }
                fclose($file);
                // 分片完成
                $complete_response = $this->completeBlock($init['data']['upload_id']);
                if (!$this->isSuccess($complete_response)) {
                    throw new UploadException('block_complete');
                }
                return $complete_response;
            }
        } else {
            // 读取数据
            $response = $this->http->request('GET', $this->filename, ['stream' => true]);
            if ($response->getReasonPhrase() != 'OK') {
                throw new UploadException('remote_open', ['filename' => $this->filename]);
            }
            // 文件类型
            $this->mime = $response->getHeader('Content-Type');
            if (isset($this->mime[0]) && substr($this->mime[0], 0, 5) == 'video') {
                $this->mime = $this->mime[0];
            } else {
                throw new UploadException('not_video');
            }
            // 文件大小
            $this->size = $response->getHeader('Content-Length');
            if (isset($this->size[0]) && $this->size[0] > 0) {
                $this->size = $this->size[0];
            } else {
                throw new UploadException('file_empty');
            }
            $file_stream = $response->getBody();
            if ($this->size <= $this->block_size) {
                // 上传到服务器
                return $this->upload($file_stream->read($this->size));
            } else {
                // 创建分片
                $init = $this->makeBlock();
                if (!$this->isSuccess($init)) {
                    throw new UploadException('block_create');
                }
                // 上传分片
                $uploaded = 0;
                $block_id = 0;
                while ($uploaded < $this->size) {
                    $block_id++;
                    $block_size = $this->blockSize($uploaded);
                    $file_stream = $file_stream->read($block_size);
                    if ($file_stream === false) {
                        throw new UploadException('read', ['filename' => $this->filename]);
                    }
                    if ($this->isSuccess(
                        $this->uploadBlock($file_stream, $init['data']['upload_id'], $block_id)
                    )) {
                        throw new UploadException('block_upload', ['block_id' => $block_id]);
                    }
                    $uploaded += $block_size;
                }
                $file_stream->close();
                // 分片完成
                $complete_response = $this->completeBlock($init['data']['upload_id']);
                if (!$this->isSuccess($complete_response)) {
                    throw new UploadException('block_complete');
                }
                return $complete_response;
            }
        }
    }

    /**
     * 整体上传
     *
     * @param $fileStream
     * @return mixed|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function upload($fileStream)
    {
        $response = $this->http->request(
            'POST',
            self::URL['upload'],
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
                        'name' => 'video',
                        'contents' => $fileStream,
                        'headers' => ['Content-Type' => $this->mime],
                        'filename' => basename($this->filename)
                    ]
                ]
            ]
        );

        return $this->getResponse($response);
    }

    /**
     * 创建分片
     *
     * @return mixed|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function makeBlock()
    {
        $response = $this->http->request('POST', self::URL['part_init'], [
            'query' => [
                'open_id' => $this->open_id,
                'access_token' => $this->access_token,
            ]
        ]);

        return $this->getResponse($response);
    }

    /**
     * 上传分片
     *
     * @param $fileStream
     * @param $upload_id
     * @param $block_id
     * @return mixed|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function uploadBlock($fileStream, $upload_id, $block_id)
    {
        $response = $this->http->request(
            'POST',
            self::URL['part_upload'],
            [
                'query' => [
                    'open_id' => $this->open_id,
                    'access_token' => $this->access_token,
                    'upload_id' => $upload_id,
                    'part_number' => $block_id,
                ],
                'headers' => [
                    'Content-Type' => 'multipart/form-data'
                ],
                'multipart' => [
                    [
                        'name' => 'video',
                        'contents' => $fileStream,
                        'headers' => ['Content-Type' => $this->mime],
                        'filename' => basename($this->filename)
                    ]
                ]
            ]
        );

        return $this->getResponse($response);
    }

    /**
     * 分片上传完成
     *
     * @param $upload_id
     * @return mixed|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function completeBlock($upload_id)
    {
        $response = $this->http->request('POST', self::URL['part_complete'], [
            'query' => [
                'open_id' => $this->open_id,
                'access_token' => $this->access_token,
                'upload_id' => $upload_id
            ]
        ]);

        return $this->getResponse($response);
    }

    /**
     * 读取分片大小
     *
     * @param int $uploaded
     * @return \Illuminate\Config\Repository|int|mixed
     */
    private function blockSize(int $uploaded)
    {
        if ($this->size < $uploaded + $this->block_size) {
            return $this->size - $uploaded;
        }
        return $this->block_size;
    }
}