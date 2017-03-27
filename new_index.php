<?php
mb_internal_encoding("UTF-8");
/*
if($_SERVER['REQUEST_URI'] == '/index.php' || $_SERVER['REQUEST_URI'] == '/index' ||  $_SERVER['REQUEST_URI'] == '/index.html'){
	$layout = 'xc';
	Header( "HTTP/1.1 301 Moved Permanently");
	header("Location:http://".$_SERVER['SERVER_NAME']);
}

if($_SERVER['HTTP_HOST'] == 'xieche.net' || $_SERVER['HTTP_HOST'] == 'xieche.com.cn'){
	$layout = 'xc';
	$url_str = $_SERVER['REQUEST_URI'];
	Header( "HTTP/1.1 301 Moved Permanently" ) ;
	header("Location:http://www.xieche.com.cn".$url_str);
}

if(substr($_SERVER['REQUEST_URI'],1,3) !='App' && substr($_SERVER['REQUEST_URI'],1,10) !='Appandroid' && substr($_SERVER['REQUEST_URI'],1,10) !='appandroid' && substr($_SERVER['REQUEST_URI'],1,3) !='app' && substr($_SERVER['REQUEST_URI'],1,13) !='index.php/app' && substr($_SERVER['REQUEST_URI'],1,13) !='index.php/App' && substr($_SERVER['REQUEST_URI'],1,13) !='index.php/appandroid'){
	define('APP_DEBUG',FALSE);
	$url = $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
	if($_SERVER['HTTP_HOST'] == 'www.xieche.net'){
		$url_str = $_SERVER['REQUEST_URI'];
		Header( "HTTP/1.1 301 Moved Permanently" ) ;
		header("Location:http://www.xieche.com.cn".$url_str);
		exit;
	}
}else{
	define('APP_DEBUG',TRUE);
}
*/

// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');

// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
define('APP_DEBUG',True);

//项目根目录
define('ROOT_PATH','./');

// 定义应用目录
define('APP_PATH','./Application/');

//定义常量
define('WEB_ROOT',"http://www.xieche.com.cn");//网址

// 引入ThinkPHP入口文件
require './ThinkPHP_new/ThinkPHP.php';

