<?php

namespace YcOpen\ChatDoc\Request;

use YcOpen\ChatDoc\Request;
use YcOpen\ChatDoc\Validator;
use YcOpen\ChatDoc\DataModel;

/**
 * 文档相关接口
 * Class DocumentRequest
 * @package YcOpen\ChatDoc\Request
 */
class DocumentRequest extends Request
{
    /**
     * 获取文档列表
     * @param mixed $query
     * @return DocumentRequest
     */
    public function list(mixed $query = null)
    {
        $this->setUrl('Document/list');
        if ($query) {
            $this->setQuery($query);
        }
        return $this;
    }
}
