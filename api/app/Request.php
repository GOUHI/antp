<?php
namespace app;

// 应用请求对象类
class Request extends \think\Request
{
  protected $proxyServerIp = ['127.0.0.1'];
}
