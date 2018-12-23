<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/4/24
 * Time: 9:18
 */

namespace Admin\Model;

use Think\Model;

class NodeModel extends Model
{
    protected $_validate = array(
        array(
            'name',
            'checkNode',
            '节点已存在',
            0,
            'callback'
        ),
    );

    public function checkNode()
    {
        if (is_string($_POST['name'])) {
            $map['name']   = $_POST['name'];
            $map['pid']    = isset($_POST['pid']) ? $_POST['pid'] : 0;
            $map['status'] = 1;
            if (!empty($_POST['id'])) {
                $map['id'] = array(
                    'neq',
                    $_POST['id']
                );
            }
            $result = $this->where($map)->filed('id')->find();
            if ($result) {
                return false;
            } else {
                return true;
            }
        }
    }
}