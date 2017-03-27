<?php
//---------------------------------------------------------
//财付通即时到帐支付请求示例，商户按照此文档进行开发即可
//---------------------------------------------------------
error_reporting(1);
require_once ("classes/RequestHandler.class.php");
require ("classes/client/ClientResponseHandler.class.php");
require ("classes/client/TenpayHttpClient.class.php");
require_once("function.php");

/* 商户号 */
//$partner = "1900000109";
$partner = "1217371701";

/* 密钥 */
//$key = "8934e7d15453e97507ef794cf7b0519d";
$key = "d19dbfec5e37ed90f61646fa19a38cd7";

//4位随机数
$membercoupon_id = $_GET['membercoupon_id'];
$order_id = $_GET['order_id'];
if($membercoupon_id){
//$membercoupon_id = '24,26';
//订单号，此处用时间加随机数生成，商户根据自己情况调整，只要保持全局唯一就行
//$out_trade_no = date("YmdHis").$randNum;
$membercoupons = get_membercoupons($membercoupon_id);
//直连银行编码
$bank_type = $_GET['bank_type'];
$coupon_name_str = '';
$coupon_summary_str = '';
$coupon_amount = 0;
if($membercoupons){
	foreach($membercoupons as $k=>$v){
		$coupon_amount += $v['coupon_amount'];
	}
	if(count($membercoupons)>1){
		$coupon_name_str = $membercoupons[0]['coupon_name'].'*'.count($membercoupons);
		$coupon_summary_str = $membercoupons[0]['coupon_summary'].'*'.count($membercoupons);
	}else{
		$coupon_name_str = $membercoupons[0]['coupon_name'];
		$coupon_summary_str = $membercoupons[0]['coupon_summary'];
	}
}
$out_trade_no = $membercoupon_id;
$coupon_amount = $coupon_amount-$_REQUEST['account_amount'];
$url = "http://www.xieche.com.cn/txpay/payReturnUrl.php";
}

if($order_id){
//$membercoupon_id = '24,26';
//订单号，此处用时间加随机数生成，商户根据自己情况调整，只要保持全局唯一就行
//$out_trade_no = date("YmdHis").$randNum;
$order_info = get_orderinfo($order_id);
//直连银行编码
$bank_type = $_GET['bank_type'];
$coupon_name_str = '上门保养套餐';
$out_trade_no = 'm'.$order_id;
$coupon_amount = $order_info['amount'];
$url = "http://www.xieche.com.cn/txpay/carservicepayNotifyUrl.php";
}

/* 创建支付请求对象 */
$reqHandler = new RequestHandler();
$reqHandler->init();
$reqHandler->setKey($key);
//$reqHandler->setGateUrl("https://gw.tenpay.com/gateway/pay.htm");
//设置初始化请求接口，以获得token_id
//$reqHandler->setGateUrl("https://wap.tenpay.com/cgi-bin/wappayv2.0/wappay_init.cgi");
$reqHandler->setGateUrl("https://www.tenpay.com/app/mpay/wappay_init.cgi");

$httpClient = new TenpayHttpClient();
//应答对象
$resHandler = new ClientResponseHandler();
//----------------------------------------
//设置支付参数 
//----------------------------------------
$coupon_amount = $coupon_amount*100;//金额/100
$reqHandler->setParameter("total_fee", $coupon_amount);  //总金额
//用户ip
$reqHandler->setParameter("spbill_create_ip", $_SERVER['REMOTE_ADDR']);//客户端IP
$reqHandler->setParameter("ver", "2.0");//版本类型
$reqHandler->setParameter("bank_type", "0"); //银行类型，财付通填写0
$reqHandler->setParameter("callback_url", "http://www.xieche.com.cn/txwappay/payCarserviceReturnUrl.php");//交易完成后跳转的URL
$reqHandler->setParameter("bargainor_id", $partner); //商户号
$reqHandler->setParameter("sp_billno", $out_trade_no); //商户订单号
$reqHandler->setParameter("notify_url", "http://www.xieche.com.cn/txwappay/payNotifyUrl.php");//接收财付通通知的URL，需绝对路径
$reqHandler->setParameter("desc", $coupon_name_str);
$reqHandler->setParameter("attach", "");
$reqHandler->setParameter("time_start", date("YmdHis"));  //订单生成时间


$httpClient->setReqContent($reqHandler->getRequestURL());

//后台调用
if($httpClient->call()) {
	var_dump($coupon_name_str,$coupon_summary_str,$httpClient->getResContent());
	$resHandler->setContent($httpClient->getResContent());
	var_dump($resHandler);
	//获得的token_id，用于支付请求
	$token_id = $resHandler->getParameter('token_id');
	//echo $token_id;exit;
	$reqHandler->setParameter("token_id", $token_id);

	//请求的URL
	$reqHandler->setGateUrl("https://wap.tenpay.com/cgi-bin/wappayv2.0/wappay_gate.cgi");
	//此次请求只需带上参数token_id就可以了，$reqUrl和$reqUrl2效果是一样的
	$reqUrl = $reqHandler->getRequestURL(); 
	$reqHandler->doSend();
	//$reqUrl = "http://wap.tenpay.com/cgi-bin/wappayv2.0/wappay_gate.cgi?token_id=".$token_id;
	
}

?>