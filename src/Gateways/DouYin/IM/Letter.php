<?php

namespace Jncinet\LaravelByteDance\Gateways\DouYin\IM;

use Jncinet\LaravelByteDance\Kernel\BaseClient;

class Letter extends BaseClient
{
    const URL = [
        'send' => 'https://open.douyin.com/enterprise/im/message/send/',
    ];
}