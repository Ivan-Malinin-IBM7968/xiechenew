<?php
error_reporting(0);
//---------------------------------------------------------
//财付通即时到帐支付页面回调示例，商户按照此文档进行开发即可
//---------------------------------------------------------
require_once ("classes/ResponseHandler.class.php");
require_once ("classes/WapResponseHandler.class.php");
require_once("function.php");

/* 密钥 */
$key = "d19dbfec5e37ed90f61646fa19a38cd7";
//$key = "8934e7d15453e97507ef794cf7b0519d";

/* 创建支付应答对象 */
$resHandler = new WapResponseHandler();
$resHandler->setKey($key);

//判断签名
if($resHandler->isTenpaySign()) {

	//商户号
	$bargainor_id = $resHandler->getParameter("bargainor_id");
	//商户订单号
	$out_trade_no = $resHandler->getParameter("sp_billno");
	//财付通交易单号
	$transaction_id = $resHandler->getParameter("transaction_id");
	//订单创建时间
	$gmt_create = $resHandler->getParameter("time_start");
	//支付时间
	$gmt_payment = $resHandler->getParameter("time_end");
	//金额,以分为单位
	$total_fee = ($resHandler->getParameter("total_fee")/100);

	if($gmt_create){
		$gmt_create = strtotime($gmt_create);
	}
	if($gmt_payment){
		$gmt_payment = strtotime($gmt_payment);
	}

	//支付结果
	$pay_result = $resHandler->getParameter("pay_result");
	
	if( "0" == $pay_result  ) {
		//header('Location: http://www.xieche.com.cn/Mobilecar-mycarservice-pa_id-234777');
		header('Location: xcwapp://');
		exit;

	} else {
		//当做不成功处理
		echo "<br/>" . "支付失败" . "<br/>";
	}
	
} else {
	echo "<br/>" . "认证签名失败" . "<br/>";
}

?>