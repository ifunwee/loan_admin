<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/4/24
 * Time: 10:59
 */

namespace Admin\Controller;

use Common\Controller\CommonController;

class RoleController extends CommonController
{
    /*
   * @api index-角色管理
   */
    public function index()
    {
        $name = 'Role';
        $data = $this->_show($name);
        $this->assign('data', $data['list']);
        $this->assign('page', $data['page']);
        $this->assign('title', '管理中心-用户管理');
        $this->display();
    }

    public function addRole()
    {
        $ui['role'] = 'active';
        $this->assign('ui', $ui);
        $this->assign('title', '管理中心-添加角色');
        $this->display();
    }

    /*
 * @api inser-角色添加
 */
    public function insert()
    {
        $name = 'Role';
        $data = $this->_insert($name);
        if (1 == $data) {
            $condition['status'] = 1;
            $condition['info']   = success;
        } else {
            $condition['status'] = 0;
            switch ($data) {
                case 2:
                    $info = '添加失败，请重试';
                    break;
                default:
                    $info = $data;
                    break;
            }
            $condition['info'] = $info;
        }
        $this->ajaxReturn($condition);
    }

    /*
     * @api edit-编辑角色
     */
    public function edit()
    {
        $name = 'Role';
        $data = $this->_edit($name);
        if (1 == $data) {
            $condition['status'] = 1;
            $condition['info']   = success;
        } else {
            $condition['status'] = 0;
            $condition['info']   = '修改失败，请重试';
        }
        $this->ajaxReturn($condition);
    }

    /*
     * @api del-删除角色
     */
    public function del()
    {
        $name = 'Role';
        $data = $this->_del($name);

        if (1 == $data) {
            $condition['status'] = 1;
            $condition['info']   = '角色删除成功';
        }
        $this->ajaxReturn($condition);
    }

    /*
    * @api alterUser-修改角色
    */
    public function alterUser()
    {

        $ui['role'] = 'active';
        $this->assign('ui', $ui);
        $data = D('Role')->show(I('get.id'));
        $this->assign('data', $data);
        $this->assign('title', '修改角色');
        $this->display();
    }
}