<?php
include_once("WxPayHelper.php");
require_once("function.php");

$commonUtil = new CommonUtil();
$wxPayHelper = new WxPayHelper();


$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
$postData = (array) $postObj;

tt(serialize($postData));
$membercoupon_id = $postData['ProductId'];
update_membercoupon_scan($membercoupon_id);//����ɨ��״̬
//�����ţ��˴���ʱ�����������ɣ��̻������Լ����������ֻҪ����ȫ��Ψһ����
//$out_trade_no = date("YmdHis").$randNum;
$membercoupons = get_membercoupons($membercoupon_id);
$coupon_name_str = '';
$coupon_summary_str = '';
$coupon_amount = 0;
if($membercoupons){
	foreach($membercoupons as $k=>$v){
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
$out_trade_no = $membercoupon_id;
$coupon_amount = $coupon_amount*100;//���/100
$coupon_name_str =str_replace(' ','',$coupon_name_str);

$wxPayHelper->setParameter("bank_type", "WX");
$wxPayHelper->setParameter("body", $coupon_name_str);//"xieche_test"
$wxPayHelper->setParameter("partner", "1219569401");
$wxPayHelper->setParameter("out_trade_no", $out_trade_no);//$commonUtil->create_noncestr()

$wxPayHelper->setParameter("total_fee", $coupon_amount);//$coupon_amount
$wxPayHelper->setParameter("fee_type", "1");
$wxPayHelper->setParameter("notify_url", "http://www.xieche.com.cn/weixinpaytest/notify_url.php");
$wxPayHelper->setParameter("spbill_create_ip", $_SERVER['REMOTE_ADDR']);
$wxPayHelper->setParameter("input_charset", "UTF-8");


echo $wxPayHelper->create_native_package();
?>