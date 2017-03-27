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
<script type="text/javascript" src="__JS__/Think/jquery-1.6.2.min.js"></script>
<!-- 菜单区域  -->
<script>
	function delete_sale(id){
		$.ajax({
			type:'POST',
			url:'__URL__/delete_sale',
			cache:false,
			dataType:'text',
			data:'id='+id,
			success:function(data){
				if(data == 1){
					alert("删除成功");
					window.location.reload();
				}else{
					alert("删除失败");
				}
			}
		})
	}
</script>
<!-- 主页面开始 -->
<div id="main" class="main" >

	<!-- 主体内容  -->
	<div class="content" >
	<div class="title">技师管理</div>
	<div>
		<!-- <form action="__APP__/Shop/Salemanage" method="post" >
			店铺类别：<select name="shop_type">
				<option value="1" <?php if($shop_type == 1): ?>selected<?php endif; ?> >4S店</option>
            	<option value="2" <?php if($shop_type == 2): ?>selected<?php endif; ?> >快修店</option>
			</select><br>
			店铺名称：<input TYPE="text" id="shop_name" class="large bLeftRequire" NAME="shop_name" value="<?php echo ($shop_name); ?>"><br>
			负责人 ： <input TYPE="text" id="shop_boss" class="large bLeftRequire" NAME="shop_boss" value="<?php echo ($shop_boss); ?>"><br>
			<input type="submit" value="搜索">
		</form> -->
	</div>
	<div class="impBtn hMargin fLeft shadow">
		<input id="" class="add imgButton" type="button" onclick="add()" value="添加" name="add">
	</div>
	<div class="list">
		<table class="list">
			<tr class="row">
				<th width="5%" align=center>ID</th>
				<th width="10%" align=center>技师姓名</th>
				<th width="15%" align=center>手机号</th>
				<th width="40%" align=center>地址</th>
				<th width="20%" align=center>擅长车型</th>
				<th width="10%" align=center>操作</th>
			</tr>
			<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
					<td align=center><?php echo ($vo["id"]); ?></td>
					<td align=center><?php echo ($vo["truename"]); ?></td>
					<td align=center><?php echo ($vo["mobile"]); ?></td>
					<td align=center><?php echo ($vo["address"]); ?></td>
					<td align=center><?php echo ($vo["brand_name"]); ?></td>
					<td align=center><a href="__APP__/Carservice/technician/edit/user_id/<?php echo ($vo["user_id"]); ?>">编辑</a></td>
				</tr><?php endforeach; endif; else: echo "" ;endif; ?>
			<tr>
				<td colspan="4"><?php echo ($page); ?></td>
			</tr>
		</table>
	</div>
	
	</div>
</div>