<?php
include_once("WxPayHelper.php");


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
$membercoupon_id = $_REQUEST['membercoupon_id'];
$all_amount = $_REQUEST['all_amount'];
$coupon_name = $_REQUEST['coupon_name'];

//echo "membercoupon_id".$membercoupon_id;
//$a=$wxPayHelper->create_native_url('3019');
$a=$wxPayHelper->create_native_url(2888);
sc2($a);
/*
	PHP���ɶ�ά��
*/
	function sc2($data){
		include("../ThinkPHP/Extend/Library/ORG/Qrcode/phpqrcode.php");
		//$data = 'www.xieche.com.cn';
	   // ������L��M��Q��H
	   $errorCorrectionLevel = 'L';
		// ��Ĵ�С��1��10
	   $matrixPointSize = 4;
	   // ���ɵ��ļ���
	   $path = "../UPLOADS/Shop/Logo/";
	   //if (!file_exists($path)){
		///mkdir($path);
	   //}

	   $filename = $path.$errorCorrectionLevel.'.'.$matrixPointSize.'.png';
	   QRcode::png($data, $filename, $errorCorrectionLevel, $matrixPointSize, 2);
		echo "<!DOCTYPE html>
				<html>
				<head>
					<meta charset=\"utf-8\">
					<title>΢��֧��</title>
					<link rel=\"stylesheet\" href=\"http://statics.xieche.com.cn/common/css/reset.css\">
					<link rel=\"stylesheet\" href=\"http://statics.xieche.com.cn/common/css/weixinpay_s.css\">
					<script type=\"text/javascript\" src=\"http://statics.xieche.com.cn/common/js/libs/jquery/jquery-1.11.min.js\"></script>
				</head>
				<body>
					<div id=\"container\">
						<div id=\"qr-code-contianer\">
							<img src=\"$filename\" alt=\"\" width=\"330\" height=\"330\" id=\"qr-code-img\" >
							<div id=\"qr-code-sign\"></div>
						</div>
						<div id=\"price-container\">
							<span id=\"price-sign\"></span>
							<div id=\"price-number\">
								<span>��</span>".$all_amount."
							</div>
						</div>
						<div id=\"info-container\">
							<span id=\"info-sign\">Я����</span>
							<dl id=\"infos-list\">
								<dt>���׵���</dt>
								<dd>".$membercoupon_id."</dd>
								<dt>��Ʒ����</dt>
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
								console.log(\"%o\", data);

								$(\".info\").html(data.info);
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