<?php
header('Content-Type:text/html;charset=utf-8');
require dirname(__FILE__).'/PushMsg.php';
$pushmsg = new PushMsg ();
//$title, $desc, $url, $imageUrl, $authType, $toUserId      $authType=loginAuthʱ���û�������ӻὫ����auth_code�����Ի�ȡ�û���Ϣ
if(!$_REQUEST['title']||!$_REQUEST['desc']||!$_REQUEST['url']||!$_REQUEST['imageUrl']||!$_REQUEST['authType']||!$_REQUEST['toUserId']){
	$data = array('status'=>0,'message'=>'Argument is missing');
	echo json_encode($data);
}else{
	$pushmsg->checkPush($_REQUEST['title'],$_REQUEST['desc'],$_REQUEST['url'],$_REQUEST['imageUrl'],$_REQUEST['authType'],$_REQUEST['toUserId']);
	$data = array('status'=>1,'message'=>'Push the success');
	echo json_encode($data);
}
?>