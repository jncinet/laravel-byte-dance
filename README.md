## Laravel-byte-dance （部份接口）

```shell
$ composer require jncinet/Laravel-byte-dance
```

### 示例
```php
// 查询授权账号视频数据
app('byte-dance.dou-yin')->video()->search('open_id', 'access_token')->paginate();
```