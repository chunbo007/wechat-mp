<?php

namespace app\admin\model;

use app\common\model\Authorizers as AuthorizersModel;
use app\common\service\wechat\MiniProgram;
use Psr\SimpleCache\InvalidArgumentException;
use support\Request;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use Tinywan\ExceptionHandler\Exception\BadRequestHttpException;

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
    public static function list(Request $request, $field = '*'): array
    {
        $params = self::buildWhere($request);
        return self::where($params['where'])
            ->field($field)
            ->order('auth_time', 'desc')
            ->paginate([
                'page' => $params['current_page'],
                'list_rows' => $params['page_size']
            ])->toArray();
    }

    /**
     * @throws DataNotFoundException
     * @throws ModelNotFoundException
     * @throws DbException
     * @throws BadRequestHttpException
     * @throws InvalidArgumentException
     */
    public function getMiniProgram(Request $request): array
    {
        $platform_id = $request->post('platform_id');
        $miniProgram = new MiniProgram($platform_id);
        $list = self::list($request);
        foreach ($list['data'] as &$item) {
            $visit_status = $miniProgram->getVisitStatus($item['appid']);
            $item['visit_status'] = $visit_status['status'] ?? '';
            $version_info = $miniProgram->getversioninfo($item['appid']);
            $item['exp_info'] = $version_info['exp_info'] ?? '';
            $item['release_info'] = $version_info['release_info'] ?? '';
        }
        return $list;
    }
}