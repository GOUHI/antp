<?php 
namespace app\agent\validate;

use think\Validate;

class Coupon extends Validate{
  protected $rule = [
    'id|标识'=>'require',
    'activity_id|活动标识'=>'require',
    'name|优惠券名称'=>'require|min:2|max:60|unique:api_activity_coupon',
    'distribution_total|发放总量'=>'require',
    'face_amount_bond|券面额'=>'require',
    'threshold|使用门槛'=>'require|in:1,2',
    'full_reduction|满减金额'=>'requireIf:threshold,2',
    'expiry_type|有效期类型'=>'require|in:1,2',
    'expiry_start|有效期开始时间'=>'requireIf:expiry_type,1',
    'expiry_end|有效期结束时间'=>'requireIf:expiry_type,1',
    'expiry_date|有效期剩余天数'=>'requireIf:expiry_type,2',
    'limit_get|每人限领'=>'require|min:1|max:10',
    'coupon_desc|优惠券描述'=>'max:255',
  ];

  protected $scene = [
    'save' => ['activity_id','name','distribution_total','face_amount_bond','threshold','full_reduction','expiry_type','expiry_start','expiry_end','expiry_date','limit_get','coupon_desc'],
    'update' => ['id','activity_id','name','distribution_total','face_amount_bond','threshold','full_reduction','expiry_type','expiry_start','expiry_end','expiry_date','limit_get','coupon_desc']
  ];
}