<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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

<div id="main" class="main" >
    <script type='text/javascript' src='__PUBLIC__/Js/Xheditor/jquery/jquery-1.4.4.min.js'></script>
    <script type='text/javascript' src='__PUBLIC__/Js/Xheditor/xheditor-1.1.13-zh-cn.min.js'></script>
	<script type='text/javascript' src='__PUBLIC__/Js/calendar.js'></script>
	<script type="text/javascript" src="__PUBLIC__/Js/DatePicker/WdatePicker.js?v=<?php echo (C("VERSION_DATE")); ?>"></script>
    <style type='text/css'>
        .btnMap {
            width:50px !important;
            background:transparent url(__PUBLIC__/Js/Xheditor/googlemap/map.gif) no-repeat center center;
        }
    </style>
<body>
<!-- 菜单区域  -->

<!-- 主页面开始 -->
<div id="main" class="main" >

<!-- 主体内容  -->
<div class="content" >

<div class="title">订单处理</div>

<!-- 列表显示区域  -->
<div >

<form name="invoiceForm" id="invoiceForm" action="__URL__/update_invoice" method="post">
<table style="line-height:50px;">
<tr>
	<td>开票金额：</td>
	<td><?php echo ($order_info["amount"]); ?></td>
    <!--<td>  <input id="chk_price" value="检查并修改价格"  type="button"> </td> -->
</tr>
	<tr>
		<td>开票类型：</td>
		<td>
			<select name="invoice_type" id="invoice_type">
				<option value="0" <?php if($invoice_info["invoice_type"] == 0): ?>selected<?php endif; ?>>普通发票</option>
				<option value="1" <?php if($invoice_info["invoice_type"] == 1): ?>selected<?php endif; ?>>增值税发票</option>
			</select>
		</td>
	</tr>
	<tr>
		<td>银行：</td>
		<td><input name="bank_name" value="<?php echo ($invoice_info["bank_name"]); ?>" /></td>
	</tr>
	<tr>
		<td>银行账号：</td>
		<td><input name="bank_account" value="<?php echo ($invoice_info["bank_account"]); ?>" /></td>
	</tr>
	<tr>
		<td>纳税人识别号：</td>
		<td><input name="taxpayer_id" value="<?php echo ($invoice_info["taxpayer_id"]); ?>" /></td>
	</tr>
<tr>
	<td>发票抬头：</td>
	<td><input name="customer_name" value="<?php echo ($invoice_info["customer_name"]); ?>" /></td>
</tr>

<?php if($invoice_info['express_id'] == '' and $invoice_info['invoice_status'] != 2): ?><tr>
        <td>收票人姓名：</td>
        <td><input name="receiver_name" value="<?php echo ($invoice_info["receiver_name"]); ?>" /></td>
    </tr>

    <tr>
        <td>收票人电话：</td>
        <td><input name="receiver_phone" id="receiver_phone" value="<?php echo ($invoice_info["receiver_phone"]); ?>" /></td>
    </tr>

    <tr>
        <td>收票人地址：</td>
        <td><input name="receiver_address" value="<?php echo ($invoice_info["receiver_address"]); ?>" /></td>
    </tr>
<?php else: ?>
    <tr>
        <td>收票人姓名：</td>
        <td><?php echo ($invoice_info["receiver_name"]); ?></td>
    </tr>

    <tr>
        <td>收票人电话：</td>
        <td><span id="receiver_phone"><?php echo ($invoice_info["receiver_phone"]); ?></td>
    </tr>

    <tr>
        <td>收票人地址：</td>
        <td><?php echo ($invoice_info["receiver_address"]); ?></td>
    </tr><?php endif; ?>

<tr>
	<td>快递公司：</td>
	<td>
		<select name="express" id="express">
			<option value="2" <?php if($invoice_info["express"] == 2): ?>selected<?php endif; ?>>申通</option>
			<option value="1" <?php if($invoice_info["express"] == 1): ?>selected<?php endif; ?>>顺丰</option>
			<option value="3" <?php if($invoice_info["express"] == 3): ?>selected<?php endif; ?>>韵达</option>
			<option value="4" <?php if($invoice_info["express"] == 4): ?>selected<?php endif; ?>>中通</option>
			<option value="5" <?php if($invoice_info["express"] == 5): ?>selected<?php endif; ?>>圆通</option>
		</select>
	</td>
</tr>

<tr>
    <td>发票号：</td>
    <td><input type="text" name="invoice_no" id="invoice_no" value="<?php echo ($invoice_info['invoice_no']); ?>"></td>
</tr>

<tr>
	<td>快递单号：</td>
	<td><input type="text" name="express_id" id="express_id" value="<?php echo ($invoice_info['express_id']); ?>"></td>
</tr>

<tr>
	<td>开票状态：</td>
	<td>   
		<select name="invoice_status" id="invoice_status">
			<option value="1" <?php if($invoice_info["invoice_status"] == 1): ?>selected<?php endif; ?>>待开票</option>
			<option value="2" <?php if($invoice_info["invoice_status"] == 2): ?>selected<?php endif; ?>>已开票已寄出</option>
			<option value="3" <?php if($invoice_info["invoice_status"] == 3): ?>selected<?php endif; ?>>已签收</option>
		</select>
	</td>
    <input type="hidden" value="$invoice_info.invoice_status" id="invoice_status">
</tr>
<tr>
	<td>备注：</td>
	<td><textarea name="remark" rows="10" cols="10"><?php echo ($invoice_info['remark']); ?></textarea></td>
</tr>
<tr>
	<td colspan="2" align="center">
		<input type="button" name="invoiceBtn" id="invoiceBtn" value="提交">
	</td>
</tr>
</tr>
</table>
</form>
<script>
    //提交发票数据
    $("#invoiceBtn").click(function() {
        var express_id = $("#express_id").val();
        var invoice_status = $("#invoice_status").val();
        //alert(express_id);
        //alert(invoice_status);
        if(invoice_status!=2 && express_id==''){
            var receiver_phone = $("#receiver_phone").val();
            var mobileRegExp = /^1[3|4|5|7|8][0-9]\d{4,8}$/;
            if (!mobileRegExp.test(receiver_phone) || receiver_phone.length!=11) {
                alert('请填写正确的手机号码');
                $("#receiver_phone").focus();
                return false;
            }else {
                $("#invoiceForm").attr("action", "__URL__/update_invoice?id=<?php echo ($id); ?>");
                $("#invoiceForm").submit();
                return true;
            }
        }else{
            $("#invoiceForm").attr("action", "__URL__/update_invoice?id=<?php echo ($id); ?>");
            $("#invoiceForm").submit();
            return true;
        }
    });
</script>

<form name="detailForm" id="detailForm" action="__URL__/update" method="post">
<table style="line-height:50px;">
	
