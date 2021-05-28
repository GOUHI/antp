<?php

namespace app\agent\model;

use think\facade\Db;
use think\Model;

define('DOUYIN_CLIENT_KEY', 'aw8qsdjvtk5twjne');
define('DOUYIN_CLIENT_SECRET', 'e830612330928dbc117697b216e8c519');
define('DOUYIN_SCOPE', 'user_info,renew_refresh_token,mobile,data.external.item,video.create');
define('DOUYIN_REDIRECT_URI', 'http://www.ailschn.com/activitys');

class DouyinModel extends Model
{
    /**
     * 拼接的授权二维码
    */
    public static function getDouyinAuthUrl($activeId){
        return 'https://open.douyin.com/platform/oauth/connect/?client_key='. DOUYIN_CLIENT_KEY .'&response_type=code&scope='. DOUYIN_SCOPE .'&redirect_uri='. DOUYIN_REDIRECT_URI.'&state='.$activeId;
    }

    /**
     * 获取抖音的accessToken
    */
    public static function getAccessToken($code){
        $url = 'https://open.douyin.com/oauth/access_token/?client_key=' . DOUYIN_CLIENT_KEY . '&client_secret=' . DOUYIN_CLIENT_SECRET . '&code=' . $code . '&grant_type=authorization_code';
        $res = curlGet($url);
        return $res;
    }

    /**
     * 获取抖音用户数据
     */
    public static function getUserInfo($open_id,$access_token){
        $url = 'https://open.douyin.com/oauth/userinfo/?open_id='.$open_id.'&access_token='.$access_token;
        $res = curlGet($url);
        return $res;
    }

    /**
     * 解密手机号
     */
    public static function decrypt($encrypted_mobile){
        $iv = substr(DOUYIN_CLIENT_SECRET, 0, 16);
        return openssl_decrypt($encrypted_mobile, 'aes-256-cbc', DOUYIN_CLIENT_SECRET, 0, $iv);
    }

    /**
     * 上传视频
     */
    public static function uploadVideo($open_id,$access_token,$video){
        $url = 'https://open.douyin.com/video/upload/?open_id='.$open_id.'&access_token='.$access_token;
        $data['video'] = $video;
        $res = curlPost($url,$data,['Content-Type'=>'multipart/form-data']);
        return $res;
    }

    /**
     * 发布视频
     */
    public static function createVideo($open_id,$access_token,$video_id){
        $url = 'https://open.douyin.com/video/create/?open_id='.$open_id.'&access_token='.$access_token;
        $data['video_id'] = $video_id;
        $res = curlPost($url,json_encode($data),['Content-Type'=>'application/json']);
        return $res;
    }
}
