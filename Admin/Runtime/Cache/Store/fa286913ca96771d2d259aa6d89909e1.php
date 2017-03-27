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
		$("#add_series").click(function(){
			var word = $("#word").val();
			var brand_id = <?php echo ($brand_info["brand_id"]); ?>;
			//车系名称
            var series_name = $("#series_name").val();
            if(series_name==''){
                alert('车系名称不能为空!');
                return false ;
            }
			$.ajax({
				type:'POST',
				url:'__APP__/Store/Carseries/add_series',
				cache:false,
				dataType:'text',
				data:'&word='+word+'&brand_id='+brand_id+'&series_name='+series_name ,
				success:function(data){
					if(data == 1){
						alert("添加成功");
						window.location.reload();
					}
				}
			})
		})
	})
	
	function update_series(id){
        //首字母
		var word = $("#seriesword_"+id).val();
        //车系名称
		var seriesname = $("#seriesname_"+id).val();
		$.ajax({
			type:'POST',
			url:'__APP__/Store/Carseries/save_series',
			cache:false,
			dataType:'text',
			data:'word='+word+'&seriesname='+seriesname+'&series_id='+id ,
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

	function delete_series(id){
		if(!confirm("确定要删除吗？")){
			return false;
		}
		$.ajax({
			type:'POST',
			url:'__APP__/Store/Carseries/delete_series',
			cache:false,
			dataType:'text',
			data:'series_id='+id,
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
	<div class="title">车型库-<?php echo ($brand_info["brand_name"]); ?></div>
	<div>
		<table >
			<tr>
				<td>新增车系：</td>
				<td><input type="text" name="series_name" id="series_name"/></td>
			</tr>
            <tr>
				<td>首字母：</td>
				<td><input type="text" name="word" id="word"  /></td>
			</tr>
<!--			<tr>
				<td>类型：</td>
				<td><input type="text" name="sort" id="sort"/></td>
			</tr>
			<tr>
				<td>投产年份：</td>
				<td><input type="text" name="start_year" id="start_year"/></td>
			</tr>
			<tr>
				<td>停产年份：</td>
				<td><input type="text" name="end_year" id="end_year"/></td>
			</tr>
			<tr>
				<td>选择4S店：</td>
				<td><select name="fsid" id="fsid">
					<option value='0'>选择4S店</option>
				<?php if(is_array($fs)): $i = 0; $__LIST__ = $fs;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$eachfs): $mod = ($i % 2 );++$i;?><option value='<?php echo ($eachfs["fsid"]); ?>'><?php echo ($eachfs["fsname"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
			</select></td>
			</tr>
			<tr>
				<td>新增4S店：</td>
				<td><input type="text" name="fsname" id="fsname" /></td>
			</tr> -->
			<tr>
				<td></td>
				<td><input type="button" id="add_series" value="提交"/></td>
			</tr>
			
		</table>
	</div>
	<div style="height:20px;"></div>
	<div>
		<form action="__APP__/Store/Carseries/index/brand_id/<?php echo ($brand_info["brand_id"]); ?>" method="post" >
			车系搜索： 名称<input type="text" name="series_name" value="<?php echo ($series_name); ?>" />&nbsp;&nbsp;<input type="submit" value="搜索">
		</form>
	</div>
	<div class="list">
		<table class="list">
			<tr class="row">
				<th width="5%">车系ID</th>
				<th width="5%">首字母</th>
				<th width="10%">车系名称</th>
<!--			<th width="10%">车系</th>
				<th width="10%">类型</th>
				<th width="10%">投产年份</th>
				<th width="10%">停产年份</th>
			    <th width="10%">4S店</th>-->
				<th width="20%">操作</th>
			</tr>
			<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
					<td><?php echo ($vo["series_id"]); ?></td>
					<td><input type="text" id="seriesword_<?php echo ($vo["series_id"]); ?>" value="<?php echo ($vo["word"]); ?>" size="5" /></td>
					<td>
                        <input type="text" id="seriesname_<?php echo ($vo["series_id"]); ?>" value="<?php echo ($vo["series_name"]); ?>" size="20" />
                    </td>
<!--					<td><input type="text" id="type_<?php echo ($vo["series_id"]); ?>" value="<?php echo ($vo["type"]); ?>" size="10" /></td>
					<td><input type="text" id="sort_<?php echo ($vo["series_id"]); ?>" value="<?php echo ($vo["sort"]); ?>" size="10" /></td>
					<td><input type="text" id="start_year_<?php echo ($vo["series_id"]); ?>" value="<?php echo ($vo["start_year"]); ?>" size="5" ></td>
					<td><input type="text" id="end_year_<?php echo ($vo["series_id"]); ?>" value="<?php echo ($vo["end_year"]); ?>" size="5" /></td>
					<td>
						<select id="fsid_<?php echo ($vo["series_id"]); ?>">
							<option value='0' <?php if($vo['fsid'] == 0): ?>selected<?php endif; ?> >选择4S店</option>
							<?php if(is_array($fs)): $i = 0; $__LIST__ = $fs;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$fslist): $mod = ($i % 2 );++$i;?><option value="<?php echo ($fslist["fsid"]); ?>" <?php if($fslist['fsid'] == $vo['fsid']): ?>selected<?php endif; ?> ><?php echo ($fslist["fsname"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
						</select>
					</td>-->
					<td><input type="button" value="修改" onclick="update_series(<?php echo ($vo["series_id"]); ?>);">&nbsp;&nbsp;&nbsp;<a href="__APP__/Store/Carmodel/index/series_id/<?php echo ($vo["series_id"]); ?>" >详情</a>&nbsp;&nbsp;&nbsp;<input type="button" value="删除" onclick="delete_series(<?php echo ($vo["series_id"]); ?>);"></td>
				</tr><?php endforeach; endif; else: echo "" ;endif; ?>
			<tr>
				<td colspan="4"><?php echo ($page); ?></td>
			</tr>
		</table>
	</div>
	
	</div>
</div>