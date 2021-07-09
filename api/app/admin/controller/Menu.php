<?php

namespace app\admin\controller;

use app\admin\model\Menu as ModelMenu;
use app\BaseController;

class Menu extends BaseController{
  /**
   * 返回菜单数据信息
   * 提供给前端使用路由信息和菜单信息
   */
  public function getTreeMenuList(){
    $list = ModelMenu::getMenuTreeData();
    return ret(200,'',$list);
  }
}