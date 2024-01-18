<?php

namespace app\admin\model;

use app\common\model\Platform as PlatformModel;
use support\Request;
use think\db\exception\DbException;
use Tinywan\ExceptionHandler\Exception\BadRequestHttpException;

/**
 * @property mixed $id
 */
class Platform extends PlatformModel
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

    /**
     * @throws BadRequestHttpException
     */
    public static function add($data)
    {
        try {
            if ($data['is_default']) {
                self::where('is_default', 1)->update(['is_default' => 0]);
            }
            self::create($data);
        } catch (\Exception $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
    }

    /**
     * @throws BadRequestHttpException
     */
    public static function edit($data)
    {
        try {
            if ($data['is_default']) {
                self::where('is_default', 1)->update(['is_default' => 0]);
            }
            if ($data['return_forward_platform']) {
                self::where('return_forward_platform', 1)->update(['return_forward_platform' => 0]);
            }
            if ($data['return_forward_app']) {
                self::where('return_forward_app', 1)->update(['return_forward_app' => 0]);
            }
            self::update($data);
        } catch (\Exception $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
    }
}