# wechat-mp 项目简介
微信开放平台管理工具

微信开放平台服务商一般有多套小程序需要管理，帮人代开发时需要客户不停扫码授权，体验极其不好。对于SAAS小程序也服务商可以在运维平台中尝试二开对接开放平台的api，但是项目多了以后每个运营平台都需要重复造轮子，而且维护成本也高，所以萌生了写一个通用管理微信开放平台的工具。

微信官方其实有提供“第三方平台云服务”，但需要付费使用或者下载他们的源码本地部署，可惜官方只提供了GO语言的版本，所以只能自己写一个了。

目前打算先实现以下功能

1. 快速创建小程序
2. 配置服务器域名
3. 配置业务域名
4. 上传小程序
5. 生成体验版
6. 发布小程序
7. ...

