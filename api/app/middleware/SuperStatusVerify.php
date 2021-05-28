<?php

declare(strict_types=1);

namespace app\middleware;

use think\facade\Db;

class SuperStatusVerify
{
    /**
     * 处理请求
     *
     * @param \think\Request $request
     * @param \Closure       $next
     * @return Response
     */
    public function handle($request, \Closure $next)
    {
        try {
            $where[] = ['id','=',$request->id];
            $where[] = ['delete_at','=',0];
            $info = Db::name('super_admin')->where($where)->find();
            if($info){
                if($info['status'] == 2){
                    ret(400,'管理员黑名单状态');
                }
                $request->adminInfo = $info;
                return $next($request);
            }else{
                return ret(400,'管理员不存在或者已经被删除');
            }
        } catch (\Exception $e) {
            return ret(201, '获取管理员信息失败，请检查中间件', $e->getMessage());
        }
    }
}
