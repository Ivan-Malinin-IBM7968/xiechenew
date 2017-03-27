<?php
/*
 * 功能：支付宝主动通知调用的页面（服务器异步通知页面） 版本：2.0 日期：2011-09-01 '说明：
 * '以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 * '该代码仅供学习和研究支付宝接口使用，只是提供一个参考。
 */
// /////////页面功能说明///////////////
// 创建该页面文件时，请留心该页面文件中无任何HTML代码及空格。
// 该页面不能在本机电脑测试，请到服务器上做测试。请确保外部可以访问该页面。
// 该页面调试工具请使用写文本函数log_result，该函数已被默认关闭，见alipay_notify.php中的函数notify_verify
// TRADE_FINISHED(表示交易已经成功结束);
// 该服务器异步通知页面面主要功能是：对于返回页面（return_url.php）做补单处理。如果没有收到该页面返回的 success
// 信息，支付宝会在24小时内按一定的时间策略重发通知
// ///////////////////////////////////

require_once ("class/alipay_notify.php");
require_once ("alipay_config.php");
require_once ("function.php");

$alipay = new alipay_notify ( $partner, $key, $sec_id, $_input_charset ); // 构造通知函数信息
$verify_result = $alipay->notify_verify (); // 计算得出通知验证结果

if ($verify_result) { // 验证成功
                      // ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                      // 请在这里加上商户的业务逻辑程序代
                      
	// ——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
                      // 获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表
	
	$status = getDataForXML ( $_POST ['notify_data'], '/notify/trade_status' ); // 返回token
	if ($status == 'TRADE_FINISHED') { // 交易成功结束
	                                   // 判断该笔订单是否在商户网站中已经做过处理
	                                   // 如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
	                                   // 如果有做过处理，不执行商户的业务程序
		$out_trade_no = getDataForXML ( $_POST ['notify_data'], '/notify/out_trade_no' ); 
		$trade_no = getDataForXML ( $_POST ['notify_data'], '/notify/trade_no' ); 
		$total_fee = getDataForXML ( $_POST ['notify_data'], '/notify/total_fee' );
		$trade_status = $status;
		/*$true_order_id = get_orderid($out_trade_no);
		$order_info = get_order($true_order_id);
		if($order_info['pay_status']==0){
			save_order_pay($out_trade_no,$trade_no,$total_fee,$trade_status);
			$order_info['order_verify'] = updage_order_state($out_trade_no,1);
			send_sms($order_info,$out_trade_no);
			if($order_info['coupon_id']){
				update_coupon_count($order_info['coupon_id']);
			}
		}
		$membercoupon_info = get_membercoupon($out_trade_no);
		if($membercoupon_info['is_pay']==0){
			save_membercoupon_pay($out_trade_no,$trade_no,$total_fee,$trade_status);
			update_membercoupon_state($out_trade_no,1);
			//$order_info['order_verify'] = update_membercoupon_state($out_trade_no,1);
			coupon_send_sms($membercoupon_info['membercoupon_id']);
			if($membercoupon_info['coupon_id']){
				update_coupon_count($membercoupon_info['coupon_id']);
			}
		}*/
		$membercoupon_info = get_membercoupons($out_trade_no);
		save_membercoupon_pay($out_trade_no,$trade_no,$total_fee,$trade_status);
		if($membercoupon_info){
			foreach($membercoupon_info as $k=>$v){
				if($v['is_pay']==0){
					update_membercoupon_state($v['membercoupon_id'],1);
					//$order_info['order_verify'] = update_membercoupon_state($out_trade_no,1);
					coupon_send_sms($v['membercoupon_id']);
					if($v['coupon_id']){
						update_coupon_count($v['coupon_id']);
					}
				}
			}
		}

		echo "success"; // 请不要修改或删除，在判断交易正常后，必须在页面输出success
		                
		// 写文本函数记录程序运行情况是否正常
		//log_result ( "success" );
	} else {
		echo "fail"; // 交易未完成
			             
		// 调试用，写文本函数记录程序运行情况是否正常
			             // log_result ("这里写入想要调试的代码变量值，或其他运行的结果记录");
	}
	// ——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
	
	// ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
} else {
	// 验证失败
	echo "fail";
	
	// 调试用，写文本函数记录程序运行情况是否正常
	// log_result ("这里写入想要调试的代码变量值，或其他运行的结果记录");
}
?>