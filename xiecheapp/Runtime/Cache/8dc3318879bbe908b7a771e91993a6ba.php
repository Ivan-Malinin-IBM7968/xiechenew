<?php if (!defined('THINK_PATH')) exit(); if($Think['const'].TOP_CSS == 'xc'): ?><!DOCTYPE html ><html ><head><meta charset="utf-8" /><!-- Google Web Master Tracking Start--><meta name="google-site-verification" content="8wmX5B2WFkCUepvfui4OFCwlvesXyiuy6tllvITgB28" /><!-- Google Web Master Tracking End--><meta name="baidu-site-verification" content="d6mO5rDwhc" /><!-- for com.cn --><meta name="baidu-site-verification" content="Vn7wCqQ1FE" /><!-- for net --><title><?php if(!empty($title)): echo ($title); else: ?>携车网,汽车保养维修预约,4S店折扣优惠,事故车维修预约<?php endif; ?></title><?php if(empty($meta_keyword)): ?><meta content="汽车保养,汽车维修,携车网,4S店预约保养,事故车维修" name="keywords"><?php else: ?><meta content="<?php echo ($meta_keyword); ?>" name="keywords"><?php endif; if(empty($description)): ?><meta content="汽车保养维修,事故车维修-首选携车网,全国唯一4S店汽车保养维修折扣的网站,最低5折起,还有更多团购套餐任您选,事故车维修预约,不花钱,还有返利拿4006602822"  name="description"><?php else: ?><meta content="<?php echo ($description); ?>" name="description"><?php endif; ?><script type="text/javascript" src="__PUBLIC__/Js/DatePicker/WdatePicker.js?v=<?php echo (C("VERSION_DATE")); ?>"></script><!--加载时间控件插件--><script type="text/javascript" src="__PUBLIC__/Js/com.js?v=<?php echo (C("VERSION_DATE")); ?>"></script><link rel="shortcut icon" href="__PUBLIC__/new_2/images/favicon.ico" type="image/x-icon"><link rel="stylesheet" type="text/css" href="__PUBLIC__/new_2/css/common.css?v=<?php echo (C("VERSION_DATE")); ?>" /><script src="__PUBLIC__/new_2/js/jquery.min.js" ></script><script src="__PUBLIC__/new_2/js/jquery-ui-1.9.2.custom.min.js" ></script><link rel="stylesheet" href="__PUBLIC__/new_2/css/redmond/jquery-ui-1.9.2.custom.css"><!-- ! GA track code start --><script src="__PUBLIC__/new_2/js/ga.js" ></script><!-- ! GA track code end --><?php if(!empty($canonical)): ?><link rel="canonical" href="<?php echo ($canonical); ?>"/><?php endif; ?></head><body ><!-- 头部Nav开始 --><div class='top-container' style="padding: 20px 0px; height: 90px;" ><div style="float: left;width:513px;margin-top:10px;position:relative" id='logo'><a href="http://www.xieche.com.cn" title='携车网,汽车保养维修预约,4S店折扣优惠,事故车维修预约' style="display:block;position:absolute;left:0px;top:0px;width:188px;height:90px">携车网</a><!-- <a href="javascript:void(0)" style="display:block;position:absolute;left:189px;top:0px;width:167px;height:90px" title="携车网,中国平安旗下投资企业">&nbsp;</a> --></div><div class="ad banner" style="margin:10px auto; width: 687px; height: 80px; float: left"><a href="http://www.xieche.com.cn/coupon-explosion" title="携车网 养车更养眼" target="_blank"><img src="__PUBLIC__/new_2/images/ac_explosion.jpg" alt="携车网 养车更养眼" width="687" height="80"></a></div></div><div class="nav-container"><div class="nav"><ul class='main-nav'><li style="width:1px;border-left:none;"></li><li><a href="__APP__/index.php" title="汽车保养,汽车维修,携车网,4S店预约保养,事故车维修,养车3折团">首页</a></li><li <?php if($current == 'order' ): ?>class="current"<?php endif; ?>><a href="__APP__/order">4S店售后预约</a></li><li <?php if($current == 'coupon' ): ?>class="current"<?php endif; ?>><a href="__APP__/coupon">4S店售后团购</a></li><!-- <li <?php if($current == 'noticelist' ): ?>class="current"<?php endif; ?>><a href="__APP__/noticelist">优惠速递</a></li> --><li <?php if($current == 'articlelist' ): ?>class="current"<?php endif; ?>><a href="__APP__/articlelist">用车心得</a></li><li <?php if($current == 'shiguche' ): ?>class="current"<?php endif; ?>><a href="__APP__/shiguche" target="_blank">事故车维修</a></li><li <?php if($current == 'yangchetuan' ): ?>class="current"<?php endif; ?>><a href="__APP__/coupon-explosion" target="_blank">养车3折团</a><span class="new"></span></li><li <?php if($current == 'carservice' ): ?>class="current"<?php endif; ?>><a href="__APP__/carservice" target="_blank">府上养车</a><span class="new"></span></li></ul><div class="login-box"><?php if(!empty($_SESSION['uid'])): ?><span >您好
				<?php if(isset($_SESSION['username'])): echo ($_SESSION['username']); else: if(isset($_SESSION['cardid'])): echo ($_SESSION['cardid']); else: echo ($_SESSION['mobile']); endif; endif; ?></span><?php else: ?><span class='not-user'><a href="__APP__/Public/login?jumpUrl=%2Fmyhome" rel="nofollow">登陆 / 注册</a><!-- <a href="<?php echo URL('/member/add',array()); echo C('HTML_URL_SUFFIX');?>" rel="nofollow"></a> --></span><?php endif; ?><span class='my-account'>我的账户</span><?php if(!empty($_SESSION['uid'])): ?><b class="dropdown-arrow-down"></b><div class="account-dropdown"><ul><li>我的订单</li><li class="sub"><a href="<?php echo URL('/myhome/index',array()); echo C('HTML_URL_SUFFIX');?>">未消费订单</a><a href="<?php echo URL('/myhome/index',array()); echo C('HTML_URL_SUFFIX');?>">未评价订单</a></li><li><a href="<?php echo URL('/myhome/myorder',array()); echo C('HTML_URL_SUFFIX');?>">我的维修保养订单</a></li><li>资产中心</li><li><a href="<?php echo URL('/myhome/mycoupon1',array()); echo C('HTML_URL_SUFFIX');?>">我的现金券</a></li><li><a href="<?php echo URL('/myhome/mycoupon2',array()); echo C('HTML_URL_SUFFIX');?>">我的套餐券</a></li><li><a href="<?php echo URL('/myhome/my_salecoupon',array()); echo C('HTML_URL_SUFFIX');?>">我的抵用券</a></li><li><a href="<?php echo URL('/myhome/my_account',array()); echo C('HTML_URL_SUFFIX');?>">我的账户余额</a></li><li><a href="<?php echo URL('/myhome/mypoints',array()); echo C('HTML_URL_SUFFIX');?>">我的积分</a></li><li>个人中心</li><li><a href="<?php echo URL('/myhome/myinfo',array()); echo C('HTML_URL_SUFFIX');?>">我的信息</a></li><li><a href="<?php echo URL('/myhome/my_safe',array()); echo C('HTML_URL_SUFFIX');?>">账户安全</a></li><li><a href="<?php echo URL('/myhome/my_car',array()); echo C('HTML_URL_SUFFIX');?>">我的车辆</a></li><li><a href="<?php echo URL('/myhome/my_comment',array()); echo C('HTML_URL_SUFFIX');?>">我的点评</a></li><li class="log-out"><a href="<?php echo URL('/public/logout',array()); echo C('HTML_URL_SUFFIX');?>" target="_self">[退出]</a></li></ul></div><?php endif; ?></div></div></div><!-- 头部Nav结束 --><script src="__PUBLIC__/new_2/js/jquery.min.js" ></script><script src="__PUBLIC__/new_2/js/base.js" ></script><script type="text/javascript" src="__PUBLIC__/Js/car_select/car_data.js?v=<?php echo (C("VERSION_DATE")); ?>"></script><script type="text/javascript" src="__PUBLIC__/Js/car_select/normal.js?v=<?php echo (C("VERSION_DATE")); ?>"></script><script src="__PUBLIC__/think/jquery.think.js?v=<?php echo (C("VERSION_DATE")); ?>"></script><style>	.weixin{width: 1200px; height: 430px; background: url("http://statics.xieche.com.cn/new_2/images/order-complete-weixin/weixin-1.png") center 0px no-repeat; margin: 20px auto; border: 1px solid #cacaca;}
</style><link rel="stylesheet" type="text/css" href="__PUBLIC__/new_2/css/index.css?v=<?php echo (C("VERSION_DATE")); ?>" /><style>	.gy a{

	}
	.gy a:visited{
		color: #000000;
	}
	.gy a:hover{
		text-decoration: underline;
	}
</style><script>	function fleshVerify(){ 
	//重载验证码
	var timenow = new Date().getTime();
		$('#verifyImg').attr("src",'__APP__/common/verify/'+timenow);
	}

	function showid(id,name){
		$('#address').val(name);
		$('#shop_area').val(id);
		$('#address_Shop').hide();
		$('#ShopAddressDiv').hide();
	}

	function showid_1(id,name){
		$('#address_1').val(name);
		$('#shop_area_1').val(id);
		$('#address_Shop_1').hide();
		$('#ShopAddressDiv_1').hide();
	}

	function close_div(id){
		if(id == 1){
			$('#address_Shop').hide();
			$('#ShopAddressDiv').hide();
		}else{
			$('#ShopName_div').hide();
		}
	}

	function close_div_1(id){
		if(id == 1){
			$('#address_Shop_1').hide();
			$('#ShopAddressDiv_1').hide();
		}else{
			$('#ShopName_div_1').hide();
		}
	}
	
	function giveshopname(id,name){
		$('#text_shop_name').val(name);
		$('#shop_name').val(name);
		$('#ShopName_div').hide();
		
	}

	function giveshopname_1(id,name){
		$('#text_shop_name_1').val(name);
		$('#shop_name_1').val(name);
		$('#ShopName_div_1').hide();
		
	}
	function showadress(){
		if( this.value == '可选填')value=' ';
		$('#address_Shop').show();
		$('#ShopAddressDiv').show();
		$.ajax({
			type: "POST",
			url: "/index-AjaxInfo",
			cache: false,
			data: "type=1",
			success: function(data){
				
				var str = "<table width=100% border='0' align='center' cellpadding='1' cellspacing='13' style='text-align: center; '>";
				data = eval("(" + data + ")");
				var count = 1;
				for(a=0;a<data.length;a++){
					if(a%4==0){
						str+="<tr>";
					}
					str+="<td ><a href='###' onclick=showid("+(data[a]['id'])+",'"+(data[a]['region_name'])+"');><font style='color:#666666;'>"+data[a]['region_name']+"</font></a></td>";
					if(a!=0){
						if(count%4==0){
							str+="</tr>";
						}
					}
					count++;
				}
				str+="</table>";
				$('#ShopAddressDiv').html(str);

			}
		})
	}


	function showadress_1(){
		if( this.value == '可选填')value=' ';
		$('#address_Shop_1').show();
		$('#ShopAddressDiv_1').show();
		$.ajax({
			type: "POST",
			url: "/index-AjaxInfo",
			cache: false,
			data: "type=1",
			success: function(data){
				
				var str = "<table width=100% border='0' align='center' cellpadding='1' cellspacing='13' style='text-align: center; '>";
				data = eval("(" + data + ")");
				var count = 1;
				for(a=0;a<data.length;a++){
					if(a%4==0){
						str+="<tr>";
					}
					str+="<td ><a href='###' onclick=showid_1("+(data[a]['id'])+",'"+(data[a]['region_name'])+"');><font style='color:#666666;'>"+data[a]['region_name']+"</font></a></td>";
					if(a!=0){
						if(count%4==0){
							str+="</tr>";
						}
					}
					count++;
				}
				str+="</table>";
				$('#ShopAddressDiv_1').html(str);

			}
		})
	}
		
	function showshopname(){
		var shopname = $('#text_shop_name').val();
		shopname = shopname.trim();
		if(shopname){
		
			$.ajax({
				type: "POST",
				url: "/index/AjaxInfo",
				cache: false,
				data: "type=2&shopname="+shopname,
				success: function(data){
					
					var str = "<div style='TEXT-ALIGN: right;' style='height: 25px;'><a href='###' onclick='close_div(2);'>[关闭]</a>&nbsp;</div>";
					str+= "<table width=100% border='0' align='left' cellpadding='1' cellspacing='10'>";
					data = eval("(" + data + ")");
					if(data){
						for(a=0;a<data.length;a++){
						str+="<tr  align='left'><td ><a href='###' onclick=giveshopname("+(data[a]['id'])+",'"+(data[a]['shop_name'])+"');><font style='color:#666666;'>"+data[a]['shop_name']+"</font></a></td></tr>";
						}
						str+="</table>";
						$('#ShopName_div').show();
						$('#ShopName_div').html(str);
					}
					

				}
			})
		}
		
			
	}

	function showshopname_1(){
		var shopname = $('#text_shop_name_1').val();
		shopname = shopname.trim();
		if(shopname){
		
			$.ajax({
				type: "POST",
				url: "/index/AjaxInfo",
				cache: false,
				data: "type=2&shopname="+shopname,
				success: function(data){
					var str = "<div style='TEXT-ALIGN: right;' style='height: 25px;'><a href='###' onclick='close_div_1(2);'>[关闭]</a>&nbsp;</div>";
					str+= "<table width=100% border='0' align='left' cellpadding='1' cellspacing='10'>";
					data = eval("(" + data + ")");
					if(data){
						for(a=0;a<data.length;a++){
						str+="<tr  align='left'><td ><a href='###' onclick=giveshopname_1("+(data[a]['id'])+",'"+(data[a]['shop_name'])+"');><font style='color:#666666;'>"+data[a]['shop_name']+"</font></a></td></tr>";
						}
						str+="</table>";
						$('#ShopName_div_1').show();
						$('#ShopName_div_1').html(str);
					}
					

				}
			})
		}
		
			
	}

	</script><!--index row 1 Start--><div class="w1200 index1"><div class="service" id=""><ul id="tabs1" class="tabs"><li class="current ss">搜索4S店铺维修保养</li><li class="mine">车型快速入口</li><li class="quick"><?php if(!empty($_SESSION['uid'])): ?>我的车辆<?php else: ?>登录快速入口<?php endif; ?></li></ul><div id="con1" class="con"><div style="display: none;" class='current'><form class="search"  name="form2" method="POST" action="__APP__/order"><ul><li id="pinpai"><label>选择品牌:</label><select id="get_brand" name="brand_id"  onchange="comp_brlist('get_brand','get_series', 'get_model');"></select></li><li id="chexing"><label>选择车系:</label><select id="get_series" name="series_id"  disabled onchange="comp_yearlist('get_brand', 'get_series', 'get_model');"></select><label style="margin-left:10px;">选择车型:</label><select class="car_select" id="get_model" name="model_id"  disabled></select></li><li id="diqu"><label>所在地区:</label><input type="text" id="address" name="address" class="input-text" onclick="showadress();" onblur=" if( this.value == '可选填')value=' '" onblur=" if( this.value.replace(/[ ]/g,'') == '')value='可选填'" value="可选填"><div id="address_Shop" style="position: absolute; z-index:1; left: 106px; top: 112px; width: 358px; height: 30px; border:#CCCCCC solid 1px; background-color:white; display:none  ">可直接选择地理位置
										<a href="#" style="color:#0066cc; float:right; display: inline-block; width: 50px; height: 30px; line-height: 30px;"  onclick="close_div(1);">[关闭]</a><br class="clear"></div><div id="ShopAddressDiv" style="position:absolute;z-index:1;left: 106px; top: 143px; width: 358px; height: 160px; border:#CCCCCC solid 1px;background-color:white;display:none;"></div></li><li id="guanjianzi"><label>4S店铺名:</label><input type="text" id="text_shop_name" name="shop_name" class="input-text" onkeyup="showshopname();" onclick=" if( this.value == '可选填')value=''" onblur=" if( this.value.replace(/[ ]/g,'') == '')value='可选填'" value="可选填" style="height:25px;"><div id="ShopName_div" style="position:absolute;z-index:1;left:123px;top:178px;width:388px;height:218px;border:#CCCCCC solid 1px;background-color:white;display:none;overflow-y:auto;"></li></ul><input type="submit" value="提 交 搜 索" class="submit-btn" ></form></div><!-- 车型快速入口 --><div style="display: none; overflow: hidden;" class='quick-access'><dl><dt>小型车</dt><dd><a href="<?php echo URL('/order/index',array('brand_id'=>17,'series_id'=>153,'model_id'=>408)); echo C('HTML_URL_SUFFIX');?>" target="_blank">新Polo</a></dd><dd><a href="<?php echo URL('/order/index',array('brand_id'=>63,'series_id'=>439,'model_id'=>1186)); echo C('HTML_URL_SUFFIX');?>" target="_blank">晶锐</a></dd><dd><a href="<?php echo URL('/order/index',array('brand_id'=>54,'series_id'=>377,'model_id'=>1028)); echo C('HTML_URL_SUFFIX');?>" target="_blank">K2</a></dd><dd><a href="<?php echo URL('/order/index',array('brand_id'=>25,'series_id'=>231,'model_id'=>631)); echo C('HTML_URL_SUFFIX');?>" target="_blank">嘉年华</a></dd><dd><a href="<?php echo URL('/order/index',array('brand_id'=>68,'series_id'=>471,'model_id'=>1278)); echo C('HTML_URL_SUFFIX');?>" target="_blank">爱唯欧</a></dd><dd><a href="<?php echo URL('/order/index',array('brand_id'=>8,'series_id'=>83,'model_id'=>227)); echo C('HTML_URL_SUFFIX');?>" target="_blank">飞度</a></dd><br class="clear"></dl><dl><dt>紧凑型车</dt><dd><a href="<?php echo URL('/order/index',array('brand_id'=>17,'series_id'=>156,'model_id'=>418)); echo C('HTML_URL_SUFFIX');?>" target="_blank">朗逸</a></dd><dd><a href="<?php echo URL('/order/index',array('brand_id'=>17,'series_id'=>164,'model_id'=>449)); echo C('HTML_URL_SUFFIX');?>" target="_blank">高尔夫</a></dd><dd><a href="<?php echo URL('/order/index',array('brand_id'=>63,'series_id'=>440,'model_id'=>1188)); echo C('HTML_URL_SUFFIX');?>" target="_blank">明锐</a></dd><dd><a href="<?php echo URL('/order/index',array('brand_id'=>11,'series_id'=>118,'model_id'=>315)); echo C('HTML_URL_SUFFIX');?>" target="_blank">凯越</a></dd><dd><a href="<?php echo URL('/order/index',array('brand_id'=>56,'series_id'=>407,'model_id'=>1099)); echo C('HTML_URL_SUFFIX');?>" target="_blank">荣威550</a></dd><dd><a href="<?php echo URL('/order/index',array('brand_id'=>25,'series_id'=>229,'model_id'=>617)); echo C('HTML_URL_SUFFIX');?>" target="_blank">新福克斯</a></dd><dd><a href="<?php echo URL('/order/index',array('brand_id'=>10,'series_id'=>102,'model_id'=>278)); echo C('HTML_URL_SUFFIX');?>" target="_blank">307</a></dd><dd><a href="<?php echo URL('/order/index',array('brand_id'=>68,'series_id'=>473,'model_id'=>1286)); echo C('HTML_URL_SUFFIX');?>" target="_blank">科鲁兹</a></dd><dd><a href="<?php echo URL('/order/index',array('brand_id'=>17,'series_id'=>170,'model_id'=>473)); echo C('HTML_URL_SUFFIX');?>" target="_blank">速腾</a></dd><dd><a href="<?php echo URL('/order/index',array('brand_id'=>23,'series_id'=>205,'model_id'=>555)); echo C('HTML_URL_SUFFIX');?>" target="_blank">卡罗拉</a></dd><br class="clear"></dl><dl><dt>中级车</dt><dd><a href="<?php echo URL('/order/index',array('brand_id'=>17,'series_id'=>152,'model_id'=>400)); echo C('HTML_URL_SUFFIX');?>" target="_blank">新帕萨特</a></dd><dd><a href="<?php echo URL('/order/index',array('brand_id'=>11,'series_id'=>114,'model_id'=>303)); echo C('HTML_URL_SUFFIX');?>" target="_blank">君威</a></dd><dd><a href="<?php echo URL('/order/index',array('brand_id'=>63,'series_id'=>438,'model_id'=>1179)); echo C('HTML_URL_SUFFIX');?>" target="_blank">昊锐</a></dd><dd><a href="<?php echo URL('/order/index',array('brand_id'=>25,'series_id'=>233,'model_id'=>1403)); echo C('HTML_URL_SUFFIX');?>" target="_blank">蒙迪欧致胜</a></dd><dd><a href="<?php echo URL('/order/index',array('brand_id'=>55,'series_id'=>397,'model_id'=>1078)); echo C('HTML_URL_SUFFIX');?>" target="_blank">天籁</a></dd><br class="clear"></dl></div><!-- 我的车型 --><div style="display: none;" class='my-cars'><?php if(!empty($_SESSION['uid'])): ?><form class="search"  name="form3" method="POST" action="__APP__/order"><ul id=""><li id="wc"><label>我的车型:</label><ul class="wclist"><?php if(is_array($list_membercar)): $k = 0; $__LIST__ = $list_membercar;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($k % 2 );++$k;?><li><input type="radio" name="u_c_id" value="<?php echo ($vo["u_c_id"]); ?>" <?php if($vo["u_c_id"] == $u_c_id): ?>checked<?php endif; ?>/><?php echo ($vo["car_name"]); ?>&nbsp;<?php echo ($vo["brand_name"]); ?>-<?php echo ($vo["series_name"]); ?>-<?php echo ($vo["model_name"]); ?></li><?php endforeach; endif; else: echo "" ;endif; ?><div class='clear'></div></ul></li><li id="guanjianzi"><label>4S店铺名:</label><input type="text" id="text_shop_name_1" name="shop_name_1" class="input-text" onkeyup="showshopname_1();" onclick=" if( this.value == '可选填')value=''" onblur=" if( this.value.replace(/[ ]/g,'') == '')value='可选填'" value="可选填" style="height:25px;"><div id="ShopName_div_1" style="position:absolute;z-index:1;left:123px;top:178px;width:388px;height:218px;border:#CCCCCC solid 1px;background-color:white;display:none;"></li><div class='submit'><input type="submit" value="提 交 搜 索" class="submit-btn" style="float:right"><br class="clear"></div></ul></form><?php else: ?><form class="search"  name="form4" method="POST" action="__APP__/public/logincheck"><ul id="my-cars" ><li id="pinpai" style="display: none;"><label>选择品牌:</label><select id="get_brand1" name="brand_id1"  onchange="comp_brlist('get_brand1','get_series1', 'get_model1');"></select></li><li id="diqu"><label>用户名：</label><input type="text" id="username" name="username" class='input-text'></li><li id="guanjianzi"><label>密 码:</label><input class='input-text' type="password" name="password" type="password" ></li><li id="guanjianzi"><label>验证码:</label><input type="text" id='reg-code' check="Require" warning="请输入验证码" name="verify" /><img  height='27'  style='float:left; margin-top: 6px; margin-left: 5px; height: 27px; cursor:pointer' id="verifyImg" SRC="__APP__/common/verify/" onClick="fleshVerify()" BORDER="0" ALT="点击刷新验证码" align="absmiddle" /></li><div class='submit'><input type="submit"  value="快 速 登 录" class="submit-btn" style="position: absolute; bottom: 40px; right: 68px; }"><br class="clear"></div></ul></form><?php endif; ?></div></div><div class="clear"></div></div><style type="text/css">				/* focus */
				#focus{width:580px;height:300px;overflow:hidden;position:relative;float:right;margin:0 auto;}
				#focus ul{height:380px;position:absolute;}
				#focus ul li{float:left;width:580px;height:300px;overflow:hidden;position:relative;background:#000;}
				#focus ul li div{position:absolute;overflow:hidden;}
				#focus .btnBg{position:absolute;width:580px;height:20px;left:0;bottom:0;background:#000;}
				#focus .btn{position:absolute;width:580px;height:10px;padding:5px 10px;right:0;bottom:0;text-align:right;}
				#focus .btn span{display:inline-block;_display:inline;_zoom:1;width:25px;height:10px;_font-size:0;margin-left:5px;cursor:pointer;background:#fff;}
				#focus .btn span.on{background:#fff;}
			</style><div class="slideshow" id="focus"><ul id="innerSlide"><li><a href="http://www.xieche.com.cn/carservice" target="_blank"><img width="580" height="300" alt="" src="__PUBLIC__/new/images/banner/womencall.jpg"/></a></li><li><a href="http://www.xieche.com.cn/coupon/371" target="_blank"><img width="580" height="300" alt="" src="__PUBLIC__/new/images/banner/a/e20140508.jpg"/></a></li><!-- <li><a href="http://www.xieche.com.cn/shop/1367" target="_blank"><img width="580" height="300" alt="" src="__PUBLIC__/new/images/banner/a/e20140501.jpg"/></a></li> --><!--<li><a href="http://event.xieche.com.cn/20140501/" target="_blank"><img width="580" height="300" alt="" src="__PUBLIC__/new/images/banner/a/e20140501-2.jpg"/></a></li>--><!-- <li><a href="http://www.xieche.com.cn/shop/287" target="_blank"><img width="580" height="300" alt="上海万兴-别克4S店汽车保养维修_携车网" src="__PUBLIC__/new/images/banner/banner-2.jpg"/></a></li> --><li><a href="http://www.xieche.com.cn/coupon/75" target="_blank"><img width="580" height="300" alt="上海绿地徐通-雪佛兰4S店-上海绿地徐通雪佛兰4S店1000元现金券900.00现金券-携车网" src="__PUBLIC__/new/images/banner/banner-3.png"/></a></li><li><a href="http://www.xieche.com.cn/shop/87" target="_blank"><img width="580" height="300" alt="上海绿地徐汇-别克4S店汽车保养维修_携车网" src="__PUBLIC__/new/images/banner/banner-4.png"/></a></li><li><a href="http://www.xieche.com.cn/carservice" target="_blank"><img width="580" height="300" alt="" src="__PUBLIC__/new/images/banner/mancall.jpg"/></a></li><!-- <li><a href="http://event.xieche.com.cn/20140402/" target="_blank"><img width="580" height="300" alt="南京分站招商-携车网" src="__PUBLIC__/new/images/banner/banner-6.jpg"/></a></li> --></ul></div><div class="clear"></div></div><!-- 通栏广告位 1 START --><!-- <div class="w1200 content banner-container"><div class="index-banner-1"><a href="http://www.xieche.com.cn/cattle" target="_blank" ><img src="http://statics.xieche.net/image/event/20140516/index-banner/20140516-banner.png" alt="携车网-养车惠分享，好事荐成双" 、></a></div></div> --><!-- 通栏广告位 1 END --><!--index row 1 ends --><div class="w1200 content index2"><div class="w830 content-left"><!--sales 1 start--><div class="w830 sale1"><div class="cate-title"><h3>现金券</h3></div><ul class='w830 filter' id="tabs2"><li  class="current"><a href="<?php echo URL('/coupon/index',array('coupon_type'=>1)); echo C('HTML_URL_SUFFIX');?>">全部</a></li><?php if(is_array($data_Pcoupon["Coupon"])): $i = 0; $__LIST__ = $data_Pcoupon["Coupon"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$voc1): $mod = ($i % 2 );++$i;?><li><a href="<?php echo URL('/coupon/index',array('coupon_type'=>1,'brand_id'=>$voc1['brand_id'])); echo C('HTML_URL_SUFFIX');?>" title="<?php echo ($voc1["brand_name"]); ?>现金券"><?php echo ($voc1["brand_name"]); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?></ul><div id="con2"><!--全部内容开始--><div><ul class="w830 sales-con"><?php if(is_array($data_AllPcoupon)): $i = 0; $__LIST__ = $data_AllPcoupon;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$arr): $mod = ($i % 2 );++$i;?><li ><span class="tuijian-tag"></span><div class="img-con"><a href="__APP__/coupon/<?php echo ($arr["id"]); ?>" target="_blank"><img src="/UPLOADS/Coupon/Logo/<?php echo ($arr["coupon_pic"]); ?>" width="253" height="190" alt="<?php echo ($arr["coupon_name"]); ?>" title="<?php echo ($arr["coupon_name"]); ?>"></a><span class="address"><strong href="#"><?php echo ($arr["shop_address"]); ?></strong></span></div><div class="content"><a href="__APP__/coupon/<?php echo ($arr["id"]); ?>" target="_blank"><h5><?php echo ($arr["coupon_name"]); ?></h5></a><span><?php echo ($arr["shop_name"]); ?><a href="__APP__/shop/<?php echo ($arr["shop_id"]); ?>" target="_blank">[查看店铺]</a></span><div class="price"><strong class="orange">￥<?php echo ($arr["coupon_amount"]); ?>元</strong><em> / <?php echo ($arr["cost_price"]); ?>元</em></div><div class="bought"><strong class="orange"><?php echo ($arr["pay_count"]); ?></strong><em>人已购</em></div></div></li><?php endforeach; endif; else: echo "" ;endif; ?><div class="clear"></div></ul></div><!--全部内容结算--><?php if(is_array($data_Pcoupon["Coupon"])): $i = 0; $__LIST__ = $data_Pcoupon["Coupon"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$voc1): $mod = ($i % 2 );++$i;?><div><ul class="w830 sales-con"><?php if(is_array($voc1["Pcoupon"])): $i = 0; $__LIST__ = $voc1["Pcoupon"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$arr): $mod = ($i % 2 );++$i;?><li <?php if($arr['is_tuijian'] == '1'): ?>class="tuijian-tag-con"<?php endif; ?>><span class="tuijian-tag"></span><div class="img-con"><a href="__APP__/coupon/<?php echo ($arr["id"]); ?>" target="_blank"><img src="/UPLOADS/Coupon/Logo/<?php echo ($arr["coupon_pic"]); ?>" width="253" height="190" alt="<?php echo ($arr["coupon_name"]); ?>" title="<?php echo ($arr["coupon_name"]); ?>"></a><span class="address"><strong href="#"><?php echo ($arr["shop_address"]); ?></strong></span></div><div class="content"><a href="__APP__/coupon/<?php echo ($arr["id"]); ?>" target="_blank"><h5><?php echo ($arr["coupon_name"]); ?></h5></a><span><?php echo ($arr["shop_name"]); ?><a href="__APP__/shop/<?php echo ($arr["shop_id"]); ?>" target="_blank">[查看店铺]</a></span><div class="price"><strong class="orange">￥<?php echo ($arr["coupon_amount"]); ?>元</strong><em> / <?php echo ($arr["cost_price"]); ?>元</em></div><div class="bought"><strong class="orange"><?php echo ($arr["pay_count"]); ?></strong><em>人已购</em></div></div></li><?php endforeach; endif; else: echo "" ;endif; ?><div class="clear"></div></ul></div><?php endforeach; endif; else: echo "" ;endif; ?></div><div class="clear"></div></div><!--sale1 ends--><div class="ad banner" style="margin:20px auto; width: 850px; height: 80px; clear: both;"><a href="http://www.xieche.com.cn/shiguche" title="携车网 事故车" target="_blank"><img src="http://statics.xieche.com.cn/ad-banner/830/ac-car.jpg" alt="携车网 事故车" width="830" height="80"></a></div><!--sale2 start--><div class="w830 sale2"><div class="cate-title"><h3>套餐券</h3></div><ul class='w830 filter' id="tabs3"><li  class="current"><a href="<?php echo URL('/coupon/index',array('coupon_type'=>2)); echo C('HTML_URL_SUFFIX');?>">全部</a></li><?php if(is_array($data_Gcoupon["Coupon"])): $i = 0; $__LIST__ = $data_Gcoupon["Coupon"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$voc1): $mod = ($i % 2 );++$i;?><li><a href="<?php echo URL('/coupon/index',array('coupon_type'=>2,'brand_id'=>$voc1['brand_id'])); echo C('HTML_URL_SUFFIX');?>" title="<?php echo ($voc1["brand_name"]); ?>套餐券"><?php echo ($voc1["brand_name"]); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?></ul><div id="con3"><!--全部内容开始--><div><ul class="w830 sales-con" ><?php if(is_array($data_AllGcoupon)): $i = 0; $__LIST__ = $data_AllGcoupon;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$arr): $mod = ($i % 2 );++$i;?><li><span class="tuijian-tag"></span><div class="img-con"><a href="__APP__/coupon/<?php echo ($arr["id"]); ?>" target="_blank"><img src="/UPLOADS/Coupon/Logo/<?php echo ($arr["coupon_pic"]); ?>" width="105" height="80" alt="<?php echo ($arr["coupon_name"]); ?>" title="<?php echo ($arr["coupon_name"]); ?>"></a></div><div class="content"><a href="__APP__/coupon/<?php echo ($arr["id"]); ?>" target="_blank"><h5><?php echo ($arr["coupon_name"]); ?></h5></a><span><?php echo ($arr["shop_name"]); ?></span><span class="address"><?php echo ($arr["shop_address"]); ?><a href="__APP__/shop/<?php echo ($arr["shop_id"]); ?>" target="_blank">[查看店铺]</a></span><div class="price"><strong class="orange">￥<?php echo ($arr["coupon_amount"]); ?>元</strong><em> / <?php echo ($arr["cost_price"]); ?>元</em></div><div class="bought"><strong class="orange"><?php echo ($arr["pay_count"]); ?></strong><em>人已购</em></div></div></li><?php endforeach; endif; else: echo "" ;endif; ?><div class="clear"></div></ul></div><!--全部内容结束--><?php if(is_array($data_Gcoupon["Coupon"])): $i = 0; $__LIST__ = $data_Gcoupon["Coupon"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$voc1): $mod = ($i % 2 );++$i;?><div><ul class="w830 sales-con" ><?php if(is_array($voc1["Gcoupon"])): $i = 0; $__LIST__ = $voc1["Gcoupon"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$arr): $mod = ($i % 2 );++$i;?><li <?php if($arr['is_tuijian'] == '1'): ?>class="tuijian-tag-con"<?php endif; ?>><span class="speacial-banner"></span><div class="img-con"><a href="__APP__/coupon/<?php echo ($arr["id"]); ?>" target="_blank"><img src="/UPLOADS/Coupon/Logo/<?php echo ($arr["coupon_pic"]); ?>" width="105" height="80" alt="<?php echo ($arr["coupon_name"]); ?>" title="<?php echo ($arr["coupon_name"]); ?>"></a></div><div class="content"><a href="__APP__/coupon/<?php echo ($arr["id"]); ?>" target="_blank"><h5><?php echo ($arr["coupon_name"]); ?></h5></a><span><?php echo ($arr["shop_name"]); ?></span><span class="address"><?php echo ($arr["shop_address"]); ?><a href="__APP__/shop/<?php echo ($arr["shop_id"]); ?>" target="_blank">[查看店铺]</a></span><div class="price"><strong class="orange">￥<?php echo ($arr["coupon_amount"]); ?>元</strong><em> / <?php echo ($arr["cost_price"]); ?>元</em></div><div class="bought"><strong class="orange"><?php echo ($arr["pay_count"]); ?></strong><em>人已购</em></div></div></li><?php endforeach; endif; else: echo "" ;endif; ?><div class="clear"></div></ul></div><?php endforeach; endif; else: echo "" ;endif; ?></div><div class="clear"></div></div><!--shops ends--><!--shop starts--><div class="w830 hot-shop"><div class="cate-title"><h3>热门店铺
						<!-- <div class='gy' style='float:right; font-weight: normal' ><a href='/order'>共有<span  style='color: #009DD7'><?php echo ($shop1count); ?></span>家特约4S店,<span  style='color: #009DD7'><?php echo ($shop2count); ?></span>家非特约4S店.</a></div> --></h3></div><ul class='w830 filter' id="tabs4"><li  class="current"><a href="__APP__/order">全部</a></li><?php if(is_array($fs_shoplist)): $i = 0; $__LIST__ = $fs_shoplist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$fs_vo): $mod = ($i % 2 );++$i;?><li><a href="<?php echo URL('/order/index',array('fsid'=>$fs_vo[fsid])); echo C('HTML_URL_SUFFIX');?>" title="<?php echo ($fs_vo["fs_name"]); ?>"><?php echo ($fs_vo["fs_name"]); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?></ul><div id='con4'><div><ul class="w830 sales-con" ><?php if(is_array($shoplist)): $i = 0; $__LIST__ = $shoplist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$slist): $mod = ($i % 2 );++$i;?><li><div class="content"><a href="__APP__/shop/<?php echo ($slist["id"]); ?>" target="_blank"><h5><?php echo ($slist["shop_name"]); ?></h5></a><span class="shop-address"><?php echo ($slist["shop_address"]); ?><a href="__APP__/shop/<?php echo ($slist["id"]); ?>" target="_blank">[查看店铺]</a></span><div class="comment-rate"><a href="__APP__/shop/<?php echo ($slist["id"]); ?>#comment"><?php echo ($slist["comment_number"]); ?></a>人评论</div><div class="shop-rating"><span <?php echo "class='rating-".round($slist[comment_rate]/10)." rating-img'"; ?>></span><strong><?php echo ($slist[comment_rate]/10); ?></strong></div><div class="clear"></div><div class="price">工时折扣: <strong class="orange"><?php echo ($slist["workhours_sale"]); ?>折起</strong></div></div><div class="img-con"><div class='shop-img'><a href="__APP__/shop/<?php echo ($slist["id"]); ?>" target="_blank"><img src="<?php echo ($slist["shop_pic"]); ?>" width="105" height="80" alt="<?php echo ($slist["shop_name"]); ?>" title="<?php echo ($slist["shop_name"]); ?>"></a><span class="speacial-banner"></span></div><div class='clear'></div></div></li><?php endforeach; endif; else: echo "" ;endif; ?><div class="clear"></div></ul></div><?php if(is_array($fs_shoplist)): $i = 0; $__LIST__ = $fs_shoplist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$fs_shop_vo): $mod = ($i % 2 );++$i;?><div><ul class="w830 sales-con" ><?php if(is_array($fs_shop_vo["shoplist"])): $i = 0; $__LIST__ = $fs_shop_vo["shoplist"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$slist): $mod = ($i % 2 );++$i;?><li><div class="content"><a href="__APP__/shop/<?php echo ($slist["id"]); ?>" target="_blank"><h5><?php echo ($slist["shop_name"]); ?></h5></a><span class="shop-address"><?php echo ($slist["shop_address"]); ?><a href="__APP__/shop/<?php echo ($slist["id"]); ?>" target="_blank">[查看店铺]</a></span><div class="comment-rate"><a href="__APP__/shop/<?php echo ($slist["id"]); ?>#comment"><?php echo ($slist["comment_number"]); ?></a>人评论</div><div class="shop-rating"><span <?php echo "class='rating-".round($slist[comment_rate]/10)." rating-img'"; ?>></span><strong><?php echo ($slist[comment_rate]/10); ?></strong></div><div class="clear"></div><div class="price">工时折扣: <strong class="orange"><?php echo ($slist["workhours_sale"]); ?>折起</strong></div></div><div class="img-con"><div class='shop-img'><a href="__APP__/shop/<?php echo ($slist["id"]); ?>" target="_blank"><img src="<?php echo ($slist["shop_pic"]); ?>" width="105" height="80" alt="<?php echo ($slist["shop_name"]); ?>" title="<?php echo ($slist["shop_name"]); ?>"></a><span class="speacial-banner"></span></div><div class='clear'></div></div></li><?php endforeach; endif; else: echo "" ;endif; ?><div class="clear"></div></ul></div><?php endforeach; endif; else: echo "" ;endif; ?></div><div class="clear"></div></div><!--shops ends--></div><div class="w350 content-right"><div class="tuijian"><h3>话谈府上养车</h3><!--<ul><li><div class='image'><img src="http://www.xieche.com.cn/UPLOADS/Coupon/Logo/51a6f28d2385c.jpg" width='80' height="60" ></div><div class='info'><strong class="title"> 上海泰宸上海大众4S店500元现金券</strong></div></li><div class="clear"></div></ul>--><!-- <ul style="font-size: 13px;"><?php if(is_array($shopnotice)): $i = 0; $__LIST__ = $shopnotice;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$snvo): $mod = ($i % 2 );++$i;?><li><a target="_blank" href="<?php echo URL('/shop/favtype',array('shop_id'=>$snvo['shop_id'],'id'=>$snvo['id'])); echo C('HTML_URL_SUFFIX');?>" title="<?php echo ($snvo["noticetitle"]); ?>"> &bull; <?php echo (g_substr($snvo["noticetitle"],45)); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?></ul> --><ul style="font-size: 13px;"><?php if(is_array($shopnotice)): $i = 0; $__LIST__ = $shopnotice;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$snvo): $mod = ($i % 2 );++$i;?><li><a target="_blank" href="__APP__/article/<?php echo ($snvo["id"]); ?>" title="<?php echo ($snvo["title"]); ?>">&bull; <?php echo (g_substr($snvo["title"],45)); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?></ul></div><div class="tuijian"><h3>用车心得</h3><!--<ul><li><div class='image'><img src="http://www.xieche.com.cn/UPLOADS/Coupon/Logo/51a6f28d2385c.jpg" width='80' height="60" ></div><div class='info'><strong class="title">    上海泰宸上海大众4S店500元现金券</strong></div></li><div class="clear"></div></ul--><ul style="font-size: 13px;"><?php if(is_array($articlelist)): $i = 0; $__LIST__ = $articlelist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$artvo): $mod = ($i % 2 );++$i;?><li><a target="_blank" href="__APP__/article/<?php echo ($artvo["id"]); ?>" title="<?php echo ($artvo["title"]); ?>">&bull; <?php echo (g_substr($artvo["title"],45)); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?></ul></div><div class='w350 app-promote'><a href="__APP__/Application" title='携车网APP'><img src="__APP__/Public/new_2/images/app.jpg" alt="携车网APP" width='350' height='190' /></a></div><div class="weibo" style='margin-top: 20px; '><iframe width="350" height="493" class="share_self"  frameborder="0" scrolling="no" src="http://widget.weibo.com/weiboshow/index.php?language=&width=350&height=493&fansRow=1&ptype=1&speed=300&skin=1&isTitle=1&noborder=1&isWeibo=1&isFans=1&uid=2463073343&verifier=ff61d30e&dpc=1"></iframe></div></div><div class="clear"></div></div><script type='text/javascript'>			comp_fctlist("get_brand", "get_series", "get_model");
			/*
			function new_slider(slidercon, mainspeed, inspeed, outspeed) {
			    this.slidercon = slidercon;
			    this.mainspeed = mainspeed;
			    this.inspeed = inspeed;
			    this.outspeed = outspeed;
			    var i = 1;
			    var listcon = '<li class="current">' + 1 + '</li>';
			    
			    $(slidercon).append('<ul id="slider_counter"></ul>');
			    for (var j = 2; j <= $(slidercon).children("ul:first").children("li").length; j++) {
			        listcon = listcon + "<li>" + j + "</li>";
			    };
			    $(slidercon).children("#slider_counter").html(listcon);
			    $(slidercon).children("#slider_counter").children("li").mouseover(function() {
			        var index = $.inArray(this, $(slidercon).children("#slider_counter").children("li"));
			        $(slidercon).children("#slider_counter").children("li").removeClass("current").eq(index).addClass("current");
			        $(slidercon).children("ul:first").children("li").fadeOut(outspeed).eq(index).fadeIn(inspeed);
			        $(slidercon).children("#slider_title").html($(slidercon).children("ul:first").children("li:eq(" + parseInt($(this).html() - 1) + ")").children("a").children("img").attr("title"));
			        i = $(this).index() + 1;
			        if (i == $(slidercon).children("ul:first").children("li").length) {
			            i = 0;
			        };
			    });

			    function init() {
			        $(slidercon).children("ul:first").children("li:not(" + i + ")").fadeOut(outspeed);
			        $(slidercon).children("ul:first").children("li:eq(" + i + ")").fadeIn(inspeed);
			        $(slidercon).children("#slider_counter").children("li:not(" + i + ")").removeClass("current");
			        $(slidercon).children("#slider_counter").children("li:eq(" + i + ")").addClass("current");
			        $(slidercon).children("#slider_title").html($(slidercon).children("ul:first").children("li:eq(" + i + ")").children("a").children("img").attr("title"));
			        i++;
			        if (i >= $(slidercon).children("ul:first").children("li").length) {
			            i = 0;
			        };
			    };
			    var move = setInterval(init, mainspeed);
			    $(slidercon).mouseover(function() {
			        clearInterval(move);
			    });
			    $(slidercon).mouseout(function() {
			        move = setInterval(init, mainspeed);
			    });
			};

			new_slider('#new_slider',2000,300,10);
			*/

			function hTab(tab_controler,tab_con){
			this.tab_controler = tab_controler;
			this.tab_con = tab_con;
			var tabs = $(tab_controler).children("li");
			var panels = $(tab_con).children("div");
			$(tab_con).children("div").css("display","none");
			$(tab_con).children("div:first").css("display","block");
			$(tabs).hover(function(){
				var index = $.inArray(this,tabs);
				tabs.removeClass("current")
				.eq(index).addClass("current");
				panels.css("display","none")
				.eq(index).css("display","block");
			});
		};

		hTab('#tabs1','#con1');
		hTab('#tabs2','#con2');
		hTab('#tabs3','#con3');
		hTab('#tabs4','#con4');
		

		$(function() {
			var sWidth = $("#focus").width(); //获取焦点图的宽度（显示面积）
			var len = $("#focus ul li").length; //获取焦点图个数
			var index = 0;
			var picTimer;
			
			//以下代码添加数字按钮和按钮后的半透明条，还有上一页、下一页两个按钮
			var btn = "<div class='btnBg'></div><div class='btn'>";
			for(var i=0; i < len; i++) {
				btn += "<span></span>";
			}
			btn += "</div>";
			$("#focus").append(btn);
			$("#focus .btnBg").css("opacity",0.5);

			//为小按钮添加鼠标滑入事件，以显示相应的内容
			$("#focus .btn span").css("opacity",0.4).mouseover(function() {
				index = $("#focus .btn span").index(this);
				showPics(index);
			}).eq(0).trigger("mouseover");

			//本例为左右滚动，即所有li元素都是在同一排向左浮动，所以这里需要计算出外围ul元素的宽度
			$("#focus ul").css("width",sWidth * (len));
			
			//鼠标滑上焦点图时停止自动播放，滑出时开始自动播放
			$("#focus").hover(function() {
				clearInterval(picTimer);
			},function() {
				picTimer = setInterval(function() {
					showPics(index);
					index++;
					if(index == len) {index = 0;}
				},4000); //此4000代表自动播放的间隔，单位：毫秒
			}).trigger("mouseleave");
			
			//显示图片函数，根据接收的index值显示相应的内容
			function showPics(index) { //普通切换
				var nowLeft = -index*sWidth; //根据index值计算ul元素的left值
				$("#focus ul").stop(true,false).animate({"left":nowLeft},500); //通过animate()调整ul元素滚动到计算出的position
				//$("#focus .btn span").removeClass("on").eq(index).addClass("on"); //为当前的按钮切换到选中的效果
				$("#focus .btn span").stop(true,false).animate({"opacity":"0.4"},500).eq(index).stop(true,false).animate({"opacity":"1"},500); //为当前的按钮切换到选中的效果
			}
		});
		</script><!-- <div class="weixin"></div> --><div class="hotlink"><h4>友情链接</h4><ul class="row"><li><a href="http://www.xieche.com.cn" target="_blank">上海汽车保养</a></li><li><a href="http://www.xieche.com.cn" target="_blank">上海上门保养</a></li><li><a href="http://www.xieche.com.cn" target="_blank">上门保养</a></li><li><a href="http://www.xieche.com.cn" target="_blank">汽车保养</a></li><li><a href="http://www.xieche.com.cn" target="_blank">汽车维修</a></li><li><a href="http://www.xieche.com.cn" target="_blank">4S店预约保养</a></li><li><a href="http://www.xieche.com.cn" target="_blank">事故车维修</a></li><li><a href="http://jining.273.cn" target="_blank">济宁二手车</a></li><li><a href="http://www.haerbinzuche.cn" target="_blank">哈尔滨租车公司</a></li><li><a href="http://www.auto008.com" target="_blank">汽车专家网</a></li><li><a href="http://www.zjgjcc.com/" target="_blank">湛江海田国际车城</a></li><li><a href="http://www.abkitty.com" target="_blank">大连旅游租车</a></li><li><a href="http://www.icip.com.cn" target="_blank">宁波二手车</a></li><li><a href="http://www.cqluwei.com" target="_blank">重庆租车</a></li><li><a href="http://www.nyqcw.cn" target="_blank">南阳汽车网 </a></li><li><a href="http://www.maotiao.com" target="_blank">汽车报价网 </a></li><li><a href="http://www.cntui.cn" target="_blank">b2b电子商务 </a></li><li><a href="http://www.wxqgl.net" target="_blank">桂林旅游 </a></li><li><a href="http://www.7895.com" target="_blank">电气设备 </a></li></ul></div><!-- right widget START --><div id="widget_right"><ul><li id="back-top"><a href="javascript:void(0)"></a></li><li id="service-chat"><a href="http://qiao.baidu.com/v3/?module=default&controller=im&action=index&ucid=6534618&type=n&siteid=3474866" target="_blank"><!-- <div id="BDBridgeFixedWrap"></div> --></a></li><li id="wechat-xieche"><div id="wechat-qrcode"></div><a href="javascript:void(0)"></a></li></ul></div><!-- right widget END --><!--<script src="__PUBLIC__/new_2/js/jquery.min.js" ></script><script src="__PUBLIC__/new_2/js/jquery-ui-1.9.2.custom.min.js" ></script>--><script src="__PUBLIC__/new_2/js/base.js" ></script><div class="clear"></div><div class="w1200 footer"><ul class="row gy"><li><a href="__APP__/about/4" target="_blank" rel="nofollow">关于我们</a>|</li><li><a href="__APP__/about/16" target="_blank" rel="nofollow">联系我们</a>|</li><li><a href="__APP__/about/24" target="_blank" rel="nofollow">服务协议</a>|</li><li><a href="__APP__/about/9" target="_blank">如何预约维修保养 </a>|</li><li style="display:none;"><a href="__APP__/Sitemap.xml">xml地图</a>|</li><li><a href="__APP__/sitemap.html">网站地图</a></li></ul><div class="row ba">
				Copyright © <?php echo (C("YEAR")); ?> WWW.XIECHE.COM.CN 沪ICP备12017241号 携车网 版权所有
				
				<!-- Start Alexa Certify Javascript --><script type="text/javascript">
				_atrk_opts = { atrk_acct:"blklh1aMQV00iP", domain:"xieche.com.cn",dynamic: true};
				(function() { var as = document.createElement('script'); as.type = 'text/javascript'; as.async = true; as.src = "https://d31qbv1cthcecs.cloudfront.net/atrk.js"; var s = document.getElementsByTagName('script')[0];s.parentNode.insertBefore(as, s); })();
				</script><noscript><img src="https://d5nxst8fruw4z.cloudfront.net/atrk.gif?account=blklh1aMQV00iP" style="display:none" height="1" width="1" alt="" /></noscript><!-- End Alexa Certify Javascript --><br class="clear"></div><div class="row fi"><ul><li class="t1"><a href="http://www.sgs.gov.cn/lz/licenseLink.do?method=licenceView&entyId=20121109104613460" title="经营性网站备案信息" target="_blank" rel="nofollow"></a></li><li class="t2"><a href="http://webscan.360.cn/index/checkwebsite/url/www.xieche.com.cn" title="360网站安全检测" target="_blank" rel="nofollow"></a></li><li class='t3'><a href="http://www.zx110.org/" title="征信网" target="_blank" rel="nofollow"></a></li><li class="t4"><!--<a href="http://www.alipay.com/" rel="nofollow"></a>--></li></ul></div><div class="clear"></div></div><div style="text-align:center"><!--新加的百度统计--><script type="text/javascript">
		var _bdhmProtocol = (("https:" == document.location.protocol) ? " https://" : " http://");
		document.write(unescape("%3Cscript src='" + _bdhmProtocol + "hm.baidu.com/h.js%3F60969e039f9a2a7252a22e6e27e9f16f' type='text/javascript'%3E%3C/script%3E"));
		</script><!--新加的百度统计结束--><!--CNZZ--><script type="text/javascript">var cnzz_protocol = (("https:" == document.location.protocol) ? " https://" : " http://");document.write(unescape("%3Cspan id='cnzz_stat_icon_5724214'%3E%3C/span%3E%3Cscript src='" + cnzz_protocol + "s9.cnzz.com/stat.php%3Fid%3D5724214%26show%3Dpic1' type='text/javascript'%3E%3C/script%3E"));</script></div><!-- Google Tag Manager --><noscript><iframe src="//www.googletagmanager.com/ns.html?id=GTM-MFHJWV" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript><script>
		(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
		new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
		j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
		'//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
		})(window,document,'script','dataLayer','GTM-MFHJWV');
	</script><!-- End Google Tag Manager --></body></html><?php else: ?><!DOCTYPE html><html><head><meta charset="utf-8" /><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /><meta http-equiv="X-UA-Compatible" content="IE=10;IE=9;IE=8;IE=7;" /><title><?php if(!empty($title)): echo ($title); else: ?>平安好车,汽车保养维修预约,4S店折扣优惠,事故车维修预约<?php endif; ?></title><?php if(empty($meta_keyword)): ?><meta content="汽车保养,汽车维修,携车网,4S店预约保养,事故车维修" name="keywords"><?php else: ?><meta content="<?php echo ($meta_keyword); ?>" name="keywords"><?php endif; if(empty($description)): ?><meta content="汽车保养维修,事故车维修-首选携车网,全国唯一4S店汽车保养维修折扣的网站,最低5折起,还有更多团购套餐任您选,事故车维修预约,不花钱,还有返利拿4006602822"  name="description"><?php else: ?><meta content="<?php echo ($description); ?>" name="description"><?php endif; ?><script type="text/javascript" src="__PUBLIC__/Js/DatePicker/WdatePicker.js?v=<?php echo (C("VERSION_DATE")); ?>"></script><!--加载时间控件插件--><script type="text/javascript" src="__PUBLIC__/Js/com.js?v=<?php echo (C("VERSION_DATE")); ?>"></script><link rel="shortcut icon" href="http://www.pahaoche.com/favicon.ico" type="image/x-icon"><link rel="stylesheet" type="text/css" href="__PUBLIC__/new_pa/css/basic.css?v=<?php echo (C("VERSION_DATE")); ?>" /><link rel="stylesheet" type="text/css" href="__PUBLIC__/new_pa/css/common.css?v=<?php echo (C("VERSION_DATE")); ?>" /><script src="__PUBLIC__/new_pa/js/jquery.min.js" ></script><script src="__PUBLIC__/new_pa/js/jquery-ui-1.9.2.custom.min.js" ></script><link rel="stylesheet" href="__PUBLIC__/new_pa/css/redmond/jquery-ui-1.9.2.custom.css"><script src="__PUBLIC__/new_pa/js/base.js"></script><!-- GA Track Code Start --><script src="__PUBLIC__/new_pa/js/pa_ga.js"></script><!-- GA Track Code End --><script type="text/javascript">		 $(document).ready(function(){
				paMenu();
		 });
		 function paMenu()
		 {
		 	$(".navbar>li:eq(1)").mouseover(function(){
				$(".navbar>li>ul").css("display","block");
			});
			$(".navbar>li:eq(1)").siblings().mouseover(function(){
				$(".navbar>li>ul").css("display","none");
				$(".navbar>li:eq(1) a").attr("class","");
			});
			$(".navbar>li>ul").mouseover(function(){
				$("#m1").attr("class","subNavMouseOut");
			});
			$(".navbar>li>ul").mouseout(function(){
				$(".navbar>li>ul").css("display","none");
				$(".navbar>li:eq(1) a").attr("class","");
			});
		 }
