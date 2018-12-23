<?php

namespace Admin\Controller;

use Common\Controller\CommonController;
use Think\Page;

class MenuController extends CommonController
{
    /**
     * @api index-菜单列表
     */
    public function index()
    {
        $model = D(CONTROLLER_NAME);
        $count = $model->count();
        $p     = I('p');
        $sort  = I('sort');
        $limit = 10;
        $page  = new Page($count, $limit);
        $list  = $model->order('sort asc, id asc')->page($p, $limit)->select();

        //排序
        if (IS_POST) {
            $sort && $this->_sort($sort);
        }

        $this->assign('page', $page->show());
        $this->assign('list', $list);
        $this->display();
    }

    /**
     * @api edit-菜单编辑
     */
    public function edit()
    {
        $id    = I('id');
        $model = D(CONTROLLER_NAME);
        $data  = $model->find($id);
        if (!empty($data)) {
            $condition['menu_id'] = $data['id'];
            $tags                 = M('MenuTags')->where($condition)->select();
            $brand                = M('MenuBrand')->where($condition)->select();
            $goods                = M('MenuGoods')->where($condition)->select();

            $data['tags']  = $tags;
            $data['brand'] = $brand;
            $data['goods'] = $goods;
        }

        if (IS_POST) {
            $rules = array(
                array('title', 'require', '标题不能为空！'),
                array('identity', 'require', '唯一标识不能为空！'),
                array('category_id', 'require', '分类ID不能为空！'),
            );

            if (false === $data = $model->validate($rules)->create()) {
                $this->error($model->getError());
            }

            if (empty($id)) {
                $result = $model->add($data);

                if (false !== $result) {
                    $model->updateMenuCache();
                    $this->success('添加成功！', U('index'));
                } else {
                    $this->error('添加失败！' . $model->getError());
                }
            } else {
                $condition['id'] = $id;
                $result          = $model->where($condition)->save($data);

                if (false !== $result) {
                    $model->updateMenuCache();
                    $this->success('编辑成功！', U('index'));
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
     * @api del-菜单删除
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
