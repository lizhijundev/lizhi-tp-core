# Lizhi Tp Core
基于ThinkPHP 8 框架二次开发api基础框架

## 功能
+ 统一API响应
+ 统一错误码配置
+ 全局异常处理，标准化输出json格式数据，支持debug日志记录
+ 二开BaseContainer控制器基类，统一权限处理

## 快速上手
### 安装
```shell
composer -vvv require lizhijun/lizhi-tp-core
```
安装成功后，自动创建配置文件`config/apicode.php`
```php
<?php
// +----------------------------------------------------------------------
// | Apicode 错误码配置
// +----------------------------------------------------------------------
return [
    // ===========================  以下配置请勿删除  ===========================
    // 成功
    'success' => [0, ''],
    // 系统内部错误
    'system_error' => [500, '很抱歉，服务器内部错误'],
    // 数据库错误
    'db_error' => [501, '数据库异常'],
    // 业务错误
    'business_error' => [400, '业务错误'],
    // 未登录
    'not_logged' => [401, '未登录'],
    // 无权限
    'not_permission' => [403, '无权限'],
    // ===========================  自定义错误码  ===========================
];
```

### 配置`app/provider.php`
因为 `lizhi-tp-core` 拓展会自动接管 `think\Request` 和 `think\exception\Handle`,所以需要确认这两项配置需要删掉
```php
<?php

// 容器Provider定义文件
return [
    // 'think\Request'          => Request::class,
    //'think\exception\Handle' => ExceptionHandle::class,
];

```