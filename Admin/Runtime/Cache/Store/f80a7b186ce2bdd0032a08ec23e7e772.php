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
	$(function(){
		$("#add_model").click(function(){
			var model_name = $("#model_name").val();
			var series_id = '<?php echo ($series_info["series_id"]); ?>';
			$.ajax({
				type:'POST',
				url:'__APP__/Store/Carmodel/add_model',
				cache:false,
				dataType:'text',
				data:'model_name='+model_name+'&series_id='+series_id,
				success:function(data){
					if(data == 1){
						alert("添加成功");
						window.location.reload();
					}
				}
			})
		})
	})
	
	function update_model(id){
		var model_name = $("#model_name_"+id).val();
		$.ajax({
			type:'POST',
			url:'__APP__/Store/Carmodel/save_model',
			cache:false,
			dataType:'text',
			data:'model_name='+model_name+'&model_id='+id ,
			success:function(data){
				if(data == 1){
					alert("修改成功");
					window.location.reload();
				}else{
					alert("修改失败");
				}
			}
		})
	}

	function delete_model(id){
		if(!confirm("确定要删除吗？")){
			return false;
		}
		$.ajax({
			type:'POST',
			url:'__APP__/Store/Carmodel/delete_model',
			cache:false,
			dataType:'text',
			data:'model_id='+id,
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
	<div class="title">车型库-<?php echo ($series_info["series_name"]); ?></div>
	<div>
		<table >
			<tr>
				<td>新增车型名称：</td>
				<td><input type="text" name="model_name" id="model_name" /></td>
			</tr>
<!--			<tr>
				<td>排量：</td>
				<td><input type="text" name="output" id="output" /></td>
			</tr>
			<tr>
				<td>变速箱：</td>
				<td><input type="text" name="gearbox" id="gearbox" /></td>
			</tr>-->
			<tr>
				<td></td>
				<td><input type="button" id="add_model" value="提交"/></td>
			</tr>
		</table>
	</div>
	<div style="height:20px;"></div>
	<div>
		<form action="__APP__/Store/Carmodel/index/series_id/<?php echo ($series_info["series_id"]); ?>" method="post" >
			车型搜索： <input type="text" name="model_name" value="<?php echo ($model_name); ?>" />&nbsp;&nbsp;<input type="submit" value="搜索">
		</form>
	</div>
	<div class="list">
		<table class="list">
			<tr class="row">
				<th width="10%">车型ID</th>
				<th width="20%">车型名称</th>
<!--			<th width="10%">车型</th>
				<th width="10%">排量</th>
				<th width="10%">变速箱</th>-->
				<th width="20%">操作</th>
			</tr>
			<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
					<td><?php echo ($vo["model_id"]); ?></td>
					<td>
                        <input type="text" id="model_name_<?php echo ($vo["model_id"]); ?>" value="<?php echo ($vo["model_name"]); ?>" size="100"/>
                    </td>
<!--				<td><input type="text" id="model_<?php echo ($vo["model_id"]); ?>" value="<?php echo ($vo["model"]); ?>" /></td>
					<td><input type="text" id="output_<?php echo ($vo["model_id"]); ?>" value="<?php echo ($vo["output"]); ?>" /></td>
					<td><input type="text" id="gearbox_<?php echo ($vo["model_id"]); ?>" value="<?php echo ($vo["gearbox"]); ?>" /></td>-->
					<td><input type="button" value="修改" onclick="update_model(<?php echo ($vo["model_id"]); ?>);">&nbsp;&nbsp;&nbsp;<input type="button" value="删除" onclick="delete_model(<?php echo ($vo["model_id"]); ?>);"></td>
				</tr><?php endforeach; endif; else: echo "" ;endif; ?>
			<tr>
				<td colspan="4"><?php echo ($page); ?></td>
			</tr>
		</table>
	</div>
	
	</div>
</div>