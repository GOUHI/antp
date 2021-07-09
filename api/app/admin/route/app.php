<?php

use think\facade\Route;
use app\middleware\CheckSuperToken;
use app\middleware\SuperStatusVerify;

// 无需中间件的路由分组
Route::group('index', function () {
  Route::get('test', 'index/test')->middleware(\app\middleware\CheckAgentToken::class);
  Route::get('qianfa', 'index/qianfa');
  Route::get('yanzheng', 'index/yanzheng');
  Route::post('login', 'index/login');
})->pattern([['id' => '\d+']]);

/**
 * 菜单操作
 */
Route::group('menu',function(){
  // 获取树形菜单数据
  Route::get('tree','menu/getTreeMenuList');
});

// 管理员操作
Route::group('super', function () {
  // 管理员列表
  Route::get('list','admin/list');
  // 管理员详情
  Route::get('info','admin/info');
  // 创建管理员
  Route::post('save', 'admin/save');
  // 编辑管理员
  Route::post('update', 'admin/update');
  // 删除管理员
  Route::get('delete', 'admin/delete');
})->pattern([['id' => '\d+']])->middleware([CheckSuperToken::class,SuperStatusVerify::class]);

// 代理操作
Route::group('custom', function () {
  // 代理列表
  Route::get('list','customUser/list');
  // 代理详情
  Route::get('info','customUser/info');
  // 创建代理
  Route::post('save', 'customUser/save');
  // 编辑代理
  Route::post('update', 'customUser/update');
  // 删除代理
  Route::get('delete', 'customUser/delete');
})->pattern([['id' => '\d+']])->middleware([CheckSuperToken::class,SuperStatusVerify::class]);