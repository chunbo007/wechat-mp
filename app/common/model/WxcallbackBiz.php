<?php

namespace app\common\model;

/**
 * 消息与事件通知记录
 */
class WxcallbackBiz extends BaseModel
{
    static protected array $column = [
        ['key' => 'appid', 'name' => 'appid'],
        ['key' => 'msgtype', 'name' => 'msgtype'],
        ['key' => 'event', 'name' => 'event'],
        ['key' => 'postbody', 'name' => 'postbody', 'where' => 'like'],
        ['key' => 'receivetime', 'name' => 'receivetime', 'where' => 'time_range'],
    ];
    protected $table = 'wxcallback_biz';
}