<?php
 /**
  * 功能：异步通知页面
  * 版本：1.0
  * 日期：2012-10-11
  * 作者：中国银联UPMP团队
  * 版权：中国银联
  * 说明：以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己的需要，按照技术文档编写,并非一定要使用该代码。该代码仅供参考。
  * */

header('Content-Type:text/html;charset=utf-8');
require_once("lib/upmp_service.php");
require_once("conf/function.php");

if (UpmpService::verifySignature($_POST)){// 服务器签名验证成功
	//请在这里加上商户的业务逻辑程序代码
	//获取通知返回参数，可参考接口文档中通知参数列表(以下仅供参考)
	$transStatus = $_POST['transStatus'];// 交易状态
	if (""!=$transStatus && "00"==$transStatus){
		// 交易处理成功
		//获取网站订单ID
		$order_arr = explode("-",$_POST['orderNumber']);
		$out_trade_no =  $order_arr[1];

		$out_trade_no = str_replace("c","," , $out_trade_no );

		$membercoupon_info = get_membercoupons($out_trade_no);
		save_unionmembercoupon_pay($out_trade_no,$_POST['qn'],'',$_POST['settleAmount']/100,$_POST['respMsg'],$_POST['settleDate'],0);
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

	}else {
	}
	echo "success";
}else {// 服务器签名验证失败
	echo "fail";
}

?>