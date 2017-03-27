<? if(!defined('IN_TIPASK')) exit('Access Denied'); global $starttime,$querynum;$mtime = explode(' ', microtime());$runtime=number_format($mtime['1'] + $mtime['0'] - $starttime,6); $setting=$this->setting;$user=$this->user;$headernavlist=$this->nav;$regular=$this->regular; ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>【上海汽车网|上海车市_上海汽车报价_上海汽车团购服务】_纵横携车网1</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta content="all" name="robots" />
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />
    <meta content="上海汽车网，上海汽车报价，上海车市最新报价，上海汽车报道，上海易车网，上海汽车团购服务"
        name="keywords">
    <meta content="纵横携车网为您提供武汉车市行情、纵横携车网汽车市场行情、纵横携车网汽车价格、纵横携车网汽车报价信息。纵横携车网车市最新报价，优惠信息，纵横携车网新车及经销商信息，纵横携车网汽车团购服务等，是您选车购车的第一网络平台"
        name="description">
    <!--页头导航_ICON开始zd-->
<link rel="Shortcut Icon" href="../Public/note/images/icon16.ico" />
    <!--页头导航_ICON结束-->
    <!--CSS-->
    
<link rel="stylesheet" type="text/css" href="../Public/new/style/index_style-20121120135046.css" /> 
<script src="../Public/Js/DatePicker/WdatePicker.js" type="text/javascript"></script>
<script src="../Public/think/jquery-1.6.2.min.js" type="text/javascript"></script>
<script src="../Public/Js/note/note_script.js" type="text/javascript"></script>                
<script src="../Public/Js/com.js" type="text/javascript"></script>

<link href="../Public/new/style/master_2009.v201211260.css" type="text/css" rel="stylesheet" />
<link href="../Public/new/style/askweb_2011.v201211260.css" type="text/css" rel="stylesheet" />
<link href="../Public/new/style/index1039_2009.v201211260.css" type="text/css" rel="stylesheet" />

<link href="<?=SITE_URL?>css/default/ask.css" rel="stylesheet" type="text/css" /><? $toolbars="'".str_replace(",", "','", $setting['editor_toolbars'])."'"; ?><script type="text/javascript">
var g_site_url='<?=SITE_URL?>';g_prefix='<?=$setting['seo_prefix']?>';g_suffix='<?=$setting['seo_suffix']?>';editor_options={toolbars:[[<?=$toolbars?>]],wordCount:<?=$setting['editor_wordcount']?>,elementPathEnabled:<?=$setting['editor_elementpath']?>};messcode='<?=$setting['code_message']?>';</script>
<script src="<?=SITE_URL?>js/jquery.js" type="text/javascript"></script>
<script src="<?=SITE_URL?>js/dialog.js" type="text/javascript"></script>
<script src="<?=SITE_URL?>js/common.js" type="text/javascript"></script>
<?=$setting['seo_headers']?>
    <!--JS-->
</head>
<body class="on_festival">
    <script>
        var currentCityId = '1201';
        var centerCityId = '1201';
        var currentCityName = '上海';
        var currentCityEName = 'shanghai';
        var centerName = '上海';
        var centerEName = 'shanghai';
        var navCityName = '上海';
        var navCityEName = 'shanghai';
        var navCityid = '1201';
    </script>
<div class="close_festival">
  <div id="close_f">关闭节日主题</div>
</div>
    <!--201202_首页_全屏广告-->
<script type="text/javascript">
var BitautoCityForAd = currentCityName;
</script>

<ins id="div_432996f2-7573-4a9a-bef1-ca65a80fafc0" type="ad_play" adplay_IP="" adplay_AreaName=""  adplay_CityName=""    adplay_BrandID=""  adplay_BrandName=""  adplay_BrandType=""  adplay_BlockCode="432996f2-7573-4a9a-bef1-ca65a80fafc0"> </ins>

<!--201202_首页_顶部登录内容-->    
<!--登录条开始-->
    <div class="bit_top" style="border-bottom:1px solid #CCC">
        <div class="bit950">
            <!-- <ul>
                <li><a target="_blank" href="#">纵横携车网</a></li>
