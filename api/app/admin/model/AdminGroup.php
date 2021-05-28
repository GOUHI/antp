<?php
/**
 * 管理员操作
 */

 namespace app\admin\model;

use think\facade\Db;
use think\Model;

class AdminGroup extends Model{
  // 表名称
  protected $name = 'admin_group';
  // 数据转换为驼峰命名
  protected $convertNameToCamel = true;

  /**
   * 通过ID查询分组信息
   */ 
  public static function getGroupInfo($id){
     return AdminGroup::where('id',$id)->find();
  }
 }