# 客户端云服务中心

#### 安装

```
composer require yc-open/cloud-service
```

#### 使用说明
``` php
<?php
require 'vendor/autoload.php';
define('ROOT_PATH',dirname(__FILE__));
use YcOpen\CloudService\Request;
use YcOpen\CloudService\Exception\HttpException;
use YcOpen\CloudService\Exception\HttpResponseException;
use YcOpen\CloudService\Exception\ValidateException;
function p($data){
    echo '<pre>';
    print_r($data);
    echo '</pre>';
}
function pe(\Throwable $th){
    p([
        'code'=>$th->getCode(),
        'message'=>$th->getMessage(),
        'file'=>$th->getFile(),
        'line'=>$th->getLine(),
        'trace'=>$th->getTrace(),
    ]);
}
function base_path($path=''){
    return ROOT_PATH.'/'.$path;
}
try {
    // Request::login()->outLogin();
    // Request::Login();
    /* $data=Request::coupon()->getAvailableCoupon()->setQuery(['type'=>'apps'])->cloud()->send();
    p($data); */
    $data=Request::SystemUpdate()->verify()->setQuery(['version'=>1,'version_name'=>'1.0.0'])->cloud()->send();
    p($data);
    return;
} catch (ValidateException $e) {
    # 参数验证错误
    pe($e);
} catch (HttpException $e) {
    # 服务器错误
    pe($e);
} catch (HttpResponseException $e) {
    # 业务错误
    pe($e);
} catch (\Throwable $th) {
    # 其他错误
    pe($th);
}
```