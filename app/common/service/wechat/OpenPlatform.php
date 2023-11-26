<?php
namespace app\common\service\wechat;

use app\common\service\BaseServices;
use EasyWeChat\Factory;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class OpenPlatform extends BaseServices {
    public $app;

    public function __construct($config)
    {
        $this->app = Factory::openPlatform($config);
    }

    public function handle($request)
    {
        try {
            $symfony_request = new SymfonyRequest($request->get(), $request->post(), [], $request->cookie(), [], [], $request->rawBody());
            $symfony_request->headers = new HeaderBag($request->header());
            $this->app->rebind('request', $symfony_request);
            $response = $this->app->server->serve();
            return $response->getContent();
        } catch (\Exception $e){
            return $e->getMessage();
        }
    }
}