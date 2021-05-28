<?php

declare(strict_types=1);

namespace app\middleware;

use app\agent\model\AccountModel;
use lib\Token;
use think\facade\Db;

class CheckToken
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
        /**
         * 获取token，进行jwt验证。并且获取管理员信息
         */
        try {
            $token = $_SERVER['HTTP_AUTHORIZATION'];
            $jwt = new Token();
    
            if (strpos($token, 'Bearer') !== false) {
                $heads = explode(' ', $token);
                $token = $heads[1];
            }
            $res = $jwt->checkToken($token);
            if ($res['code'] == 200) {
                $request->id = ((array)$res['data']['data'])['id'];
                $isExistIp = AccountModel::returnExistIp($request->id, $request->ip());
                if (!$isExistIp) {
                    return ret(IP_VERFITY_ERROR, Account_Ip_Pass);
                }

                return $next($request);
            } else {
                return ret(201, '验证错误', $res['msg']);
            }
        } catch (\Exception $e) {
            return ret(201, '未传递auth信息', $e->getMessage());
        }
    }
}
