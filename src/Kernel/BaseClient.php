<?php

namespace Jncinet\LaravelByteDance\Kernel;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Storage;
use Jncinet\LaravelByteDance\Exceptions\UploadException;
use Psr\Http\Message\ResponseInterface;

/**
 * Class BaseClient
 * @package Jncinet\LaravelByteDance\Kernel
 */
class BaseClient
{
    protected $http, $block_size, $account_type, $client_key, $client_secret, $tempFile = null;

    /**
     * Base constructor.
     */
    public function __construct()
    {
        $this->http = new Client([
            'timeout' => config('byte-dance.timeout', 2.0),
            'verify' => false,
        ]);
        $this->client_key = config('byte-dance.client_key');
        $this->client_secret = config('byte-dance.client_secret');
        // 分片大小
        $this->block_size = config('byte-dance.block_size', 20971520);
        // 账户类型
        $this->account_type = config('byte-dance.account_type', 'individual');
    }

    /**
     * @param ResponseInterface $response
     * @return mixed|null
     */
    protected function getResponse(ResponseInterface $response)
    {
        if ($response->getReasonPhrase() == 'OK') {
            return json_decode((string)$response->getBody(), true);
        }

        return null;
    }

    /**
     * 格式化文件名
     *
     * @param $filename
     * @return null|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws UploadException
     */
    protected function formatFilename($filename)
    {
        if ($this->isLocal($filename)) {
            $storageDiskName = config('filesystems.default');
            if (config('filesystems.disks.' . $storageDiskName . '.driver') == 'local') {
                // 本地存储时转换为文件完整路径
                return config('filesystems.disks.' . $storageDiskName . '.root')
                    . DIRECTORY_SEPARATOR . $filename;
            }

            // 如果不是本地存储统一转换为网络地址
            $filename = Storage::url($filename);
        }

        $suffix = '.';
        $suffix .= pathinfo($filename, PATHINFO_EXTENSION);
        $newFilename = 'temp' . DIRECTORY_SEPARATOR . md5($filename) . $suffix;
        // 如果已经保存过，不再重复下载
        if (!Storage::disk('public')->exists($newFilename)) {
            // 远程文件，先验证文件小于4G（4294967296），保存到本地 storage_path('app/public/temp/filename.suffix')
            $response = $this->http->request('GET', $filename, ['stream' => true]);
            if ($response->getReasonPhrase() === 'OK') {
                // 创建本地文件，发布完成后会删除此文件
                if (Storage::disk('public')->put($newFilename, $response->getBody())) {
                    $this->tempFile = $newFilename;
                } else {
                    throw new UploadException('read', ['filename' => $filename]);
                }
            } else {
                throw new UploadException('remote_open', ['filename' => $filename]);
            }
        }

        return config('filesystems.disks.public.root') . DIRECTORY_SEPARATOR . $newFilename;
    }

    /**
     * 删除临时文件
     *
     * @return bool|null
     */
    protected function deleteTempFile()
    {
        if (!empty($this->tempFile)) {
            return Storage::disk('public')->delete($this->tempFile);
        }
        return null;
    }

    /**
     * 判断请求是否成功
     *
     * @param $response
     * @return bool
     */
    protected function isSuccess($response)
    {
        if (isset($response['data']['error_code']) && $response['data']['error_code'] == 0) {
            return true;
        }
        return false;
    }

    /**
     * 是否本地文件
     *
     * @param $filename
     * @return bool
     */
    protected function isLocal($filename)
    {
        return empty(parse_url($filename, PHP_URL_SCHEME));
    }

    /**
     * 上传文件
     *
     * @param $url
     * @param $name
     * @param $fileName
     * @param $fileBody
     * @param null $mimeType
     * @param array $options
     * @param array $fields
     * @param array $headers
     * @return ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function multipartPost($url, $name, $fileName, $fileBody,
                                  $mimeType = null, $options = [], $fields = [], $headers = [])
    {
        $data = [];
        $mimeBoundary = md5(microtime());

        foreach ($fields as $key => $val) {
            array_push($data, '--' . $mimeBoundary);
            array_push($data, "Content-Disposition: form-data; name=\"$key\"");
            array_push($data, '');
            array_push($data, $val);
        }

        array_push($data, '--' . $mimeBoundary);
        $finalMimeType = empty($mimeType) ? 'application/octet-stream' : $mimeType;
        array_push($data, "Content-Disposition: form-data; name=\"$name\"; filename=\"$fileName\"");
        array_push($data, "Content-Type: $finalMimeType");
        array_push($data, '');
        array_push($data, $fileBody);

        array_push($data, '--' . $mimeBoundary . '--');
        array_push($data, '');

        $body = implode("\r\n", $data);

        $contentType = 'multipart/form-data; boundary=' . $mimeBoundary;
        $headers['Content-Type'] = $contentType;
        $request = new Request('POST', $url, $headers, $body);
        return $this->http->send($request, $options);
    }
}