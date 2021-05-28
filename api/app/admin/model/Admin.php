<?php
/**
 * 管理员操作
 */

 namespace app\admin\model;

use think\facade\Db;
use think\Model;

class Admin extends Model{
  // 表名称
  protected $name = 'admin';
  // 数据转换为驼峰命名
  protected $convertNameToCamel = true;

  /**
   * 通过名称查询管理员是否重复 
   */ 
  public static function getAdminInfoByAccount($id,$account){
    if($id){
     return Db::name('super_admin')->where('account',$account)->where('id','<>',$id)->field('*')->find();
    }else{
    //  return Db::name('super_admin')->where('account',$account)->field('*')->find();
     return Admin::where('account',$account)->field('*')->find();
    }
  }
  /**
   * 通过名称查询管理员是否重复 
   */ 
  public static function getAdminInfoByName($id,$name){
     if($id){
      return Db::name('super_admin')->where('name',$name)->where('id','<>',$id)->field('*')->find();
     }else{
      return Db::name('super_admin')->where('name',$name)->field('*')->find();
     }
   }

   /**
    * 通过管理员ID查询详情
    */
  public static function getAdminInfoById($id){
    $where[] = ['id','=',$id];
    $where[] = ['delete_at','=',0];
    return Db::name('super_admin')->where($where)->find();
  }
 }