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

<script type="text/javascript" src="__JS__/Think/jquery-1.6.2.min.js"></script>
<script type='text/javascript' src='__PUBLIC__/Js/calendar.js'></script>

<!-- 主页面开始 -->
<div id="main" class="main" >

<!-- 主体内容  -->
<div class="content" >
<div class="title">机滤管理</div>

<div class="operate">
  <div class="impBtn hMargin fLeft shadow"><input type="button" id="" name="add" value="新增" onclick="add()" class="add imgButton"></div>
  <form method="post" ACTION="__URL__">
  	<label for="name">名称:</label>
  	<input type="text" name="name" />
  	<input type="submit" value="搜索" />
  </form>
</div>

<!-- 列表显示区域  -->
<div class="list" >
<table class="list">
<tr class="row">

<th width="2%">编号</th>
<th width="10%">名称</th>
<!--<th>品牌</th>-->
<th width="10%">价格</th>
<th width="10%">操作</th>

</tr>

<tr>
  <?php if(is_array($list)): foreach($list as $key=>$filter): ?><tr id="filter_<?php echo ($filter["id"]); ?>">
      <td><?php echo ($filter["id"]); ?></td>
      <td> 
          <input id="name_<?php echo ($filter["id"]); ?>" value="<?php echo ($filter["name"]); ?>"/>
           二维码： <input id="code_<?php echo ($filter["id"]); ?>" value="<?php echo ($filter["code"]); ?>">
           <a href="javascript:void(0);" onclick="if( confirm('确定绑定吗?') ){ bindcode(<?php echo ($filter["id"]); ?>) }"> 绑定二维码 </a>
      
      </td>
<!--      <td>
      	<select id="brand_<?php echo ($filter["id"]); ?>">
      		<option value="<?php echo ($filter["brand"]["id"]); ?>"><?php echo ($filter["brand"]["name"]); ?></option>
      		<?php if(is_array($brand_list)): foreach($brand_list as $key=>$vo): ?><option value="<?php echo ($vo["id"]); ?>"><?php echo ($vo["name"]); ?></option><?php endforeach; endif; ?>
      	</select>
      </td>-->
      <td><input id="price_<?php echo ($filter["id"]); ?>" value="<?php echo ($filter["price"]); ?>"/>&nbsp;元</td>
      <td>
        <a href="javascript:void(0);" onclick="if( confirm('确定要修改吗?') ){ edit_filter(<?php echo ($filter["id"]); ?>); }">修改</a>
        <a href="javascript:void(0);" onclick="if( confirm('确定要删除吗?') ){ del_filter(<?php echo ($filter["id"]); ?>); }">删除</a>
      </td>
    </tr><?php endforeach; endif; ?>
</tr>
</volist>
</table>
</div>
<!--  分页显示区域 -->
<div class="page"><?php echo ($page); ?></div>
<!-- 列表显示区域结束 -->
</div>
<!-- 主体内容结束 -->
</div>
<!-- 主页面结束 -->

<script>
//编辑物品
function edit_filter(_item_id){
  var _name=$('#name_'+_item_id).val();
  var _brand_id=$('#brand_'+_item_id).val();
  var _price=$('#price_'+_item_id).val();
  var _code=$('#code_'+_item_id).val();

  $.ajax({
    url: '__APP__/Carservice/Machinefilterconfig/ajax_edit',
    type: 'POST',
    data:{ id:_item_id,name:_name,price:_price,brand_id:_brand_id,code:_code},
    dataType: 'json',
    //timeout: 1000,
    error: function(){
      alert('系统繁忙!');
      return false;
    },
    success: function( data ){
      data = data.data;
      if(data.errno == '0'){
        alert(data.errmsg);
        return true;
      }
    }
  });
}
function del_filter(_item_id){
  $.ajax({
    url: '__APP__/Carservice/Machinefilterconfig/ajax_del',
    type: 'POST',
    data:{ id:_item_id},
    dataType: 'json',
    //timeout: 1000,
    error: function(){
      alert('系统繁忙!');
      return false;
    },
    success: function( data ){
      data = data.data;
      if(data.errno == '0'){
        $("#filter_"+_item_id).remove();
        alert(data.errmsg);
        return true;
      }
    }
  });
}
function add(){
  window.location.href='__APP__/Carservice/Machinefilterconfig/add';
}

//绑定二维码
function bindcode(_item_id){
    
  var _code=$('#code_'+_item_id).val();
  
  $.ajax({
        url: '__APP__/Carservice/Machinefilterconfig/ajax_bindcode',
        type: 'POST',
        data:{ id:_item_id,code:_code},
        dataType: 'json',
        //timeout: 1000,
        error: function(){
          alert('系统繁忙!');
          return false;
        },
        success: function( data ){
          data = data.data;
          if(data.errno == '0'){
            alert(data.msg);
            return true;
          }
        }
  });

}

</script>