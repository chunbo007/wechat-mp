<?php

use Yansongda\Pay\Pay;

return [
    'alipay' => [
        'default' => [
            // 必填-支付宝分配的 app_id
            'app_id' => '', // 正式
            // 必填-应用私钥 字符串或路径
            'app_secret_cert' => '',
            // 必填-应用公钥证书 路径
            'app_public_cert_path' => base_path('/config/alipay/appCertPublicKey_2021004128663683.crt'),
            // 必填-支付宝公钥证书 路径
            'alipay_public_cert_path' => base_path('/config/alipay/alipayCertPublicKey_RSA2.crt'),
            // 必填-支付宝根证书 路径
            'alipay_root_cert_path' => base_path('/config/alipay/alipayRootCert.crt'),
            // 选填-服务商模式下的服务商 id，当 mode 为 Pay::MODE_SERVICE 时使用该参数
            'service_provider_id' => '',
            // 选填-默认为正常模式。可选为： MODE_NORMAL, MODE_SANDBOX, MODE_SERVICE
            'mode' => class_exists(Pay::class) ? Pay::MODE_NORMAL : 0, // 正式
        ],
    ],
    'wechat' => [
        'default' => [
            // 必填-商户号，服务商模式下为服务商商户号
            'mch_id' => '',
            // 必填-商户秘钥
            'mch_secret_key' => '',
            // 必填-商户私钥 字符串或路径
            'mch_secret_cert' => base_path('/config/wechat/apiclient_key.pem'),
            // 必填-商户公钥证书路径
            'mch_public_cert_path' => base_path('/config/wechat/apiclient_cert.pem'),
            // 公众号 的 app_id
            'mp_app_id' => '',
            // 公众号 的 开发者密码(AppSecret)，使用jsapi时需要配置
            'mp_app_secret' => '',
            // 选填-小程序 的 app_id
            'mini_app_id' => '',
            // 选填-app 的 app_id
            'app_id' => '',
            // 选填-合单 app_id
            'combine_app_id' => '',
            // 选填-合单商户号
            'combine_mch_id' => '',
            // 选填-服务商模式下，子公众号 的 app_id
            'sub_mp_app_id' => '',
            // 选填-服务商模式下，子 app 的 app_id
            'sub_app_id' => '',
            // 选填-服务商模式下，子小程序 的 app_id
            'sub_mini_app_id' => '',
            // 选填-服务商模式下，子商户id
            'sub_mch_id' => '',
            // 选填-微信公钥证书路径, optional，强烈建议 php-fpm 模式下配置此参数
            'wechat_public_cert_path' => [],
            // 选填-默认为正常模式。可选为： MODE_NORMAL, MODE_SERVICE
            'mode' => class_exists(Pay::class) ? Pay::MODE_NORMAL : 0, // 正式
        ]
    ],
    'logger' => [
        'enable' => true,
        'file' => runtime_path() . '/logs/payment.log',
        'level' => env('APP_DEBUG', false) ? 'debug' : 'info', // 建议生产环境等级调整为 info，开发环境为 debug
        'type' => 'single', // optional, 可选 daily.
        'max_file' => 30, // optional, 当 type 为 daily 时有效，默认 30 天
    ],
    'http' => [ // optional
        'timeout' => 5.0,
        'connect_timeout' => 5.0,
        // 更多配置项请参考 [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
    ],
    '_force' => true, // 必须
];