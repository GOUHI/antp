<?php

namespace app\agent\controller;

use app\agent\model\AccountModel;
use app\agent\model\DouyinModel;
use app\agent\validate\Account as validate;
use app\BaseController;
use app\Request;
use lib\Token;

class Account extends BaseController{
  /**
   * 登录
   */
  public function login(){
    $data = input();
    $validate = new validate();
    if (!$validate->scene('login')->check($data)) {
        return ret(400, $validate->getError());
    }
    $ip = request()->ip();
    $userInfo = AccountModel::getAgent_CustomInfoByName($data['name']);
    if (empty($userInfo)) {
      return ret(DATA_ERROR_CODE, Account_Exist_No);
    }
    if (!password_verify($data['password'], $userInfo['password'])) {
      return ret(DATA_ERROR_CODE, Login_Password_Error);
    }

    $isExistIp = AccountModel::returnExistIp($userInfo['id'],$ip);
    if (!$isExistIp) {
      return ret(IP_VERFITY_ERROR, Account_Ip_Pass);
    }else{
      unset($userInfo['password']);
      #生成一个token
      $jwt = new Token();
      $token = $jwt->createToken(['id' => $userInfo['id']]);
      $userInfo['token'] = $token['data'];
      return ret(SUCCESS_CODE, '请求成功',$userInfo);
    }
  }

  /**
   * 代理商功能
   * 添加商户
   */
  public function createBusiness(Request $request){
    $data = input();
    $validate = new validate();
    if (!$validate->scene('createBusiness')->check($data)) {
      return ret(400, $validate->getError());
    }

    $userInfo = $request->userInfo;
    if ($userInfo['account_type'] == 2 || $userInfo['account_type'] == 3) { //拥有代理商权限
      $accountData['account_type']=1;
      $accountData['create_at'] = time();
      $accountData['status'] = 1;
      $accountData['p_id'] = $userInfo['id'];
      $accountData['name']= $data['business_name'];
      $accountData['city'] = $data['city'];
      $accountData['password']=password_hash($data['password'], PASSWORD_DEFAULT);
      if (!empty(AccountModel::getAgent_CustomInfoByName($data['business_name']))) {
        # code...
        return ret(DATA_ERROR_CODE,'名称已存在');
      }
      if (($custom_id = AccountModel::createBusiness($accountData)) !== -1) {
        //写入ip白名单
        $ipData['ip']=$data['ip'];
        $ip = $data['ip'];
        if (!empty($ip)) {
          $ipList = [];
          foreach (explode('|', $ip) as $key => $value) {
            array_push($ipList,['ip'=>$value, 'create_at'=>time(), 'agent_custom_id'=> $custom_id]);
          }
          if (AccountModel::addIpList($ipList, $custom_id) !== false) {
            //添加日志
            writLogInfo('用户ID:'. $custom_id . ' 添加白名单 - '.$ip);
          }
        }

        //写入商家基础信息
        $baseInfo['agent_custom_id'] = $custom_id;
        $baseInfo['contact_name'] = $data['contact_name'];
        $baseInfo['contact_mobile'] = $data['contact_mobile'];
        $baseInfo['address'] = $data['address'];
        $baseInfo['custom_logo'] = $data['custom_logo'];
        $baseInfo['custom_dy_num'] = $data['custom_dy_num'];
        $baseInfo['custom_dy_home'] = $data['custom_dy_home'];
        $baseInfo['custom_open_id'] = $data['custom_open_id'];
        $baseInfo['contact_genger'] = $data['contact_genger'];
        $baseInfo['agent_id'] = $userInfo['id'];
        $baseInfo['custom_active_num'] = $data['custom_active_num'];
        $baseInfo['expire_at'] = $data['expire_at'];
        $baseInfo['scan_num'] = $data['scan_num'];
        if (AccountModel::insertBusinessBaseInfo($baseInfo) === true) {
          //写入商家基础信息
          writLogInfo($userInfo['name'] . ' 添加商户:' . $data['business_name'] . ' 联系人:' . $data['contact_name']);
        }
        return ret(SUCCESS_CODE, '创建成功');
      }else{
        return ret(DATA_ERROR_CODE, '创建失败，请稍后重试');
      }
    }else{
      return ret(DATA_ERROR_CODE, '您还不是代理商,不可添加商户！请联系客服');
    }
  }

  /**
   * 获取IP白名单
  */
  public static function getIpList(Request $request){
    $data = input();
    $userInfo = $request->userInfo;
    $id = $userInfo['id'];
    if (!empty($data['customer_id'])) {
      $id = $data['customer_id'];
    }
    return ret(SUCCESS_CODE,'成功',AccountModel::getIpList($id));
  }

  /**
   * 获取商户详情
  */
  public static function getCustomDetail(Request $request){
    $data = input();
    $validate = new validate();
    if (!$validate->scene('updateBusiness')->check($data)) {
      return ret(400, $validate->getError());
    }

    $userInfo = $request->userInfo;
    $customInfo = AccountModel::getAgentCustomInfoById($data['customer_id']);
    if (empty($customInfo)) {
      return ret(DATA_ERROR_CODE, '商户不存在');
    } else {
      if (!($customInfo['id'] === $userInfo['id'] || $customInfo['p_id'] === $userInfo['id'])) {
        return ret(DATA_ERROR_CODE, '您无权限查看此商户信息');
      }
    }
    return ret(SUCCESS_CODE, '成功', AccountModel::getBusinessDetail($data['customer_id']));
  }

