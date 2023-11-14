<?php

namespace app\controller;
use support\Request;
use EasyWeChat\Factory;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
class IndexController
{
    public function index(Request $request)
    {
        try {
            $config = [
                'app_id'   => 'wx3a67b967164b59d1',
                'secret'   => '604cf7409d1ace34a1fb8226cef89c30',
                'token'    => 'chunboli888',
                'aes_key'  => 'rvEuQLD5QqVqWQeHHTMvxIW8GtOBLIhVzZh6uGL2Cqr',
            ];
            $openPlatform = Factory::openPlatform($config);

            $symfony_request = new SymfonyRequest($request->get(), $request->post(), [], $request->cookie(), [], [], $request->rawBody());
            $symfony_request->headers = new HeaderBag($request->header());
            $openPlatform->rebind('request', $symfony_request);
            $response = $openPlatform->server->serve();

//            // 处理授权事件
//            $openPlatform->push(function ($message) {
//                var_dump($message);
//            },Guard::EVENT_COMPONENT_VERIFY_TICKET);
//            $server->serve();

            return $response->getContent();
        } catch (\Exception $e){
            var_dump($e->getMessage());
        }

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
        $url = $openPlatform->getAuthorizers();
        return view('index/view', ['name' => 'webman']);
    }

    public function json(Request $request)
    {
        return json(['code' => 0, 'msg' => 'ok']);
    }

}
