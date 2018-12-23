<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/4/29
 * Time: 9:30
 */

namespace Admin\Model;

use Think\Model\RelationModel;

class UserRelationModel extends RelationModel
{
    protected $tableName = 'user';
    //定义表名称
    //定义关联关系

    protected $_link = array(
        'role' => array(
            'mapping_type'   => self::MANY_TO_MANY,
            'foreign_key'    => 'user_id',
            'relation_key'   => 'role_id',
            'relation_table' => 'role_user',
            'mapping_fields' => 'id,name,remark',
        ),
    );

}