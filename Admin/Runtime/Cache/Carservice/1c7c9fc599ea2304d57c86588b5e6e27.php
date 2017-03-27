<?php if (!defined('THINK_PATH')) exit();?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<HEAD>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<TITLE>『<?php echo (C("web_name")); ?>管理平台』By ThinkPHP <?php echo (THINK_VERSION); ?></TITLE>
<link rel="stylesheet" type="text/css" href="__CSS__/blue.css" />
 <script type="text/javascript" src="__JS__/jquery-1.9.1.js"></script>
<script type="text/javascript" src="__JS__/common.js"></script>
<script type="text/javascript" src="__PUBLIC__/Js/Base.js"></script><script type="text/javascript" src="__PUBLIC__/Js/prototype.js"></script><script type="text/javascript" src="__PUBLIC__/Js/mootools.js"></script><script type="text/javascript" src="__PUBLIC__/Js/Think/ThinkAjax.js"></script><script type="text/javascript" src="__PUBLIC__/Js/Form/CheckForm.js"></script><script type="text/javascript" src="__PUBLIC__/Js/Util/ImageLoader.js"></script>



<SCRIPT LANGUAGE="JavaScript">
//<!--
//指定当前组模块URL地址 
var URL = '__URL__';
var APP	 =	 '__APP__';
var PUBLIC = '__PUBLIC__';
ThinkAjax.image = [	 '__IMG__/loading2.gif', '__IMG__/ok.gif','__IMG__/update.gif' ]
ImageLoader.add("__IMG__/bgline.gif","__IMG__/bgcolor.gif","__IMG__/titlebg.gif");
ImageLoader.startLoad();

//-->
</SCRIPT>


</HEAD>

<body onload="loadBar(0)">
<div id="loader" >页面加载中...</div>
<style>
  .popbox{width:600px; height: 440px font-size: 14px; border: 1px solid #aaaaaa; border-radius: 5px; background: #FFFFFF; position: fixed; left: 50%; margin-left: -300px; top: 50%; margin-top:-220px; _position:absolute; display: none;}
  .popbox .popboxtop{height: 40px; border-bottom: 1px solid #aaaaaa; line-height: 40px;}
  .popbox .popboxtop a{float: right; height: 40px; width: 40px; text-align: center; border-left: 1px solid #aaa; font-size: 18px; cursor: pointer;}
  .popbox .popboxtop a:hover{background: #ccc;}
  .popbox .popboxtop h3{font-size: 14px; margin: 0 0 0 10px; padding: 0; line-height: 40px;}
  .popbox .popboxcon{height: 400px; overflow-y: auto;}
  .popbox .popboxcon ul{margin: 0; padding: 0;}
  .popbox .popboxcon ul li{list-style: none; border-bottom: 1px dashed #ccc; line-height: 30px; color: #555555; font-size: 12px; text-indent: 10px;}
  .popbox .popboxcon ul li span{display: inline-block; width: 155px; text-indent: 0px;}
</style>

<div class="popbox" id="showlog">
  <div class="popboxtop"><a id="popclose" onclick="jQuery(this).parent().parent().hide()">×</a><h3>操作日志</h3></div>
  <div class="popboxcon" id="showlogcontent"></div>
</div>

<script>
function showlog(controller,id){
	jQuery.post('/Admin/index.php/Admin/Showlog/index',{c:controller,id:id},function(data){
		if(data){
			var html_log = '<ul>';
			jQuery.each(data,function(index,val){
				html_log += '<li>'+val.log+'</li>';
			})
			html_log += '</ul>';
			jQuery('#showlog').show();
			jQuery('#showlogcontent').html(html_log);
		}else{
	  jQuery('#showlog').show();
	  jQuery('#showlogcontent').html("<ul><li>暂无日志</li></ul>");
    }
	},'json')
}
</script>
<script src="__JS__/Think/jquery-1.6.2.min.js"></script>
<!-- 菜单区域  -->
<SCRIPT LANGUAGE="JavaScript">
    <!--
    function cache(){
        ThinkAjax.send('__URL__/cache','ajax=1');
    }
    //-->
	function checkName(){
	ThinkAjax.send('__URL__/checkAccount/','ajax=1&account='+$F('account'));
	}
</SCRIPT>
<!-- 主页面开始 -->
<div id="main" class="main" >
    <!-- 主体内容  -->
    <div class="content" >
         <div class="title">新增技师 [ <A HREF="__URL__">返回列表</A> ]</div>
		 <form METHOD=POST id="form1" name="form1" action="__URL__/do_add">
			<table cellpadding=3 cellspacing=3>
				<tr>
					<td class="tRight" >用户名：</td>
					<td class="tLeft" ><input type="text" class="medium bLeftRequire"  check='Require' warning="用户名不能为空" id="account" name="account" value="<?php echo ($vo["account"]); ?>"><input type="button" value="检测帐号" onclick="checkName()" class="submit hMargin"></td>
				</tr>
				<tr>
				<td class="tRight" >密码：</td>
					<td class="tLeft" ><input type="text" id="password" class="medium bLeft" name="password" value=""></td>
				</tr>
				<TR>
                    <TD class="tRight" >昵称：</TD>
                    <TD class="tLeft" ><input TYPE="text" id="nickname" class="large bLeftRequire" NAME="nickname"></TD>
                </TR>
				<TR>
                    <TD class="tRight" >手机号码：</TD>
                    <TD class="tLeft" ><input TYPE="text" id="mobile" class="large bLeftRequire" NAME="mobile"></TD>
                </TR>
				<TR>
                    <TD class="tRight" >设备号码：</TD>
                    <TD class="tLeft" ><input TYPE="text" id="pad_mobile" class="large bLeftRequire" NAME="pad_mobile"></TD>
                </TR>
				<TR>
                    <TD class="tRight" >设备卡号：</TD>
                    <TD class="tLeft" ><input TYPE="text" id="pad_sim" class="large bLeftRequire" NAME="pad_sim"></TD>
                </TR>
				<TR>
                    <TD class="tRight" >地址：</TD>
                    <TD class="tLeft" ><input TYPE="text" id="address" class="large bLeftRequire" NAME="address"></TD>
                </TR>
				<TR>
                    <TD class="tRight" >擅长车型：</TD>
                    <TD class="tLeft" ><input TYPE="text" id="brand" class="large bLeftRequire" NAME="brand"></TD>
                </TR>
				<TR>
                    <TD class="tRight" >状态：</TD>
                    <TD class="tLeft" >
						<select name="status" >
                    	<option value="1">开启</option>
                    	<option value="2">禁用</option>
						</select>
					</TD>
                </TR>
				<tr>
				<td class="tRight tTop">备  注：</td>
					<td class="tLeft"><TEXTAREA class="large bLeft"  name="remark" rows="5" cols="57"></textarea></td>
				</tr>
                <TR>
                    <TD class="tRight" ></TD>
                    <TD class="tLeft" ><input TYPE="button" value="提交" onclick="check()"></TD>
                </TR>
			</table>
		 </form>
    </div>
    <!-- 主体内容结束 -->
</div>
<!-- 主页面结束 -->
<script>
function check(){
	var account = $('#account').val();
	if(account==''){
		alert('必须填写用户名');
		return false;
	}
	var password = $('#password').val();
	if(password==''){
		alert('必须填写登录密码');
		return false;
	}
	var nickname = $('#nickname').val();
	if(nickname==''){
		alert('必须填写昵称');
		return false;
	}
	var mobile = $('#mobile').val();
	if(mobile==''){
		alert('必须填写手机号');
		return false;
	}
	$('#form1').submit();
}
</script>