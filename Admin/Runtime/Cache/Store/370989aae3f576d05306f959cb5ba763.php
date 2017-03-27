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
<SCRIPT LANGUAGE="JavaScript">
    <!--
    function cache(){
        ThinkAjax.send('__URL__/cache','ajax=1');
    }
    //-->
</SCRIPT>
<script type='text/javascript' src='__PUBLIC__/Js/calendar.js'></script>
<!-- 主页面开始 -->
<div id="main" class="main" >
    <!-- 主体内容  -->
    <div class="content" >
        <div class="title">用户信息<?php if($member_info["status"] == 1): ?>[<a href="<?php echo U('/Store/Order/selectshop',array('uid'=>$member_info[uid]));?>">代下单</a>]<?php endif; ?>
		[<a href="<?php echo U('/Store/membercoupon/member_order',array('uid'=>$member_info[uid]));?>">代下优惠券</a>]
		[<a href="<?php echo U('/Carservice/Carserviceorder/place_order',array('uid'=>$member_info[uid]));?>">代下上门保养</a>]
		[<a href="javascript:void(0)" onclick="$('#model').show();return false;">给用户发送短信</a>]
		</div>
            <!-- 查询区域 -->
            <div class="content">
            <?php echo ($string); ?>
			<table>
			<tr><TD class="tRight" >Uid：</TD><td><?php echo ($member_info["uid"]); ?></td></tr>
			<tr><TD class="tRight" >用户名：</TD><td><?php echo ($member_info["username"]); ?></td></tr>
			<tr><TD class="tRight" >卡号：</TD><td><?php echo ($member_info["cardid"]); ?></td></tr>
			<tr><TD class="tRight" >邮箱：</TD><td><?php echo ($member_info["email"]); ?></td></tr>
			<tr><TD class="tRight" >电话：</TD><td><?php echo ($member_info["mobile"]); ?></td></tr>
			<tr><TD class="tRight" >地区：</TD><td><?php echo ($member_info["prov"]); ?>--<?php echo ($member_info["city"]); ?>--<?php echo ($member_info["area"]); ?></td></tr>
			<tr><TD class="tRight" ><a href="__URL__/edit/id/<?php echo ($member_info["uid"]); ?>">来源：</a></TD>
			<td>
				<?php if(1 == $member_info['fromstatus']): ?>百度/谷歌<?php endif; ?>
				<?php if(2 == $member_info['fromstatus']): ?>论坛/微博<?php endif; ?>
				<?php if(3 == $member_info['fromstatus']): ?>朋友介绍<?php endif; ?>
				<?php if(4 == $member_info['fromstatus']): ?>APP<?php endif; ?>
				<?php if(5 == $member_info['fromstatus']): ?>客服电话<?php endif; ?>
				<?php if(6 == $member_info['fromstatus']): ?>老用户<?php endif; ?>
				<?php if(7 == $member_info['fromstatus']): ?>宣传单<?php endif; ?>
				<?php if(9 == $member_info['fromstatus']): ?>短信推广<?php endif; ?>
				<?php if(8 == $member_info['fromstatus']): ?>其他<?php endif; ?>
			</td></tr>
			<tr><TD class="tRight" >用户积分：</TD><td><?php echo ($member_info["point_number"]); ?></td></tr>
			<tr><TD class="tRight" >注册时间：</TD><td><?php echo (date("Y-m-d",$member_info["reg_time"])); ?></td></tr>
			<tr><TD class="tRight" >账号状态：</TD><td><?php if($member_info["status"] == 1): ?>账号正常<?php else: ?><font color="red">账号已被禁用</font><?php endif; ?></td></tr>
			<tr><TD class="tRight" >IP地址：</TD><td><?php echo ($member_info["ip"]); ?></td></tr>
			<tr><TD class="tRight" >备注：</TD><td><?php echo ($member_info["memo"]); ?></td></tr>
			<tr><TD class="tRight" >用户车型信息：</TD><td><?php echo ($webusercar); ?></br>车牌号：<?php echo ($webusercar_info["car_number"]); ?></br>
				<?php if(empty($webusercar_info)): ?><input type="button" value="为用户备注车型" onClick="window.location.href='<?php echo U('/Store/Member/add_webusercar',array('uid'=>$member_info[uid]));?>'"/>
				<?php else: ?>
				<input type="button" value="修改用户备注车型" onClick="window.location.href='<?php echo U('/Store/Member/edit_webusercar',array('uid'=>$member_info[uid],'id'=>$webusercar_info[id]));?>'"/><?php endif; ?>
				</td></tr>
			</table>
			</div>
		    <div class="title">用户自定义车型信息</div>
            <!-- 查询区域 -->
            <div class="content">
            
            <table class="list">
			<th>车辆数</th>
			<th>自定义车名</th>
			<th>车型信息</th>
			<th>车辆状态</th>
			
			<?php if(is_array($membercar_info)): $i = 0; $__LIST__ = $membercar_info;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$carvo): $mod = ($i % 2 );++$i;?><tr>
					<td>车辆<?php echo ($i); ?></td>
					<td><?php echo ($carvo["car_name"]); ?></td>
					<td><?php echo ($carvo["car_info"]["brand_name"]); ?>--<?php echo ($carvo["car_info"]["series_name"]); ?>--<?php echo ($carvo["car_info"]["model_name"]); ?></td>
					<td><?php if($carvo["status"] == -1): ?>已删除<?php elseif($carvo["status"] == 1): ?>正常<?php else: ?>注销<?php endif; ?></td>
				</tr><?php endforeach; endif; else: echo "" ;endif; ?>
			</table>

			<div class="title">用户订单</div>
				<table cellspacing="10">
					<tr>
						<th>订单编号</th>
						<th>订单类型</th>
						<th>预约时间</th>
						<th>真实姓名</th>
						<th>电话</th>
						<th>车牌号</th>
						<th>备注</th>
						<th>订单状态</th>
						<th>操作</th>
					</tr>

					<?php if(is_array($order_info)): $i = 0; $__LIST__ = $order_info;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$ordervo): $mod = ($i % 2 );++$i;?><tr><TD><?php echo ($ordervo["id"]); ?></TD><td>4s店预约保养</td><td><?php echo (date("Y-m-d H:i:s",$ordervo["order_time"])); ?></td><td><?php echo ($ordervo["truename"]); ?></td><td><?php echo ($ordervo["mobile"]); ?></td>
							<td><?php echo ($ordervo["licenseplate"]); ?></td><td><?php echo ($ordervo["remark"]); ?></td>
							<td>
								<?php echo (C("ORDER_STATE.$ordervo[order_state]")); ?>
							</td>
							<td>
								<a href="__APP__/Store/order/edit/order_id/<?php echo ($ordervo["id"]); ?>" target="_blank">详细</a>
							</td>
						</tr><?php endforeach; endif; else: echo "" ;endif; ?>
					<?php if(is_array($re_order_info)): $i = 0; $__LIST__ = $re_order_info;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$re_order): $mod = ($i % 2 );++$i;?><tr><TD><?php echo ($re_order["id"]); ?></TD><td>上门保养</td><td><?php echo (date("Y-m-d H:i:s",$re_order["order_time"])); ?></td><td><?php echo ($re_order["truename"]); ?></td><td><?php echo ($re_order["mobile"]); ?></td>
							<td><?php echo ($re_order["licenseplate"]); ?></td><td><?php echo ($re_order["remark"]); ?></td>
							<td>
								<?php echo (C("RESERVATION_ORDER_STATE.$re_order[status]")); ?>
							</td>
							<td>
								<a href="/Admin/index.php/Carservice/carserviceorder/detail?id=<?php echo ($re_order["id"]); ?>" target="_blank">详细</a>&nbsp
								<?php if($re_order["status"] == 9): ?><a href="/mobile/check_report-report_id-<?php echo ($re_order["checkreport_id"]); ?>" target="_blank">查看检测报告</a>
									<?php else: endif; ?>
							</td><?php endforeach; endif; else: echo "" ;endif; ?>
					<?php if(is_array($bidorder_info)): $i = 0; $__LIST__ = $bidorder_info;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$bidorder): $mod = ($i % 2 );++$i;?><tr><TD><?php echo ($bidorder["id"]); ?></TD><td>事故车</td><td><?php echo (date("Y-m-d H:i:s",$bidorder["order_time"])); ?></td><td><?php echo ($bidorder["truename"]); ?></td><td><?php echo ($bidorder["mobile"]); ?></td>
							<td><?php echo ($bidorder["licenseplate"]); ?></td><td><?php echo ($bidorder["remark"]); ?></td>
							<td>
								<?php echo (C("BIDORDER_STATE.$bidorder[order_status]")); ?>
							</td>
							<td>
								<a href="__APP__/Store/bidorder/orderdetail/id/<?php echo ($bidorder["id"]); ?>" target="_blank">详细</a>
							</td>
						</tr><?php endforeach; endif; else: echo "" ;endif; ?>
				</table>

			 <div class="title">用户购买优惠卷信息</div>
            <!-- 查询区域 -->
          
           <table class="list">
			<tr class="row">
			<th>优惠卷号</th>
			<th>券名</th>
			<th>用户名</th>
			<th width=10%>金额</th>
			
			<th>类别</th>
			<th>4S店</th>
			<th>添加时间</th>
			<th>使用状态</th>
			<th width=8%>使用时间</th>
			<th>支付状态</th>
			<th width=8%>支付时间</th>
		
			</tr>
			<tr>
			<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><td><?php echo ($vo["membercoupon_id"]); ?></td>
			<td><a href="__WEB__/coupon/<?php echo ($vo["coupon_id"]); ?>" target="_blank"><?php echo ($vo["coupon_name"]); ?></a></td>
		
			<td><a href="__APP__/Store/member/CouponRead/id/<?php echo ($vo["uid"]); ?>" target="_blank"><?php echo ($vo["username"]); ?></a><br>
				 <?php if($vo["licenseplate"] == ''): echo ($vo["mobile"]); else: ?> <?php echo ($vo["licenseplate"]); endif; ?>
			</td>
			<td>原价:<?php echo ($vo["cost_price"]); ?><br>现价:<?php echo ($vo["coupon_amount"]); ?></td>
			
			<td>
			<?php if($vo["coupon_type"] == 1): ?>现金券<?php else: ?>团购券<?php endif; ?>
			</td>

			<td><?php echo ($vo["shop_name"]); ?></td>
			<td><?php echo (date("y-m-d H:i:s",$vo["create_time"])); ?></td>
			<td>
				<?php if($vo["is_use"] == 0): ?>未使用
					<?php if($vo["is_refund"] == 1): ?>,已退款<?php endif; ?>
				<?php else: ?>
				已使用<?php endif; ?>

			</td>
			<td >
				<?php if($vo["use_time"] > 0): echo (date("y-m-d H:i:s",$vo["use_time"])); else: ?>--<?php endif; ?>

			</td>
			<td>
			<?php if(is_array(C("PAY_STATE"))): $i = 0; $__LIST__ = C("PAY_STATE");if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo_os): $mod = ($i % 2 );++$i; if(($key) == $vo["is_pay"]): echo ($vo_os); endif; endforeach; endif; else: echo "" ;endif; ?>
			</td>

			<td><?php if($vo["pay_time"] > 0): echo (date("y-m-d H:i:s",$vo["pay_time"])); else: ?>--<?php endif; ?></td>

			
			</tr><?php endforeach; endif; else: echo "" ;endif; ?>
			</table>

			<!-- 回访显示区域开始 -->
			<div class="title">用户回访信息</div>
			<table>
			<tr>
				<TD class="tRight" >历史回访备注：</TD>
				<td colspan=5>
					<table width=100%>
						<tr>
						<th width=70%>备注内容</th><th width=15%>操作人</th><th width=15%>添加时间</th>
						</tr>
						<?php if(is_array($feedback_info)): $i = 0; $__LIST__ = $feedback_info;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo_fe): $mod = ($i % 2 );++$i;?><tr>
							<td><?php echo ($vo_fe["content"]); ?></td>
							<td><?php echo ($vo_fe["operator_name"]); ?></td>
							<td><?php echo (date("y-m-d H:i:s",$vo_fe["create_time"])); ?></td>
						</tr><?php endforeach; endif; else: echo "" ;endif; ?>
					</table>
				</td>
			</tr>
			<form action="__URL__/insert_feedbackinfo/" method="post">	
			<tr>
				<TD class="tRight" >回访备注：</TD>
				<td><textarea rows="10" cols="40" name="content"></textarea></td>
				<TD class="tRight" >下次回访时间：</TD>
				<td><input TYPE="text" class="large bLeft"  NAME="recall_time" onclick="new Calendar().show(this);" value="<?php echo ($start_time); ?>" readonly="readonly"></td>
				<TD class="tRight" >是否关闭回访：</TD>
				<td><input type="radio" name="is_close" value="true" <?php if($is_close == 'true'): ?>checked<?php endif; ?>>是</input><input type="radio" name="is_close" value="false" <?php if($is_close == 'false' or $is_close == ''): ?>checked<?php endif; ?>>否</input></td>
				<td><input type="hidden" name="uid" value="<?php echo ($member_info["uid"]); ?>"><input type="hidden" name="mobile" value="<?php echo ($member_info["mobile"]); ?>"></td>
			</tr>
			<tr><TD class="tRight" >&nbsp;</TD><td><input type="submit" id="btn_ok" name="btn_ok" value="提交"></td></tr>
			</form>
			</table>
			<!-- 回访显示区域结束 -->

			</div>

        <!-- 列表显示区域结束 -->
    </div>
