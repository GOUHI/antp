<?php 
namespace app\agent\validate;

use think\Validate;

class ActiveVali extends Validate{
  protected $rule = [
    'id|标识'=>'require',
    'custom_id|商户编号'=>'require',
    'active_name|活动名称' => 'require|min:1|max:100',
    'active_model|活动模式' => 'require|in:1,2',
    'goto_type|跳转链接类型' => 'require|min:1|max:3',
    'goto_url|跳转链接' => 'require|min:1|max:100',
    'active_scan_num|活动扫码数' => 'require',
    'active_desc|活动描述' => 'require',
  ];

  protected $scene = [
    'createActive' => ['custom_id','active_name','active_model','goto_type','goto_url','active_scan_num','active_desc']
  ];
}