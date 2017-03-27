<?php

// 附件模型
class FriendlinkModel extends CommonModel {
    
    protected $dbName = 'tp_xieche';
    protected $tablePrefix = 'xc_';
    
	

	protected $_auto = array(
		array('addtime','time',1,'function'),
		);
}
?>