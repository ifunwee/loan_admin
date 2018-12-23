<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/4/27
 * Time: 10:09
 */

namespace Admin\Model;

use Think\Model;

class  RoleModel extends Model
{

    public function show($id)
    {
        $data = $this->where(array('id' => $id))->find();
        if ($data) {
            return $data;
        } else {
            return '删除失败';
        }
    }
}