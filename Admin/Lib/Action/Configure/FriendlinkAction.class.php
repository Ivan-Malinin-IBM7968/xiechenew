<?php
/*
 * create by ysh
 * 友情链接管理
 * 2013/5/3
 */
class FriendlinkAction extends CommonAction{
	
    function _filter(&$map){
		if($_POST['name']) {
			$map['name'] = array('like',"%".$_POST['name']."%");	
		}
    }

	
}