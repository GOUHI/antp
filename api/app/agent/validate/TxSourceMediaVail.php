<?php

namespace app\agent\validate;

use think\Validate;

class TxSourceMediaVail extends Validate
{
    protected $rule = [
        'media_id|媒体文件编号' => 'require',
        'media_type|媒体文件类型' => 'require',
        'media_name|媒体文件名称' => 'require',
        'media_url|媒体文件下载地址' => 'require',
        'collect_count|视频合成数量' => 'require',
        'active_id|活动编号' => 'require',
        'vedio_time_rang|时间范围' => 'require',
        'vedio_title|视频标题' => 'require',
    ];

    protected $scene = [
        'uploadMedia' => ['media_id', 'medil_type', 'media_name', 'media_url'],
        'mediaCollect' => ['active_id','collect_count', 'vedio_time_rang', 'vedio_title']
    ];
}
