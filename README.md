#### Composer安装

```bash
composer require yc-open/chat-doc
```

#### 示例
    
```php
<?php
require_once __DIR__ . '/vendor/autoload.php';
try {
    $options=[
        # 缓存驱动类，实例化后的对象，需要实现get、set、delete方法
        # cache->get($key);
        # cache->set($key, $data, $expire = 0);
        # cache->delete($key);
        # 'cache'=>new Cache,
        # 有写入权限的目录，用于存储access_token，优先使用cache，当二者都为空时手动处理
        # 'runtime'=>'/runtime',
        # 项目KEY
        'yc-project-key'=>''
    ];
    $data = Request::Auth($options)->access_token([
        'api_key'=>'',
        'api_secret'=>'',
    ])->response();
    print_r($data->access_token);
} catch (ValidateException $e) {
    # 参数验证错误
} catch (HttpException $e) {
    # 服务器错误
} catch (HttpResponseException $e) {
    # 业务错误
} catch (\Throwable $th) {
    # 其他错误
}
```