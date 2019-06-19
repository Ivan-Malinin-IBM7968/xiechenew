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

<style>
    .tongji {
        width:800px !important;
    }
</style>

<script type='text/javascript' src='__PUBLIC__/Js/calendar.js'></script>

<!-- 主页面开始 -->
<div id="main" class="main" >

<!-- 主体内容  -->
<div class="content" >
<div class="title">上门保养预约订单列表</div>
<!-- 查询区域 -->
<div>

<FORM METHOD=POST ACTION="__URL__/index/type/2"   id="myform">
<div style="margin:10px">电话：<input type="text"  name="mobile" id="mobile" value="<?php echo ($data["mobile"]); ?>"/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;订单号：&nbsp;&nbsp;&nbsp;<input type="text"  name="id" id="id" value="<?php echo ($data["id"]); ?>"/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;车牌：<input type="text" name="licenseplate" id="licenseplate" value="<?php echo ($licenseplate); ?>"/>&nbsp;&nbsp;&nbsp;&nbsp;地址：<input TYPE="text" name="address" value="<?php echo ($address); ?>" style="width:500px"></div>
<div style="margin:10px">
    订单状态：
    <select name="status" id="status">
        <option value="all" <?php if($status == 'all'): ?>selected<?php endif; ?>>全部</option>
        <option value="0" <?php if($status == '0'): ?>selected<?php endif; ?>>等待处理</option>
        <option value="1" <?php if($status == 1): ?>selected<?php endif; ?>>预约确认</option>
        <option value="2" <?php if($status == 2): ?>selected<?php endif; ?>>已分配技师</option>
        <option value="8" <?php if($status == 8): ?>selected<?php endif; ?>>订单作废</option>
        <option value="9" <?php if($status == 9): ?>selected<?php endif; ?>>服务已完成</option>
        <option value="99" <?php if($status == 99): ?>selected<?php endif; ?>>技师对账</option>
    </select>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    城市：
    <select name="city_id" id="city_id">
        <option value="all" <?php if($city_id == 'all'): ?>selected<?php endif; ?>>全部</option>
        <option value="1" <?php if($city_id == 1): ?>selected<?php endif; ?>>上海</option>
        <option value="2" <?php if($city_id == 2): ?>selected<?php endif; ?>>杭州</option>
        <option value="3" <?php if($city_id == 3): ?>selected<?php endif; ?>>苏州</option>
        <option value="4" <?php if($city_id == 4): ?>selected<?php endif; ?>>成都</option>
        <option value="5" <?php if($city_id == 5): ?>selected<?php endif; ?>>济南</option>
        <option value="6" <?php if($city_id == 6): ?>selected<?php endif; ?>>福州</option>
    </select>
    支付方式：
    <select name="pay_type" id="pay_type">
        <option value="all" <?php if($pay_type == 'all'): ?>selected<?php endif; ?>>全部</option>
        <option value="1" <?php if($pay_type == 1): ?>selected<?php endif; ?>>现金</option>
        <option value="2" <?php if($pay_type == 2): ?>selected<?php endif; ?>>线上支付</option>
        <option value="3" <?php if($pay_type == 3): ?>selected<?php endif; ?>>POS机</option>
        <option value="4" <?php if($pay_type == 4): ?>selected<?php endif; ?>>淘宝支付</option>
        <option value="5" <?php if($pay_type == 5): ?>selected<?php endif; ?>>点评支付</option>
        <option value="7" <?php if($pay_type == 7): ?>selected<?php endif; ?>>京东支付</option>
        <option value="9" <?php if($pay_type == 9): ?>selected<?php endif; ?>>PAD支付宝扫码</option>
        <option value="10" <?php if($pay_type == 10): ?>selected<?php endif; ?>>点评到家</option>
        <option value="11" <?php if($pay_type == 11): ?>selected<?php endif; ?>>支付宝WAP</option>
        <option value="12" <?php if($pay_type == 12): ?>selected<?php endif; ?>>天猫</option>
        <option value="13" <?php if($pay_type == 13): ?>selected<?php endif; ?>>同程旅游</option>
    </select>
    客&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;服：&nbsp;
    <select name="customer_id" id="customer_id">
        <option value="">--请选择客服--</option>
        <?php if(is_array($customer_list)): $i = 0; $__LIST__ = $customer_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$customervo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($customervo["id"]); ?>" <?php if($customervo['id'] == $data['customer_id']): ?>selected<?php endif; ?>><?php echo ($customervo["nickname"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
        <option value="1" <?php if($data['customer_id'] == 1): ?>selected<?php endif; ?>>admin</option>
    </select>
    技师姓名：
    <select name="technician_id" id="technician_id">
        <option value="0">全部</option>
        <?php if(is_array($technician_list)): foreach($technician_list as $key=>$technician_list): ?><option value="<?php echo ($technician_list["id"]); ?>"><?php echo ($technician_list["truename"]); ?></option><?php endforeach; endif; ?>
    </select>
    &nbsp;&nbsp;&nbsp;&nbsp;
