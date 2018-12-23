<?php

namespace Admin\Controller;

use Think\Controller;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/4/20
 * Time: 11:15
 * 后台公共控制器
 */
class PublicController extends Controller
{

    public function login()
    {
        $this->assign('title', 'PC商城后台管理系统');
        $this->display();
    }

    public function verify()
    {
        $verify           = new \Think\Verify();
        $verify->length   = 4;
        $verify->fontSize = 40;
        $verify->entry();
    }


    public function checklogin()
    {
        $username = I('post.username');
        $pwd      = I('post.password');
        $verify   = I('post.verify');

        $pass = check_verify($verify);
        if ($pass == true) {
            $result = D('User')->login($username, $pwd);
            if ($result != 1) {
                $content['status'] = 0;
                switch ($result) {
                    case -1:
                        $content['info'] = "该账户已被冻结，请联系管理员";
                        break;
                    case -2:
                        $content['info'] = "当前用户名密码错误或账户不存在";
                        break;
                    default:
                        $content['info'] = $result;
                        break;
                }
            } else {
                $content['status'] = 1;
                $content['info']   = array(
                    'link' => U('Index/index'),
                    'text' => '登录成功，跳转至后台首页。。。',
                );
            }
            $this->ajaxReturn($content);
        } else {

            $content['status'] = 0;
            $content['info']   = "请确认验证码后重新输入";
            $this->ajaxReturn($content);
        }
    }
}