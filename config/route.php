<?php
/**
 * This file is part of webman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author    walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link      http://www.workerman.net/
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */

use Webman\Route;

// 安装向导
Route::any('/install', [app\admin\controller\InstallController::class, 'index']);
Route::group('/admin/install', function () {
    Route::get('/', [app\admin\controller\InstallController::class, 'index']);
    Route::get('/step', [app\admin\controller\InstallController::class, 'step']);
    Route::get('/checkEnv', [app\admin\controller\InstallController::class, 'checkEnv']);
    Route::post('/testDb', [app\admin\controller\InstallController::class, 'testDb']);
    Route::post('/install', [app\admin\controller\InstallController::class, 'install']);
    Route::get('/checkInstall', [app\admin\controller\InstallController::class, 'checkInstall']);
});
// Nginx配置测试
Route::group('/admin', function () {
    Route::get('/nginxTest/admin', [app\admin\controller\UserController::class, 'nginxTest']);
    Route::get('/nginxTest/wechat', [app\wechat\controller\IndexController::class, 'nginxTest']);
    Route::get('/nginxTest/openapi', [app\wechat\controller\OpenApiController::class, 'nginxTest']);
});

// 发起微信授权
Route::any('/wechat/authorizer/{url}', [app\wechat\controller\IndexController::class, 'authorizer'])->name('wechat.authorizer');
// 微信授权事件、消息与事件通知回调
Route::any('/wechat[/{appid}]', [app\wechat\controller\IndexController::class, 'index']);

// openapi
Route::group('/openapi', function () {
    Route::get('/getToken', [app\wechat\controller\OpenApiController::class, 'getToken']);
})->middleware(
    app\wechat\middleware\OpenApiMiddleware::class
);
