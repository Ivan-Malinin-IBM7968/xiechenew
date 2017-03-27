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
<script type="text/javascript" src="__WEB__/Public/Js/DatePicker/WdatePicker.js?v=<?php echo (C("VERSION_DATE")); ?>"></script>

<style type="text/css">
#main{
	width: 150% ;
	margin: 0  auto ;

}
.ddtx{
	padding:20px ;
	width: 30% ;
}

.ddtl{
	padding:30px;
	width: 30% ;
}
.cs-line{
width:80%;
clear:both;
height:50px;
}
.cs-left{float:left;color:#000000;}
.f1{width:80px}
.f2{width:120px;}
.f3{width:230px}
.form select{color:#666666;width:100%;height:28px;line-height:28px}
.form i{color:#ff0000}
.form p{cursor:pointer;width:288px;color:#ffffff;background-color:#00b3f7;height:30px;line-height:30px;margin-left:48px;text-align:center}
.form span{display:block;height:28px;line-height:28px;padding-left:10px}
.fitbox{
    width:80% ;
    clear :both;
    height:auto;
}

.fl{
  float: left ;
}

.fr{
  float: right ;	
}
</style>

<!-- 主页面开始 -->
<div id="main" class="main" >
	<!-- 主体内容  -->
	<div class="content" >
		<div class="title">代下单</div>
		<form  method="post" name="subForm" id="subForm" action="__URL__/create_order">
		<!--左边开始-->
		<div class="ddtl fl">
			<div class="cs-line">
			<div class="cs-left f1">
			<span><i>*</i>抵扣码：</span>
			</div>
			<div class="cs-left">
				<input type="text" name="code" id="code"> 
                                <span id='codeinfo'>
					<input type="button" onclick="get_codeinfo()" value="获取券码信息">
				</span>
			</div>
                            
                    <!--<div class="cs-left f3">
				
			</div>-->
			</div>

			<div class="cs-line">
			<div class="cs-left f1">
			<span><i>*</i>套餐:</span>
			</div>
			<div class="cs-left f3">
			<select name="order_type" id="order_type">
				<option value="">选择套餐</option>
                <option value="2">上门检测订单</option>
				<!--<option value="3">淘宝已支付订单</option>-->
				<option value="13">好车况套餐</option>
				<option value="14">好空气套餐(奥迪宝马奔驰除外)</option>
				<option value="33">好空气套餐(奥迪宝马奔驰)</option>
				<option value="15">好动力套餐</option>
				<option value="16">保养服务+检测+养护</option>
				<option value="17">矿物质油小保养+检测+养护(黄喜力)</option>
				<option value="18">半合成油小保养+检测+养护(蓝喜力)</option>
				<option value="19">全合成油小保养+检测+养护(灰喜力)</option>
				<option value="21">38项检测+7项细节养护(光大)</option>
				<option value="22">168套餐(企业)</option>
				<option value="23">268套餐(企业)</option>
				<option value="24">368套餐(企业)</option>
				<option value="25">199套餐(浦发)</option>
				<option value="26">299套餐(浦发)</option>
				<option value="27">399套餐(浦发)</option>
				<!--<option value="28">全车检测38项(淘38)</option>-->
				<!--<option value="29">细节养护7项(淘38)</option>-->
				<!--<option value="30">更换空调滤工时(淘38)</option>-->
				<!--<option value="31">更换雨刮工时(淘38)</option>-->
				<!--<option value="32">小保养工时(淘38)</option>-->
				<option value="34">补配件免人工订单</option>
				<option value="36">黄壳小保养199（点评团购）</option>
				<option value="37">蓝壳小保养299（点评团购）</option>
				<option value="38">灰壳小保养399（点评团购）</option>
				<!--<option value="47">防雾霾活动</option>-->
				<!-- <option value="48">防雾霾1元活动</option> -->
				<!-- <option value="49">防雾霾8元活动</option> -->
				<option value="50">上门检测+养护(点评团购)</option>
				<option value="51">保养人工费+检测+养护(点评团购)</option>
				<option value="4">9.8元检测</option>
				<option value="52">好空气套餐(平安财险)</option>
				<option value="53">好动力套餐(后付费)</option>
				<option value="54">发动机舱精洗</option>
				<option value="55">好空气套餐(奥迪宝马奔驰后付费)</option> 
                <option value="56">黄壳199套餐（预付费）</option>
				<option value="57">蓝壳299套餐（预付费）</option>
				<option value="58">灰壳399套餐（预付费）</option> 
                <option value="60">矿物机油大保养268（预付费）</option>
                <option value="61">半合成油大保养378（预付费）</option>
                <option value="62">空调清洗套餐（后付费）</option>
                <option value="73">发动机除碳（后付费）</option>
                <option value="65">轮毂清洗套餐（预付费）</option>
                <option value="71">空调清洗套餐（预付费）</option>
                <option value="72">发动机除碳（预付费）</option>
                <option value="66">空调清洗（点评到家）</option>
                <option value="67">汽车检测和细节养护套餐（点评到家）</option>
                
                <option value="68"> 空调滤更换非宝马奥迪奔驰（点评到家）</option>
                <option value="69"> 保养人工费工时套餐（点评到家） </option>
                
            </select>
			</div>
			</div>

			<div class="cs-line">
			<div class="cs-left f1">
			<span><i>*</i>品牌:</span>
			</div>
			<div class="cs-left f3">
			<select name="brand_id" id="brand_id">
				<option value="">选择品牌</option>
				<?php if(is_array($brand_list)): $i = 0; $__LIST__ = $brand_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["brand_id"]); ?>"><?php echo ($vo["word"]); ?>&nbsp;<?php echo ($vo["brand_name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
			</select>
			</div>
			</div>
			<div class="cs-line">
			<div class="cs-left f1">
			<span><i>*</i>车型:</span>
			</div>
			<div class="cs-left f3">
			<select name="model_id" id="model_id">
				<option value="">选择车型</option>
			</select>
			</div>
			</div>
			<div class="cs-line">
			<div class="cs-left f1">
			<span><i>*</i>款式:</span>
			</div>
			<div class="cs-left f3">
			   <select name="style_id" id="style_id">
				<option value="">选择款式</option>
			</select>
			</div>
			</div>
			  


		<!--配件显示区域开始-->
		<div class="fitbox" id="test"> 
			<div class="title">配件信息</div>
			<p>暂无配件信息 </p>
		</div>
		<!--配件显示区域结束-->    
			
		</div>
		<!--左边结束-->

		<!--右边开始-->
		<div class="ddtx fl">

		<!-- <h2 class="tx_title">请填写以下内容</h2> -->
		<div class="tx_bd">
		  <table width="100%" border="0" cellspacing="4" cellpadding="4">
			<tr>
			  <td width="40%" height="65" align="right"  valign="right"><img src="__PUBLIC__/carservice/img/xx.png" width="6" height="6" /> 姓名 :</td>
			  <td width="30%" valign="middle">
				<label for="truename"></label>
				<input type="text" name="truename" id="truename" value="<?php echo ($userinfo['truename']); ?>" />
			  </td>
			  <td width="30%">&nbsp;</td>
			</tr>
			<tr>
			  <td height="65" align="right"  valign="right"><img src="__PUBLIC__/carservice/img/xx.png" width="6" height="6" /> 城市 :</td>
			  <td valign="middle">
				<label for="city"></label>
				<select name="city_id" id="city_id" style="font-size:14px">
				  <?php if(is_array($city_list)): $i = 0; $__LIST__ = $city_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vi): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vi["id"]); ?>" <?php if(($userinfo['city_id']) == "vi['id']"): ?>selected<?php endif; ?>><?php echo ($vi["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
				</select>
                                
                                <select name="area_id" id="area_id" style="font-size:14px">
                                    <option value="">选择区域</option>
				</select>
			  </td>
			  <td>&nbsp</td>
			  
			</tr>
			<tr>
			  <td height="65" align="right"  valign="middle">&nbsp;<img src="__PUBLIC__/carservice/img/xx.png" width="6" height="6" /> 详细地址：</td>
			  <td>
				<label for="address"></label>
				<input name="address" type="text" id="address" value="<?php echo ($userinfo['address']); ?>" />  
			  </td>
			  <td>（外环内）</td>
			  </tr>
			<tr>
			  <td height="65" align="right"  valign="middle">&nbsp;<img src="__PUBLIC__/carservice/img/xx.png" alt="" width="6" height="6" /> 手机号码：</td>
			  <td>
				<label for="mobile"></label>
				<input type="text" name="mobile" id="mobile" value="<?php echo ($userinfo['mobile']); ?>" />
			  </td>
			  <td>&nbsp;</td>
			</tr>
			<tr>
			  <td height="65" align="right"  valign="middle">&nbsp;<img src="__PUBLIC__/carservice/img/xx.png" alt="" width="6" height="6" /> 车 牌 号 ：</td>
			  <td>
				<select name="licenseplate_type" id="licenseplate_type" style="font-size:14px">
				  <?php if(is_array(C("SHORT_PROVINCIAL_CAPITAL"))): $i = 0; $__LIST__ = C("SHORT_PROVINCIAL_CAPITAL");if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo_os): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo_os); ?>"><?php echo ($vo_os); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
				</select>
				<input name="licenseplate" type="text" id="licenseplate" value="" style="width:100px" />
			  </td>
			  <td>&nbsp;</td>
			</tr>
			<tr>
			  <td height="65" align="right"  valign="middle">&nbsp;<img src="__PUBLIC__/carservice/img/xx.png" alt="" width="6" height="6" /> 服务时间：</td>
			  <td>
				<label for="order_time"></label>
				<input class="input-class datepicker1" onfocus="WdatePicker({minDate:'%y-%M-%d',maxDate:'%y-%M-{%d+30}','dateFormat':'yy-mm-dd'})" type="text" name="order_time" id="order_time" placeholder="请选择上门服务时间">
			  </td>
			  <td>
				<label for="time2"></label>
				<select name="order_time2" id="order_time2" style="font-size:14px" onblur="prevent()">
					<option value="0">8:00-8:59</option>
					<option value="1">9:00-9:59</option>
					<option value="2">10:00-10:59</option>
					<option value="3">11:00-11:59</option>
					<option value="4">12:00-12:59</option>
					<option value="5">13:00-13:59</option>
					<option value="6">14:00-14:59</option>
					<option value="7">15:00-15:59</option>
					<option value="8">16:00-16:59</option>
					<option value="9">17:00-17:59</option>
					<option value="10">18:00-18:59</option>
					<option value="11">19:00-19:59</option>
					<option value="12">20:00-20:59</option>
					<option value="13">21:00-21:59</option>
				</select>
			  </td>
			</tr>
			<tr>
			  <td height="65" align="right"  valign="middle">&nbsp;<img src="__PUBLIC__/carservice/img/xx.png" alt="" width="6" height="6" /> 付款方式：</td>
			  <td>
				<label for="chash"></label>
				<select name="chash" id="chash" style="font-size:14px">
				  <option>现金</option>
				</select>
			  </td>
			  <td>&nbsp;</td>
			</tr>

			<tr>
			  <td height="65" align="right"  valign="middle"> 业务来源 :</td>
			  <td valign="middle">
				<label for="city"></label>
				<select name="level1" id="business_source" level_id="2" style="font-size:14px">
					<option value="">请选择来源</option>
				  <?php if(is_array($business_source_list)): $i = 0; $__LIST__ = $business_source_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vi): $mod = ($i % 2 );++$i;?><!--<option value="<?php echo ($i); ?>"><?php echo ($vi); ?></option>-->
                      <option value="<?php echo ($vi["id"]); ?>"><?php echo ($vi["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
				</select>
              </td>
              <td>
                  <select name="level2" id="level2" level_id="3" style="font-size:14px">
                      <option value="">选择分类</option>
                  </select>
			  </td>
                <td>
                    <select name="business_source" id="level3" style="font-size:14px">
                        <option value="">选择分类</option>
                    </select>
                </td>
			  <td>&nbsp;</td>
			</tr>

			
			
			<tr>
			  <td height="65" align="right"  valign="middle">车辆注册时间：</td>
			  <td>
				<label for="clzc"></label>
				<input class="input-class datepicker2" onfocus="WdatePicker({'minDate':'+2','maxDate':'+16','dateFormat':'yy-mm-dd'})" type="text" name="car_reg_time" id="car_reg_time" placeholder="请选择车辆注册时间">
			  </td>
			  <td>&nbsp;</td>
			</tr>
			<tr>
			  <td height="65" align="right"  valign="middle">发动机号码：</td>
			  <td>
				<label for="zctime"></label>
				<input type="text" name="engine_num" id="engine_num" />
			  </td>
			  <td>&nbsp;</td>
			</tr>
			<tr>
			  <td height="65" align="right"  valign="middle">VIN：</td>
			  <td>
				<label for="vin"></label>
				<input type="text" name="vin_num" id="vin_num" />
			  </td>
			  <td>&nbsp;</td>
			</tr>
			<tr>
			  <td height="65" align="right"  valign="middle">客 服 备 注：</td>
			  <td>
				<label for="remark"></label>
				<input type="text" name="operator_remark" id="operator_remark" />
			  </td>
			  <td>&nbsp;</td>
			</tr>
			
		  </table>
		  
		</div>

		
		<div class="zf" style="background:#efefef; width:100%; height:80px;line-height:80px">
		  <table width="100%" border="0" cellspacing="6" cellpadding="0">
			<tr>
			
			  <td width="30%" height="70" align="right" style="font-size:16px">
			  应付总额：
			  <font style="font-size:22px;margin-right:20px;color:#e80012">
			     ￥<span id='item_amount'><?php echo ($item_amount); ?></span>
			  </font>
			  </td>

			  <td width="18%" style="height:80px;line-height:80px">
				<img src="__WEB__/Public/carservice/img/tj.png" width="180" height="50" id="subBtn" style="display:block;cursor:pointer"/>
			  </td>
			</tr>
		  </table>
		</div>

		</div>
		<!--右边结束-->
		</form>
	</div>
</div>

<script type="text/javascript">
    jQuery("#brand_id").change(function(){
        var brand_id = jQuery(this).val();
        jQuery.ajax({
            type:'POST',
            url:'__URL__/ajax_car_model',
            cache:false,
            dataType:'JSON',
            data:'brand_id='+brand_id,
            success:function(data){
                data = data.data;
                if(data.errno == 0){
                    var model_list_html = '<option value="">选择车型</option>';
                    jQuery.each(data.result.model_list, function(k, v){
                        //console.log("%o", v);
                        //model_list_html += '<option value="'+v['id']+'">'+v['name']+'</option>';
						model_list_html += '<option value="'+v['series_id']+'">'+v['word'].substr(1, 1)+'&nbsp;'+v['series_name']+'</option>';
                    });
                    jQuery("#model_id").html(model_list_html);
                }else{
                    alert(data.errmsg);
                    return false;
                }
            }
        });
    });

    jQuery("#model_id").on("change", function(){
        var model_id = jQuery(this).val();
        jQuery.ajax({
            type:'POST',
			url:'__URL__/ajax_car_style',
            cache:false,
            dataType:'JSON',
            data:'model_id='+model_id,
            success:function(data){
                data = data.data;
                if(data.errno == 0){
                    var style_list_html = '<option value="">选择款式</option>';
                    jQuery.each(data.result.style_list, function(k, v){
						style_list_html += '<option value="'+v['model_id']+'">'+v['model_name']+'</option>';
                    });
                    jQuery("#style_id").html(style_list_html);
                }else{
                    alert(data.errmsg);
                    return false;
                }
            }
        });
    });
    
    
    //选择车型测试,追加配件信息
    jQuery("#style_id").on("change", function(){
        //alert(1111111);
        var brand_id = jQuery("#brand_id").val();
        var model_id = jQuery("#model_id").val();
        var style_id = jQuery("#style_id").val();
		var order_type = jQuery("#order_type").val();
        var code = jQuery("#code").val();
		jQuery('#code').attr('disabled','disabled');
        if(!style_id){
        	alert('请选择车型');
            return false;
        }
        $url = '__URL__/sub_car?brand_id='+brand_id+'&model_id='+model_id+'&style_id='+style_id+'&code='+code+'&order_type='+order_type; 
        //alert($url); 
        jQuery('#test').load($url,function(){
        	//alert('111111');
			//加载完成动态更改右边价格
			var amount = jQuery('#amount').html();
			jQuery('#item_amount').html(amount);
		});
		//jQuery('#test').load('__URL__/sub_car'，{'brand_id':brand_id,'model_id':model_id,'style_id':style_id,'code':code,'order_type':order_type});
		   
    });
    
    
    //城市选择时联动地区,目前只开放上海
    //onchange事件
    jQuery("#city_id").on("change", function(){
        var city_id = jQuery(this).val();
        city_ajax(city_id);
    });
    //页面加载完执行一次
    jQuery(function(){
        var city_id = jQuery('#city_id').val();
        city_ajax(city_id);
    });

    function  city_ajax(city_id){
        jQuery.ajax({
            type:'POST',
            url:'__URL__/ajax_area',
            cache:false,
            dataType:'JSON',
            data:'city_id='+city_id,
            success:function(data){
                data = data.data;
                if(data.errno == 0){
                    var area_list_html = '<option value="">选择区域</option>';
                    jQuery.each(data.result.area_list, function(k, v){
                    area_list_html += '<option value="'+v['areaID']+'">'+v['area']+'</option>';
                    });
                    jQuery("#area_id").html(area_list_html);
                }else{
                    return false;
                }
            }
        });  
    }

    jQuery("#business_source").on("change", function(){
        var level_id = jQuery('#business_source').attr('level_id');
        ajax_level(level_id);
    });
    jQuery("#level2").on("change", function(){
        var level_id = jQuery('#level2').attr('level_id');
        var pid = jQuery('#level2').val();
        ajax_level(level_id,pid);
    });
    function  ajax_level(level_id,pid){
        jQuery.ajax({
            type:'POST',
            url:'__URL__/ajax_level',
            cache:false,
            dataType:'JSON',
            data:'level_id='+level_id+'&pid='+pid,
            success:function(data){
                data = data.data;
                if(data.errno == 0){
                    var level_list = '<option value="">选择分类</option>';
                    jQuery.each(data.result.level_list, function(k, v){
                        level_list += '<option value="'+v['id']+'">'+v['name']+'</option>';
                    });
                    jQuery("#level"+level_id).html(level_list);
                }else{
                    return false;
                }
            }
        });
    }

	//获取优惠券信息
	function get_codeinfo(){
		var code = jQuery('#code').val();
		jQuery.ajax({
			type:'POST',
			url:'__URL__/get_codeinfo',
			cache:false,
			dataType:'JSON',
			data:'code='+code,
			success:function(data){
				data = data.data;
				//alert(data);
				if(data.errno == 0){
					var style_list_html = '';
					jQuery.each(data.result.code, function(k, v){
						style_list_html += '<table><tr><th>价值</th><th>渠道</th><th>截止日期</th></tr><tr><td>'+v['price']+'</td><td>'+v['remark']+'</td><td>'+v['end_time']+'</td></tr></table>';
					});
					jQuery("#codeinfo").html(style_list_html);

					var style_list_html = '';
					jQuery.each(data.result.code, function(k, v){
					style_list_html += '<option value="'+v['model_id']+'">'+v['model_name']+'</option>';
					});
					jQuery("#style_id").html(style_list_html);

					var style_list_html = '';
					jQuery.each(data.result.code, function(k, v){
					style_list_html += '<option value="'+v['series_id']+'">'+v['series_name']+'</option>';
					});
					jQuery("#model_id").html(style_list_html);

					var style_list_html = '';
					jQuery.each(data.result.code, function(k, v){
					style_list_html += '<option value="'+v['brand_id']+'">'+v['brand_name']+'</option>';
					});
					jQuery("#brand_id").html(style_list_html);
				}else{
					alert(data.errmsg);
					return false;
				}
			}
		});
	}
</script>



<script>
jQuery(function(){
	 //4-19 超过下午四点 5-20
	 /*  var minDate = new Date();
	  hours = minDate.getHours();
	  
	  var maxDate = new Date();
	  if(hours >=16){
		  jQuery(".datepicker1").datePicker({'minDate':'+2','maxDate':'+16','dateFormat':'yy-mm-dd'});
	  }else{
		  jQuery(".datepicker1").datePicker({'minDate':'+1','maxDate':'+15','dateFormat':'yy-mm-dd'});
	  }
	  
	  jQuery(".datepicker2").datepicker({'maxDate':new Date(),'dateFormat':'yy-mm-dd'});	 */
	
  jQuery("#yzmBtn").click(function(){
      var mobile = jQuery("#mobile").val();
      if(mobile){
      }else{
        alert('请填写手机号码');
        jQuery("#mobile").focus();
        return false;
      }
      jQuery.ajax({
          type:'POST',
          url:'__URL__/giveeverify',
          cache:false,
          dataType:'JSON',
          data:'mobile='+mobile,
          success:function(data){
              data = data.data;
              if(data.errno == 0){
                  alert('验证码发送成功');
                  return true;
              }else{
                  alert(data.errmsg);
                  return false;
              }
          }
      });
  });

  jQuery("#subBtn").click(function(){

	prevent();
    addValues();
        
    var truename = jQuery("#truename").val();
    var address = jQuery("#address").val();
    var mobile = jQuery("#mobile").val();
    var licenseplate = jQuery("#licenseplate_type").val()+jQuery("#licenseplate").val();
    var order_time = jQuery("#order_time").val();
    var source = jQuery("#business_source").val();
    var city_id = jQuery("#city_id option:checked").val();
    var area_id = jQuery("#area_id option:checked").val();
	//车型信息验证
	var brand_id = jQuery("#brand_id").val();
    var model_id = jQuery("#model_id").val();
    var style_id = jQuery("#style_id").val();
	
    if(city_id == 1 && area_id == ""){
      alert('请选择区域');
      jQuery("#area_id").focus();
      return false;
    }
    
	if(brand_id == ""){
      alert('请选择品牌');
      jQuery("#brand_id").focus();
      return false;
    }	
	if(model_id == ""){
      alert('请选择车型');
      jQuery("#model_id").focus();
      return false;
    }
    if(style_id == ""){
      alert('请选择款式');
      jQuery("#style_id").focus();
      return false;
    }    
    if(truename == ""){
      alert('请填写姓名');
      jQuery("#truename").focus();
      return false;
    }
    if(address == ""){
      alert('请填写详细地址');
      jQuery("#address").focus();
      return false;
    }
    if(mobile == ""){
      alert('请填写手机号码');
      jQuery("#mobile").focus();
      return false;
    }
    if(licenseplate == ""){
      alert('请填写车牌号');
      jQuery("#licenseplate").focus();
      return false;
    }
    if(order_time == ""){
      alert('请选择服务时间');
      jQuery("#order_time").focus();
      return false;
    }
    if(source == ""){
      alert('请选择来源');
      jQuery("#business_source").focus();
      return false;
    }
    jQuery('#code').removeAttr('disabled');
    jQuery("#subForm").submit();
  });
});
//阻止客服同一时间段下过盛的订单
function prevent(){
    var order_time = jQuery("#order_time").val();
    var order_time2 = jQuery("#order_time2").val();
	var city_id = jQuery("#city_id").val();
	if(city_id==1){
		jQuery.ajax({
		  type:'POST',
		  url:'__URL__/prevent',
		  cache:false,
		  dataType:'JSON',
		  data:'order_time='+order_time+'&order_time2='+order_time2,
		  success:function(data){
			  //alert(data);
			  if(data<26){
				jQuery("#subBtn").attr('style','');
				return true;
			  }else{
				jQuery("#subBtn").attr('style','display:none;');
				alert('这个时间段单量已经饱和');
				return false;
			  }
		  }
		});
	}
}
</script>






</body>
</html>