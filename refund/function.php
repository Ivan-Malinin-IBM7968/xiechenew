<?php
error_reporting(0);
include_once("mysqli.php");
$mysql=new _xq_mysqli;
$mysql->config();

function get_order($orderid){
	global $mysql;
	$sql = "select * from xc_order where id='".$orderid."' ";
	$order_info=$mysql->query($sql,'assoc');
	return $order_info;
}
function get_orderid($orderid){
	$orderid = substr(trim($orderid),0,-1);
	$orderid = $orderid - 987;
	return $orderid;
}
function get_trade_no($orderid){
	global $mysql;
	$sql = "select * from xc_orderpay where order_id='".$orderid."' ";
	$orderpay_info=$mysql->query($sql,'assoc');
	return $orderpay_info['trade_no'];
}
function get_orderid_from_orderpay($trade_no){
	global $mysql;
	$sql = "select * from xc_orderpay where trade_no='".$trade_no."' ";
	$orderpay_info=$mysql->query($sql,'assoc');
	return $orderpay_info['order_id'];
}
function save_order_pay($order_id,$trade_no,$total_fee=0,$trade_status=''){
	global $mysql;
	$order_id = get_orderid($order_id);
	$now = time();
	$sql = "INSERT INTO xc_orderpay SET order_id='".$order_id."',trade_no='".$trade_no."',total_fee='".$total_fee."',trade_status='".$trade_status."',create_time='".$now."'";
	$mysql->query($sql);
}
function save_orderrefund($batch_no,$success_num,$result_details){
	global $mysql;
	$now = time();
	$sql = "INSERT INTO xc_orderrefund SET batch_no='".$batch_no."',success_num='".$success_num."',result_details='".$result_details."'";
	$mysql->query($sql);
}
function update_order_state_refund($order_id){
	global $mysql;
	$now = time();
	$sql = "UPDATE xc_order SET pay_status='3' WHERE id='".$order_id."' LIMIT 1";
	$mysql->query($sql);
}
function update_order_state($order_id,$pay_status){
	global $mysql;
	$order_id = get_orderid($order_id);
	$rand_str = randString(9,1);
	$now = time();
	$sql = "UPDATE xc_order SET pay_status='".$pay_status."',pay_time='".$now."',order_verify='".$rand_str."' WHERE id='".$order_id."' LIMIT 1";
	$mysql->query($sql);
}
function update_coupon_count($coupon_id){
	global $mysql;
	$coupon_sql = "UPDATE xc_coupon SET pay_count=pay_count+1 where id='".$coupon_id."'";
	$mysql->query($coupon_sql);
}
function send_sms($order){
	$data['phones'] = $order['mobile'];
	$data['content'] = "����֧���ѳɹ���(".$order['order_name'].")����ƾ���������̻�������:".$order['order_verify'];
	$send_time = time();
	curl_sms($data);
	$sql = "INSERT INTO xc_sms SET phones='".$data['phones']."',sendtime='".$send_time."',content='".$data['content']."' ";
	$mysql->query($sql);
}
//���Žӿ�
function curl_sms($post = '' , $charset = null ){
	$datamobile = array('130','131','132','155','156','186','185');
	$submobile = substr($post['phones'],0,3);
	$post['content'] = str_replace("��ͨ", "��_ͨ", $post['content']);
	if(in_array($submobile,$datamobile)){
		$post['content'] = $post['content']."  �ظ�TD�˶�";
	}

	$xml_data="<?xml version=\"1.0\" encoding=\"UTF-8\"?><message><account>dh21007</account><password>49e96c9b07f0628fec558b11894a9e8f</password><msgid></msgid><phones>$post[phones]</phones><content>$post[content]</content><subcode></subcode><sendtime></sendtime></message>";

	$url = 'http://www.10690300.com/http/sms/Submit';
	$curl = curl_init($url );
	if( !is_null( $charset ) ){
		curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-type:text/xml; charset=utf-8"));
	}
	if( !empty( $post ) ){
		$xml_data = 'message='.urlencode($xml_data);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $xml_data);
	}
	curl_setopt($curl, CURLOPT_TIMEOUT, 10);
	curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	$res = curl_exec($curl);
	curl_close($curl);
	//var_dump($res);
	return $res;
}

/**
 +----------------------------------------------------------
 * ��������ִ����������Զ���������
 * Ĭ�ϳ���6λ ��ĸ�����ֻ�� ֧������
 +----------------------------------------------------------
 * @param string $len ����
 * @param string $type �ִ�����
 * 0 ��ĸ 1 ���� ���� ���
 * @param string $addChars �����ַ�
 +----------------------------------------------------------
 * @return string
 +----------------------------------------------------------
 */
