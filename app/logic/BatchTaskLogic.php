<?php
// +----------------------------------------------------------------------
// | saiadmin [ saiadmin快速开发框架 ]
// +----------------------------------------------------------------------
// | Author: your name
// +----------------------------------------------------------------------
namespace app\logic;

use app\common\BaseLogic;
use app\model\BatchTask;
use app\model\BatchTaskItem;

/**
 * 批量任务逻辑层
 */
class BatchTaskLogic extends BaseLogic
{
    /**
     * 构造函数
     */
    public function __construct()
    {
        $this->model = new BatchTask();
        $this->scope = true; // 启用数据权限控制
    }

    /**
     * 刷新批量任务进度
     */
    public function refreshBatchTask($taskId)
    {
        $task = $this->model->find($taskId);
        
        if (!$task) {
            throw new \Exception('任务不存在');
        }
        
        $itemModel = new BatchTaskItem();
        
        // 统计进度
        $total = $itemModel->where('task_id', $taskId)->count();
        $progress = $itemModel->where('task_id', $taskId)
            ->where('status', '>', 0)
            ->count();
        $successCount = $itemModel->where('task_id', $taskId)
            ->where('status', 1)
            ->count();
        $failCount = $itemModel->where('task_id', $taskId)
            ->where('status', 2)
            ->count();
        
        // 更新任务状态
        $updateData = [
            'progress' => $progress,
            'success_count' => $successCount,
            'fail_count' => $failCount,
            'update_time' => date('Y-m-d H:i:s')
        ];
        
        // 判断任务是否完成
        if ($progress >= $total && $total > 0) {
            $updateData['status'] = $failCount > 0 ? 3 : 2; // 有失败则为失败，否则为完成
        }
        
        $this->model->where('id', $taskId)->update($updateData);
        
        return true;
    }
} 