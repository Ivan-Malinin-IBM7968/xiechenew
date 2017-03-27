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

    //更新数据库，将交易状态设置为已付款
    //注意保存qid，以便调用后台接口进行退货/消费撤销

	//获取网站订单ID
	$order_arr = explode("-",$arr_ret['orderNumber']);
	$out_trade_no =  $order_arr[1];

	$out_trade_no = str_replace("c","," , $out_trade_no );

	$membercoupon_info = get_membercoupons($out_trade_no);
	save_unionmembercoupon_pay($out_trade_no,$arr_ret['qid'],$arr_ret['traceNumber'],$arr_ret['orderAmount']/100,$arr_ret['respMsg'],$arr_ret['respTime'],0);
	if($membercoupon_info){
		foreach($membercoupon_info as $k=>$v){
			if($v['is_pay']==0){
					update_membercoupon_state($v['membercoupon_id'],1);
					coupon_send_sms($v['membercoupon_id']);
				if($v['coupon_id']){
					update_coupon_count($v['coupon_id']);
				}
			}
		}
	}

    //以下仅用于测试
    //file_put_contents('notify.txt', var_export($arr_ret, true));

}
catch(Exception $exp) {
    //后台通知出错
    //file_put_contents('notify.txt', var_export($exp, true));
}

?>