<tr>
	<td>订单号：</td>
	<td id="order_id" data-id="<?php echo ($id); ?>"><?php echo ($order_info["show_id"]); ?></td>
</tr>

<tr>
	<td>用户姓名：</td>
	<td><input name="truename" value="<?php echo ($order_info["truename"]); ?>" /></td>
</tr>

<tr>
	<td>手机号码：</td>
	<td><input name="mobile" value="<?php echo ($order_info["mobile"]); ?>" /></td>
</tr>

<tr>
	<td>车牌号：</td>
	<td><input name="licenseplate" value="<?php echo ($order_info["licenseplate"]); ?>" /></td>
</tr>

<tr>
	<td>VIN：</td>
	<td><input name="vin_num" value="<?php echo ($order_info["vin_num"]); ?>" /></td>
</tr>


<?php if($order_info[city_id] == 1): ?><tr id="area">
        <td> 城市区域 :</td>
        <td valign="middle">
            <label for="city"></label>
            <select name="area_id" id="area_id">
                  <option value="">选择区域</option>
            </select>
        </td>
        <td><input type="hidden" value="<?php echo ($order_info[city_id]); ?>" id="city_id"></td>
    </tr><?php endif; ?>


<tr>
	<td>用户地址：</td>
	<td><input id="address" name="address" value="<?php echo ($order_info["address"]); ?>" /></td>
</tr>

<tr>
	<td>用户坐标：</td>
	<td><input type="text" name="coordinate" id="coordinate" value="<?php echo ($order_info['coordinate']); ?>" ></td>
	<td>
		<input type="button" onclick="get_coordinate()" value="获取坐标">
	</td>
</tr> 

<tr>
	<td>车型：</td>
	<td><input name="car_name" value="<?php echo ($order_info["car_name"]); ?>" /></td>
</tr>

<!-- <tr>
	<td width="150">选车</td>
	<td>
		<select name="brand_id" id="brand_id">
			<option value="">选择品牌</option>
			<?php if(is_array($brand_list)): $i = 0; $__LIST__ = $brand_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["brand_id"]); ?>"><?php echo ($vo["word"]); ?>&nbsp;<?php echo ($vo["brand_name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
		</select>

		<select name="model_id" id="model_id">
			<option value="">选择车型</option>
		</select>

		<select name="style_id" id="style_id">
			<option value="">选择款式</option>
		</select>
	</td>
</tr> -->

