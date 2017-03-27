<?php
include_once 'function.php';
session_start();
header("charset:GBK");
$POSID = $_REQUEST['POSID'];
$BRANCHID = $_REQUEST['BRANCHID'];
$ORDERID = $_REQUEST['ORDERID'];
$PAYMENT = $_REQUEST['PAYMENT'];
$CURCODE = $_REQUEST['CURCODE'];
$REMARK1 = $_REQUEST['REMARK1'];
$REMARK2 = $_REQUEST['REMARK2'];
//$ACC_TYPE = $_REQUEST['ACC_TYPE'];//仅服务器通知有
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
    saveccblog($ORDERID,$PAYMENT,$arr."YYYYYY页面反馈判断成功".$_SESSION['clientip'].$_SESSION['orderid'].$_SESSION['payment']);
}else{
    saveccblog($ORDERID,$PAYMENT,$arr."YYYYYY页面反馈判断失败".$_SESSION['clientip'].$_SESSION['orderid'].$_SESSION['payment']);
}
if($SUCCESS=="Y" and $CLIENTIP == $_SESSION['clientip'] and $ORDERID==$_SESSION['orderid'] and $PAYMENT==$_SESSION['payment']){
    if ($SIGN){
        $ORDERID = substr($ORDERID, 0, 1);
        //上门保养跳转！
        if ($ORDERID == 'm') {
            header("location:http://www.xieche.com.cn/Mobilecar-mycarservice");
        }else {
            if ($REFERER == "http://www.xieche.com.cn/ccb_pay/merchant.php") {
                //团购券页面跳转！
                $ORDERID = $_SESSION['orderid'];
                $ORDERID2 = str_replace('|', ',', $ORDERID);
                $membercoupon_info = get_membercoupons($ORDERID2);
                // var_dump($membercoupon_info);
                if ($membercoupon_info) {
                    $is_pa_order = 0;
                    foreach ($membercoupon_info as $k => $v) {
                        if ($v['pa'] == 1) {
                            $is_pa_order = 1;
                        }
                    }
                }
                //------------------------------
                //处理业务完毕 跳转页面
                //------------------------------
                if ($is_pa_order == 1) {
                    header("Location:http://baoyang.pahaoche.com/myhome/mycoupon");
                    exit;
                } else {
                    header("Location:http://www.xieche.com.cn/myhome/my_coupon");
                    exit;
                }
            }
        }
    }
    //header("location:http://www.xieche.com.cn/all_order");
} else {
        echo "支付失败!";
       // header("location:http://www.xieche.com.cn/ccb_pay/all_order");
    }
?>

