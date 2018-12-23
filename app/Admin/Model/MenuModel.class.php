<?php

namespace Admin\Model;

use Think\Model;

class  MenuModel extends Model
{
    protected function _before_insert($data, $options)
    {
        $result = $this->where(array('category_id' => $data['category_id']))->find();
        if (!empty($result)) {
            $this->error = '该分类ID已经存在。';

            return false;
        }

        $result = $this->where(array('identity' => $data['identity']))->find();
        if (!empty($result)) {
            $this->error = '该唯一标识已经存在。';

            return false;
        }
    }

    protected function _before_update($data, $options)
    {
        $result = $this->where(array('category_id' => $data['category_id']))->find();
        if (!empty($result) && $result['id'] != $data['id']) {
            $this->error = '该分类ID已经存在。';

            return false;
        }

        $result = $this->where(array('identity' => $data['identity']))->find();
        if (!empty($result) && $result['id'] != $data['id']) {
            $this->error = '该唯一标识已经存在。';

            return false;
        }
    }

    protected function _after_insert($data, $options)
    {
        return $this->_after($data);
    }

    protected function _after_update($data, $options)
    {
        return $this->_after($data);
    }

    protected function _after_delete($data, $options)
    {
        $menu_id = $data['id'];
        M('MenuTags')->where(array('menu_id' => $menu_id))->delete();
        M('MenuBrand')->where(array('menu_id' => $menu_id))->delete();
        M('MenuGoods')->where(array('menu_id' => $menu_id))->delete();
    }

    private function _after($data)
    {
        //过滤数组空元素并重建索引
        $tags  = array_values(array_filter(I('tags')));
        $brand = array_values(array_filter(I('brand')));
        $goods = array_values(array_filter(I('goods')));

        $menu_id = $data['id'];
        if ($tags) {
            foreach ($tags as $key => &$val) {
                $val['menu_id'] = $menu_id;
                unset($val);
            }

            M('MenuTags')->where(array('menu_id' => $menu_id))->delete();
            M('MenuTags')->addAll($tags);
        }

        if ($brand) {
            foreach ($brand as $key => &$val) {
                $val['menu_id'] = $menu_id;
                unset($val);
            }

            M('MenuBrand')->where(array('menu_id' => $menu_id))->delete();
            M('MenuBrand')->addAll($brand);
        }

        if ($goods) {
            foreach ($goods as $key => &$val) {
                $val['menu_id'] = $menu_id;
                unset($val);
            }

            M('MenuGoods')->where(array('menu_id' => $menu_id))->delete();
            M('MenuGoods')->addAll($goods);
        }
    }

    public function updateMenuCache()
    {
        $list = $this->where(array('status' => 1))->order('sort asc')->field('id,title,sort,category_id,identity')->select();

        if (empty($list)) {
            return;
        }

        foreach ($list as &$category) {
            $condition['menu_id'] = $category['id'];

            $category['tags']  = M('MenuTags')->where($condition)->field('title,url')->select();
            $category['brand'] = M('MenuBrand')->where($condition)->field('title,url')->select();
            $category['goods'] = M('MenuGoods')->where($condition)->field('goods_id,title,seo_title,image,url')->select();
        }

        $cache = getRedis();
        $key   = 'PCMenu:list';
        $cache->set($key, json_encode($list));
    }
}
