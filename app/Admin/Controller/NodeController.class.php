<?php

namespace Admin\Controller;

use Common\Controller\CommonController;

class NodeController extends CommonController
{
    /**
     * @api index-节点管理
     */
    public function index()
    {
        $node = M('node')->select();

        $this->assign('node', $node);
        $this->assign('title', '操作节点管理');
        $this->display();
        //$a='1234';
        // var_dump($a);
    }

    /**
     * @api showlist-节点列表
     */
    public function showlist()
    {
        $field = array(
            'id',
            'name',
            'title',
            'pid',
            'status'
        );
        $node  = M('node')->field($field)->order('sort')->select();
        $node  = node_merge($node);
        //print_r($node);
        ///dump($node);

        $this->assign('node', $node);
        $this->assign('title', '操作节点管理');
        $this->display();
    }

    /**
     * @api addNode-添加节点
     */
    public function addNode()
    {
        $ui['node'] = 'active';
        $this->assign('ui', $ui);
        $pid   = isset($_GET['pid']) ? $_GET['pid'] : 0;
        $level = isset($_GET['level']) ? $_GET['level'] : 1;
        $this->assign('pid', $pid);
        $this->assign('level', $level);
        switch ($level) {
            case 1:
                $this->type = '应用';
                break;
            case 2:
                $this->type = '控制器';
                break;
            case 3:
                $this->type = '动作方法';

                break;
        }

        if ($this->type == '动作方法') {
            $condition['id'] = $_GET['pid'];
            $node            = M('node');
            $controllername  = $node->where($condition)->getField('name');
            $str             = file_get_contents(APP_PATH . '/Admin/Controller/' . $controllername . 'Controller' . '.' . 'class.php');
            preg_match_all('/@api*(.*?)-/i', $str, $matchs);
            $listff = ($matchs['1']);
            foreach ($listff as $k) {
                if (trim($k) == '*(.*?)') {
                    continue;
                }
                $k          = $this . str_replace(' ', '', $k);
                $newArray[] = $k;
            }
            $this->assign("listff", $newArray);
        }
        $this->assign('title', '添加节点');
        $this->display();
    }

    /**
     * 添加
     */
    public function addNodeHandle()
    {
        //print_r($_POST);
        $name   = 'Node';
        $result = $this->_insert($name);
        if (1 == $result) {
            $condition['status'] = 1;
            $condition['info']   = '添加成功';
        } else {
            $conditon['status'] = 0;
            switch ($result) {
                case 2:
                    $condition['info'] = '添加失败，请重试';
                    break;
                default:
                    $condition['info'] = $result;
                    break;
            }
        }
        $this->ajaxReturn($condition);
    }

    /**
     * 修改
     */
    public function updateNodeHandle()
    {
        $name   = 'node';
        $result = $this->_edit($name);
        if ($result == 1) {
            $condition['status'] = 1;
            $condition['info']   = '修改成功！';
        } else {
            $condition['status'] = 0;
            $condition['info']   = '修改失败，请重试！';
        }
        $this->ajaxReturn($condition);
    }

    /**
     * @api alterNode-修改节点
     */
    public function alterNode()
    {
        $ui['node'] = 'active';
        $this->assign('ui', $ui);
        $NO     = M('Node');
        $NOINFO = $NO->find($_GET['id']);
        $this->assign('node', $NOINFO);
        $this->assign('title', '修改节点');
        $this->display();
    }

    /**
     * @api del-删除节点
     */
    public function del()
    {
        $name   = 'node';
        $result = $this->_del($name);
        if (1 == $result) {
            $condition['status'] = 1;
            $condition['info']   = '节点删除成功';
        } else {
            $condition['status'] = 0;
            $condition['info']   = '角色删除失败请重试';
        }
        $this->ajaxReturn($condition);
    }
}
