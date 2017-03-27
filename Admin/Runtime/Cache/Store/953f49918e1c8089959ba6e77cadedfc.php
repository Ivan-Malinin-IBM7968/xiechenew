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
<!-- 菜单区域  -->

<SCRIPT LANGUAGE="JavaScript">
    <!--
    function cache(){
        ThinkAjax.send('__URL__/cache','ajax=1');
    }
    //-->
</SCRIPT>
<style>
    .input-blue{background: url("__WEB__/Public/Admin/Images/idx_btnbg.gif") 50% 0% repeat-x; border: 1px solid #316835; text-align: center; cursor: pointer; font-size: bold;}
</style>
<!-- 主页面开始 -->
<div id="main" class="main" >
    <!-- 主体内容  -->
    <div class="content" >
        <div class="title">用户列表</div>
        <!--  功能操作区域  -->
        <div class="operate" >
			<div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="add" value="添加" onclick="add()" class="add imgButton"></div>
            <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="edit" value="编辑" onclick="edit()" class="edit imgButton"></div>
            <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="sort" value="排序" onclick="sort()" class="sort imgButton"></div>
            <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="delete" value="删除" onclick="del()" class="delete imgButton"></div>
            <div class="impBtn hMargin fLeft shadow" ><input type="button" id="" name="sort" value="缓存" onclick="cache()" class="sort imgButton"></div>
			<input value="用户回访" class="input-blue" onclick="window.location.href='/Admin/index.php/Store/member/feedback/'"  name="feedback" />
            <!-- 查询区域 -->
           <FORM METHOD=POST ACTION="__URL__">
            <div class="fRig">
                    <div class="fLeft">
                    	<select name="type">
                    		<option value="mobile">电话</option>
                    		<option value="username">用户名</option>
                    		<option value="uid">用户ID</option>
                    		<option value="cardid">卡号</option>
                    	</select>
                    	<span id="key">
                    		<input TYPE="text" NAME="keyword" title="用户名或密码查询" value="<?php echo ($_POST['name']); ?>" class="medium" >
                    	</span>
                    </div>
                    <div class="impBtn hMargin fLeft shadow" ><input type="submit" id="" name="search" value="查询" onclick="" class="search imgButton"></div>
            </div>
            </FORM>
        </div>
        <!-- 功能操作区域结束 -->

        <!-- 列表显示区域<?php if($fromstatus == 1): ?>百度<?php endif; ?>-->
        <div class="list" >
				<!-- Think 系统列表组件开始 -->
<table id="checkList" class="list" cellpadding=0 cellspacing=0 ><tr><td height="5" colspan="12" class="topTd" ></td></tr><tr class="row" ><th width="8"><input type="checkbox" id="check" onclick="CheckAll('checkList')"></th><th width="10%"><a href="javascript:sortBy('uid','<?php echo ($sort); ?>','index')" title="按照编号<?php echo ($sortType); ?> ">编号<?php if(($order) == "uid"): ?><img src="../Public/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('username','<?php echo ($sort); ?>','index')" title="按照用户名<?php echo ($sortType); ?> ">用户名<?php if(($order) == "username"): ?><img src="../Public/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('cardid','<?php echo ($sort); ?>','index')" title="按照卡号<?php echo ($sortType); ?> ">卡号<?php if(($order) == "cardid"): ?><img src="../Public/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('mobile','<?php echo ($sort); ?>','index')" title="按照电话<?php echo ($sortType); ?> ">电话<?php if(($order) == "mobile"): ?><img src="../Public/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('sale','<?php echo ($sort); ?>','index')" title="按照抵用券<?php echo ($sortType); ?> ">抵用券<?php if(($order) == "sale"): ?><img src="../Public/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('fromstatus','<?php echo ($sort); ?>','index')" title="按照来源<?php echo ($sortType); ?> ">来源<?php if(($order) == "fromstatus"): ?><img src="../Public/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('email','<?php echo ($sort); ?>','index')" title="按照邮箱<?php echo ($sortType); ?> ">邮箱<?php if(($order) == "email"): ?><img src="../Public/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th width="15%"><a href="javascript:sortBy('reg_time','<?php echo ($sort); ?>','index')" title="按照注册时间<?php echo ($sortType); ?> ">注册时间<?php if(($order) == "reg_time"): ?><img src="../Public/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th width="15%"><a href="javascript:sortBy('ip','<?php echo ($sort); ?>','index')" title="按照IP地址<?php echo ($sortType); ?> ">IP地址<?php if(($order) == "ip"): ?><img src="../Public/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th><a href="javascript:sortBy('status','<?php echo ($sort); ?>','index')" title="按照状态<?php echo ($sortType); ?> ">状态<?php if(($order) == "status"): ?><img src="../Public/images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"><?php endif; ?></a></th><th >操作</th></tr><?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$member): $mod = ($i % 2 );++$i;?><tr class="row" onmouseover="over(event)" onmouseout="out(event)" onclick="change(event)" ><td><input type="checkbox" name="key"	value="<?php echo ($member["id"]); ?>"></td><td><?php echo ($member["uid"]); ?></td><td><a href="javascript:read('<?php echo (addslashes($member["uid"])); ?>')"><?php echo ($member["username"]); ?></a></td><td><?php echo ($member["cardid"]); ?></td><td><?php echo ($member["mobile"]); ?></td><td><?php echo ($member["sale"]); ?></td><td><?php echo ($member["fromstatus"]); ?></td><td><?php echo ($member["email"]); ?></td><td><?php echo (todate($member["reg_time"],'Y-m-d H：i：s')); ?></td><td><?php echo ($member["ip"]); ?></td><td><?php echo (statusshow($member["status"])); ?></td><td><a href="javascript:coupon('<?php echo ($member["uid"]); ?>')">优惠券</a>&nbsp;<a href="javascript:read('<?php echo ($member["uid"]); ?>')">详情</a>&nbsp; <?php echo (showstatus($member["status"],$member['uid'])); ?>&nbsp; <?php echo (memo($member["uid"],$member['memo'])); ?>&nbsp; <?php echo (resetpw($member["uid"],$member['mobile'])); ?>&nbsp;<a href="javascript:edit('<?php echo ($member["uid"]); ?>')">添加来源</a>&nbsp;</td></tr><?php endforeach; endif; else: echo "" ;endif; ?><tr><td height="5" colspan="12" class="bottomTd"></td></tr></table>
<!-- Think 系统列表组件结束 -->

        </div>
        <!--  分页显示区域 -->
        <div class="page"><?php echo ($page); ?></div>
        <!-- 列表显示区域结束 -->
    </div>
   

    <!-- 主体内容结束 -->
</div>
<!-- 主页面结束 -->