<?php

namespace Common\Model;

use Org\Util\Rbac;
use Think\Model;

/**
 * Created by PhpStorm.
 * User: zhaoxinyu
 * Date: 2015/4/20
 * Time: 13:53
 */
abstract class BaseuserModel extends Model
{
    protected $_config = array();

    protected $patchValidate = true;
    protected $_validate = array(
        /*about username*/

        // array('username','3,15','用户名长度在6到20位之间','length'),
        /*about password*/
        //array('password','6,20','密码长度错误,在6到20位之间','length'),
        /*about email*/
        array('username', '', '用户名已存在', self::MUST_VALIDATE, 'unique', self::MODEL_BOTH),
        array('email', '', '邮箱地址已存在', self::MUST_VALIDATE, 'unique', self::MODEL_BOTH),
        array('email', 'email', '邮箱格式不符合规则'),
    );
    protected $_auto = array(
        array('password', 'md5', 3, 'function'),
    );

    public function addUser($data, $role)
    {
        if ($info = $this->create($data)) {

            $res = $this->add();
            if ($res) {
                $ip   = get_client_ip();
                $time = time();
                $re   = array(
                    'id'       => $res['id'],
                    'reg_time' => $time,
                    'reg_ip'   => $ip,
                );
                $this->save($re);
                foreach ($role as $v) {
                    $role1[] = array(
                        'role_id' => $v,
                        'user_id' => $res,
                    );
                }
                M('role_user')->addAll($role1);

                return 1;
            }

            return -1;
        }

        return $this->getError();
    }

    public function login($user, $pwd)
    {
        $map             = array();
        $map['username'] = $user;
        $map['status']   = array('gt', 0);
        $authInfo        = RBAC::authenticate($map);
        if (false === $authInfo) {
            return -1;
        } else {
            if ($authInfo['password'] != md5($pwd)) {
                return -2;
            }
            $super_admin = C('RBAC_SUPERADMIN');
            if ($authInfo['username'] == $super_admin) {
                session('superadmin', true);
            }
            $this->loginSuccess($authInfo);
            RBAC::saveAccessList();

            return 1;
        }
    }

    public function loginSuccess($data)
    {

        $ip = get_client_ip();

        $time = time();
        $res  = array(
            'id'              => $data['id'],
            'login_count'     => array('exp', '`login_count`+1'),
            'last_login_time' => $time,
            'last_login_ip'   => $ip,
        );
        $this->save($res);
        foreach ($this->_config['session'] as $key => $value) {

            session($key, $data[$value]);
        }
    }

    public function loginout()
    {
        session(null);
    }

}