<?php

namespace app\agent\model;

use think\facade\Db;
use think\Model;
use TencentCloud\Vod\V20180717\VodClient;
use TencentCloud\Vod\V20180717\Models\ComposeMediaRequest;
use TencentCloud\Common\Credential;
use TencentCloud\Common\Profile\ClientProfile;
use TencentCloud\Common\Profile\HttpProfile;
use TencentCloud\Common\Exception\TencentCloudSDKException;
use TencentCloud\Vod\V20180717\Models\PullEventsRequest;
use TencentCloud\Vod\V20180717\Models\ConfirmEventsRequest;

class MediaModel extends Model
{

    protected $name = 'api_media_resource';

    /**
     * 材料元素上报
     */
    public static function mediaInfoUpload($data)
    {
        try {
            $media = Db::name('api_media')->where('media_id',$data['media_id'])->find();
            if (empty($media)) {
                $id = Db::name('api_media')->insertGetId($data);
                return $id;
            }else{
                return -1001;
            }
        } catch (\Throwable $th) {
            return -1;
        }
    }

    /**
     * 读取活动已合成的视频数
     */
    public static function getMediaListByActiveId($active_id)
    {
        try {
            $count = Db::query('select count(*) as count from api_media_resource where active_id='. $active_id);
            return $count[0]['count'];
        } catch (\Throwable $th) {
            return -1;
        }
    }

    /**
     * 读取视频素材库
    */
    public static function getMediaByCustomId($custom_id){
        try {
            $count = Db::query('SELECT * FROM api_media WHERE custom_id = '.$custom_id.' and media_type=1');
            return $count;
        } catch (\Throwable $th) {
            return -1;
        }
    }

    /**
     * 读取音频素材库
    */
    public static function getAedioByCustomId($custom_id){
        try {
            $count = Db::query('SELECT * FROM api_media WHERE custom_id = '.$custom_id.' and media_type=2');
            return $count;
        } catch (\Throwable $th) {
            return -1;
        }
    }
    
    /**
     * 读取商家上传素材的总时长
    */
    public static function getMediaTimesByCustomId($custom_id){
        try {
            $count = Db::query('SELECT SUM(media_time) as total_time FROM api_media WHERE custom_id = '.$custom_id);
            return $count;
        } catch (\Throwable $th) {
            return -1;
        }
    }

    /**
     * 创建视频生成规则
    */
    public static function createRule($time,$allMedia,$allAdio,$max_count=5){
        $create_time = 0;
        $rule_str = null;
        while ($create_time < $time) {
            //随机一个视频
            $key = array_rand($allMedia);
            $media = $allMedia[$key];
            unset($allMedia[$key]);
            if ($media['media_type'] == 3) {//图片
                // 如果是图片，设置3s显示时长
                if (empty($rule_str)) {
                    $rule_str = $media['id'].'_0-3';
                }else{
                    //从0s开始 长度3秒
                    $rule_str = $rule_str.'|'.$media['id'].'_0-3';
                }
                $create_time += 3;
            }else {
                //该视频的时长
                $media_time = $media['media_time'];
                if ($media_time < 3) {
                    if (empty($rule_str)) {
                        $rule_str = $media['id'] . '_0-' . $media_time;
                    } else {
                        //从0s开始 长度3秒
                        $rule_str = $rule_str . '|' . $media['id'] . '_0-' . $media_time;
                    }
                    $create_time += $media_time;
                    continue;
                }
                //可以从哪开始的计算
                $start_time_off = $media_time-3;
                $start_time = mt_rand(0,$start_time_off);
                if (empty($rule_str)) {
                    $rule_str = $media['id'].'_'.$start_time.'-3';
                }else{
                    //从0s开始 长度3秒
                    $rule_str = $rule_str.'|'.$media['id'].'_'.$start_time.'-3';
                }
                $create_time += 3;
            }
            if (empty($allMedia)) {
                break;
            }
        }
        //判断该规则的视频存不存在
        $rule = $time.'|'.$rule_str;
        $media_resource = Db::name('api_media_resource')->where('composite_role',$rule)->find();
        if (empty($media_resource)) {//数据库不存在该合成规则
            return $rule_str;
        }else{
            if ($max_count == 0) {
                return null;
            }
            return MediaModel::createRule($time,$allMedia,$allAdio,--$max_count);
        }
    }

    /**
     * 取出资源
    */
    public static function getSource($source_id,$sources){
        foreach ($sources as $key => $value) {
            if ($value['id'] == $source_id) {
                return $value;
            }
        }
        return null;
    }

