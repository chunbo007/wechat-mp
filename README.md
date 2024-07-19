# wechat-mp 项目简介
微信开放平台管理工具
微信开放平台服务商一般有多套小程序需要管理，帮人代开发时需要客户不停扫码授权，体验极其不好。对于SAAS小程序服务商也可以在运维平台中尝试二开对接开放平台的api，但是项目多了以后每个运营平台都需要重复造轮子，而且维护成本也高，所以萌生了写一个通用管理微信开放平台的工具。

微信官方其实有提供“第三方平台云服务”，但需要付费使用或者下载他们的源码本地部署，可惜官方只提供了GO语言的版本，所以只能自己写一个了。

目前打算先实现以下功能

1. 做为中台，为其他项目提供 authorizer_access_token
2. 配置服务器域名
3. 配置业务域名
4. 上传小程序
5. 生成体验版
6. 发布小程序
7. ...

# 开源支持
1. 后端 [webman](https://www.workerman.net/doc/webman)
2. 前端 [ant-design-vue-pro](https://pro.antdv.com/docs/getting-started)
3. SDK [EasyWechat](https://easywechat.com)


# 本地搭建视频教程
[wechat-mp 微信开放平台小程序管理工具本地部署教程](https://www.bilibili.com/video/BV1M7421f7Ff)

# 界面演示
![授权管理](https://github.com/chunbo007/wechat-mp/assets/16535982/14700ac3-fea0-475e-a1e6-f3a6ebbec556)
![版本管理](https://github.com/chunbo007/wechat-mp/assets/16535982/2dbe277d-77ab-4295-9755-9b39dcf3a9ca)
![版本管理2](https://github.com/chunbo007/wechat-mp/assets/16535982/92cb9fc4-ace3-4fb8-93e3-f9a039b04e9e)
![管理管理3](https://github.com/chunbo007/wechat-mp/assets/16535982/37abf340-e1c7-4538-895a-fe9ac767a14e)
![体验者](https://github.com/user-attachments/assets/746c115b-f839-4a9e-bbcc-55fef7481690)
![隐私协议](https://github.com/user-attachments/assets/6d381d8f-2ec6-4953-9cd1-2d9de7fc59b3)
![开放平台](https://github.com/chunbo007/wechat-mp/assets/16535982/5a3ef41d-5257-4051-a450-2f67907d1fe5)
![开放平台2](https://github.com/chunbo007/wechat-mp/assets/16535982/9d195873-62a2-4483-ae27-9fdb9b2166e0)
![消息日志](https://github.com/chunbo007/wechat-mp/assets/16535982/7fa50073-c979-43c5-aaeb-30386f1ab1b6)
![转发日志](https://github.com/chunbo007/wechat-mp/assets/16535982/9614e728-5a9e-4438-83d8-f736c2fbd17c)



# 安装步骤

1. 下载代码
    ```
    // github
    git clone https://github.com/chunbo007/wechat-mp.git
    
    // gitee
    git clone https://gitee.com/chunboli/wechat-mp.git
    ```
2. 安装相关依赖
    ```
    // 安装php相关依赖
    cd wechat-mp
    composer install
    // 安装前端依赖
    cd front
    yarn install --production(推荐) 或 npm install --production
    ```
3. 配置文件
   + 手动执行根目录下的 install.sql 导入数据库
   + 复制根目录下的 .env.example 文件为 .env 文件，并修改相关配置
4. 本地运行
   + windows下运行 php windwos.php start
   + linux下运行 php start.php start (用于开发调试)
   + linux下后台运行 php start.php start -d (用于正式环境)

   以上根据自己实际情况选择，然后运行前端项目
   + 在 front 目录下执行 yarn serve

   之后打开浏览器访问 http://localhost:8001 就可以了，默认用户名/密码都是 admin

5. 打包上线
   1. 进行front下运行 yarn build 进行打包
   2. 将打包后的dist文件夹上传到服务器
   3. 修改nginx配置文件，在其中添加
   ```
    location /admin {
      proxy_pass http://127.0.0.1:8789/admin;
      proxy_set_header   X-Forwarded-Proto $scheme;
      proxy_set_header   X-Real-IP         $remote_addr;
    }
    
    location /wechat {
      proxy_pass http://127.0.0.1:8789/wechat;
      proxy_set_header   X-Forwarded-Proto $scheme;
      proxy_set_header   X-Real-IP         $remote_addr;
    }

    location /openapi {
      proxy_pass http://127.0.0.1:8789/openapi;
      proxy_set_header   X-Forwarded-Proto $scheme;
      proxy_set_header   X-Real-IP         $remote_addr;
    }
    
    location / {
      try_files $uri $uri/ /index.html;
    }
   ```

# 外部调用

如果你需要的不只是小程序版本管理相关的功能，需要自己实现其他功能，可能需要用到
component_access_token、component_appid、authorizer_appid、authorizer_access_token
等参数，由于你在自己的项目中刷新token，可能会让wechat-mp的token失效，或者wechat-mp会让你项目的token失效
所以留了一个开放接口，供其他项目获取相关token，具体调用方式如下

```
// 生成签名
function generateSign($params, $secretKey): string
{
    // 将参数按照键名进行字典排序
    ksort($params);
    // 拼接参数和对应的值
    $signStr = '';
    foreach ($params as $key => $value) {
        $signStr .= $key . '=' . $value . '&';
    }
    // 拼接密钥
    $signStr .= 'key=' . $secretKey;
    // 使用哈希函数计算签名，这里使用 MD5 作为示例
    return strtoupper(md5($signStr));
}

$params = [
    'platform_appid' => '', // 开放平台的appid
    'appid' => '', // 小程序的appid
    'time' => time(),
];
$secretKey = '';
$params['sign'] = generateSign($params,$secretKey);
$url = 'https://xxxxxx.com/openapi/getToken?' . http_build_query($params);
$res = file_get_contents($url);
//var_dump(json_decode($res,true));
```

# 疑难解答
## curl: (60) SSL certificate problem: unable to get local issuer certificate
这是 SSL 证书问题所致，在使用 SDK 调用微信支付等相关的操作时可能会遇到报 “SSL certificate problem: unable to get local issuer certificate” 的错误。

微信公众平台提供的文档中建议对部分较敏感的操作接口使用 https 协议进行访问，例如微信支付和红包等接口中涉及到操作商户资金的一些操作。 wechat SDK 遵循了官方建议，所以在调用这些接口时，除了按照官方文档设置操作证书文件外，还需要保证服务器正确安装了 CA 证书。

下载 CA 证书
你可以从 http://curl.haxx.se/ca/cacert.pem 下载 或者 使用微信官方提供的证书中的 CA 证书 rootca.pem 也是同样的效果。

在 php.ini 中配置 CA 证书
只需要将上面下载好的 CA 证书放置到您的服务器上某个位置，然后修改 php.ini 的 curl.cainfo 为该路径（绝对路径！），重启 php-fpm 服务即可。

ini
curl.cainfo = /path/to/downloaded/cacert.pem
注意证书文件路径为绝对路径！以自己实际情况为准。

其它修改 HTTP 类源文件的方式是不允许的。

如有不明白的可以留言，欢迎各位提pr

如果对你的项目有帮助，欢迎star，谢谢
