<?php

//前台支付接口示例
require_once('classes/quickpay_service.php');
require_once('function.php');


/*接受数据*/
$membercoupon_id = $_GET['membercoupon_id'];
//$membercoupon_id = '1';
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
$coupon_amount = $coupon_amount-$_REQUEST['account_amount'];
//下面这行用于测试，以生成随机且唯一的订单号
mt_srand(quickpay_service::make_seed());

$orderNumber = str_replace(",","c" , $membercoupon_id );


$param['transType']             = quickpay_conf::CONSUME;  //交易类型，CONSUME or PRE_AUTH

$param['orderAmount']           = $coupon_amount*100;        //交易金额
$param['orderNumber']           = date('YmdHis') ."-".$orderNumber; //订单号，必须唯一
$param['orderTime']             = date('YmdHis');   //交易时间, YYYYmmhhddHHMMSS
$param['orderCurrency']         = quickpay_conf::CURRENCY_CNY;  //交易币种，CURRENCY_CNY=>人民币

$param['customerIp']            = $_SERVER['REMOTE_ADDR'];  //用户IP
$param['frontEndUrl']           = "http://www.xieche.com.cn/unionpay/front_notify.php";    //前台回调URL
$param['backEndUrl']            = "http://www.xieche.com.cn/unionpay/back_notify.php";    //后台回调URL
$param['commodityName']         = $coupon_name_str;   //商品名称

/* 可填空字段
   $param['commodityUrl']          = "http://www.example.com/product?name=商品";  //商品URL
   $param['commodityName']         = '商品名称';   //商品名称
   $param['commodityUnitPrice']    = 11000;        //商品单价
   $param['commodityQuantity']     = 1;            //商品数量
*/

//其余可填空的参数可以不填写

$pay_service = new quickpay_service($param, quickpay_conf::FRONT_PAY);
$html = $pay_service->create_html();

header("Content-Type: text/html; charset=" . quickpay_conf::$pay_params['charset']);
echo $html; //自动post表单

?>
