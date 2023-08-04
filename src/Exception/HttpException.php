<?php
namespace YcOpen\ChatDoc\Exception;
use Exception;
use YcOpen\ChatDoc\Response\ResponseCode;

class HttpException extends Exception
{
    protected $response;
    public function __construct($response,$code=ResponseCode::FAIL)
    {
        $this->response=$response;
        parent::__construct('服务器错误', $code);
    }
    public function getData()
    {
        return $this->response;
    }
}