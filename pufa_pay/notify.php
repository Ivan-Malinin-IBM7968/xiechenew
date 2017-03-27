<?php
require_once("java/Java.inc");
java_require("properties.jar;spdbmerchant.jar;bcprov-jdk14-127.jar;jsse.jar;jnet.jar;jcert.jar;jce.jar;");

/**
 * 浦发支付结果回调 
 */

header("content-type:text/html; charset=utf-8");

$input = file_get_contents('php://input');

var_dump($input);exit;

$merverify = new Java('com.csii.payment.client.core.MerchantSignVerify');

$plainFromPayGate = "TranAbbr=IPER|AcqSsn=000000073601|MercDtTm=20090615144037|TermSsn=15144037|RespCode=00|TermCode=00000000|MercCode=990108160003311|TranAmt=1.80|SettDate=20090608";
$signatureFromPayGate = "4a0532b6c873a683e95968ef9888baa568872c37202d2fd55bc5584b85e110a36b6eeeb2300179ff49c6a6cfb96dd509dea495a48d0c2141738d0cdb8813198af5267e467f8beecfa421bd4ebcc70111ed601feb53aa8d2d7715333c3c4d9090b89c7af1de3b633ac45ff564f1820a8ae16be82f32b8aea6bf16f26a110aa12a";

//VERIFY PAYGATE SIGNATURE
$verify = $merverify->merchantVerifyPayGate_ABA($signatureFromPayGate, $plainFromPayGate);
