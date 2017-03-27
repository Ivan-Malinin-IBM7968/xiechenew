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
<script src="http://api.map.baidu.com/api?v=1.5&ak=3db05159a3e3c55937fbf0160e2d8933" type="text/javascript"></script> 
<script type='text/javascript' src='__PUBLIC__/Js/Xheditor/jquery/jquery-1.4.4.min.js'></script>
<!-- <script type='text/javascript' src='__PUBLIC__/Js/calendar.js'></script> -->
<script type="text/javascript" src="__WEB__/Public/Js/DatePicker/WdatePicker.js?v=<?php echo (C("VERSION_DATE")); ?>"></script>
<style type="text/css">
 .cs td{border:1px solid #cccccc;text-align: center; width:110px; height: 65px; font-size: 14px; line-height: 2;}
 .color-14{color:#ffffff;background-color:#de1616 }.color-15{color:#ffffff;background-color:#de4a16}.color-16{color:#ffffff;background-color:#de7d16}
 .color-17{color:#ffffff;background-color:#45a220}.color-18{color:#ffffff;background-color:#05e1ca}
 .color-19{color:#ffffff;background-color:#48c019}.color-20{color:#ffffff;background-color:#0d69b0}.color-21{color:#ffffff;background-color:#0d69b0}
 .color-22{color:#ffffff;background-color:#19c083 }.color-23{color:#ffffff;background-color:#fc76ab}.color-24{color:#ffffff;background-color:#1f129a}
 .color-25{color:#ffffff;background-color:#4231e6 }.color-26{color:#ffffff;background-color:#0e8ff1}.color-27{color:#ffffff;background-color:#43a9f7}
 .color-28{color:#ffffff;background-color:#f95898 }
 .rl{position:relative;cursor:pointer}
 .ab{position:absolute;left:100px;top:0px;width:300px;height:230px;display:none;background-color:#f5f5f5;z-index:99;border:1px solid #cccccc;padding:0px 0px 10px 20px;color:#000;text-align: left;}
 .map_font_info{margin:0;line-height:1.5;font-size:13px;text-align:center;color:red; }
 .map_font2_info{margin:0;line-height:1.5;font-size:12px;color:red;text-align:right;padding-right:20px; }
 .time_font{font-size:12px;text-align:right; margin:0;padding:0;}
</style>
<!-- 主页面开始 -->
<div id="main" class="main" >

<!-- 主体内容  -->
<div class="content" >
<div class="title">技师排班</div>
<!-- 查询区域 -->
<div>

<FORM METHOD=POST ACTION="__URL__/index/">
<table cellspacing="0" cellpadding="10" > 
	<tr>  
	<td colspan="3">
		
		上门日期：
		<?php if($authId == 1): ?><input TYPE="text" class="large bLeft"  NAME="start_time" onfocus="WdatePicker()" value="<?php echo ($start_time); ?>" readonly="readonly">
		<?php else: ?>
			<input TYPE="text" class="large bLeft"  NAME="start_time" onfocus="WdatePicker({minDate:'%y-%M-%d',maxDate:'%y-%M-{%d+30}','dateFormat':'yy-mm-dd'})" value="<?php echo ($start_time); ?>" readonly="readonly"><!-- &nbsp;&nbsp;——&nbsp;&nbsp;
			<input TYPE="text" class="large bLeft"  NAME="end_time" onclick="new Calendar().show(this);" value="<?php echo ($end_time); ?>" readonly="readonly">--><?php endif; ?>
	</td>
	<td>
			按照技师姓名查询：
			<select name="technician_id">
			<option value="0">请选择</option>
			<?php if(is_array($jses)): $i = 0; $__LIST__ = $jses;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$js): $mod = ($i % 2 );++$i;?><option <?php if(($s_js) == $js["id"]): ?>selected<?php endif; ?> value="<?php echo ($js["id"]); ?>"><?php echo ($js["truename"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
		</select>&nbsp;&nbsp;&nbsp;&nbsp;
		按照城市查询：
			<select name="city_id">
			<option value="0">请选择</option>
			<option value="1" <?php if(($city_id) == "1"): ?>selected<?php endif; ?>>上海</option>
			<option value="2" <?php if(($city_id) == "2"): ?>selected<?php endif; ?>>杭州</option>
			<option value="3" <?php if(($city_id) == "3"): ?>selected<?php endif; ?>>苏州</option>
		</select>
		</td>
		<td>
			<input type="submit" value="查询" style="impBtn hMargin fLeft shadow">&nbsp;&nbsp;
		</td>
	</tr>
</table>

</FORM>
</div>
<p>共 <?php echo ($count); ?> 单，服务技师 <?php echo ($count_js); ?> 个（保养 <?php echo ($count1); ?> 单，免费检测 <?php echo ($count2); ?> 单，淘宝99元保养订单 <?php echo ($count3); ?> 单，免99元服务费订单 <?php echo ($count6); ?> 单，黄喜力套餐 <?php echo ($count7); ?> 单，蓝喜力套餐 <?php echo ($count8); ?> 单，灰喜力套餐 <?php echo ($count9); ?> 单，淘宝订单 <?php echo ($count13+$count14+$count15+$count16+$count17+$count18); ?> 单）</p>
<div style="clear:both">
	<div style="float:left;padding-left:10px;cursor:pointer">
		<a href="__URL__/autoAssign?day=<?php echo ($start_time); ?>">启动智能排班</a>
	</div>
	<div style="padding-left: 120px;float:left">当前时间：<?php echo ($start_time); ?></div>
</div>
<!-- 功能操作区域结束 -->
<div style="clear:both;padding-top:20px;*zoom:1;">
	<div style="float:left;width:400px">
		<div class="map" id="map">
          <div id='inner-map' style="height:400px;">
          </div>
        </div>
	</div>
	<div style="float:left;width:auto;padding-left:20px;">
		<table class="cs">
			<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
					<td class="ck_td"><?php echo ($key); ?>:00 - <?php echo ($key); ?>:59<br><input name="ck" type="checkbox" checked /></td>
					<?php if(is_array($vo)): $k = 0; $__LIST__ = $vo;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$co): $mod = ($k % 2 );++$k;?><td class="rl position color-<?php echo ($co["technician_id"]); ?>" data-lng="<?php echo ($co["longitude"]); ?>,<?php echo ($co["latitude"]); ?>" data-address="<?php echo ($co["address"]); ?>" data-technician="<?php echo ($co["technician_name"]); ?>" data-order_time="<?php echo (date('H:i',$co["order_time"])); ?>">
							<p style="position:absolute;bottom:-10px;left:3px;color:#43a9f7;font-size:12px"><?php if(($co["city_id"]) == "1"): ?>上海<?php else: ?>杭州<?php endif; ?></p>
							<?php echo ($co["order_name"]); ?><br/><?php if(!empty($co["technician_name"])): ?><font style="font-size:13px;">&nbsp;&nbsp;&nbsp;<?php echo ($co["technician_name"]); ?></font><?php endif; ?>
							<p class="time_font"><?php echo (date("H:i",$co["order_time"])); ?></p>
							<div class="ab">
								<p><a href="__APP__/Carservice/carserviceorder/detail?id=<?php echo ($co["id"]); ?>" target="_blank">订单id:<?php echo ($co["id"]); ?></a><i style="float:right;padding-right:20px" onClick="$(this).parent().parent().addClass('noshow')">X</i></p>
								<p>预约时间:<?php echo (date("Y-m-d H:i:s",$co["order_time"])); ?></p>
								<p>服务项目:<?php echo ($co["order_name"]); ?></p>
								<p>地址:<?php echo ($co["address"]); ?></p>
								<?php if(empty($co["technician_name"])): if(empty($isKefu)): ?><p>分配给:
									<select name="js" data-id="<?php echo ($co["id"]); ?>" >
										<option value="0">请选择</option>
										<?php if(is_array($co["allDistance"])): $i = 0; $__LIST__ = $co["allDistance"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$js): $mod = ($i % 2 );++$i;?><option value="<?php echo ($js["name"]); ?>" data-time="<?php echo ($js["time"]); ?>" data-distance="<?php echo ($js["distance"]); ?>"><?php echo ($js["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
									</select>
									<button class="sub">提交</button>
								</p><?php endif; endif; ?>
								<p style="color:#ff4a00"><span class="chosedTech">距离<?php echo ($co["near_technician"]); ?>有:<?php echo ($co["near_distance"]); ?>千米<?php if(!empty($co["near_time"])): ?>,两单时隔<?php echo ($co["near_time"]); ?>小时<?php endif; ?></span></p>
							</div>
						</td><?php endforeach; endif; else: echo "" ;endif; ?>
				</tr><?php endforeach; endif; else: echo "" ;endif; ?>
		</table>
	</div>
</div>
</div>
<!-- 主体内容结束 -->
<script type="text/javascript">

$('.rl').click(function(){
	$('.ab').hide();
	if(!$(this).find('.ab').hasClass('noshow')){
		$(this).find('.ab').show();
	}else{
		$(this).find('.ab').removeClass('noshow')
	}
})
$('select[name=js]').change(function(){
	dis = $(this).find("option:selected").attr('data-distance');
	time = $(this).find("option:selected").attr('data-time');
	name = $(this).val();
	text = '距离'+name+'有'+dis+'千米';
	if(time){
		text +=',两单时隔'+time+'小时';
	}
	$(this).parent().next().find('.chosedTech').text(text);
	
})
$('.sub').click(function(){
	var js = $(this).parent().find('select');
	order_id = $(js).attr('data-id');
	technician_name = $(js).val();
	if(!technician_name || !order_id || technician_name==0){
		alert('请选择技师');
		return false;
	}
	$.post("__URL__/bindTech",{'order_id':order_id,'technician_name':technician_name},function(data){
		if(data.status){
			alert('分配技师成功');
			document.location.reload(true);
		}else{
			alert(data.data);
		}
	},'json')
})
$('input[type=checkbox]').click(function(){
	initMap();
})

	var map = new BMap.Map("inner-map"), // 初始化百度地图
		shopList = $('.position'),  //获取所有顶铺List
		listLanArr = [];

	map.centerAndZoom("上海", 12);  
	map.enableScrollWheelZoom();    //启用滚轮放大缩小，默认禁用
	map.enableContinuousZoom();    //启用地图惯性拖拽，默认禁用
	map.setViewport(listLanArr);	   //根据相关point设置地图viewport
	
	initMap();
	
	function initMap(){
		map.clearOverlays();
		shopList.each(function(index, list){
			var coorArr = $(list).attr("data-lng").split(','),
				coor1 = parseFloat(coorArr[0]),
				coor2 = parseFloat(coorArr[1]),
				shopName = $(list).attr("data-technician"),
				shopAddress =$(list).attr('data-address'),
				shopTime = $(list).attr('data-order_time');
			
			var singleShop = {
				'name' : shopName,
				'address' : shopAddress,
				'coor' : {
					"x" : coor1,
					"y" : coor2
				},
				'time' : shopTime
			};
				
			listLanArr.push(coor1 + "," +coor2);
	
	
			var point = new BMap.Point(coor1, coor2), //New Point类
				marker = new BMap.Marker(point); // New marker类
	
			//验证要不要标文本
			ck = $(list).prevAll('.ck_td').find('input[type=checkbox]').attr('checked');
			
			
			addMarker(point, singleShop,ck); // add marker 并且在地途中绑定houver事件
	  		
	  		function addMarker(point, shopObject,ck){
				var marker = new BMap.Marker(point);
				map.addOverlay(marker);
				
				html ="<div><p class='map_font_info' style='color:#000'>"+shopObject.name+shopObject.time+"</p></div>";
				//标文本
				var opts = {
		        		  position : point,    // 指定文本标注所在的地理位置
		        		  offset   : new BMap.Size(10, -30)    //设置文本偏移量
		        		}
		        var label = new BMap.Label(html, opts);  // 创建文本标注对象
				label.setStyle({
					 color : "#000",
					 fontSize : "12px",
					 height : "20px",
					 lineHeight : "20px",
					 fontFamily:"微软雅黑"
				 });
		        if(ck){
					map.addOverlay(label);
		        }
				marker.addEventListener("mouseover", function(e){
					var sContent =
						"<div>" +
						"<p class='map_font_info'>已分配给："+shopObject.name+shopObject.time+"</p>" +
						"</div>";
					var infoWindow = new BMap.InfoWindow(sContent); 
					e.target.openInfoWindow(infoWindow, {
						"enableMessage" : false,
						"enableAutoPan" : true
					});
				});	
				marker.addEventListener("click", function(e){
					var sContent =
						"<div>" + 
						
						"<p class='map_font_info'>已分配给："+shopObject.name+shopObject.time+"</p>" +
						"</div>";
					var infoWindow = new BMap.InfoWindow(sContent); 
					e.target.openInfoWindow(infoWindow, {
						"enableMessage" : false,
						"enableAutoPan" : true
					});
	
				});	
				marker.addEventListener("mouseout",function(){
					$("#"+singleShop.id).css({
						// "background" : "none"
					})
				})
	
	  		}
			
	  		
		});
	}

	shopList.click(function(){
		var $this = $(this),
			coorArr = $this.attr("data-lng").split(','),
			thisShop = {
				"coor1" : parseFloat(coorArr[0]),
				"coor2" : parseFloat(coorArr[1]),
				"name" : $this.attr("data-technician"),
				"address" : $this.attr('data-address'),
				"time" : $this.attr('data-order_time')

			}

		var point = new BMap.Point(thisShop.coor1, thisShop.coor2),
			marker = new BMap.Marker(point);
			map.addOverlay(marker);
			var sContent =
				"<div>" + 
				
				"<p class='map_font_info'>已分配给："+thisShop.name+thisShop.time+"</p>" +
				"</div>";
			var infoWindow = new BMap.InfoWindow(sContent, {
				"enableMessage" : false,
				"enableAutoPan" : true
			}); 
			marker.openInfoWindow(infoWindow);
			marker.addEventListener("mouseover", function(e){
				var sContent =
					"<div>" + 
					"<p class='map_font_info'>已分配给："+thisShop.name+thisShop.time+"</p>" +
					"</div>";
				var infoWindow1 = new BMap.InfoWindow(sContent); 
				
				e.target.openInfoWindow(infoWindow1, {
					"enableMessage" : false,
					"enableAutoPan" : true
				});
			});	

			marker.addEventListener("click", function(e){
				var sContent =
					"<div>" + 
					"<p class='map_font_info'>已分配给："+thisShop.name+thisShop.time+"</p>" +
					"</div>";
				var infoWindow1 = new BMap.InfoWindow(sContent); 
				
				e.target.openInfoWindow(infoWindow1, {
					"enableMessage" : false,
					"enableAutoPan" : true
				});
				
				
			});	

			marker.addEventListener("mouseout",function(){
				$("#"+thisShop.shopId).css({
					// "background" : "none"
				})
			})

	}, function(){
		var $this = $(this);
		$this.css({
		
			// "background" : "none" <h4 style='margin:0 0 5px 0;padding:0.2em 0'>"+thisShop.address+"</h4>
		})
	});

</script>
</div>
<!-- 主页面结束 -->