</div>
<div style="margin:10px">
    开始时间：<input TYPE="text" class="large bLeft"  NAME="start_time" onclick="new Calendar().show(this);" value="<?php echo ($start_time); ?>" readonly="readonly" style="width:150px">
    结束时间：<input TYPE="text" class="large bLeft"  NAME="end_time" onclick="new Calendar().show(this);" value="<?php echo ($end_time); ?>" readonly="readonly" style="width:150px">
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;时间纬度：
    <select name="time_type" id="time_type">
        <option value="create_time" <?php if($time_type == create_time): ?>selected<?php endif; ?>>下单时间</option>
        <option value="order_time" <?php if($time_type == order_time): ?>selected<?php endif; ?>>上门时间</option>
    </select>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;订单来源：
    <select name="remark">
        <option value="">请选择</option>
        <option value="滴答拼车99元通用码" <?php if(($remark) == "滴答拼车99元通用码"): ?>selected<?php endif; ?>>滴答拼车99元通用码</option>
        <option value="携程自驾游月刊项目" <?php if(($remark) == "携程自驾游月刊项目"): ?>selected<?php endif; ?>>携程自驾游月刊项目</option>
        <option value="红牛社区活动" <?php if(($remark) == "红牛社区活动"): ?>selected<?php endif; ?>>红牛社区活动</option>
        <option value="中美国际99元专车年卡" <?php if(($remark) == "中美国际99元专车年卡"): ?>selected<?php endif; ?>>中美国际99元专车年卡</option>
        <option value="中银保险99元专车年卡" <?php if(($remark) == "中银保险99元专车年卡"): ?>selected<?php endif; ?>>中银保险99元专车年卡</option>
        <option value="华安财险99元专车年卡" <?php if(($remark) == "华安财险99元专车年卡"): ?>selected<?php endif; ?>>华安财险99元专车年卡</option>
        <option value="中美国际99元专车年卡1000张" <?php if(($remark) == "中美国际99元专车年卡1000张"): ?>selected<?php endif; ?>>中美国际99元专车年卡1000张</option>
        <option value="华安财险" <?php if(($remark) == "华安财险"): ?>selected<?php endif; ?>>华安财险</option>
        <option value="爱代驾268元中端车型小保养套餐" <?php if(($remark) == "爱代驾268元中端车型小保养套餐"): ?>selected<?php endif; ?>>爱代驾268元中端车型小保养套餐</option>
        <option value="爱代驾368元高端车型小保养套餐" <?php if(($remark) == "爱代驾368元高端车型小保养套餐"): ?>selected<?php endif; ?>>爱代驾368元高端车型小保养套餐</option>
        <option value="免费领取20元券" <?php if(($remark) == "免费领取20元券"): ?>selected<?php endif; ?>>免费领取20元券</option>
        <option value="快的积分商城" <?php if(($remark) == "快的积分商城"): ?>selected<?php endif; ?>>快的积分商城</option>
        <option value="三星" <?php if(($remark) == "三星"): ?>selected<?php endif; ?>>三星</option>
        <option value="平安好车采购" <?php if(($remark) == "平安好车采购"): ?>selected<?php endif; ?>>平安好车采购</option>
        <option value="中化道达尔代金卡2000张" <?php if(($remark) == "中化道达尔代金卡2000张"): ?>selected<?php endif; ?>>中化道达尔代金卡2000张</option>
        <option value="中化道达尔礼品卡10000张" <?php if(($remark) == "中化道达尔礼品卡10000张"): ?>selected<?php endif; ?>>中化道达尔礼品卡10000张</option>
        <option value="驴妈妈" <?php if(($remark) == "驴妈妈"): ?>selected<?php endif; ?>>驴妈妈</option>
        <option value="驴妈妈地推及自驾游" <?php if(($remark) == "驴妈妈地推及自驾游"): ?>selected<?php endif; ?>>驴妈妈地推及自驾游</option>
        <option value="驴妈妈积分商城20元券" <?php if(($remark) == "驴妈妈积分商城20元券"): ?>selected<?php endif; ?>>驴妈妈积分商城20元券</option>
        <option value="三星车险赠送卡制作用" <?php if(($remark) == "三星车险赠送卡制作用"): ?>selected<?php endif; ?>>三星车险赠送卡制作用</option>
        <option value="大客户" <?php if(($remark) == "大客户"): ?>selected<?php endif; ?>>大客户</option>
        <option value="补中化道达尔代金卡8000张（送）" <?php if(($remark) == "补中化道达尔代金卡8000张（送）"): ?>selected<?php endif; ?>>补中化道达尔代金卡8000张（送）</option>
    </select>
</div>
<div style="margin:10px">
    下单渠道：
    <select name="order_origin">
        <option value="">请选择</option>
        <option value="1" <?php if(($origin) == "1"): ?>selected<?php endif; ?>>PC下单</option>
        <option value="2" <?php if(($origin) == "2"): ?>selected<?php endif; ?>>微信下单</option>
        <option value="3" <?php if(($origin) == "3"): ?>selected<?php endif; ?>>后台带下单</option>
        <option value="4" <?php if(($origin) == "8"): ?>selected<?php endif; ?>>新版APP下单</option>
    </select>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;订单类型：
    <select name="order_type" id="order_type">
        <option value="">请选择</option>
        <option value="1" <?php if(($order_type) == "1"): ?>selected<?php endif; ?>>保养订单</option>
        <option value="2" <?php if(($order_type) == "2"): ?>selected<?php endif; ?>>检测订单</option>
        <option value="3" <?php if(($order_type) == "3"): ?>selected<?php endif; ?>>淘宝已支付订单</option>
        <option value="20" <?php if(($order_type) == "20"): ?>selected<?php endif; ?>>上海平安</option>
        <option value="22" <?php if(($order_type) == "22"): ?>selected<?php endif; ?>>光大168</option>
        <option value="23" <?php if(($order_type) == "23"): ?>selected<?php endif; ?>>光大268</option>
        <option value="24" <?php if(($order_type) == "24"): ?>selected<?php endif; ?>>光大368</option>
        <option value="35" <?php if(($order_type) == "35"): ?>selected<?php endif; ?>>好车况(市场部推广)</option>
        <option value="36" <?php if(($order_type) == "36"): ?>selected<?php endif; ?>>大众点评199</option>
        <option value="37" <?php if(($order_type) == "37"): ?>selected<?php endif; ?>>大众点评299</option>
        <option value="38" <?php if(($order_type) == "38"): ?>selected<?php endif; ?>>大众点评399</option>
        <option value="50" <?php if(($order_type) == "50"): ?>selected<?php endif; ?>>好车况(点评)</option>
        <option value="51" <?php if(($order_type) == "51"): ?>selected<?php endif; ?>>保养(点评)</option>

        <option value="52" <?php if(($order_type) == "52"): ?>selected<?php endif; ?>>建设银行168</option>
        <option value="53" <?php if(($order_type) == "53"): ?>selected<?php endif; ?>>建设银行268</option>
        <option value="54" <?php if(($order_type) == "54"): ?>selected<?php endif; ?>>建设银行368</option>
        <option value="55" <?php if(($order_type) == "55"): ?>selected<?php endif; ?>>建设银行好空气16.8</option>
        <option value="56" <?php if(($order_type) == "56"): ?>selected<?php endif; ?>>建设银行好空气98</option>
    </select>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;业务类型：
    <select name="business_source" id="business_source">
        <option value="">请选择</option>
        <?php if(is_array($business_source_list)): $i = 0; $__LIST__ = $business_source_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo[id]); ?>" <?php if(($order_type) == "<?php echo ($vo[id]); ?>"): ?>selected<?php endif; ?>><?php echo ($vo[name]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
    </select>
    旧业务类型：
    <select name="business_source_old" id="business_source">
        <option value="">请选择</option>
        <?php if(is_array($business_source_old)): $i = 0; $__LIST__ = $business_source_old;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($key); ?>" <?php if(($order_type) == "<?php echo ($key); ?>"): ?>selected<?php endif; ?>><?php echo ($vo); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
    </select>
    &nbsp;绑定微信：
    <select name="weixinbind" id="weixinbind">
        <option value="">请选择</option>
        <option value="1" <?php if(($weixinbind) == "1"): ?>selected<?php endif; ?>>未绑定</option>
        <option value="2" <?php if(($weixinbind) == "2"): ?>selected<?php endif; ?>>已绑定</option>
    </select>
