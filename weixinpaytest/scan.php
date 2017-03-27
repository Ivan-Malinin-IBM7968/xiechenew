<?php
error_reporting(0);
include_once("function.php");
/*AJAX判断是否扫码
*return:1.未扫2.已扫
*
*/

global $mysql;
set_time_limit(0);
$begin = time();

while(true){
	$now = time();

	/* ================================
	 * 这里查询用户是否有扫一扫的行为
	 ================================= */
	$membercoupon_id = $_REQUEST['id'];
	$info = get_membercoupon($membercoupon_id);

	if($info['is_scan'] == 1){
		$res = "is_scan";

		break;  //如果有扫一扫行为断开;
	}

	if($now > $begin + 20){
		$res = "test timeout";

		break;
	}else{
		sleep(3);
	}
}

echo json_encode($res);exit;

?>