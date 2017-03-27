<?php
date_default_timezone_set('Asia/Shanghai');
error_reporting(7);
set_time_limit(5);
session_start();

$start_time=explode(" ",microtime());
$start_time=floatval($start_time[0])+floatval($start_time[1]);

if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
        $onlineip = getenv('HTTP_CLIENT_IP');
} elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
        $onlineip = getenv('HTTP_X_FORWARDED_FOR');
} elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
        $onlineip = getenv('REMOTE_ADDR');
} elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
        $onlineip = $_SERVER['REMOTE_ADDR'];
}

preg_match("/[\d\.]{7,15}/", $onlineip, $onlineipmatches);
$ip = $onlineipmatches[0] ? $onlineipmatches[0] : '';
unset($onlineipmatches,$onlineip);
if(!$ip){
    //exit("服务器取不到您的IP");
}
require_once("config.php");

require ($config["lib_path"].'/class.db.php');




$db=new mysql($db_config);

$db_bbs=new mysql($db_bbs_config);


$ip=$db->safe($ip);

define("ip",$ip);
$ips=explode(".",ip);
list($nowm, $now) = explode(" ", microtime());
define("now",$now);



require($config["lib_path"].'/class.memcache.php');
require($config["lib_path"].'/class.template.php');


extract($_root_init_table);
$checksql="SELECT COUNT(*) FROM {$table_qg}";
$table_to_create=end(explode(".",$table_qg));
/*
$createtablesql="CREATE TABLE IF NOT EXISTS `{$table_to_create}` (
  `qid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `user_name` char(20) NOT NULL,
  `last_ip` char(15) NOT NULL,
  `truename` tinyint(1) NOT NULL,
  `tel` tinyint(1) NOT NULL,
  `qg_time` int(10) unsigned NOT NULL,
  `qg_microtime` float(9,9) unsigned NOT NULL,
  `acceptad` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`qid`),
  KEY `user_id` (`user_id`),
  KEY `qg_time` (`qg_time`),
  KEY `qg_microtime` (`qg_microtime`),
  KEY `last_ip` (`last_ip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1";
*/
//路径


$_root_init_class=Array(
"mc"=>$mc,
"t"=>$t,
"db"=>$db,
);
require "lib/class.root.php";
require($config["lib_path"].'/function.php');

$qgsid = substr($_COOKIE['qgsid'],0,32);
if(!$qgsid or strlen($qgsid)!=32){
        $qgsid=md5(microtime().$ip.mt_rand(10000,99999));
        setcookie('qgsid', $qgsid, 2145801600, "/", ".xieche.net");
}
$qgsid = $db->safe($qgsid);

//$session_in_mc=$mc->del($ip.$qgsid);

//$sess=get_session();
//$sess[goto_page]="http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
//echo '<pre>';print_r($_SESSION);

$session_in_mc=$mc->get($ip.$qgsid);
$discuz_uid=$session_in_mc[uid];
$discuz_user=$session_in_mc[username];
$discuz_mobile=$session_in_mc[mobile];
$discuz_cardid=$session_in_mc[cardid];
$discuz_email=$session_in_mc[email];
/*
$discuz_uid = $_SESSION[uid];
$discuz_user=$_SESSION[username];
$discuz_mobile=$_SESSION[mobile];
$discuz_cardid=$_SESSION[cardid];
$discuz_email=$_SESSION[email];*/
if ($discuz_user){
    $have_username = true;
    $sess[username]=$discuz_user;
}else{
    $have_username = false;
    $sess[username] = "";
}
if ($discuz_mobile){
    $have_mobile = true;
    $sess[mobile]=$discuz_mobile;
}else{
    $have_mobile = false;
    $sess[mobile] = "";
}
if ($discuz_email){
    $have_email = true;
    $sess[email]=$discuz_email;
}else {
    $have_email = false;
    $sess[email] = "";
}

if($discuz_uid){
    $is_login=true;
    $sess[user_id]=$discuz_uid;
    $sess[user_name]=$discuz_user;
}else{
    $is_login=false;
    $sess[user_name]="";
}

$t->set("have_username",$have_username,true);
$t->set("have_mobile",$have_mobile,true);
$t->set("have_email",$have_email,true);
$t->set("is_login",$is_login,true);

$user=$db->query("SELECT * FROM {$table_user_info} WHERE user_id='$discuz_uid' LIMIT 1","assoc");

