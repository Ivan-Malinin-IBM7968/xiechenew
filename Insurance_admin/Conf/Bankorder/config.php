<?php
$config	= require '../config.php';
//var_dump($config);
$array = array(
    'DB_HOST'           => 'localhost',
    'DB_USER'           =>'root',
    'DB_PWD'            =>'123456',
    'DB_NAME'           =>'tp_xieche',
    'DB_PORT'           =>'3306',
	'DB_PREFIX'         =>'xc_',
	
    'COMPLAIN_STATUS'  => array(//投诉状态
                                '1'=>'新投诉',
                                '2'=>'处理中',
                                '3'=>'已处理',
                                '4'=>'已返分'
                            ),

    );


    return array_merge($config,$array);
?>