<li><a target="_blank" href="#">纵横携车网</a></li>
<li><a target="_blank" href="#">纵横携车网</a></li>
<li><a target="_blank" href="#">汽车报价</a></li>
<li><a target="_blank" href="#">纵横携车网</a></li>
    <li class="last"><a target="_blank" href="#">纵横携车网</a></li>
            </ul> -->
            <!--网站导航-->
            <div class="bt_login_box">
        	<ul>
            	<li class="last bit_link"  id="login_navhover">
        	<strong><a href="../index.php/myhome" target="_blank">个人中心</a></strong>
        
          
        		</li>
            </ul>
        </div>
        <!--用户登陆-->
           <div class="bt_login_box bt_login_box_l" id="divLoginDivID">
            <? if(0!=$user['uid']) { ?>            	<ul>
            		<li>您好,</li>
            		<li onmouseout="" onmouseover="" class="bit_link">
            			<strong>
            				<a href="" id="aHeaderUserName" target="_blank">
            				<?=$user['username']?>
            				<em></em></a><sub></sub>
            			</strong>
            		</li>
            		<!--index-->
            		<li onmouseout="" onmouseover="" class="bit_link" id="myfocuscar">
            			<strong><a href="../index.php/myhome/mycarlist">我的车辆<em></em></a><sub></sub></strong>
            		</li><!--/index-->
            		<li class="lastB"><a href="../index.php/public/logout" target="_self">[退出]</a></li></ul>
            <? } else { ?>            	<ul>
<li>
<label>帐号</label>
<input type="text" id="txtUserNameTopHeader" name="txtUserNameTopHeader" class="bit_loginInput" >
</li>
<li>
<label>密码</label>
<input type="password" id="txtUserPwdHeader" name="txtUserPwdHeader" class="bit_loginInput" >
</li>
<li class="login">
<input type="button" id="btnUserLoginHeader" class="bit_logintop" value="登录" onclick="check_login();">
</li>
<li class="login_0">
<a href="../index.php/member/add" target="_blank">注册</a>
</li>
<!-- <li onmouseout="document.getElementById('ulSqure').style.display='none';this.className='bit_link';" onmouseover="document.getElementById('ulSqure').style.display='';this.className='bit_link bit_hover';" class="bit_link">
<strong><a href="javascript:void(0)">其它帐号登录<em></em></a><sub></sub></strong>
<ul style="display: none;" id="ulSqure">
<li>
<a onclick="Bitauto.Login.OtherWebSiteLogin('sina',600,410);" href="javascript:void(0)" title="新浪微博帐号登录" class="sina" target="_self">新浪微博</a>
</li>
<li>
<a onclick="Bitauto.Login.OtherWebSiteLogin('qq',450,320);" href="javascript:void(0)" title="QQ帐号登录" class="qq" target="_self">QQ帐号</a>
</li>
<li>
<a onclick="Bitauto.Login.OtherWebSiteLogin('renren',560,340);" href="javascript:void(0)" class="renren" title="人人网帐号登录" target="_self">人人网</a>
</li>
<li>
<a onclick="Bitauto.Login.OtherWebSiteLogin('kaixin',520,400);" href="javascript:void(0)" class="kaixin" id="kx001_btn_login" target="_self">开心网</a>
</li>
<li>
<a onclick="Bitauto.Login.OtherWebSiteLogin('baidu',560,340);" href="javascript:void(0)" class="baidu" target="_self">百度</a>
</li>
<li class="last">
<a onclick="Bitauto.Login.OtherWebSiteLogin('taobao',520,520);" href="javascript:void(0)" class="taobao" target="_self">淘宝网</a>
</li>
</ul>
</li> -->
</ul><? } ?></div>
            <!--用户登陆-->

        </div>
    </div>
    <!--登录条结束-->

    <!--登录条结束-->
<!--顶通提示开始-->

