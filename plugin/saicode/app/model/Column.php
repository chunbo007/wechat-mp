<?php

// +----------------------------------------------------------------------
// | saithink [ saithink快速开发框架 ]
// +----------------------------------------------------------------------
// | Author: sai <1430792918@qq.com>
// +----------------------------------------------------------------------
namespace plugin\saicode\app\model;

use plugin\saiadmin\basic\BaseModel;

/**
 * 低代码字段模型
 */
class Column extends BaseModel
{
    /**
     * 数据表主键
     * @var string
     */
    protected $pk = 'id';

    protected $table = 'saicode_column';

    public function getOptionsAttr($value)
    {
        return json_decode($value ?? '', true);
    }

}