<?php

namespace Admin\Controller;

use Common\Controller\CommonController;
use Think\Page;

class GoodsController extends CommonController
{
    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * @api index-楼层列表
     */
    public function index()
    {
        $model = D(CONTROLLER_NAME);
        $count = $model->count();
        $p     = I('p');
        $sort  = I('sort');
        $limit = 10;
        $page  = new Page($count, $limit);
        $list  = $model->order('status desc, sort desc')->page($p, $limit)->select();

        if (IS_POST) {
            $sort && $this->_sort($sort);
        }

        $this->assign('page', $page->show());
        $this->assign('list', $list);
        $this->display();
    }

    /**
     * @api edit-固楼层编辑
     */
    public function edit()
    {
        $id    = I('id');
        $model = D(CONTROLLER_NAME);
        $data  = $model->find($id);

        if (IS_POST) {
            $rules = array(
                array('title', 'require', '名称不能为空！'),
            );

            if (false === $data = $model->validate($rules)->create()) {
                $this->error($model->getError());
            }

            if (empty($id)) {
                $result = $model->add($data);

                if (false !== $result) {
                    $this->success('添加成功！', U('index'));
                } else {
                    $this->error('添加失败！' . $model->getError());
                }
            } else {
                $condition['id'] = $id;
                $result          = $model->where($condition)->save($data);

                if (false !== $result) {
                    $this->success('编辑成功！');
                } else {
                    $this->error('编辑失败！' . $model->getError());
                }
            }
        }

        $this->assign('id', $id);
        $this->assign('data', $data);
        $this->display();
    }

    /**
     * @api del-楼层删除
     */
    public function del()
    {
        $model = D(CONTROLLER_NAME);
        $id    = I('id');

        if (empty($id)) {
            $this->error('缺少参数！');
        }

        $result = $model->where(array('id' => $id))->delete();

        if (false !== $result) {
            $this->success('删除成功！');
        } else {
            $this->error('删除失败！');
        }
    }

}