function randString($len=6,$type='',$addChars='') {
	$str ='';
	switch($type) {
		case 0:
			$chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'.$addChars;
			break;
		case 1:
			$chars= str_repeat('0123456789',3);
			break;
		case 2:
			$chars='ABCDEFGHIJKLMNOPQRSTUVWXYZ'.$addChars;
			break;
		case 3:
			$chars='abcdefghijklmnopqrstuvwxyz'.$addChars;
			break;
		case 4:
			$chars = "�����ҵ�������ʱҪ��������һ�ǹ�������巢�ɲ���ɳ��ܷ������˲����д�����������Ϊ����������ѧ�¼��ظ���ͬ����˵�ֹ����ȸ�����Ӻ������С��Ҳ�����߱������������ʵ�Ҷ������ˮ������������������ʮս��ũʹ��ǰ�ȷ���϶�·ͼ�ѽ�������¿���֮��ӵ���Щ�������¶�����������˼�����ȥ�����������ѹԱ��ҵ��ȫ�������ڵ�ƽ��������ëȻ��Ӧ�����������ɶ������ʱ�չ�������û���������ϵ������Ⱥͷ��ֻ���ĵ����ϴ���ͨ�����Ͽ��ֹ�����������ϯλ����������ԭ�ͷ�������ָ��������ںܽ̾��ش˳�ʯǿ�������Ѹ���ֱ��ͳʽת�����о���ȡ������������־�۵���ôɽ�̰ٱ��������汣��ί�ָĹܴ�������֧ʶ�������Ϲ�רʲ���;�ʾ������ÿ�����������Ϲ����ֿƱ�������Ƹ��������������༯������װ����֪���е�ɫ����ٷ�ʷ����������֯�������󴫿ڶϿ��ɾ����Ʒ�вβ�ֹ��������ȷ������״��������Ŀ����Ȩ�Ҷ����֤��Խ�ʰ��Թ�˹��ע�첼�����������ر��̳�������ǧʤϸӰ�ð׸�Ч���ƿ��䵶Ҷ������ѡ���»������ʼƬʩ���ջ�������������ҩ����Ѵ��ʿ���Һ��׼��ǽ�ά�������������״����ƶ˸������ش幹���ݷǸ���ĥ�������ʽ���ֵ��̬���ױ�������������̨���û������ܺ���ݺ����ʼ��������Ͼ��ݼ���ҳ�����Կ�Ӣ��ƻ���Լ�Ͳ�ʡ���������ӵ۽�����ֲ������������ץ���縱����̸Χʳ��Դ�������ȴ����̻����������׳߲��зۼ������濼�̿�������ʧ��ס��֦�־����ܻ���ʦ������Ԫ����ɰ�⻻̫ģƶ�����ｭ��Ķľ����ҽУ���ص�����Ψ�们վ�����ֹĸ�д��΢�Է�������ĳ�����������൹�������ù�Զ���Ƥ����ռ����Ȧΰ��ѵ�ؼ��ҽ��ƻ���������ĸ�����ֶ���˫��������ʴ����˿Ůɢ��������Ժ�䳹����ɢ�����������������Ѫ��ȱ��ò�����ǳ���������������̴���������������Ͷ��ū����ǻӾഥ�����ͻ��˶��ٻ����δͻ�ܿ���ʪƫ�Ƴ�ִ����կ�����ȶ�Ӳ��Ŭ�����Ԥְ������Э�����ֻ���ì������ٸ�������������ͣ����Ӫ�ո���Ǯ��������ɳ�˳��ַ�е�ذ����İ��������۵��յ���ѽ�ʰɿ��ֽ�������������ĩ������ڱ������������������𾪶ټ�����ķ��ɭ��ʥ���մʳٲ��ھؿ��������԰ǻ�����������������ӡ�伱�����˷�¶��Ե�������������Ѹ��������ֽҹ������׼�����ӳ��������ɱ���׼辧�尣ȼ��������ѿ��������̼��������ѿ����б��ŷ��˳������͸˾Σ������Ц��β��׳����������������ţ��Ⱦ�����������Ƽ�ֳ�����ݷô���ͭ��������ٺ�����Դ��ظ���϶¯����úӭ��ճ̽�ٱ�Ѯ�Ƹ�������Ը���������̾䴿������������³�෱�������׶ϣ�ذܴ�����ν�л��ܻ���ڹ��ʾ����ǳ���������Ϣ������������黭�������������躮ϲ��ϴʴ���ɸ���¼������֬ׯ��������ҡ���������������Ű²��ϴ�;�������Ұ�ž�ıŪ�ҿ�����ʢ��Ԯ���Ǽ���������Ħæ�������˽����������������Ʊܷ�������Ƶ�������Ҹ�ŵ����Ũ��Ϯ˭��л�ڽ���Ѷ���鵰�պ������ͽ˽������̹����ù�����ո��伨���ܺ���ʹ�������������ж�����׷���ۺļ���������о�Ѻպ��غ���Ĥƪ��פ������͹�ۼ���ѩ�������������߲��������ڽ������˹�̿������������ǹ���ð������Ͳ���λ�����Ϳζ����Ϻ�½�����𶹰�Ī��ɣ�·쾯���۱�����ɶ���ܼ��Ժ��浤�ɶ��ٻ���ϡ���������ǳӵѨ������ֽ����������Ϸ��������ò�����η��ɰ���������ˢ�ݺ���������©�������Ȼľ��з������Բ����ҳ�����ײ����ȳ����ǵ������������ɨ������оү���ؾ����Ƽ��ڿ��׹��ð��ѭ��ף���Ͼ����������ݴ���ι�������Ź�ó����ǽ���˽�ī������ж����������ƭ�ݽ�".$addChars;
			break;
		default :
			// Ĭ��ȥ�������׻������ַ�oOLl������01��Ҫ�����ʹ��addChars����
			$chars='ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789'.$addChars;
			break;
	}
	if($len>10 ) {//λ�������ظ��ַ���һ������
		$chars= $type==1? str_repeat($chars,$len) : str_repeat($chars,5);
	}
	if($type!=4) {
		$chars   =   str_shuffle($chars);
		$str     =   substr($chars,0,$len);
	}else{
		// ���������
		for($i=0;$i<$len;$i++){
		  $str.= self::msubstr($chars, floor(mt_rand(0,mb_strlen($chars,'utf-8')-1)),1);
		}
	}
	return $str;
}
?>