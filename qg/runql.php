<?php
date_default_timezone_set('Asia/Shanghai');
$db = mysql_connect("localhost", "root","123456");
mysql_query("SET NAMES 'utf8'",$db);
mysql_select_db("tp_xieche",$db);

//$user_arr = array(114,397,395,399,393,396,3,389,394,398);
$user_arr = array(6,7,8,16,15,28,27,45,53,43,34,399,393,395,114,40,37,30,24,9,52,54);
for($i=0;$i<2000;$i++){
	$randid = rand(0,21);
	$uid = $user_arr[$randid];
	$userinfo = mysql_query("select * from xc_member where uid='".$uid."' LIMIT 1",$db);
	$member = mysql_fetch_array($userinfo);
	
	$us=mysql_query("select * from lifan_20120905 where user_id='".$uid."' order by qid DESC LIMIT 1",$db);
	$row=mysql_fetch_array($us);
	

	$s_rand = rand(0,3);
	sleep($s_rand);
	
	if($row['qg_time']<(time()-15) ){
		list($nowm, $now) = explode(" ", microtime());
		mysql_query("insert into lifan_20120905 set user_id='".$member['uid']."',user_name='".$member['username']."',last_ip='".$member['ip']."',qg_time='".$now."',qg_microtime='".$nowm."'",$db);
	}
	
}
?>