<?php
return array(
    //'配置项'=>'配置值'
    'DB_TYPE'              => 'mysql',
    'DB_HOST'              => '127.0.0.1',
    'DB_NAME'              => 'loan',
    'DB_USER'              => 'root',
    'DB_PWD'               => 'root',
    'DB_PORT'              => 3306,
    'DB_PREFIX'            => '',
    'DB_CHARSET'           => 'utf8',
    /*用户权限的配置*/
    'USER_AUTH_ON'         => true,
    'USER_AUTH_TYPE'       => 1,
    'USER_AUTH_KEY'        => 'uid',
    'DEFAULT_GROUP'        => 'Admin',
    'USER_AUTH_GATEWAY'    => '/Admin/Public/login', // 默认认证网关
    'NOT_AUTH_MODULE'      => 'Public', // 默认无需认证模块
    'RBAC_SUPERADMIN'      => 'admin', //超级管理员名称
    'ADMIN_AUTH_KEY'       => 'superadmin', //超级管理员识别
    'RBAC_ROLE_TABLE'      => 'role', //角色表名称
    'RBAC_USER_TABLE'      => 'role_user', //角色和用户的中间表名称
    'RBAC_ACCESS_TABLE'    => 'access', //权限表名称
    'RBAC_NODE_TABLE'      => 'node', //节点表名称
    'USER_AUTH_MODEL'      => 'User', // 默认验证数据表模型
    'SHOW_PAGE_TRACE'      => false,
    'REQUIRE_AUTH_MODULE'  => '', // 默认需要认证模块
    'NOT_AUTH_ACTION'      => '', // 默认无需认证操作
    'REQUIRE_AUTH_ACTION'  => '', // 默认需要认证操作
    'GUEST_AUTH_ON'        => false, // 是否开启游客授权访问
    'GUEST_AUTH_ID'        => 0, // 游客的用户ID
    'COOKIE_HTTPONLY'      => 1,
    'SESSION_AUTO_START'   => true,
    'URL_CASE_INSENSITIVE' => true,
);