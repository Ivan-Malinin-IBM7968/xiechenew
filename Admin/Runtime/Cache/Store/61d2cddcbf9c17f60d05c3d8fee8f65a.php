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
		$("#add_brand").click(function(){
			var brand_name = $("#brand_name").val();
			var word = $("#word").val();
			if(brand_name==''){
                alert('请填写新增品牌名称');
                return  false ;
            }
            if(word==''){
                alert('请填写新增品牌首字母');
                return  false ;
            }
            
            document.getElementById("add_form").submit();
			
		})
	})
	
	function update_brand(id){
		var word = $("#brandword_"+id).val();
		var brand_name = $("#brand_name_"+id).val();
		$.ajax({
			type:'POST',
			url:'__APP__/Store/Carbrand/save_brand',
			cache:false,
			dataType:'text',
			data:'brand_name='+brand_name+'&word='+word+'&brand_id='+id,
			success:function(data){
				if(data == 1){
					alert("修改成功");
				}else{
					alert("修改失败");
				}
			}
		})
	}
	function delete_brand(id){
		$.post('__APP__/Store/Carbrand/delete_brand',{'brand_id':id},function(data){
			if(data == 1){
				alert("删除成功");
				document.location.reload();
			}else{
				alert("删除失败");
			}
		})
	}
    
    //上传图片
    function  upload_pic(brand_id,brand_name){
        $('#logo_box').show();
        $('#pinpai_id').val(brand_id);
        $('#pinpai_name').val(brand_name);
    }
	
</script>
<!-- 主页面开始 -->
<div id="main" class="main" >

	<!-- 主体内容  -->
	<div class="content" >
	<div class="title">车型库-品牌管理</div>
	<div>
        <form action="__APP__/Store/Carbrand/add_brand" id="add_form" method="post"  enctype="multipart/form-data">
            新增车辆品牌：<input type="text" name="brand_name" id="brand_name"/>&nbsp;&nbsp;
            首字母：<input type="text" name="word" id="word" size="1"/>&nbsp;&nbsp;
            LOGO：<input type="file" name="brand_logo" id="brand_logo" />&nbsp;&nbsp;
            <input type="submit" id="add_brand" value="提交"/>
        </form>
	</div>

    
    <div id="logo_box" style="display:none;margin-top:20px;">
        <form action="__APP__/Store/Carbrand/upload_logo"  method="post"  enctype="multipart/form-data">
            上传品牌图片&nbsp;&nbsp;
            品牌id:<input type="text" name="pinpai_id"  id="pinpai_id" readonly="readonly"/>&nbsp;&nbsp;
            品牌名称:<input type="text" name="pinpai_name"  id="pinpai_name" readonly="readonly"/>&nbsp;&nbsp;
            LOGO：<input type="file" name="pinpai_logo" id="pinpai_logo" />&nbsp;&nbsp;
            <input type="submit"  value="提交"/>
        </form>
	</div>
    
    
	<div style="height:20px;"></div>
	<div>
		<form action="__APP__/Store/Carbrand" method="post" >
			品牌搜索： 名称<input type="text" name="brand_name" value="<?php echo ($brand_name); ?>" />&nbsp;&nbsp;<input type="submit" value="搜索">
		</form>
	</div>
	<div class="list">
		<table class="list">
			<tr class="row">
				<th width="5%">品牌ID</th>
				<th width="5%">首字母</th>
				<th width="10%">品牌名称</th>
                <th width="10%">品牌图片 </th>
				<th width="20%">操作</th>
			</tr>
			<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
					<td><?php echo ($vo["brand_id"]); ?></td>
					<td><input type="text" id="brandword_<?php echo ($vo["brand_id"]); ?>" value="<?php echo ($vo["word"]); ?>" /></td>
					<td><input type="text" id="brand_name_<?php echo ($vo["brand_id"]); ?>" value="<?php echo ($vo["brand_name"]); ?>" /></td>
                    <td> 
                        <img src="/UPLOADS/Brand/Logo/<?php echo ($vo["brand_logo"]); ?>">
                        <a href="javascript:void(0)" onclick="upload_pic(<?php echo ($vo["brand_id"]); ?>,'<?php echo ($vo["brand_name"]); ?>')">上传图片 </a>
                    </td>
					<td><input type="button" value="修改" onclick="update_brand(<?php echo ($vo["brand_id"]); ?>);">&nbsp;&nbsp;&nbsp;<a href="__APP__/Store/Carseries/index/brand_id/<?php echo ($vo["brand_id"]); ?>" >详情</a>&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" onclick="if(confirm('确认要删除<?php echo ($vo["brand_name"]); ?>吗'))delete_brand(<?php echo ($vo["brand_id"]); ?>)" >删除</a></td>
				</tr><?php endforeach; endif; else: echo "" ;endif; ?>
			<tr>
				<td colspan="4"><?php echo ($page); ?></td>
			</tr>
		</table>
	</div>
	
	</div>
</div>