<?php
#公共参数
use think\facade\Db;

define('SUCCESS','请求成功');
define('FAILED','请求失败');
define('PARAMETER','参数错误');
define('TOKEN_FALURE','身份失效，请重新登录');
define('NOT_AUTH','权限不足,请联系管理员');
define('400','程序错误，请联系管理员');
define('SQL_ERROR','数据库错误');

/**
 * 公用返回函数
 * @param string $code         验证码
 * 200成功
 * 201登录失效
 * 202权限不足
 * 400业务错误
 * 404数据库错误
 * 405签名错误
 * 500其他错误
 * @param string $msg           信息
 * @param array $data           数据
 * @param array $extra          额外参数
 * @param string $browserCode   浏览器状态码
 */
function ret($code = 200, $msg = '', $data = [],$extra = [],$browserCode = 200)
{
    $ret['code'] = $code;
    $ret['msg'] = $msg;
    $ret['data'] = $data;
 
    //正常返回
    if($code == 200){
        $ret['msg'] = empty($msg) ? SUCCESS: $msg;
    }
    //登录失败
    if($code == 201){
        $ret['msg'] = empty($msg) ? TOKEN_FALURE: $msg;
    }
    //权限不足
    if($code == 202){
        $ret['msg'] = empty($msg) ? NOT_AUTH: $msg;
    }
    //业务错误
    if($code == 400){
        $ret['msg'] = empty($msg) ? 400: $msg;
    }
    //数据库错误
    if($code == 500){
        $ret['msg'] = empty($msg) ? SQL_ERROR: $msg;
    }
 
    //循环获取额外参数
    foreach ($extra as $key=>$value){
        $ret[$key] = $value;
    }
 
    //返回输出数据
    return json($ret)->code($browserCode);
}

/**
 * 获取随机数作为验证码
 * by gouhi
 * @param     $digit        验证码位数
 * @param int $type         验证码类型 1：默认数字 2：字符 3:数字+字符
 * @return string $authnum  返回验证码
 */
function randomStr($digit = 6, $type = 1)
{
    if ($type == 1) {
        $ychar = "0,1,2,3,4,5,6,7,8,9";
        $x = 9;
    } elseif ($type == 2) {
        $ychar = "A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z";
        $x = 25;
    }else{
        $ychar = "0,1,2,3,4,5,6,7,8,9,A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z";
        $x = 35;
    }
 
    $authnum = "";
    $list = explode(",", $ychar);
    for ($i = 0; $i < $digit; $i++) {
        $randnum = rand(0, $x); // 10+26;
        $authnum .= $list[$randnum];
    }
    return $authnum;
}

/**
 * 获取IP地区(淘宝api)
 * by gh
 * @param string $ip
 * @return string   地区-城市
 */
function get_ip_area($ip)
{
    try {
        $ipInfo = json_decode(file_get_contents('http://ip.taobao.com/service/getIpInfo.php?ip=' . $ip), 1);
        if ($ipInfo) {
            if ($ipInfo['data']['region'] == 'XX') {
                return $ipInfo['data']['city'];
            } else {
                return $ipInfo['data']['region'] . '-' . $ipInfo['data']['city'];
            }
        } else {
            return '未知';
        }
    } catch (\Exception $e) {
        return '未知';
    }
}

/**
 * 日志记录写入
*/
function writLogInfo($content){
    try {
        $data['content'] = $content;
        $data['create_at'] = time();
        Db::name('t_operation_log')->insert($data);
    } catch (\Throwable $th) {
        //throw $th;
    }
}

/**
 * 获取树形结构数据
 * @param $list
 * @param int $id
 * @return array
 */
function getChild($list,$id=0){
  //初始化数组
  $child = [];
  //循环数据源
  foreach ($list as $key=>$value){
      //判断父级ID是否等于传递过来的ID
      if($value['father'] == $id){
          //如果等于先保存下来
          $child[$key] = $value;
          //把自己排除在外
          unset($list[$key]);
          //继续去找子级
          $child[$key]['child'][] = getChild($list,$value['id']);
      }
  }
  //返回数据
  return array_values($child);
}

/**
 * GET提交
 * @param $url
 * return mixed
 */
function curlGet($url)
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 500);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_URL, $url);

    $res = curl_exec($curl);
    curl_close($curl);
    return $res;
}

/**
 * POST提交
 * @param $url
 * @param $data (注释： $header[] = 'Content-Type:application/json;charset=utf-8';则传递json后的字符串)
 * @param $header (头信息)
 * return mixed
 */
function curlPost($url, $data, $header = array())
{ // 模拟提交数据函数
    $curl = curl_init(); // 启动一个CURL会话
    curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
    curl_setopt($curl, CURLOPT_HTTPHEADER, $header); //设置头信息
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0); // 从证书中检查SSL加密算法是否存在
    curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
    curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
    curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
    //    curl_setopt($curl, CURLOPT_COOKIEFILE, $GLOBALS['cookie_file']); // 读取上面所储存的Cookie信息
    curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
    curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
    $tmpInfo = curl_exec($curl); // 执行操作
    if (curl_errno($curl)) {
        echo 'Errno' . curl_error($curl);
    }
    curl_close($curl); // 关键CURL会话
    return $tmpInfo; // 返回数据
}