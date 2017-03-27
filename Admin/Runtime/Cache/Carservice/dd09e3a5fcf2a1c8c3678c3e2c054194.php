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
<script type='text/javascript' src='__PUBLIC__/Js/calendar.js'></script>
<style> 
    .main{
        width: 100% !important;
    }
</style>
<!-- 菜单区域  -->

<!-- 主页面开始 -->
<div id="main" class="main" >

	<!-- 主体内容  -->
	<div class="content" >
	<div class="title">配件回收管理</div>
	
	<div>
            开始时间：<input TYPE="text" class="large bLeft"  NAME="start_time" onclick="new Calendar().show(this);" value="<?php echo ($start_time); ?>" readonly="readonly">
            结束时间：<input TYPE="text" class="large bLeft"  NAME="end_time" onclick="new Calendar().show(this);" value="<?php echo ($end_time); ?>" readonly="readonly">
            <button id="itemback">查询</button>
            <button id="itemback_export">导出订单为csv</button>
	</div>
	<div class="list">
		<table class="list">
			<tr class="row">
				<th width="5%" align=center> 订单号</th>
				<th width="5%" align=center>发生时间</th>
				<th width="5%" align=center>技师</th>
				<th width="10%" align=center>机油</th>
				<th width="10%" align=center>机滤</th>
				<th width="10%" align=center>空气滤</th>
                                <th width="10%" align=center>空调滤</th>
			</tr>
			<?php if(is_array($rs)): $i = 0; $__LIST__ = $rs;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
                                <td align=center><?php echo ($vo["order_id"]); ?></td>
                                <td align=center><?php echo (date('Y-m-d H:i:s',$vo["create_time"])); ?></td>
                                <td align=center><?php echo ($vo["truename"]); ?></td>
                                <td align=center><?php echo ($vo["oil"]); ?></td>
                                <td align=center><?php echo ($vo["filter"]); ?></td>
                                <td align=center> <?php echo ($vo["kongqi"]); ?> </td>
                                <td align=center> <?php echo ($vo["kongtiao"]); ?> </td>
                            </tr><?php endforeach; endif; else: echo "" ;endif; ?>
			
		</table>
	</div>
	
	</div>
</div>

<script type='text/javascript'>
jQuery(function(){
    //配件导出
    jQuery('#itemback_export').bind('click',function(){
        var  start_time = jQuery('input[name=start_time]').val();
        var  end_time = jQuery('input[name=end_time]').val();
        
        var ret = check(start_time,end_time);
        if(ret==0){
            return false ;
        }
        
        var url = '/Admin/index.php/Carservice/Itemback/itemback_export?start_time='+start_time+'&end_time='+end_time ;
        window.location.href= url ; 
        
    });
    
    //配件查询
    jQuery('#itemback').bind('click',function(){
        var  start_time = jQuery('input[name=start_time]').val();
        var  end_time = jQuery('input[name=end_time]').val();
        
        var ret = check(start_time,end_time);
        //alert(ret);
        if(ret==0){
            return false ;
        }
        var url = '/Admin/index.php/Carservice/Itemback/index?start_time='+start_time+'&end_time='+end_time ;
        window.location.href= url ; 
        
    });
    
    
    function check(start_time,end_time){
        if(start_time==''){
            alert('请选择开始时间!');
            return 0 ;
        }
        if(end_time==''){
            alert('请选择结束时间!');
            return 0 ;
        }
    }
                      
});    
    
    
</script>