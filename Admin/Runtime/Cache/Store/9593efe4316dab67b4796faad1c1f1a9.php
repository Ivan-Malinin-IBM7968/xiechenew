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
<script src="__JS__/Think/jquery-1.6.2.min.js"></script>
<script src="__JS__/Think/jquery.think.js"></script>
<script type="text/javascript" src="__PUBLIC__/Js/car_select/car_select.js"></script>

<script>
$(function(){
	//初始化
	Think.init({
			basePath:"__PUBLIC__/Js/Think"
	})

	$(".checksubmit").click(function(){
		 var shop_id=$(this).attr('shop_id');
		 var timesaleversion_id=$(this).attr('timesaleversion_id');
		 var select_services=$(this).attr('select_services');
		 var model_id=$(this).attr('model_id');
		 var brand_id=$(this).attr('brand_id');
		 var series_id=$(this).attr('series_id');
		 var u_c_id=$(this).attr('u_c_id')|0;
		// var coupon_id=$(this).attr('coupon_id');
		 var uid= "<?php echo ($uid); ?>";
		 var order_id = "<?php echo ($orderinfo["id"]); ?>";
		 var url="__URL__/addorder/shop_id/"+shop_id+"/uid/"+uid+"/select_services/"+select_services+"/brand_id/"+brand_id+"/model_id/"+model_id+"/series_id/"+series_id+"/timesaleversion_id/"+timesaleversion_id+"/u_c_id/"+u_c_id;
		 if(order_id){
			 url = url+"/order_id/"+order_id;
		 }
		 window.location.href=url;
	})
	
})
function popup1(){
	Think.popup({
		content:'<h3 class="flb" drag="1"><em>维修保养价格</em><a close="1"  class="flbc">关闭</a></h3><div  style="height:400px; overflow:scroll;padding:20px;" id="product_content"></div>',
		drag:true,//不可拖动
		cover:true,//设置笼罩
		x:0,
		y:-250
	});
}
	
function other_car_select(){
	$("#list_other_car").show();
}
function own_car_select(){
	$("#list_other_car").hide();
}

function baoyang_selected_check(d){
	if($(d).attr("checked")=='checked'){
		$("[name='select_services[]']").each(function(){
			if($(this).attr("service_type")=='baoyang'){
				$(this).removeAttr("checked");
			}
		})
		$(d).attr("checked",'true');
	}
}
function yuyue_submit(shopid,timesaleid,services_str,not_coupon_services_str){
	if(services_str == ''){
		alert("很抱歉，您所选择的维修保养项目不在该优惠活动范围内，请点击该活动查看优惠项目详情。");
		return false;
	}
	if($("#product_content_"+shopid+"_"+timesaleid).is(":hidden")){
		if(not_coupon_services_str != ''){
			alert("很抱歉，您所选择的一个或多个维修保养项目不在该优惠活动范围内，请点击该活动查看优惠项目详情。如果您仍需预约活动范围外的维修保养项目，请您分开下单。");
		}
		$("#product_content_"+shopid+"_"+timesaleid).show();	
	}else{
		$("#product_content_"+shopid+"_"+timesaleid).hide();
	}
}
function hide_product_info(shopid,timesaleid){
	$("#product_content_"+shopid+"_"+timesaleid).hide();
}