</script></head><body><div class="topContainer"><!-- topContent Start --><div class="topContent"><div class="Header"><a href="http://www.pahaoche.com" target="_top" class="logo"></a><div class="Head_r"><div class="log_buyer"><a href="http://jp.pahaoche.com/jp/perCenter.do" target="_top">经销商登录</a></div><div class="top_cx"><a href="http://jp.pahaoche.com/jp/seller/seller.do" target="_top">竞价查询</a></div><div class="hotline" style="margin-top: 30px;"><img src="__PUBLIC__/new_pa/images/hotline.png" /></div></div></div><!--  导航   --><div class="subNav"><ul class="navbar"><li><a href="http://www.pahaoche.com/" target="_top">首页</a></li><li><a href="http://www.pahaoche.com/yuyue.w" id="m1" target="_blank">我要卖车</a><ul style="display:none;"><li><a href="http://www.pahaoche.com/yuyue.w" target="_blank">预约卖车</a></li><li><a href="http://www.pahaoche.com/helpQA.w">卖车流程</a></li></ul></li><li><a href="http://baoyang.pahaoche.com">我要养车</a></li><li><a href="http://www.pahaoche.com/mapStore.w" target="_top">门店地图</a></li><li><a href="http://www.pahaoche.com/consulting.w" target="_top">好车资讯</a></li><li><a href="http://www.pahaoche.com/yuyue.w" target="_blank">好车活动</a></li><li><a id="chexianhead" style="float:left;display:block;width:45px;text-align:right;" href="http://www.4008000000.com/cpchexian/bd/bd.shtml?WT.mc_id=ec03-pahaoche-001" target="_blank">车险</a><a id="chedaihead" style="float:left;display:block;width:45px;text-align:left;text-indent:5px;" href="http://daikuan.pingan.com/qichedaikuan.shtml?WT.mc_id=ec03-pahaoche-001" target="_blank">车贷</a></li><li><a href="http://www.pahaoche.com/about.w" target="_top">关于我们</a></li></ul></div></div><!-- topContent End --></div><script src="__PUBLIC__/new_2/js/jquery.min.js" ></script><script src="__PUBLIC__/new_2/js/base.js" ></script><script type="text/javascript" src="__PUBLIC__/Js/car_select/car_data.js?v=<?php echo (C("VERSION_DATE")); ?>"></script><script type="text/javascript" src="__PUBLIC__/Js/car_select/normal.js?v=<?php echo (C("VERSION_DATE")); ?>"></script><script src="__PUBLIC__/think/jquery.think.js?v=<?php echo (C("VERSION_DATE")); ?>"></script><style>	.weixin{width: 1200px; height: 430px; background: url("http://statics.xieche.com.cn/new_2/images/order-complete-weixin/weixin-1.png") center 0px no-repeat; margin: 20px auto; border: 1px solid #cacaca;}
</style><link rel="stylesheet" type="text/css" href="__PUBLIC__/new_2/css/index.css?v=<?php echo (C("VERSION_DATE")); ?>" /><style>	.gy a{

	}
	.gy a:visited{
		color: #000000;
	}
	.gy a:hover{
		text-decoration: underline;
	}
</style><script>	function fleshVerify(){ 
	//重载验证码
	var timenow = new Date().getTime();
		$('#verifyImg').attr("src",'__APP__/common/verify/'+timenow);
	}

	function showid(id,name){
		$('#address').val(name);
		$('#shop_area').val(id);
		$('#address_Shop').hide();
		$('#ShopAddressDiv').hide();
	}

	function showid_1(id,name){
		$('#address_1').val(name);
		$('#shop_area_1').val(id);
		$('#address_Shop_1').hide();
		$('#ShopAddressDiv_1').hide();
	}

	function close_div(id){
		if(id == 1){
			$('#address_Shop').hide();
			$('#ShopAddressDiv').hide();
		}else{
			$('#ShopName_div').hide();
		}
	}

	function close_div_1(id){
		if(id == 1){
			$('#address_Shop_1').hide();
			$('#ShopAddressDiv_1').hide();
		}else{
			$('#ShopName_div_1').hide();
		}
	}
	
	function giveshopname(id,name){
		$('#text_shop_name').val(name);
		$('#shop_name').val(name);
		$('#ShopName_div').hide();
		
	}

	function giveshopname_1(id,name){
		$('#text_shop_name_1').val(name);
		$('#shop_name_1').val(name);
		$('#ShopName_div_1').hide();
		
	}
	function showadress(){
		if( this.value == '可选填')value=' ';
		$('#address_Shop').show();
		$('#ShopAddressDiv').show();
		$.ajax({
			type: "POST",
			url: "/index-AjaxInfo",
			cache: false,
			data: "type=1",
			success: function(data){
				
				var str = "<table width=100% border='0' align='center' cellpadding='1' cellspacing='13' style='text-align: center; '>";
				data = eval("(" + data + ")");
				var count = 1;
				for(a=0;a<data.length;a++){
					if(a%4==0){
						str+="<tr>";
					}
					str+="<td ><a href='###' onclick=showid("+(data[a]['id'])+",'"+(data[a]['region_name'])+"');><font style='color:#666666;'>"+data[a]['region_name']+"</font></a></td>";
					if(a!=0){
						if(count%4==0){
							str+="</tr>";
						}
					}
					count++;
				}
				str+="</table>";
				$('#ShopAddressDiv').html(str);

			}
		})
	}


	function showadress_1(){
		if( this.value == '可选填')value=' ';
		$('#address_Shop_1').show();
		$('#ShopAddressDiv_1').show();
		$.ajax({
			type: "POST",
			url: "/index-AjaxInfo",
			cache: false,
			data: "type=1",
			success: function(data){
				
				var str = "<table width=100% border='0' align='center' cellpadding='1' cellspacing='13' style='text-align: center; '>";
				data = eval("(" + data + ")");
				var count = 1;
				for(a=0;a<data.length;a++){
					if(a%4==0){
						str+="<tr>";
					}
					str+="<td ><a href='###' onclick=showid_1("+(data[a]['id'])+",'"+(data[a]['region_name'])+"');><font style='color:#666666;'>"+data[a]['region_name']+"</font></a></td>";
					if(a!=0){
						if(count%4==0){
							str+="</tr>";
						}
					}
					count++;
				}
				str+="</table>";
				$('#ShopAddressDiv_1').html(str);

			}
		})
	}
		
	function showshopname(){
		var shopname = $('#text_shop_name').val();
		shopname = shopname.trim();
		if(shopname){
		
			$.ajax({
				type: "POST",
				url: "/index/AjaxInfo",
				cache: false,
				data: "type=2&shopname="+shopname,
				success: function(data){
					
					var str = "<div style='TEXT-ALIGN: right;' style='height: 25px;'><a href='###' onclick='close_div(2);'>[关闭]</a>&nbsp;</div>";
					str+= "<table width=100% border='0' align='left' cellpadding='1' cellspacing='10'>";
					data = eval("(" + data + ")");
					if(data){
						for(a=0;a<data.length;a++){
						str+="<tr  align='left'><td ><a href='###' onclick=giveshopname("+(data[a]['id'])+",'"+(data[a]['shop_name'])+"');><font style='color:#666666;'>"+data[a]['shop_name']+"</font></a></td></tr>";
						}
						str+="</table>";
						$('#ShopName_div').show();
						$('#ShopName_div').html(str);
					}
					

				}
			})
		}
		
			
	}

	function showshopname_1(){
		var shopname = $('#text_shop_name_1').val();
		shopname = shopname.trim();
		if(shopname){
		
			$.ajax({
				type: "POST",
				url: "/index/AjaxInfo",
				cache: false,
				data: "type=2&shopname="+shopname,
				success: function(data){
					var str = "<div style='TEXT-ALIGN: right;' style='height: 25px;'><a href='###' onclick='close_div_1(2);'>[关闭]</a>&nbsp;</div>";
					str+= "<table width=100% border='0' align='left' cellpadding='1' cellspacing='10'>";
					data = eval("(" + data + ")");
					if(data){
						for(a=0;a<data.length;a++){
						str+="<tr  align='left'><td ><a href='###' onclick=giveshopname_1("+(data[a]['id'])+",'"+(data[a]['shop_name'])+"');><font style='color:#666666;'>"+data[a]['shop_name']+"</font></a></td></tr>";
						}
						str+="</table>";
						$('#ShopName_div_1').show();
						$('#ShopName_div_1').html(str);
					}
					

				}
			})
		}
		
			
	}

	</script><!--index row 1 Start--><div class="w1200 index1"><div class="service" id=""><ul id="tabs1" class="tabs"><li class="current ss">搜索4S店铺维修保养</li><li class="mine">车型快速入口</li><li class="quick"><?php if(!empty($_SESSION['uid'])): ?>我的车辆<?php else: ?>登录快速入口<?php endif; ?></li></ul><div id="con1" class="con"><div style="display: none;" class='current'><form class="search"  name="form2" method="POST" action="__APP__/order"><ul><li id="pinpai"><label>选择品牌:</label><select id="get_brand" name="brand_id"  onchange="comp_brlist('get_brand','get_series', 'get_model');"></select></li><li id="chexing"><label>选择车系:</label><select id="get_series" name="series_id"  disabled onchange="comp_yearlist('get_brand', 'get_series', 'get_model');"></select><label style="margin-left:10px;">选择车型:</label><select class="car_select" id="get_model" name="model_id"  disabled></select></li><li id="diqu"><label>所在地区:</label><input type="text" id="address" name="address" class="input-text" onclick="showadress();" onblur=" if( this.value == '可选填')value=' '" onblur=" if( this.value.replace(/[ ]/g,'') == '')value='可选填'" value="可选填"><div id="address_Shop" style="position: absolute; z-index:1; left: 106px; top: 112px; width: 358px; height: 30px; border:#CCCCCC solid 1px; background-color:white; display:none  ">可直接选择地理位置
										<a href="#" style="color:#0066cc; float:right; display: inline-block; width: 50px; height: 30px; line-height: 30px;"  onclick="close_div(1);">[关闭]</a><br class="clear"></div><div id="ShopAddressDiv" style="position:absolute;z-index:1;left: 106px; top: 143px; width: 358px; height: 160px; border:#CCCCCC solid 1px;background-color:white;display:none;"></div></li><li id="guanjianzi"><label>4S店铺名:</label><input type="text" id="text_shop_name" name="shop_name" class="input-text" onkeyup="showshopname();" onclick=" if( this.value == '可选填')value=''" onblur=" if( this.value.replace(/[ ]/g,'') == '')value='可选填'" value="可选填" style="height:25px;"><div id="ShopName_div" style="position:absolute;z-index:1;left:123px;top:178px;width:388px;height:218px;border:#CCCCCC solid 1px;background-color:white;display:none;overflow-y:auto;"></li></ul><input type="submit" value="提 交 搜 索" class="submit-btn" ></form></div><!-- 车型快速入口 --><div style="display: none; overflow: hidden;" class='quick-access'><dl><dt>小型车</dt><dd><a href="<?php echo URL('/order/index',array('brand_id'=>17,'series_id'=>153,'model_id'=>408)); echo C('HTML_URL_SUFFIX');?>" target="_blank">新Polo</a></dd><dd><a href="<?php echo URL('/order/index',array('brand_id'=>63,'series_id'=>439,'model_id'=>1186)); echo C('HTML_URL_SUFFIX');?>" target="_blank">晶锐</a></dd><dd><a href="<?php echo URL('/order/index',array('brand_id'=>54,'series_id'=>377,'model_id'=>1028)); echo C('HTML_URL_SUFFIX');?>" target="_blank">K2</a></dd><dd><a href="<?php echo URL('/order/index',array('brand_id'=>25,'series_id'=>231,'model_id'=>631)); echo C('HTML_URL_SUFFIX');?>" target="_blank">嘉年华</a></dd><dd><a href="<?php echo URL('/order/index',array('brand_id'=>68,'series_id'=>471,'model_id'=>1278)); echo C('HTML_URL_SUFFIX');?>" target="_blank">爱唯欧</a></dd><dd><a href="<?php echo URL('/order/index',array('brand_id'=>8,'series_id'=>83,'model_id'=>227)); echo C('HTML_URL_SUFFIX');?>" target="_blank">飞度</a></dd><br class="clear"></dl><dl><dt>紧凑型车</dt><dd><a href="<?php echo URL('/order/index',array('brand_id'=>17,'series_id'=>156,'model_id'=>418)); echo C('HTML_URL_SUFFIX');?>" target="_blank">朗逸</a></dd><dd><a href="<?php echo URL('/order/index',array('brand_id'=>17,'series_id'=>164,'model_id'=>449)); echo C('HTML_URL_SUFFIX');?>" target="_blank">高尔夫</a></dd><dd><a href="<?php echo URL('/order/index',array('brand_id'=>63,'series_id'=>440,'model_id'=>1188)); echo C('HTML_URL_SUFFIX');?>" target="_blank">明锐</a></dd><dd><a href="<?php echo URL('/order/index',array('brand_id'=>11,'series_id'=>118,'model_id'=>315)); echo C('HTML_URL_SUFFIX');?>" target="_blank">凯越</a></dd><dd><a href="<?php echo URL('/order/index',array('brand_id'=>56,'series_id'=>407,'model_id'=>1099)); echo C('HTML_URL_SUFFIX');?>" target="_blank">荣威550</a></dd><dd><a href="<?php echo URL('/order/index',array('brand_id'=>25,'series_id'=>229,'model_id'=>617)); echo C('HTML_URL_SUFFIX');?>" target="_blank">新福克斯</a></dd><dd><a href="<?php echo URL('/order/index',array('brand_id'=>10,'series_id'=>102,'model_id'=>278)); echo C('HTML_URL_SUFFIX');?>" target="_blank">307</a></dd><dd><a href="<?php echo URL('/order/index',array('brand_id'=>68,'series_id'=>473,'model_id'=>1286)); echo C('HTML_URL_SUFFIX');?>" target="_blank">科鲁兹</a></dd><dd><a href="<?php echo URL('/order/index',array('brand_id'=>17,'series_id'=>170,'model_id'=>473)); echo C('HTML_URL_SUFFIX');?>" target="_blank">速腾</a></dd><dd><a href="<?php echo URL('/order/index',array('brand_id'=>23,'series_id'=>205,'model_id'=>555)); echo C('HTML_URL_SUFFIX');?>" target="_blank">卡罗拉</a></dd><br class="clear"></dl><dl><dt>中级车</dt><dd><a href="<?php echo URL('/order/index',array('brand_id'=>17,'series_id'=>152,'model_id'=>400)); echo C('HTML_URL_SUFFIX');?>" target="_blank">新帕萨特</a></dd><dd><a href="<?php echo URL('/order/index',array('brand_id'=>11,'series_id'=>114,'model_id'=>303)); echo C('HTML_URL_SUFFIX');?>" target="_blank">君威</a></dd><dd><a href="<?php echo URL('/order/index',array('brand_id'=>63,'series_id'=>438,'model_id'=>1179)); echo C('HTML_URL_SUFFIX');?>" target="_blank">昊锐</a></dd><dd><a href="<?php echo URL('/order/index',array('brand_id'=>25,'series_id'=>233,'model_id'=>1403)); echo C('HTML_URL_SUFFIX');?>" target="_blank">蒙迪欧致胜</a></dd><dd><a href="<?php echo URL('/order/index',array('brand_id'=>55,'series_id'=>397,'model_id'=>1078)); echo C('HTML_URL_SUFFIX');?>" target="_blank">天籁</a></dd><br class="clear"></dl></div><!-- 我的车型 --><div style="display: none;" class='my-cars'><?php if(!empty($_SESSION['uid'])): ?><form class="search"  name="form3" method="POST" action="__APP__/order"><ul id=""><li id="wc"><label>我的车型:</label><ul class="wclist"><?php if(is_array($list_membercar)): $k = 0; $__LIST__ = $list_membercar;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($k % 2 );++$k;?><li><input type="radio" name="u_c_id" value="<?php echo ($vo["u_c_id"]); ?>" <?php if($vo["u_c_id"] == $u_c_id): ?>checked<?php endif; ?>/><?php echo ($vo["car_name"]); ?>&nbsp;<?php echo ($vo["brand_name"]); ?>-<?php echo ($vo["series_name"]); ?>-<?php echo ($vo["model_name"]); ?></li><?php endforeach; endif; else: echo "" ;endif; ?><div class='clear'></div></ul></li><li id="guanjianzi"><label>4S店铺名:</label><input type="text" id="text_shop_name_1" name="shop_name_1" class="input-text" onkeyup="showshopname_1();" onclick=" if( this.value == '可选填')value=''" onblur=" if( this.value.replace(/[ ]/g,'') == '')value='可选填'" value="可选填" style="height:25px;"><div id="ShopName_div_1" style="position:absolute;z-index:1;left:123px;top:178px;width:388px;height:218px;border:#CCCCCC solid 1px;background-color:white;display:none;"></li><div class='submit'><input type="submit" value="提 交 搜 索" class="submit-btn" style="float:right"><br class="clear"></div></ul></form><?php else: ?><form class="search"  name="form4" method="POST" action="__APP__/public/logincheck"><ul id="my-cars" ><li id="pinpai" style="display: none;"><label>选择品牌:</label><select id="get_brand1" name="brand_id1"  onchange="comp_brlist('get_brand1','get_series1', 'get_model1');"></select></li><li id="diqu"><label>用户名：</label><input type="text" id="username" name="username" class='input-text'></li><li id="guanjianzi"><label>密 码:</label><input class='input-text' type="password" name="password" type="password" ></li><li id="guanjianzi"><label>验证码:</label><input type="text" id='reg-code' check="Require" warning="请输入验证码" name="verify" /><img  height='27'  style='float:left; margin-top: 6px; margin-left: 5px; height: 27px; cursor:pointer' id="verifyImg" SRC="__APP__/common/verify/" onClick="fleshVerify()" BORDER="0" ALT="点击刷新验证码" align="absmiddle" /></li><div class='submit'><input type="submit"  value="快 速 登 录" class="submit-btn" style="position: absolute; bottom: 40px; right: 68px; }"><br class="clear"></div></ul></form><?php endif; ?></div></div><div class="clear"></div></div><style type="text/css">				/* focus */
				#focus{width:580px;height:300px;overflow:hidden;position:relative;float:right;margin:0 auto;}
				#focus ul{height:380px;position:absolute;}
				#focus ul li{float:left;width:580px;height:300px;overflow:hidden;position:relative;background:#000;}
				#focus ul li div{position:absolute;overflow:hidden;}
				#focus .btnBg{position:absolute;width:580px;height:20px;left:0;bottom:0;background:#000;}
				#focus .btn{position:absolute;width:580px;height:10px;padding:5px 10px;right:0;bottom:0;text-align:right;}
				#focus .btn span{display:inline-block;_display:inline;_zoom:1;width:25px;height:10px;_font-size:0;margin-left:5px;cursor:pointer;background:#fff;}
				#focus .btn span.on{background:#fff;}
			</style><div class="slideshow" id="focus"><ul id="innerSlide"><li><a href="http://www.xieche.com.cn/carservice" target="_blank"><img width="580" height="300" alt="" src="__PUBLIC__/new/images/banner/womencall.jpg"/></a></li><li><a href="http://www.xieche.com.cn/coupon/371" target="_blank"><img width="580" height="300" alt="" src="__PUBLIC__/new/images/banner/a/e20140508.jpg"/></a></li><!-- <li><a href="http://www.xieche.com.cn/shop/1367" target="_blank"><img width="580" height="300" alt="" src="__PUBLIC__/new/images/banner/a/e20140501.jpg"/></a></li> --><!--<li><a href="http://event.xieche.com.cn/20140501/" target="_blank"><img width="580" height="300" alt="" src="__PUBLIC__/new/images/banner/a/e20140501-2.jpg"/></a></li>--><!-- <li><a href="http://www.xieche.com.cn/shop/287" target="_blank"><img width="580" height="300" alt="上海万兴-别克4S店汽车保养维修_携车网" src="__PUBLIC__/new/images/banner/banner-2.jpg"/></a></li> --><li><a href="http://www.xieche.com.cn/coupon/75" target="_blank"><img width="580" height="300" alt="上海绿地徐通-雪佛兰4S店-上海绿地徐通雪佛兰4S店1000元现金券900.00现金券-携车网" src="__PUBLIC__/new/images/banner/banner-3.png"/></a></li><li><a href="http://www.xieche.com.cn/shop/87" target="_blank"><img width="580" height="300" alt="上海绿地徐汇-别克4S店汽车保养维修_携车网" src="__PUBLIC__/new/images/banner/banner-4.png"/></a></li><li><a href="http://www.xieche.com.cn/carservice" target="_blank"><img width="580" height="300" alt="" src="__PUBLIC__/new/images/banner/mancall.jpg"/></a></li><!-- <li><a href="http://event.xieche.com.cn/20140402/" target="_blank"><img width="580" height="300" alt="南京分站招商-携车网" src="__PUBLIC__/new/images/banner/banner-6.jpg"/></a></li> --></ul></div><div class="clear"></div></div><!-- 通栏广告位 1 START --><!-- <div class="w1200 content banner-container"><div class="index-banner-1"><a href="http://www.xieche.com.cn/cattle" target="_blank" ><img src="http://statics.xieche.net/image/event/20140516/index-banner/20140516-banner.png" alt="携车网-养车惠分享，好事荐成双" 、></a></div></div> --><!-- 通栏广告位 1 END --><!--index row 1 ends --><div class="w1200 content index2"><div class="w830 content-left"><!--sales 1 start--><div class="w830 sale1"><div class="cate-title"><h3>现金券</h3></div><ul class='w830 filter' id="tabs2"><li  class="current"><a href="<?php echo URL('/coupon/index',array('coupon_type'=>1)); echo C('HTML_URL_SUFFIX');?>">全部</a></li><?php if(is_array($data_Pcoupon["Coupon"])): $i = 0; $__LIST__ = $data_Pcoupon["Coupon"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$voc1): $mod = ($i % 2 );++$i;?><li><a href="<?php echo URL('/coupon/index',array('coupon_type'=>1,'brand_id'=>$voc1['brand_id'])); echo C('HTML_URL_SUFFIX');?>" title="<?php echo ($voc1["brand_name"]); ?>现金券"><?php echo ($voc1["brand_name"]); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?></ul><div id="con2"><!--全部内容开始--><div><ul class="w830 sales-con"><?php if(is_array($data_AllPcoupon)): $i = 0; $__LIST__ = $data_AllPcoupon;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$arr): $mod = ($i % 2 );++$i;?><li ><span class="tuijian-tag"></span><div class="img-con"><a href="__APP__/coupon/<?php echo ($arr["id"]); ?>" target="_blank"><img src="/UPLOADS/Coupon/Logo/<?php echo ($arr["coupon_pic"]); ?>" width="253" height="190" alt="<?php echo ($arr["coupon_name"]); ?>" title="<?php echo ($arr["coupon_name"]); ?>"></a><span class="address"><strong href="#"><?php echo ($arr["shop_address"]); ?></strong></span></div><div class="content"><a href="__APP__/coupon/<?php echo ($arr["id"]); ?>" target="_blank"><h5><?php echo ($arr["coupon_name"]); ?></h5></a><span><?php echo ($arr["shop_name"]); ?><a href="__APP__/shop/<?php echo ($arr["shop_id"]); ?>" target="_blank">[查看店铺]</a></span><div class="price"><strong class="orange">￥<?php echo ($arr["coupon_amount"]); ?>元</strong><em> / <?php echo ($arr["cost_price"]); ?>元</em></div><div class="bought"><strong class="orange"><?php echo ($arr["pay_count"]); ?></strong><em>人已购</em></div></div></li><?php endforeach; endif; else: echo "" ;endif; ?><div class="clear"></div></ul></div><!--全部内容结算--><?php if(is_array($data_Pcoupon["Coupon"])): $i = 0; $__LIST__ = $data_Pcoupon["Coupon"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$voc1): $mod = ($i % 2 );++$i;?><div><ul class="w830 sales-con"><?php if(is_array($voc1["Pcoupon"])): $i = 0; $__LIST__ = $voc1["Pcoupon"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$arr): $mod = ($i % 2 );++$i;?><li <?php if($arr['is_tuijian'] == '1'): ?>class="tuijian-tag-con"<?php endif; ?>><span class="tuijian-tag"></span><div class="img-con"><a href="__APP__/coupon/<?php echo ($arr["id"]); ?>" target="_blank"><img src="/UPLOADS/Coupon/Logo/<?php echo ($arr["coupon_pic"]); ?>" width="253" height="190" alt="<?php echo ($arr["coupon_name"]); ?>" title="<?php echo ($arr["coupon_name"]); ?>"></a><span class="address"><strong href="#"><?php echo ($arr["shop_address"]); ?></strong></span></div><div class="content"><a href="__APP__/coupon/<?php echo ($arr["id"]); ?>" target="_blank"><h5><?php echo ($arr["coupon_name"]); ?></h5></a><span><?php echo ($arr["shop_name"]); ?><a href="__APP__/shop/<?php echo ($arr["shop_id"]); ?>" target="_blank">[查看店铺]</a></span><div class="price"><strong class="orange">￥<?php echo ($arr["coupon_amount"]); ?>元</strong><em> / <?php echo ($arr["cost_price"]); ?>元</em></div><div class="bought"><strong class="orange"><?php echo ($arr["pay_count"]); ?></strong><em>人已购</em></div></div></li><?php endforeach; endif; else: echo "" ;endif; ?><div class="clear"></div></ul></div><?php endforeach; endif; else: echo "" ;endif; ?></div><div class="clear"></div></div><!--sale1 ends--><div class="ad banner" style="margin:20px auto; width: 850px; height: 80px; clear: both;"><a href="http://www.xieche.com.cn/shiguche" title="携车网 事故车" target="_blank"><img src="http://statics.xieche.com.cn/ad-banner/830/ac-car.jpg" alt="携车网 事故车" width="830" height="80"></a></div><!--sale2 start--><div class="w830 sale2"><div class="cate-title"><h3>套餐券</h3></div><ul class='w830 filter' id="tabs3"><li  class="current"><a href="<?php echo URL('/coupon/index',array('coupon_type'=>2)); echo C('HTML_URL_SUFFIX');?>">全部</a></li><?php if(is_array($data_Gcoupon["Coupon"])): $i = 0; $__LIST__ = $data_Gcoupon["Coupon"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$voc1): $mod = ($i % 2 );++$i;?><li><a href="<?php echo URL('/coupon/index',array('coupon_type'=>2,'brand_id'=>$voc1['brand_id'])); echo C('HTML_URL_SUFFIX');?>" title="<?php echo ($voc1["brand_name"]); ?>套餐券"><?php echo ($voc1["brand_name"]); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?></ul><div id="con3"><!--全部内容开始--><div><ul class="w830 sales-con" ><?php if(is_array($data_AllGcoupon)): $i = 0; $__LIST__ = $data_AllGcoupon;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$arr): $mod = ($i % 2 );++$i;?><li><span class="tuijian-tag"></span><div class="img-con"><a href="__APP__/coupon/<?php echo ($arr["id"]); ?>" target="_blank"><img src="/UPLOADS/Coupon/Logo/<?php echo ($arr["coupon_pic"]); ?>" width="105" height="80" alt="<?php echo ($arr["coupon_name"]); ?>" title="<?php echo ($arr["coupon_name"]); ?>"></a></div><div class="content"><a href="__APP__/coupon/<?php echo ($arr["id"]); ?>" target="_blank"><h5><?php echo ($arr["coupon_name"]); ?></h5></a><span><?php echo ($arr["shop_name"]); ?></span><span class="address"><?php echo ($arr["shop_address"]); ?><a href="__APP__/shop/<?php echo ($arr["shop_id"]); ?>" target="_blank">[查看店铺]</a></span><div class="price"><strong class="orange">￥<?php echo ($arr["coupon_amount"]); ?>元</strong><em> / <?php echo ($arr["cost_price"]); ?>元</em></div><div class="bought"><strong class="orange"><?php echo ($arr["pay_count"]); ?></strong><em>人已购</em></div></div></li><?php endforeach; endif; else: echo "" ;endif; ?><div class="clear"></div></ul></div><!--全部内容结束--><?php if(is_array($data_Gcoupon["Coupon"])): $i = 0; $__LIST__ = $data_Gcoupon["Coupon"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$voc1): $mod = ($i % 2 );++$i;?><div><ul class="w830 sales-con" ><?php if(is_array($voc1["Gcoupon"])): $i = 0; $__LIST__ = $voc1["Gcoupon"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$arr): $mod = ($i % 2 );++$i;?><li <?php if($arr['is_tuijian'] == '1'): ?>class="tuijian-tag-con"<?php endif; ?>><span class="speacial-banner"></span><div class="img-con"><a href="__APP__/coupon/<?php echo ($arr["id"]); ?>" target="_blank"><img src="/UPLOADS/Coupon/Logo/<?php echo ($arr["coupon_pic"]); ?>" width="105" height="80" alt="<?php echo ($arr["coupon_name"]); ?>" title="<?php echo ($arr["coupon_name"]); ?>"></a></div><div class="content"><a href="__APP__/coupon/<?php echo ($arr["id"]); ?>" target="_blank"><h5><?php echo ($arr["coupon_name"]); ?></h5></a><span><?php echo ($arr["shop_name"]); ?></span><span class="address"><?php echo ($arr["shop_address"]); ?><a href="__APP__/shop/<?php echo ($arr["shop_id"]); ?>" target="_blank">[查看店铺]</a></span><div class="price"><strong class="orange">￥<?php echo ($arr["coupon_amount"]); ?>元</strong><em> / <?php echo ($arr["cost_price"]); ?>元</em></div><div class="bought"><strong class="orange"><?php echo ($arr["pay_count"]); ?></strong><em>人已购</em></div></div></li><?php endforeach; endif; else: echo "" ;endif; ?><div class="clear"></div></ul></div><?php endforeach; endif; else: echo "" ;endif; ?></div><div class="clear"></div></div><!--shops ends--><!--shop starts--><div class="w830 hot-shop"><div class="cate-title"><h3>热门店铺
						<!-- <div class='gy' style='float:right; font-weight: normal' ><a href='/order'>共有<span  style='color: #009DD7'><?php echo ($shop1count); ?></span>家特约4S店,<span  style='color: #009DD7'><?php echo ($shop2count); ?></span>家非特约4S店.</a></div> --></h3></div><ul class='w830 filter' id="tabs4"><li  class="current"><a href="__APP__/order">全部</a></li><?php if(is_array($fs_shoplist)): $i = 0; $__LIST__ = $fs_shoplist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$fs_vo): $mod = ($i % 2 );++$i;?><li><a href="<?php echo URL('/order/index',array('fsid'=>$fs_vo[fsid])); echo C('HTML_URL_SUFFIX');?>" title="<?php echo ($fs_vo["fs_name"]); ?>"><?php echo ($fs_vo["fs_name"]); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?></ul><div id='con4'><div><ul class="w830 sales-con" ><?php if(is_array($shoplist)): $i = 0; $__LIST__ = $shoplist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$slist): $mod = ($i % 2 );++$i;?><li><div class="content"><a href="__APP__/shop/<?php echo ($slist["id"]); ?>" target="_blank"><h5><?php echo ($slist["shop_name"]); ?></h5></a><span class="shop-address"><?php echo ($slist["shop_address"]); ?><a href="__APP__/shop/<?php echo ($slist["id"]); ?>" target="_blank">[查看店铺]</a></span><div class="comment-rate"><a href="__APP__/shop/<?php echo ($slist["id"]); ?>#comment"><?php echo ($slist["comment_number"]); ?></a>人评论</div><div class="shop-rating"><span <?php echo "class='rating-".round($slist[comment_rate]/10)." rating-img'"; ?>></span><strong><?php echo ($slist[comment_rate]/10); ?></strong></div><div class="clear"></div><div class="price">工时折扣: <strong class="orange"><?php echo ($slist["workhours_sale"]); ?>折起</strong></div></div><div class="img-con"><div class='shop-img'><a href="__APP__/shop/<?php echo ($slist["id"]); ?>" target="_blank"><img src="<?php echo ($slist["shop_pic"]); ?>" width="105" height="80" alt="<?php echo ($slist["shop_name"]); ?>" title="<?php echo ($slist["shop_name"]); ?>"></a><span class="speacial-banner"></span></div><div class='clear'></div></div></li><?php endforeach; endif; else: echo "" ;endif; ?><div class="clear"></div></ul></div><?php if(is_array($fs_shoplist)): $i = 0; $__LIST__ = $fs_shoplist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$fs_shop_vo): $mod = ($i % 2 );++$i;?><div><ul class="w830 sales-con" ><?php if(is_array($fs_shop_vo["shoplist"])): $i = 0; $__LIST__ = $fs_shop_vo["shoplist"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$slist): $mod = ($i % 2 );++$i;?><li><div class="content"><a href="__APP__/shop/<?php echo ($slist["id"]); ?>" target="_blank"><h5><?php echo ($slist["shop_name"]); ?></h5></a><span class="shop-address"><?php echo ($slist["shop_address"]); ?><a href="__APP__/shop/<?php echo ($slist["id"]); ?>" target="_blank">[查看店铺]</a></span><div class="comment-rate"><a href="__APP__/shop/<?php echo ($slist["id"]); ?>#comment"><?php echo ($slist["comment_number"]); ?></a>人评论</div><div class="shop-rating"><span <?php echo "class='rating-".round($slist[comment_rate]/10)." rating-img'"; ?>></span><strong><?php echo ($slist[comment_rate]/10); ?></strong></div><div class="clear"></div><div class="price">工时折扣: <strong class="orange"><?php echo ($slist["workhours_sale"]); ?>折起</strong></div></div><div class="img-con"><div class='shop-img'><a href="__APP__/shop/<?php echo ($slist["id"]); ?>" target="_blank"><img src="<?php echo ($slist["shop_pic"]); ?>" width="105" height="80" alt="<?php echo ($slist["shop_name"]); ?>" title="<?php echo ($slist["shop_name"]); ?>"></a><span class="speacial-banner"></span></div><div class='clear'></div></div></li><?php endforeach; endif; else: echo "" ;endif; ?><div class="clear"></div></ul></div><?php endforeach; endif; else: echo "" ;endif; ?></div><div class="clear"></div></div><!--shops ends--></div><div class="w350 content-right"><div class="tuijian"><h3>话谈府上养车</h3><!--<ul><li><div class='image'><img src="http://www.xieche.com.cn/UPLOADS/Coupon/Logo/51a6f28d2385c.jpg" width='80' height="60" ></div><div class='info'><strong class="title"> 上海泰宸上海大众4S店500元现金券</strong></div></li><div class="clear"></div></ul>--><!-- <ul style="font-size: 13px;"><?php if(is_array($shopnotice)): $i = 0; $__LIST__ = $shopnotice;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$snvo): $mod = ($i % 2 );++$i;?><li><a target="_blank" href="<?php echo URL('/shop/favtype',array('shop_id'=>$snvo['shop_id'],'id'=>$snvo['id'])); echo C('HTML_URL_SUFFIX');?>" title="<?php echo ($snvo["noticetitle"]); ?>"> &bull; <?php echo (g_substr($snvo["noticetitle"],45)); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?></ul> --><ul style="font-size: 13px;"><?php if(is_array($shopnotice)): $i = 0; $__LIST__ = $shopnotice;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$snvo): $mod = ($i % 2 );++$i;?><li><a target="_blank" href="__APP__/article/<?php echo ($snvo["id"]); ?>" title="<?php echo ($snvo["title"]); ?>">&bull; <?php echo (g_substr($snvo["title"],45)); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?></ul></div><div class="tuijian"><h3>用车心得</h3><!--<ul><li><div class='image'><img src="http://www.xieche.com.cn/UPLOADS/Coupon/Logo/51a6f28d2385c.jpg" width='80' height="60" ></div><div class='info'><strong class="title">    上海泰宸上海大众4S店500元现金券</strong></div></li><div class="clear"></div></ul--><ul style="font-size: 13px;"><?php if(is_array($articlelist)): $i = 0; $__LIST__ = $articlelist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$artvo): $mod = ($i % 2 );++$i;?><li><a target="_blank" href="__APP__/article/<?php echo ($artvo["id"]); ?>" title="<?php echo ($artvo["title"]); ?>">&bull; <?php echo (g_substr($artvo["title"],45)); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?></ul></div><div class='w350 app-promote'><a href="__APP__/Application" title='携车网APP'><img src="__APP__/Public/new_2/images/app.jpg" alt="携车网APP" width='350' height='190' /></a></div><div class="weibo" style='margin-top: 20px; '><iframe width="350" height="493" class="share_self"  frameborder="0" scrolling="no" src="http://widget.weibo.com/weiboshow/index.php?language=&width=350&height=493&fansRow=1&ptype=1&speed=300&skin=1&isTitle=1&noborder=1&isWeibo=1&isFans=1&uid=2463073343&verifier=ff61d30e&dpc=1"></iframe></div></div><div class="clear"></div></div><script type='text/javascript'>			comp_fctlist("get_brand", "get_series", "get_model");
			/*
			function new_slider(slidercon, mainspeed, inspeed, outspeed) {
			    this.slidercon = slidercon;
			    this.mainspeed = mainspeed;
			    this.inspeed = inspeed;
			    this.outspeed = outspeed;
			    var i = 1;
			    var listcon = '<li class="current">' + 1 + '</li>';
			    
			    $(slidercon).append('<ul id="slider_counter"></ul>');
			    for (var j = 2; j <= $(slidercon).children("ul:first").children("li").length; j++) {
			        listcon = listcon + "<li>" + j + "</li>";
			    };
			    $(slidercon).children("#slider_counter").html(listcon);
			    $(slidercon).children("#slider_counter").children("li").mouseover(function() {
			        var index = $.inArray(this, $(slidercon).children("#slider_counter").children("li"));
			        $(slidercon).children("#slider_counter").children("li").removeClass("current").eq(index).addClass("current");
			        $(slidercon).children("ul:first").children("li").fadeOut(outspeed).eq(index).fadeIn(inspeed);
			        $(slidercon).children("#slider_title").html($(slidercon).children("ul:first").children("li:eq(" + parseInt($(this).html() - 1) + ")").children("a").children("img").attr("title"));
			        i = $(this).index() + 1;
			        if (i == $(slidercon).children("ul:first").children("li").length) {
			            i = 0;
			        };
			    });

			    function init() {
			        $(slidercon).children("ul:first").children("li:not(" + i + ")").fadeOut(outspeed);
			        $(slidercon).children("ul:first").children("li:eq(" + i + ")").fadeIn(inspeed);
			        $(slidercon).children("#slider_counter").children("li:not(" + i + ")").removeClass("current");
			        $(slidercon).children("#slider_counter").children("li:eq(" + i + ")").addClass("current");
			        $(slidercon).children("#slider_title").html($(slidercon).children("ul:first").children("li:eq(" + i + ")").children("a").children("img").attr("title"));
			        i++;
			        if (i >= $(slidercon).children("ul:first").children("li").length) {
			            i = 0;
			        };
			    };
			    var move = setInterval(init, mainspeed);
			    $(slidercon).mouseover(function() {
			        clearInterval(move);
			    });
			    $(slidercon).mouseout(function() {
			        move = setInterval(init, mainspeed);
			    });
			};

			new_slider('#new_slider',2000,300,10);
			*/

			function hTab(tab_controler,tab_con){
			this.tab_controler = tab_controler;
			this.tab_con = tab_con;
			var tabs = $(tab_controler).children("li");
			var panels = $(tab_con).children("div");
			$(tab_con).children("div").css("display","none");
			$(tab_con).children("div:first").css("display","block");
			$(tabs).hover(function(){
				var index = $.inArray(this,tabs);
				tabs.removeClass("current")
				.eq(index).addClass("current");
				panels.css("display","none")
				.eq(index).css("display","block");
			});
		};

		hTab('#tabs1','#con1');
		hTab('#tabs2','#con2');
		hTab('#tabs3','#con3');
		hTab('#tabs4','#con4');
		

		$(function() {
			var sWidth = $("#focus").width(); //获取焦点图的宽度（显示面积）
			var len = $("#focus ul li").length; //获取焦点图个数
			var index = 0;
			var picTimer;
			
			//以下代码添加数字按钮和按钮后的半透明条，还有上一页、下一页两个按钮
			var btn = "<div class='btnBg'></div><div class='btn'>";
			for(var i=0; i < len; i++) {
				btn += "<span></span>";
			}
			btn += "</div>";
			$("#focus").append(btn);
			$("#focus .btnBg").css("opacity",0.5);

			//为小按钮添加鼠标滑入事件，以显示相应的内容
			$("#focus .btn span").css("opacity",0.4).mouseover(function() {
				index = $("#focus .btn span").index(this);
				showPics(index);
			}).eq(0).trigger("mouseover");

			//本例为左右滚动，即所有li元素都是在同一排向左浮动，所以这里需要计算出外围ul元素的宽度
			$("#focus ul").css("width",sWidth * (len));
			
			//鼠标滑上焦点图时停止自动播放，滑出时开始自动播放
			$("#focus").hover(function() {
				clearInterval(picTimer);
			},function() {
				picTimer = setInterval(function() {
					showPics(index);
					index++;
					if(index == len) {index = 0;}
				},4000); //此4000代表自动播放的间隔，单位：毫秒
			}).trigger("mouseleave");
			
			//显示图片函数，根据接收的index值显示相应的内容
			function showPics(index) { //普通切换
				var nowLeft = -index*sWidth; //根据index值计算ul元素的left值
				$("#focus ul").stop(true,false).animate({"left":nowLeft},500); //通过animate()调整ul元素滚动到计算出的position
				//$("#focus .btn span").removeClass("on").eq(index).addClass("on"); //为当前的按钮切换到选中的效果
				$("#focus .btn span").stop(true,false).animate({"opacity":"0.4"},500).eq(index).stop(true,false).animate({"opacity":"1"},500); //为当前的按钮切换到选中的效果
			}
		});
		</script><!-- <div class="weixin"></div> --><div class="hotlink"><h4>友情链接</h4><ul class="row"><li><a href="http://www.xieche.com.cn" target="_blank">上海汽车保养</a></li><li><a href="http://www.xieche.com.cn" target="_blank">上海上门保养</a></li><li><a href="http://www.xieche.com.cn" target="_blank">上门保养</a></li><li><a href="http://www.xieche.com.cn" target="_blank">汽车保养</a></li><li><a href="http://www.xieche.com.cn" target="_blank">汽车维修</a></li><li><a href="http://www.xieche.com.cn" target="_blank">4S店预约保养</a></li><li><a href="http://www.xieche.com.cn" target="_blank">事故车维修</a></li><li><a href="http://jining.273.cn" target="_blank">济宁二手车</a></li><li><a href="http://www.haerbinzuche.cn" target="_blank">哈尔滨租车公司</a></li><li><a href="http://www.auto008.com" target="_blank">汽车专家网</a></li><li><a href="http://www.zjgjcc.com/" target="_blank">湛江海田国际车城</a></li><li><a href="http://www.abkitty.com" target="_blank">大连旅游租车</a></li><li><a href="http://www.icip.com.cn" target="_blank">宁波二手车</a></li><li><a href="http://www.cqluwei.com" target="_blank">重庆租车</a></li><li><a href="http://www.nyqcw.cn" target="_blank">南阳汽车网 </a></li><li><a href="http://www.maotiao.com" target="_blank">汽车报价网 </a></li><li><a href="http://www.cntui.cn" target="_blank">b2b电子商务 </a></li><li><a href="http://www.wxqgl.net" target="_blank">桂林旅游 </a></li><li><a href="http://www.7895.com" target="_blank">电气设备 </a></li></ul></div><script type="text/javascript">	function selectNet() {
		var netF = document.getElementById("net").style.display;
		if (netF == "none") {
			document.getElementById("net").style.display = "block";
		} else if (netF == "block") {
			document.getElementById("net").style.display = "none";
		}
	}

	function openGw() {
		document.getElementById("net").style.display = "none";
	}

	function openYzt() {
		document.getElementById("net").style.display = "none";
	}
