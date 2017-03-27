<?php

/*
 * 商铺管理模型
 * create by yyc
 * time 12/05/26
 *
 */

class NoticeModel extends CommonModel {
    
    //protected $dbName = 'tp_xieche';
    //protected $tablePrefix = 'xc_';

    protected $_auto = array(
        array('update_time', 'time', 3, 'function'),
		//暂时先默认为4S店
    );
    
}

