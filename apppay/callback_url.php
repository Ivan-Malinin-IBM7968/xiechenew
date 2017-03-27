<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<?php
/*
 * 功能：付完款后跳转的页面（页面跳转同步通知页面） 版本：2.0 日期：2011-09-01 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 * 该代码仅供学习和研究支付宝接口使用，只是提供一个参考。
 */
// /////////页面功能说明///////////////
// 该页面可在本机电脑测试
// 该页面称作“页面跳转同步通知页面”，是由支付宝服务器同步调用，可当作是支付完成后的提示信息页，如“您的某某某订单，多少金额已支付成功”。
// 可放入HTML等美化页面的代码和订单交易完成后的数据库更新程序代码
// 该页面可以使用PHP开发工具调试，也可以使用写文本函数log_result进行调试，该函数已被默认关闭，见alipay_notify.php中的函数return_verify
// TRADE_FINISHED(表示交易已经成功结束);
// /////////////////////////////////

require_once ("class/alipay_notify.php");
require_once ("alipay_config.php");
require_once ("function.php");

// 构造通知函数信息
$alipay = new alipay_notify ( $partner, $key, $sec_id, $_input_charset );
// 计算得出通知验证结果
$verify_result = $alipay->return_verify ();

if ($verify_result) { // 验证成功
                      // ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                      // 请在这里加上商户的业务逻辑程序代码
                      
	// ——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
                      // 获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表
	$out_trade_no = $_GET ['out_trade_no']; // 外部交易号
	$myresult = $_GET ['result']; // 订单状态，是否成功
	$trade_no = $_GET ['trade_no']; // 交易号
	$total_fee = $_GET ['total_fee'];
	if ($_GET ['result'] == 'success') {
		// 判断该笔订单是否在商户网站中已经做过处理（可参考“集成教程”中“3.4返回数据处理”）
		// 如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
		// 如果有做过处理，不执行商户的业务程序
		/*$true_order_id = get_orderid($out_trade_no);
		$order_info = get_order($true_order_id);
		if($order_info['pay_status']==0){
			save_order_pay($out_trade_no,$trade_no,$total_fee,$_GET['result']);
			$order_info['order_verify'] = updage_order_state($out_trade_no,1);
			send_sms($order_info,$out_trade_no);
			if($order_info['coupon_id']){
				update_coupon_count($order_info['coupon_id']);
			}
		}
		$membercoupon_info = get_membercoupon($out_trade_no);
		if($membercoupon_info['is_pay']==0){
			save_membercoupon_pay($out_trade_no,$trade_no,$total_fee,$_GET['result']);
			update_membercoupon_state($out_trade_no,1);
			//$order_info['order_verify'] = update_membercoupon_state($out_trade_no,1);
			coupon_send_sms($membercoupon_info['membercoupon_id']);
			if($membercoupon_info['coupon_id']){
				update_coupon_count($membercoupon_info['coupon_id']);
			}
		}*/
		$membercoupon_info = get_membercoupons($out_trade_no);
		save_membercoupon_pay($out_trade_no,$trade_no,$total_fee,$_GET['result']);
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
		//echo "trade_status=" . $_GET ['result'];
	} else {
		//echo "trade_status=" . $_GET ['result'];
	}
	//header('Location: xieche-uri://?type='.$membercoupon_info[0]['coupon_type']);
	header('Location: xieche-uri://?type=1');
	exit;
	// ——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
	// ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
} else {
	// 验证失败
	// 如要调试，请看alipay_notify.php页面的return_verify函数，比对sign和mysign的值是否相等，或者检查$veryfy_result有没有返回true
	echo "fail";
}
?>