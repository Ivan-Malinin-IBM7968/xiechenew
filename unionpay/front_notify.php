<?php

require_once('classes/quickpay_service.php');
require_once("function.php");
try {
    $response = new quickpay_service($_POST, quickpay_conf::RESPONSE);
    if ($response->get('respCode') != quickpay_service::RESP_SUCCESS) {
        $err = sprintf("Error: %d => %s", $response->get('respCode'), $response->get('respMsg'));
        throw new Exception($err);
    }
    $arr_ret = $response->get_args(); 
    //告诉用户交易完成
    //echo "订单 {$arr_ret['orderNumber']} 支付成功";
	
	//获取网站订单ID
	$order_arr = explode("-",$arr_ret['orderNumber']);
	$out_trade_no =  $order_arr[1];
	
	$out_trade_no = str_replace("c","," , $out_trade_no );

	$membercoupon_info = get_membercoupons($out_trade_no);
	save_unionmembercoupon_pay($out_trade_no,$arr_ret['qid'],$arr_ret['traceNumber'],$arr_ret['orderAmount']/100,$arr_ret['respMsg'],$arr_ret['respTime'],1);

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

	if($is_pa_order == 1) {
		header("Location:http://baoyang.pahaoche.com/myhome/mycoupon".$membercoupon_info[0]['coupon_type'].".html");exit;
	}else {
		header("Location:http://www.xieche.com.cn/myhome/mycoupon".$membercoupon_info[0]['coupon_type'].".html");exit;
	}
	echo "<br/>" . "支付成功" . "<br/>";

/*
	订单 20140123205547-1558 支付成功Array
(
    [charset] => UTF-8
    [exchangeDate] => 
    [exchangeRate] => 
    [merAbbr] => cs
    [merId] => 105550149170027
    [orderAmount] => 1
    [orderCurrency] => 156
    [orderNumber] => 20140123205547-1558
    [qid] => 201401232055471994592
    [respCode] => 00
    [respMsg] => 支付成功
    [respTime] => 20140123205617
    [settleAmount] => 1
    [settleCurrency] => 156
    [settleDate] => 0123
    [traceNumber] => 199459
    [traceTime] => 0123205547
    [transType] => 01
    [version] => 1.0.0
)

消息版本号	version
字符编码	charset
签名方法	signMethod
签名信息	signature
交易类型	transType
响应码	respCode
响应信息	respMsg
商户名称	merAbbr
商户代码	merId
商户订单号	orderNumber
系统跟踪号	traceNumber
系统跟踪时间	traceTime
交易流水号	qid
交易金额	orderAmount
交易币种	orderCurrency
交易完成时间	respTime
清算金额	settleAmount
清算币种	settleCurrency
清算日期	settleDate
清算汇率	exchangeRate
兑换日期	exchangeDate
系统保留域	cupReserved
*/

}
catch(Exception $exp) {
    $str .= var_export($exp, true);
    die("error happend: " . $str);
}

?>