  /**
   * 商户列表
  */
  public function businessList(Request $request){
    $data = input();
    $userInfo = $request->userInfo;
    if ($userInfo['account_type'] == 1) {
      return ret(DATA_ERROR_CODE, '您还不是代理商,无法读取商户列表');
    }
    return ret(SUCCESS_CODE,'成功',AccountModel::getBusinessList($userInfo['id'], !empty($data['keywords'])? $data['keywords']:'',!empty($data['limit'])? $data['limit']:'10'));
  }

    /**
     * 更新商户信息
    */
    public function updateBaseInfo(Request $request){
        $data = input();
        $validate = new validate();
        if (!$validate->scene('updateBusiness')->check($data)) {
          return ret(400, $validate->getError());
        }

        $userInfo = $request->userInfo;
        $customInfo = AccountModel::getAgentCustomInfoById($data['customer_id']);
        if (empty($customInfo)) {
            return ret(DATA_ERROR_CODE,'商户不存在');
        }else{
            if(!($customInfo['id'] === $userInfo['id'] || $customInfo['p_id'] === $userInfo['id'])){
                return ret(DATA_ERROR_CODE,'您无权限修改此商户信息');
            }
        }

        $updateData = [];
        if (!empty($data['business_name']) && $data['business_name'] !== $customInfo['name']) {
            if (!empty(AccountModel::getAgent_CustomInfoByName($data['business_name']))) {
                return ret(DATA_ERROR_CODE,'名称已存在,不可修改');
            }
            $updateData['name']= $data['business_name'];
        }
        if (!empty($data['status'])) {
            if ($data['status'] !== 1) { //非正常
                $updateData['status'] = $data['status'];
                if ($data['status'] == 3 || $data['status'] == 4) {
                    $updateData['delete_at'] = time();
                }
            }
        }
        if (!empty($data['avatar'])) {
            $updateData['avatar'] = $data['avatar'];
        }
        if (!empty($data['city'])) {
          $updateData['city'] = $data['city'];
        }
        if (!empty($data['password'])) {
            $updateData['password']=password_hash($data['password'], PASSWORD_DEFAULT);
        }
        $updateData['update_at'] = time();
        if (AccountModel::updateBusinessAccount($updateData,$data['customer_id']) !== false) {
            //添加日志
            writLogInfo($userInfo['id'] . '修改商户编号为: '.$customInfo['id'].' 的信息');
            $custom_id = $data['customer_id'];
            //写入ip白名单
            if (!empty($data['ip'])) {
                $ipData['ip']=$data['ip'];
                $ip = $data['ip'];
                if (!empty($ip)) {
                    $ipList = [];
                    foreach (explode('|', $ip) as $key => $value) {
                        array_push($ipList,['ip'=>$value, 'create_at'=>time(), 'agent_custom_id'=> $custom_id]);
                    }
                    if (AccountModel::updateIpList($ipList, $custom_id) !== false) {
                        //添加日志
                        writLogInfo('用户ID:'. $userInfo['id'] . ' 修改了白名单 - '.$ip);
                    }
                }    
            }

            //写入商家基础信息
            $baseInfo = [];
            if (!empty($data['contact_name'])) {
                $baseInfo['contact_name'] = $data['contact_name'];
            }
            if (!empty($data['contact_mobile'])) {
                $baseInfo['contact_mobile'] = $data['contact_mobile'];
            }
            if (!empty($data['address'])) {
                $baseInfo['address'] = $data['address'];
            }
            if (!empty($data['custom_logo'])) {
                $baseInfo['custom_logo'] = $data['custom_logo'];
            }
            if (!empty($data['custom_dy_num'])) {
                $baseInfo['custom_dy_num'] = $data['custom_dy_num'];
            }
            if (!empty($data['custom_dy_home'])) {
                $baseInfo['custom_dy_home'] = $data['custom_dy_home'];
            }
            if (!empty($data['custom_open_id'])) {
                $baseInfo['custom_open_id'] = $data['custom_open_id'];
            }
            if (!empty($data['contact_genger'])) {
                $baseInfo['contact_genger'] = $data['contact_genger'];
            }
            if (!empty($data['custom_active_num'])) {
                $baseInfo['custom_active_num'] = $data['custom_active_num'];
            }
            if (!empty($data['expire_at']) || $data['expire_at'] === '0') {
                $baseInfo['expire_at'] = $data['expire_at'];
            }
            if (!empty($data['scan_num'])) {
                $baseInfo['scan_num'] = $data['scan_num'];
            }
            if (AccountModel::updateBusinessBaseInfo($baseInfo,$custom_id) !== false) {
                //写入商家基础信息
                writLogInfo($userInfo['id'] . ' 修改商户基础信息:' . $custom_id);
            }
            return ret(SUCCESS_CODE,'更新成功');
        }
        return ret(DATA_ERROR_CODE, '更新失败，请稍后重试');
    }

  /**
   * 获取授权URL
  */
  public function getDouyinAuthUrl()
  {
    $activeId = input('get.activeId');
    if(empty($activeId)){
      return ret(400,'参数错误');
    }
    return ret(SUCCESS_CODE,'成功',DouyinModel::getDouyinAuthUrl($activeId));
  }

  /**
   * 获取access_token
  */
  public function getAccessToken(){
    $data = input();
    if (empty($data['code'])) {
        return ret(DATA_ERROR_CODE,'授权code不可为空');
    }
    return ret(SUCCESS_CODE,'成功',json_decode(DouyinModel::getAccessToken($data['code']),1));
  }
}