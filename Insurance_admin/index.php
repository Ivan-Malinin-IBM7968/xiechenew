<?php

	$url = $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];//csml.com/bank_admin/index.php
	if($_SERVER['HTTP_HOST'] == 'www.xieche.net'){
		
		$url_str = $_SERVER['REQUEST_URI'];
		Header( "HTTP/1.1 301 Moved Permanently" ) ;
		header("Location:http://www.xieche.com.cn".$url_str);
		exit;
	}

    define('THINK_PATH','../ThinkPHP/');
    //define('RUNTIME_ALLINONE',1);
    define('SITE_ROOT' ,substr(dirname(__FILE__),0,-5));//硬盘目录
    //define('WEB_ROOT',"http://127.0.0.1/xieche_tp3.0/");//网址
	define('WEB_ROOT',"http://www.xieche.com.cn/");//网址
    define('APP_NAME','Store');
    define('APP_PATH','./');
    define('APP_DEBUG',1);
    define('APP_PUBLIC_PATH',WEB_ROOT.'Public/Admin');
	//定义常量
	define('POINT_ADD',"500");//订单完成时，满足积分增加条件，积分增加数

    require(THINK_PATH.'ThinkPHP.php');
?>