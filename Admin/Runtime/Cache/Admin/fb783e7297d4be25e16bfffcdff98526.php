<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html>
<head>
<title>页面提示</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv='Refresh' content='<?php echo ($waitSecond); ?>;URL=<?php echo ($jumpUrl); ?>'>
<style>
html, body{margin:0; padding:0; border:0 none;font:14px Tahoma,Verdana;line-height:150%;background:white}
a{text-decoration:none; color:#174B73; border-bottom:1px dashed gray}
a:hover{color:#F60; border-bottom:1px dashed gray}
div.message{margin:10% auto 0px auto;clear:both;padding:5px; text-align:center; width:45%}
span.wait{color:blue;font-weight:bold}
span.error{color:red;font-weight:bold}
span.success{color:blue;font-weight:bold}
div.msg{margin:20px 0px}
.weixin{width: 1200px; height: 430px; background: url("http://statics.xieche.com.cn/new_2/images/order-complete-weixin/weixin-1.png") center 0px no-repeat; margin: 0 auto; border-top :1px solid silver; margin-top: 150px}
</style>
<script type="text/javascript">
var _bdhmProtocol = (("https:" == document.location.protocol) ? " https://" : " http://");
document.write(unescape("%3Cscript src='" + _bdhmProtocol + "hm.baidu.com/h.js%3F60969e039f9a2a7252a22e6e27e9f16f' type='text/javascript'%3E%3C/script%3E"));
</script>
</head>
<body>

<script language="javascript" type="text/javascript">
    var secs =<?php echo ($waitSecond); ?>; //倒计时的秒数
    var URL ;
    function Load(url){
        URL =url;
        for(var i=secs;i>=0;i--)
        {
            window.setTimeout('doUpdate(' + i + ')', (secs-i) * 1000);
        }
    }

    function doUpdate(num)
    {
        document.getElementById("ShowDiv").innerHTML = num;
    }

    window.onload=function(){
	Load("<?php echo ($jumpUrl); ?>");
    }
  </script>

<div class="message">
	<div class="msg">
	<?php if(isset($message)): ?><span class="success"><?php echo ($msgTitle); echo ($message); ?></span>
	<?php else: ?>
	<span class="error"><?php echo ($msgTitle); echo ($error); ?></span><?php endif; ?>
	</div>
	<div class="tip">
	<?php if(isset($closeWin)): ?>页面将在 <span class="wait" id="ShowDiv"><?php echo ($waitSecond); ?></span> 秒后自动关闭，如果不想等待请点击 <a href="<?php echo ($jumpUrl); ?>">这里</a> 关闭
	<?php else: ?>
		页面将在 <span class="wait" id="ShowDiv"><?php echo ($waitSecond); ?></span> 秒后自动跳转，如果不想等待请点击 <a href="<?php echo ($jumpUrl); ?>">这里</a> 跳转<?php endif; ?>
	</div>
	
</div>
<div class="weixin">
	<?php if($jump != 'jump'): endif; ?>	
</div>
</body>
</html>