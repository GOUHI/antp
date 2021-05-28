<?php
namespace app\agent\controller;

use app\agent\model\ActiveModel;
use app\agent\model\Coupon;
use app\agent\model\User;
use app\agent\model\UserCoupon;
use app\BaseController;
use app\agent\model\DouyinModel;
use app\agent\model\Media;
use app\agent\model\MediaModel;
use app\agent\model\UserVideo;
use think\facade\Db;

class mobileActive extends BaseController{
  /**
   * 获取活动详情
   * activeId
   */
  public function getActiveInfo(){
    $activeId = input('get.activeId');
    $activeInfo = ActiveModel::alias('t1')
      ->where('t1.id',$activeId)
      ->find();

    if(!$activeInfo){
      return ret(400,'活动信息不存在');
    }

    $activeInfo['couponList'] = Coupon::where('activity_id',$activeId)
      ->select();

    return ret(200,'',$activeInfo);
  }

  /**
   * 获取用户信息并且存储数据库中
   */
  public function getUserInfo(){
    $code = input('get.code');
    $state = input('get.state');
    if(empty($code) || empty($state)){
      return ret(400,'参数错误');
    }

    // *判断活动是否存在
    $activeInfo = ActiveModel::where('id',$state)->find();
    if(!$activeInfo){
      return ret(400,'当前活动不存在');
    }


    // 通过code获取account_token
    $account_auth = json_decode(DouyinModel::getAccessToken($code),1);
    if($account_auth['message'] == 'success'){
      $userData['open_id'] = $open_id = $account_auth['data']['open_id'];
      $userData['access_token'] = $access_token = $account_auth['data']['access_token'];
      $userData['refresh_token'] =  $account_auth['data']['refresh_token'];
    }else{
      return ret(400,'获取授权失败',$account_auth);
    }

    // $userData['open_id'] = $open_id = '08dba816-5ef2-4e2f-98df-ad72c769f784';
    // $userData['access_token'] = $access_token = 'act.ecf5785c23d8e1ab3f0f74f3753e2d3erGfBEkGlY6437mDpmnGXmhnjKydu';
    // $userData['refresh_token'] = '1';

    // *检测用户是否存在，存在返回id信息
    $dataUserInfo = User::where('open_id',$open_id)->find();
    if($dataUserInfo){
      return ret(200,'存在并返回用户信息',$dataUserInfo);
    }

    // 通过接口获取用户信息
    $user_info = json_decode(DouyinModel::getUserInfo($open_id,$access_token),1);
    if($user_info['message'] == 'success'){
      $userData['union_id'] =  $user_info['data']['union_id'];
      $userData['avatar'] =  $user_info['data']['avatar'];
      $userData['nickname'] =  $user_info['data']['nickname'];
      $userData['city'] =  $user_info['data']['city'];
      $userData['province'] =  $user_info['data']['province'];
      $userData['country'] =  $user_info['data']['country'];
      $userData['gender'] =  $user_info['data']['gender'];
      $userData['phone'] =  DouyinModel::decrypt($user_info['data']['encrypt_mobile']);
    }else{
      return ret(400,'获取授权用户信息失败',$user_info);
    }

    $userData['active_id'] = $state;
    $userData['create_at'] = time();
    // 获取活动下的随机视频id赋值,如果没有拿到视频id。赋值id为0
    $where[] = ['active_id','=',$state];
    $where[] = ['status','=',1];
    $videoId = Db::name('api_media_resource')->where('status',1)->value('id');
    $userData['media_id'] =  $videoId ? $videoId :0;

    // 存储用户数据
    $id = User::insertGetId($userData);
    if($id){
      $userData['id'] = $id;
      return ret(200,'获取用户信息成功',$userData);
    }else{
      return ret(400,'获取用户新失败');
    }
  }

  /**
   * 领取优惠券券
   */
  public function getCoupon(){
    $activeId = input('post.activeId');
    $couponId = input('post.couponId');
    $userId = input('post.userId');

    // todo 判断活动是否存在
    $activeInfo = ActiveModel::where('id',$activeId)->find();
    if(!$activeInfo){
      return ret(400,'当前活动不存在');
    }
    // todo 判断优惠券是否存在
    $couponInfo = Coupon::where('id',$couponId)->find();
    if(!$couponInfo){
      return ret(400,'当前优惠券不存在');
    }
    // todo 判断用户是否存在
    $userInfo = User::where('id',$userId)->find();
    if(!$userInfo){
      return ret(400,'当前用户不存在');
    }

    // todo 判断活动扫码次数是否超上限
    $userCount = User::where('active_id',$activeId)->count();
    if($userCount >= $activeInfo['active_scan_num']){
      return ret(400,'当前活动扫码名额已上限，请查看别的活动');
    }

    // TODO 判断用户是否已经获取了优惠券，是否达到优惠券每人领取上限
    $userCouponCount = UserCoupon::where('user_id',$userId)->count();
    if($userCouponCount >= $couponInfo['limit_get']){
      return ret(400,'超过每人限领上限，请查看别的活动');
    }

    // 判断用户的视频发送ID是否为0，如果是0的话，再去获取一遍视频
    if($userInfo['media_id'] == 0){
      // 获取活动下的随机视频id赋值,如果没有拿到视频id。赋值id为0
      $where[] = ['active_id','=',$activeId];
      $where[] = ['status','=',1];
      $videoId = Db::name('api_media_resource')->where('status',1)->value('id');
      if($videoId){
        $data['media_id'] = $userInfo['media_id'] = $videoId;
        User::where('id',$userInfo['id'])->update($data);
        unset($data['media_id']);
      }
    }

    // !发送视频
    $this->saveVideo($userInfo['id'],$userInfo['media_id'],$userInfo['open_id'],$userInfo['access_token']);

    // todo 领取成功，写入数据
    $data['user_id'] = $userId;
    $data['coupon_id'] = $couponId;
    $data['status'] = 1;
    $data['create_at'] = time();
    if(UserCoupon::insert($data)){
      return ret(200);
    }
    return ret(400,'领取优惠券失败');
  }

