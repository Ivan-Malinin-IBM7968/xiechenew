<?php
include_once("WxPayHelper.php");
require_once("function.php");

$commonUtil = new CommonUtil();
$wxPayHelper = new WxPayHelper();

/*
$wxPayHelper->setParameter("bank_type", "WX");
$wxPayHelper->setParameter("body", "test");
$wxPayHelper->setParameter("partner", "1900000109");
$wxPayHelper->setParameter("out_trade_no", $commonUtil->create_noncestr());
$wxPayHelper->setParameter("total_fee", "1");
$wxPayHelper->setParameter("fee_type", "1");
$wxPayHelper->setParameter("notify_url", "htttp://www.baidu.com");
$wxPayHelper->setParameter("spbill_create_ip", "127.0.0.1");
$wxPayHelper->setParameter("input_charset", "GBK");
*/
//echo $wxPayHelper->create_native_package();
//print_r($_REQUEST);

//根据订单号判断使用哪种模式
$output=strpos($_REQUEST['membercoupon_id'],',');
if($output==false){
	$membercoupon_id = $first = $_REQUEST['membercoupon_id'];
	$all_amount = $_REQUEST['all_amount'];
	$coupon_name = $_REQUEST['coupon_name'];

	//echo "membercoupon_id".$membercoupon_id;
	$a=$wxPayHelper->create_native_url($membercoupon_id);
	//echo $a;

	if($membercoupon_id){
		sc2($a,$membercoupon_id,$all_amount,$coupon_name);
	}else{
		is_scan();
	}
}else{
	//模式二，支持多订单号
	$array = explode(",",$_REQUEST['membercoupon_id']);
	$first = $array['0'];
	$membercouponid = str_replace(',','|',$_REQUEST['membercoupon_id']);

	$appid = 'wx43430f4b6f59ed33';
	$PartnerKey = "4cc5e45c2dc9fb3fdcc5517598f7059d";
	$attach = '支付测试';
	$body = str_replace(" ","",$_REQUEST['coupon_name']);
	$mch_id = '1219569401';
	$nonce_str = $commonUtil->create_noncestr();
	$notify_url = 'http://www.xieche.com.cn/weixinpaytest/newnotify_url.php';
	$out_trade_no = $membercouponid;
	$spbill_create_ip = '14.23.150.211';
	$total_fee = $_REQUEST['all_amount']*100;
	$trade_type = 'NATIVE';
	$string1 = 'appid='.$appid.'&attach='.$attach.'&body='.$body.'&mch_id='.$mch_id.'&nonce_str='.$nonce_str.'&notify_url='.$notify_url.'&out_trade_no='.$out_trade_no.'&spbill_create_ip='.$spbill_create_ip.'&total_fee='.$total_fee.'&trade_type='.$trade_type.'&key='.$PartnerKey;
	//echo 'string1='.$string1;
	$sign = strtoupper(md5($string1));
	//echo 'sign='.$sign;
	$post = "<xml>
	<appid>{$appid}</appid>
	<attach>{$attach}</attach>
	<body>{$body}</body>
	<mch_id>{$mch_id}</mch_id>
	<nonce_str>{$nonce_str}</nonce_str>
	<notify_url>{$notify_url}</notify_url>
	<out_trade_no>{$out_trade_no}</out_trade_no>
	<spbill_create_ip>{$spbill_create_ip}</spbill_create_ip>
	<total_fee>{$total_fee}</total_fee>
	<trade_type>NATIVE</trade_type>
	<sign>{$sign}</sign>
	</xml>";

	$host = "https://api.mch.weixin.qq.com/pay/unifiedorder";
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_URL,$host);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_TIMEOUT,30); 
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
	$output = curl_exec($ch);
	//var_dump($output);
	curl_close($ch);

	//解析返回xml
	$dom = new DOMDocument;
	$dom->loadXML($output);

	$retCode = $dom->getElementsByTagName('code_url');
	$a = $retCode->item(0)->nodeValue;

	//echo "a=".$a;

	$membercoupon_id = $_REQUEST['membercoupon_id'];
	$all_amount = $_REQUEST['all_amount'];
	$coupon_name = $_REQUEST['coupon_name'];

	if($membercoupon_id){
		sc2($a,$membercoupon_id,$all_amount,$coupon_name,$first);
	}else{
		is_scan();
	}
}
/*
	PHP生成二维码
*/
	function sc2($data,$membercoupon_id,$all_amount,$coupon_name,$first){
		include("../ThinkPHP/Extend/Library/ORG/Qrcode/phpqrcode.php");
		//$data = 'www.xieche.com.cn';
		//$data = 'weixin://wxpay/bizpayurl?pr=xqfQ5wN';
	   // 纠错级别：L、M、Q、H
	   $errorCorrectionLevel = 'L';
		// 点的大小：1到10
	   $matrixPointSize = 4;
	   // 生成的文件名
	   $path = "../UPLOADS/weixinpay/";
	   //if (!file_exists($path)){
		///mkdir($path);
	   //}
		//echo $data;
	   $filename = $path.$first.'.png';
	   QRcode::png($data, $filename, $errorCorrectionLevel, $matrixPointSize, 2);
		echo "<!DOCTYPE html>
				<html>
				<head>
					<meta charset=\"utf-8\">
					<title>微信支付</title>
					<link rel=\"stylesheet\" href=\"http://statics.xieche.com.cn/common/css/reset.css\">
					<link rel=\"stylesheet\" href=\"http://statics.xieche.com.cn/common/css/weixinpay_s.css\">
					<script type=\"text/javascript\" src=\"http://statics.xieche.com.cn/common/js/libs/jquery/jquery-1.11.min.js\"></script>
				</head>
				<body>
					<div id=\"container\">
						<div id=\"qr-code-contianer\">
							<img src=\"$filename\" alt=\"\" width=\"330\" height=\"330\" id=\"qr-code-img\" >
							<div id=\"qr-code-sign\" class=\"icon\"></div>
						</div>
						<div id=\"price-container\">
							<span id=\"price-sign\"></span>
							<div id=\"price-number\">
								<span>￥</span>".$all_amount."
							</div>
						</div>
						<div id=\"info-container\">
							<span id=\"info-sign\">携车网</span>
							<dl id=\"infos-list\">
								<dt>交易单号</dt>
								<dd>".$membercoupon_id."</dd>
								<dt>商品名称</dt>
								<dd>".$coupon_name."</dd>
								<div class=\"clear\"></div>
							</dl>
							<input type=\"hidden\" id='mid' value='".$membercoupon_id."'>
						</div>
						<div id=\"extra-infos-container\">
							<div id=\"phonecall-sign\"></div>
						</div>
					</div>
				</body>
				</html>
				<script>
				$(function(){
					var mid = $('#mid').val();
					var selVote = function(){
						$.ajax({
							url: \"../weixinpaytest/scan.php\",
							type: \"GET\",
							cache: false,
							dataType:\"JSON\",
							data: {\"id\":mid},
							timeout: 35000,
							success: function(data){
								//alert(data);
								if(data=='is_scan'){
									$('.icon').attr('id','qr-code-result');
								}
							},
							error: function(data){
								$(\".info\").html(\"test error\");
							}
						}); 
					}

					selVote();

				});
				</script>
				";
	}
//echo sc2();

?>
