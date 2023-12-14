<?php

namespace app\admin\controller;

use app\admin\model\WxcallbackBiz;
use app\admin\model\WxcallbackComponent;
use support\Request;
use support\Response;
use think\db\exception\DbException;

class MessageController extends BaseController
{
    public function EventList(Request $request)
    {
        return success(WxcallbackBiz::list($request));
    }

    /**
     * @param Request $request
     * @return array|Response
     * @throws DbException
     */
    public function list(Request $request)
    {
        return success(WxcallbackComponent::list($request));
    }
}