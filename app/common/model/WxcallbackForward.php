<?php

namespace app\common\model;

/**
 * 请求转发记录表
 */
class WxcallbackForward extends BaseModel
{
    static protected array $column = [
        ['key' => 'appid', 'name' => 'appid'],
        ['key' => 'create_time', 'name' => 'create_time', 'where' => 'time_range'],
    ];
    protected $table = 'wxcallback_forward';
}