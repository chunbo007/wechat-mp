<?php

namespace app\admin\model;

use app\common\model\WxcallbackForward as WxcallbackForwardModel;
use support\Request;
use think\db\exception\DbException;

/**
 * @property mixed $id
 */
class WxcallbackForward extends WxcallbackForwardModel
{
    /**
     * @param Request $request
     * @return array
     * @throws DbException
     */
    public static function list(Request $request): array
    {
        $params = self::buildWhere($request);
        return self::where($params['where'])
            ->order('id', 'desc')
            ->paginate([
                'page' => $params['current_page'],
                'list_rows' => $params['page_size']
            ])->toArray();
    }
}