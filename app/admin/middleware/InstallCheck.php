<?php

namespace app\admin\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class InstallCheck implements MiddlewareInterface
{
    public function process(Request $request, callable $next): Response
    {
        $installLockFile = base_path() . '/runtime/install.lock';
        $path = $request->path();

        $ignorePaths = [
            '/install',
            '/admin/install',
            '/admin/install/checkEnv',
            '/admin/install/testDb',
            '/admin/install/install',
            '/admin/install/step',
            '/admin/install/index',
            '/wechat',
            '/openapi'
        ];

        $isInstallPath = false;
        foreach ($ignorePaths as $ignorePath) {
            if (strpos($path, $ignorePath) === 0) {
                $isInstallPath = true;
                break;
            }
        }

        if (!file_exists($installLockFile) && !$isInstallPath) {
            return redirect('/install');
        }

        if (file_exists($installLockFile) && $path === '/install') {
            return redirect('/admin/login');
        }

        return $next($request);
    }
}
