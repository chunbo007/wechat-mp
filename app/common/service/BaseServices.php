<?php

namespace app\common\service;

use app\admin\model\Platform;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use Tinywan\ExceptionHandler\Exception\BadRequestHttpException;

class BaseServices
{
    /**
     * @param $platform_id |platform id 获 platform app_id
     * @return Platform|array|mixed|
     * @throws BadRequestHttpException
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function getPlatformParams($platform_id)
    {
        $platform = Platform::where('id', $platform_id)->whereOr('app_id', $platform_id)->find();
        if (!$platform) throw new BadRequestHttpException('平台不存在');
        return $platform;
    }
}