<tr>
	<td>服务项目：</td>
	<td>
		<table width="100%" border="0" cellspacing="1" cellpadding="1" bgcolor="#CCCCCC" class="table">
			  <tr>
				<td width="38%" bgcolor="#f8f8f8">&nbsp;&nbsp;保养项目</td>
				<td width="12%" bgcolor="#f8f8f8">&nbsp;&nbsp;价格</td>
				<td width="25%" bgcolor="#f8f8f8" align="center">&nbsp;&nbsp;总价</td>
			  </tr>
			  <?php if(is_array($item_list)): foreach($item_list as $key=>$value): ?><tr>
			    <td height="40" bgcolor="#F8F8F8" style="text-align:left;">
			    	<?php if($key == 0): ?>[机油]<?php echo ($value['type']); ?>
			        <?php elseif($key == 1): ?>
			        [机油滤清器]
			        <?php elseif($key == 2): ?>
			        [空气滤清器]
			        <?php elseif($key == 3): ?>
			        [空调滤清器]<?php endif; ?>  
			    	<?php if($key == 0): ?><span id='order_oil'><?php echo ($value['name']); ?><a href='javascript:void(0);' onclick='edit_oil()'>编辑</a></span>
					<?php elseif($key == 1): ?>
					<span id='order_jy'><?php echo ($value['name']); ?><a href='javascript:void(0);' onclick='edit_jy()'>编辑</a></span>
			        <?php elseif($key == 2): ?>
					<span id='order_kq'><?php echo ($value['name']); ?><a href='javascript:void(0);' onclick='edit_kq()'>编辑</a></span>
			        <?php elseif($key == 3): ?>
					<span id='order_kt'><?php echo ($value['name']); ?><a href='javascript:void(0);' onclick='edit_kt()'>编辑</a></span><?php endif; ?>

			    	<?php if($key == 0): ?><label for="item_0"></label>
					<select id='oil' style='display:none;'>
					<?php if(empty($default['oil_id'])): ?><!-- <input data-oil-1-id="0" data-oil-2-id="0" data-oil-1-num="0" data-oil-2-num="0" name="item_0" type="text" id="item_0" class="item_i oil-input" placeholder="请选择机油" data_id="0" value="自备配件（￥0）" /> -->
						<option data-oil-1-id="0" data-oil-2-id="0" data-oil-1-num="0" data-oil-2-num="0" name="item_0" type="text" id="item_0" class="item_i oil-input" placeholder="请选择机油" data_id="0" value="不需要配件" />不需要配件</option>
					<?php elseif($default['oil_id'] == -1): ?>
						<option data-oil-1-id="0" data-oil-2-id="0" data-oil-1-num="0" data-oil-2-num="0" name="item_0" type="text" id="item_0" class="item_i oil-input" placeholder="请选择机油" data_id="-1" value="自备配件（￥0）" />自备配件（￥0）</option>
					<?php else: ?>
						<!-- <input data-oil-1-id="<?php echo ($item_sets[0]['oil_1']); ?>" data-oil-2-id="<?php echo ($item_sets[0]['oil_2']); ?>" data-oil-1-num="<?php echo ($item_sets[0]['oil_1_num']); ?>" data-oil-2-num="<?php echo ($item_sets[0]['oil_2_num']); ?>" name="item_0" type="text" id="item_0" class="item_i oil-input" placeholder="请选择机油" data_id="<?php echo ($item_sets[0]['id']); ?>" value="<?php echo ($item_sets[0]['name']); ?>&nbsp;<?php echo ($car_style['norms']); ?>（￥<?php echo ($item_sets[0]['price']); ?>）" /> -->
						<option data-oil-1-id="<?php echo ($item_sets[0]['oil_1']); ?>" data-oil-2-id="<?php echo ($item_sets[0]['oil_2']); ?>" data-oil-1-num="<?php echo ($item_sets[0]['oil_1_num']); ?>" data-oil-2-num="<?php echo ($item_sets[0]['oil_2_num']); ?>" name="item_0" type="text" id="item_0" class="item_i oil-input" placeholder="请选择机油" data_id="<?php echo ($item_sets[0]['id']); ?>" value="<?php echo ($item_sets[0]['name']); ?>&nbsp;<?php echo ($car_style['norms']); ?>（￥<?php echo ($item_sets[0]['price']); ?>）" /><?php echo ($default['oil_name']); ?>&nbsp;<?php echo ($default['norms']); ?>（￥<?php echo ($default['oil_price']); ?>）</option><?php endif; ?>
					<div class="item_d" id="item_0_d" style="position:absolute;z-index:1000;width:594px;left:140px;top:34px;background:#FFF;border:1px solid #CCC;border-radius:4px;line-height:30px;display:none;">
					  <!-- <ul> -->
					  <?php if(is_array($item_sets)): foreach($item_sets as $k=>$set1): ?><!-- <li data-oil-1-id="<?php echo ($set1['oil_1']); ?>" data-oil-2-id="<?php echo ($set1['oil_2']); ?>" data-oil-1-num="<?php echo ($set1['oil_1_num']); ?>" data-oil-2-num="<?php echo ($set1['oil_2_num']); ?>" width="309" height="45" class="CheckboxGroup" data-tag="check1" id="CheckboxGroup0_<?php echo ($set1['id']); ?>" data_id="<?php echo ($set1['id']); ?>" data-price="<?php echo ($set1['price']); ?>" style="padding-left:20px;"><?php echo ($set1["name"]); ?>&nbsp;<?php echo ($car_style['norms']); ?><strong>（￥<?php echo ($set1["price"]); ?>）</strong></li> -->
						<option data-oil-1-id="<?php echo ($set1['oil_1']); ?>" data-oil-2-id="<?php echo ($set1['oil_2']); ?>" data-oil-1-num="<?php echo ($set1['oil_1_num']); ?>" data-oil-2-num="<?php echo ($set1['oil_2_num']); ?>" width="309" height="45" class="CheckboxGroup" data-tag="check1" id="CheckboxGroup0_<?php echo ($set1['id']); ?>" data_id="<?php echo ($set1['id']); ?>" data-price="<?php echo ($set1['price']); ?>" style="padding-left:20px;">
						<?php echo ($set1["name"]); ?>&nbsp;<?php echo ($car_style['norms']); ?><strong>（￥<?php echo ($set1["price"]); ?>）</strong></option><?php endforeach; endif; ?>
						<option data-oil-1-id="0" data-oil-2-id="0" data-oil-1-num="0" data-oil-2-num="0" name="item_0" type="text" id="item_0" class="item_i oil-input" placeholder="请选择机油" data_id="-1" data-price="0" value="自备配件（￥0）" />自备配件（￥0）</option>
						<option data-oil-1-id="0" data-oil-2-id="0" data-oil-1-num="0" data-oil-2-num="0" name="item_0" type="text" id="item_0" class="item_i oil-input" placeholder="请选择机油" data_id="0" data-price="0" value="不需要配件" />不需要配件</option>
					  <!-- </ul> -->
					</div>
					</select>
					<?php elseif($key == 1): ?>
					<label for="item_1"></label>
					<select id='jilv' style='display:none;'>
					<?php if(empty($default['filter_id'])): ?><!-- <input name="item_1" type="text" id="item_1" class="item_i" placeholder="请选择机油滤清器" data_id="0" value="自备配件（￥0）" /> -->
						<option name="item_1" type="text" id="item_1" class="item_i" placeholder="请选择机油滤清器" data_id="0" value="不需要配件" />不需要配件</option>
					<?php elseif($default['filter_id'] == -1): ?>
						<option name="item_1" type="text" id="item_1" class="item_i" placeholder="请选择机油滤清器" data_id="-1" value="自备配件（￥0）" />自备配件（￥0）</option>
					<?php else: ?>
						<!-- <input name="item_1" type="text" id="item_1" class="item_i" placeholder="请选择机油滤清器" data_id="<?php echo ($item_set['1']['0']['id']); ?>" value="<?php echo ($item_set['1']['0']['name']); ?>（￥<?php echo ($item_set['1']['0']['price']); ?>）" /> -->
						<option name="item_1" type="text" id="item_1" class="item_i" placeholder="请选择机油滤清器" data_id="<?php echo ($item_set['1']['0']['id']); ?>" value="<?php echo ($item_set['1']['0']['name']); ?>（￥<?php echo ($item_set['1']['0']['price']); ?>）" /><?php echo ($default['filter_name']); ?>（￥<?php echo ($default['filter_price']); ?>）</option><?php endif; ?>
					<div class="item_d" id="item_1_d" style="position:absolute;z-index:1000;width:594px;left:140px;top:34px;background:#FFF;border:1px solid #CCC;border-radius:4px;line-height:30px;display:none;">
					  <!-- <ul> -->
					  <?php if(is_array($item_set[1])): foreach($item_set[1] as $kk=>$vv): ?><!-- <li width="309" height="45" class="CheckboxGroup" data-tag="check2" id="CheckboxGroup1_<?php echo ($value['id']); ?>" data_id="<?php echo ($value['id']); ?>" data-price="<?php echo ($value['price']); ?>" style="padding-left:20px;"><?php echo ($value['name']); ?><strong>（￥<?php echo ($value['price']); ?>）</strong></li> -->
						<option width="309" height="45" class="CheckboxGroup" data-tag="check2" id="CheckboxGroup1_<?php echo ($vv['id']); ?>" data_id="<?php echo ($vv['id']); ?>" data-price="<?php echo ($vv['price']); ?>" style="padding-left:20px;"><?php echo ($vv['name']); ?><strong>（￥<?php echo ($vv['price']); ?>）</strong></option><?php endforeach; endif; ?>
						<option width="309" height="45" class="CheckboxGroup" data-tag="check2" id="CheckboxGroup1_0" data_id="-1" data-price="0" style="padding-left:20px;">自备配件<strong>（￥0）</strong></option>
						<option width="309" height="45" class="CheckboxGroup" data-tag="check2" id="CheckboxGroup1_0" data_id="0" data-price="0" style="padding-left:20px;">不需要配件<strong>（￥0）</strong></option>
					  <!-- </ul> -->  
					</div>
					</select>
			        <?php elseif($key == 2): ?>
					<label for="item_2"></label>
					 <select id='konglv' style='display:none;'>
					  <?php if(empty($default['kongqi_id'])): ?><!-- <input name="item_2" type="text" id="item_2" class="item_i" placeholder="请选择空气滤清器" data_id="0" value="自备配件（￥0）" /> -->
						<option name="item_2" type="text" id="item_2" class="item_i" placeholder="请选择空气滤清器" data_id="0" value="不需要配件" />不需要配件</option>
					  <?php elseif($default['kongqi_id'] == -1): ?>
						<option name="item_2" type="text" id="item_2" class="item_i" placeholder="请选择空气滤清器" data_id="-1" value="自备配件（￥0）" />自备配件（￥0）</option>
					  <?php else: ?>
						<!-- <input name="item_2" type="text" id="item_2" class="item_i" placeholder="请选择空气滤清器" data_id="<?php echo ($item_set['2']['0']['id']); ?>" value="<?php echo ($item_set['2']['0']['name']); ?>（￥<?php echo ($item_set['2']['0']['price']); ?>）" /> -->
						<option name="item_2" type="text" id="item_2" class="item_i" placeholder="请选择空气滤清器" data_id="<?php echo ($item_set['2']['0']['id']); ?>" value="<?php echo ($item_set['2']['0']['name']); ?>（￥<?php echo ($item_set['2']['0']['price']); ?>）" /><?php echo ($default['kongqi_name']); ?>（￥<?php echo ($default['kongqi_price']); ?>）</option><?php endif; ?>	
					<div class="item_d" id="item_2_d" style="position:absolute;z-index:1000;width:594px;left:140px;top:34px;background:#FFF;border:1px solid #CCC;border-radius:4px;line-height:30px;display:none;">
					  <!-- <ul> -->
						  <?php if(is_array($item_set[2])): foreach($item_set[2] as $kkk=>$vvv): ?><!-- <li width="309" height="45" class="CheckboxGroup" data-tag="check3" id="CheckboxGroup2_<?php echo ($value['id']); ?>" data_id="<?php echo ($value['id']); ?>" data-price="<?php echo ($value['price']); ?>" style="padding-left:20px;"><?php echo ($value['name']); ?><strong>（￥<?php echo ($value['price']); ?>）</strong></li> -->
							<option width="309" height="45" class="CheckboxGroup" data-tag="check3" id="CheckboxGroup2_<?php echo ($vvv['id']); ?>" data_id="<?php echo ($vvv['id']); ?>" data-price="<?php echo ($vvv['price']); ?>" style="padding-left:20px;"><?php echo ($vvv['name']); ?><strong>（￥<?php echo ($vvv['price']); ?>）</strong></option><?php endforeach; endif; ?>
						  <option width="309" height="45" class="CheckboxGroup" data-tag="check3" id="CheckboxGroup2_0" data_id="-1" data-price="0" style="padding-left:20px;">自备配件<strong>（￥0）</strong></option>
						  <option width="309" height="45" class="CheckboxGroup" data-tag="check3" id="CheckboxGroup2_0" data_id="0" data-price="0" style="padding-left:20px;">不需要配件<strong>（￥0）</strong></option>
					  <!-- </ul> -->
					</div>
					</select>
			        <?php elseif($key == 3): ?>
					<label for="kt"></label>
					<select id='kongtiaolv' style='display:none;'>
					  <?php if(empty($default['kongtiao_id'])): ?><!-- <input name="item_3" type="text" id="item_3" class="item_i" placeholder="请选择空调滤清器" data_id="0" value="自备配件（￥0）" /> -->
						<option name="item_3" type="text" id="item_3" class="item_i" placeholder="请选择空调滤清器" data_id="0" value="不需要配件" />不需要配件</option>
					  <?php elseif($default['kongqi_id'] == -1): ?>
						<option name="item_3" type="text" id="item_3" class="item_i" placeholder="请选择空调滤清器" data_id="-1" value="自备配件（￥0）" />自备配件（￥0）</option>
					  <?php else: ?>
						<!-- <input name="item_3" type="text" id="item_3" class="item_i" placeholder="请选择空调滤清器" data_id="<?php echo ($item_set['3']['0']['id']); ?>" value="<?php echo ($item_set['3']['0']['name']); ?>（￥<?php echo ($item_set['3']['0']['price']); ?>）" /> -->
						<option name="item_3" type="text" id="item_3" class="item_i" placeholder="请选择空调滤清器" data_id="<?php echo ($item_set['3']['0']['id']); ?>" value="<?php echo ($item_set['3']['0']['name']); ?>（￥<?php echo ($item_set['3']['0']['price']); ?>）" /><?php echo ($default['kongtiao_name']); ?>（￥<?php echo ($default['kongtiao_price']); ?>）</option><?php endif; ?>
					<div class="item_d" id="item_3_d" style="position:absolute;z-index:1000;width:594px;left:140px;top:34px;background:#FFF;border:1px solid #CCC;border-radius:4px;line-height:30px;display:none;">
					  <!-- <ul> -->
						  <?php if(is_array($item_set[3])): foreach($item_set[3] as $kkkk=>$vvvv): ?><!-- <li width="309" height="45" class="CheckboxGroup" data-tag="check4" id="CheckboxGroup3_<?php echo ($value['id']); ?>" data_id="<?php echo ($value['id']); ?>" data-price="<?php echo ($value['price']); ?>" style="padding-left:20px;"><?php echo ($value['name']); ?><strong>（￥<?php echo ($value['price']); ?>）</strong></li> -->
							<option width="309" height="45" class="CheckboxGroup" data-tag="check4" id="CheckboxGroup3_<?php echo ($vvvv['id']); ?>" data_id="<?php echo ($vvvv['id']); ?>" data-price="<?php echo ($vvvv['price']); ?>" style="padding-left:20px;"><?php echo ($vvvv['name']); ?><strong>（￥<?php echo ($vvvv['price']); ?>）</strong></option><?php endforeach; endif; ?>
						  <option width="309" height="45" class="CheckboxGroup" data-tag="check4" id="CheckboxGroup3_0" data_id="-1" data-price="0" style="padding-left:20px;">自备配件<strong>（￥0）</strong></option>
						  <option width="309" height="45" class="CheckboxGroup" data-tag="check4" id="CheckboxGroup3_0" data_id="0" data-price="0" style="padding-left:20px;">不需要配件<strong>（￥0）</strong></option>
					  <!-- </ul> -->
					</div>
					</select><?php endif; ?>

			    </td>
			    <td bgcolor="#F8F8F8"> 
					<?php if($key == 0): ?><span id='oil_price' price='<?php echo ($value[price]); ?>'>￥<?php echo ($value['price']); ?></span>
					<?php elseif($key == 1): ?>
						<span id='jy_price' price='<?php echo ($value[price]); ?>'>￥<?php echo ($value['price']); ?></span>
					<?php elseif($key == 2): ?>
						<span id='kq_price' price='<?php echo ($value[price]); ?>'>￥<?php echo ($value['price']); ?></span>
					<?php elseif($key == 3): ?>
						<span id='kt_price' price='<?php echo ($value[price]); ?>'>￥<?php echo ($value['price']); ?></span><?php endif; ?>
				</td>
				<td bgcolor="#F8F8F8"></td>
			  </tr><?php endforeach; endif; ?>
		
			  <tr>
			    <td height="40" bgcolor="#F8F8F8" style="text-align:left;padding-left:50px">服务费</td>
			    <td bgcolor="#F8F8F8"><?php echo ($service_cost); ?></td>
				<td bgcolor="#F8F8F8"></td>
			  </tr>
			  <tr>
			    <td height="40" bgcolor="#F8F8F8" style="text-align:left;padding-left:50px">优惠券 <?php if($order_info['replace_code'] > 0): ?><font color=red>(已抵扣)</font><?php endif; ?></td>
			    <td bgcolor="#F8F8F8"><span id='dikou' price='<?php echo ($dikou); ?>'><?php echo ($dikou); ?></span></td>
			    <td bgcolor="#F8F8F8" align="center"> <strong><span id='amount' price='<?php echo ($order_info[amount]+$order_info[dikou_amount]); ?>'>￥<?php echo ($order_info['amount']+$order_info[dikou_amount]); ?><span></strong></td>
			  </tr>

			  <tr>
			    <td height="40" bgcolor="#F8F8F8" style="text-align:left;padding-left:50px">抵扣金额</td>
			    <td bgcolor="#F8F8F8" colspan="2">￥-<?php echo ($order_info[dikou_amount]); ?></td>
				
			  </tr>

			  <tr>
			    <td height="40" bgcolor="#F8F8F8" style="text-align:left;padding-left:50px">支付金额</td>
			    <td bgcolor="#F8F8F8" colspan="2">￥<?php echo ($order_info[amount]); ?></td>
				
			  </tr>
			</table>
	</td>
