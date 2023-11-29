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
     * @throws DataNotFoundException
     * @throws ModelNotFoundException
     * @throws DbException
     * @throws BadRequestHttpException
     */
    public function getPlatformParams($platform_id)
    {
        $platform = Platform::where('id', $platform_id)->find();
        if (!$platform) throw new BadRequestHttpException('平台不存在');
        return $platform;
    }
}