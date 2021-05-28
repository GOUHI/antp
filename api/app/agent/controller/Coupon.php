<?php
namespace app\agent\controller;

use app\agent\model\ActiveModel;
use app\agent\model\Coupon as ModelCoupon;
use app\agent\validate\Coupon as ValidateCoupon;
use app\BaseController;
class Coupon extends BaseController{
  /**
   * 优惠券列表
   */
  public function list(){
    $activityId = input('get.activityId');
    $threshold = input('get.threshold');
    $expiryType = input('get.expiryType');
    $keywords = input('get.keywords');
    $limit = input('get.limit',20);

    $where = [];

    // 查询活动所属优惠券
    if($activityId){
      $where[] = ['t1.activity_id','=',$activityId];
    }

    // 判断查询的是类型
    if($threshold){
      $where[] = ['t1.threshold','=',$threshold];
    }

    // 判断查询的是代理商或者商户
    if($expiryType){
      $where[] = ['t1.expiry_type','=',$expiryType];
    }

    if($keywords){
      $where[] = ['t1.name|coupon_desc','like','%'.$keywords.'%'];
    }

    $list = ModelCoupon::alias('t1')
    ->where($where)
    ->order('t1.create_at','desc')
    ->paginate($limit);

    $listData = $list->toArray()['data'];

    $thresholdText = ['未知','无门槛','满减'];
    $expiryTypeText = ['未知','时间段有效','领取剩余天数'];

    if(!empty($listData)){
      foreach($listData as $k=>$v){
        $listData[$k]['threshold_text'] = $thresholdText[$v['threshold']];
        $listData[$k]['expiry_type_text'] = $expiryTypeText[$v['expiry_type']];
      }
    }

    return ret(200,'',$listData,['total'=>$list->total()]);
  }
  
  /**
   * 优惠券详情
   */
  public function info(){
    $id = input('get.id');

    $info = ModelCoupon::where('id',$id)->find();
    if(!$info){
      return ret(400,'暂无优惠券信息');
    }
    $thresholdText = ['未知','无门槛','满减'];
    $info['threshold_text'] = $thresholdText[$info['threshold']];

    $expiryTypeText = ['未知','时间段有效','领取剩余天数'];
    $info['expiry_type_text'] = $expiryTypeText[$info['expiry_type']];

    return ret(200,'',$info);
  }
  
  /**
   * 创建优惠券
   */
  public function save(){
    $data = input();
    $validate = new ValidateCoupon();
    if (!$validate->scene('save')->check($data)) {
        return ret(400, $validate->getError());
    }

    // *判断活动是否存在
    $activeInfo = ActiveModel::where('id',$data['activity_id'])->find();
    if(!$activeInfo){
      return ret(400,'当前活动不存在');
    }

    if($data['expiry_type'] == 1){
      if($data['expiry_start'] < time() || $data['expiry_end'] < time()){
        return ret(400,'有效期时间不可以小于当前时间');
      }

      if($data['expiry_start'] >= $data['expiry_end']){
        return ret(400,'开始时间不能小于或者等于结束时间');
      }
    }

    $data['create_at'] = $data['update_at'] = time();
    if(ModelCoupon::insert($data)){
      return ret(200);
    }
    return ret(400,'优惠券创建失败');
  }

  /**
   * 修改优惠券
   */
  public function update(){
    $data = input();
    $validate = new ValidateCoupon();
    if (!$validate->scene('update')->check($data)) {
        return ret(400, $validate->getError());
    }

    // *判断活动是否存在
    $activeInfo = ActiveModel::where('id',$data['activity_id'])->find();
    if(!$activeInfo){
      return ret(400,'当前活动不存在');
    }

    if($data['expiry_type'] == 1){
      if($data['expiry_start'] < time() || $data['expiry_end'] < time()){
        return ret(400,'有效期时间不可以小于当前时间');
      }

      if($data['expiry_start'] >= $data['expiry_end']){
        return ret(400,'开始时间不能小于或者等于结束时间');
      }
    }

    $data['update_at'] = time();
    if(ModelCoupon::where('id',$data['id'])->update($data)){
      return ret(200);
    }
    return ret(400,'优惠券修改失败');
  }

  /**
   * 删除代理商或者商户
   */
  public function delete(){
    $id = input('get.id');

    // 查询优惠券否存在
    $info =  ModelCoupon::where('id','=',$id)->find();
    if(!$info){
      return ret(400,'优惠券不存在');
    }

    try{
      ModelCoupon::where('id',$id)->delete();
      return ret(200);
    }catch(\Exception $e){
      return ret(404,'',$e->getMessage());
    }
  }
}