<!-- edit_item start -->
<td>
<!-- <span id="edititem"><a href='javascript:void(0);' onclick='edit_show()'>编辑配件</a></span> -->
<span id="do_edititem" style="display:none;">
  <table width="300" border="0" cellspacing="5" cellpadding="0" class="table">
    <tr style="border-bottom:none">
      <td><table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
        <tr style="border:none">
          <td width="50%" align="left">
			<span id="sub">
            <!--<form method="post" name="subForm" id="subForm" action="__APP__/carservice/order"> -->
               <input type="hidden" name="oil_1_id" value="<?php echo ($default['oil_1_id']); ?>" />
              <input type="hidden" name="oil_1_num" value="<?php echo ($default['oil_1_num']); ?>" />
              <input type="hidden" name="oil_2_id" value="<?php echo ($default['oil_2_id']); ?>"/>
              <input type="hidden" name="oil_2_num" value="<?php echo ($default['oil_2_num']); ?>"/>
              <input type="hidden" name="CheckboxGroup0_res" id="CheckboxGroup0_res" value="<?php echo ($default['oil_id']); ?>">
              <input type="hidden" name="CheckboxGroup1_res" id="CheckboxGroup1_res" value="<?php echo ($default['filter_id']); ?>">
              <input type="hidden" name="CheckboxGroup2_res" id="CheckboxGroup2_res" value="<?php echo ($default['kongqi_id']); ?>">
              <input type="hidden" name="CheckboxGroup3_res" id="CheckboxGroup3_res" value="<?php echo ($default['kongtiao_id']); ?>">
			</span>
            <!-- </form> -->
            
          </td>
          <td width="16%">&nbsp;</td>
        </tr>
      </table></td>
      <td>&nbsp;</td>
    </tr>
  </table>
  </span>
  <span id="step">
  <table width="300" border="0" cellspacing="5" cellpadding="0" class="table">
	<tr>
	<th>技师步骤</th><th>时间</th><?php if($order_info['report_id'] != ''): ?><th><a href="javascript:;" onclick="showbaogao()">查看检测报告</a></th><?php endif; ?>
	</tr>
	<?php if(is_array($step_info)): foreach($step_info as $b=>$a): ?><tr>
	<td><?php echo ($a['step_name']); ?></td>
	<td><?php echo ($a['create_time']); ?></td>
	</tr><?php endforeach; endif; ?>
	<tr><td><?php if($order_info['report_id'] != ''): ?><a href="__WEB__/mobile/check_report-report_id-<?php echo ($order_info['report_id']); ?>">报告链接：__WEB__/mobile/check_report-report_id-<?php echo ($order_info['report_id']); ?></a><?php endif; ?></td><td>点播地址：<?php echo ($url); ?></td></tr>
  </table>
  <span>
