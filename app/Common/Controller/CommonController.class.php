<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/4/23
 * Time: 10:46
 */

namespace Common\Controller;

use Org\Util\Rbac;
use Think\Controller;
use Think\Page;
use Admin\Org\Qiniu;

class CommonController extends Controller
{
    public function _initialize()
    {
        $this->assign('title', '贷超后台管理');
        $this->assign('image_domain', C('image_domain'));
        //用户权限检查
        if (C('USER_AUTH_ON') && !in_array(MODULE_NAME, explode(',', C('NOT_AUTH_MODULE')))) {

            if (!RBAC::AccessDecision()) {
                if (!$_SESSION[C('USER_AUTH_KEY')]) {
                    redirect(PHP_FILE . C('USER_AUTH_GATEWAY'), 2, '你还没登录不能访问');
                }

                $this->error('没有权限');
            }
        }
    }

    public function _show($name = '')
    {
        $model             = D($name);
        $count             = $model->count();
        $Page              = new  Page($count, 10);
        $condition['page'] = $Page->show();
        $condition['list'] = $model->order(array('id' => 'desc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();

        return $condition;
    }

    public function show($name = '', $charset = '', $contentType = '', $prefix = '')
    {
        $model             = D($name);
        $count             = $model->count();
        $Page              = new  Page($count, 10);
        $condition['page'] = $Page->show();
        $condition['list'] = $model->order(array('sort' => 'asc'))->limit($Page->firstRow . ',' . $Page->listRows)->select();

        return $condition;
    }

    public function _insert($name = '')
    {
        $model = D($name);
        if (false === $model->create()) {
            return $model->getError();
        }

        $data = $_POST;
        if ($name == 'Role') {
            $data['create_time'] = time();
        }
        $list = $model->add($data);
        if (false !== $list) {
            return 1;
        } else {
            return 2;
        }
    }

    public function _edit($name = '')
    {
        $model = D($name);
        $array = $_POST;

        $condition['id'] = $array['id'];
        unset($array['id']);
        unset($array['pid']);
        unset($array['level']);
        $result = $model->where($condition)->save($array);

        if (false === $result) {
            return -1;
        } else {
            return 1;
        }
    }

    public function _del($name = '')
    {
        $model  = D($name);
        $id     = $_GET['id'];
        $result = $model->where(array('id' => $id))->delete();
        if (false === $result) {
            return -1;
        } else {
            return 1;
        }
    }

    public function _sort($sort, $db = CONTROLLER_NAME)
    {
        $model = M($db);
        foreach ($sort as $id => $val) {
            $val          = (int)$val > 0 ? $val : '';
            $data['sort'] = $val;
            $model->where(array('id' => $id))->save($data);
        }

        $this->success('排序成功', U('index'));
    }

    public function _search($keyword, $db = CONTROLLER_NAME)
    {
        $model = M($db);
        $condition = array('title' => array("like","%".trim($keyword)."%"));
        $count = $model->where($condition)->count();
        $page = new  Page($count, 2);
        $list = $model->where($condition)->limit($page->firstRow . ',' . $page->listRows)->select();

        $this->assign('page', $page->show());
        $this->assign('list', $list);
        $this->assign('keyword', $keyword);
        $this->display();
    }

}