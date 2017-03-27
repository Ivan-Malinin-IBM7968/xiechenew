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
<script>
	function order_delete(id){
		if(!confirm("确定要删除吗？")){
			return false;
		}
		$.ajax({
			type:'POST',
			url:'__URL__/delete_order',
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



	function SendSms(membercoupon_id,coupon_code){
	
		$.ajax({
			type:'POST',
			url:'__APP__/Store/coupon/AjaxSendsms',
			cache:false,
			dataType:'text',
			data:'coupon_code='+coupon_code+'&membercoupon_id='+membercoupon_id,
			success:function(data){
				alert(data);
			}
		})
	
	}

	function GetShopname(){
		var shopname = $('#shopname').val();
		$.ajax({
			type: "POST",
			url: "__URL__/GetShopname",
			cache: false,
			data: "shopname="+shopname,
			success: function(data){
				if(data!='none'){
					data = eval("(" + data + ")");
					$("#shop_id").html("");
					$("#shop_id").append("<option value=''>请选择4S店</option>");
					for (i=0; i<data.length; i++ ){
						$("#shop_id").append("<option value='"+data[i]['id']+"'>"+data[i]['shop_name']+"</option>");
					}
				}
			}
		})
	}

</script>
<!-- 主页面开始 -->
<div id="main" class="main" >
	<script type='text/javascript' src='__PUBLIC__/Js/Xheditor/jquery/jquery-1.4.4.min.js'></script>
    <script type='text/javascript' src='__PUBLIC__/Js/Xheditor/xheditor-1.1.13-zh-cn.min.js'></script>
	<script type='text/javascript' src='__PUBLIC__/Js/calendar.js'></script>
<!-- 主体内容  -->
<div class="content" >
<div class="title">数据列表</div>
<!--  功能操作区域  -->
<div class="operate" >
<!-- 查询区域 -->
<div>
<FORM METHOD=POST ACTION="__URL__">
<table cellspacing="0" cellpadding="10" width=80% >
	<tr>	
		<td colspan="2">	
			搜索4S店：<input type="text" name="shopname" id="shopname" value="<?php echo ($data["shopname"]); ?>"><input type="button" id="btn_ok" value="查询" onclick="GetShopname();">	（点查询后下方4S店只列出包含该关键字的店铺给你选择）

		</td>
	</tr>
	<tr>	
		<td colspan="2">
			4S&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;店：&nbsp;
			<select name="shop_id" id="shop_id">
			<option value="">--请选择4S店--</option>
			<?php if(is_array($data["ShopList"])): $i = 0; $__LIST__ = $data["ShopList"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$shopvo): $mod = ($i % 2 );++$i; echo ($data["shop_id"]); ?>
			<option value="<?php echo ($shopvo["id"]); ?>" <?php if($data['shop_id'] == $shopvo['id']): ?>selected<?php endif; ?>><?php echo ($shopvo["shop_name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
			</select>
		</td>
	</tr>
	
	
	<!--<tr>	
		<td colspan="2">	
			支付时间：
			开始时间：<input TYPE="text" class="large bLeft"  NAME="pay_start_time" onclick="new Calendar().show(this);" value="<?php echo ($data["pay_start_time"]); ?>" readonly="readonly">
			结束时间：<input TYPE="text" class="large bLeft"  NAME="pay_end_time" onclick="new Calendar().show(this);" value="<?php echo ($data["pay_end_time"]); ?>" readonly="readonly">
		</td>
	</tr>
	<tr>	
		<td colspan="2">	
			使用时间：
			开始时间：<input TYPE="text" class="large bLeft"  NAME="use_start_time" onclick="new Calendar().show(this);" value="<?php echo ($data["use_start_time"]); ?>" readonly="readonly">
			结束时间：<input TYPE="text" class="large bLeft"  NAME="use_end_time" onclick="new Calendar().show(this);" value="<?php echo ($data["use_end_time"]); ?>" readonly="readonly">
		</td>
	</tr>-->
	<tr>	
		<td colspan="2" align="center">	
			<input type="submit" value="查询" style="impBtn hMargin fLeft shadow">
		</td>
	</tr>
</table>	

<!--
<div>
优惠卷号:
<input type="text"  name="membercoupon_id" value="<?php echo ($data["membercoupon_id"]); ?>"/>
</div>
<div>
手机号:
<input type="text"  name="mobile" value="<?php echo ($data["mobile"]); ?>"/>
</div>
<div>
订单状态:
<input type="radio"  value="0" name="is_pay" <?php if($data["is_pay"] == 0): ?>checked<?php endif; ?>>未支付
<input type="radio"  value="1" name="is_pay" <?php if($data["is_pay"] == 1): ?>checked<?php endif; ?>>已支付
<input type="radio" value="" name="is_pay" <?php if($data["is_pay"] == ''): ?>checked<?php endif; ?>>全部
</div>
<div>
4S店
<select name="shop_id">
<option value="">--请选择4S店--</option>
<?php if(is_array($data["ShopList"])): $i = 0; $__LIST__ = $data["ShopList"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$shopvo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($shopvo["id"]); ?>" <?php if($shopvo["id"] == $data['shop_id']): ?>selected<?php endif; ?>><?php echo ($shopvo["shop_name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
</select>
</div>


<div><!--按时间搜索：
	select name="search_type">
		<option value="">选择搜索时间</option>
		<option value="create_time" <?php if($search_type == 'create_time'): ?>selected<?php endif; ?>>下定时间</option>
		<option value="complete_time" <?php if($search_type == 'complete_time'): ?>selected<?php endif; ?>>完成时间</option>
	</select>
	下订时间：
	开始时间：<input TYPE="text" class="large bLeft"  NAME="start_time" onclick="new Calendar().show(this);" value="<?php echo ($data["start_time"]); ?>" readonly="readonly">
	结束时间：<input TYPE="text" class="large bLeft"  NAME="end_time" onclick="new Calendar().show(this);" value="<?php echo ($data["end_time"]); ?>" readonly="readonly">
</div>
<div>
<input type="submit" value="查询" style="impBtn hMargin fLeft shadow">
</div>-->
</FORM>
</div>
<!-- 功能操作区域结束 -->

<!-- 列表显示区域  -->
<div class="list" >
<table class="list">
<tr class="row">
<th>4S店铺名</th>
<th>申请结算订单量</th>
<th>支付结算订单量</th>
<th>应收发票的订单量</th>
<th>未申请结算时间</th>
<th>查看结算订单信息</th>
</tr>
<tr>
<?php if(is_array($data["Membersalecoupon"])): $i = 0; $__LIST__ = $data["Membersalecoupon"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><td><?php echo ($vo["shop_name"]); ?></td>
<td><a href="__URL__/shopcoupon/shop_id/<?php echo ($vo["shop_id"]); ?>" >未结算的订单数量<?php echo ($vo["count"]); ?></a></td>
<td> 
	<a href="__URL__/jiesuancoupon/shop_id/<?php echo ($vo["shop_id"]); ?>" >申请结算支付的订单数量<?php echo ($vo["isjiesuan_count"]); ?></a>
</td>
<td>
	<a href="__URL__/opencoupon/shop_id/<?php echo ($vo["shop_id"]); ?>" >应收发票订单数量<?php echo ($vo["open_count"]); ?></a>
</td>

<td>
	<?php echo (date("y-m-d ",$vo["jiesuan_time"])); ?>
</td>
<td>
	<a href="__URL__/viewdetail/shop_id/<?php echo ($vo["shop_id"]); ?>" >查看</a>
</td>
</tr><?php endforeach; endif; else: echo "" ;endif; ?>
</table>
</div>
<!--  分页显示区域 -->
<div class="page"><?php echo ($page); ?></div>
<!-- 列表显示区域结束 -->
</div>
<!-- 主体内容结束 -->
</div>
<!-- 主页面结束 -->