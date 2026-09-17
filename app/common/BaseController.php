<?php /** @noinspection DuplicatedCode */

namespace app\common;

use plugin\saiadmin\exception\ApiException;
use support\exception\BusinessException;
use support\think\Db;
use support\Request;
use support\Response;

class BaseController extends \plugin\saiadmin\basic\BaseController
{
    protected array $userIds = [];

    /**
     * 当前应用
     * @var 
     */
    protected $authorizer;

    public function __construct()
    {
        parent::__construct();
        $this->checkScope();
    }

    public function save(Request $request) : Response
    {
        filter_tester($this->adminId);
        return parent::save($request);
    }

    /**
     * 修改数据
     * @param $id
     * @param Request $request
     * @return Response
     */
    public function update(Request $request, $id) : Response
    {
        filter_tester($this->adminId);
        
        $id = $request->input('id', $id);
        $data = $request->post();
        if ($this->validate) {
            if (!$this->validate->scene('update')->check($data)) {
                return $this->fail($this->validate->getError());
            }
        }
        $info = $this->logic->find($id);
        if (!$info) {
            return $this->fail('没有找到该数据');
        }
        $result = $this->logic->update($data, [$this->pk => $id]);
        if ($result) {
            $this->afterChange('update');
            return $this->success('操作成功');
        } else {
            return $this->fail('操作失败');
        }
    }

    /**
     * 修改状态
     * @param Request $request
     * @return Response
     */
    public function changeStatus(Request $request) : Response
    {
        filter_tester($this->adminId);
        
        $id = $request->input('id', '');
        $status = $request->input('status', 1);
        $result = $this->logic->where($this->pk, $id)->update(['status' => $status]);
        if ($result) {
            $this->afterChange('changeStatus');
            return $this->success('操作成功');
        } else {
            return $this->fail('操作失败');
        }
    }

    /**
     * 删除数据
     * @param Request $request
     * @return Response
     */
    public function destroy(Request $request) : Response
    {
        filter_tester($this->adminId);
        
        $ids = $request->input('ids', '');
        if (!empty($ids)) {
            $this->logic->destroy($ids);
            $this->afterChange('destroy');
            return $this->success('操作成功');
        } else {
            return $this->fail('参数错误，请检查');
        }
    }

    /**
     * 读取数据
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function read(Request $request, $id) : Response
    {
        
        $id = $request->input('id', $id);
        $model = $this->logic->find($id);
        if ($model) {
            $data = is_array($model) ? $model : $model->toArray();
            return $this->success($data);
        } else {
            return $this->fail('未查找到信息');
        }
    }

    /**
     * 恢复数据
     * @param Request $request
     * @return Response
     */
    public function recovery(Request $request) : Response
    {
        filter_tester($this->adminId);
        
        $ids = $request->input('ids', '');
        if (!empty($ids)) {
            $this->logic->restore($ids);
            $this->afterChange('recovery');
            return $this->success('恢复成功');
        } else {
            return $this->fail('参数错误，请检查');
        }
    }

    /**
     * 数据销毁-真实删除
     * @param Request $request
     * @return Response
     */
    public function realDestroy(Request $request) : Response
    {
        filter_tester($this->adminId);
        
        $ids = $request->input('ids', '');
        if (!empty($ids)) {
            $this->logic->destroy($ids, true);
            $this->afterChange('realDestroy');
            return $this->success('操作成功');
        } else {
            return $this->fail('参数错误，请检查');
        }
    }

    /**
     * 数据权限检查
     * 检查传入的 id 或 ids 是否在当前用户的数据权限范围内
     * 只返回用户有权限访问的数据ID
     */
    public function checkScope(): void
    {
        $id = request()->input('id');
        $ids = request()->input('ids');
        
        // 检查 id 和 ids 至少有一个有效值
        if (
            (empty($id) || $id === 'undefined' || $id === 'null') && 
            (empty($ids) || $ids === 'undefined' || $ids === 'null')
        ) {
            return;
        }

        if (!$this->adminInfo) {
            throw new ApiException('数据权限读取失败');
        }

        foreach ($this->adminInfo['roleList'] as $role) {
            $data_scope = $role['data_scope'];
            $role_id = $role['id'];
            switch ($data_scope) {
                case $this->logic::ALL_SCOPE:
                    return;
                case $this->logic::CUSTOM_SCOPE:
                    $deptIds = Db::table('eb_system_role_dept')->where('role_id', $role_id)->column('dept_id');
                    $userIds = Db::table('eb_system_user')->where('dept_id', 'in', $deptIds)->column('id');
                    $this->userIds = array_merge($this->userIds, $userIds);
                    break;
                case $this->logic::SELF_DEPT_SCOPE:
                    $deptId = $this->adminInfo['dept_id'];
                    $userIds = Db::table('eb_system_user')->where('dept_id', $deptId)->column('id');
                    $this->userIds = array_merge($this->userIds, $userIds);
                    break;
                case $this->logic::DEPT_BELOW_SCOPE:
                    $deptId = $this->adminInfo['dept_id'];
                    $deptInfo = Db::table('eb_system_dept')->where('id', $deptId)->find();
                    $level = $deptInfo['level'].",".$deptId;
                    $deptIds = Db::table('eb_system_dept')->where(function ($query) use($level) {
                        $query->where('level', $level)
                            ->whereOr('level', 'like', $level . ',%');
                    })->column('id');
                    $deptIds[] = $deptId;
                    $userIds = Db::table('eb_system_user')->where('dept_id', 'in', $deptIds)->column('id');
                    $this->userIds = array_merge($this->userIds, $userIds);
                    break;
                case $this->logic::SELF_SCOPE:
                    $this->userIds = array_merge($this->userIds, [$this->adminInfo['id']]);
                    break;
                default:
                    break;
            }
        }

        // 根据 created_by 过滤,只查询当前用户有权限的数据
        $query = $this->logic->whereIn('created_by', array_unique($this->userIds));
        $paramField = '';
        $newParams = [];
        
        if (!is_null($id)) {
            $paramField = 'id';
            $newParams = $query->field($this->pk)->where($this->pk, $id)->value($this->pk);
        } elseif (!is_null($ids)) {
            $paramField = 'ids';
            $newParams = $query->field($this->pk)->whereIn($this->pk, $ids)->column($this->pk);
        }
        
        if (empty($newParams)){
            throw new BusinessException('数据不存在');
        }
        
        // 根据原始参数来源设置到对应位置
        if (!is_null(request()->get($paramField))){
            request()->setGet($paramField, $newParams);
        } else {
            request()->setPost($paramField, $newParams);
        }
    }

}