<?php

/**
 *类名：alipay_to.php
 *功能：支付宝wap接口处理页面
 *详细：该页面是调用底层接口并返回处理结果页面，无需修改
 *版本：2.0
 *日期：2011-09-01
 '说明：
 '以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 '该代码仅供学习和研究支付宝接口使用，只是提供一个参考。
*/

require_once ("alipay_config.php");
require_once ("class/alipay_service.php");
require_once ("function.php");

$membercoupon_id = $_GET['membercoupon_id'];
//$coupon = get_membercoupon($membercoupon_id);
$membercoupons = get_membercoupons($membercoupon_id);
$coupon_name_str = '';
$coupon_summary_str = '';
$coupon_amount = 0;
if($membercoupons){
	foreach($membercoupons as $k=>$v){
		$coupon_name_arr[] = $v['coupon_name'];
		$coupon_summary_arr[] = $v['coupon_summary'];
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
/*$order_id = $_GET['order_id'];
$true_order_id = get_orderid($order_id);
$uid = $_GET['uid'];
$orderinfo = get_order($true_order_id,$uid);*/

//echo '<pre>';print_r($orderinfo);exit;
$subject		= $coupon_name_str;			//产品名称
$out_trade_no	= $membercoupon_id;			//请与贵网站订单系统中的唯一订单号匹配
$total_fee		= $coupon_amount;			//订单总金额
$out_user		= $membercoupons[0]['uid'];			//商户系统中用户唯一标识、例如UID、NickName
/**
 * ****************************alipay_wap_trade_create_direct*************************************
 */

// 构造要请求的参数数组，无需改动
$pms1 = array (
		"req_data" => '<direct_trade_create_req><subject>' . $subject . '</subject><out_trade_no>' . $out_trade_no . '</out_trade_no><total_fee>' . $total_fee . "</total_fee><seller_account_name>" . $seller_email . "</seller_account_name><notify_url>" . $notify_url . "</notify_url><out_user>" . $out_user . "</out_user><merchant_url>" . $merchant_url . "</merchant_url>" . "<call_back_url>" . $call_back_url . "</call_back_url></direct_trade_create_req>",
		"service" => $Service_Create,
		"sec_id" => $sec_id,
		"partner" => $partner,
		"req_id" => date ( "Ymdhms" ),
		"format" => $format,
		"v" => $v 
);

//echo '<pre>';
// 构造请求函数
$alipay = new alipay_service ();

// 调用alipay_wap_trade_create_direct接口，并返回token返回参数
$token = $alipay->alipay_wap_trade_create_direct ( $pms1, $key, $sec_id );

/**
 * ************************************************************************************************
 */

/**
 * *******************************alipay_Wap_Auth_AuthAndExecute***********************************
 */

// 构造要请求的参数数组，无需改动
$pms2 = array (
		"req_data" => "<auth_and_execute_req><request_token>" . $token . "</request_token></auth_and_execute_req>",
		"service" => $Service_authAndExecute,
		"sec_id" => $sec_id,
		"partner" => $partner,
		"call_back_url" => $call_back_url,
		"format" => $format,
		"v" => $v 
);

// 调用alipay_Wap_Auth_AuthAndExecute接口方法，并重定向页面
$alipay->alipay_Wap_Auth_AuthAndExecute ( $pms2, $key );

/**
 * ************************************************************************************************
 */
?>
