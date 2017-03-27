<?php
    //$config	= require './config.php';
    $array=array(
        'APP_AUTOLOAD_PATH'=>'@.TagLib',
        'SESSION_AUTO_START'=>true,
    	'USER_ID'=>'uid',
		'DB_TYPE'=>'mysql',
		'DB_HOST'=>'localhost',
		'DB_NAME'=>'tp_xieche',
		'DB_USER'=>'root',
		'DB_PWD'=>'123456',
		'DB_PORT'=>'3306',
		'DB_PREFIX'=>'xc_',
		'ALL_PS'=>'xieche123~!@',
		'__UPLOAD__' => __ROOT__.'/UPLOADS',
		'DB_FIELDS_CACHE'=>false,
    	'WEIXIN_TEMPLATE'=>array(
    		1=>'QD8Bb2ohPwUTYmR9faJWdxTlWd96Qnzd5v0Ov5AmFFc',
    	)

		//'SHOW_PAGE_TRACE'=>1,//显示调试信息
    	//'LAYOUT_ON'=>true,
		//'LAYOUT_NAME'=>'layout',

    );

	if(substr($_SERVER['REQUEST_URI'],1,3) !='App' && substr($_SERVER['REQUEST_URI'],1,10) !='Appandroid' && substr($_SERVER['REQUEST_URI'],1,10) !='appandroid' && substr($_SERVER['REQUEST_URI'],1,3) !='app' && substr($_SERVER['REQUEST_URI'],1,13) !='index.php/app' && substr($_SERVER['REQUEST_URI'],1,13) !='index.php/App' && substr($_SERVER['REQUEST_URI'],1,13) !='index.php/appandroid'){

		$array['URL_PATHINFO_DEPR'] = '-';
		$array['URL_HTML_SUFFIX'] = '.html';
		//$array['URL_HTML_SUFFIX'] = '';
	}

    //return array_merge($config,$array);
	return $array;
?>
