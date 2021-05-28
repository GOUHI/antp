<?php
namespace app\agent\controller;
use app\agent\model\MediaModel;
use app\agent\model\ActiveModel;
use app\BaseController;
use app\Request;
use app\agent\validate\TxSourceMediaVail as validate;

class TxResourcesUpload extends BaseController{
    /**
     * 获取签名
    */
    public function getSignature(Request $request)
    {
        $userInfo = $request->userInfo;
        if ($userInfo['account_type'] == 1 || $userInfo['account_type'] == 3) { //拥有商户权限
            // 确定签名的当前时间和失效时间
            $current = time();
            $expired = $current + 86400;  // 签名有效期：1天

            // 向参数列表填入参数
            $arg_list = array(
                "secretId" => TX_SECRET_ID,
                "currentTimeStamp" => $current,
                "expireTime" => $expired,
                "random" => rand()
            );

            // 计算签名
            $original = http_build_query($arg_list);
            $signature = base64_encode(hash_hmac('SHA1', $original, TX_SECRET_KEY, true) . $original);
            writLogInfo('用户编号:'. $userInfo['id']."生成了签名");
            return ret(SUCCESS_CODE, '签名成功',$signature);
        } else {
            return ret(DATA_ERROR_CODE, '您还没有拥有商户权限,请联系客服');
        }
    }

    /**
     * 媒体素材文件上传成功后上报数据库
    */
    public function uploadMediaFile(Request $request)
    {
        $data = input();
        $validate = new validate();
        if (!$validate->scene('uploadMedia')->check($data)) {
            return ret(400, $validate->getError());
        }
        $userInfo = $request->userInfo;
        if ($userInfo['account_type'] == 1 || $userInfo['account_type'] == 3) { //拥有代理商权限
            $data['custom_id'] = $userInfo['id'];
            $data['create_at'] = time();
            if (($id = MediaModel::mediaInfoUpload($data)) !== -1) {
                if ($id == -1001) {
                    return ret(DATA_ERROR_CODE, '素材已存在，无需重复上传');
                }
                writLogInfo($userInfo['id'].'提交了素材');
                return ret(SUCCESS_CODE, '提交成功',$id);
            }else{
                return ret(DATA_ERROR_CODE, '提交失败,请稍后重试!');
            }
        } else {
            return ret(DATA_ERROR_CODE, '您还没有拥有商户权限,请联系客服');
        }
    }

    /**
     * 视频合成
    */
    public function createNewVedio(Request $request){
        $data = input();
        $validate = new validate();
        if (!$validate->scene('mediaCollect')->check($data)) {
            return ret(400, $validate->getError());
        }
        $userInfo = $request->userInfo;
        if ($userInfo['account_type'] == 1) { //拥有代理商权限
            return ret(DATA_ERROR_CODE, '您还不是代理商,不可生成视频记录！请联系客服');
        }

        $activeInfo = ActiveModel::getActiveById($data['active_id']);
        if (empty($activeInfo)) {
            return ret(DATA_ERROR_CODE, '活动不存在或活动已结束');
        }else{
            if ($activeInfo['p_id'] != $userInfo['id'] && $activeInfo['custom_id'] !== $userInfo['id']) {
                return ret(DATA_ERROR_CODE, '您无权生成该商家的视频记录');
            }
            $scan_num = MediaModel::getMediaListByActiveId($data['active_id']);
            if ($activeInfo['active_scan_num'] <= $scan_num['count']+$data['collect_count']) {
                return ret(DATA_ERROR_CODE, '您的活动视频最多还能生成'.($activeInfo['active_scan_num'] - $scan_num['count']).'条');
            }
        }

        $res_code = MediaModel::createMedia($data['active_id'],$data['collect_count'], $activeInfo['custom_id'], $data['vedio_time_rang'], $data['vedio_title'], $data['width'], $data['height']);
        if ($res_code == -1001) {
            return ret(DATA_ERROR_CODE, '视频消耗完成，请上传更多素材视频');
        }
        if ($res_code == -1002) {
            return ret(DATA_ERROR_CODE, '腾讯api报错,请及时联系客服');
        }
        if ($res_code == 1) {
            return ret(SUCCESS_CODE,'请求合成成功');
        }
        return ret(DATA_ERROR_CODE,"请求失败");
    }

    /**
     * 获取合成视频的记录
     */
    public function getVedioList()
    {
        $data = input();
        $status = empty($data['status']) ? null : $data['status'];
        $active_id = empty($data['active_id']) ? null : $data['active_id'];
        return ret(SUCCESS_CODE, '成功', MediaModel::getVedioRecrodList($status, $active_id));
    }

    /**
     * 获取该活动已合成视频数
     */
    public function getVedioUploadPress()
    {
        $data = input();
        MediaModel::getVedioRecrodList();
        return ret(SUCCESS_CODE, '成功',['num'=>MediaModel::getMediaListByActiveId($data['active_id'])]);
    }

    /**
     * 获取素材列表
     */
    public function getAllBaseVedioList(Request $request)
    {
        $data = input();
        $userInfo = $request->userInfo;

        return ret(SUCCESS_CODE, '成功', MediaModel::getAllBaseVedioList($userInfo['id'], !empty($data['limit']) ? $data['limit'] : '10'));
    }

    /**
     * 删除素材
     */
    public function deleteBaseVedio(Request $request)
    {
        $data = input();
        $userInfo = $request->userInfo;
        if (MediaModel::deleteBaseVedio($data['id'], $userInfo['id']) != false) {
            return ret(SUCCESS_CODE, '删除成功');
        }else{
            return ret(DATA_ERROR_CODE, '删除失败');
        }
    }
}