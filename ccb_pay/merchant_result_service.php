<?php
include_once 'function.php';
session_start();
header("charset:GBK");
$POSID = $_REQUEST['POSID'];
$BRANCHID = $_REQUEST['BRANCHID'];
global $ORDERID ;
$ORDERID= $_REQUEST['ORDERID'];
$PAYMENT = $_REQUEST['PAYMENT'];
$CURCODE = $_REQUEST['CURCODE'];
$REMARK1 = $_REQUEST['REMARK1'];
$REMARK2 = $_REQUEST['REMARK2'];
$ACC_TYPE = $_REQUEST['ACC_TYPE'];//仅服务器通知有
$SUCCESS  = $_REQUEST['SUCCESS'];
$TYPE = $_REQUEST['TYPE'];
$REFERER = $_REQUEST['REFERER'];
$CLIENTIP = $_REQUEST['CLIENTIP'];
$ACCDATE = $_REQUEST['ACCDATE'];
$USRMSG = $_REQUEST['USRMSG'];
$SIGN = $_REQUEST['SIGN'];
$arr="";
$arr.='POSID='.$POSID;
$arr.='&BRANCHID='.$BRANCHID;
$arr.='&ORDERID='.$ORDERID;
$arr.='&PAYMENT='.$PAYMENT;
$arr.='&CURCODE='.$CURCODE;
$arr.='&REMARK1='.$REMARK1;
$arr.='&REMARK2='.$REMARK2;
$arr.='&ACC_TYPE='.$ACC_TYPE;
$arr.='&SUCCESS='.$SUCCESS;
$arr.='&TYPE='.$TYPE;
$arr.='&REFERER='.$REFERER;
$arr.='&CLIENTIP='.$CLIENTIP;
$arr.='&USRMSG='.$USRMSG;
$arr.='&SIGN='.$SIGN;
if($SUCCESS=="Y"){
    saveccblog($ORDERID,$PAYMENT,$arr."YYYYYY服务器端反馈判断成功".$_SESSION['clientip'].$_SESSION['orderid'].$_SESSION['payment']);
}else{
    saveccblog($ORDERID,$PAYMENT,$arr."YYYYYY服务器端反馈判断失败".$_SESSION['clientip'].$_SESSION['orderid'].$_SESSION['payment']);

}
//判断是上门保养 还是 团购券
if($SUCCESS=="Y") {
    if ($SIGN) {
        saveccblog($ORDERID,$PAYMENT,$arr);
        $ORDERID2 = substr($ORDERID, 0, 1);
        if ($ORDERID2 == 'm') {
            //$ORDERID = $_SESSION['orderid'];
            global $ORDERID3;
            $ORDERID3= substr($ORDERID, 1);
            $resrvation_info = get_reservation_order($ORDERID3);
            if ($resrvation_info['pay_status'] == 0) {
                update_reservation_order_state($ORDERID3, 1,6);
                saveccblog($ORDERID3,$PAYMENT,"上门保养支付成功！");
            }
        } else{
            if($SUCCESS=="Y" and $REFERER=="http://www.xieche.com.cn/ccb_pay/merchant.php"){
                saveccblog($ORDERID,$PAYMENT,$arr."团购订单！");
                //$ORDERID = $_SESSION['orderid'];
                $ORDERID = str_replace('|', ',', $ORDERID);
                $membercoupon_info = get_membercoupons($ORDERID);
                // var_dump($membercoupon_info);
                if ($membercoupon_info){
                    $is_pa_order = 0;
                    foreach ($membercoupon_info as $k => $v){
                        // var_dump($k ."=>".$v);
                        if ($v['is_pay'] == 0) {
                            update_membercoupon_state($v['membercoupon_id'], 1);
                            coupon_send_sms($v['membercoupon_id']);
                            saveccblog($v['membercoupon_id'],$v['coupon_amount'],"团购券支付成功！");
                            if ($v['coupon_id']) {
                                update_coupon_count($v['coupon_id']);
                            }
                        } else {
                            saveccblog($v['membercoupon_id'],$v['coupon_amount'],"团购券已支付！");
                        }
                        if ($v['pa'] == 1) {
                            $is_pa_order = 1;
                        }
                    }
                }
            }
        }
    }
}
////    $out = get_data_from_server("127.0.0.1",1224,$arr."\n");
////    saveccblog($ORDERID,$PAYMENT,$arr.$out);

?>

