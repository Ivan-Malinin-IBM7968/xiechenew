<?php if (!defined('THINK_PATH')) exit(); if($Think['const'].TOP_CSS == 'xc'): ?><!DOCTYPE html ><html ><head><meta charset="utf-8" /><!-- Google Web Master Tracking Start--><meta name="google-site-verification" content="8wmX5B2WFkCUepvfui4OFCwlvesXyiuy6tllvITgB28" /><!-- Google Web Master Tracking End--><meta name="baidu-site-verification" content="d6mO5rDwhc" /><!-- for com.cn --><meta name="baidu-site-verification" content="Vn7wCqQ1FE" /><!-- for net --><title><?php if(!empty($title)): echo ($title); else: ?>携车网,汽车保养维修预约,4S店折扣优惠,事故车维修预约<?php endif; ?></title><?php if(empty($meta_keyword)): ?><meta content="汽车保养,汽车维修,携车网,4S店预约保养,事故车维修" name="keywords"><?php else: ?><meta content="<?php echo ($meta_keyword); ?>" name="keywords"><?php endif; if(empty($description)): ?><meta content="汽车保养维修,事故车维修-首选携车网,全国唯一4S店汽车保养维修折扣的网站,最低5折起,还有更多团购套餐任您选,事故车维修预约,不花钱,还有返利拿4006602822"  name="description"><?php else: ?><meta content="<?php echo ($description); ?>" name="description"><?php endif; ?><script type="text/javascript" src="__PUBLIC__/Js/DatePicker/WdatePicker.js?v=<?php echo (C("VERSION_DATE")); ?>"></script><!--加载时间控件插件--><script type="text/javascript" src="__PUBLIC__/Js/com.js?v=<?php echo (C("VERSION_DATE")); ?>"></script><link rel="shortcut icon" href="__PUBLIC__/new_2/images/favicon.ico" type="image/x-icon"><link rel="stylesheet" type="text/css" href="__PUBLIC__/new_2/css/common.css?v=<?php echo (C("VERSION_DATE")); ?>" /><script src="__PUBLIC__/new_2/js/jquery.min.js" ></script><script src="__PUBLIC__/new_2/js/jquery-ui-1.9.2.custom.min.js" ></script><link rel="stylesheet" href="__PUBLIC__/new_2/css/redmond/jquery-ui-1.9.2.custom.css"><!-- ! GA track code start --><script src="__PUBLIC__/new_2/js/ga.js" ></script><!-- ! GA track code end --><?php if(!empty($canonical)): ?><link rel="canonical" href="<?php echo ($canonical); ?>"/><?php endif; ?></head><body ><!-- 头部Nav开始 --><div class='top-container' style="padding: 20px 0px; height: 90px;" ><div style="float: left;width:513px;margin-top:10px;position:relative" id='logo'><a href="http://www.xieche.com.cn" title='携车网,汽车保养维修预约,4S店折扣优惠,事故车维修预约' style="display:block;position:absolute;left:0px;top:0px;width:188px;height:90px">携车网</a><!-- <a href="javascript:void(0)" style="display:block;position:absolute;left:189px;top:0px;width:167px;height:90px" title="携车网,中国平安旗下投资企业">&nbsp;</a> --></div><div class="ad banner" style="margin:10px auto; width: 687px; height: 80px; float: left"><a href="http://www.xieche.com.cn/coupon-explosion" title="携车网 养车更养眼" target="_blank"><img src="__PUBLIC__/new_2/images/ac_explosion.jpg" alt="携车网 养车更养眼" width="687" height="80"></a></div></div><div class="nav-container"><div class="nav"><ul class='main-nav'><li style="width:1px;border-left:none;"></li><li><a href="__APP__/index.php" title="汽车保养,汽车维修,携车网,4S店预约保养,事故车维修,养车3折团">首页</a></li><li <?php if($current == 'order' ): ?>class="current"<?php endif; ?>><a href="__APP__/order">4S店售后预约</a></li><li <?php if($current == 'coupon' ): ?>class="current"<?php endif; ?>><a href="__APP__/coupon">4S店售后团购</a></li><!-- <li <?php if($current == 'noticelist' ): ?>class="current"<?php endif; ?>><a href="__APP__/noticelist">优惠速递</a></li> --><li <?php if($current == 'articlelist' ): ?>class="current"<?php endif; ?>><a href="__APP__/articlelist">用车心得</a></li><li <?php if($current == 'shiguche' ): ?>class="current"<?php endif; ?>><a href="__APP__/shiguche" target="_blank">事故车维修</a></li><li <?php if($current == 'yangchetuan' ): ?>class="current"<?php endif; ?>><a href="__APP__/coupon-explosion" target="_blank">养车3折团</a><span class="new"></span></li><li <?php if($current == 'carservice' ): ?>class="current"<?php endif; ?>><a href="__APP__/carservice" target="_blank">府上养车</a><span class="new"></span></li></ul><div class="login-box"><?php if(!empty($_SESSION['uid'])): ?><span >您好
				<?php if(isset($_SESSION['username'])): echo ($_SESSION['username']); else: if(isset($_SESSION['cardid'])): echo ($_SESSION['cardid']); else: echo ($_SESSION['mobile']); endif; endif; ?></span><?php else: ?><span class='not-user'><a href="__APP__/Public/login?jumpUrl=%2Fmyhome" rel="nofollow">登陆 / 注册</a><!-- <a href="<?php echo URL('/member/add',array()); echo C('HTML_URL_SUFFIX');?>" rel="nofollow"></a> --></span><?php endif; ?><span class='my-account'>我的账户</span><?php if(!empty($_SESSION['uid'])): ?><b class="dropdown-arrow-down"></b><div class="account-dropdown"><ul><li>我的订单</li><li class="sub"><a href="<?php echo URL('/myhome/index',array()); echo C('HTML_URL_SUFFIX');?>">未消费订单</a><a href="<?php echo URL('/myhome/index',array()); echo C('HTML_URL_SUFFIX');?>">未评价订单</a></li><li><a href="<?php echo URL('/myhome/myorder',array()); echo C('HTML_URL_SUFFIX');?>">我的维修保养订单</a></li><li>资产中心</li><li><a href="<?php echo URL('/myhome/mycoupon1',array()); echo C('HTML_URL_SUFFIX');?>">我的现金券</a></li><li><a href="<?php echo URL('/myhome/mycoupon2',array()); echo C('HTML_URL_SUFFIX');?>">我的套餐券</a></li><li><a href="<?php echo URL('/myhome/my_salecoupon',array()); echo C('HTML_URL_SUFFIX');?>">我的抵用券</a></li><li><a href="<?php echo URL('/myhome/my_account',array()); echo C('HTML_URL_SUFFIX');?>">我的账户余额</a></li><li><a href="<?php echo URL('/myhome/mypoints',array()); echo C('HTML_URL_SUFFIX');?>">我的积分</a></li><li>个人中心</li><li><a href="<?php echo URL('/myhome/myinfo',array()); echo C('HTML_URL_SUFFIX');?>">我的信息</a></li><li><a href="<?php echo URL('/myhome/my_safe',array()); echo C('HTML_URL_SUFFIX');?>">账户安全</a></li><li><a href="<?php echo URL('/myhome/my_car',array()); echo C('HTML_URL_SUFFIX');?>">我的车辆</a></li><li><a href="<?php echo URL('/myhome/my_comment',array()); echo C('HTML_URL_SUFFIX');?>">我的点评</a></li><li class="log-out"><a href="<?php echo URL('/public/logout',array()); echo C('HTML_URL_SUFFIX');?>" target="_self">[退出]</a></li></ul></div><?php endif; ?></div></div></div><!-- 头部Nav结束 --><script language="javascript" type="text/javascript">    var secs =10; //倒计时的秒数
    var URL ;
    function Load(url){
        URL =url;
        for(var i=secs;i>=0;i--)
        {
            window.setTimeout('doUpdate(' + i + ')', (secs-i) * 1000);
        }
    }

    function doUpdate(num)
    {
        document.getElementById("ShowDiv").innerHTML = '将在<strong><font color=red> '+num+' </font></strong>秒后自动转入<a  href="http://www.xieche.com.cn" class="link_1">携车网首页</a>，请稍候...' ;
        if(num == 0) { window.location=URL;  }
    }
    $(function(){
	Load('http://www.xieche.com.cn');
    })
  </script><style>    .error{margin: 60px auto 300px;}
    .error h2{margin: 0 auto; text-align: center; font-size: 18px; font-weight: normal; }
    .error #ShowDiv{ margin: 20px; font-size: 14px; font-weight: normal; text-align: center;}
