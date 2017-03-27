<?php
header("Content-Type: text/html; charset=utf-8");
/* *
 * 功能：统一预下单接口接入页
 * 版本：3.3
 * 修改日期：2012-07-23
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 * 该代码仅供学习和研究支付宝接口使用，只是提供一个参考。

 *************************注意*************************
 * 如果您在接口集成过程中遇到问题，可以按照下面的途径来解决
 * 1、商户服务中心（https://b.alipay.com/support/helperApply.htm?action=consultationApply），提交申请集成协助，我们会有专业的技术工程师主动联系您协助解决
 * 2、商户帮助中心（http://help.alipay.com/support/232511-16307/0-16307.htm?sh=Y&info_type=9）
 * 3、支付宝论坛（http://club.alipay.com/read-htm-tid-8681712.html）
 * 如果不想使用扩展功能请把扩展功能参数赋空值。
 */

require_once("alipay.config.php");
require_once("lib/alipay_submit.class.php");

/**************************请求参数**************************/

//服务器异步通知页面路径
$notify_url = "http://www.xieche.com.cn/pay/notify_url_code.php";
//需http://格式的完整路径，不能加?id=123这类自定义参数

//商户订单号
$order_id =1; //$_REQUEST['order_id'];
$out_trade_no =$order_id;// $_POST['WIDout_trade_no'];
//商户网站订单系统中唯一订单号，必填

//订单名称
$order_name ="baoyang";// $_REQUEST['name'];
$subject = $order_name;
//必填

//订单业务类型
$product_code = 'QR_CODE_OFFLINE';//$_POST['WIDproduct_code'];
//目前只支持QR_CODE_OFFLINE（二维码支付），必填

//付款金额
$total_fee = 0.01;
//必填

//卖家支付宝帐户
$seller_email = 'zz@xieche.net';
//必填

//订单描述
$body = '';


/************************************************************/

//构造要请求的参数数组，无需改动
$parameter = array(
    "service" => "alipay.acquire.precreate",
    "partner" => trim($alipay_config['partner']),
    "notify_url"	=> $notify_url,
    "out_trade_no"	=> $out_trade_no,
    "subject"	=> $subject,
    "product_code"	=> $product_code,
    "total_fee"	=> $total_fee,
    "seller_email"	=> $seller_email,
    "body"	=> $body,
    "_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
);

//建立请求
var_dump($aliapy_config);
$alipaySubmit = new AlipaySubmit($aliapy_config);

 var_dump($alipaySubmit);
$html_text = $alipaySubmit->buildRequestHttp($parameter);
//解析XML
//注意：该功能PHP5环境及以上支持，需开通curl、SSL等PHP配置环境。建议本地调试时使用PHP开发软件
$doc = new DOMDocument();
$doc->loadXML($html_text);

//请在这里加上商户的业务逻辑程序代码

//——请根据您的业务逻辑来编写程序（以下代码仅作参考）——

//获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表

//解析XML
if( ! empty($doc->getElementsByTagName( "alipay" )->item(0)->nodeValue) ) {
    $alipay = $doc->getElementsByTagName( "alipay" )->item(0)->nodeValue;
    echo $alipay;
}

//——请根据您的业务逻辑来编写程序（以上代码仅作参考）——

?>