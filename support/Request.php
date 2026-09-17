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

namespace support;

/**
 * Class Request
 * @package support
 */
class Request extends \Webman\Http\Request
{

    /**
     * 获取参数增强方法
     * @param array $params
     * @return array
     */
    public function more(array $params): array
    {
        $p = [];
        foreach ($params as $param) {
            if (!is_array($param)) {
                $p[$param] = $this->input($param);
            } else {
                if (!isset($param[1])) $param[1] = '';
                if (is_array($param[0])) {
                    $name = $param[0][0] . '/' . $param[0][1];
                    $keyName = $param[0][0];
                } else {
                    $name =  $param[0];
                    $keyName = $param[0];
                }
                $p[$keyName] = $this->input($name, $param[1]);
            }
        }
        return $p;
    }

}