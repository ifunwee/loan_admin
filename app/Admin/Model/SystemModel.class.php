<?php

namespace Admin\Model;

use Think\Model;

class SystemModel extends Model
{

    public function __construct()
    {
        /**
         * 实现父类model构造
         *
         * @param string $name        模型名称 --表名
         * @param string $tablePrefix 表前缀
         * @param mixed  $connection  数据库连接信息
         */
        parent::__construct('system');
    }

    /**
     * 获取所有数据
     * User：xiehongfei
     * Date：2015.07.10
     *
     * @param $name int 名称
     * @param $pass strint 密码
     * @param $sex  int 性别
     *
     * @return  string
     */
    public function getAccess()
    {
        return $this->select();
        // return 1;
    }
}