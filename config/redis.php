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

return [
    'default' => [
        'host' => env('REDIS_HOST'),
        'password' => env('REDIS_PASSWORD'),
        'port' => env('REDIS_PORT'),
        'database' => env('REDIS_DATABASE'),
    ],
    'cache' => [
        'password' => env('REDIS_PASSWORD'),
        'host' => env('REDIS_HOST'),
        'port' => env('REDIS_PORT'),
        'database' => env('REDIS_DATABASE'),
        'prefix' => env('REDIS_QUEUE_PREFIX'),
    ],
];
