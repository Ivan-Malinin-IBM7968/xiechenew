<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//Dtd XHTML 1.0 Transitional//EN" "http://www.w3.org/tr/xhtml1/Dtd/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo (C("web_name")); ?>管理系统登录</title>
<link rel="stylesheet" type="text/css" href="__CSS__/style.css" />
<script type="text/javascript" src="__JS__/prototype.js"></script>
<script type="text/javascript" src="__PUBLIC__/Js/Base.js"></script><script type="text/javascript" src="__PUBLIC__/Js/mootools.js"></script><script type="text/javascript" src="__PUBLIC__/Js/Think/ThinkAjax.js"></script><script type="text/javascript" src="__PUBLIC__/Js/PHP/php.min.js"></script><script type="text/javascript" src="__PUBLIC__/Js/Form/CheckForm.js"></script>
<script LANGUAGE="JavaScript">
<!--
var PUBLIC	 =	 '__PUBLIC__';
ThinkAjax.image = [	 '__IMG__/loading2.gif', '__IMG__/ok.gif','__IMG__/update.gif' ]
ThinkAjax.updateTip	=	'进行中～';

function loginHandle(data,status){
	if (status==1)
	{
		$('result').innerHTML	=	'<span style="color:blue"><IMG SRC="__IMG__/ok.gif" WIDTH="20" HEIGHT="20" BORDER="0" ALT="" align="absmiddle" > 登录成功！3 秒后跳转～</span>';
		$('form1').reset();
		window.location = '__APP__/Admin';
	}else{
		 fleshVerify();
	}
}

function keydown(e){
	var e = e || event;

	if (e.keyCode==13)
	{



		ThinkAjax.sendForm('form1','__URL__/checkLogin/',loginHandle,'result');
	}
}

window.onload=function(){
      fleshVerify(1);
}

function fleshVerify(type){ 
	//重载验证码
	var timenow = new Date().getTime();
	if (type)
	{
	$('verifyImg').src= '__URL__/verify/adv/1/'+timenow;
	}else{
	$('verifyImg').src= '__URL__/verify/'+timenow;
	}

}
	if ( window != top){  window.top.location =window.location;}  



//-->



</script>

<script>
function giveeverify(){
	var mobile = $('#mobile').val();
	$.ajax({
			type: "POST",
			url: "__URL__/giveeverify",
			cache: false,
			data: "mobile="+mobile,
			success: function(data){
				
				alert('发送成功！请注意查收短信！');
			}
	})

}

</script>


</head>
<body onload='document.login.account.focus()'>    
	<form METHOD=POST name="login" id="form1" ACTION="__URL__/checkLogin">
		<div class="tCenter hMargin">
			<table id="checkList" class="login shadow" cellpadding=0 cellspacing=0 >
				<tr>
					<td height="5" colspan="2" class="topTd" ></td>
				</tr>
				<tr class="row" >
					<Th colspan="2" class="tCenter space" ><img src="__IMG__/preview_f2.png" width="32" height="32" border="0" alt="" align="absmiddle"> <?php echo (C("web_name")); ?>管理系统登录</Th>
				</tr>
				<tr>
					<td height="5" colspan="2" class="topTd" ></td>
				</tr>
				<tr class="row" >
					<td colspan="2" class="tCenter"><div id="result" class="result none"></div></td>
				</tr>
				<tr class="row" >
					<td class="tRight" width="25%">帐 号：</td>
					<td><input type="text" class="medium bLeftRequire" check="Require" warning="请输入帐号" NAME="account" value="<?php echo ($account); ?>"></td>
				</tr>
				<input type="hidden" id="hide_count" name="hide_count" value="<?php echo ($count); ?>">
                                    
                                    
<!--				<?php if($count == 0): ?><tr class="row" >
						<td class="tRight" style="white-space:nowrap;">手机验证码：</td>
						<td><input type="text" onkeydown="keydown(event)" class="small bLeftRequire" check="Require" warning="请输入手机验证码" NAME="mobileeverify" id="mobileeverify"><input type="button" name="btn_ok" id="btn_ok" value="获取手机验证码" onclick="giveeverify();">
						<input type="button" name="btn_ok" id="btn_ok" value="获取手机验证码" onclick="ThinkAjax.sendForm('form1','__URL__/giveeverify/',loginHandle,'result')">
						</td>
					</tr><?php endif; ?>-->
                                    
				<tr class="row" >
					<td class="tRight">密 码：</td>
					<td><input type="password" class="medium bLeftRequire" check="Require" warning="请输入密码" NAME="password" value=""></td>
				</tr>
				<?php if((C("ADV_LOGIN_VERIFY")) == "1"): ?><tr class="row" >
						<td class="tRight">验证码：</td>
						<td><input type="text" onkeydown="keydown(event)" class="medium bLeftRequire" check="Require" warning="请输入验证码" NAME="verify"> </td>
					</tr>
					<tr class="row" >
						<td class="tCenter" colspan="2" style="color:gray;">请输入帐号验证码对应的英文字母</td>
					</tr>
					<tr class="row" >
						<td class="tRight">数 字：<BR>英 文：</td>
						<td><img id="verifyImg" SRC="__URL__/verify/adv/1/" onclick="fleshVerify(1)" BORDER="0" ALT="点击刷新验证码" style="cursor:pointer" align="absmiddle"></td>
					</tr>
				<?php else: ?>
					<tr class="row" >
						<td class="tRight">验证码：</td>
						<td><input type="text" onkeydown="keydown(event)" class="small bLeftRequire" check="Require" warning="请输入验证码" NAME="verify" > <img id="verifyImg" SRC="__URL__/verify/" onclick="fleshVerify()" BORDER="0" ALT="点击刷新验证码" style="cursor:pointer" align="absmiddle"></td>
					</tr><?php endif; ?>
				
				<tr class="row" >
					<td class="tCenter" align="justify" colspan="2">
						<input type="hidden" name="ajax" value="1">
						<!-- <input type="reset" value="重 置" class="submit small"> -->
						<input type="button" value="登 录" class="submit medium hMargin"  onclick="ThinkAjax.sendForm('form1','__URL__/checkLogin/',loginHandle,'result')" >
					</td>
				</tr>
				<tr>
					<td height="5" colspan="2" class="bottomTd" ></td>
				</tr>
			</table>
		</div>
	</form>
</body>
</html>