function submit_form(){
	$('#form1').submit(function(){
		return flase;
	});
}
</script>
</HEAD>
<body>
<!-- 菜单区域  -->
<!-- 主页面开始 -->
<div>
    <!-- 主体内容  -->
    <div class="content" >
         <div class="title">代号码为【<?php echo ($memberinfo["mobile"]); ?>】下单 [ <A HREF="__URL__/index">返回列表</A> ]</div>
		 <form method="post" id="form1" action="<?php echo U('/Store/Order/selectshop',array('uid'=>$_GET['uid'],'order_id'=>$_GET['order_id']));?>">
			<?php if(is_array($list_si_level_0)): $i = 0; $__LIST__ = $list_si_level_0;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><dl class="filter_li">
                    <dt><?php echo ($vo["name"]); ?></dt>
                    <dd><div class="filter_data1">
                    	<table><tr>
                    		<?php $n=0;?>
							<?php if(is_array($list_si_level_1)): $i = 0; $__LIST__ = $list_si_level_1;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo1): $mod = ($i % 2 );++$i; if($vo[id] == $vo1[service_item_id]): ?><td>
									<?php $n++;?>
										<?php if($vo1['service_item_id'] == 1): ?><input type="checkbox" id="server_<?php echo ($vo1["id"]); ?>" onclick="baoyang_selected_check(this);" name="select_services[]" service_type="baoyang" value="<?php echo ($vo1["id"]); ?>" <?php if(in_array(($vo1["id"]), is_array($select_services_str)?$select_services_str:explode(',',$select_services_str))): ?>checked<?php endif; ?> />
										&nbsp;&nbsp;
										<?php else: ?>
										<input type="checkbox" name="select_services[]" <?php if(in_array(($vo1["id"]), is_array($select_services_str)?$select_services_str:explode(',',$select_services_str))): ?>checked<?php endif; ?> value="<?php echo ($vo1["id"]); ?>"/>&nbsp;&nbsp;<?php endif; echo ($vo1["name"]); ?>
									</td><?php if($n%4 == 0): ?></tr><tr><?php endif; endif; endforeach; endif; else: echo "" ;endif; ?>
						</tr></table>
						   </div>
                    </dd>
                </dl><?php endforeach; endif; else: echo "" ;endif; ?>
			<dl class="filter_li">
				<dt>其他</dt>
                <dd>
					<div class="filter_data1">
						<table>
							<tbody>
								<tr>
									<td>
										<input type="checkbox" value="-1" name="select_services[]">&nbsp;&nbsp;我不知道做什么项目，到店检查为准</td>
								</tr>
							</tbody>
						</table>
					</div>
                 </dd>
            </dl>
				<!--
                <dl class="filter_li">
                    <dt>车型：</dt>
                    <dd>
                        <div class="filter_data1">
                            <span>ALFA 147 2004款 2.0T</span><a href="#">[更改]</a></div>
                    </dd>
                </dl>-->
      <dl class="filter_li">
      	<dt>车型：</dt><dd><div class="filter_data1">
      	<font><b>&nbsp;选择我的车型</b></font><br />
		<?php if(is_array($list_membercar)): $k = 0; $__LIST__ = $list_membercar;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($k % 2 );++$k;?><input type="radio" name="u_c_id" class="own_car" value="<?php echo ($vo["u_c_id"]); ?>" <?php if(((!isset($u_c_id)) && ($other_car != 1) && ($k == 1)) || ($vo["u_c_id"] == $u_c_id)): ?>checked<?php endif; ?> onclick="own_car_select();"/><?php echo ($vo["car_name"]); ?>&nbsp;<?php echo ($vo["brand_name"]); ?>-<?php echo ($vo["series_name"]); ?>-<?php echo ($vo["model_name"]); ?><br><?php endforeach; endif; else: echo "" ;endif; ?>
		<font><b>&nbsp;选择其他车型</b></font><br />
		<input type="radio" id="other_car" name="other_car" value="1" onclick="other_car_select();" <?php if($other_car == 1): ?>checked<?php endif; ?>/>选择其他车辆：
		<div class="filter_data2 mfpart" id="list_other_car" <?php if($other_car != 1): ?>style="display:none;"<?php endif; ?> >
			<table>
			<tr>
			<th>品牌</th>
			<td>
			<select id="get_brand" name="brand_id">
			<option value="0">请选择品牌</option>
			<?php if(is_array($brand)): $i = 0; $__LIST__ = $brand;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$brand_vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($brand_vo["brand_id"]); ?>" <?php if($brand_vo["brand_id"] == $brand_id): ?>selected<?php endif; ?> ><?php echo ($brand_vo["word"]); ?>&nbsp;<?php echo ($brand_vo["brand_name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
			</select>
			<?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$data_brand): $mod = ($i % 2 );++$i;?><input type="checkbox" name="checkbox_brand_id" value="<?php echo ($data_brand["brand_id"]); ?>|<?php echo ($data_brand["fsid"]); ?>" class="checkbox_brand"/><?php echo ($data_brand["brand_name"]); endforeach; endif; else: echo "" ;endif; ?>
			</td>
			</tr>
			<tr>
			<th>车系</th>
			<td>
			<select id="get_series" name="series_id">
				<option value="0">请选择车系</option>
			</select>
			</td>
			</tr>
			<tr>
			<th>车型</th>
			<td>
			<select id="get_model" name="model_id">
				<option value="0">请选择车型</option>
			</select>
			</td>
			</tr>
			</table>
</div>
							</div>
                        </dd>
 
              </dl>
		                <dl class="filter_li">
							<dt>店铺名：<input type="text" name="shop_name"value="<?php echo ($shop_name); ?>">&nbsp;&nbsp;&nbsp;
									<input type="checkbox" name="is_across" value="1">跨品牌店铺
							</dt>
							<dd style="align:center;">
								<button id="submit-btn" onclick="submit_form();">查找</button>
							</dd>
						</dl>
              </form> 
              
<input type="hidden" name="brand_id" id="brand_id" value="<?php echo ($brand_id); ?>"></input> 
<input type="hidden" name="series_id" id="series_id" value="<?php echo ($series_id); ?>"></input> 
<input type="hidden" name="model_id" id="model_id" value="<?php echo ($model_id); ?>"></input>

