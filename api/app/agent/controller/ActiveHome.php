<?php

namespace app\agent\controller;

use app\agent\model\ActiveModel;
use app\agent\model\AccountModel;
use app\agent\validate\ActiveVali as validate;
use app\BaseController;
use app\Request;
use lib\Token;

class ActiveHome extends BaseController{
    /**
     * 添加活动
     */
    public function createActive(Request $request){
        $data = input();
        $userInfo = $request->userInfo;
        if ($userInfo['account_type'] == 1 || $userInfo['account_type'] == 3) { //拥有商户权限
            $validate = new validate();
            $data['custom_id']=$userInfo['id'];
            if (!$validate->scene('createActive')->check($data)) {
                return ret(DATA_ERROR_CODE, $validate->getError());
            }

            $currentInfo = AccountModel::getBusinessDetail($userInfo['id']);
            $activeInfo = ActiveModel::getActiveScaNum($userInfo['id']);
            if ($currentInfo['custom_active_num'] <= $activeInfo['active_num']) {
                return ret(DATA_ERROR_CODE, '活动次数创建上限');
            }
            if ($currentInfo['scan_num'] !=0 &&  $currentInfo['scan_num'] <= $activeInfo['scan_num']) {
                return ret(DATA_ERROR_CODE, '扫码次数上限');
            }

            $data['create_at'] = time();
            $data['status'] = 1;
            if (($activeId = ActiveModel::createActive($data)) == -1) {
                return ret(DATA_ERROR_CODE, '活动创建失败,请联客服');
            }else{
                return ret(SUCCESS_CODE, '活动创建成功',['active_id'=>$activeId]);
            }
        }else{
            return ret(DATA_ERROR_CODE, '您还没有拥有商户权限,请联系客服');
        }
    }

    /**
     * 活动列表
     */
    public function getActiveList()
    {
        $status = input('get.status');
        $keywords = input('get.keywords');
        $limit = input('get.limit', 20);

        $where = [];
        if ($keywords) {
            $where[] = ['t1.active_name|id', 'like', '%' . $keywords . '%'];
        }
        if ($status) {
            $where[] = ['t1.status', '=', $status];
        }
        $list = ActiveModel::alias('t1')
        ->where($where)
        ->order('t1.create_at', 'desc')
        ->paginate($limit);

        $listData = $list;
        return ret(SUCCESS_CODE, '获取成功', $listData);
    }

    /**
     * 活动详情
     */
    public function getActiveDetail()
    {
        $id = input('post.id');
        try {
            $info = ActiveModel::alias('t1')
                ->where('t1.id', $id)
                ->find();
            return ret(SUCCESS_CODE, '获取成功', $info);
        } catch (\Throwable $th) {
            return ret(DATA_ERROR_CODE, '获取失败，请稍后再试');
        }
    }

    /**
     * 修改活动内容
     */
    public function updateActiveInfo(Request $request)
    {
        $data = input();
        $data['update_at'] = time();
        if (ActiveModel::where('id', $data['id'])->update($data)) {
            return ret(SUCCESS_CODE,'修改成功');
        }
        return ret(DATA_ERROR_CODE, '修改失败');
    }

}