<div style="width:500px;z-index:99;background-color:#efefef;padding:0px 0px 20px 40px;position:fixed;top:30%;left:30%;display:none" id="model">
	<div style="float:right;padding:10px 20px 0px 0px;cursor:pointer" onClick="$(this).parent().hide()">X</div>
	<h3 style="padding-top:30px">请填写客户资料:</h3>
	<div style="padding:10px 0px"><label style="width:80px;display:inline-block">手机号码：</label><input name="add_mobile" /></div>
	<div style="padding:10px 0px"><label style="width:80px;display:inline-block;vertical-align:top">备注：</label><textarea rows="5" style="width:300px" name="add_remark" readonly></textarea></div>
	<div style="padding:10px 0px"><label style="width:80px;display:inline-block;vertical-align:top"></label><button id="send">添加</button></div>	
</div>
<script type="text/javascript">
	
	<?php if(!empty($staff)): ?>var html = '【携车网-府上养车】你很忙，我们知道，99元上门为您保养爱车，4S品质，省钱省事。另有4S店预约折扣、车辆维修返利等，好多便宜。400-660-2822，服务工号：<?php echo ($staff["staff_id"]); ?> <?php echo ($staff["name"]); ?>';
		$('textarea[name=add_remark]').val(html);<?php endif; ?>
	
	$('#send').click(function(){
		/* if(!canSend){
			alert('您无权限发送');
			return false;
		} */
		var add_mobile = $('input[name="add_mobile"]').val();
		if(!add_mobile){
			alert('请填写客户电话');
			return false;
		}
		var add_remark = $('textarea[name="add_remark"]').val();
		if(!add_remark){
			alert('请填写发送内容');
			return false;
		}
		$.post('__URL__/sendMsg',{'mobile':add_mobile,'content':add_remark},function(data){
			if(data.status){
				alert('发送成功');
				window.location.reload();
			}else{
				alert(data.msg);
			}
		},'json')
	})
</script>
    <!-- 主体内容结束 -->
</div>
<!-- 主页面结束 -->