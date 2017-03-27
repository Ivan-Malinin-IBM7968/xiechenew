<?php
session_start();
include_once 'function.php';
//print_r($_REQUEST);exit;
	//require_once("config.inc");
	//获得表单传过来的数据
	$MERCHANTID = '105290075381305';
	$POSID = '306812816';
	$BRANCHID = '310000000';
	$ORDERID = $_REQUEST["ORDERID"];
	$PAYMENT = $_REQUEST["PAYMENT"];
	$ORDERID = str_replace(',','|',$ORDERID);

	$_SESSION["orderid"]=$ORDERID;
	$_SESSION["payment"]=$PAYMENT;
	$CURCODE = '01';

	$REMARK1 = '';
	$REMARK2 = '';
	$TXCODE = '520100';

	$bankURL = 'https://ibsbjstar.ccb.com.cn/app/ccbMain';
	$TYPE = 1;
	$pubstr='30819d300d06092a864886f70d010101050003818b0030818702818100f99046359d93f920c225d273c3758a55ac0ae2091f975496c379a576db96e996e368b5ab1a7ca3a3fbf10ec34ef7f7d354d1b7b522dbcb69d42bd235c4be1d5e6988789e74156c00555666657b29b71bd626df7ce9da378436642300a80d14b2895b28632262700c7bd819c7321485e0fd790ee4db0b4d703aa455272df2bb71020111';
	$PUB32TR2 = substr($pubstr, -30);

	$GATEWAY = '';
	$CLIENTIP = getIPaddress();   //可以自己写个方法,客户端的局域网ip；
    $_SESSION["clientip"]=$CLIENTIP;
	$REGINFO = '';

	$PROINFO = '';
	$REFERER = '';

	//连接字符串
	$temp = "";
	$temp_New ="";
	$temp_New1 ="";
	$temp .='MERCHANTID='.$MERCHANTID.'&POSID='.$POSID.'&BRANCHID='.$BRANCHID.'&ORDERID='.$ORDERID.'&PAYMENT='.$PAYMENT.'&CURCODE='.$CURCODE.'&TXCODE='.$TXCODE.'&REMARK1='.$REMARK1.'&REMARK2='.$REMARK2;
	$temp_New .=$temp."&TYPE=".$TYPE."&PUB=".$PUB32TR2."&GATEWAY=".$GATEWAY."&CLIENTIP=".$CLIENTIP."&REGINFO=".$REGINFO."&PROINFO=".$PROINFO."&REFERER=".$REFERER;
	$temp_New1 .=$temp."&TYPE=".$TYPE."&GATEWAY=".$GATEWAY."&CLIENTIP=".$CLIENTIP."&REGINFO=".$REGINFO."&PROINFO=".$PROINFO."&REFERER=".$REFERER;

	$strMD5 = md5($temp_New);
	//$URL .= $bankURL."?".$temp_New1."&MAC=".$strMD5;
		?>
		<html>
		<head>
			<title>商户订单测试</title>
			<meta http-equiv = "Content-Type" content = "text/html;charset=GBK">
		</head>
		<div><p>连接验证中，请稍候~</p></div>
		<body bgcolor = "#FFFFFF" text = "#000000" onload="form1.submit()">
		<form name = "form1" method = "post" action = "<?php echo ($bankURL); ?>">
			<input type = "hidden" name = "MERCHANTID" value = "<?php echo ($MERCHANTID);?>"/>
			<input type = "hidden" name = "POSID" value = "<?php echo ($POSID);?>"/>
			<input type = "hidden" name = "BRANCHID" value = "<?php echo ($BRANCHID);?>"/>
			<input type = "hidden" name = "ORDERID" value = "<?php echo ($ORDERID);?>"/>
			<input type = "hidden" name = "PAYMENT" value = "<?php echo ($PAYMENT);?>"/>
			<input type = "hidden" name = "CURCODE" value = "<?php echo ($CURCODE);?>"/>
			<input type = "hidden" name = "TXCODE" value = "<?php echo ($TXCODE);?>"/>
			<input type = "hidden" name = "REMARK1" value = "<?php echo ($REMARK1);?>"/>
			<input type = "hidden" name = "REMARK2" value = "<?php echo ($REMARK2);?>"/>
			<input type = "hidden" name = "TYPE" value = "<?php echo ($TYPE);?>"/>
			<input type = "hidden" name = "GATEWAY" value = "<?php echo ($GATEWAY);?>"/>
			<input type = "hidden" name = "CLIENTIP" value = "<?php echo ($CLIENTIP);?>"/>
			<input type = "hidden" name = "REGINFO" value = "<?php echo ($REGINFO);?>"/>
			<input type = "hidden" name = "PROINFO" value = "<?php echo ($PROINFO);?>"/>
			<input type = "hidden" name = "REFERER" value = "<?php echo ($REFERER);?>"/>
			<input type = "hidden" name = "MAC" value = "<?php echo ($strMD5);?>"/>
		</form>
		</body>

		</html>