</td>
<!-- edit_item end -->
</tr>

<tr>
	<td>预约时间：</td>
	<td>
	<input name="order_time" id="order_time" onclick="new Calendar().show(this);" value="<?php echo ($order_info["order_date"]); ?>" />
	<!-- <select name="order_hours">
		<option value="<?php echo ($order_info["order_hours"]); ?>" selected><?php echo ($order_info["order_hours"]); ?></option>
		<option value="9" >9</option>
		<option value="10" >10</option>
		<option value="11" >11</option>
		<option value="12" >12</option>
		<option value="13" >13</option>
		<option value="14" >14</option>
		<option value="15" >15</option>
		<option value="16" >16</option>
		<option value="17" >17</option>
	</select>
	<select name="order_minutes">
		<option value="<?php echo ($order_info["order_minutes"]); ?>" selected><?php echo ($order_info["order_minutes"]); ?></option>
		<option value="00" >00</option>
		<option value="10" >10</option>
		<option value="20" >20</option>
		<option value="30" >30</option>
		<option value="40" >40</option>
		<option value="50" >50</option>
	</select>
	-<?php echo ($order_info["order_hours"]); ?>:59 -->
    <select id="order_time2" name="order_time2" class="form-control">
		<option value="<?php echo ($order_info["order_hours"]); ?>:00"><?php echo ($order_info["order_hours"]); ?>:00-<?php echo ($order_info["order_hours"]); ?>:59</option>
		<option value="8:00">8:00-8:59</option>
		<option value="9:00">9:00-9:59</option>
		<option value="10:00">10:00-10:59</option>
		<option value="11:00">11:00-11:59</option>
		<option value="12:00">12:00-12:59</option>
		<option value="13:00">13:00-13:59</option>
		<option value="14:00">14:00-14:59</option>
		<option value="15:00">15:00-15:59</option>
		<option value="16:00">16:00-16:59</option>
		<option value="17:00">17:00-17:59</option>
		<option value="18:00">18:00-18:59</option>
		<option value="19:00">19:00-19:59</option>
		<option value="20:00">20:00-20:59</option>
		<option value="21:00">21:00-21:59</option>
	</select>
	</td>
</tr>

<tr>
	<td>下订时间：</td>
	<td><input name="create_time" value="<?php echo (date("y-m-d H:i:s",$order_info["create_time"])); ?>" /></td>
</tr>

<tr>
	<td>分配技师：</td>
	<td><?php echo ($order_info["technician_name"]); ?></td>
</tr>
<tr>
	<td>第三方ID：</td>
	<td><input name="taobao_id" value="<?php echo ($order_info["taobao_id"]); ?>" /></td>
</tr>
<tr>
	<td>来源：</td>
	<td><?php echo ($order_info["origin"]); ?></td>
