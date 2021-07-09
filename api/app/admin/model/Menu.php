<?php
/**
 * 管理员操作
 */

 namespace app\admin\model;

use think\facade\Db;
use think\Model;

class Menu extends Model{
  // 表名称
  protected $name = 'admin_menu';
  // 数据转换为驼峰命名
  protected $convertNameToCamel = true;

  /**
   * 获取菜单列表，树形结构
   */
  public static function getMenuTreeData(){
    return Menu::select();
  }
}