</div>
<div style="margin:10px">
    <input type="checkbox" name="is_del" value='1' <?php if($type == ''): ?>checked<?php endif; ?>>不显示作废订单
    <input type="checkbox" name="is_delay" value='1'>显示已过预约时间未完成订单
    <input type="submit" value="查询" style="impBtn hMargin fLeft shadow">&nbsp;&nbsp;
    <input type="button" value="重置" style="impBtn hMargin fLeft shadow" onclick="window.location.href='__APP__/Carservice/Carserviceorder/';">&nbsp;&nbsp;&nbsp;&nbsp;
    <a href="__APP__/Carservice/Carserviceorder/index/status/all" style="padding-right:50px">(当前显示的是未处理订单，点此查看全部订单)</a>
</div>

<!--<table cellspacing="0" cellpadding="10" >-->
  <!--<tr>  -->
    <!--<td width=25%>-->
      <!--订单号：&nbsp;&nbsp;&nbsp;<input type="text"  name="id" id="id" value="<?php echo ($data["id"]); ?>"/></td>-->
    <!--<td >-->
      <!--电话：<input type="text"  name="mobile" id="mobile" value="<?php echo ($data["mobile"]); ?>"/></span>-->
    <!--</td>-->
	<!--<td>-->
	<!--客&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;服：&nbsp;-->
	<!--<select name="customer_id" id="customer_id">-->
		<!--<option value="">--请选择客服--</option>-->
		<!--<?php if(is_array($customer_list)): $i = 0; $__LIST__ = $customer_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$customervo): $mod = ($i % 2 );++$i;?>-->
		<!--<option value="<?php echo ($customervo["id"]); ?>" <?php if($customervo['id'] == $data['customer_id']): ?>selected<?php endif; ?>><?php echo ($customervo["nickname"]); ?></option>-->
	<!--<?php endforeach; endif; else: echo "" ;endif; ?>-->
		<!--<option value="1" <?php if($data['customer_id'] == 1): ?>selected<?php endif; ?>>admin</option>-->
	<!--</select>-->
	<!--</td>-->
	<!--<td>  -->
		<!--<input type="checkbox" name="is_delay" value='1'>显示已过预约时间未完成订单-->
	<!--</td>-->
  <!--</tr>-->

  <!---->
  <!--<tr>  -->
    <!--<td>-->
      <!--车牌：<input type="text" name="licenseplate" id="licenseplate" value="<?php echo ($licenseplate); ?>"/>-->
    <!--</td>-->

    <!--<td>-->
    <!--技师姓名：-->
    <!--<select name="technician_id" id="technician_id">-->
      <!--<option value="0">全部</option>-->
      <!--<?php if(is_array($technician_list)): foreach($technician_list as $key=>$technician_list): ?>-->
      <!--<option value="<?php echo ($technician_list["id"]); ?>"><?php echo ($technician_list["truename"]); ?></option>-->
      <!--<?php endforeach; endif; ?>-->
    <!--</select>-->
	<!--&nbsp;&nbsp;&nbsp;&nbsp;-->
	<!--</td>-->

	<!--<td>-->
    <!--订单状态：-->
    <!--<select name="status" id="status">-->
		<!--<option value="all" <?php if($status == 'all'): ?>selected<?php endif; ?>>全部</option>-->
		<!--<option value="0" <?php if($status == '0'): ?>selected<?php endif; ?>>等待处理</option>-->
		<!--<option value="1" <?php if($status == 1): ?>selected<?php endif; ?>>预约确认</option>-->
		<!--<option value="2" <?php if($status == 2): ?>selected<?php endif; ?>>已分配技师</option>-->
		<!--<option value="8" <?php if($status == 8): ?>selected<?php endif; ?>>订单作废</option>-->
		<!--<option value="9" <?php if($status == 9): ?>selected<?php endif; ?>>服务已完成</option>-->
		<!--<option value="99" <?php if($status == 99): ?>selected<?php endif; ?>>技师对账</option>-->
    <!--</select>-->
	<!--&nbsp;&nbsp;&nbsp;&nbsp;-->
	<!--</td>-->
	<!--<td>-->
    <!--支付方式：-->
    <!--<select name="pay_type" id="pay_type">-->
		<!--<option value="all" <?php if($pay_type == 'all'): ?>selected<?php endif; ?>>全部</option>-->
		<!--<option value="1" <?php if($pay_type == 1): ?>selected<?php endif; ?>>现金</option>-->
		<!--<option value="2" <?php if($pay_type == 2): ?>selected<?php endif; ?>>线上支付</option>-->
		<!--<option value="3" <?php if($pay_type == 3): ?>selected<?php endif; ?>>POS机</option>-->
		<!--<option value="4" <?php if($pay_type == 4): ?>selected<?php endif; ?>>淘宝支付</option>-->
		<!--<option value="5" <?php if($pay_type == 5): ?>selected<?php endif; ?>>点评支付</option>-->
		<!--<option value="7" <?php if($pay_type == 7): ?>selected<?php endif; ?>>京东支付</option>-->
        <!--<option value="9" <?php if($pay_type == 9): ?>selected<?php endif; ?>>PAD支付宝扫码</option>-->
        <!--<option value="10" <?php if($pay_type == 10): ?>selected<?php endif; ?>>点评到家</option>-->
        <!--<option value="11" <?php if($pay_type == 11): ?>selected<?php endif; ?>>支付宝WAP</option>-->
        <!--<option value="12" <?php if($pay_type == 12): ?>selected<?php endif; ?>>天猫</option>-->
        <!--<option value="13" <?php if($pay_type == 13): ?>selected<?php endif; ?>>同程旅游</option>-->
    <!--</select>-->
	<!--城市：-->
    <!--<select name="city_id" id="city_id">-->
		<!--<option value="all" <?php if($city_id == 'all'): ?>selected<?php endif; ?>>全部</option>-->
		<!--<option value="1" <?php if($city_id == 1): ?>selected<?php endif; ?>>上海</option>-->
		<!--<option value="2" <?php if($city_id == 2): ?>selected<?php endif; ?>>杭州</option>-->
		<!--<option value="3" <?php if($city_id == 3): ?>selected<?php endif; ?>>苏州</option>-->
		<!--<option value="4" <?php if($city_id == 4): ?>selected<?php endif; ?>>成都</option>-->
		<!--<option value="5" <?php if($city_id == 5): ?>selected<?php endif; ?>>济南</option>-->
        <!--<option value="6" <?php if($city_id == 6): ?>selected<?php endif; ?>>福州</option>-->
    <!--</select>-->
    <!--</td>-->
  <!--</tr>-->
  <!---->
  <!--<tr>  -->
    <!--<td colspan="3">  -->
      <!--开始时间：<input TYPE="text" class="large bLeft"  NAME="start_time" onclick="new Calendar().show(this);" value="<?php echo ($start_time); ?>" readonly="readonly">-->
      <!--结束时间：<input TYPE="text" class="large bLeft"  NAME="end_time" onclick="new Calendar().show(this);" value="<?php echo ($end_time); ?>" readonly="readonly">-->
    <!--</td>-->
	<!--<td>  -->
		<!--时间纬度：-->
		<!--<select name="time_type" id="time_type">-->
			<!--<option value="create_time" <?php if($time_type == create_time): ?>selected<?php endif; ?>>下单时间</option>-->
			<!--<option value="order_time" <?php if($time_type == order_time): ?>selected<?php endif; ?>>上门时间</option>-->
		<!--</select>-->
	<!--</td>-->
  <!--</tr>-->
  <!--<tr>-->
	<!--<td colspan="2">地址：<input TYPE="text" name="address" value="<?php echo ($address); ?>" style="width:500px"></td>-->
	<!--<td>-->
	<!--绑定微信：-->
	<!--<select name="weixinbind" id="weixinbind">-->
		<!--<option value="">请选择</option>-->
		<!--<option value="1" <?php if(($weixinbind) == "1"): ?>selected<?php endif; ?>>未绑定</option>-->
		<!--<option value="2" <?php if(($weixinbind) == "2"): ?>selected<?php endif; ?>>已绑定</option>-->
	<!--</select>-->
	<!--</td>-->
	<!--<td>  -->
	<!--业务类型：-->
	<!--<select name="business_source" id="business_source">-->
	<!--<option value="">请选择</option>-->
		<!--<?php if(is_array($business_source_list)): $i = 0; $__LIST__ = $business_source_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>-->
		<!--<option value="<?php echo ($key); ?>" <?php if(($order_type) == $key): ?>selected<?php endif; ?>><?php echo ($vo); ?></option>-->
		<!--<?php endforeach; endif; else: echo "" ;endif; ?>-->
	<!--</select>-->
	<!--</td>-->
	<!--<td>  -->
	    <!--订单类型：-->
		<!--<select name="order_type" id="order_type">-->
			<!--<option value="">请选择</option>-->
			<!--<option value="1" <?php if(($order_type) == "1"): ?>selected<?php endif; ?>>保养订单</option>-->
			<!--<option value="2" <?php if(($order_type) == "2"): ?>selected<?php endif; ?>>检测订单</option>-->
			<!--<option value="3" <?php if(($order_type) == "3"): ?>selected<?php endif; ?>>淘宝已支付订单</option>-->
			<!--<option value="20" <?php if(($order_type) == "20"): ?>selected<?php endif; ?>>上海平安</option>-->
			<!--<option value="22" <?php if(($order_type) == "22"): ?>selected<?php endif; ?>>光大168</option>-->
			<!--<option value="23" <?php if(($order_type) == "23"): ?>selected<?php endif; ?>>光大268</option>-->
			<!--<option value="24" <?php if(($order_type) == "24"): ?>selected<?php endif; ?>>光大368</option>-->
			<!--<option value="35" <?php if(($order_type) == "35"): ?>selected<?php endif; ?>>好车况(市场部推广)</option>-->
			<!--<option value="36" <?php if(($order_type) == "36"): ?>selected<?php endif; ?>>大众点评199</option>-->
			<!--<option value="37" <?php if(($order_type) == "37"): ?>selected<?php endif; ?>>大众点评299</option>-->
			<!--<option value="38" <?php if(($order_type) == "38"): ?>selected<?php endif; ?>>大众点评399</option>-->
			<!--<option value="50" <?php if(($order_type) == "50"): ?>selected<?php endif; ?>>好车况(点评)</option>-->
			<!--<option value="51" <?php if(($order_type) == "51"): ?>selected<?php endif; ?>>保养(点评)</option>-->
                <!---->
                        <!--<option value="52" <?php if(($order_type) == "52"): ?>selected<?php endif; ?>>建设银行168</option>-->
			<!--<option value="53" <?php if(($order_type) == "53"): ?>selected<?php endif; ?>>建设银行268</option>-->
			<!--<option value="54" <?php if(($order_type) == "54"): ?>selected<?php endif; ?>>建设银行368</option>-->
			<!--<option value="55" <?php if(($order_type) == "55"): ?>selected<?php endif; ?>>建设银行好空气16.8</option>-->
			<!--<option value="56" <?php if(($order_type) == "56"): ?>selected<?php endif; ?>>建设银行好空气98</option>-->
		<!--</select>-->
	<!--</td>-->

  <!--</tr>-->
  <!--<tr>  -->
  <!--&lt;!&ndash;  <td><a href="/car/index.php/Admin/Index/select_car" target="_blank">下单</a></td>&ndash;&gt;-->
    <!--<td colspan="2"> -->
		<!--<a href="__APP__/Carservice/Carserviceorder/index/status/all" style="padding-right:50px">(当前显示的是未处理订单，点此查看全部订单)</a>-->
		<!--<input type="submit" value="查询" style="impBtn hMargin fLeft shadow">&nbsp;&nbsp;-->
		<!--<input type="button" value="重置" style="impBtn hMargin fLeft shadow" onclick="window.location.href='__APP__/Carservice/Carserviceorder/';">&nbsp;&nbsp;&nbsp;&nbsp;-->
		<!--<input type="checkbox" name="is_del" value='1' <?php if($type == ''): ?>checked<?php endif; ?>>不显示作废订单-->

    <!--</td>-->
    <!--<td>-->
    <!--下单渠道：-->
    <!--<select name="order_origin">-->
    	<!--<option value="">请选择</option>-->
    	<!--<option value="1" <?php if(($origin) == "1"): ?>selected<?php endif; ?>>PC下单</option>-->
    	<!--<option value="2" <?php if(($origin) == "2"): ?>selected<?php endif; ?>>微信下单</option>-->
    	<!--<option value="3" <?php if(($origin) == "3"): ?>selected<?php endif; ?>>后台带下单</option>-->
    	<!--<option value="4" <?php if(($origin) == "8"): ?>selected<?php endif; ?>>新版APP下单</option>-->
    <!--</select>-->
    <!--</td>-->
	<!--<td>-->
	<!--订单来源：-->
	<!--<select name="remark">-->
		<!--<option value="">请选择</option>-->
		<!--<option value="携程自驾游月刊项目" <?php if(($remark) == "携程自驾游月刊项目"): ?>selected<?php endif; ?>>携程自驾游月刊项目</option>-->
		<!--<option value="红牛社区活动" <?php if(($remark) == "红牛社区活动"): ?>selected<?php endif; ?>>红牛社区活动</option>-->
		<!--<option value="中美国际99元专车年卡" <?php if(($remark) == "中美国际99元专车年卡"): ?>selected<?php endif; ?>>中美国际99元专车年卡</option>-->
		<!--<option value="中银保险99元专车年卡" <?php if(($remark) == "中银保险99元专车年卡"): ?>selected<?php endif; ?>>中银保险99元专车年卡</option>	-->
		<!--<option value="华安财险99元专车年卡" <?php if(($remark) == "华安财险99元专车年卡"): ?>selected<?php endif; ?>>华安财险99元专车年卡</option>	-->
		<!--<option value="中美国际99元专车年卡1000张" <?php if(($remark) == "中美国际99元专车年卡1000张"): ?>selected<?php endif; ?>>中美国际99元专车年卡1000张</option>	-->
		<!--<option value="华安财险" <?php if(($remark) == "华安财险"): ?>selected<?php endif; ?>>华安财险</option>	-->
		<!--<option value="爱代驾268元中端车型小保养套餐" <?php if(($remark) == "爱代驾268元中端车型小保养套餐"): ?>selected<?php endif; ?>>爱代驾268元中端车型小保养套餐</option>-->
		<!--<option value="爱代驾368元高端车型小保养套餐" <?php if(($remark) == "爱代驾368元高端车型小保养套餐"): ?>selected<?php endif; ?>>爱代驾368元高端车型小保养套餐</option>-->
		<!--<option value="免费领取20元券" <?php if(($remark) == "免费领取20元券"): ?>selected<?php endif; ?>>免费领取20元券</option>-->
		<!--<option value="快的积分商城" <?php if(($remark) == "快的积分商城"): ?>selected<?php endif; ?>>快的积分商城</option>-->
		<!--<option value="三星" <?php if(($remark) == "三星"): ?>selected<?php endif; ?>>三星</option>-->
		<!--<option value="平安好车采购" <?php if(($remark) == "平安好车采购"): ?>selected<?php endif; ?>>平安好车采购</option>-->
		<!--<option value="中化道达尔代金卡2000张" <?php if(($remark) == "中化道达尔代金卡2000张"): ?>selected<?php endif; ?>>中化道达尔代金卡2000张</option>-->
		<!--<option value="中化道达尔礼品卡10000张" <?php if(($remark) == "中化道达尔礼品卡10000张"): ?>selected<?php endif; ?>>中化道达尔礼品卡10000张</option>-->
		<!--<option value="驴妈妈" <?php if(($remark) == "驴妈妈"): ?>selected<?php endif; ?>>驴妈妈</option>-->
		<!--<option value="驴妈妈地推及自驾游" <?php if(($remark) == "驴妈妈地推及自驾游"): ?>selected<?php endif; ?>>驴妈妈地推及自驾游</option>-->
		<!--<option value="驴妈妈积分商城20元券" <?php if(($remark) == "驴妈妈积分商城20元券"): ?>selected<?php endif; ?>>驴妈妈积分商城20元券</option>-->
		<!--<option value="三星车险赠送卡制作用" <?php if(($remark) == "三星车险赠送卡制作用"): ?>selected<?php endif; ?>>三星车险赠送卡制作用</option>-->
		<!--<option value="大客户" <?php if(($remark) == "大客户"): ?>selected<?php endif; ?>>大客户</option>-->
		<!--<option value="补中化道达尔代金卡8000张（送）" <?php if(($remark) == "补中化道达尔代金卡8000张（送）"): ?>selected<?php endif; ?>>补中化道达尔代金卡8000张（送）</option>-->
	<!--</select>-->
	<!--</td>-->
  <!--</tr>-->
