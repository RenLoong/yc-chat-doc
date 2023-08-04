<?php

namespace YcOpen\ChatDoc\Request;

use YcOpen\ChatDoc\Request;
use YcOpen\ChatDoc\Validator;
use YcOpen\ChatDoc\DataModel;

/**
 * 授权相关接口
 * Class AuthRequest
 * @package YcOpen\ChatDoc\Request
 */
class AuthRequest extends Request
{
    /**
     * 获取access_token
     * @param mixed $query
     * @return AuthRequest
     */
    public function access_token(mixed $query = null)
    {
        $this->offAutoAddToken=true;
        $this->setUrl('Auth/access_token');
        $validator = new Validator;
        $validator->rules([
            'api_key' => 'required',
            'api_secret' => 'required',
        ]);
        $this->validator = $validator;
        if ($query) {
            $this->setQuery($query);
        }
        return $this;
    }
    /**
     * 设置响应数据模型
     * @return DataModel
     */
    public function setResponse(mixed $data): DataModel
    {
        if($this->cache){
            (new $this->cache)->set(Request::CACHE_KEY,$data['access_token'],$data['expires_in']);
        }else if($this->runtime){
            if(!is_dir($this->runtime)){
                mkdir($this->runtime,0777,true);
            }
            file_put_contents($this->runtime.'/'.Request::CACHE_KEY,
            json_encode(['access_token'=>$data['access_token'],'expire'=>time()+$data['expires_in']],JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));
        }
        return new DataModel($data);
    }
}
