<?php

//---------------------------------------------------------
//财付通即时到帐支付后台回调示例，商户按照此文档进行开发即可
//---------------------------------------------------------

require ("classes/ResponseHandler.class.php");
require ("classes/WapNotifyResponseHandler.class.php");
require_once("function.php");

/* 商户号，上线时务必将测试商户号替换为正式商户号 */
$partner = "1217371701";
//$partner = "1900000109";
/* 密钥 */
$key = "d19dbfec5e37ed90f61646fa19a38cd7";
//$key = "8934e7d15453e97507ef794cf7b0519d";


/* 创建支付应答对象 */
$resHandler = new WapNotifyResponseHandler();
$resHandler->setKey($key);

//判断签名
if($resHandler->isTenpaySign()) {
	
	//商户号
	$bargainor_id = $resHandler->getParameter("bargainor_id");
	//商户订单号
	$out_trade_no = $resHandler->getParameter("sp_billno");	
	//财付通交易单号
	$transaction_id = $resHandler->getParameter("transaction_id");
	//财务通用户号
	$buyer_id = $resHandler->getParameter("purchase_alias");
	//金额,以分为单位
	$total_fee = ($resHandler->getParameter("total_fee")/100);
	//订单创建时间
	//$gmt_create = $resHandler->getParameter("time_start");
	//支付时间
	$gmt_payment = $resHandler->getParameter("time_end");
	if($gmt_payment){
		$gmt_payment = strtotime($gmt_payment);
	}

	//支付结果
	$pay_result = $resHandler->getParameter("pay_result");

	if( "0" == $pay_result  ) {
		$membercoupon_info = get_membercoupons($out_trade_no);
			save_txmembercoupon_pay($out_trade_no,$transaction_id,$buyer_id,"",$gmt_payment,$total_fee,"",0);

			if($membercoupon_info){
				foreach($membercoupon_info as $k=>$v){
					if($v['is_pay']==0){
							update_membercoupon_state($v['membercoupon_id'],1);
							coupon_send_sms($v['membercoupon_id']);
						if($v['coupon_id']){
							update_coupon_count($v['coupon_id']);
						}
					}
				}
			}

		echo 'success';
	}
	else
	{
		echo 'fail';
	} 
	
} else {
	//回调签名错误
	echo "fail";
}


?>