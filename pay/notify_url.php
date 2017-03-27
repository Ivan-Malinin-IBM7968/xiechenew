<?php
/* *
 * 功能：支付宝服务器异步通知页面
 * 版本：3.2
 * 日期：2011-03-25
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 * 该代码仅供学习和研究支付宝接口使用，只是提供一个参考。


 *************************页面功能说明*************************
 * 创建该页面文件时，请留心该页面文件中无任何HTML代码及空格。
 * 该页面不能在本机电脑测试，请到服务器上做测试。请确保外部可以访问该页面。
 * 该页面调试工具请使用写文本函数logResult，该函数已被默认关闭，见alipay_notify_class.php中的函数verifyNotify
 * 如果没有收到该页面返回的 success 信息，支付宝会在24小时内按一定的时间策略重发通知
 
 * TRADE_FINISHED(表示交易已经成功结束，并不能再对该交易做后续操作);
 * TRADE_SUCCESS(表示交易已经成功结束，可以对该交易做后续操作，如：分润、退款等);
 */


require_once("alipay.config.php");
require_once("lib/alipay_notify.class.php");
require_once("function.php");

//计算得出通知验证结果
$alipayNotify = new AlipayNotify($aliapy_config);
$verify_result = $alipayNotify->verifyNotify();

if($verify_result) {//验证成功
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//请在这里加上商户的业务逻辑程序代
	
	//——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
    //获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表
    $out_trade_no = $_POST['out_trade_no'];	    //获取订单号
    $trade_no = $_POST['trade_no'];	    	//获取支付宝交易号
	$buyer_email = $_POST['buyer_email'];	    	//获取买家支付宝帐号
	$gmt_create = $_POST['gmt_create'];		//获取交易创建时间
	$gmt_payment = $_POST['gmt_payment'];		//获取交易支付时间
    $total_fee = $_POST['total_fee'];			//获取总价格

    if($_POST['trade_status'] == 'TRADE_FINISHED') {
		//判断该笔订单是否在商户网站中已经做过处理
			//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
			//如果有做过处理，不执行商户的业务程序
		//$true_order_id = get_orderid($out_trade_no);
		//$order_info = get_order($true_order_id);
		$membercoupon_info = get_membercoupons($out_trade_no);

		if( $gmt_create ) {
			$gmt_create = strtotime($gmt_create);
		}
		if( $gmt_payment ) {
			$gmt_payment = strtotime($gmt_payment);
		}
		save_membercoupon_pay($out_trade_no,$trade_no,$buyer_email,$gmt_create,$gmt_payment,$total_fee,$_POST['trade_status'],0);
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
		/*$membercoupon_info = get_membercoupon($out_trade_no);
		if($membercoupon_info['is_pay']==0){
			save_membercoupon_pay($out_trade_no,$trade_no,$total_fee,$_POST['trade_status']);
			update_membercoupon_state($out_trade_no,1);
			//$order_info['order_verify'] = update_membercoupon_state($out_trade_no,1);
			coupon_send_sms($membercoupon_info['membercoupon_id']);
			if($membercoupon_info['coupon_id']){
				update_coupon_count($membercoupon_info['coupon_id']);
			}
		}*/
		//注意：
		//该种交易状态只在两种情况下出现
		//1、开通了普通即时到账，买家付款成功后。
		//2、开通了高级即时到账，从该笔交易成功时间算起，过了签约时的可退款时限（如：三个月以内可退款、一年以内可退款等）后。

        //调试用，写文本函数记录程序运行情况是否正常
        //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
    }
    else if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
		//判断该笔订单是否在商户网站中已经做过处理
			//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
			//如果有做过处理，不执行商户的业务程序
		$membercoupon_info = get_membercoupons($out_trade_no);

		if( $gmt_create ) {
			$gmt_create = strtotime($gmt_create);
		}
		if( $gmt_payment ) {
			$gmt_payment = strtotime($gmt_payment);
		}
		save_membercoupon_pay($out_trade_no,$trade_no,$buyer_email,$gmt_create,$gmt_payment,$total_fee,$_POST['trade_status'],0);
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
		/*$membercoupon_info = get_membercoupon($out_trade_no);
		if($membercoupon_info['is_pay']==0){
			save_membercoupon_pay($out_trade_no,$trade_no,$total_fee,$_POST['trade_status']);
			update_membercoupon_state($out_trade_no,1);
			//$order_info['order_verify'] = update_membercoupon_state($out_trade_no,1);
			coupon_send_sms($membercoupon_info['membercoupon_id']);
			if($membercoupon_info['coupon_id']){
				update_coupon_count($membercoupon_info['coupon_id']);
			}
		}*/
		//注意：
		//该种交易状态只在一种情况下出现——开通了高级即时到账，买家付款成功后。

        //调试用，写文本函数记录程序运行情况是否正常
        //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
    }
	//——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
        
	echo "success";		//请不要修改或删除
	
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
}
else {
    //验证失败
    echo "fail";

    //调试用，写文本函数记录程序运行情况是否正常
    //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
}
?>