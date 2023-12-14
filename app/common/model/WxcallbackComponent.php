<?php

namespace app\common\model;

/**
 * 授权事件记录表
 */
class WxcallbackComponent extends BaseModel
{
    static protected array $column = [
        ['key' => 'appid', 'name' => 'appid'],
        ['key' => 'infotype', 'name' => 'infotype'],
        ['key' => 'receivetime', 'name' => 'receivetime', 'where' => 'time_range'],
    ];
    protected $table = 'wxcallback_component';
}