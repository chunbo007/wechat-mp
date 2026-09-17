<?php
// +----------------------------------------------------------------------
// | saiadmin [ saiadmin快速开发框架 ]
// +----------------------------------------------------------------------
// | Author: your name
// +----------------------------------------------------------------------
namespace app\model;

use app\common\BaseModel;

/**
 * 批量任务明细模型
 */
class BatchTaskItem extends BaseModel
{
    /**
     * 数据表主键
     * @var string
     */
    protected $pk = 'id';

    /**
     * 数据库表名称
     * @var string
     */
    protected $table = 'system_batch_task_item';

    /**
     * 可填充字段
     * @var array
     */
    protected $fillable = [
        'task_id',
        'target_id',
        'target_name',
        'status',
        'message',
        'create_time',
        'update_time'
    ];

    /**
     * 状态常量
     */
    const STATUS_PENDING = 0;  // 待处理
    const STATUS_SUCCESS = 1;  // 成功
    const STATUS_FAILED = 2;   // 失败

    /**
     * 任务ID 搜索
     */
    public function searchTaskIdAttr($query, $value)
    {
        $query->where('task_id', $value);
    }

    /**
     * 目标应用ID 搜索
     */
    public function searchTargetIdAttr($query, $value)
    {
        $query->where('target_id', 'like', '%'.$value.'%');
    }

    /**
     * 目标应用名称 搜索
     */
    public function searchTargetNameAttr($query, $value)
    {
        $query->where('target_name', 'like', '%'.$value.'%');
    }

    /**
     * 状态 搜索
     */
    public function searchStatusAttr($query, $value)
    {
        $query->where('status', $value);
    }

    /**
     * 关联主任务
     */
    public function task()
    {
        return $this->belongsTo(BatchTask::class, 'task_id', 'id');
    }
} 