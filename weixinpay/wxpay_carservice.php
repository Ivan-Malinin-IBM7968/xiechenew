<?php
include_once("WxPayHelper.php");
require_once("function.php");

$commonUtil = new CommonUtil();
$wxPayHelper = new WxPayHelper();

$order_id = $_GET['membercoupon_id'];
//订单号，此处用时间加随机数生成，商户根据自己情况调整，只要保持全局唯一就行
//$out_trade_no = date("YmdHis").$randNum;
$membercoupons = get_orderinfo($order_id);
$coupon_name_str = '上门保养套餐';
$coupon_summary_str = '';
$coupon_amount = $membercoupons['amount'];
if($membercoupons){
	$uid = $order_id['uid'];
	$mobile = $order_id['mobile'];
	$pa_id = get_weixin_paid($uid,$mobile);
}
$out_trade_no = $order_id;
$coupon_amount = $coupon_amount*100;//金额/100


$wxPayHelper->setParameter("bank_type", "WX");
$wxPayHelper->setParameter("body", $coupon_name_str);//"xieche_test"
$wxPayHelper->setParameter("partner", "1219569401");
$wxPayHelper->setParameter("out_trade_no", $out_trade_no);//$commonUtil->create_noncestr()

$wxPayHelper->setParameter("total_fee", $coupon_amount);//$coupon_amount
$wxPayHelper->setParameter("fee_type", "1");
$wxPayHelper->setParameter("notify_url", "http://www.xieche.com.cn/weixinpaytest/carservice_notify_url.php");
$wxPayHelper->setParameter("spbill_create_ip", $_SERVER['REMOTE_ADDR']);
$wxPayHelper->setParameter("input_charset", "UTF-8");

?>
<html>

<head>
	<title>微信支付</title>
   	<meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0;" name="viewport">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="format-detection" content="telephone=no">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	<meta name="apple-itunes-app" content="app-id=588144466" />
	<link rel="stylesheet" href="http://event.xieche.com.cn/20140620/css/libs/jquery.mobile.min.css">
	<link rel="stylesheet" href="http://event.xieche.com.cn/20140620/css/reset.css">
	<script src="http://statics.xieche.com.cn/common/js/libs/jquery/jquery.js"></script>
	<script src="http://statics.xieche.com.cn/common/js/libs/jquery/jquery.mobile-1.4.2.min.js"></script>
	<style>
		body{background: #f5f5f5;}
		.basic-info{padding:10px 20px; height: 40px; line-height: 40px; text-align: left; font-size: .9em; color: #999999; background: #ffffff}
		.product-name{height: 50px; line-height: 25px; font-size: 1.1em; color: #22272a; padding: 10px 20px; background: #ffffff}
		.product-price{height: 40px; line-height: 40px; color: #22272a; padding: 10px 20px; text-align: right; font-size: .9em}
		.product-price strong{color: #ff7811; font-size: 1.3em; }
		.product-pay button{width: 90%; margin: 0 auto; display: block;  border: 0px; color: #ffffff; background: #00aff2;}
	</style>
</head>
<body>
	<div id="wrapper">
		<div class="ui-grid-a basic-info">
			<div class="ui-block-a">商品信息</div>
			<div class="ui-block-b"></div>
		</div>
		<div class="ui-grid-solo product-name">
				<?php echo $coupon_name_str; ?>
		</div>
		<div class="ui-grid-solo product-price">
			应付金额：<strong><?php echo $coupon_amount/100; ?></strong> 元
		</div>
		<div class="ui-grid-solo product-pay">
			<button style="margin: 0 auto; display: block; " id="btn">立即支付</button>
		</div>
	</div>

<script language="javascript">

$(document).ready(function(){
	function onBridgeReady(){
	 WeixinJSBridge.call('hideOptionMenu');
	}

	if (typeof WeixinJSBridge == "undefined"){
		if( document.addEventListener ){
			document.addEventListener('WeixinJSBridgeReady', onBridgeReady, false);
		}else if (document.attachEvent){
			document.attachEvent('WeixinJSBridgeReady', onBridgeReady); 
			document.attachEvent('onWeixinJSBridgeReady', onBridgeReady);
		}
	}else{
		onBridgeReady();
	}


	$("#btn").click(function(){
		alert('11111111111');
			WeixinJSBridge.invoke('getBrandWCPayRequest',<?php echo $wxPayHelper->create_biz_package(); ?>,function(res){
	
				WeixinJSBridge.log(res.err_msg);
				//alert(res.err_code+res.err_desc+res.err_msg);

				if(res.err_msg == "get_brand_wcpay_request:ok" ){
				  // 在这里面进行 支付成功操作！
					window.location.href="http://www.xieche.com.cn/Mobile-my_coupon-pa_id-<?php echo $pa_id; ?>";
				}

			});
	})

})
	

</script>

</body>
</html>
