<?php
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


$ip=$db->safe($ip);

define("ip",$ip);
$ips=explode(".",ip);
list($nowm, $now) = explode(" ", microtime());
define("now",$now);



//require($config["lib_path"].'/class.memcache.php');
require($config["lib_path"].'/class.template.php');






$_root_init_table=Array(
"table_qg"=>"tp_xieche.cbf125_qg",
);

extract($_root_init_table);


//路径





$_root_init_class=Array(
"mc"=>$mc,
"t"=>$t,
"db"=>$db,
);
require "lib/class.root.php";
require($config["lib_path"].'/function.php');



//$sess=get_session();
//$sess[goto_page]="http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
$sid = isset($_GET['sid']) ? $_GET['sid'] :(isset($_POST['sid']) ? $_POST['sid'] :$_SESSION['sid']);

$discuz_uid = $_SESSION['uid'];
$discuz_uid=$discuz_uid+1-1;
//$db->query("UPDATE xc_xsession SET lastactivity=$now WHERE uid='$discuz_uid' LIMIT 1");
//$discuz_user=$db->query(" SELECT username FROM xc_xsession WHERE s_id='$sid' LIMIT 1","1");
$discuz_user = $_SESSION['username'];
if($discuz_uid && $discuz_user){
    $is_login=true;
    $sess[user_id]=$discuz_uid;
    $sess[user_name]=$discuz_user;
}else{
    $is_login=false;	
}
$t->set("is_login",$is_login,true);

$user=$db->query("SELECT * FROM {$table_qg} WHERE user_id='$discuz_uid' LIMIT 1","assoc");
$joined=false;
if($user[qg_time]){
    $joined=true;
}
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
if( strlen(trim($user[truename]))>=2 and strlen(trim($user[tel]))>=6 and strlen(trim($user[idcard]))>=13 and strlen(trim($user[address]))>=6 and strlen(trim($user[zipcode]))>=5){
    $have_profile=true;
}
if($discuz_uid and !$user[tel]){
    $user[tel]=$db->query("SELECT mobile FROM xc_member WHERE uid='$discuz_uid' LIMIT 1","1");
}
$t->set("user",$user);


$qg_start=false;

$the_s_time="2012-8-20 20:00:00";
$the_e_time="2012-8-22 22:00:00";

if($now>=strtotime($the_s_time) and $now<=strtotime($the_e_time)){
    $qg_start=true;
}

$t->set("have_profile",$have_profile,true);