</tr>
<tr>
	<td>用户备注：</td>
	<td>
		<textarea name="remark" id="remark" style="width:300px;height:150px;" readonly><?php echo ($order_info["remark"]); ?></textarea>
	</td>
</tr>

<tr>
	<td>客服备注:</td>
	<td>
		<textarea name="operator_remark" id="operator_remark" style="width:300px;height:150px;"><?php echo ($order_info["operator_remark"]); ?></textarea>
	</td>
</tr>

<?php if(!empty($recall_info)): ?><tr>
	<th colspan="2">历史回访</th><th>回访时间</th>
	</tr>
	<?php if(is_array($recall_info)): foreach($recall_info as $d=>$c): ?><tr>
	<td colspan="2"><?php echo ($c['content']); ?></td>
	<td><?php echo (date("y-m-d H:i:s",$c['create_time'])); ?></td>
	</tr><?php endforeach; endif; endif; ?>

<?php if($order_info["status"] == 9): ?><tr>
	<td>回访备注:</td>
	<td>
		<textarea name="content" id="content" style="width:300px;height:150px;"></textarea>
	</td>
</tr><?php endif; ?>

<tr>
	<td colspan="2" align="center">
        <?php if($order_info["status"] != 9): ?><input type="button" name="saveBtn" id="editBtn" value="修改订单">&nbsp;&nbsp;
            <input type="button" name="saveBtn" id="saveBtn" value="预约确认">&nbsp;&nbsp;
            <input type="button" name="saveBtn" id="delBtn" value="作废">&nbsp;&nbsp;<?php endif; ?>
        <?php if($order_info["status"] == 9): ?><input type="button" name="saveBtn" id="recallBtn" value="提交回访">&nbsp;&nbsp;<?php endif; ?>
        <?php if($order_info["status"] != 9): if(($authId == 1) OR ($authId == 238) OR ($authId == 234) OR ($authId == 300) OR ($authId == 171) OR ($authId == 242) OR ($authId == 252) OR ($authId == 269) OR ($authId == 304) OR ($authId == 336)): ?><input type="button" name="saveBtn" id="iniBtn" value="初始化">&nbsp;&nbsp;<?php endif; endif; ?>
        <?php if(($order_info['business_source'] == 20 AND $order_info['pay_type'] != 8 AND $authId == 234) OR ($order_info['business_source'] == 20 AND $order_info['pay_type'] != 8 AND $authId == 1)): ?><input type="button" name="ddBtn" id="ddBtn" value="养车点点已完成">&nbsp;&nbsp;<?php endif; ?>
	</td>
</tr>