</script><!-- coryright --><div class="clear"></div><div class="pageFooter"><div class="pageFooterText"><div class="footerLeft"><span><a href="http://www.pahaoche.com/about.w" target="_top">关于我们</a></span>| <span><a
				href="http://www.pahaoche.com/hire.w" target="_top">诚聘英才</a></span></div><div class="footerRight fr"><div class="pft_link fr"><input type="button" class="pft_btn fr" id="" onkeydown=""
					onclick="selectNet()" /><input type="text" class="pft_inp fl"
					readonly value="平安集团旗下网站" onclick="selectNet()" /><div id="net" style="display: none;"><ul class="sel_pftlink" id="pftlink"
						style="position: absolute; clear: both; overflow-y: hidden; height: 80px;"><li><a href="http://www.pingan.com/index.shtml"
							onclick="openGw()" target="_blank"> 中国平安官网 </a></li><li><a href="http://one.pingan.com/" onclick="openYzt()"
							target="_blank"> 平安一账通 </a></li><li><a href="http://www.4008000000.com/?WT.mc_id=ec03-pahaoche-001" 
						target="_blank">平安直通保险</a></li></ul></div></div><a href="#"><img src="__PUBLIC__/new_pa/images/pa_logo.gif" /></a></div></div><div class="copyright">		郑重提示：本公司从不上门现金收车，一旦发现请拨打4009-686-868举报，为保护您合法权益，车辆成交烦请务必到平安好车门店办理手续，谨防假冒。<br />		CopyRight © 2013 pahaoche.com 沪ICP备13010056号 版权所有 上海平安汽车电子商务有限公司
	</div></div><script>	$(function() {
		jQuery.fn.center = function(loaded) {
			var obj = this;
			body_width = parseInt($(window).width());
			body_height = (document.body.clientHeight < document.documentElement.clientHeight) ? document.body.clientHeight
					: document.documentElement.clientHeight;
			block_width = parseInt(obj.width());
			block_height = parseInt(obj.height());

			left_position = parseInt((body_width / 2) - (block_width / 2)
					+ $(window).scrollLeft());
			if (body_width < block_width) {
				left_position = 0 + $(window).scrollLeft();
			}

			top_position = parseInt((body_height / 2) - (block_height / 2)
					+ $(window).scrollTop());
			if (body_height < block_height) {
				top_position = 0 + $(window).scrollTop();
			}

			if (!loaded) {

				obj.css({
					'position' : 'absolute'
				});
				obj.css({
					'top' : top_position,
					'left' : left_position
				});
				$(window).bind('resize', function() {
					obj.center(!loaded);
				});
				$(window).bind('scroll', function() {
					obj.center(!loaded);
				});

			} else {
				obj.stop();
				obj.css({
					'position' : 'absolute'
				});
				obj.animate({
					'top' : top_position
				}, 200, 'linear');
			}
		}
	});
