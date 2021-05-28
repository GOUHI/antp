<?php

namespace app\agent\model;

use think\facade\Db;
use think\Model;

class ActiveModel extends Model{
  protected $name = 'api_custom_active';
  /**
   * 活动创建
   */
  public static function createActive($activeData)
  {
    try {
        $active_id = Db::name('api_custom_active')->insertGetId($activeData);
        return $active_id;
    } catch (\Throwable $th) {
      return -1;
    }
  }

  /**
   * 活动读取
   */
  public static function getActiveById($active_id)
  {
    try {
        $activeInfo = Db::name('api_custom_active')->alias('t1')
                      ->join('api_custom_user t2', 't1.custom_id=t2.id')
                      ->field('*')->where('t1.id',$active_id)->where('t1.status',1)->find();
        return $activeInfo;
    } catch (\Throwable $th) {
      return null;
    }
  }

  /**
   * 读取商家已创建活动的扫码总数和活动总数
  */
  public static function getActiveScaNum($custom_id){
    // active_scan_num
    try {
      $activeInfo = Db::query('SELECT sum(active_scan_num) as scan_num,count(*) as active_num FROM api_custom_active WHERE custom_id='.$custom_id);
      return $activeInfo[0];
    } catch (\Throwable $th) {
      return null;
    }
  }
}