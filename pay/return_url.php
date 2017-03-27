<?php
/* * 
 * 功能：支付宝页面跳转同步通知页面
 * 版本：3.2
 * 日期：2011-03-25
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 * 该代码仅供学习和研究支付宝接口使用，只是提供一个参考。

 *************************页面功能说明*************************
 * 该页面可在本机电脑测试
 * 可放入HTML等美化页面的代码、商户业务逻辑程序代码
 * 该页面可以使用PHP开发工具调试，也可以使用写文本函数logResult，该函数已被默认关闭，见alipay_notify_class.php中的函数verifyReturn
 
 * TRADE_FINISHED(表示交易已经成功结束，并不能再对该交易做后续操作);
 * TRADE_SUCCESS(表示交易已经成功结束，可以对该交易做后续操作，如：分润、退款等);
 */
 

require_once("alipay.config.php");
require_once("lib/alipay_notify.class.php");
require_once("function.php");
?>
<!DOCTYPE HTML>
<html>
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<?php
//计算得出通知验证结果
$alipayNotify = new AlipayNotify($aliapy_config);
$verify_result = $alipayNotify->verifyReturn();

if($verify_result) {//验证成功
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//请在这里加上商户的业务逻辑程序代码
	
	//——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
    //获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表
    $out_trade_no	= $_GET['out_trade_no'];	//获取订单号
    $trade_no = $_GET['trade_no'];		//获取支付宝交易号
	$buyer_email = $_GET['buyer_email'];	 //获取买家支付宝帐号
	$gmt_create = $_GET['gmt_create'];		//获取交易创建时间
	$gmt_payment = $_GET['gmt_payment'];		//获取交易支付时间
    $total_fee = $_GET['total_fee'];		//获取总价格
	
    if($_GET['trade_status'] == 'TRADE_FINISHED' || $_GET['trade_status'] == 'TRADE_SUCCESS') {
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
		save_membercoupon_pay($out_trade_no,$trade_no,$buyer_email,$gmt_create,$gmt_payment,$total_fee,$_GET['trade_status'],1);
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
		/*if($membercoupon_info['is_pay']==0){
			save_membercoupon_pay($out_trade_no,$trade_no,$total_fee,$_GET['trade_status']);
			update_membercoupon_state($out_trade_no,1);
			//$order_info['order_verify'] = update_membercoupon_state($out_trade_no,1);
			coupon_send_sms($membercoupon_info['membercoupon_id']);
			if($membercoupon_info['coupon_id']){
				update_coupon_count($membercoupon_info['coupon_id']);
			}
		}*/
		
    }
    else {
      //echo "trade_status=".$_GET['trade_status'];
    }
	echo "支付成功";
	header("Location:http://www.xieche.net/myhome/mycoupon".$membercoupon_info[0]['coupon_type']);exit;
	
	//echo "验证成功<br />";
	//echo "trade_no=".$trade_no;
	
	//——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
	
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
}
else {
	header("Location:http://www.xieche.net/myhome");exit;
    //验证失败
    //如要调试，请看alipay_notify.php页面的verifyReturn函数，比对sign和mysign的值是否相等，或者检查$responseTxt有没有返回true
    echo "验证失败";
}
?>
        <title>支付宝即时到帐接口</title>
	</head>
    <body>
    </body>
</html>