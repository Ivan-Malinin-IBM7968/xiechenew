<?php

/*
 * 商铺管理模型
 * create by yyc
 * time 12/05/26
 *
 */

class ShopModel extends CommonModel {
    
    protected $dbName = 'tp_xieche';
    protected $tablePrefix = 'xc_';

    protected $_auto = array(
        array('create_time', 'time', 1, 'function'),
        array('update_time', 'time', 3, 'function'),
		array('start_time', 'strtotime', 3, 'function'),
        array('end_time', 'strtotime', 3, 'function'),
		//暂时先默认为4S店
		array('shop_class','1',1),
    );
    
    protected $_validate = array(
        array('shop_name','require','必须填写商铺名称！',1),

    );

	public $_link = array(
		'Shopdetail'=> HAS_ONE,
	);

}