</table>
</form>
</div>
<!-- 列表显示区域结束 -->
</div>
<!-- 主体内容结束 -->
</div>
<!-- 主页面结束 -->
<script>
	function edit_oil(){
		$("#order_oil").attr('style','display:none;');
		$("#oil").attr('style','');
	}
	function edit_jy(){
		$("#order_jy").attr('style','display:none;');
		$("#jilv").attr('style','');
	}
	function edit_kq(){
		$("#order_kq").attr('style','display:none;');
		$("#konglv").attr('style','');
	}
	function edit_kt(){
		$("#order_kt").attr('style','display:none;');
		$("#kongtiaolv").attr('style','');
	}
	//初始化
	$(function(){
		$("#iniBtn").click(function(){
			var id = $('#order_id').attr('data-id');
			$.post('__APP__/Carservice/Carserviceorder/initialization',{'id':id},function(data){
				if(data.status){
					alert(data.info);
					window.location.href= '__APP__/Carservice/Carserviceorder/';
				}else{
					alert(data.info);
				}
			},'json')
		});
	});
	//提交回访数据
	$(function(){
		$("#recallBtn").click(function(){
			var content = $('#content').val();
			var id = $('#order_id').attr('data-id');
			$.post('__APP__/Carservice/Carserviceorder/insert_recall',{'content':content,'id':id},function(data){
				if(data.status){
					alert(data.info);
					window.location.href= '__APP__/Carservice/Carserviceorder/';
				}else{
					alert(data.info);
				}
			},'json')
		});
	});

	$("#editBtn").click(function(){
		var coordinate = $("#coordinate").val();
		/* if(coordinate == ""){
			alert('请填写用户坐标');
			$("#coordinate").focus();
			return false;
		} */

		$("#detailForm").attr("action", "__URL__/edit?id=<?php echo ($id); ?>");
		//addValues();
		$("#detailForm").submit();
		return true;
	});

	$("#saveBtn").click(function(){
		var coordinate = $("#coordinate").val();
		/* if(coordinate == ""){
			alert('请填写用户坐标');
			$("#coordinate").focus();
			return false;
		} */
		
		$("#detailForm").attr("action", "__URL__/process_1?id=<?php echo ($id); ?>");
		$("#detailForm").submit();
		return true;
	});

	$("#delBtn").click(function(){
		$("#detailForm").attr("action", "__URL__/del?id=<?php echo ($id); ?>");
		$("#detailForm").submit();
		return true;
	});
	//获取车系
	$(function(){
		$("#brand_id").change(function(){
			var brand_id = $(this).val();
			$.post('__APP__/Carservice/Carserviceorder/ajax_car_model',{'brand_id':brand_id},function(data){
					data = data.data;
					if(data.errno == 0){
						var model_list_html = '<option value="">选择车型</option>';
						$.each(data.result.model_list, function(k, v){
							//console.log("%o", v);
							model_list_html += '<option value="'+v['series_id']+'">'+v['word']+'&nbsp;'+v['series_name']+'</option>';
						});
						$("#model_id").html(model_list_html);
					}else{
						alert(data.errmsg);
						return false;
					}
			},'json')
			/*$.ajax({
				type:'POST',
				url:'__APP__/Carservice/Carserviceorder/ajax_car_model',
				cache:false,
				dataType:'JSON',
				data:'brand_id='+brand_id,
				success:function(data){

				}
			});*/
		});
	//获取车型
		$("#model_id").live("change", function(){
			var model_id = $(this).val();
			$.post('__APP__/Carservice/Carconfig/ajax_car_style',{'model_id':model_id},function(data){
				data = data.data;
				if(data.errno == 0){
					var style_list_html = '<option value="">选择款式</option>';
					$.each(data.result.style_list, function(k, v){
						//console.log("%o", v);
						style_list_html += '<option value="'+v['model_id']+'">'+v['model_name']+'</option>';
					});
					$("#style_id").html(style_list_html);
				}else{
					alert(data.errmsg);
					return false;
				}
			},'json')
			/*$.ajax({
				type:'POST',
				url:'__APP__/Carservice/Carconfig/ajax_car_style',
				cache:false,
				dataType:'JSON',
				data:'model_id='+model_id,
				success:function(data){

				}
			});*/
		});
	});
	//根据车型返回配件数据
	function edit_show(){
		$("#edititem").attr('style','display:none;');
		$("#do_edititem").attr('style','');
		submit = "<input type=\"hidden\" name=\"oil_1_id\" value=\"<?php echo ($default['oil_1_id']); ?>\"><input type=\"hidden\" name=\"oil_1_num\" value=\"<?php echo ($default['oil_1_num']); ?>\" /><input type=\"hidden\" name=\"oil_2_id\" value=\"<?php echo ($default['oil_2_id']); ?>\"><input type=\"hidden\" name=\"oil_2_num\" value=\"<?php echo ($default['oil_2_num']); ?>\" /><input type=\"hidden\" name=\"CheckboxGroup0_res\" id=\"CheckboxGroup0_res\" value=\"<?php echo ($default['oil_id']); ?>\"><input type=\"hidden\" name=\"CheckboxGroup1_res\" id=\"CheckboxGroup1_res\" value=\"<?php echo ($default['filter_id']); ?>\"><input type=\"hidden\" name=\"CheckboxGroup2_res\" id=\"CheckboxGroup2_res\" value=\"<?php echo ($default['kongqi_id']); ?>\"><input type=\"hidden\" name=\"CheckboxGroup3_res\" id=\"CheckboxGroup3_res\" value=\"<?php echo ($default['kongtiao_id']); ?>\"><input type=\"hidden\" name=\"sub\" value=\"1\">"
		$('#sub').html(submit);
	}

	$("#oil").change(function(){
		var $oil_1_id = $(this).find("option:selected").attr('data-oil-1-id');
		var $oil_1_num = $(this).find("option:selected").attr('data-oil-1-num');
    	var $oil_2_id = $(this).find("option:selected").attr('data-oil-2-id');
    	var $oil_2_num = $(this).find("option:selected").attr('data-oil-2-num');
		var CheckboxGroup0_res = $(this).find("option:selected").attr('data_id');
		

		$('input[name=oil_1_id]').val($oil_1_id);//放到要提交的数据框里面
		$('input[name=oil_2_id]').val($oil_2_id);
		$('input[name=oil_1_num]').val($oil_1_num);
		$('input[name=oil_2_num]').val($oil_2_num);
		$('#CheckboxGroup0_res').val(CheckboxGroup0_res);
		var oil_price = $(this).find("option:selected").attr('data-price');
		var jy_price = $('#jy_price').attr('price');
		var kt_price = $('#kt_price').attr('price');
		var kq_price = $('#kq_price').attr('price');
		var dikou = $('#dikou').attr('price');
		amount = oil_price*1+jy_price*1+kt_price*1+kq_price*1-dikou*1+99;
		$('#oil_price').text('￥'+oil_price);
		$('#oil_price').attr('price',oil_price);
		$('#amount').text('￥'+amount);
	});

	$("#jilv").change(function(){
		var $data_id = $(this).find("option:selected").attr('data_id');
		$('#CheckboxGroup1_res').val($data_id);//放到要提交的数据框里面
		var jy_price = $(this).find("option:selected").attr('data-price');
		var oil_price = $('#oil_price').attr('price');
		var kt_price = $('#kt_price').attr('price');
		var kq_price = $('#kq_price').attr('price');
		var dikou = $('#dikou').attr('price');
		amount = oil_price*1+jy_price*1+kt_price*1+kq_price*1-dikou*1+99;
		$('#jy_price').text('￥'+jy_price);
		$('#jy_price').attr('price',jy_price);
		$('#amount').text('￥'+amount);
	});
	$("#konglv").change(function(){
		var $data_id = $(this).find("option:selected").attr('data_id');
		$('#CheckboxGroup2_res').val($data_id);//放到要提交的数据框里面
		var kq_price = $(this).find("option:selected").attr('data-price');
		var oil_price = $('#oil_price').attr('price');
		var kt_price = $('#kt_price').attr('price');
		var jy_price = $('#jy_price').attr('price');
		var dikou = $('#dikou').attr('price');
		amount = oil_price*1+jy_price*1+kt_price*1+kq_price*1-dikou*1+99;
		$('#kq_price').text('￥'+kq_price);
		$('#kq_price').attr('price',kq_price);
		$('#amount').text('￥'+amount);
	});
	$("#kongtiaolv").change(function(){
		var $data_id = $(this).find("option:selected").attr('data_id');
		$('#CheckboxGroup3_res').val($data_id);//放到要提交的数据框里面
		var kt_price = $(this).find("option:selected").attr('data-price');
		var oil_price = $('#oil_price').attr('price');
		var kq_price = $('#kq_price').attr('price');
		var jy_price = $('#jy_price').attr('price');
		var dikou = $('#dikou').attr('price');
		amount = oil_price*1+jy_price*1+kt_price*1+kq_price*1-dikou*1+99;
		$('#kt_price').text('￥'+kt_price);
		$('#kt_price').attr('price',kt_price);
		$('#amount').text('￥'+amount);
	});

    $("#subBtn").click(function(){
        if($('input[name="service_cost"]:checked').val() == 1){
        }else{
            alert('请确定服务费99元');
            return false;
        }
        addValues();
        $("#subForm").submit();
    });
    $('#no-service').click(function(){
    	if( $(this).attr('checked') == 'checked' ){
    		$('.checkbox').removeAttr('checked');
    		$("#amount").text('99');
    		amount = 99;
    		$('#CheckboxGroup0_res').val('');
    		$('#CheckboxGroup1_res').val('');
    		$('#CheckboxGroup2_res').val('');
    		$('#CheckboxGroup3_res').val('');
    	}else{
    		$('.checkbox').attr('checked','checked');
    		
    		$('.table').find('.checkbox').each(function(){
    			if( $(this).attr('checked') == 'checked' ){
    				amount += Number( $(this).attr('data-price') );
    			}
    		})
    		$("#amount").text(amount);
    		addValues();
    	}
    })
    
    //养车点点ajax更新事件
    $('#ddBtn').bind('click',function(){
        if(confirm('确认更新养车点点')){
            //获取订单id ,用于更新表
            var id = $('#order_id').attr('data-id');
            $.ajax({
                type:'POST',
                url:'__URL__/ajax_dd_update',
                cache:false,
                dataType:'JSON',
                data:'id='+id,
                success:function(data){
                    if(data=='success'){
                        alert('更新已成功完成');
                        location.reload();
                    }else{
                        alert('更新出现错误，请稍后重试!');
                        location.reload();
                    }
                }
            });
        }else{
            return  false ;
        }
         
    });

    //阻止客服同一时间段下过盛的订单
    $('#order_time2').change(function(){
        var order_time = $("#order_time").val();
        var order_time2 = $("#order_time2").val();
        var city_id = $("#city_id").val();
        if(city_id==1){
            $.ajax({
                type:'POST',
                url:'__URL__/prevent',
                cache:false,
                dataType:'JSON',
                data:'order_time='+order_time+'&order_time2='+order_time2+'&type=1',
                success:function(data){
                    //alert(data);
                    if(data<26){
                        jQuery("#editBtn").attr('style','');
                        return true;
                    }else{
                        jQuery("#editBtn").attr('style','display:none;');
                        alert('这个时间段单量已经饱和');
                        return false;
                    }
                }
            });
        }
    });
    
    /*城市区域选择,目前只开放上海 wql@20150727 */  
    $(function(){
        var area_id = Number(<?php echo ($order_info["area_id"]); ?>) ;   
        //alert(area_id);
        $.ajax({
            type:'POST',
            url:'__URL__/ajax_area',
            cache:false,
            dataType:'JSON',
            data:'city_id=1',
            success:function(data){
                var Data = eval('(' + data + ')');
                var data = Data.data;
                if(data.errno == 0){
                    var area_list_html = '<option value="">选择区域</option>';
                    $.each(data.result.area_list, function(k, v){
                        if(area_id && area_id==v['areaID']){
                            area_list_html += '<option selected="selected" value="'+v['areaID']+'">'+v['area']+'</option>';
                        }else{
                            area_list_html += '<option value="'+v['areaID']+'">'+v['area']+'</option>';
                        }
                    });
                    $("#area_id").html(area_list_html);
                }else{
                    return false;
                }
            }
        });  
    });
    
    
    //检查并修改价格 wql@20150728
    $('#chk_price').bind('click',function(){
        $.ajax({
            type:'POST',
            url:'__URL__/ajax_chk_price',
            cache:false,
            dataType:'JSON',
            data:'order_id='+<?php echo ($order_info["id"]); ?>,
            success:function(data){
                var Data = eval('(' + data + ')');
                var data = Data.data;
                if(data.errno == 0){
                   alert(data.errmsg); 
                   location.reload();
                }else{
                    return false;
                }
            }
        });  
    });


    function addValues(){
    	CheckboxGroup0_res = $('option[name=item_0]').attr('data_id');
        CheckboxGroup1_res = $('option[name=item_1]').attr('data_id');
        CheckboxGroup2_res = $('option[name=item_2]').attr('data_id');
        CheckboxGroup3_res = $('option[name=item_3]').attr('data_id');
      	//获得每样的数量
        var $oil_1_id = $('option[name=item_0]').attr('data-oil-1-id'); 
    	var $oil_1_num = $('option[name=item_0]').attr('data-oil-1-num');
    	var $oil_2_id = $('option[name=item_0]').attr('data-oil-2-id');
    	var $oil_2_num = $('option[name=item_0]').attr('data-oil-2-num');
        //if( $('#check1').attr('checked') == 'checked' ){
        	$('option[name=oil_1_id]').val($oil_1_id);//放到要提交的数据框里面
        	$('option[name=oil_2_id]').val($oil_2_id);
        	$('option[name=oil_1_num]').val($oil_1_num);
        	$('option[name=oil_2_num]').val($oil_2_num);
        	$('#CheckboxGroup0_res').val(CheckboxGroup0_res);
        //}else{
        	//$('#CheckboxGroup0_res').val('');
        //}
        if( $('#check2').attr('checked') == 'checked' ){
        	$('#CheckboxGroup1_res').val(CheckboxGroup1_res);
        }else{
        	$('#CheckboxGroup1_res').val('');
        }
        if( $('#check3').attr('checked') == 'checked' ){
        	$('#CheckboxGroup2_res').val(CheckboxGroup2_res);
        }else{
        	$('#CheckboxGroup2_res').val('');
        }
        if( $('#check4').attr('checked') == 'checked' ){
        	$('#CheckboxGroup3_res').val(CheckboxGroup3_res);
        }else{
        	$('#CheckboxGroup3_res').val('');
        }
    }
	function checkClick(event,k){
		$('#no-service').removeAttr('checked');
		k--;
		var $item_price = $('#item_price_'+k);
		if( $(event).attr('checked') == 'checked' ){
			$price = $(event).attr('data-price');
			amount +=  Number($price );
			$("#amount").text(amount);
		}else{
			$price = $(event).attr('data-price');
			
			amount -= Number($price);
			if(amount<0){
				amount = 0;
			}
			console.log(amount)
			$("#amount").text(amount);
		}
		$(this).attr("data-price",$price);
	}

	function showbaogao(){
		$("#showbaogao").show();
	}

	//获取坐标
	function get_coordinate(){
		var address = $('#address').val();
		//alert(address);
		$.ajax({
			type:'POST',
			url:'__APP__/Carservice/Carserviceorder/ajax_geocoder',
			cache:false,
			dataType:'JSON',
			data:{address:address},
			success:function(data1){
				//alert(data1);
				obj = JSON.parse(data1);
				if(obj.data.errno == 0){
					 pos = obj.data.result.coordinate;
					if(pos!=""){
						alert(pos);
						$("#coordinate").val(pos);
						alert("添加坐标成功！");
					}
				}else{
					alert(data.errmsg);
					return false;
				}
			}
		});
	}