<!--顶通提示结束-->

    <!--头部开始-->
    <div class="header_styleask">
        <div class="bitauto_logo">
        </div>
        <!--页头导航_yiche_LOGO开始-->
        <h1 ><a href="#">纵横携车网</a></h1>

        <!--页头导航_yiche_LOGO结束-->
        <div class="city_name " id="city_name_boxhover">
            <a href="#">上海站</a><em></em>
            <sub></sub><ul style="display:none;" id="city_name_box">    	
  <li><a href="#" target="_blank">北京</a></li>
  <li><a href="#" target="_blank">天津</a></li>
  <li><a href="#" target="_blank">上海</a></li>
  <li><a href="#" target="_blank">杭州</a></li>
  <li><a href="#" target="_blank">宁波</a></li>
  <li><a href="#" target="_blank">南京</a></li>
  <li><a href="#" target="_blank">苏州</a></li>
  <li><a href="#" target="_blank">合肥</a></li>
  <li><a href="#" target="_blank">广州</a></li>
  <li><a href="#" target="_blank">深圳</a></li>
  <li><a href="#" target="_blank">福州</a></li>
  <li><a href="#" target="_blank">南宁</a></li>
  <li><a href="#" target="_blank">武汉</a></li>
  <li><a href="#" target="_blank">郑州</a></li>
  <li><a href="#" target="_blank">长沙</a></li>
  <li><a href="#" target="_blank">南昌</a></li>
  <li><a href="#" target="_blank">重庆</a></li>
  <li><a href="#" target="_blank">成都</a></li>
  <li><a href="#" target="_blank">西安</a></li>
  <li><a href="#" target="_blank">兰州</a></li>
  <li><a href="#" target="_blank">昆明</a></li>
  <li><a href="#" target="_blank">石家庄</a></li>
  <li><a href="#" target="_blank">哈尔滨</a></li>
  <li><a href="#" target="_blank">沈阳</a></li>
  <li><a href="#" target="_blank">大连</a></li>
  <li><a href="#" target="_blank">长春</a></li>
  <li><a href="#" target="_blank">济南</a></li>
  <li><a href="#" target="_blank">青岛</a></li>
  <li><a href="#" target="_blank">太原</a></li>
  <li><a href="#" target="_blank">呼和浩特</a></li>
  <li class="lastcity"><a href="#"  target="_blank">341个城市&gt;&gt;</a></li>
</ul>
</div>
<div style="width:450px; height:49px; margin:0px; padding:0px; float:right;" id="head-min"><img src="/Public/new/images/nav.jpg" /></div>
<!-- <div class="ad_w700_h40"> <ins id="div_f4830636-f3fb-403c-9909-67355b7d639b" type="ad_play" adplay_IP="" adplay_AreaName=""  adplay_CityName=""    adplay_BrandID=""  adplay_BrandName=""  adplay_BrandType=""  adplay_BlockCode="f4830636-f3fb-403c-9909-67355b7d639b"> </ins>
</div> -->

    </div>
    <!--头部结束-->
    <!--导航开始-->
    <div class="publicTabNewask">
<ul id="ulForTempClickStat" class="tab">
<li><a id="treeNav_chexing" target="_blank" href="/">首页</a></li>
<li><a id="treeNav_pingce" target="_blank" href="../index.php/order">预约4S店</a></li>
<li><a id="treeNav_shipin" target="_blank" href="../index.php/coupon">携车优惠券</a></li>
<li><a id="treeNav_daogou" target="_blank" href="/tipask">明星顾问疑答</a></li>
<li><a href="../index.php/article/articlelist" >后市场资讯</a></li>

