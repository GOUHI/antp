<?php

namespace app\agent\model;

use think\facade\Db;
use think\Model;

class AccountModel extends Model{
  /**
   * 通过用户获取用户信息
   */
  public static function getAgent_CustomInfoByName($name)
  {
    $userInfo = null;
    try {
      $userInfo = Db::name('api_custom_user')->where('name', $name)->field('*')->find();
    } catch (\Throwable $th) {
      return null;
    }
    return $userInfo;
  }

  /**
   * 通过用户ID获取用户信息
   */
  public static function getAgentCustomInfoById($id)
  {
    $userInfo = null;
    try {
      $userInfo = Db::name('api_custom_user')->where('id', $id)->field('*')->find();
    } catch (\Throwable $th) {
      return null;
    }
    return $userInfo;
  }

  /**
   * 添加商户
  */
  public static function createBusiness($data)
  {
    try {
      $id = Db::name('api_custom_user')->insertGetId($data);
      return $id;
    } catch (\Throwable $th) {
      return -1;
    }
  }

  /**
   * 获取商户列表
  */
  public static function getBusinessList($id, $keywords='',$limit=10){
    try {
      $where[] = ['t1.p_id','=', $id];
      if (!empty($keywords)) {
        $where[] = ['t1.name|t2.contact_mobile', 'like', '%' . $keywords . '%'];
      }
      $list = Db::name('api_custom_user')->alias('t1')
        ->join('api_agent_custom_baseinfo t2', 't1.id=t2.agent_custom_id')
        ->field('t1.id as customer_id, t1.sale_name, t1.city, t1.account_type, t1.name as business_name, t1.avatar, t1.status, t1.create_at, t1.update_at, t1.delete_at, t1.p_id, 
                 t2.agent_custom_id,t2.contact_name,t2.custom_logo, t2.contact_mobile, t2.custom_dy_num, t2.custom_dy_home,
                 t2.custom_open_id,t2.contact_genger,t2.agent_id,t2.custom_active_num,t2.expire_at,t2.scan_num,t2.address')
        ->where($where)
        ->order('t1.create_at', 'desc')
        ->paginate($limit);
      return $list;
    } catch (\Throwable $th) {
      return null;
    }
    return null;
  }

  /**
   * 获取商户详情
   */
  public static function getBusinessDetail($id)
  {
    try {
      $where[] = ['t1.id', '=', $id];
      $info = Db::name('api_custom_user')->alias('t1')
      ->join('api_agent_custom_baseinfo t2', 't1.id=t2.agent_custom_id')
      ->field('t1.id as customer_id, t1.id, t1.sale_name, t1.city, t1.account_type, t1.name as business_name, t1.avatar, t1.status, t1.create_at, t1.update_at, t1.delete_at, t1.p_id, 
                 t2.agent_custom_id,t2.contact_name,t2.custom_logo, t2.contact_mobile, t2.custom_dy_num, t2.custom_dy_home,
                 t2.custom_open_id,t2.contact_genger,t2.agent_id,t2.custom_active_num,t2.expire_at,t2.scan_num,t2.address')
      ->where($where)
        ->find();
      return $info;
    } catch (\Throwable $th) {
      return null;
    }
    return null;
  }


  /**
   * 获取IP白名单
  */
  public static function getIpList($id){
    try {
      $list = Db::name('api_agent_custom_ip')
        ->where('agent_custom_id',$id)
        ->field('*')
        ->select();
      return $list;
    } catch (\Throwable $th) {
      return null;
    }
    return null;
  }

  /**
   * 更新商户账户信息
  */
  public static function updateBusinessAccount($data, $id)
  {
    try {
      if (Db::name('api_custom_user')->where('id',$id)->update($data) !== false) {
        return true;
      }
      return false;
    } catch (\Throwable $th) {
      return false;
    }
  }

  /**
   * 更新商户基础信息
  */
  public static function updateBusinessBaseInfo($data, $id)
  {
    try {
      if (Db::name('api_agent_custom_baseinfo')->where('agent_custom_id',$id)->update($data) !== false) {
        return true;
      }
      return false;
    } catch (\Throwable $th) {
      return false;
    }
  }

  /**
   * 添加商户基础信息
  */
  public static function insertBusinessBaseInfo($data){
    try {
      if (Db::name('api_agent_custom_baseinfo')->insert($data) !== false) {
        return true;
      }
      return false;
    } catch (\Throwable $th) {
      return false;
    }
  }

  /**
   * 添加ip白名单
  */
  public static function addIpList($ipList, $custom_id)
  {
    try {
      if (Db::name('api_agent_custom_ip')->insertAll($ipList) !== false) {
        return true;
      }else{
        return false;
      }
    } catch (\Throwable $th) {
      return false;
    }
  }

  /**
   * 更新ip白名单
  */
  public static function updateIpList($ipList, $custom_id)
  {
    try {
      if (Db::name('api_agent_custom_ip')->where('agent_custom_id',$custom_id)->delete() !== false) {
        return AccountModel::addIpList($ipList, $custom_id);
      }else{
        return false;
      }
    } catch (\Throwable $th) {
      return false;
    }
  }

  /**
   * 通过用户id查询当前ip是否可用
   */
  public static function returnExistIp($id,$ip)
  {
    try {
      $ipList = Db::name('api_agent_custom_ip')->where('agent_custom_id', $id)->field('ip')->select();
      if (count($ipList) > 0) {
        foreach ($ipList as $key => $value) {
          if ($value['ip'] == $ip) {
            return true;
          }
        }
        return false;
      }
      return false;
    } catch (\Throwable $th) {
      return false;
    }
  }
}