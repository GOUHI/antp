<?php

namespace app\admin\controller;

use app\admin\model\Admin as ModelAdmin;
use app\admin\validate\Admin as ValidateAdmin;
use app\BaseController;
use think\facade\Db;

class Admin extends BaseController{

  /**
   * 管理员列表
   */
  public function list(){
    $query = input();
    $where = [];
    if(array_key_exists("keywords",$query)){
      $where[] = ['account|name','like','%'.$query['keywords'].'%'];
    }
    // 获取每页显示条数
    $limit = in_array('limit',$query) ? $query['limit'] : 20;

    // 获取列表数据
    $list = ModelAdmin::where($where)
      ->order('create_at','desc')
      ->paginate($limit);
    return ret(200,'',$list->toArray()['data'],["total"=>$list->total()]);
  }

  /**
   * 管理员详情
   */
  public function info(){
    $id = input('get.id');
    $info = ModelAdmin::getAdminInfoById($id);
    if(!$info){
      return ret(400,'管理员不存在');
    }

    return ret(200,'',$info);
  }

  /**
   * 创建管理员
   */
  public function save(){
    $data = input();
    $validate = new ValidateAdmin();
    if (!$validate->scene('save')->check($data)) {
        return ret(400, $validate->getError());
    }
    // 判断管理员是否重复
    $info = ModelAdmin::getAdminInfoByAccount(null,$data['account']);
    if($info){
      return ret(400,'管理员账户已经存在');
    }

    if($info['name'] == $data['name']){
      return ret(400,'管理员名称已经存在');
    }

    $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
    $data['status'] = 1;
    $data['create_at'] = $data['update_at'] = time();
    if(Db::name('super_admin')->save($data)){
      return ret(200,'创建管理员成功');
    }
    return ret(400,'创建管理员失败');
  }

  /**
   * 修改管理员
   */
  public function update(){
    $data = input();
    $validate = new ValidateAdmin();
    if (!$validate->scene('update')->check($data)) {
        return ret(400, $validate->getError());
    }
    // 判断管理员是否重复
    $info = ModelAdmin::getAdminInfoByAccount($data['id'],$data['account']);
    if($info){
      return ret(400,'管理员账户已经存在');
    }

    if(ModelAdmin::getAdminInfoByName($data['id'],$data['name'])){
      return ret(400,'管理员名称已经存在');
    }

    if(in_array('password',$data)){
      $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
    }
    $data['status'] = $data['status'];
    $data['update_at'] = time();
    if(Db::name('super_admin')->where('id',$data['id'])->update($data)){
      return ret(200,'修改管理员成功');
    }
    return ret(400,'修改管理员失败');
  }

  /**
   * 删除管理员(软删除)
   */
  public function delete(){
    $id = input('get.id');
    $info = ModelAdmin::getAdminInfoById($id);
    if(!$info){
      return ret(400,'删除的管理员不存在');
    }

    $data['delete_at'] = time();
    if(ModelAdmin::where('id',$id)->update($data)){
      return ret(200,'管理员删除成功');
    }

    return ret(400,'管理员删除失败');
  }
}