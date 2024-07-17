<?php

namespace app\admin\model;

use app\common\model\Tester as TesterModel;
use support\Request;
use think\db\exception\DbException;

/**
 * @property mixed $id
 */
class Tester extends TesterModel
{

    public static function list($request): array
    {
        $params = self::buildWhere(null, $request);
        return self::where($params['where'])
            ->order('id', 'desc')
            ->paginate([
                'page' => $params['current_page'],
                'list_rows' => $params['page_size']
            ])->toArray();
    }
}