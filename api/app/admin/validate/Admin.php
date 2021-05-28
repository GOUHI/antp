<?php 
namespace app\admin\validate;

use think\Validate;

class Admin extends Validate{
  protected $rule = [
    'id|标识'=>'require',
    'account|账户'=>'require|min:1|max:60|unique:super_admin',
    'name|账户'=>'require|min:1|max:60|unique:super_admin',
    'password|密码'=>'require|min:6|max:20',
    'status|状态'=>'require|in:1,2'
  ];

  protected $scene = [
    'save' => ['account','name','password'],
    'update' => ['id','account','name','status']
  ];
}