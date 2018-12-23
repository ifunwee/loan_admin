<?php

namespace Admin\Model;

use Common\Model\BaseuserModel;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/4/20
 * Time: 14:56
 */
class UserModel extends BaseuserModel
{
    protected $_config = array(
        'login_info' => 'id,nickname,last_login_time,status',
        'session'    => array(
            'uid'             => 'id',
            'username'        => 'username',
            'last_login_time' => 'last_login_time',
        ),
    );

    public function unlock($id)
    {
        $condition['status'] = 1;
        $result              = $this->where(array('id' => $id))->save($condition);
        if (false === $result) {
            return -1;
        } else {
            return 1;
        }
    }

    public function lock($id)
    {
        $condition['status'] = 0;
        $result              = $this->where(array('id' => $id))->save($condition);

        if (false === $result) {
            return -1;
        } else {
            return 1;
        }
    }
}