<!--</table>-->
<input type="hidden" value="<?php echo ($list_type); ?>" name="list_type">
</FORM>
<div>
    发送20元抵用券<input name="send_mobile" type="text" /><button id="send_msg">发送</button>
    <?php if(in_array($authId,$csvArray)): ?><button id="order_export">导出订单为csv</button> <button id="fit_export">导出配件合计为csv</button><?php endif; ?>
    <a href="__APP__/Carservice/Carserviceorder/index/list_type/bill<?php echo ($search); ?>" style="padding-right:50px">切换技师对账数据</a> 
</div>
</div>
<!-- 功能操作区域结束 -->

 <?php if($list_type == ''): ?><!-- 列表显示区域  -->
	<div class="list" >
	<table class="list">
	<tr class="row">
	<th width=5%>订单号</th>
	<th width=2%>城市</th>
	<th width=5%>
	 <?php if($order == 'order_time'): ?><a href="javascript:sortBy('order_time','<?php echo ($sort); ?>','index/status/<?php echo ($status); ?>')" title="按照编号<?php echo ($sortType); ?>">预约时间<img src="__PUBLIC__/Images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"></a>
	 <?php else: ?>
		<a href="javascript:sortBy('order_time','0','index/status/<?php echo ($status); ?>/customer_id/<?php echo ($admin_id); ?>/time_type/<?php echo ($time_type); ?>/start_time/<?php echo ($start_time); ?>/end_time/<?php echo ($end_time); ?>')" title="按照编号升序排列 ">预约时间</a><?php endif; ?>
	</th>

	<th width=5%>
	 <?php if($order == 'create_time'): ?><a href="javascript:sortBy('create_time','<?php echo ($sort); ?>','index/status/<?php echo ($status); ?>/customer_id/<?php echo ($admin_id); ?>/time_type/<?php echo ($time_type); ?>/start_time/<?php echo ($start_time); ?>/end_time/<?php echo ($end_time); ?>')" title="按照编号<?php echo ($sortType); ?>">下单时间<img src="__PUBLIC__/Images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"></a>
	 <?php else: ?>
		<a href="javascript:sortBy('create_time','0','index/status/<?php echo ($status); ?>')" title="按照编号升序排列 ">下单时间</a><?php endif; ?>
	</th>

	<th width=3%>姓名</th>

	<th width=5%>
	 <?php if($order == 'mobile'): ?><a href="javascript:sortBy('mobile','<?php echo ($sort); ?>','index')" title="按照编号<?php echo ($sortType); ?>">电话<img src="__PUBLIC__/Images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"></a>
	 <?php else: ?>
		<a href="javascript:sortBy('mobile','1','index')" title="按照编号升序排列 ">电话</a><?php endif; ?>
	</th>

	<th width=4%>
	 <?php if($order == 'licenseplate'): ?><a href="javascript:sortBy('licenseplate','<?php echo ($sort); ?>','index')" title="按照编号<?php echo ($sortType); ?>">车牌<img src="__PUBLIC__/Images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"></a>
	 <?php else: ?>
		<a href="javascript:sortBy('licenseplate','1','index')" title="按照编号升序排列 ">车牌</a><?php endif; ?>
	</th>

	<th width=7%>地址</th>

	<th width=5%>
	 <?php if($order == 'amount'): ?><a href="javascript:sortBy('amount','<?php echo ($sort); ?>','index')" title="按照编号<?php echo ($sortType); ?>">金额<img src="__PUBLIC__/Images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"></a>
	 <?php else: ?>
		<a href="javascript:sortBy('amount','1','index')" title="按照编号升序排列 ">金额</a><?php endif; ?>
	</th>
	<th width=5%>抵用码</th>

	<th width=3%>

	 <?php if($order == 'status'): ?><a href="javascript:sortBy('status','<?php echo ($sort); ?>','index')" title="按照编号<?php echo ($sortType); ?>">支付状态<img src="__PUBLIC__/Images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"></a>
	 <?php else: ?>
		<a href="javascript:sortBy('status','1','index')" title="按照编号升序排列 ">预约状态</a><?php endif; ?>

	</th>

	<th width=2%>技师</th>
	<th>客服</th>
	<th>操作</th>
	<th>车型</th>
	<th>机油</th>
	<th>机滤</th>
	<th>空气滤</th>
	<th>空调滤</th>
	</tr>

	<tr>
	<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><td><?php if($authId != '237'): ?><a href="__URL__/detail?id=<?php echo ($vo["id"]); ?>" style="margin-left:5px"><?php echo ($vo["show_id"]); ?></a><?php else: echo ($vo["show_id"]); endif; ?></td>
	<td>
	<?php if($vo["city_id"] == '1'): ?>上海<?php endif; ?>
	<?php if($vo["city_id"] == '2'): ?>杭州<?php endif; ?>
	<?php if($vo["city_id"] == '3'): ?>苏州<?php endif; ?>
	<?php if($vo["city_id"] == '4'): ?>成都<?php endif; ?>
	<?php if($vo["city_id"] == '5'): ?>济南<?php endif; ?>
    <?php if($vo["city_id"] == '6'): ?>福州<?php endif; ?>
	</td>
	<td><?php echo (date("y-m-d H:i:s",$vo["order_time"])); ?></td>
	<td><?php echo (date("y-m-d H:i:s",$vo["create_time"])); ?></td>
	<td><?php echo ($vo["truename"]); ?></td>
	<td><?php echo ($vo["mobile"]); ?></td>
	<td><?php echo ($vo["licenseplate"]); ?></td>
	<td><?php echo ($vo["address"]); ?></td>
	<td><?php echo ($vo["amount"]); ?></td>
	<td><?php echo ($vo["replace_code"]); ?></td>
	<td><?php echo ($vo["status_name"]); ?></td>
	<td><?php echo ($vo["technician_name"]); ?></td>
	<td><?php echo ($vo["operate"]); ?>(<?php echo ($vo["operate_id"]); ?>)</td>
	<td>
	  <?php if($vo["status"] == '0'): if($authId != '237'): ?><a href="__URL__/detail?id=<?php echo ($vo["id"]); ?>">处理订单</a><?php endif; ?>
	  <?php elseif($vo["status"] == '1'): ?>

	  <a href="__URL__/technician_assign?id=<?php echo ($vo["id"]); ?>&start_time=<?php echo ($start_time); ?>&end_time=<?php echo ($end_time); ?>">分配技师</a>

	  <?php elseif($vo["status"] == '2'): ?>
		<?php if(($authId == 271) OR ($authId == 286) OR ($authId == 219) OR ($authId == 240) OR ($authId == 1) OR ($authId == 140) OR ($authId == 244) OR ($authId == 237) OR ($authId == 272) OR ($authId == 278) OR ($authId == 287) OR ($authId == 288) OR ($authId == 392) OR ($authId == 384) OR ($authId == 366) OR ($authId == 248)): ?><a href="javascript:if(confirm('确实要重置吗?'))location='__URL__/update_to_process1?id=<?php echo ($vo["id"]); ?>'">重置分配</a><?php endif; ?>
		<?php if($authId != '237'): ?><!-- <a href="__URL__/process_9?id=<?php echo ($vo["id"]); ?>">完成订单</a> --><a href="__URL__/detail?id=<?php echo ($vo["id"]); ?>">处理订单
	  </a><?php endif; ?>
	  <?php elseif($vo["status"] == '9'): ?>
		<?php if($authId != '237'): ?><a href="__URL__/detail?id=<?php echo ($vo["id"]); ?>" style="margin-left:5px">订单已完成</a><?php endif; endif; ?>
	  <a href="javascript:void(0);" onClick="showlog('carserviceorder',<?php echo ($vo["true_id"]); ?>)">查看日志</a>
	  <!-- <a href="__URL__/show?id=<?php echo ($vo["id"]); ?>" style="margin-left:5px">查看订单</a> -->
	</td>
	<td><?php echo ($vo["car_name"]); ?></td>
	<td><?php echo ($vo["oil"]); ?></td>
	<td><?php echo ($vo["filter"]); ?></td>
	<td><?php echo ($vo["kongqi"]); ?></td>
	<td><?php echo ($vo["kongtiao"]); ?></td>
	</tr><?php endforeach; endif; else: echo "" ;endif; ?>
	</table>
    <table class="list tongji">
        <tr class="row">
            <th>套餐类型</th>
            <th>订单数</th>
            <th>去重手机数</th>
            <th>去重车牌数</th>
            <th>去重地址数</th>
            <th>绝对唯一订单数</th>
            <th>总价</th>
        </tr>
        <?php if(is_array($priceInfo)): $i = 0; $__LIST__ = $priceInfo;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$price): $mod = ($i % 2 );++$i;?><tr>
                <td><?php echo ($price["name"]); ?></td>
                <td><?php echo ($price["orderCount"]); ?></td>
                <td><?php echo ($price["distinctMobile"]); ?></td>
                <td><?php echo ($price["distinctLicenseplate"]); ?></td>
                <td><?php echo ($price["distinctAddress"]); ?></td>
                <td><?php echo ($price["absoluteCount"]); ?></td>
                <td><?php echo ($price["value"]); ?></td>
            </tr><?php endforeach; endif; else: echo "" ;endif; ?>
    </table>
	</div>
