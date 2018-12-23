<?php

namespace Admin\Controller;

use Common\Controller\CommonController;

class IndexController extends CommonController
{

    /**
     * @api index-后台首頁
     */
    public function index()
    {
        $name = I('name');
        $menu = C('LEFT_MENU');
        if (!isset($menu[$name])) {
            $left_menu['list']    = $menu['welcome'];
            $left_menu['current'] = 'welcome';
        } else {
            $left_menu['list']    = $menu[$name];
            $left_menu['current'] = $name;
        }

        session('LEFT_MENU', $left_menu);
        $this->display();
    }

    /**
     * @api logout-後台登出
     */
    public function logout()
    {
        D('User')->loginout();
        redirect(U('Public/login'), 1, '成功登出，跳转中。。。');
    }

}

