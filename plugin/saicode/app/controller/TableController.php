<?php
// +----------------------------------------------------------------------
// | saithink [ saithink快速开发框架 ]
// +----------------------------------------------------------------------
// | Author: sai <1430792918@qq.com>
// +----------------------------------------------------------------------
namespace plugin\saicode\app\controller;

use support\Request;
use support\Response;
use plugin\saiadmin\basic\BaseController;
use plugin\saicode\app\logic\TableLogic;
use plugin\saicode\app\logic\DbLogic;
use plugin\saicode\app\validate\TableValidate;

/**
 * 低代码控制器
 */
class TableController extends BaseController
{

    /**
     * 数据源逻辑层
     */
    public $dbLogic;

    /**
     * 构造
     */
    public function __construct()
    {
        $this->logic = new TableLogic();
        $this->dbLogic = new DbLogic();
        $this->validate = new TableValidate;
        parent::__construct();
    }

    /**
     * 读取系统数据源
     * @return Response
     */
    public function source(): Response
    {
        $data = config('thinkorm.connections');
        $list = [];
        foreach ($data as $k => $v) {
            $list[] = $k;
        }
        return $this->success($list);
    }

    /**
     * 数据源数据表列表
     * @param Request $request
     * @return Response
     */
    public function sourceTable(Request $request): Response
    {
        $where = $request->more([
            ['name', ''],
            ['source', ''],
        ]);
        $data = $this->dbLogic->getList($where);
        return $this->success($data);
    }

    /**
     * 装载数据表
     * @param Request $request
     * @return Response
     */
    public function loadTable(Request $request): Response
    {
        $names = $request->input('names', []);
        $source = $request->input('source', '');
        $this->logic->loadTable($names, $source);
        return $this->success('操作成功');
    }

    /**
     * 数据列表
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $where = $request->more([
            ['table_name', ''],
        ]);
        $query = $this->logic->search($where);
        $data = $this->logic->getList($query);
        return $this->success($data);
    }

    /**
     * 修改数据
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function update(Request $request, $id) : Response
    {
        $data = $request->post();
        if (!$this->validate->scene('update')->check($data)) {
            return $this->fail($this->validate->getError());
        }
        $this->logic->updateTableAndColumns($data);
        return $this->success('修改成功');
    }

    /**
     * 同步数据表字段信息
     * @param Request $request
     * @return Response
     */
    public function sync(Request $request): Response
    {
        $id = $request->input('id');
        $this->logic->sync($id);
        return $this->success('操作成功');
    }

    /**
     * 代码预览
     */
    public function preview(Request $request): Response
    {
        $id = $request->input('id');
        $data = $this->logic->preview($id);
        return $this->success($data);
    }

    /**
     * 代码生成
     */
    public function generate(Request $request)
    {
        $ids = $request->input('ids', '');
        $data = $this->logic->generate($ids);
        return response()->download($data['download'], $data['filename']);
    }

    /**
     * 生成到模块
     */
    public function generateFile(Request $request): Response
    {
        $id = $request->input('id', '');
        $this->logic->generateFile($id);
        return $this->success('操作成功');
    }

    /**
     * 获取数据表字段信息
     * @param Request $request
     * @return Response
     */
    public function getTableColumns(Request $request): Response
    {
        $table_id = $request->input('table_id', '');
        $data = $this->logic->getTableColumns($table_id);
        return $this->success($data);
    }

    public function saveDesign(Request $request): Response
    {
        $table = $request->input('table');
        $columns = $request->input('columns', []);
        $data = [
            'id' => $table['id'],
            'form_width' => $table['form_width'],
            'is_full' => $table['is_full'] === true ? 2 : 1,
            'component_type' => $table['component_type'],
            'span' => $table['form_span'],
        ];
        $this->logic->saveDesign($data, $columns);
        return $this->success('操作成功');
    }

}