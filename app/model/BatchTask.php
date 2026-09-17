<?php
// +----------------------------------------------------------------------
// | saiadmin [ saiadmin快速开发框架 ]
// +----------------------------------------------------------------------
// | Author: your name
// +----------------------------------------------------------------------
namespace app\model;

use app\common\BaseModel;

/**
 * 批量任务模型
 */
class BatchTask extends BaseModel
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
    protected $table = 'system_batch_task';

    /**
     * 可填充字段
     * @var array
     */
    protected $fillable = [
        'title',
        'type',
        'status',
        'total',
        'progress',
        'success_count',
        'fail_count',
        'create_time',
        'update_time'
    ];

    /**
     * 状态常量
     */
    const STATUS_PENDING = 0;    // 待处理
    const STATUS_PROCESSING = 1; // 处理中
    const STATUS_SUCCESS = 2;    // 完成
    const STATUS_FAILED = 3;     // 失败

    /**
     * 类型常量
     */
    const TYPE_SERVER_DOMAIN = 'server_domain';
    const TYPE_WEBVIEW_DOMAIN = 'webview_domain';
    const TYPE_CATEGORY = 'category';

    /**
     * 任务类型 搜索
     */
    public function searchTypeAttr($query, $value)
    {
        $query->where('type', 'like', '%'.$value.'%');
    }

    /**
     * 任务标题 搜索
     */
    public function searchTitleAttr($query, $value)
    {
        $query->where('title', 'like', '%'.$value.'%');
    }

    /**
     * 状态 搜索
     */
    public function searchStatusAttr($query, $value)
    {
        $query->where('status', $value);
    }

    /**
     * 关联任务明细
     */
    public function items()
    {
        return $this->hasMany(BatchTaskItem::class, 'task_id', 'id');
    }
} 