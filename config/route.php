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
Route::group('/install', function () {
    Route::get('/status', [app\controller\InstallController::class, 'status']);
    Route::get('/check', [app\controller\InstallController::class, 'check']);
    Route::post('/db-test', [app\controller\InstallController::class, 'dbTest']);
    Route::post('/redis-test', [app\controller\InstallController::class, 'redisTest']);
    Route::post('/execute', [app\controller\InstallController::class, 'execute']);
    Route::get('/version-info', [app\controller\InstallController::class, 'getVersionInfo']);
});

// 批量任务
Route::group('/batchTask', function () {
    Route::get('/index', [app\controller\BatchTaskController::class, 'index']);
    Route::get('/detail', [app\controller\BatchTaskController::class, 'detail']);
    Route::post('/refresh', [app\controller\BatchTaskController::class, 'refresh']);
});

// 系统升级
Route::group('/upgrade', function () {
    Route::get('/getPatchList', [app\controller\UpgradeController::class, 'getPatchList']);
    Route::get('/patchInfo', [app\controller\UpgradeController::class, 'getPatchInfo']);
    Route::get('/nextPatchInfo', [app\controller\UpgradeController::class, 'getNextPatchInfo']);
    Route::post('/executeUpgrade', [app\controller\UpgradeController::class, 'executeUpgrade']);
    Route::get('/getUpgradeStatus', [app\controller\UpgradeController::class, 'getUpgradeStatus']);
    Route::get('/getAllProjectsWithVersions', [app\controller\UpgradeController::class, 'getAllProjectsWithVersions']);
    Route::get('/previewUninstall', [app\controller\UpgradeController::class, 'previewUninstall']);
    Route::post('/uninstallPlugin', [app\controller\UpgradeController::class, 'uninstallPlugin']);
    Route::post('/reinstallPlugin', [app\controller\UpgradeController::class, 'reinstallPlugin']);
});

// 用户管理
Route::group('/user', function () {
    Route::post('/register', [app\controller\UserController::class, 'register']);
    Route::post('/sendForgetEmailCode', [app\controller\UserController::class, 'sendForgetEmailCode']);
    Route::post('/resetPassword', [app\controller\UserController::class, 'resetPassword']);
});
