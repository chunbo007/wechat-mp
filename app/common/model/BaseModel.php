<?php

namespace app\common\model;

use support\Request;
use think\Model;

class BaseModel extends Model
{
    /**
     * @param Request|null $request
     * @param array $filter 导出时可能会用到，传数组
     * @param array $pre 关联表需要指明要查的字段属于哪张表 ['erp_enquiry' => ['model', 'user_id', 'create_time']];
     * @param callable|null $callback 自定义查询条件
     * @return array
     */
    protected static function buildWhere(Request $request = null, array $filter = [], array $pre = [], callable $callback = null): array
    {
        $column = array_column(static::$column, null, 'key');
        if (!empty($request)) {
            $current_page = $request->input('pageNo', 1);
            $page_size = $request->input('pageSize', 10);
            $params = $request->except(['token_', 'current', 'pageSize']);
        } else {
            $current_page = $filter['pageNo'] ?? 1;
            $page_size = $filter['pageSize'] ?? 10;
            $params = $filter;
        }
        $where = [];
        foreach ($params as $key => $field) {
            if (key_exists($key, $column) && !empty($field)) {
                if (!is_array($field)) $field = trim($field);
                $where_type = $column[$key]['where'] ?? '';
                // 处理关联表
                $keyArr = array_keys($pre);
                foreach ($keyArr as $_key) {
                    if (in_array($key, $pre[$_key])) {
                        $key = $_key . '.' . $key;
                        break;
                    }
                }
                switch (trim($where_type)) {
                    case '>':
                        $where[] = [$key, '>', (int)$field];
                        break;
                    case '>=':
                        $where[] = [$key, '>=', $field];
                        break;
                    case '<':
                        $where[] = [$key, '<', (int)$field];
                        break;
                    case '<=':
                        $where[] = [$key, '<=', $field];
                        break;
                    case 'like%':
                        $where[] = [$key, 'like', "{$field}%"];
                        break;
                    case '%like':
                        $where[] = [$key, 'like', "%{$field}"];
                        break;
                    case 'like':
                        $where[] = [$key, 'like', "%{$field}%"];
                        break;
                    case 'time>':
                        $where[] = [$key, '>=', strtotime($field)];
                        break;
                    case 'in':
                        $field = str_replace('，', ',', $field);
                        $field = explode(',', $field);
                        $field = array_map('trim', $field);
                        $where[] = [$key, 'in', $field];
                        break;
                    case 'time_range':
                        $where[] = [$key, 'between', [strtotime(trim($field[0], '"')), strtotime(trim($field[1], '"'))]];
                        break;
                    case 'custom':
                        if (is_callable($callback)) {
                            if ($callback($key, $field) !== false) {
                                $where[] = $callback($key, $field);
                            }
                        }
                        break;
                    default:
                        $where[] = [$key, '=', $field];
                }
            }
        }
        return [
            'page_size' => $page_size,
            'current_page' => $current_page,
            'where' => $where
        ];
    }
}