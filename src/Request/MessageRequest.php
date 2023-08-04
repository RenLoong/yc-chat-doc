<?php

namespace YcOpen\ChatDoc\Request;

use YcOpen\ChatDoc\Request;
use YcOpen\ChatDoc\Validator;
use YcOpen\ChatDoc\DataModel;

/**
 * 问答相关接口
 * Class DocumentRequest
 * @package YcOpen\ChatDoc\Request
 */
class MessageRequest extends Request
{
    public function chat(mixed $params=null)
    {
        $this->setMethod('POST');
        $this->setUrl('Message/chat');
        $validator = new Validator;
        $validator->rules([
            'anonymous_code' => 'required',
            'message' => 'required'
        ]);
        $this->validator = $validator;
        if ($params) {
            $this->setParams($params);
        }
        return $this;
    }
    /**
     * 获取消息历史
     * @param mixed $query
     * @return DocumentRequest
     */
    public function history(mixed $query = null)
    {
        $this->setUrl('Message/history');
        $validator = new Validator;
        $validator->rules([
            'anonymous_code' => 'required'
        ]);
        $this->validator = $validator;
        if ($query) {
            $this->setQuery($query);
        }
        return $this;
    }
}
