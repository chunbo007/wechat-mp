<?php

namespace app\admin\model;

use app\common\model\Authorizers as AuthorizersModel;
use support\Request;
use think\db\exception\DbException;

/**
 * @property mixed $id
 */
class Authorizers extends AuthorizersModel
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
            ->order('auth_time', 'desc')
            ->paginate([
                'page' => $params['current_page'],
                'list_rows' => $params['page_size']
            ])->toArray();
    }
}