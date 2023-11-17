<?php

namespace app\controller;
use support\Request;
use EasyWeChat\Factory;
use app\service\wechat\OpenPlatform;
class IndexController
{
    public function index(Request $request)
    {
        $xml = $request->rawBody();
        $xml = simplexml_load_string($xml);
        if (empty($xml)) return '请求体为空';
        $appId = (string)$xml->AppId;
        $openPlatformConfig = config("wechat.open_platform.$appId");
        $app =  new OpenPlatform($openPlatformConfig);
        return $app->handle($request);
      }

    public function view(Request $request)
    {
//        $cache = $this->getCache()->set('easywechat.open_platform.verify_ticket.wx3a67b967164b59d1', '', 3600);
//        return $this->getCache()->get('easywechat.open_platform.verify_ticket.wx3a67b967164b59d1');
//        if (!$this->getCache()->has('testcache')) {
//            return 'cache failed';
//        }
//        echo json_encode($this->getCache(),true);
//        return 123;

        $config = [
            'app_id'   => 'wx3a67b967164b59d1',
            'secret'   => '604cf7409d1ace34a1fb8226cef89c30',
            'token'    => 'chunboli888',
            'aes_key'  => 'rvEuQLD5QqVqWQeHHTMvxIW8GtOBLIhVzZh6uGL2Cqr'
        ];
        $openPlatform = Factory::openPlatform($config);
        $Authorizers = $openPlatform->getAuthorizer('wx15d93c0b30202ad5');
        return json($Authorizers);
        return view('index/view', ['name' => 'webman']);
    }

    public function json(Request $request)
    {
        return json(['code' => 0, 'msg' => 'ok']);
    }

}
