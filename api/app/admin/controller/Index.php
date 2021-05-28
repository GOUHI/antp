<?php
namespace app\admin\controller;

use app\admin\model\Admin as ModelAdmin;
use app\admin\model\AdminGroup;
use lib\Token as LibToken;
use think\Request;

class Index{
  /**
   * 登录
   */
  public function login(){
    $account = input('post.account');
    $password = input('post.password');
    
    if(!$account || !$password){
      return ret(400,'参数错误');
    }
    
    // 获取信息，判断是否符合通过
    $info = ModelAdmin::getAdminInfoByAccount(0,$account);
    if(!$info){
      return ret(400,'管理员不存在或者密码错误');
    }
    if($info['delete_at'] != 0){
      return ret(400,'管理员不存在');
    }
    if($info['status'] ==  2){
      return ret(400,'管理员黑名单，请联系站点维护人员');
    }
    // if(!password_verify($password, $info['password'])){
    //   return ret(400,'管理员不存在或者密码错误');
    // }
    
    // 获取分组信息
    $groupInfo = AdminGroup::getGroupInfo($info['group_id']);
    if($groupInfo){
      $info['group'] = $groupInfo;
    }

    
    // 清除密码参数
    unset($info['password']);

    // 生成token
    $jwt = new LibToken();
    $token = $jwt->createToken(['id'=>2]);
    if($token['code'] != 200){
      return ret(400,'登录成功，token生成失败',json_encode($token));
    }

    // 插入token到info中，并且返回数据
    $info['token'] = $token['data'];
    return ret(200,'',$info);
  }
  public function test(Request $request){
    print_r($request->admin_info);
    return ret(200,'成功');
  }

  public function qianfa(){
    $jwt = new LibToken();
    $token = $jwt->createToken(['id'=>2]);
    if($token['code'] == 200){
      return ret(200,'生成token成功',$token['data']);
    }else{
      return ret(400,'token生成失败',json_encode($token));
    }
    // $key     = 'customer';
    // $jwtData = [
    //     'lat' => time(),
    //     'nbf' => time(),
    //     'exp' => time()+24* 3600,
    //     'uid' => 12,
    //     'mobile' => 1775506605, //可以加入自己想要获得的用户信息参数
    // ];
 
    // $jwtToken = JWT::encode($jwtData, $key);


    // return ret(200,'',$jwtToken);
  }

  public function yanzheng(){
    $token = $_SERVER['HTTP_AUTHORIZATION'];
    $jwt = new LibToken();
    $data = $jwt->checkToken($token);
    print_r($data);
    // if($data['code'] == 200){
    //   return ret(200,'解析成功',$token['data']);
    // }else{
    //   return ret(400,'解析token失败',json_decode($token));
    // }
  }
}