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
<script type='text/javascript' src='__PUBLIC__/Js/calendar.js'></script>
<style type="text/css">
 .cs td{border:1px solid #cccccc;text-align: center; width:110px; height: 65px; font-size: 14px; line-height: 2;}
 .color-1{color:#ffffff;background-color:#de1616 }.color-2{color:#ffffff;background-color:#de4a16}.color-3{color:#ffffff;background-color:#de7d16}
 .color-4{color:#ffffff;background-color:#45a220}.color-5{color:#ffffff;background-color:#05e1ca}
 .color-6{color:#ffffff;background-color:#48c019}.color-7{color:#ffffff;background-color:#0d69b0}.color-21{color:#ffffff;background-color:#0d69b0}
 .color-22{color:#ffffff;background-color:#19c083 }.color-23{color:#ffffff;background-color:#fc76ab}.color-24{color:#ffffff;background-color:#1f129a}
 .color-25{color:#ffffff;background-color:#4231e6 }.color-26{color:#ffffff;background-color:#0e8ff1}.color-27{color:#ffffff;background-color:#43a9f7}
 .color-28{color:#ffffff;background-color:#f95898 }
 .rl{position:relative;cursor:pointer}
 .ab{position:absolute;left:100px;top:0px;width:300px;height:250px;display:none;background-color:#f5f5f5;z-index:99;border:1px solid #cccccc;padding:0px 0px 10px 20px;color:#000;text-align: left;}
 .map_font_info{margin:0;line-height:1.5;font-size:13px;text-align:center;color:red; }
 .map_font2_info{margin:0;line-height:1.5;font-size:12px;color:red;text-align:right;padding-right:20px; }
 .time_font{font-size:12px;text-align:right; margin:0;padding:0;}
</style>
<!-- 主页面开始 -->
<div id="main" class="main" >

<!-- 主体内容  -->
<div class="content" >
<div class="title">技师自动排班</div>
<!-- 查询区域 -->
<div>

<FORM METHOD=POST ACTION="__URL__/autoAssign">
<table cellspacing="0" cellpadding="10" > 
	<tr>  
	<td colspan="3">  
		上门日期：<input TYPE="text" class="large bLeft"  NAME="start_time" onclick="new Calendar().show(this);" value="<?php echo ($start_time); ?>" readonly="readonly"><!-- &nbsp;&nbsp;——&nbsp;&nbsp;
		 <input TYPE="text" class="large bLeft"  NAME="end_time" onclick="new Calendar().show(this);" value="<?php echo ($end_time); ?>" readonly="readonly">-->
	</td>
	<!--  <td>
			按照技师姓名查询：
			<select name="technician_id">
			<option value="0">请选择</option>
			<?php if(is_array($technical)): $i = 0; $__LIST__ = $technical;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$js): $mod = ($i % 2 );++$i;?><option <?php if(($s_js) == $js["id"]): ?>selected<?php endif; ?> value="<?php echo ($js["id"]); ?>"><?php echo ($js["truename"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
		</select>&nbsp;&nbsp;&nbsp;&nbsp;
		按照城市查询：
			<select name="city_id">
			<option value="0">请选择</option>
			<option value="1" <?php if(($city_id) == "1"): ?>selected<?php endif; ?>>上海</option>
			<option value="2" <?php if(($city_id) == "2"): ?>selected<?php endif; ?>>杭州</option>
		</select>
		</td>
	-->
		<td>
			<input type="submit" value="查询" style="impBtn hMargin fLeft shadow">&nbsp;&nbsp;
		</td>
	
	</tr>
</table>

</FORM>
</div>
<!-- 功能操作区域结束 -->
<div style="clear:both;padding-top:20px;*zoom:1;">
	<div style="float:left;width:400px">
		<div class="map" id="map">
          <div id='inner-map' style="height:400px;">
          </div>
        </div>
	</div>
	<?php if(!empty($carArrs)): ?><div style="float:left;width:auto;padding-left:20px;">
			<h1 style="margin-bottom:10px">为您智能排班如下:<button style="margin-left:30px" onclick="if(confirm('确认要提交排班？')){submitAssign()}">提交排班</button></h1>
			<table class="cs">
				<tr>
						<td>车辆</td>
						<td>08:00 - 08:59</td>
						<td>09:00 - 09:59</td>
						<td>10:00 - 10:59</td>
						<td>11:00 - 11:59</td>
						<td>12:00 - 12:59</td>
						<td>13:00 - 13:59</td>
						<td>14:00 - 14:59</td>
						<td>15:00 - 15:59</td>
						<td>16:00 - 16:59</td>
						<td>17:00 - 17:59</td>
						<td>18:00 - 18:59</td>
						<td>19:00 - 19:59</td>
						<td>20:00 - 20:59</td>
						<td>21:00 - 22:59</td>
					</tr>
				<?php if(is_array($carArrs)): $k = 0; $__LIST__ = $carArrs;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$carArr): $mod = ($k % 2 );++$k;?><tr>
					<td class="ck_td">车辆<?php echo ($k); ?>:<br>技师:<?php echo ($techName["$k"]); ?><br>
						<select name="change_js">
							<option value="0">修改技师</option>
						<?php if(is_array($technical)): $i = 0; $__LIST__ = $technical;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$js): $mod = ($i % 2 );++$i;?><option value="<?php echo ($js["id"]); ?>" data-carid="<?php echo ($k); ?>"><?php echo ($js["truename"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
						</select>
						<br><input name="ck" type="checkbox" checked />
					</td>
					<?php if(is_array($carArr)): $key = 0; $__LIST__ = $carArr;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$ko): $mod = ($key % 2 );++$key; if(empty($ko)): ?><td></td>
						<?php else: ?>
							<td style="vertical-align:top">
								<?php if(is_array($ko)): $i = 0; $__LIST__ = $ko;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$co): $mod = ($i % 2 );++$i;?><div  class="rl position <?php if(!empty($co["alreadyAssign"])): ?>color-28<?php endif; ?>" data-lng="<?php echo ($co["longitude"]); ?>,<?php echo ($co["latitude"]); ?>" data-address="<?php echo ($co["address"]); ?>" data-technician="<?php echo ($co["technician_name"]); ?>" data-order_time="<?php echo (date('H:i',$co["order_time"])); ?>">
									<div>时间：<?php echo (date('H:i',$co["order_time"])); ?><br><?php echo ($co["order_name"]); if(!empty($co["alreadyAssign"])): ?><br /> 已经分配给<?php echo ($co["technician_name"]); endif; ?></div>
									<div class="ab">
											<p><a href="__APP__/Carservice/carserviceorder/detail?id=<?php echo ($co["tmpId"]); ?>" target="_blank">订单id:<?php echo ($co["id"]); ?></a><i style="float:right;padding-right:20px" onClick="$(this).parent().parent().addClass('noshow')">X</i></p>
											<p>预约时间:<?php echo (date("Y-m-d H:i:s",$co["order_time"])); ?></p>
											<p>服务项目:<?php echo ($co["order_name"]); ?></p>
											<p>地址:<?php echo ($co["address"]); ?></p>
											<p>分配给:<?php echo ($co["technician_name"]); ?></p>
											<?php if(!empty($co["near_distance"])): ?><p>距离上一单距离:<?php echo ($co["near_distance"]); ?>千米</p><?php endif; ?>
											<?php if(empty($co["alreadyAssign"])): ?><p>重新分配给车辆:
												<select name="js" data-id="<?php echo ($co["id"]); ?>" style="color:#666">
													<option value="0">请选择</option>
													<?php $__FOR_START__=1;$__FOR_END__=$cars+1;for($i=$__FOR_START__;$i < $__FOR_END__;$i+=1){ ?><option value="<?php echo ($i); ?>"><?php echo ($i); ?></option><?php } ?>
												</select>
												<button class="sub">提交</button>
											</p><?php endif; ?>
										</div>
								</div><?php endforeach; endif; else: echo "" ;endif; ?>
							</td><?php endif; endforeach; endif; else: echo "" ;endif; ?>	
				</tr><?php endforeach; endif; else: echo "" ;endif; ?>
			</table>
		</div>
	<?php else: ?>
		<p>无可分配订单</p><?php endif; ?>
</div>
</div>
<!-- 主体内容结束 -->
<script type="text/javascript">

$('.rl').click(function(){
	if(!$(this).hasClass('color-28')){
		$('.rl').not($('.color-28')).css('background-color','#ffffff');
		$(this).css('background-color','#cccccc');
	}
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
$('select[name=change_js]').change(function(){
	tech_id = $(this).find("option:selected").val();
	car_id = $(this).find("option:selected").attr('data-carid');
	if(tech_id){
		$.post("__URL__/changeJs",{'tech_id':tech_id,'car_id':car_id},function(data){
			if(data.status){
				alert('修改技师成功');
				document.location.reload(true);
			}else{
				alert(data.data);
			}
		},'json')
	}
	
})
function submitAssign(){
	$.post("__URL__/submitAutoAssign",function(data){
		if(data.status){
			alert('排班成功');
			document.location.href = "__URL__/index";
		}else{
			alert(data.data);
		}
	},'json')	
}

$('.sub').click(function(){
	var js = $(this).parent().find('select');
	order_id = $(js).attr('data-id');
	car_id = $(js).val();
	if(!car_id || !order_id || car_id==0){
		alert('请选择车辆');
		return false;
	}
	$.post("__URL__/bindCar",{'order_id':order_id,'car_id':car_id},function(data){
		if(data.status){
			alert('重新分配车辆成功');
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
			ck = $(list).parent().prevAll('.ck_td').find('input[name=ck]').attr('checked');
			
			console.log(ck);
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