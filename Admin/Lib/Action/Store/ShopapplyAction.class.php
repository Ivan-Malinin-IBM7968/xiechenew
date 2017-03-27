<?php
/*
 * create by ysh
 * 商家申请
 * 2013/4/17
 */
class ShopapplyAction extends CommonAction{
	
    function _filter(&$map){
		if($_POST['name']) {
			$map['shop_name'] = array('like',"%".$_POST['shop_name']."%");	
		}
    }

	
}