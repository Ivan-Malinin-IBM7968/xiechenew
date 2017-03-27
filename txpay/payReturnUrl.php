<?php
//---------------------------------------------------------
//财付通即时到帐支付页面回调示例，商户按照此文档进行开发即可
//---------------------------------------------------------
require ("classes/ResponseHandler.class.php");
require ("classes/RequestHandler.class.php");
require ("classes/client/ClientResponseHandler.class.php");
require ("classes/client/TenpayHttpClient.class.php");
require_once("function.php");
/* 商户号，上线时务必将测试商户号替换为正式商户号 */
$partner = "1217372501";
//$partner = "1900000109";
/* 密钥 */
$key = "e35518dfe4ce45109fc0a1b5683654ea";
//$key = "8934e7d15453e97507ef794cf7b0519d";
/* 创建支付应答对象 */
$resHandler = new ResponseHandler();
$resHandler->setKey($key);

//判断签名
if($resHandler->isTenpaySign()) {
	//通知id
	$notify_id = $resHandler->getParameter("notify_id");
	//通过通知ID查询，确保通知来至财付通
	//创建查询请求
	$queryReq = new RequestHandler();
	$queryReq->init();
	$queryReq->setKey($key);
	$queryReq->setGateUrl("https://gw.tenpay.com/gateway/verifynotifyid.xml");
	$queryReq->setParameter("partner", $partner);
	$queryReq->setParameter("notify_id", $notify_id);
	
	//通信对象
	$httpClient = new TenpayHttpClient();
	$httpClient->setTimeOut(120);
	//设置请求内容
	$httpClient->setReqContent($queryReq->getRequestURL());
	
	//后台调用
	if($httpClient->call()) {
		//设置结果参数
		$queryRes = new ClientResponseHandler();
		$queryRes->setContent($httpClient->getResContent());
		$queryRes->setKey($key);
		
		//判断签名及结果
		//只有签名正确,retcode为0，trade_state为0才是支付成功
		if($queryRes->isTenpaySign() && $queryRes->getParameter("retcode") == "0" && $queryRes->getParameter("trade_state") == "0" && $queryRes->getParameter("trade_mode") == "1" ) {
			//取结果参数做业务处理
			$out_trade_no = $queryRes->getParameter("out_trade_no");
			//财付通订单号
			$transaction_id = $queryRes->getParameter("transaction_id");
			//财务通用户号
			$buyer_id = $queryRes->getParameter("buyer_alias");
			//订单创建时间
			$gmt_create = $queryRes->getParameter("time_start");
			//支付时间
			$gmt_payment = $queryRes->getParameter("time_end");
			//金额,以分为单位
			$total_fee = ($queryRes->getParameter("total_fee")/100);
			if($gmt_create){
				$gmt_create = strtotime($gmt_create);
			}
			if($gmt_payment){
				$gmt_payment = strtotime($gmt_payment);
			}
			
			//如果有使用折扣券，discount有值，total_fee+discount=原请求的total_fee
			$discount = $queryRes->getParameter("discount");
			
			//------------------------------
			//处理业务开始
			//------------------------------
			//获取网站订单ID
			//$leng =  strlen($out_trade_no);
			//$lengout_trade_no = substr($out_trade_no,14,$leng-14);
			//$lengout_trade_no = explode(',',$lengout_trade_no);
			$membercoupon_info = get_membercoupons($out_trade_no);
			save_txmembercoupon_pay($out_trade_no,$transaction_id,$buyer_id,$gmt_create,$gmt_payment,$total_fee,$queryRes->getParameter("trade_state"),1);

			if($membercoupon_info){
				$is_pa_order = 0;
				foreach($membercoupon_info as $k=>$v){
					if($v['is_pay']==0){
							update_membercoupon_state($v['membercoupon_id'],1);
							coupon_send_sms($v['membercoupon_id']);
						if($v['coupon_id']){
							update_coupon_count($v['coupon_id']);
						}
					}
					if($v['pa'] == 1) {
						$is_pa_order = 1;
					}
				}
			}
			//处理数据库逻辑
			//注意交易单不要重复处理
			//!!!注意判断返回金额!!!
			
			//------------------------------
			//处理业务完毕
			//------------------------------
			if($is_pa_order == 1) {
				//header("Location:http://baoyang.pahaoche.com/myhome/mycoupon".$membercoupon_info[0]['coupon_type'].".html");exit;
				header("Location:http://baoyang.pahaoche.com/myhome/coupon_order.html");exit;
			}else {
				//header("Location:http://www.xieche.com.cn/myhome/mycoupon".$membercoupon_info[0]['coupon_type'].".html");exit;
				header("Location:http://www.xieche.com.cn/myhome/coupon_order.html");exit;
			}
			echo "<br/>" . "支付成功" . "<br/>";
			
		}else {
			//错误时，返回结果可能没有签名，写日志trade_state、retcode、retmsg看失败详情。
			//echo "验证签名失败 或 业务错误信息:trade_state=" . $queryRes->getParameter("trade_state") . ",retcode=" . $queryRes->getParameter("retcode"). ",retmsg=" . $queryRes->getParameter("retmsg") . "<br/>" ;
			echo "<br/>" . "支付失败" . "<br/>";
		}
		
		//获取查询的debug信息,建议把请求、应答内容、debug信息，通信返回码写入日志，方便定位问题
		/*
		echo "<br>------------------------------------------------------<br>";
		echo "http res:" . $httpClient->getResponseCode() . "," . $httpClient->getErrInfo() . "<br>";
		echo "query req:" . htmlentities($queryReq->getRequestURL(), ENT_NOQUOTES, "GB2312") . "<br><br>";
		echo "query res:" . htmlentities($queryRes->getContent(), ENT_NOQUOTES, "GB2312") . "<br><br>";
		echo "query reqdebug:" . $queryReq->getDebugInfo() . "<br><br>" ;
		echo "query resdebug:" . $queryRes->getDebugInfo() . "<br><br>";
		*/
	}else {
		//通信失败
		//echo "fail";
		//后台调用通信失败,写日志，方便定位问题，这些信息注意保密，最好不要打印给用户
		echo "<br>订单通知查询失败:" . $httpClient->getResponseCode() ."," . $httpClient->getErrInfo() . "<br>";
	} 
} else {
	//签名错误
	echo "<br>签名失败<br>";
}

//获取debug信息,建议把debug信息写入日志，方便定位问题
//echo $resHandler->getDebugInfo() . "<br>";
?>