<?php
  session_start();
  header('Content-Type: text/html;charset=utf-8');
  header('Access-Control-Allow-Origin:*'); // *代表允许任何网址请求
  header('Access-Control-Allow-Methods:POST,GET,OPTIONS,DELETE'); // 允许请求的类型
  header('Access-Control-Allow-Credentials: true'); // 设置是否允许发送 cookies
  header('Access-Control-Allow-Headers: Content-Type,Content-Length,Accept-Encoding,X-Requested-with, Origin'); // 设置允许自定义请求头的字段
  // 解决 axios跨域请求发送两次问题
  // var_dump();
  if(strtoupper($_SERVER['REQUEST_METHOD']) == "OPTIONS"){ 
    exit(); 
  } 
?>