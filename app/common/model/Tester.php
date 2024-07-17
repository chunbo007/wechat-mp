<?php

namespace app\common\model;

/**
 * 体验者管理
 */
class Tester extends BaseModel
{
    static protected array $column = [
        ['key' => 'authorizer_appid', 'name' => 'authorizer_appid'],
    ];
    protected $table = 'tester';
}