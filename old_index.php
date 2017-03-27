<?php

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

/*
if($_SERVER['HTTP_HOST'] == 'xieche.net' || $_SERVER['HTTP_HOST'] == 'xieche.com.cn'){
	$layout = 'xc';
	$url_str = $_SERVER['REQUEST_URI'];
	Header( "HTTP/1.1 301 Moved Permanently" ) ;
	header("Location:http://www.xieche.com.cn".$url_str);
}
*/

if($_SERVER['HTTP_HOST'] == 'baoyang.pahaoche.com'){
	$layout = 'pa';
	$topname = 'new_pa';
	$url_str = $_SERVER['REQUEST_URI'];
	
}else{
	$layout = 'xc';
	$topname = 'new_2';
}

if($_SERVER['HTTP_HOST'] == 'pa.xieche.com.cn'){
	$pabanner = "pa";
	$url_str = $_SERVER['REQUEST_URI'];
}else{
	$pabanner = "xc";
}
/*
if(substr($_SERVER['REQUEST_URI'],1,8) == 'Pamobile'){
	$str = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"];
	$string = preg_replace('/\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|$!:,.;]*[A-Z0-9+&@#\/%=~_|$]/i', '', $str);
	$url = str_replace($string,'',$str);
	if($string){
		echo $string.'</br>';
		echo "请不要攻击我们网站";
		exit;
	}
}
*/

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

define('PA_BANNER', $pabanner);

define('APP_NAME', 'xiecheapp');
define('APP_PATH', 'xiecheapp/');
define('TOP_CSS',$layout);//网址
define('TOP_NAME',$topname);//网址
//define('THINK_PATH', 'ThinkPHP');
//定义常量
define('WEB_ROOT',"http://www.xieche.com.cn");//网址
define('REGISTER_CODE',"abcd1234");//推荐注册码
define('UID_ADD',"16324");//推荐注册时Uid增加数
//开启调试模式
//加载框架入口文件
require('./ThinkPHP/ThinkPHP.php');
