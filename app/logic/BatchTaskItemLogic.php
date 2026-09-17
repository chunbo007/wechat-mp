<?php
// +----------------------------------------------------------------------
// | saiadmin [ saiadmin快速开发框架 ]
// +----------------------------------------------------------------------
// | Author: your name
// +----------------------------------------------------------------------
namespace app\logic;

use app\common\BaseLogic;
use app\model\BatchTaskItem;

/**
 * 批量任务逻辑层
 */
class BatchTaskItemLogic extends BaseLogic
{
    /**
     * 构造函数
     */
    public function __construct()
    {
        $this->model = new BatchTaskItem();
        $this->scope = true; // 启用数据权限控制
    }
} 