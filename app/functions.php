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

// 加密函数
function encrypt($data, $key)
{
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
    $encrypted = openssl_encrypt($data, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
    return base64_encode($encrypted . '::' . $iv);
}

// 解密函数
function decrypt($data, $key)
{
    list($encrypted_data, $iv) = explode('::', base64_decode($data), 2);
    return openssl_decrypt($encrypted_data, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
}