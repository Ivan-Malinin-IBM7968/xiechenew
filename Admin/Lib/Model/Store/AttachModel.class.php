<?php

// 附件模型
class AttachModel extends CommonModel {
    
    protected $dbName = 'tp_xieche';
    protected $tablePrefix = 'xc_';
    
	protected $_validate = array(
		array('name','require','名称必须'),
		);

	protected $_auto		=	array(
		array('create_time','time','function',self::MODEL_INSERT),
		array('update_time','time','function',self::MODEL_UPDATE),
		array('user_id','getMemberId','callback',self::MODEL_INSERT),
		);
}
?>