<?php

namespace app\common\model;

/**
 * 开放平台参数
 */
class Platform extends BaseModel
{
    static protected array $column = [
        ['key' => 'name', 'name' => '公司名称'],
        ['key' => 'app_id', 'name' => 'app_id'],
    ];
    protected $table = 'platform';
}