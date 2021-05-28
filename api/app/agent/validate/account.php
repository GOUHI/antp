<?php 
namespace app\agent\validate;

use think\Validate;

class Account extends Validate{
  protected $rule = [
    'id|标识'=>'require',
    'customer_id|标识'=>'require',
    'name|账户' => 'require|min:1|max:25',
    'business_name|商家名称' => 'require|min:1|max:50',
    'contact_name|商家联系人' => 'require|min:1|max:25',
    'contact_mobile|手机号' => 'require|min:11|max:11',
    'custom_open_id|openid' => 'require|min:1|max:128',
    'custom_dy_home|商家抖音主页' => 'require|min:1|max:128',
    'scan_num|扫码次数' => 'require',
    'custom_active_num|活动次数' => 'require',
    'password|密码'=>'require|min:6|max:20',
    'status|状态'=>'require|in:1,2'
  ];

  protected $scene = [
    'login' => ['name','password'],
    'createBusiness' => ['business_name','contact_name', 'contact_mobile', 'custom_open_id','password', 'scan_num', 'custom_active_num', 'custom_dy_home'],
    'updateBusiness' => ['customer_id']
  ];
}