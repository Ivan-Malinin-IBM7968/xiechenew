<?php
	
	if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown")) 
		$ip = getenv("HTTP_CLIENT_IP"); 
	else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown")) 
		$ip = getenv("HTTP_X_FORWARDED_FOR"); 
	else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown")) 
		$ip = getenv("REMOTE_ADDR"); 
	else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown")) 
		$ip = $_SERVER['REMOTE_ADDR']; 
	else 
		$ip = "unknown"; 


	$conn = @mysql_connect('localhost','root','123456') or die("error");
	mysql_select_db('tp_xieche',$conn);
	mysql_query("SET NAMES utf-8");
	$addtime = date("Y-m-d H:i:s",time());

	
	if(eregi('iPhone',$_SERVER['HTTP_USER_AGENT']) ) {
		header("Location:https://itunes.apple.com/cn/app/zong-heng-xie-che-qi-che-shou/id588144466?mt=8");
		$sql = "INSERT INTO xc_applocation SET agent='{$_SERVER[HTTP_USER_AGENT]}' ,ip='{$ip}' , addtime='{$addtime}',loaction_app='iphone' ";
		$res =mysql_query($sql);
		exit();
	}
	if(eregi('ipad',$_SERVER['HTTP_USER_AGENT']) ) {
		header("Location:https://itunes.apple.com/cn/app/zong-heng-xie-che-qi-che-shou/id588144466?mt=8");
		$sql = "INSERT INTO xc_applocation SET agent='{$_SERVER[HTTP_USER_AGENT]}' ,ip='{$ip}' , addtime='{$addtime}',loaction_app='ipad' ";
		$res =mysql_query($sql);
		exit();
	}
	if(eregi('android',$_SERVER['HTTP_USER_AGENT']) ) {
		header("Location:http://www.xieche.com.cn/xc.apk");
		$sql = "INSERT INTO xc_applocation SET agent='{$_SERVER[HTTP_USER_AGENT]}' ,ip='{$ip}' , addtime='{$addtime}',loaction_app='android' ";
		$res =mysql_query($sql);
		exit();
	}

	header("Location:http://www.xieche.com.cn");
	$sql = "INSERT INTO xc_applocation SET agent='{$_SERVER[HTTP_USER_AGENT]}' ,ip='{$ip}' , addtime='{$addtime}',loaction_app='web' ";
	$res =mysql_query($sql);
	
	
?>