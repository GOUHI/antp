<?php

declare(strict_types=1);

namespace app\middleware;

use lib\Token;
use think\facade\Db;
use app\agent\model\AccountModel;

class AgentStatusVerify
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
            $info = AccountModel::getBusinessDetail($request->id);
            if ($info) {
                /**
                 * 1、正常
                 * 2、续签用户
                 * 3、停用
                 * 4、黑名单
                 */
                if ($info['status'] == 3) {
                    return ret(DATA_ERROR_CODE, '用户已停用');
                }
                if ($info['status'] == 4) {
                    return ret(DATA_ERROR_CODE, '用户已被系统拉入黑名单,请及时联系管理员');
                }
                if ($info['expire_at']!=0 && $info['expire_at'] <= time()) {
                    return ret(DATA_ERROR_CODE, '商户权限已过期,请及时续费');
                }
                $request->userInfo = $info;
                return $next($request);
            } else {
                return ret(400, '管理员不存在或者已经被删除');
            }
        } catch (\Exception $e) {
            return ret(201, '未传递auth信息', $e->getMessage());
        }
    }
}
