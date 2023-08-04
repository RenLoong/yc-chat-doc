<?php
if(!function_exists('chat_doc_env')){
    function chat_doc_env($key,$default=null){
        $envf='env';
        if(function_exists($envf)){
            return $envf($key,$default);
        }
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/../');
        $dotenv->load();
        return $_ENV[$key]??$default;
    }
}