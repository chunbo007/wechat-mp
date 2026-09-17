<?php
// +----------------------------------------------------------------------
// | saiadmin [ saiadmin快速开发框架 ]
// +----------------------------------------------------------------------
// | Author: your name
// +----------------------------------------------------------------------
namespace app\controller;

use app\common\BaseController;
use app\logic\BatchTaskLogic;
use app\logic\BatchTaskItemLogic;
use support\Request;
use support\Response;

/**
 * 批量任务控制器
 */
class BatchTaskController extends BaseController
{
    /**
     * 构造函数
     */
    public function __construct()
    {
        $this->logic = new BatchTaskLogic();
        parent::__construct();
    }

    /**
     * 获取批量任务列表
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $where = $request->more([]);
        $this->logic->setOrderField('id');
        $this->logic->setOrderType('desc');
        $query = $this->logic->search($where);
        $data = $this->logic->getList($query);
        return $this->success($data);
    }

    /**
     * 获取批量任务详情
     * @param Request $request
     * @return Response
     */
    public function detail(Request $request): Response
    {
        $where = $request->more([
            ['task_id', $request->get('task_id')],
        ]);
        $this->logic = new BatchTaskItemLogic();
        $this->init();
        $this->logic->setOrderField('id');
        $this->logic->setOrderType('desc');
        $query = $this->logic->search($where);
        $data = $this->logic->getList($query);
        return $this->success($data);
    }

    /**
     * 刷新批量任务进度
     * @param Request $request
     * @return Response
     */
    public function refresh(Request $request): Response
    {
        $taskId = $request->post('task_id');
        
        if (!$taskId) {
            return $this->fail('任务ID不能为空');
        }
        
        $result = $this->logic->refreshBatchTask($taskId);
        
        return $this->success($result, '刷新成功');
    }
} 