if($user[acceptad]){
    $user[acceptad]=" checked";
}
$t->set("joined",$joined,true);

$have_profile=false;

if(!$user){
    $user=array(
        "user_id"=>"",
        "user_name"=>"",
        "truename"=>"",
        "tel"=>"",
        "idcard"=>"",
        "address"=>"",
        "zipcode"=>"",
        "acceptad"=>" checked",
        );
}
if( strlen(trim($user[truename]))>=2 and strlen(trim($user[tel]))>=6 and $user[brand_id]>0 and $user[series_id]>0 and $user[model_id]>0 and strlen(trim($discuz_user))>=1 and strlen(trim($discuz_mobile))>=6 and strlen(trim($discuz_email))>=6){
    $have_profile=true;
}
if($discuz_uid and !$user[tel]){
    $user[tel]=$db_bbs->query("SELECT mobile FROM tp_xieche.xc_member WHERE uid='$discuz_uid' LIMIT 1","1");
}
$t->set("user",$user);


$qg_start=false;

/*
$week=date("N",$now);
$hour=date("G",$now);
$the_s_time="2011-1-19";
$the_s_time="2011-2-17";
if($now>=strtotime($the_s_time) and $now<=strtotime($the_e_time)){
        if($week==3 or $week==7){
                if($hour>=14 AND $hour<=23){
                        $qg_start=true;
                }
        }
}
*/












//$qgdays[]="2011-10-08 10:00:00";

//$qgdays[]="2011-10-11 21:00:00";
//$qgdays[]="2011-10-14 21:00:00";
//$qgdays[]="2011-10-18 21:00:00";
//$qgdays[]="2011-10-21 21:00:00";
//$qgdays[]="2011-10-25 21:00:00";
//$qgdays[]="2012-09-03 20:00:00";
//$qgdays[]="2012-09-04 20:00:00";
$qgdays[]="2012-09-05 14:00:00";

$nowday=strtotime(date("Y-m-d",$now));//今天日期
foreach($qgdays as $qgday){
	$shijianduan=3;
	$this_start=strtotime($qgday);
	$this_end=strtotime($qgday)+3600*$shijianduan;//每次抢楼都固定是两小时
	$this_day=strtotime(date("Y-m-d",$this_start));//抢购日期表里面的日期
	//if($nowday>=$this_day){//今天比赛开始前，读取上一次的抢购信息
		$table_qg_read_list="tp_xieche.lifan_".date("Ymd",$this_start);
		//$table_qg_read_list="qianggou.lifan_20110202";
        //$db->queryorquery($checksql,$createtablesql);
	//}
	if($nowday<=$this_end){//如果今天小于等于本次循环的结束日期，如24号当天，满足此条件，则最新抢购时间是本次循环，中止。
		$the_s_time=$this_start;
		$the_e_time=$this_end;
		break;
	}


}

/*
echo date("Y-m-d H:i:s",$the_s_time);
echo date("Y-m-d H:i:s",$the_e_time);
*/












//页面上显示的最近一次抢购时间
foreach($qgdays as $qgday){
	$shijianduan=3;
	$this_start=strtotime($qgday);
	$this_end=strtotime($qgday)+3600*$shijianduan;
	$this_day=strtotime(date("Y-m-d",$this_start));
	if($nowday<=$this_end){
		$next_s_time=date("Y-m-d H:i:s",$this_start);
		$next_e_time=date("Y-m-d H:i:s",$this_end);
		break;
	}
}


$qg_end=false;
if(!$the_s_time or !$the_e_time){
	$the_s_time="------";
	$the_e_time="------";
	$qg_end=true;
}
if(!$next_s_time or !$next_e_time){
	$next_s_time="------";
	$next_e_time="------";
	$qg_end=true;
}








if($now>=$the_s_time and $now<=$the_e_time){
	$qg_start=true;
}

$show_list=true;
if($now>=$the_e_time){
	$qg_end=true;
	//$show_list=true;
}
if($now<$the_s_time){
	//$qg_end=true;
	//$show_list=true;
}
$qg_start=false;
if ($qg_start == false){
    $remaintime = $the_s_time - $now;
	$remaintime = 0;
    $t->set("remaintime",$remaintime,true);
}
$t->set("have_profile",$have_profile,true);
$ipvcode=md5('zkjdfksha8wjh'.$discuz_uid.$ip);