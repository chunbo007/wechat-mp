<?php
/**
 * Here is your custom functions.
 */

function success($data = [], $code = 0, $msg = '操作成功', $is_array = false)
{
    if ($is_array) {
        return ['code' => $code, 'data' => $data, 'msg' => $msg];
    } else {
        return json(['code' => $code, 'data' => $data, 'msg' => $msg]);
    }
}

function error($msg = "操作失败", $code = -1, $data = [], $is_array = false)
{
    if ($is_array) {
        return ['code' => $code, 'data' => $data, 'msg' => $msg];
    } else {
        return json(['code' => $code, 'data' => $data, 'msg' => $msg]);
    }
}