<!--  分页显示区域 -->
<div class="page" style="float:left"><?php if($page != ''): echo ($page); else: ?>共<?php echo ($source_count); ?>条记录<?php endif; ?></div><?php endif; ?>
<?php if($list_type == 'bill'): ?><!-- 列表显示区域  -->
<div class="list" >
<table class="list">
<tr class="row">
<th>订单号</th>
<th>城市</th>
<th>
 <?php if($order == 'order_time'): ?><a href="javascript:sortBy('order_time','<?php echo ($sort); ?>','index/status/<?php echo ($status); ?>')" title="按照编号<?php echo ($sortType); ?>">预约时间<img src="__PUBLIC__/Images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"></a>
 <?php else: ?>
    <a href="javascript:sortBy('order_time','0','index/status/<?php echo ($status); ?>')" title="按照编号升序排列 ">预约时间</a><?php endif; ?>
</th>

<th>
 <?php if($order == 'create_time'): ?><a href="javascript:sortBy('create_time','<?php echo ($sort); ?>','index/status/<?php echo ($status); ?>')" title="按照编号<?php echo ($sortType); ?>">下单时间<img src="__PUBLIC__/Images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"></a>
 <?php else: ?>
    <a href="javascript:sortBy('create_time','0','index/status/<?php echo ($status); ?>')" title="按照编号升序排列 ">下单时间</a><?php endif; ?>
