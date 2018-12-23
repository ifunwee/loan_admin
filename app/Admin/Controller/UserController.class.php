<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/4/28
 * Time: 9:32
 */

namespace Admin\Controller;

use Common\Controller\CommonController;

class UserController extends CommonController
{
    /*
* @api addUser-添加用户
*/

    public function addUser()
    {
        $ui['user'] = 'active';
        $this->assign('ui', $ui);
        $role = D('role');
        $r    = $role->select();
        $this->assign('role', $r);
        $this->assign('title', '添加用户');
        $this->display();
    }

    public function index()
    {
        $name = 'user';
        $data = $this->_show($name);
        $this->assign('data', $data['list']);
        $this->assign('page', $data['page']);
        $this->assign('title', '用户管理');
        $this->display();
    }

    /*
   * @api userlist-用户列表
   */
    public function userlist()
    {

        $u = D('UserRelation')->field('password', true)->relation(true)->select();

        $this->assign('user', $u);
        $this->assign('title', '当前用户');
        $this->display();
    }

    public function alterUser()
    {
        $ui['user'] = 'active';
        $this->assign('ui', $ui);
        $vip  = M('user');
        $id   = $_GET['id'];
        $data = $vip->where(array('id' => $id))->find();
        $this->assign('data', $data);
        $this->assign('title', '用户信息修改');
        $this->display();
    }

    /**
     * @api edit-修改用戶信息
     *      修改用户
     */
    public function edit()
    {

        $model = M('user');
        $array = $_POST;

        $condition['id'] = $array['id'];

        $result = $model->where($condition)->save($array);

        if (false === $result) {
            $condition['status'] = 0;
            $condition['info']   = '修改失败，请重试！';
        } else {
            $condition['status'] = 1;
            $condition['info']   = '修改成功！';
        }

        $this->ajaxReturn($condition);
    }

    /**
     * @api del-删除用户
     */
    public function del()
    {
        $Drange = $_GET['id'];
        if ($Drange == 1) {

            $condition['status'] = 0;
            $condition['info']   = '超级管理员不可删除';
            $this->ajaxReturn($condition);
        } else {
            $name = 'user';
            $data = $this->_del($name);
            if (1 == $data) {
                $condition['status'] = 1;
                $condition['info']   = '用户删除成功';
            }
            $this->ajaxReturn($condition);
        }
    }

    /**
     * @api add-添加用戶
     */
    public function add()
    {

        $user   = array(
            'username' => I('username'),
            'password' => I('password'),
            'email'    => I('email'),
            'reg_time' => time(),
            'reg_ip'   => get_client_ip(),
        );
        $role   = I('role_id');
        $result = D('User')->addUser($user, $role);
        if (1 == $result) {
            $condition['status'] = 1;
            $conditin['info']    = '添加成功';
        } else {
            $condition['status'] = 0;
            $condition['info']   = '添加失败，请保持用户名唯一和邮箱地址正确';
        }
        $this->ajaxReturn($condition);
    }

    /**
     * @api unlock-
     */
    public function unlock()
    {
        $result = D('User')->unlock(I('get.id'));
        if (1 == $result) {
            $condition['status'] = 1;
            $condition['info']   = '账户激活成功！';
        } else {
            $condition['status'] = 0;
            $condition['info']   = '账户激活失败！';
        }
        $this->ajaxReturn($condition);
    }

    /*
      * @api lock-用户锁定
      */
    public function lock()
    {

        $result = D('User')->lock(I('get.id'));
        if (1 == $result) {
            $condition['status'] = 1;
            $condition['info']   = '账户已锁定！';
        } else {
            $condition['status'] = 0;
            $condition['info']   = '账户锁定失败！';
        }
        $this->ajaxReturn($condition);
    }

}