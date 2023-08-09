<?php

namespace YcOpen\ChatDoc;

use GuzzleHttp\Client;
use LDAP\Result;
use Psr\Http\Message\ResponseInterface;

class Request
{
    const API_VERSION_V2 = 'v2/';
    const CACHE_KEY = 'YC_CHAT_DOC_ACCESS_TOKEN';
    protected $baseUrl = 'https://chatdoc.kaifa.cc/app/ycChatdoc/RestfullApi/';
    protected $version = '';
    protected $url;
    protected $params = [];
    protected $query = [];
    protected $method = 'GET';
    protected $runtime;
    protected $cache;
    protected $headers = [];
    protected $exception = true;
    protected $validator;
    protected $file;
    protected $isDownFile = false;
    protected $offAutoAddToken = false;
    protected $timeout = 60;
    public function __construct(mixed $options = null)
    {
        $baseUrl = chat_doc_env('YC_CHAT_DOC_BASE_URL');
        if ($baseUrl) {
            $this->baseUrl = $baseUrl;
        }
        if ($options) {
            $this->setOptions((array)$options);
        }
    }
    /**
     * 发送请求
     * @return ResponseInterface|null
     */
    public function Builder()
    {
        $this->check();
        if ($this->isDownFile) {
            return;
        }
        $this->autoAddToken();
        $option = [
            'base_uri' => $this->baseUrl,
            'headers' => $this->headers,
            'timeout' => $this->timeout,
        ];
        # 判断url是否为http(s)开头
        if (preg_match('/^http(s)?:\/\//', $this->url)) {
            $option['base_uri'] = '';
        }
        $client = new Client($option);
        $options = [];
        if ($this->method == 'GET') {
            $options['query'] = $this->query;
        } else {
            $options['form_params'] = $this->params;
        }
        return $client->request($this->method, $this->url, $options);
    }
    /**
     * 设置BaseUrl
     * @param string $baseUrl
     * @return Request
     */
    public function setBaseUrl(string $baseUrl)
    {
        $this->baseUrl = $baseUrl;
        return $this;
    }
    /**
     * 设置请求地址
     * @param string $url
     * @return Request
     */
    public function setUrl(string $url)
    {
        $this->url = $url;
        return $this;
    }
    /**
     * 设置接口版本
     * @param string $version
     * @return Request
     */
    public function setVersion(string $version = null)
    {
        if (!$version) {
            return $this;
        }
        $this->version = $version;
        $this->baseUrl = $this->baseUrl . $this->version;
        return $this;
    }
    /**
     * 设置请求参数
     * @param array $options
     * @return Request
     */
    public function setOptions(array $options)
    {
        foreach ($options as $key => $value) {
            $this->$key = $value;
        }
        return $this;
    }
    /**
     * 设置接口版本为v2
     * @return Request
     */
    public function v2()
    {
        $this->setVersion(self::API_VERSION_V2);
        return $this;
    }
    /**
     * 设置timeout
     * @param int $timeout
     * @return Request
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
        return $this;
    }
    /**
     * 设置请求方法
     * @param string $method
     * @return Request
     */
    public function setMethod(string $method)
    {
        $this->method = $method;
        return $this;
    }
    /**
     * 设置请求头
     * @param string $key
     * @param string $value
     * @return Request
     */
    public function setHeader(string $key, string $value)
    {
        $this->headers[$key] = $value;
        return $this;
    }
    /**
     * 设置token
     * @param string $token
     * @return Request
     */
    public function setToken(string $token)
    {
        $this->setHeader('Authorization', $token);
        return $this;
    }
    private function autoAddToken()
    {
        if ($this->offAutoAddToken) {
            return;
        }
        if (empty($this->headers['Authorization'])) {
            $access_token = '';
            if ($this->cache) {
                $access_token = $this->cache->get(Request::CACHE_KEY);
            } else if ($this->runtime) {
                $_content = file_get_contents($this->runtime . '/' . Request::CACHE_KEY);
                try {
                    $_content = json_decode($_content, true);
                    if ($_content['expire'] > time()) {
                        $access_token = $_content['access_token'];
                    }
                } catch (\Throwable $e) {
                    $access_token = '';
                }
            }
            if ($access_token) {
                $this->setToken($access_token);
            }
        }
    }
    /**
     * 设置请求头
     * @param array $headers
     * @return Request
     */
    public function setHeaders(array $headers)
    {
        $this->headers = array_merge($this->headers, $headers);
        return $this;
    }
    /**
     * 设置POST参数
     * @param string|array|object $key
     * @param mixed $value
     * @return Request
     */
    public function setParams(mixed $key, mixed $value = null)
    {
        if (is_string($key)) {
            $this->params[$key] = $value;
        } elseif (is_array($key)) {
            $this->params = array_merge($this->params, $key);
        } elseif (is_object($key)) {
            $this->params = array_merge($this->params, (array) $key);
        }
        return $this;
    }
    /**
     * 设置GET参数
     * @param string|array|object $key
     * @param mixed $value
     * @return Request
     */
    public function setQuery(mixed $key, mixed $value = null)
    {
        if (is_string($key)) {
            $this->query[$key] = $value;
        } elseif (is_array($key)) {
            $this->query = array_merge($this->query, $key);
        } elseif (is_object($key)) {
            $this->query = array_merge($this->query, (array) $key);
        }
        return $this;
    }
    /**
     * 获取参数
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        if ($this->method == 'POST') {
            return $this->params[$name] ?? null;
        } else {
            return $this->query[$name] ?? null;
        }
    }
    /**
     * 设置参数
     * @param string $name
     * @param mixed $value
     * @example $request->username='admin';
     * @example $request->password='admin';
     * @return void
     */
    public function __set(string $name, mixed $value): void
    {
        if ($this->method == 'POST') {
            $this->setParams($name, $value);
        } else {
            $this->setQuery($name, $value);
        }
    }
    /**
     * 判断参数是否存在
     * @param string $name
     * @return bool
     */
    public function __isset($name)
    {
        if ($this->method == 'POST') {
            return isset($this->params[$name]);
        } else {
            return isset($this->query[$name]);
        }
    }
    /**
     * 获取所有参数
     * @return array
     */
    public function toArray(): array
    {
        if ($this->method == 'POST') {
            return $this->params;
        } else {
            return $this->query;
        }
    }
    /**
     * 关闭自动异常
     * @return Request
     */
    public function offAutoException()
    {
        $this->exception = false;
        return $this;
    }
    /**
     * 判断是否开启自动异常
     * @return bool
     */
    public function isException()
    {
        return $this->exception;
    }
    /**
     * 设置文件保存路径
     * @param string $file
     * @return Request
     */
    public function setSaveFile(string $file)
    {
        $this->file = $file;
        $this->isDownFile = true;
        return $this;
    }
    /**
     * 判断是否为下载文件
     * @return bool
     */
    public function isDownFile()
    {
        return $this->isDownFile;
    }
    public function download()
    {
        if (!$this->isDownFile) {
            throw new Exception\HttpException('非下载文件请求');
        }
        if (!$this->file) {
            throw new Exception\HttpException('未设置文件保存路径');
        }
        $client = new Client([
            'timeout' => 0,
            'headers' => [
                'Accept-Encoding' => 'gzip',
            ],
            'decode_content' => false
        ]);
        $response = $client->get($this->url);

        $zip_file = $this->file;
        # 判断文件夹是否存在
        $dir = dirname($zip_file);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        if ($response->getStatusCode() === 200) {
            # 保存文件
            $file = fopen($zip_file, 'w');
            // 获取响应主体
            $body = $response->getBody();

            // 将响应主体写入文件流
            while (!$body->eof()) {
                fwrite($file, $body->read(1024));
            }

            // 关闭文件
            fclose($file);
        } else {
            throw new Exception\HttpException('文件下载失败', $response->getStatusCode());
        }
        return file_exists($zip_file);
    }
    /**
     * 验证器
     * @return void
     */
    public function check(): void
    {
        if ($this->validator) {
            $this->validator->setData($this);
            $this->validator->check();
        }
    }
    /**
     * 设置响应数据模型
     * @param mixed $data
     * @return DataModel
     */
    public function setResponse(mixed $data): DataModel
    {
        return new DataModel($data);
    }
    /**
     * 使用内置云服务类
     * @return Cloud
     */
    public function cloud()
    {
        return new Cloud($this);
    }
    /**
     * 静态链式调用
     * 用于调用请求类，第一个方法为必须为类名，不含Request
     * @param [type] $name
     * @param [type] $arguments
     * @return Request
     */
    public static function __callStatic($name, $arguments)
    {
        $name = ucfirst($name);
        $class = __NAMESPACE__ . '\\Request\\' . $name . 'Request';
        if (!class_exists($class)) {
            throw new \Exception($name . '：请求类不存在');
        }
        return new $class(...$arguments);
    }
    /**
     * 发送请求并返回响应结果
     * @return DataModel|string|bool
     */
    public function response()
    {
        return $this->cloud()->send();
    }
}
