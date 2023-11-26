<?php

namespace app\common\model;

/**
 * 开放平台参数
 */
class Authorizers extends BaseModel
{
    static protected array $column = [
        ['key' => 'name', 'name' => '公司名称'],
        ['key' => 'app_id', 'name' => 'app_id'],
    ];
    protected $table = 'authorizers';

    public function getAuthTimeAttr($value)
    {
        return date('Y-m-d H:i:s', $value);
    }
}