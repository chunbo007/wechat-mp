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
