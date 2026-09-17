# wechat-mp 项目简介
云管家官网 [https://isvapi.com](https://isvapi.com)

微信小程序服务商通常需要同时管理多个小程序。帮客户代开发时，经常要让客户反复扫码授权，整个流程比较繁琐，体验也不太好。

如果是 SaaS 小程序项目，也可以直接在自己的运营或运维后台对接微信开放平台 API，把授权、版本管理、发布等功能集成进去。但项目一多，每套系统都要重复开发一遍类似功能，不仅浪费时间，后续维护起来也很麻烦。

所以我就有了做一个通用管理工具的想法，把微信开放平台常用能力统一整合起来，让不同项目都可以直接使用，减少重复开发，也方便集中管理多个小程序。


微信官方其实有提供“第三方平台云服务”，但是功能模块脱离群众、不够实用，且官方只提供了GO语言的版本，所以只能自己写一个了。

目前以实现以下功能
1. 添加多个开放平台，对开放平台模板进行管理
2. 配置服务器域名、配置业务域名、隐私保护、类目管理、
3. 批量上传小程序、生成体验版、发布小程序、版本回退、自动发布
4. 交易保障(处理小程序交易投诉)、体验者管理、微信认证、ICP备案
5. 开放api(便捷的对接自己的系统)、调试工具、消息日志

# 开源支持
1. 后端 [webman](https://www.workerman.net/doc/webman)
2. 前端 Vue3
3. 微信php SDK [EasyWechat](https://easywechat.com)

# 环境要求

| 项目 | 要求 |
| --- | --- |
| PHP | 8.2.x |
| PHP 扩展 | pdo_mysql、redis、curl、fileinfo、mbstring、openssl、json、xml |
| 数据库 | MySQL 5.7 / 8.0 |
| Redis | 5.0 以上（队列、token 缓存依赖） |
| 目录权限 | `runtime`、`public/storage` 可写；`.env` 可写（安装向导需要写入） |

安装向导还会检测 `stream_socket_server`、`proc_open`、`shell_exec` 等函数是否被 `disable_functions` 禁用，Linux 下还需要 pcntl、posix 扩展。

# 安装
0. 参考 [https://www.isvapi.com/install/baota.html](https://www.isvapi.com/install/baota.html)
1. 上传代码到服务器，站点根目录指向 `public`
2. nginx 把请求反代到 webman 进程（默认监听 `0.0.0.0:8989`），参考下方「打包上线」里的配置
3. 启动 webman：Linux 下 `php start.php start -d`，Windows 下 `php windows.php`
4. 浏览器访问站点，未安装时会自动进入安装向导，按提示填写数据库、Redis、站点地址和管理员账号
5. **安装完成后重启一次 webman**

<img src="https://img.isvapi.com/www/images/install/step-6.png" alt="安装向导">

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

如有不明白的可以留言，欢迎各位提pr

如果对你的项目有帮助，欢迎star，谢谢

<img src="https://img.isvapi.com/www/images/wechat/wechat-qrcode.jpg" alt="个人微信" width=260>