ThinkPHP 5.0 SAE扩展
===============
[![Composer Compatible](https://img.shields.io/badge/composer-compatible-brightgreen.svg?style=flat)](https://github.com/composer/composer)
[![License](https://img.shields.io/badge/license-Apache-blue.svg?style=flat)](https://github.com/Osnobel/tp5-sae/blob/master/LICENSE)
[![Platform](https://img.shields.io/badge/platform-web-lightgrey.svg?style=flat)](https://github.com/Osnobel/tp5-sae)

## 获取

### Composer

在框架根目录下安装

```bash
$ composer require osnobel/tp5-sae
```

更新

```bash
$ composer update osnobel/tp5-sae
```

### Git Subtree

假设项目仓库的根目录就是ThinkPHP5框架的根目录，那么第三方类库自动加载的目录路径就是/extend/。

添加子目录，建立与项目仓库的关联

```bash
$ git remote add -f tp5sae https://github.com/Osnobel/tp5-sae
$ git subtree add --prefix=extend/osnobel tp5sae master --squash
```

从远程仓库更新子目录

```bash
$ git fetch tp5sae master  
$ git subtree pull --prefix=extend/osnobel tp5sae master --squash
```

## 配置

修改应用配置文件（默认位于application/config.php）添加场景配置的状态“sae”。

```php
'app_status' => 'sae'，
```

框架会自动加载该状态对应的配置文件（默认位于application/sae.php）。该配置文件设置如下即可自动在SAE运行环境下加载，本地运行环境不加载。

```php
<?php
//sae配置文件
if (!function_exists('sae_debug')) {
    return [];
}
return [
    // +----------------------------------------------------------------------
    // | 应用设置
    // +----------------------------------------------------------------------
    // 关闭调试模式
    'app_debug' => false,

    // +----------------------------------------------------------------------
    // | 数据库设置（共享型MySQL）
    // +----------------------------------------------------------------------
    // 数据库类型
    'type'        => 'mysql',
    // 服务器地址
    'hostname'    => SAE_MYSQL_HOST_M . ',' . SAE_MYSQL_HOST_S,
    // 数据库名
    'database'    => SAE_MYSQL_DB,
    // 用户名
    'username'    => SAE_MYSQL_USER,
    // 密码
    'password'    => SAE_MYSQL_PASS,
    // 端口
    'hostport'    => SAE_MYSQL_PORT,
    // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
    'deploy'      => 1,

    // +----------------------------------------------------------------------
    // | 日志设置
    // +----------------------------------------------------------------------
    'log'       =>  [
        'type'  => '\osnobel\sae\Log',
    ],

    // +----------------------------------------------------------------------
    // | 模板设置
    // +----------------------------------------------------------------------
    'template'  =>  [
        // 模板引擎类型 支持 php think 支持扩展
        'type'         => 'Think',
        // 缓存方式 支持 KVDB 和 Memcached
        'compile_type'  =>  '\osnobel\sae\template\KVDB',
        //'compile_type'  =>  '\osnobel\sae\template\Memcached',
        // 模板引擎普通标签开始标记
        'tpl_begin'    => '{',
        // 模板引擎普通标签结束标记
        'tpl_end'      => '}',
        // 标签库标签开始标记
        'taglib_begin' => '{',
        // 标签库标签结束标记
        'taglib_end'   => '}',
    ],

    // +----------------------------------------------------------------------
    // | 缓存设置
    // +----------------------------------------------------------------------
    'cache'     =>  [
        // 驱动方式 支持 KVDB 和 Memcached
        'type'   => '\osnobel\sae\cache\KVDB',
        //'type'   => '\osnobel\sae\cache\Memcached',
        // 缓存前缀
        'prefix' => '',
        // 缓存有效期 0表示永久缓存
        'expire' => 0,
    ],
];
```
