<?php

namespace app\common\model;

/**
 * 开放平台参数
 */
class Authorizers extends BaseModel
{
    static protected array $column = [
        ['key' => 'nick_name', 'name' => '公司名称'],
        ['key' => 'appid', 'name' => 'app_id'],
        ['key' => 'platform_id', 'name' => 'platform_id'],
    ];
    protected $table = 'authorizers';

    public function getAuthTimeAttr($value)
    {
        return date('Y-m-d H:i:s', $value);
    }

    public function platform(): \think\model\relation\BelongsTo
    {
        return $this->belongsTo('\app\common\model\Platform', 'platform_id', 'id');
    }
}