<?php

// 附件模型
class ArticleModel extends CommonModel {
    
    protected $dbName = 'tp_xieche';
    protected $tablePrefix = 'xc_';
    
	

	protected $_auto = array(
		array('create_time','time','function',self::MODEL_INSERT),
		);
}
?>