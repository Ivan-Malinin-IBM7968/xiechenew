<?php
error_reporting(0);
include_once("function.php");
/*AJAX�ж��Ƿ�ɨ��
*return:1.δɨ2.��ɨ
*
*/

global $mysql;
set_time_limit(0);
$begin = time();

while(true){
	$now = time();

	/* ================================
	 * �����ѯ�û��Ƿ���ɨһɨ����Ϊ
	 ================================= */
	$membercoupon_id = $_REQUEST['id'];
	$info = get_membercoupon($membercoupon_id);

	if($info['is_scan'] == 1){
		$res = "is_scan";

		break;  //�����ɨһɨ��Ϊ�Ͽ�;
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