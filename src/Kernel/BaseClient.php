<?php

namespace Jncinet\LaravelByteDance\Kernel;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Storage;
use Psr\Http\Message\ResponseInterface;


class BaseClient
{
    protected $http, $block_size, $account_type, $client_key, $client_secret;

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
     * @return string
     */
    protected function formatFilename($filename)
    {
        if ($this->isLocal($filename)) {
            $storageDiskName = config('filesystems.default');
            if ($storageDiskName === 'public' || $storageDiskName === 'local') {
                // 本地存储时转换为文件完整路径
                return storage_path(
                    config('filesystems.disks.' . $storageDiskName . '.root')
                    . DIRECTORY_SEPARATOR . $filename);
            } else {
                // 如果不是本地存储统一转换为网络地址
                return Storage::url($filename);
            }
        }
        return $filename;
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
}