<div class="title">【查询到的商家信息】</div>
	<table class="list">
		<tr>
			<td width="10%">商家名称</td>
			<td width="10%">商家地址</td>
			<td width="10%">商家电话</td>
			<td width="13%">时间</td>
			<td width="13%">日期</td>
			<td width="13%">工时折扣 </td>
			<td width="13%">零件折扣 </td>
			<td width="14%">操作</td>
		</tr>
		<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr <?php if($orderinfo['shop_id'] == $vo['id']): ?>style="background-color:#F0F0FF"<?php endif; ?>>
			<td><?php echo ($vo["shop_name"]); ?></td>
			<td><?php echo ($vo["shop_address"]); ?></td>
			<td><?php echo ($vo["shop_phone"]); ?></td>
			<td colspan="5">
				<table class="list">
		 			<?php if(is_array($list_timesale_arr[$vo[id]]['timesale_arr'])): $i = 0; $__LIST__ = $list_timesale_arr[$vo[id]]['timesale_arr'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$time_arr): $mod = ($i % 2 );++$i; if(is_array($time_arr)): $i = 0; $__LIST__ = $time_arr;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$timesale_arr): $mod = ($i % 2 );++$i;?><tr>
							<td width="20%">
								<p style="padding:8px 20px 0px 6px;margin-bottom:8px;"><?php echo ($timesale_arr["begin_time"]); ?>--<?php echo ($timesale_arr["end_time"]); ?></p>
						<!--<font color="red"><?php echo ($timesale_arr["memo"]); ?></font>-->
							</td> 
							<td width="20%">
								<p style="padding:8px 20px 0px 6px;margin-bottom:8px;"><?php echo (date("Y-m-d",$timesale_arr["s_time"])); ?>--<?php echo (date("Y-m-d",$timesale_arr["e_time"])); ?></p>
							</td> 
							<td width="20%">
								<span class="price red"><?php echo ($timesale_arr["oldworkhours_sale_str"]); ?></span>
							</td>
							<td width="20%">
								<span class="price red"><?php echo ($timesale_arr["product_sale_str"]); ?></span>
							</td>
							<td width="20%"><a href="javascript:void(0);" class="ckanbut" onclick="yuyue_submit(<?php echo ($vo["id"]); ?>,<?php echo ($timesale_arr["id"]); ?>,'<?php echo ($timesale_arr["services_str"]); ?>','<?php echo ($timesale_arr["not_coupon_services_str"]); ?>');">查看并预定</a>
							</td>
						</tr>
						<tr id="product_content_<?php echo ($vo["id"]); ?>_<?php echo ($timesale_arr["id"]); ?>" style="border: 1px solid #CCCCCC;display:none;">
						<td colspan="6">
					 <div>
						<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
							<tr>
								<td>
									<img src="/UPLOADS/Product/<?php echo ($vo["product_info"]["$timesale_arr[id]"]); ?>">
								</td>
							</tr>
							</table>
						  	<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
						  	<tr>
						    	<td align="left" valign="middle"><img src="/Public/note/images/01.png"><span class="STYLE9"><span class="STYLE11">纵横携车网所提供的汽车售后维修保养项目明细价格（包括但不限于零件费和/或工时费等费用），由与纵横携车网签约的服务提供商提供。由于汽车售后维修保养项目的零件明细、零件价格、工时价格的不定期调整，纵横携车网提供的明细价格仅供参考，不作为车主接受服务后的付款依据，最终付款以在服务提供商处实际发生额为准。但您享受到的折扣率不变。</span></span></td>
						  	</tr>
						  	<tr>
						    	<td height="20">&nbsp;</td>
						  	</tr>
						  	<tr>
						    	<td height="20" align="left">
						    		<span class="STYLE10">您所选择的4S店信息：<br><br>
						      			 4S店：<font color="blue"><?php echo ($vo['shop_name']); ?></font><br>
										 地址：<font color="blue"><?php echo ($vo['shop_address']); ?></font><br>
										 客服电话：<font color="blue"><?php echo (C("CALL_400")); ?></font>		
						    		</span>
						    	</td>
						  	</tr>
						  	<tr>
						    	<td height="20"><span class="submit_order">
						    	<a href="javascript:void(0);" shop_id="<?php echo ($vo['id']); ?>" timesaleversion_id="<?php echo ($timesale_arr["id"]); ?>" select_services="<?php echo ($timesale_arr["services_str"]); ?>" model_id="<?php echo ($select_model_id); ?>" brand_id="<?php echo ($select_brand_id); ?>" series_id="<?php echo ($select_series_id); ?>" u_c_id="<?php echo ($u_c_id); ?>" coupon_id="<?php echo ($timesale_arr["coupon_id"]); ?>" class="checksubmit"><img src="/Public/note/images/02.png" width="138" height="39"></a>
						    	</span>
						    	<span class="hide_product"><a href="###" onclick="hide_product_info(<?php echo ($vo["id"]); ?>,<?php echo ($timesale_arr["id"]); ?>);">隐藏</a></span>
						    	</td>
						  	</tr>
						</table>
						
					</div>
					</td></tr><?php endforeach; endif; else: echo "" ;endif; endforeach; endif; else: echo "" ;endif; ?>
				</table>
			</td>
		</tr><?php endforeach; endif; else: echo "" ;endif; ?>
	</table>



    </div>
    <!-- 主体内容结束 -->
</div>
<!-- 主页面结束 -->