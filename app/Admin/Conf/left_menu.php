<?php
return array(
    'LEFT_MENU' => array(
        'welcome' => array(
            array(
                'title' => '后台首页',
            )
        ),
        'market'  => array(
            array(
                'title' => '贷超管理',
                'list'  => array(
                    array('title' => '产品管理', 'url' => 'Goods/index'),
                ),

            ),
        ),
        'system'  => array(
            array(
                'title' => '系统设置',
                'list'  => array(
                    array('title' => '用户管理', 'url' => 'User/userlist'),
                    array('title' => '角色管理', 'url' => 'Role/index'),
                    array('title' => '节点管理', 'url' => 'Node/showlist'),
                ),
            ),
        ),
    )
);
