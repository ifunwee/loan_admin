<?php

namespace Admin\Controller;

use Common\Controller\CommonController;

class AccessController extends CommonController
{
    //配置权限
    public function access()
    {
        $ui['role'] = 'active';
        $this->assign('ui', $ui);
        $rid = $_GET['rid'];
        //读取有用字段
        $field = array(
            'id',
            'name',
            'title',
            'pid'
        );
        $node  = M('node')->order('sort')->field($field)->select();

        //读取用户原有权限
        $access = M('access')->where(array('role_id' => $rid))->getField('node_id', true);
        $node   = node_merge($node, $access);

        $this->assign('rid', $rid);
        $this->assign('node', $node);
        $this->assign('title', '权限配置');
        $this->display();
    }

    //配置权限接受表单
    public function setAccess()
    {
        $rid = $_POST['rid'];
        $db  = M('access');
        //删除原权限
        $db->where(array('role_id' => $rid))->delete();
        //组合新权限
        $data = array();
        foreach ($_POST['access'] as $v) {
            $tmp    = explode('_', $v);
            $data[] = array(
                'role_id' => $rid,
                'node_id' => $tmp[0],
                'level'   => $tmp[1]
            );
        }
        //插入新权限
        if ($db->addAll($data)) {
            $this->success('修改成功！', U('./Role'));
        } else {
            $this->error('修改失败！');
        }
    }
}