<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<HEAD>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<TITLE>『<?php echo (C("web_name")); ?>管理平台』By ThinkPHP <?php echo (THINK_VERSION); ?></TITLE>
<link rel="stylesheet" type="text/css" href="__CSS__/style.css" />
<base target="main" />
</HEAD>
<body>
<!-- 头部区域 -->
<div id="header" class="header">
<div class="headTitle" style="margin:8pt 10pt"> <?php echo (C("web_name")); ?> 管理平台</div>
	<!-- 功能导航区 -->
	<div class="topmenu">
<ul>
<li><span><a href="#" onclick="sethighlight(0); parent.menu.location='__APP__/Admin/Public/menu/title/后台首页';parent.main.location='__APP__/Admin/Public/main/';return false;">后台首页</a></span></li>
<?php if(is_array($nodeGroupList)): $i = 0; $__LIST__ = $nodeGroupList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$tag): $mod = ($i % 2 );++$i;?><li><span><a href="#" onclick="sethighlight(<?php echo ($i); ?>); parent.menu.location='__APP__/Admin/Public/menu/tag/<?php echo ($tag["id"]); ?>/title/<?php echo ($tag["title"]); ?>/name/<?php echo ($tag["name"]); ?>';return false;"><?php echo ($tag["title"]); ?></a></span></li><?php endforeach; endif; else: echo "" ;endif; ?>
</ul>
</div>
	<div class="nav">
	欢迎你！<?php echo ($_SESSION['loginAdminUserName']); ?> <A HREF="__APP__/" target="_blank">首页</A>
	<A HREF="__URL__/password/"><img src="__IMG__/checked_out.png" width="16" height="16" border="0" alt="" align="absmiddle"> 修改密码</A> <A HREF="__URL__/profile/"><IMG SRC="__IMG__/write.gif" WIDTH="17" HEIGHT="16" BORDER="0" ALT="" align="absmiddle"> 修改资料</A> <A HREF="__URL__/logout/" target="_top"><IMG SRC="__IMG__/error.gif" WIDTH="20" HEIGHT="20" BORDER="0" ALT="" align="absmiddle"> 退 出</A></div>
</div>
<script>
function sethighlight(n) {
	var lis = document.getElementsByTagName('span');
	for(var i = 0; i < lis.length; i++) {
		lis[i].className = '';
	}
	lis[n].className = 'current';
}
sethighlight(7);
</script>
</body>
</html>