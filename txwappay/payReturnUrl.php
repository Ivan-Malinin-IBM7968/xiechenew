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
		
		$membercoupon_info = get_membercoupons($out_trade_no);
			save_txmembercoupon_pay($out_trade_no,$transaction_id,"",$gmt_create,$gmt_payment,$total_fee,"",1);

		if($membercoupon_info){
			foreach($membercoupon_info as $k=>$v){
				if($v['is_pay']==0){
						update_membercoupon_state($v['membercoupon_id'],1);
						coupon_send_sms($v['membercoupon_id']);
					if($v['coupon_id']){
						update_coupon_count($v['coupon_id']);
					}
				}
				if($v['pa'] == 2) {
					$is_pa_order = 1;//2 平安wap版 不要跳手机
				}
				if($v['pa'] == 3) {
					$is_xieche_order = 1;//3 自己wap版
				}
			}
		}

		if($is_pa_order == 1) {
			header('Location: http://www.xieche.com.cn/Pamobile-my_coupon');
		}elseif($is_xieche_order == 1) {
			header('Location: http://www.xieche.com.cn/Mobile-my_coupon');
		}else{
			header('Location: xieche-uri://?type=1');
		}

		
		exit;

	} else {
		//当做不成功处理
		echo "<br/>" . "支付失败" . "<br/>";
	}
	
} else {
	echo "<br/>" . "认证签名失败" . "<br/>";
}

?>