</th>

<th>
 <?php if($order == 'licenseplate'): ?><a href="javascript:sortBy('licenseplate','<?php echo ($sort); ?>','index')" title="按照编号<?php echo ($sortType); ?>">车牌<img src="__PUBLIC__/Images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"></a>
 <?php else: ?>
    <a href="javascript:sortBy('licenseplate','1','index')" title="按照编号升序排列 ">车牌</a><?php endif; ?>
</th>

<th>
 <?php if($order == 'amount'): ?><a href="javascript:sortBy('amount','<?php echo ($sort); ?>','index')" title="按照编号<?php echo ($sortType); ?>">金额<img src="__PUBLIC__/Images/<?php echo ($sortImg); ?>.gif" width="12" height="17" border="0" align="absmiddle"></a>
 <?php else: ?>
    <a href="javascript:sortBy('amount','1','index')" title="按照编号升序排列 ">金额</a><?php endif; ?>
</th>
<th>支付方式</th>
<th>
    <a href="javascript:sortBy('status','1','index')" title="按照编号升序排列 ">预约状态</a>
</th>

<th>技师</th>
<th>客服</th>
<th>车型</th>
<th>机油</th>
<th>机滤</th>
<th>空气滤</th>
<th>空调滤</th>
</tr>

<tr>
<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><td><?php if($authId != '237'): ?><a href="__URL__/detail?id=<?php echo ($vo["id"]); ?>" style="margin-left:5px"><?php echo ($vo["show_id"]); ?></a><?php else: echo ($vo["show_id"]); endif; ?></td>
<td><?php if($vo["city_id"] == '1'): ?>上海<?php endif; ?>
	<?php if($vo["city_id"] == '2'): ?>杭州<?php endif; ?>
	<?php if($vo["city_id"] == '3'): ?>苏州<?php endif; ?>
    <?php if($vo["city_id"] == '4'): ?>成都<?php endif; ?>
    <?php if($vo["city_id"] == '5'): ?>济南<?php endif; ?>
