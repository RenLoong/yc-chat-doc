<?php

namespace YcOpen\ChatDoc {

    class Request
    {
        /**
         * 授权相关接口
         * @access public
         * @return \YcOpen\ChatDoc\Request\AuthRequest
         */
        public static function Auth(mixed $options = null)
        {
            /** @var \YcOpen\ChatDoc\Request\AuthRequest $instance */
            return $instance;
        }
        /**
         * 文档相关接口
         * @access public
         * @return \YcOpen\ChatDoc\Request\DocumentRequest
         */
        public static function Document(mixed $options = null)
        {
            /** @var \YcOpen\ChatDoc\Request\DocumentRequest $instance */
            return $instance;
        }
        /**
         * 问答相关接口
         * @access public
         * @return \YcOpen\ChatDoc\Request\MessageRequest
         */
        public static function Message(mixed $options = null)
        {
            /** @var \YcOpen\ChatDoc\Request\MessageRequest $instance */
            return $instance;
        }
    }
}
