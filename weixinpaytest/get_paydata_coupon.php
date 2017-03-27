<?php
include_once("WxPayHelper.php");
require_once("function.php");
error_reporting(0);
$commonUtil = new CommonUtil();
$wxPayHelper = new WxPayHelper();

$appid = 'wxf3b82cad0f586ea5';
$PartnerKey = "4cc5e45c2dc9fb3fdcc5517598f7059d";
$attach = '优惠券订单';
$body = '优惠券订单';
$mch_id = '1233312002';
$nonce_str = $commonUtil->create_noncestr();
$notify_url = 'http://www.xieche.com.cn/weixinpaytest/notify_app_coupon_url.php';
$out_trade_no1 = $_REQUEST['membercoupon_id'];
$id = $_REQUEST['id'];
$out_trade_no = $id.'_'.time();

$number = $_REQUEST['number']? $_REQUEST['number']:1;
//$out_trade_no = 1000;
$spbill_create_ip = '14.23.150.211';
$info = get_membercoupon_info($out_trade_no1);
if($number>1){
    $price=0;
    foreach( $info as $k=>$v){
        $price = $price + $v["coupon_amount"];
    }
    $price = $price*100;
}else{
      $price = $info[0]["coupon_amount"]*100;
    }
//$total_fee= $price*100;
//echo $total_fee;
$total_fee = $price;
$trade_type ='APP';
$string1 = 'appid='.$appid.'&attach='.$attach.'&body='.$body.'&mch_id='.$mch_id.'&nonce_str='.$nonce_str.'&notify_url='.$notify_url.'&out_trade_no='.$out_trade_no.'&spbill_create_ip='.$spbill_create_ip.'&total_fee='.$total_fee.'&trade_type='.$trade_type.'&key='.$PartnerKey;
$sign = strtoupper(md5($string1));

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
<trade_type>APP</trade_type>
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
var_dump($output);
curl_close($ch);

//解析返回xml
$dom = new DOMDocument;
$dom->loadXML($output);
$retCode = $dom->getElementsByTagName('sign');
$data['sign'] = $retCode->item(0)->nodeValue;
$data['appid'] = $appid;
$data['partnerId'] = $mch_id;
$data['packageValue'] = 'Sign=WXPay';
$retCode = $dom->getElementsByTagName('nonce_str');
$data['nonceStr'] = $retCode->item(0)->nodeValue;
$data['timeStamp'] = time();
$retCode = $dom->getElementsByTagName('prepay_id');
$data['prepayId'] = $retCode->item(0)->nodeValue;
//sign要重新算一遍，日了狗了
$string2 = 'appid='.$appid.'&noncestr='.$data['nonceStr'].'&package='.$data['packageValue'].'&partnerid='.$mch_id.'&prepayid='.$data['prepayId'].'&timestamp='.$data['timeStamp'].'&key='.$PartnerKey;
$data['sign'] = strtoupper(md5($string2));
//echo $string2;
//echo 'sign2='.$data['sign'];
$array = array('status'=>'1','msg'=>'success','data'=>$data);
$data = json_encode($array);
echo $data;
?>
