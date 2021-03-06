<?php

namespace Jncinet\LaravelByteDance\Gateways\DouYin\Image;

use Jncinet\LaravelByteDance\Exceptions\UploadException;
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

    /**
     * Create constructor.
     * @param $open_id
     * @param $access_token
     * @param $filename
     * @throws UploadException
     * @throws \GuzzleHttp\Exception\GuzzleException
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
     * @param array $fields
     * @return mixed|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Exception
     */
    public function publish(array $fields = [])
    {
        $upload_response = $this->uploading();

        if ($this->isSuccess($upload_response)) {
            $this->deleteTempFile();
        } else {
            throw new UploadException('upload_fail');
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
     * @throws UploadException
     */
    public function uploading()
    {
        $file = fopen($this->filename, 'rb');
        if ($file === false) {
            throw new UploadException('open', ['filename' => $this->filename]);
        }
        // 文件类型
        $f_info = finfo_open(FILEINFO_MIME);
        $this->mime = finfo_file($f_info, $this->filename);
        finfo_close($f_info);
        if (empty($this->mime) || substr($this->mime, 0, 5) != 'image') {
            throw new UploadException('not_image');
        }
        // 文件大小
        $stat = fstat($file);
        $this->size = $stat['size'];
        if (empty($this->size)) {
            throw new UploadException('file_empty');
        }
        $file_stream = fread($file, $this->size);
        fclose($file);
        if ($file_stream === false) {
            throw new UploadException('read', ['filename' => $this->filename]);
        }
        // 上传到服务器
        return $this->upload($file_stream);
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
        $url = self::URL['upload'] . '?' . http_build_query([
                'open_id' => $this->open_id,
                'access_token' => $this->access_token
            ]);

        $response = $this->multipartPost($url, 'image', basename($this->filename),
            $fileStream, $this->mime);

        return $this->getResponse($response);
    }
}