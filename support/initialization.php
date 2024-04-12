<?php

/**
 * @param string $key
 * @param $default
 * @return array|bool|mixed|string|null
 */
//function env(string $key, $default = null)
//{
//    $value = getenv($key);
//    if ($value === false) {
//        return value($default);
//    }
//    switch (strtolower($value)) {
//        case 'true':
//        case '(true)':
//            return true;
//        case 'false':
//        case '(false)':
//            return false;
//        case 'empty':
//        case '(empty)':
//            return '';
//        case 'null':
//        case '(null)':
//            return null;
//    }
//    if (($valueLength = strlen($value)) > 1 && $value[0] === '"' && $value[$valueLength - 1] === '"') {
//        return substr($value, 1, -1);
//    }
//    return $value;
//}