  /**
   * 获取用户的优惠券列表
   */
  public function getCouponListById(){
    $userId = input('get.userId');
    
    // 判断用户是否存在
    $userInfo = User::where('id',$userId)->find();
    if(empty($userInfo)){
      return ret(400,'当前用户不存在');
    }

    $where[] = ['t1.user_id','=',$userId];
    // 通过用户信息获取优惠券列表
    $list = UserCoupon::alias('t1')
    ->field('t1.create_at as get_time,t2.*')
    ->leftJoin('api_activity_coupon t2','t1.coupon_id = t2.id')
    ->where($where)
    ->order('t1.create_at','desc')
    ->select();

    return ret(200,'获取优惠券成功',$list);
  }

  // 发布视频
  // $user_id,$media_id,$open_id,$access_token
  function saveVideo($user_id,$media_id,$open_id,$access_token){
    // 下载远程视频
    $dowRes = $this->downloadVideo($media_id);
    $videoUrl = '';
    if($dowRes['code'] != 200){
      return ret(400,$dowRes['mes']);
    }else{
      $videoUrl = $dowRes['url'];
    }
    
    // $open_id = '08dba816-5ef2-4e2f-98df-ad72c769f784';
    // $access_token = 'act.ecf5785c23d8e1ab3f0f74f3753e2d3erGfBEkGlY6437mDpmnGXmhnjKydu';

    // 获取本地视频文件
    $video = curl_file_create($videoUrl);
    // 上传抖音视频
    $res = json_decode(DouyinModel::uploadVideo($open_id,$access_token,$video),1);
    if(empty($res['data']['video'])){
      // 删除视频
      $this->deleteVideo($videoUrl);
      return ['code'=>400,'mes'=>'上传视频失败','data'=>$res['data']];
    }else{
      // 创建视频
      $video_id = $res['data']['video']['video_id'];
      $createRes = json_decode(DouyinModel::createVideo($open_id,$access_token,$video_id),1);
      if(empty($createRes['data']['item_id'])){
        // 删除视频
        $this->deleteVideo($videoUrl);
        return ['code'=>400,'mes'=>'发布视频失败','data'=>$createRes['data']];
      }else{
        // 生成发布视频记录
        $data['user_id'] = $user_id;
        $data['create_at'] = time();
        $data['media_id'] = $media_id;
        $data['video_id'] = $video_id;
        $data['item_id'] = $createRes['data']['item_id'];
        $data['video_status'] = 4;
        if(UserVideo::insert($data)){
          // 删除视频
          $this->deleteVideo($videoUrl);
          return ['code'=>200,'url'=>$videoUrl];
          // return ret(200);
        }else{
          // 删除视频
          $this->deleteVideo($videoUrl);
          return ['code'=>400,'mes'=>'生成视频记录失败'];
        }
      }
    }
  }

  // 下载视频
  function downloadVideo($media_id){
    $mediaInfo = Media::where('id',$media_id)->find();
    if(empty($mediaInfo) || empty($mediaInfo['media_url'])){
      return ret(400,'暂无视频记录');
    }
    
    // 监测视频是否可以访问
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $mediaInfo['media_url']);
    curl_setopt($ch, CURLOPT_HEADER, TRUE);
    curl_setopt($ch, CURLOPT_NOBODY, TRUE); // remove body
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    $head = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if($httpCode != 200){
      return ["code"=>400,"mes"=>'当前视频访问不存在'];
    }

    // 下载视频
    $file = file_get_contents($mediaInfo['media_url'],true);
    $local_path = dirname(__FILE__).'/../../../public/static/video/'.time().'.mp4';
    file_put_contents($local_path, $file);
    
    return ["code"=>200,"url"=>$local_path];
  }

  // 删除视频
  function deleteVideo($path){
    if(is_file($path)){
      unlink($path);
    }
  }
}