    /**
     * 合成视频
     * 活动编号
     * 合成数量
     * 商户编号
    */
    public static function createMedia($active_id,$collect_count,$custom_id,$time_rang,$vedio_title,$width=null, $height=null){
        $cred = new Credential(TX_SECRET_ID, TX_SECRET_KEY);
        $httpProfile = new HttpProfile();
        $httpProfile->setEndpoint("vod.tencentcloudapi.com");
        $clientProfile = new ClientProfile();
        $clientProfile->setHttpProfile($httpProfile);
        $client = new VodClient($cred, "", $clientProfile);

        // 第一步读取商家对应的素材
        $allMedia = MediaModel::getMediaByCustomId($custom_id);
        $allAdio = MediaModel::getAedioByCustomId($custom_id);
        $total_time = MediaModel::getMediaTimesByCustomId($custom_id);
        //测试关闭
        // if ($total_time < 50) {
        //     return null;
        // }
        //生成读取规则
        //生成视频的时长
        $time_rangs = explode('-', $time_rang);
        $dbList = array();
        for($i=0;$i<$collect_count;$i++){
            $time = mt_rand((int)$time_rangs[0], (int)$time_rangs[1]);
            //取出对应规则的视频片段
            $rule_str = MediaModel::createRule($time,$allMedia,$allAdio,10);
            if (empty($rule_str)) {
                return -1001;//提示视频消耗完成，请上传更多视频
            }
            //拼接合成视频的参数
            $ary = explode('|',$rule_str);
            $trackItems = array();
            $count = 1;
            foreach ($ary as $key => $value) {
                $values = explode('_',$value);
                //资源ID
                $source_id = $values[0];
                $source_info = MediaModel::getSource($source_id,$allMedia);
                //时间范围
                $time_rang = explode('-',$values[1]);
                $start_time = $time_rang[0];
                $duration = $time_rang[1];
                $trackItem = array();
                if (!empty($width) && !empty($height)) {
                    $trackItem = array(
                        "Type" => "Video",
                        "VideoItem" => array(
                            "SourceMedia" => $source_info['media_id'],
                            "SourceMediaStartTime" => (int)$start_time,
                            "Duration" => (int)$duration,
                            "Width" => $width . 'px',
                            "Height" => $height . 'px',
                            "AudioOperations" => array(
                                array(
                                    "Type" => "Volume",
                                    "VolumeParam" => array(
                                        "Mute" => 1
                                    )
                                )
                            )
                        )
                    );
                }else{
                    $trackItem = array(
                        "Type" => "Video",
                        "VideoItem" => array(
                            "SourceMedia" => $source_info['media_id'],
                            "SourceMediaStartTime" => (int)$start_time,
                            "Duration" => (int)$duration,
                            "AudioOperations" => array(
                                array(
                                    "Type" => "Volume",
                                    "VolumeParam" => array(
                                        "Mute" => 1
                                    )
                                )
                            )
                        )
                    );
                }
                array_push($trackItems,$trackItem);

                if ($count != count($ary)) {
                    $transition = array(
                        "Type" => "Transition",
                        "TransitionItem" => array(
                            "Duration" => 1,
                            "Transitions" => array(
                                array(
                                    "Type" => "ImageFadeInFadeOut"
                                )
                            )
                        )
                    );
                    array_push($trackItems,$transition);
                }
                ++$count;
            }

            $rule_str = $time.'|'.$rule_str;
            $req = new ComposeMediaRequest();
            $params = array(
                "Tracks" => array(
                    array(
                        "Type" => "Video",
                        "TrackItems" => $trackItems
                    )
                ),
                "Output" => array(
                    "FileName" => $active_id.'|'.$rule_str,
                    "Description" => "视频描述"
                )
            );
            $req->fromJsonString(json_encode($params));
            try {
                $resp = $client->ComposeMedia($req);

                //上传数据库等待回调
                array_push($dbList, [
                    'task_id' => $resp->TaskId, 'request_id' => $resp->RequestId, 'status' => 3, 'composite_role' => $rule_str,
                    'active_id' => $active_id, 'create_at' => time(), 'width' => $width, 'height' => $height,
                    'custom_id' => $custom_id, 'vedio_title' => $vedio_title,
                    'vedio_time' => $time - count($ary) + 1
                ]);
            } catch (\Throwable $th) {
                return -1002;
            }
        }

        if (Db::name('api_media_resource')->insertAll($dbList) !== false) {
            MediaModel::pullEventsRequest();
            return 1;
        }
        return -1;
    }


