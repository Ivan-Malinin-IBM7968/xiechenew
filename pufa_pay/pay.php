<?php
require_once("java/Java.inc");
java_require("properties.jar;spdbmerchant.jar;bcprov-jdk14-127.jar;jsse.jar;jnet.jar;jcert.jar;jce.jar;");

/**
 *  浦发支付入口 
 */

header("content-type:text/html; charset=utf-8");

$formAction = 'https://124.74.239.32/payment/main';
$data = array(
    'TranAmt' => 1.8,
    'transName' => '1234',
    'MercCode' => '990108160003311',
    'TranAbbr' => 'IPER',
    'TermSsn' => $_REQUEST['order_id'],
    'OAcqSSN' => '',
    'MercDtTm' => '20090615144037',
    'TermCode' => '001',
    'Remark1' => '',
    'Remark2' => '',
    'submit' => '%3F%A8%A2%3F%3F',
    'OSttDate' => '',
    'MercUrl' => urlencode('http://www.xieche.dev/pufa_pay/notify.php'),
);

foreach ($data as $key => $value) {
    $plain[] = $key.'='.$value;
}

$merverify = new Java('com.csii.payment.client.core.MerchantSignVerify');

$plain = implode('|', $plain);

//MERCHANT SIGNATURE
$signature = $merverify->merchantSignData_ABA($plain);
?>

<html>
    <head>
    </head>
    <body onload="form.submit()">
        <div>
        <p>连接验证中，请稍候~</p>
        </div>
        <form name="form" action="<?php echo $formAction; ?>"  method="post">
        <input type="hidden" name="transName" value="IPER">
        <input type="hidden" name="Plain" value="<?php echo $plain; ?>">
        <input type="hidden" name="Signature"  value="<?php echo $signature; ?>">
        <!--
        <input type="submit" name="submit" value="浦东发展银行支付网关">
        -->
        </form>
    </body>
</html>