</script>


<!-- 检测报告 -->

<div id="showbaogao" class="baogaobox" style="display:none;">
  <div class="baogaoboxtop"><a onclick="jQuery(this).parent().parent().hide()">×</a><h3>检测报告</h3></div>
  <div class="baogaoboxcon">
  	<iframe src ="__WEB__/mobile/check_report-report_id-<?php echo ($order_info['report_id']); ?>" frameborder="0" width="100%" height="100%" marginheight="0" marginwidth="0" frameborder="0" scrolling="auto" width="100%"></iframe>
  </div>
</div>
<style type="text/css">
.baogaobox {
    background: none repeat scroll 0 0 #ffffff;
    border: 1px solid #aaaaaa;
    border-radius: 5px;
    display: none;
    left: 50%;
    margin-left: -300px;
    /*margin-top: -220px;*/
    position: fixed;
    top: 10%;
    width: 768px;
}
.baogaobox .baogaoboxtop {
    border-bottom: 1px solid #aaaaaa;
    height: 40px;
    line-height: 40px;
}
.baogaobox .baogaoboxtop a {
    border-left: 1px solid #aaa;
    cursor: pointer;
    float: right;
    font-size: 18px;
    height: 40px;
    text-align: center;
    width: 40px;
}
.baogaobox .baogaoboxtop a:hover {
    background: none repeat scroll 0 0 #ccc;
}
.baogaobox .baogaoboxtop h3 {
    font-size: 14px;
    line-height: 40px;
    margin: 0 0 0 10px;
    padding: 0;
}
.baogaobox .baogaoboxcon {
    height: 500px;
    overflow: hidden;
}

</style>