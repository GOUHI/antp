<?php 
namespace app\admin\validate;

use think\Validate;

class CustomUser extends Validate{
  protected $rule = [
    'id|标识'=>'require',
    'account_type|账户类型'=>'require|in:1,2,3',
    'name|账户名称'=>'require|min:1|max:60|unique:api_custom_user',
    'password|密码'=>'require|min:6|max:20',
    'contact_name|姓名'=>'require|min:1|max:60',
    'contact_mobile|联系方式'=>'require|mobile',
  ];

  protected $scene = [
    'save' => ['account_type','name','password','contact_name','contact_mobile'],
    'update' => ['id','account_type','name','contact_name','contact_mobile']
  ];
}