</td>
<td><?php echo (date("y-m-d",$vo["order_time"])); ?></td>
<td><?php echo (date("y-m-d",$vo["create_time"])); ?></td>
<td><?php echo ($vo["licenseplate"]); ?></td>
<td><?php echo ($vo["amount"]); ?></td>
<td><?php echo ($vo["pay_type"]); ?></td>
<td><?php echo ($vo["status_name"]); ?></td>
<td><?php echo ($vo["technician_name"]); ?></td>
<td><?php echo ($vo["operate"]); ?>(<?php echo ($vo["operate_id"]); ?>)</td>
<td><?php echo ($vo["car_name"]); ?></td>
<td><?php echo ($vo["oil"]); ?></td>
<td><?php echo ($vo["filter"]); ?></td>
<td><?php echo ($vo["kongqi"]); ?></td>
<td><?php echo ($vo["kongtiao"]); ?></td>
</tr><?php endforeach; endif; else: echo "" ;endif; ?>
<tr><td colspan="12">
	<?php if(is_array($priceInfo)): $i = 0; $__LIST__ = $priceInfo;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$price): $mod = ($i % 2 );++$i; echo ($price["name"]); ?>:<font style="color:red"><?php echo ($price["value"]); ?>元</font>&nbsp;&nbsp;&nbsp;&nbsp;<?php endforeach; endif; else: echo "" ;endif; ?>