</style><div class="error" ><h2>抱歉，您请求的页面现在无法打开！</h2><div id="ShowDiv">将在<strong><font color="red">10</font></strong>秒后自动转入<a  href="http://www.xieche.com.cn" class="link_1">携车网首页</a>，请稍候...</div></div><!-- right widget START --><div id="widget_right"><ul><li id="back-top"><a href="javascript:void(0)"></a></li><li id="service-chat"><a href="http://qiao.baidu.com/v3/?module=default&controller=im&action=index&ucid=6534618&type=n&siteid=3474866" target="_blank"><!-- <div id="BDBridgeFixedWrap"></div> --></a></li><li id="wechat-xieche"><div id="wechat-qrcode"></div><a href="javascript:void(0)"></a></li></ul></div><!-- right widget END --><!--<script src="__PUBLIC__/new_2/js/jquery.min.js" ></script><script src="__PUBLIC__/new_2/js/jquery-ui-1.9.2.custom.min.js" ></script>--><script src="__PUBLIC__/new_2/js/base.js" ></script><div class="clear"></div><div class="w1200 footer"><ul class="row gy"><li><a href="__APP__/about/4" target="_blank" rel="nofollow">关于我们</a>|</li><li><a href="__APP__/about/16" target="_blank" rel="nofollow">联系我们</a>|</li><li><a href="__APP__/about/24" target="_blank" rel="nofollow">服务协议</a>|</li><li><a href="__APP__/about/9" target="_blank">如何预约维修保养 </a>|</li><li style="display:none;"><a href="__APP__/Sitemap.xml">xml地图</a>|</li><li><a href="__APP__/sitemap.html">网站地图</a></li></ul><div class="row ba">
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
</script></head><body><div class="topContainer"><!-- topContent Start --><div class="topContent"><div class="Header"><a href="http://www.pahaoche.com" target="_top" class="logo"></a><div class="Head_r"><div class="log_buyer"><a href="http://jp.pahaoche.com/jp/perCenter.do" target="_top">经销商登录</a></div><div class="top_cx"><a href="http://jp.pahaoche.com/jp/seller/seller.do" target="_top">竞价查询</a></div><div class="hotline" style="margin-top: 30px;"><img src="__PUBLIC__/new_pa/images/hotline.png" /></div></div></div><!--  导航   --><div class="subNav"><ul class="navbar"><li><a href="http://www.pahaoche.com/" target="_top">首页</a></li><li><a href="http://www.pahaoche.com/yuyue.w" id="m1" target="_blank">我要卖车</a><ul style="display:none;"><li><a href="http://www.pahaoche.com/yuyue.w" target="_blank">预约卖车</a></li><li><a href="http://www.pahaoche.com/helpQA.w">卖车流程</a></li></ul></li><li><a href="http://baoyang.pahaoche.com">我要养车</a></li><li><a href="http://www.pahaoche.com/mapStore.w" target="_top">门店地图</a></li><li><a href="http://www.pahaoche.com/consulting.w" target="_top">好车资讯</a></li><li><a href="http://www.pahaoche.com/yuyue.w" target="_blank">好车活动</a></li><li><a id="chexianhead" style="float:left;display:block;width:45px;text-align:right;" href="http://www.4008000000.com/cpchexian/bd/bd.shtml?WT.mc_id=ec03-pahaoche-001" target="_blank">车险</a><a id="chedaihead" style="float:left;display:block;width:45px;text-align:left;text-indent:5px;" href="http://daikuan.pingan.com/qichedaikuan.shtml?WT.mc_id=ec03-pahaoche-001" target="_blank">车贷</a></li><li><a href="http://www.pahaoche.com/about.w" target="_top">关于我们</a></li></ul></div></div><!-- topContent End --></div><script language="javascript" type="text/javascript">    var secs =10; //倒计时的秒数
    var URL ;
    function Load(url){
        URL =url;
        for(var i=secs;i>=0;i--)
        {
            window.setTimeout('doUpdate(' + i + ')', (secs-i) * 1000);
        }
    }

    function doUpdate(num)
    {
        document.getElementById("ShowDiv").innerHTML = '将在<strong><font color=red> '+num+' </font></strong>秒后自动转入<a  href="http://www.xieche.com.cn" class="link_1">携车网首页</a>，请稍候...' ;
        if(num == 0) { window.location=URL;  }
    }
    $(function(){
	Load('http://www.xieche.com.cn');
    })
  </script><style>    .error{margin: 60px auto 300px;}
    .error h2{margin: 0 auto; text-align: center; font-size: 18px; font-weight: normal; }
    .error #ShowDiv{ margin: 20px; font-size: 14px; font-weight: normal; text-align: center;}
</style><div class="error" ><h2>抱歉，您请求的页面现在无法打开！</h2><div id="ShowDiv">将在<strong><font color="red">10</font></strong>秒后自动转入<a  href="http://www.xieche.com.cn" class="link_1">携车网首页</a>，请稍候...</div></div><script type="text/javascript">	function selectNet() {
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