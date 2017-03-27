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
<script type='text/javascript' src='__PUBLIC__/Js/Xheditor/jquery/jquery-1.4.4.min.js'></script>
    <script type='text/javascript' src='__PUBLIC__/Js/Xheditor/xheditor-1.1.13-zh-cn.min.js'></script>
	<script type='text/javascript' src='__PUBLIC__/Js/calendar.js'></script>
<script>
</script>
<!-- 主页面开始 -->
<div id="main" class="main" >
	
<!-- 主体内容  -->
<div class="content" >
<div class="title">每日新增列表</div>
<!--  功能操作区域  -->
<div class="operate" >
<!-- 查询区域 -->
<div>
<FORM METHOD=POST ACTION="__URL__">

<table cellspacing="0" cellpadding="10" >
	<tr>	
		<td colspan="2">
			开始时间：<input TYPE="text" class="large bLeft"  NAME="start_time" onclick="new Calendar().show(this);" value="<?php echo ($start_time); ?>" readonly="readonly">
			结束时间：<input TYPE="text" class="large bLeft"  NAME="end_time" onclick="new Calendar().show(this);" value="<?php echo ($end_time); ?>" readonly="readonly">
		</td>
		<td>
			<select name='origin_id'>
			<option value='0' <?php if($origin_id == 0): ?>selected<?php endif; ?> >选择来源</option>
			<option value='1' <?php if($origin_id == 1): ?>selected<?php endif; ?> >线上推广</option>
			<option value='2' <?php if($origin_id == 2): ?>selected<?php endif; ?> >朋友介绍</option>
			<option value='3' <?php if($origin_id == 3): ?>selected<?php endif; ?> >手机客户端</option>
			<option value='4' <?php if($origin_id == 4): ?>selected<?php endif; ?> >平安</option>
			<option value='5' <?php if($origin_id == 5): ?>selected<?php endif; ?> >客服</option>
			<option value='6' <?php if($origin_id == 6): ?>selected<?php endif; ?> >短信群发</option>
			<option value='7' <?php if($origin_id == 7): ?>selected<?php endif; ?> >线下活动</option>
			<option value='8' <?php if($origin_id == 8): ?>selected<?php endif; ?> >买名单短信</option>
			<option value='9' <?php if($origin_id == 9): ?>selected<?php endif; ?> >事故车</option>
			</select>
		</td>
		<td>	
			<input type="submit" value="查询" style="impBtn hMargin fLeft shadow">
		</td>
	</tr>
</table>

</FORM>
</div>
<!-- 功能操作区域结束 -->

<!-- 列表显示区域  -->
<div class="list" >
			<table class="list">
				<tr class="row">
					<th>日期</th>
					<th>新增订单数</th>
					<th>新增用户数</th>
					<th>新增店铺数</th>
					<th>售出的现金券数</th>
					<th>售出的现金券金额</th>
					<th>售出的团购券数</th>
					<th>售出的团购券金额</th>
					<th>上架的现金券数</th>
					<th>上架的团购券数</th>
					<th>非签约事故车订单数</th>
					<th>非签约事故车定损总额</th>
					<th>签约事故车订单数</th>
					<th>签约事故车定损总额</th>
				</tr>
				<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
					<td><?php echo (date("Y-m-d",$vo["create_time"])); ?></td>
					<td><a href="__URL__/order_new/time/<?php echo (date('Y-m-d',$vo["create_time"])); ?>"><?php echo ($vo["order_num"]); ?></a></td>
					<td><a href="__URL__/shop_new/time/<?php echo (date('Y-m-d',$vo["create_time"])); ?>"><?php echo ($vo["member_num"]); ?></a></td>
					<td><a href="__URL__/shop_new/time/<?php echo (date('Y-m-d',$vo["create_time"])); ?>"><?php echo ($vo["shop_num"]); ?></a></td>
					<td><a href="__URL__/sold_cashcoupon_new/time/<?php echo (date('Y-m-d',$vo["create_time"])); ?>"><?php echo ($vo["sold_cashcoupon"]); ?></a></td>
					<td><a href="__URL__/sold_cashcoupon_new/time/<?php echo (date('Y-m-d',$vo["create_time"])); ?>"><?php echo ($vo["sum_cashcoupon"]); ?></a></td>
					<td><a href="__URL__/sold_groupcoupon_new/time/<?php echo (date('Y-m-d',$vo["create_time"])); ?>"><?php echo ($vo["sold_groupon"]); ?></a></td>
					<td><a href="__URL__/sold_groupcoupon_new/time/<?php echo (date('Y-m-d',$vo["create_time"])); ?>"><?php echo ($vo["sum_groupon"]); ?></a></td>
					<td><a href="__URL__/onshelf_cashcoupon_new/time/<?php echo (date('Y-m-d',$vo["create_time"])); ?>"><?php echo ($vo["onshelf_cashcoupon"]); ?></a></td>
					<td><a href="__URL__/onshelf_groupcoupon_new/time/<?php echo (date('Y-m-d',$vo["create_time"])); ?>"><?php echo ($vo["onshelf_groupon"]); ?></a></td>
					<td><a href="__URL__/bidorder_detail/time/<?php echo (date('Y-m-d',$vo["create_time"])); ?>"><?php echo ($vo["bidorder_num"]); ?></a></td>
					<td><a href="__URL__/bidorder_detail/time/<?php echo (date('Y-m-d',$vo["create_time"])); ?>"><?php echo ($vo["loss_price"]); ?></a></td>
					<td><a href="__URL__/bidorder_contract_detail/time/<?php echo (date('Y-m-d',$vo["create_time"])); ?>"><?php echo ($vo["contract_bidorder_num"]); ?></a></td>
					<td><a href="__URL__/bidorder_contract_detail/time/<?php echo (date('Y-m-d',$vo["create_time"])); ?>"><?php echo ($vo["contract_loss_price"]); ?></a></td>
				</tr><?php endforeach; endif; else: echo "" ;endif; ?>
				<tr>
					<td><b>合计</b></td>
					<td><?php echo ($sum["order_num"]); ?></td>
					<td><?php echo ($sum["member_num"]); ?></td>
					<td><?php echo ($sum["shop_num"]); ?></td>
					<td><?php echo ($sum["sold_cashcoupon"]); ?></td>
					<td><?php echo ($sum["sum_cashcoupon"]); ?></td>
					<td><?php echo ($sum["sold_groupon"]); ?></td>
					<td><?php echo ($sum["sum_groupon"]); ?></td>
					<td><?php echo ($sum["onshelf_cashcoupon"]); ?></td>
					<td><?php echo ($sum["onshelf_groupon"]); ?></td>
					<td><?php echo ($sum["bidorder_num"]); ?></td>
					<td><?php echo ($sum["loss_price"]); ?></td>
					<td><?php echo ($sum["contract_bidorder_num"]); ?></td>
					<td><?php echo ($sum["contract_loss_price"]); ?></td>
				</tr>
			</table>
</div>
<!--  分页显示区域 -->
<div class="page"><?php echo ($page); ?></div>
<!-- 列表显示区域结束 -->
</div>
<!-- 主体内容结束 -->
</div>
<!-- 主页面结束 -->