</script><!-- 百度pvuv统计代码 --><script type="text/javascript">	var _bdhmProtocol = (("https:" == document.location.protocol) ? " https://"
			: " http://");
	document
			.write(unescape("%3Cscript src='"
					+ _bdhmProtocol
					+ "hm.baidu.com/h.js%3Fb593fe6dbbc3a165ec20a9a9512890f5' type='text/javascript'%3E%3C/script%3E"));
</script><!-- 好车百度统计代码 2014-02-14 Updated Start --><script type="text/javascript">var _bdhmProtocol = (("https:" == document.location.protocol) ? " https://" : " http://");
document.write(unescape("%3Cscript src='" + _bdhmProtocol + "hm.baidu.com/h.js%3F69ef61b339394883895a6da0ee2a06bc' type='text/javascript'%3E%3C/script%3E"));
</script><!-- 好车百度统计代码 2014-02-14 Updated End --><script type="text/javascript">	var cnzz_protocol = (("https:" == document.location.protocol) ? " https://" : " http://");document.write(unescape("%3Cspan id='cnzz_stat_icon_5811873'%3E%3C/span%3E%3Cscript src='" + cnzz_protocol + "s23.cnzz.com/stat.php%3Fid%3D5811873%26show%3Dpic1' type='text/javascript'%3E%3C/script%3E"));

	$("#cnzz_stat_icon_5811873").hide();
</script></body></html><?php endif; ?>