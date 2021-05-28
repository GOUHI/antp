<?php

use app\middleware\AgentStatusVerify;
use app\middleware\CheckToken;
use think\facade\Route;

Route::group('account', function () {
  // 创建管理员
  Route::post('login', 'account/login');

  // 获取抖音授权页面
  Route::get('getDouyinAuthUrl', 'account/getDouyinAuthUrl');
  // 获取抖音授权getAccessToken
  Route::get('getAccessToken', 'account/getAccessToken');
  
  // 代理添加商家
  Route::post('createBusiness', 'account/createBusiness')->middleware(CheckToken::class)->middleware(AgentStatusVerify::class);
  // 更新商家信息
  Route::post('updateBaseInfo', 'account/updateBaseInfo')->middleware(CheckToken::class)->middleware(AgentStatusVerify::class);
  //商户列表
  Route::get('businessList', 'account/businessList')->middleware(CheckToken::class)->middleware(AgentStatusVerify::class);
  //商户详情
  Route::get('getCustomDetail', 'account/getCustomDetail')->middleware(CheckToken::class)->middleware(AgentStatusVerify::class);
  //ip列表
  Route::post('getIpList', 'account/getIpList')->middleware(CheckToken::class)->middleware(AgentStatusVerify::class);
})->pattern([['id' => '\d+']]);

Route::group('active', function () {
  // 商户添加活动
  Route::post('createActive', 'activeHome/createActive')->middleware(CheckToken::class)->middleware(AgentStatusVerify::class);
  // 商户活动列表
  Route::get('getActiveList', 'activeHome/getActiveList');
  // 商户活动详情
  Route::post('getActiveDetail', 'activeHome/getActiveDetail');
  // 商户活动更新
  Route::post('updateActiveInfo', 'activeHome/updateActiveInfo')->middleware(CheckToken::class)->middleware(AgentStatusVerify::class);
})->pattern([['id' => '\d+']]);

Route::group('txapi', function () {
  // 腾讯签名
  Route::get('getSignature', 'txResourcesUpload/getSignature')->middleware(CheckToken::class)->middleware(AgentStatusVerify::class);
  //素材地址上报
  Route::post('uploadMediaFile', 'txResourcesUpload/uploadMediaFile')->middleware(CheckToken::class)->middleware(AgentStatusVerify::class);
  //视频合成
  Route::post('createNewVedio', 'txResourcesUpload/createNewVedio')->middleware(CheckToken::class)->middleware(AgentStatusVerify::class);
  //视频操作事件拉取
  Route::get('createVedioRes', 'txResourcesUpload/createVedioRes')->middleware(CheckToken::class)->middleware(AgentStatusVerify::class);
  
  //获取素材列表
  Route::get('getAllBaseVedioList', 'txResourcesUpload/getAllBaseVedioList')->middleware(CheckToken::class)->middleware(AgentStatusVerify::class);
  //删除素材
  Route::post('deleteBaseVedio', 'txResourcesUpload/deleteBaseVedio')->middleware(CheckToken::class)->middleware(AgentStatusVerify::class);
 
  //获取合成视频记录
  Route::get('getVedioList', 'txResourcesUpload/getVedioList');
  //获取该活动已合成视频数量
  Route::get('getVedioUploadPress', 'txResourcesUpload/getVedioUploadPress');
})->pattern([['id' => '\d+']]);

Route::group('coupon', function () {
  // 优惠券列表
  Route::get('list','coupon/list');
  // 优惠券详情
  Route::get('info','coupon/info');
  // 创建优惠券
  Route::post('save', 'coupon/save');
  // 编辑优惠券
  Route::post('update', 'coupon/update');
  // 删除优惠券
  Route::get('delete', 'coupon/delete');
})->pattern([['id' => '\d+']]);
//->middleware(CheckToken::class)->middleware(AgentStatusVerify::class);


Route::group('mobileActive', function () {
  // 获取用户数据
  Route::get('userInfo','mobileActive/getUserInfo');
  // 活动详情
  Route::get('info','mobileActive/getActiveInfo');
  // 领取优惠券
  Route::post('getCoupon', 'mobileActive/getCoupon');
  // 显示用户优惠券列表
  Route::get('getCouponById','mobileActive/getCouponListById');
  Route::get('saveVideo','mobileActive/saveVideo');
})->pattern([['id' => '\d+']]);
//->middleware(CheckToken::class)->middleware(AgentStatusVerify::class);