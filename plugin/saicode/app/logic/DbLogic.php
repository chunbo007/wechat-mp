<?php
// +----------------------------------------------------------------------
// | saithink [ saithink快速开发框架 ]
// +----------------------------------------------------------------------
// | Author: sai <1430792918@qq.com>
// +----------------------------------------------------------------------
namespace plugin\saicode\app\logic;

use plugin\saiadmin\basic\BaseLogic;
use plugin\saiadmin\utils\Helper;
use support\think\Db;

/**
 * 数据库表逻辑层
 */
class DbLogic extends BaseLogic
{

    /**
     * 数据表列表
     */
    public function getList($query): array
    {
        $current_page = request()->input('page', 1);
        $per_page = request()->input('limit', 10);

        if (!empty($query['source'])) {
            if (!empty($query['name'])) {
                $sql = 'show table status where name=:name ';
                $list = Db::connect($query['source'])->query($sql, ['name' => $query['name']]);
            } else {
                $list = Db::connect($query['source'])->query('show table status');
            }
        } else {
            if (!empty($query['name'])) {
                $sql = 'show table status where name=:name ';
                $list = Db::query($sql, ['name' => $query['name']]);
            } else {
                $list = Db::query('show table status');
            }
        }

        $data = [];
        foreach ($list as $item) {
            $data[] = [
                'name' => $item['Name'],
                'engine' => $item['Engine'],
                'rows' => $item['Rows'],
                'data_free' => $item['Data_free'],
                'data_length' => $item['Data_length'],
                'index_length' => $item['Index_length'],
                'collation' => $item['Collation'],
                'create_time' => $item['Create_time'],
                'comment' => $item['Comment'],
            ];
        }
        $total = count($data);
        $last_page = ceil($total/$per_page);
        $startIndex = ($current_page - 1) * $per_page;
        $pageData = array_slice($data, $startIndex, $per_page);
        return [
            'data' => $pageData,
            'total' => $total,
            'current_page' => $current_page,
            'per_page' => $per_page,
            'last_page' => $last_page,
        ];
    }

    /**
     * 获取列信息
     */
    public function getColumnList($table, $source): array
    {
        $columnList = [];
        if (preg_match("/^[a-zA-Z0-9_]+$/", $table)) {
            if (!empty($source)) {
                $list = Db::connect($source)->query('SHOW FULL COLUMNS FROM `'.$table.'`');
            } else {
                $list = Db::query('SHOW FULL COLUMNS FROM `'.$table.'`');
            }
            foreach ($list as $column) {
                preg_match('/^\w+/', $column['Type'], $matches);
                $columnList[] = [
                    'column_key' => $column['Key'],
                    'column_name'=> $column['Field'],
                    'column_type' => $matches[0],
                    'column_comment' => trim(preg_replace("/\([^()]*\)/", "", $column['Comment'])),
                    'extra' => $column['Extra'],
                    'default_value' => $column['Default'],
                    'is_nullable' => $column['Null'],
                ];
            }
        }
        return $columnList;
    }

}
