<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------

// 检测PHP环境
if (version_compare(PHP_VERSION, '5.3.0', '<')) {
    die('require PHP > 5.3.0 !');
}

// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
define('APP_DEBUG', true);

//定义根目录
define('ROOT', dirname(__FILE__) . '/');
// 绑定Admin模块到当前入口文件
//define('BIND_MODULE','Admin');

// 定义应用目录
define('APP_PATH', './app/');
define('DISTINCT_REQUEST_ID', substr(md5(uniqid(rand(0, 99999999))), 0, 16) . rand(0, 99999999));

//是否生成目录安全文件
define('BUILD_DIR_SECURE', false);

// 引入ThinkPHP入口文件
require './core/ThinkPHP.php';

// 亲^_^ 后面不需要任何代码了 就是如此简单