<?php
// +----------------------------------------------------------------------
// | saithink [ saithink快速开发框架 ]
// +----------------------------------------------------------------------
// | Author: sai <1430792918@qq.com>
// +----------------------------------------------------------------------
namespace plugin\saiadmin\app\controller\system;

use plugin\saiadmin\basic\BaseController;
use plugin\saiadmin\app\logic\system\SystemUploadfileLogic;
use support\Request;
use support\Response;

/**
 * 附件管理控制器
 */
class SystemUploadfileController extends BaseController
{
    /**
     * 构造
     */
    public function __construct()
    {
        $this->logic = new SystemUploadfileLogic();
        parent::__construct();
    }

    /**
     * 数据列表
     * @param Request $request
     * @return Response
     */
    public function index(Request $request) : Response
    {
        $where = $request->more([
            ['origin_name', ''],
            ['storage_mode', ''],
            ['mime_type', ''],
            ['create_time', ''],
        ]);
        $query = $this->logic->search($where);
        $data = $this->logic->getList($query);
        return $this->success($data);
    }

    /**
     * 删除数据
     * @param Request $request
     * @return Response
     */
    public function realDestroy(Request $request) : Response
    {
        $ids = $request->input('ids', '');
        $list = $this->logic->where('id', 'in', $ids)->select()->toarray();

        foreach($list as $item) {
            $realFilePath = parseUrl($item['url']);
            if(file_exists($realFilePath)) {
                unlink($realFilePath);
            }
        }

        if (!empty($ids)) {
            $this->logic->destroy($ids, true);
            $this->afterChange('destroy');
            return $this->success('操作成功');
        } else {
            return $this->fail('参数错误，请检查');
        }
    }

}