    /**
     * 视频合成事件拉取
     */
    public static function pullEventsRequest()
    {
        $cred = new Credential(TX_SECRET_ID, TX_SECRET_KEY);
        $httpProfile = new HttpProfile();
        $httpProfile->setEndpoint("vod.tencentcloudapi.com");

        $clientProfile = new ClientProfile();
        $clientProfile->setHttpProfile($httpProfile);
        $client = new VodClient($cred, "", $clientProfile);

        $req = new PullEventsRequest();
        $resp = [];
        try {
            $params = array();
            $req->fromJsonString(json_encode($params));

            $resp = $client->PullEvents($req);
        } catch (\Throwable $th) {
            return;
        }

        $updates = array();
        $confirms = array();
        $otherHandles = array();
        foreach ($resp->EventSet as $key => $value) {
            if ($value->EventType == 'ComposeMediaComplete') {
                $composeMediaCompleteEvent = $value->ComposeMediaCompleteEvent;
                $outPut = $composeMediaCompleteEvent->Output;
                $fileId = $outPut->FileId;
                $fileUrl = $outPut->FileUrl;
                $taskId = $composeMediaCompleteEvent->TaskId;

                $data['video_url'] = "'$fileUrl'";
                $data['video_id'] = "'$fileId'";
                $data['task_id'] = "'$taskId'";

                array_push($updates, $data);
                $handle = $value->EventHandle;
                array_push($confirms, $handle);
            }else{
                $handle = $value->EventHandle;
                array_push($otherHandles, $handle);
            }
        }
        if (!empty($otherHandles)) {
            try {
                $otherconfim = new ConfirmEventsRequest();
                $otherHandles = array(
                    "EventHandles" => $otherHandles
                );
                $otherconfim->fromJsonString(json_encode($otherHandles));
                $client->ConfirmEvents($otherconfim);
                echo '更新其他事件';
            } catch (TencentCloudSDKException $e) {
                echo '更新其他事件失败';
                echo $e;
            }
        }
        if (!empty($confirms)) {
            try {
                $confirm = new ConfirmEventsRequest();

                $task_ids = array();
                $sql = "UPDATE api_media_resource SET video_url = CASE task_id ";
                foreach ($updates as $id => $value) {
                    array_push($task_ids, $value['task_id']);
                    $video_url = $value['video_url'];
                    $task_id = $value['task_id'];
                    $sql .= sprintf("WHEN %s THEN %s ", $task_id, $video_url);
                }
                $ids = implode(',', $task_ids);
                $sql .= "END, video_id = CASE task_id ";
                foreach ($updates as $id => $value) {
                    $video_id = $value['video_id'];
                    $task_id = $value['task_id'];
                    $sql .= sprintf("WHEN %s THEN %s ", $task_id, $video_id);
                }
                $sql .= "END WHERE task_id IN ($ids)";
                // 更新数据库
                if (Db::query($sql) !== false) {
                    $handles = array(
                        "EventHandles" => $confirms
                    );
                    $confirm->fromJsonString(json_encode($handles));
                    $client->ConfirmEvents($confirm);
                }
            } catch (TencentCloudSDKException $e) {
                echo $e;
            }
        }
    }

    /**
     * 获取视频记录
    */
    public static function getVedioRecrodList($status = null,$active_id = null){
        if (!empty($status)) {
            $where[] = ['status', '=', $status];
        }
        if (!empty($active_id) && $active_id != 0) {
            $where[] = ['active_id', '=', $active_id];
        }

        if (!empty($where)) {
            return Db::name('api_media_resource')->where($where)->order('create_at', 'desc')->select();
        }else{
            return Db::name('api_media_resource')->order('create_at', 'desc')->select();
        }
    }

    /**
     * 同步视频进度
     */
    public static function getVedioUploadPress($status = null)
    {
        MediaModel::pullEventsRequest();
    }


    /**
     * 获取素材列表
    */
    public static function getAllBaseVedioList($custom_id,$limit = 10){
        $list = Db::name('api_media')
            ->field('*')
            ->where('custom_id', $custom_id)
            ->order('create_at', 'desc')
            ->paginate($limit);
        return $list;
    }

    /**
     * 删除素材
     */
    public static function deleteBaseVedio($id, $custom_id){
        if (!empty($id)) {
            try {
                return Db::name('api_media')->where('custom_id', $custom_id)->where('id',$id)->delete() !== false;
            } catch (\Throwable $th) {
                return false;
            }
        }
        return false;
    }
}