</td>
</tr>
</table>
</div>
<!--  分页显示区域 --><?php endif; ?>

<script>
jQuery('#send_msg').click(function(){
	var send_mobile = jQuery('input[name=send_mobile]').val();
	if(!send_mobile){
		alert('请先输入手机号码');
		return false;
	}
	jQuery.post('__URL__/code_process',{'mobile':send_mobile},function(data){
		var data = data.data;
		alert(data.message);
	},'json')
}) 

//导出订单csv
jQuery('#order_export').click(function(){
    //alert('11111');
    var ret = check();
    if(ret == false){
        return false ;
    }
    
    var param = jQuery('#myform').serialize() ;
    var url = '/Admin/index.php/Carservice/Carserviceorder/order_export?'+param ;
    window.location.href= url ;  
});

jQuery('#fit_export').click(function(){
    //alert('11111');
    var ret = check();
    if(ret == false){
        return false ;
    }
    
    var param = jQuery('#myform').serialize() ;
    var url = '/Admin/index.php/Carservice/Carserviceorder/fit_export?'+param ;
    window.location.href= url ;  
});

function  check(){
//    var  start_time = jQuery('input[name=start_time]').val();
//    var  end_time = jQuery('input[name=end_time]').val();
//    var  status = jQuery("#status option:checked").val();
//    var  city_id = jQuery("#city_id option:checked").val();
//    var  time_type = jQuery("#time_type option:checked").val();
//     
//    if(start_time==''){
//        alert('请选择开始时间!');
//        return false ;
//    }
//    if(end_time==''){
//        alert('请选择结束时间!');
//        return false ;
//    }
//    if(status=='all'){
//        alert('请选择订单状态!不可为全部!');
//        return false ;
//    }
//    if(city_id=='all'){
//        alert('请选择城市!不可为全部!');
//        return false ;
//    }
//    if(time_type=='create_time'){
//        alert('请选择时间纬度为上门时间!');
//        return false ;
//    }
    var  checkArr = new Array();
    checkArr[0]='start_time|'+''+'|请选择开始时间!';
    checkArr[1]='end_time|'+''+'|请选择结束时间!';
    for(var i=0;i<checkArr.length;i++){
        var valArr = checkArr[i].split("|"); //字符分割 
        var  eleval = jQuery('input[name='+valArr[0]+']').val(); 
        
        if(eleval == valArr[1]){
            alert(valArr[2]);
            return false ;
        }
    }

}

</script>
<!-- 列表显示区域结束 -->
</div>
<!-- 主体内容结束 -->
</div>
<!-- 主页面结束 -->