<li class="last">
</li>
</ul>
</div>
<div id="bodyBox">
  <div id="WebcarsHeader" style="display: block; margin: 0 auto; width: 980px;">
    <!--[网站头部导航开始]-->
    <!-- logo -->
    <div gsn="askNav" class="AskwebLogo">
      <ul class="Logo">
        <li><a href="#"> <img src="images/nav2.jpg"

                            alt="携车纵横网" title="携车纵横网"></a></li>
      </ul>
      <form name="searchform"  action="<?=SITE_URL?>?question/search/3.html" method="post">
      <ul class="AskwebSearch">
        <li class="HotKeywords"><strong>热门：</strong> <a href="http://ask.webcars.com.cn/volkswagen/"

                        target="_blank">大众</a> <a href="http://ask.webcars.com.cn/benz-c_class/" target="_blank"> 奔驰C级</a> <a href="http://ask.webcars.com.cn/volkswagen-magotan/" target="_blank">迈腾</a> <a href="http://ask.webcars.com.cn/volkswagen-golf/" target="_blank">高尔夫</a> <a href="http://ask.webcars.com.cn/peugeot-508/"

                            target="_blank">标致508</a> <a href="http://ask.webcars.com.cn/kia-K2/" target="_blank"> 起亚K2</a> <a href="http://ask.webcars.com.cn/volkswagen-polo/" target="_blank">POLO</a> <a href="http://ask.webcars.com.cn/suzuki-swift/" target="_blank">雨燕</a> <a href="http://ask.webcars.com.cn/skoda-octavia/"

                            target="_blank">明锐</a> <a href="http://ask.webcars.com.cn/toyota-corolla/" target="_blank"> 卡罗拉</a> <a href="http://ask.webcars.com.cn/honda-civic/" target="_blank">思域</a></li>
        <li>
          <input id="kw" name="" type="text" class="inputAskwebSearch" maxlength="40" />
          <span class="font14 helpgray"><a href="http://ask.webcars.com.cn/help.html" target="_blank"> 帮助</a></span> </li>
        <li class="mt8">
          <input type="button" value="我要搜索" class="btnAskwebSearch" gsn="askTopQSearch" id="Button3"

                            onclick="search_submit()" />
          <input type="button" value="我要提问" class="btnAskwebSearch" gsn="askTopQComment" id="Button1"

                            onclick="ask_submit()" />
          <!-- <input type="button" value="手机提问" class="btnAskwebSearch" onclick="javascript:postQuestion()" />
          &nbsp;&nbsp;<a gsn="askFQ" href="javascript:void(0);" onclick="OpenObjWin('ShowWindow');"

                            class="font14 QuickQuestion">快速提问>></a> -->
          <div id="anr_tw" class="phone_tw" style="display: none;">
            <iframe frameborder="0" width="420" class="phone_iframe"></iframe>
            <div id="warpDiv1"  class="phone_in" style="position:absolute;z-index:3;">
              <div class="phone_tw_head clearfix"> <span class="phone_tw_head_l">万车知道 在线提问</span> <span id="Span1" class="phone_tw_head_r close" style="cursor: pointer"><a onClick="_gaq.push(['_trackEvent','AskHP_DXPT', 'Buttonclick ','关闭'])" gsn="askPostQClose" href="javascript:void">关闭</a></span> </div>
              <div class="phone_body">
                <h3> 您问题的回复将通过短信形式发送给您，全程免费！</h3>
                <div class="online_question">
                  <ul>
                    <li class="clearfix"><strong>发送号码：</strong><span class="on_r redColora">1069 0195 5959</span></li>
                    <li class="clearfix"><strong>接收号码：</strong><span class="on_r">
                      <input name="mobile" type="text" id="mobile" class="online_input" />
                      &nbsp;&nbsp;<a href="###" id="getVerificationCode" onclick="getVerificationCode();_gaq.push(['_trackEvent','AskHP_DXPT', 'Buttonclick ','获得验证码'])" style="cursor:hand">获取验证码>></a></span></li>
                    <li class="mt10 clearfix" id="dlverification"><strong>验证码：</strong><span class="on_r">
                      <input name="verificationcode" type="text" class="online_yzm" id="verificationcode"/>
                      <img src="http://images.webcars.com.cn/mainweb/images_2010/userimg/zhuceicocw.gif" id="error" style="display:none"> <img src="http://images.webcars.com.cn/mainweb/images_2010/userimg/zhuceicozq.gif" id="ok"  style="display:none"></span></li>
                    <li class="clearfix"><strong>问题：</strong>
                      <textarea name="" cols="" rows="" class="online_text" id="textContent"></textarea>
                    </li>
                    <li class="clearfix">
                      <input name="" type="button" value="提交" class="btn15 " onclick="CheckApply();_gaq.push(['_trackEvent','AskHP_DXPT', 'Buttonclick ','提交']);" />
                      <a gsn="asktest" href="http://ask.webcars.com.cn/submitquestion.html" target="_blank" class="tw_xq"  onClick="_gaq.push(['_trackEvent','AskHP_DXPT', 'Buttonclick ','万车知道'])">万车知道>></a> <a  onClick="_gaq.push(['_trackEvent','AskHP_DXPT', 'Buttonclick ','功能详情'])" gsn="askPostQInfo" href=" http://www.webcars.com.cn/review/20110705/52403.html" target="_blank" class="tw_xq">功能详情>></a> </li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
          <a href="#anr_tw" rel="facebox"></a>
          <input name="hdVersion" id="hdVersion" type="hidden" value="10517" />
        </li>
        <li> </li>
      </ul>
      </form>
      <div class="clear"> </div>
    </div>