<?php

// 配置类型模型
class GroupModel extends CommonModel {
    
    protected $dbName = 'tp_bank_admin';
    protected $tablePrefix = 'xc_';
    
	protected $_validate = array(
		array('name','require','名称必须'),
		);

	protected $_auto		=	array(
        array('status',1,'string',self::MODEL_INSERT),
		array('create_time','time','function',self::MODEL_INSERT),
		);
}
?>