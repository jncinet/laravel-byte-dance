<?php

namespace Jncinet\LaravelByteDance\Gateways\DouYin\Image;

use Jncinet\LaravelByteDance\Kernel\BaseClient;

/**
 * Class Create
 * @package Jncinet\LaravelByteDance\Gateways\DouYin\Image
 */
class Create extends BaseClient
{
    const URL = [
        'upload' => 'https://open.douyin.com/image/upload/',
        'create' => 'https://open.douyin.com/image/create/',
    ];

    protected $open_id;
    protected $access_token;
    protected $filename;        // 上传文件，可以是网络地址或本地文件路径
    protected $mime;
    protected $size;

    public function __construct($open_id, $access_token, $filename)
    {
        parent::__construct();
        $this->open_id = $open_id;
        $this->access_token = $access_token;
        // 文件地址
        $this->filename = $this->formatFilename($filename);
    }

    /**
     * @param array $fields
     * @return mixed|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    public function publish(array $fields = [])
    {
        $upload_response = $this->uploading();

        if (!$this->isSuccess($upload_response)) {
            throw new \Exception('文件上传失败', 1);
        }

        $fields['image_id'] = $upload_response['data']['image']['image_id'];

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
     * 上传图片
     *
     * @return mixed|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    public function uploading()
    {
        if ($this->isLocal($this->filename)) {
            $file = fopen($this->filename, 'rb');
            if ($file === false) {
                throw new \Exception('文件无法打开', 1);
            }
            // 文件类型
            $f_info = finfo_open(FILEINFO_MIME);
            $this->mime = finfo_file($f_info, $this->filename);
            finfo_close($f_info);
            if (empty($this->mime) || substr($this->mime, 0, 5) != 'image') {
                throw new \Exception('文件必须为图片格式', 1);
            }
            // 文件大小
            $stat = fstat($file);
            $this->size = $stat['size'];
            if (empty($this->size)) {
                throw new \Exception('文件大小不正确', 1);
            }
            $file_stream = fread($file, $this->size);
            fclose($file);
            if ($file_stream === false) {
                throw new \Exception('文件读取失败', 1);
            }
            // 上传到服务器
            return $this->upload($file_stream);
        } else {
            // 读取数据
            $response = $this->http->request('GET', $this->filename, ['stream' => true]);
            if ($response->getReasonPhrase() != 'OK') {
                throw new \Exception('远程文件访问失败', 1);
            }
            // 文件类型
            $this->mime = $response->getHeader('Content-Type');
            if (isset($this->mime[0]) && substr($this->mime[0], 0, 5) == 'image') {
                $this->mime = $this->mime[0];
            } else {
                throw new \Exception('文件必须为图片格式', 1);
            }
            // 文件大小
            $this->size = $response->getHeader('Content-Length');
            if (isset($this->size[0]) && $this->size[0] > 0) {
                $this->size = $this->size[0];
            } else {
                throw new \Exception('文件大小不正确', 1);
            }
            $file_stream = $response->getBody();
            // 上传到服务器
            return $this->upload($file_stream->read($this->size));
        }
    }

    /**
     * 上传
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
                        'name' => 'image',
                        'contents' => $fileStream,
                        'headers' => ['Content-Type' => $this->mime],
                        'filename' => basename($this->filename)
                    ]
                ]
            ]
        );

        return $this->getResponse($response);
    }
}