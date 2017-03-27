<? if(!defined('IN_TIPASK')) exit('Access Denied'); include template('header'); ?>
<link href="../Public/new/style/ask.css" type="text/css" rel="stylesheet" />
<script src="../Public/Js/car_select/car_data.js" type="text/javascript"></script>
<script src="../Public/Js/car_select/normal.js" type="text/javascript"></script>
    <!-- 导航 -->
    <div gsn="askNav">
      <h1 style="display: none"> 万车知道-全国最专业的汽车问答平台</h1>
      <div class="AskwebMenu mb10">
        <div class="MenuLeft"></div>
        <div class="MenuCenter">
          <ul class="AskwebMenuLeft">
            <li id="ctl00_cphNav_AskNavBar1_NavTab1" class="MenuTabOn"><a gsn="askNavAsk" href="/" title="万车知道" target="_blank">携车纵横网首页</span></a></li>
            <!-- <li id="ctl00_cphNav_AskNavBar1_NavTab2"><a gsn="askNavBrands"  href="/brands/" title="品牌分类" target="_blank">品牌分类</a></li>
            <li id="ctl00_cphNav_AskNavBar1_NavTab3"><a gsn="askNavQuestion"  href="/question/" title="关键字分类" target="_blank">关键字分类</a></li>
            <li id="ctl00_cphNav_AskNavBar1_NavTab4"><a gsn="askNavExperts"  href="/ExpertsTeam.html" title="专家团" target="_blank">专家团</a></li>
            <li id="ctl00_cphNav_AskNavBar1_NavTab5"><a gsn="askNavHotQuestion"  href="/Question2.html" title="热点问题" target="_blank">热点问题</a></li>
            <li id="ctl00_cphNav_AskNavBar1_NavTab6"><a gsn="askNavUComment"  href="/UserCommentInfo.html" title="网友问答" target="_blank">网友问答</a></li>
            <li id="ctl00_cphNav_AskNavBar1_NavTab7"><a gsn="askNavKing"  href="/King.html"  title="武林王" target="_blank">武林王</a></li>
             -->
          </ul>
          <ul class="SubAskwebRight font12">
            <li><a href="/mobile/" class="mobile" target="_blank">手机版</a><a href="http://t.sina.com.cn/wanchezhidao/" class="weibo" target="_blank">官方微博</a></li>
          </ul>
        </div>
        <div class="MenuRight"></div>
        <div class="clear"></div>
      </div>
    </div>
    <!--[网站头部导航结束]-->
    <!--[通栏广告开始]-->
    <div id="swfs" style="text-align: center;" class="tablebannerbottom">
      <!--默认通栏广告-->
    </div>
    <div id="divBanner" style="text-align: center;" class="tablebannerbottom"> </div>
    <!--[通栏广告结束]-->
    <!--[网站当前位置]-->
  </div>
  <div id="wrap">
    <div id="contentBox" class="clearfix">
      <div class="w700 floatLeft">
        <div class="firstBox clearfix">
          <!-- 左边分类开始-->
          <div class="firstLeft">
            <!-- 问题分类-->
            <div gsn="askCarsCategory" class="mt10">
              <div class="blueLine indexfenlei">
                <h5>问题分类<span class="floatRight redmorew"><a gsn="ucCategoryMore" href="<?=SITE_URL?>?c-all.html" target="_blank">更多>></a></span></h5>
                <? $categorylist=$this->fromcache('categorylist'); ?>                
<? if(is_array($categorylist)) { foreach($categorylist as $category1) { ?>
                <div class="flContent" gsn="askQwxby">
                  <h6><a href="<?=SITE_URL?>?c-<?=$category1['id']?>.html" target="_blank"><?=$category1['name']?></a><span class="categorynum">(<?=$category1['questions']?>)</span></h6>
                  <ul class="flContentul">
                  
<? if(is_array($category1['sublist'])) { foreach($category1['sublist'] as $index => $category2) { ?>
                    <li><a target="_blank" href="<?=SITE_URL?>?c-<?=$category2['id']?>.html" title="<?=$category2['name']?>"><?=$category2['name']?>(<?=$category2['questions']?>)</a></li>
                  
<? } } ?>
                    <div class="clear"> </div>
                  </ul>
                </div>
                
<? } } ?>
                
              </div>
            </div>
          </div>
          <!-- 左边分类结束-->
          <div class="widthR468">
          <? $expertlsit=$this->fromcache('expertlsit'); ?>          <div id="searchdoctor"><!--找好大夫-->
<div class="l_m_title">
<div class="l_m_t_l"><a target="_blank" href="http://www.haodf.com/jibing/list.htm"><img width="107" height="40" src="http://i1.hdfimg.com/homepage/images/index_tmbg.gif"></a></div>
<div class="l_m_t_c">收录全国 <span class="orange3"><?=$expertlsit['brand_num']?></span> 个品牌 <span class="orange3"><?=$expertlsit['expert_num']?></span> 位顾问</div>
<div class="l_m_t_r"></div>
</div>
<div class="l_m_main">
<div class="m_title">
<div class="m_title_bg"><span class="m_title_tit">按品牌找顾问</span></div>
</div>
<div class="find_jb">
<div class="find_jb_l">

          
<? if(is_array($expertlsit['category'])) { foreach($expertlsit['category'] as $expert) { ?>
<strong><?=$expert['name']?></strong>
<ul>
          
<? if(is_array($expert['user'])) { foreach($expert['user'] as $user) { ?>
<li><a href="http://www.haodf.com/jibing/yigan/daifu.htm" class="black_link" target="_blank"><?=$user['username']?></a></li>
<? } } ?>
</ul>
<div class="clear"></div>
<? } } ?>
</div>

<div class="clear"></div>
</div>
<div class="clear"></div>
</div>
<!--找好大夫-->
</div>
            <!-- 品牌分类开始-->
            <div class="yellowBg yellowLine">
              <div class="fenleiContent">
                <h5> <span class="floatLeft">品牌分类</span><span class="floatRight redmorew"><a gsn="ucCategoryMore" href="<?=SITE_URL?>?c-all.html" target="_blank">更多>></a></span>
                  <div class="clear"></div>
                </h5>
                <ul gsn="askBrandCategory">
                <? $categorylist=$this->fromcache('categorylist'); ?>                
<? if(is_array($categorylist)) { foreach($categorylist as $category1) { ?>
                  <li>·<a title="<?=$category1['name']?>" href="<?=SITE_URL?>?c-<?=$category1['id']?>.html" target="_blank"><span><?=$category1['name']?>(<?=$category1['questions']?>)</span></a></li>
                
<? } } ?>
                  <div class="clear"> </div>
                </ul>
              </div>
              <div gsn="ucCategorySearch" class="flSearch">
                <ul>
                  <li>搜索：</li>
                  <form>
                    <li>
                    <select id="get_brand" name="brand_id" style= "width:120px" onchange="comp_brlist('get_brand','get_series', 'get_model');">
</select>
&nbsp;
                    <select id="get_series" name="series_id" style= "width:120px" disabled onchange="comp_yearlist('get_brand', 'get_series', 'get_model');">
   				</select>
   				
   				<!-- <select id="get_model" name="model_id" style= "width:120px" disabled>
</select> -->
                      &nbsp;
                      <input name="" type="button" class="searchButtonGray" id="SearchLink"  onclick="searchbrand();" value="搜问题"/>
                    </li>
                  </form>
                  
<script type="text/javascript">
comp_fctlist("get_brand", "get_series", "get_model");
function searchbrand(){
var brand_id = $("#get_brand").val();
var series_id = $("#get_series").val();
if(series_id>0){
$.ajax({
                        type: "post",
                        url: "<?=SITE_URL?>?category/ajaxgetcidbyseriesid.html",
                        data: "series_id="+series_id,
                        success: function(cid){
                            if(cid){
                            	window.location.href="/tipask/?c-"+cid+".html";
                            }
                        }
                    });
}else if(brand_id){
$.ajax({
                        type: "post",
                        url: "<?=SITE_URL?>?category/ajaxgetcidbybrandid.html",
                        data: "brand_id="+brand_id,
                        success: function(cid){
                            if(cid){
                            	window.location.href="/tipask/?c-"+cid+".html";
                            }
                        }
                    });
}
}
   </script>
                  <div class="clear"></div>
                </ul>
              </div>
            </div>
            <!-- 品牌分类结束-->
            <div class="mt10 a2b">
              <div id="rollExpert" style="height: 48px; overflow: hidden;">
                <ul>
                  <a target="_blank" href="/ExpertReply_U117.html" alt="广本宝辰致雅郭强专家" title="广本宝辰致雅郭强专家"><img src="http://files.webcars.com.cn/CarsPhoto/1039/20120109154344.jpg"></a>
                  <li class="ydot clearfix"><span><a href="/ExpertReply_U117.html" target="_blank">广本宝辰致雅郭强专家</a></span><span>所属单位：<a href="http://dealer.webcars.com.cn/dealer/20953/default.html" target="_blank" title="广汽本田宝辰致雅">广汽本田宝辰致雅</a></span></li>
                  <li class="clearfix"><span>销售电话：<strong>400-609-1888</strong></span><span>售后电话：<strong>400-609-1888</strong></span></li>
                </ul>
                <ul>
                  <a target="_blank" href="/ExpertReply_U119.html" alt="华日通技术总监马振江" title="华日通技术总监马振江"><img src="http://files.webcars.com.cn/CarsPhoto/1039/20120319152334.jpg"></a>
                  <li class="ydot clearfix"><span><a href="/ExpertReply_U119.html" target="_blank">华日通技术总监马振江</a></span><span>所属单位：<a href="http://dealer.webcars.com.cn/dealer/10513/default.html" target="_blank" title="华日通一汽马自达">华日通一汽马自达</a></span></li>
                  <li class="clearfix"><span>销售电话：<strong>400-669-1368</strong></span><span>售后电话：<strong>400-609-1888</strong></span></li>
                </ul>
                <ul>
                  <a target="_blank" href="/ExpertReply_U90.html" alt="联拓奥通奥迪魏来专家" title="联拓奥通奥迪魏来专家"><img src="http://files.webcars.com.cn/CarsPhoto/1039/20110512141750.jpg"></a>
                  <li class="ydot clearfix"><span><a href="/ExpertReply_U90.html" target="_blank">联拓奥通奥迪魏来专家</a></span><span>所属单位：<a href="http://dealer.webcars.com.cn/dealer/317/default.html" target="_blank" title="联拓奥通奥迪">联拓奥通奥迪</a></span></li>
                  <li class="clearfix"><span>销售电话：<strong>400-688-8935</strong></span><span>售后电话：<strong>400-609-1888</strong></span></li>
                </ul>
                <ul>
                  <a target="_blank" href="/ExpertReply_U29.html" alt="北方华驿技术经理王骏" title="北方华驿技术经理王骏"><img src="http://files.webcars.com.cn/CarsPhoto/1039/20120118154533.jpg"></a>
                  <li class="ydot clearfix"><span><a href="/ExpertReply_U29.html" target="_blank">北方华驿技术经理王骏</a></span><span>所属单位：<a href="http://dealer.webcars.com.cn/dealer/516/default.html" target="_blank" title="北方华驿一汽大众">北方华驿一汽大众</a></span></li>
                  <li class="clearfix"><span>销售电话：<strong>400-609-1888</strong></span><span>售后电话：<strong>400-609-1888</strong></span></li>
                </ul>
                <ul>
                  <a target="_blank" href="/ExpertReply_U107.html" alt="雪铁龙王辉专家" title="雪铁龙王辉专家"><img src="http://files.webcars.com.cn/CarsPhoto/1039/20110715114727.jpg"></a>
                  <li class="ydot clearfix"><span><a href="/ExpertReply_U107.html" target="_blank">雪铁龙王辉专家</a></span><span>所属单位：<a href="http://dealer.webcars.com.cn/dealer/545/default.html" target="_blank" title="东风雪铁龙金泰">东风雪铁龙金泰</a></span></li>
                  <li class="clearfix"><span>销售电话：<strong>400-609-1888</strong></span><span>售后电话：<strong>400-609-1888</strong></span></li>
                </ul>
              </div>
            </div>
            <script type="text/javascript">

                            var Roll = {

                                Startmarquee: function(id, lh, speed, delay) {

                                    var t;

                                    var o = document.getElementById(id);

                                    var p = false;

                                    o.innerHTML += o.innerHTML;

                                    o.scrollTop = 0;

                                    var oscrollHeight = 0; //scrollHeight(browser incompatibilities)

                                    if (window.navigator.userAgent.indexOf("Chrome") >= 1) {//browser:Chrome

                                        oscrollHeight = o.scrollHeight - 18;

                                    } else if (window.navigator.userAgent.indexOf("Firefox") >= 1) {//browser:Firefox

                                        oscrollHeight = o.scrollHeight - 24;

                                    } else { //browser: MSIE|opera

                                        oscrollHeight = o.scrollHeight;

                                    }

                                    function start() {

                                        t = setInterval(scrolling, speed);

                                        if (!p) {

                                            o.scrollTop += 1;

                                        }

                                    }

                                    function scrolling() {

                                        if (o.scrollTop % lh != 0) {

                                            o.scrollTop += 1;

                                            if (o.scrollTop >= oscrollHeight / 2)

                                                o.scrollTop = 0;

                                        }

                                        else {

                                            clearInterval(t);

                                            setTimeout(start, delay);

                                        }

                                    }

                                    setTimeout(start, delay);

                                }

                            }

                            if (document.getElementById("rollExpert") != null) {

                                Roll.Startmarquee("rollExpert", 48, 30, 5000);

                            } 

                        </script>
            <!-- 热点话题开始-->
            <div class="blueLine mt10">
              <div class="hotContent">
                <h5><span class="floatLeft font14">热点话题</span><span class="floatRight"><a gsn="askHotNewMore" href="<?=SITE_URL?>?c-all/6.html" target="_blank">更多>></a></span>
                  <div class="clear"></div>
                </h5>
                <ul gsn="askHotNews" class="graycolor font14">
                <? $nosolvelist=$this->fromcache('recommendlist'); ?>              
<? if(is_array($nosolvelist)) { foreach($nosolvelist as $index => $question) { ?>
              <? ++$index; ?>                  <li><font>·</font><a href="<?=SITE_URL?>?q-<?=$question['id']?>.html"  title="<?=$question['title']?>" target="_blank"><? echo cutstr($question['title'],40); ?></a><span class="grayColor">[<a target="_blank" href="<?=SITE_URL?>?c-<?=$question['cid']?>.html" title="<?=$question['category_name']?>"><? echo cutstr($question['category_name'],10,''); ?></a>]</span></li>
                
<? } } ?>
                </ul>
              </div>
            </div>
            <!-- 热点话题结束-->
            <!-- 待解决问题-->
            <div class="blueLine mt10">
              <div class="jiejueHeader">
                <div class="juejueIn">
                  <ul>
                    <li gsn="askQUR" class="floatLeft asksidetaba" id="liUnsolved" style="cursor: pointer">待解决问题</li>
                    <li gsn="askQNews" class="floatLeft " id="liLastestResolved" style="cursor: pointer">最新解决问题</li>
                    <li gsn="askQURMore" class="floatRight fontNormal"><a id="aUserCommentMore" target="_blank" href="<?=SITE_URL?>?c-all/1.html">更多>></a></li>
                  </ul>
                  <div class="clear"> </div>
                </div>
              </div>
              <div class="jiejueCont">
                <div gsn="askQUR" id="QuestionUnResolved" style="display: inline">
                <? $nosolvelist=$this->fromcache('nosolvelist'); ?>                
<? if(is_array($nosolvelist)) { foreach($nosolvelist as $index => $question) { ?>
                <? ++$index; ?>                  <ul>
                    <li class="font14 floatLeft">·<a href="<?=SITE_URL?>?q-<?=$question['id']?>.html"  title="<?=$question['title']?>" target="_blank"><? echo cutstr($question['title'],40); ?></a>&nbsp;<span class="grayColor">[<a target="_blank" href="<?=SITE_URL?>?c-<?=$question['cid']?>.html" title="<?=$question['category_name']?>"><? echo cutstr($question['category_name'],10,''); ?></a>]</span></li>
                    <li class="floatRight alignLeft"><?=$question['format_time']?></li>
                    <div class="clear"> </div>
                  </ul>
                
<? } } ?>
                </div>
                <div gsn="askQNews" id="QuestionResolved" style="display: none">
                <? $nosolvelist=$this->fromcache('solvelist'); ?>                
<? if(is_array($nosolvelist)) { foreach($nosolvelist as $index => $question) { ?>
                <? ++$index; ?>                  <ul>
                    <li class="font14 floatLeft">·<a href="<?=SITE_URL?>?q-<?=$question['id']?>.html"  title="<?=$question['title']?>" target="_blank"><? echo cutstr($question['title'],30); ?></a>&nbsp;<span class="grayColor">[<a target="_blank" href="<?=SITE_URL?>?c-<?=$question['cid']?>.html" title="<?=$question['category_name']?>"><? echo cutstr($question['category_name'],10,''); ?></a>]</span></li>
                    <li class="floatRight alignLeft"><?=$question['format_time']?></li>
                    <div class="clear"> </div>
                  </ul>
                
<? } } ?>
                </div>
              </div>
            </div>
            <script language="javascript" type="text/javascript">



    $("#liUnsolved").click(function() {

        $("#QuestionUnResolved").css("display", "inline");

        $("#QuestionResolved").css("display", "none");

        //document.getElementById("atimetitle").innerHTML = "提问时间";

        document.getElementById("liUnsolved").className = "floatLeft asksidetaba";

        document.getElementById("liLastestResolved").className = "floatLeft";



        $("#aUserCommentMore").attr("href", "<?=SITE_URL?>?c-all/1.html");

        

    });



    $("#liLastestResolved").click(function() {



    //document.getElementById("atimetitle").innerHTML = "回复时间";

        $("#QuestionResolved").css("display", "inline");

        $("#QuestionUnResolved").css("display", "none");

        document.getElementById("liLastestResolved").className = "floatLeft asksidetaba";

        document.getElementById("liUnsolved").className = "floatLeft";



        $("#aUserCommentMore").attr("href", "<?=SITE_URL?>?c-all/2.html");

        

        

    });

</script>
          </div>
        </div>
        <!-- 专家精彩答复开始-->
        <div class="mt10 blueLine">
          <div class="titleBghd"> <span class="titleBgz"><font class="floatLeft font14">专家精彩答复</font><font class="floatRight redmorew"><a gsn="ucAskAnswerMore" href="/ExpertReplyMore.html" target="_blank">更多>></a></font></span> </div>
          <div gsn="ucAskAnswer" class="dfContentBox">
            <div class="dfHeader">
              <ul>
                <li class="dfZhuanjia">专家</li>
                <li class="dfTitle3">标题</li>
                <li class="dfDate alignCenter">回复时间</li>
                <div class="clear"> </div>
              </ul>
            </div>
            <div class="dfContent ulclass" id="divExpertReply">
            <? $expertanswerlist=$this->fromcache('expertanswerlist'); ?>              
<? if(is_array($expertanswerlist)) { foreach($expertanswerlist as $index => $question) { ?>
              <? ++$index; ?>              <ul>
                <li class="dfZhuanjia"> <span id="ctl00_cphMain_ExpertBestReplyNew1_rpExpertReply_ctl00_nameLab"><a href="<?=SITE_URL?>?u-<?=$question['uid']?>.html" target="_blank" title="<?=$question['username']?>"><?=$question['username']?></a></span> </li>
                <li class="dfTitle3 font14">·<span id="ctl00_cphMain_ExpertBestReplyNew1_rpExpertReply_ctl00_titleLab"><a href="<?=SITE_URL?>?q-<?=$question['id']?>.html"  title="<?=$question['title']?>" target="_blank"><? echo cutstr($question['title'],30); ?></a><span class="grayColor">[<a target="_blank" href="<?=SITE_URL?>?c-<?=$question['cid']?>.html" title="<?=$question['category_name']?>"><? echo cutstr($question['category_name'],10,''); ?></a>]</span></span></li>
                <li class="dfDate alignCenter"> <span id="ctl00_cphMain_ExpertBestReplyNew1_rpExpertReply_ctl00_replyTimeLab"><?=$question['format_time']?></span></li>
                <div class="clear"> </div>
              </ul>
              
<? } } ?>
            </div>
            <script type="text/javascript">

            //去掉最后一个UL bottom的样式

$(document).ready(function(){

    $("#divExpertReply ul:last").css("border-bottom-style","none")

});



 </script>
          </div>
        </div>
        <!-- 专家精彩答复结束-->
        <!-- 网友问答开始 -->
        <div class="mt10 blueLine wangyouAnswer">
          <div class="jiejueHeader"> <span class="floatLeft font14 fontB">网友问答</span> <span class="floatLeft">
            <div class="newQuestionHeader wangyouAnswer">
              <ul>
                <li class="tabQuestiona" id="liUserCommentAll" style="cursor: pointer" gsn="askUserCommentAll">全部问题</li>
                <li class="tabQuestionb" id="liUnResolved" style="cursor: pointer" gsn="askUnResolved">待解决问题</li>
                <li class="tabQuestionb" id="liResolved" style="cursor: pointer" gsn="askResolved">已解决问题</li>
                <li class="tabQuestionb" id="liEncourage" style="cursor: pointer" gsn="askEncourage">悬赏问题</li>
                <li class="tabQuestionbr">&nbsp;</li>
                <div class="clear"> </div>
              </ul>
            </div>
            </span> <span class="floatRight"><a

        href="<?=SITE_URL?>?c-all.html" target="_blank" id="aUserCommentMoreMore" gsn="askUserCommentQuestionMore">更多&gt;&gt;
            <div

            class="clear"> </div>
            </a></span> </div>
          <div class="ppHeader">
            <ul>
              <li class="ppTitle2 blueColor">标题</li>
              <li class="ppHf blueColor">回复数</li>
              <li class="ppT blueColor" id="liResolvedPerson">提问人</li>
              <li class="ppDate alignCenter blueColor" id="liResolvedDateTime">提问时间</li>
              <div class="clear"> </div>
            </ul>
          </div>
          <div class="pinpaiQuestion ulclass" id="UserCommentAll">
          <? $alllist_answer=$this->fromcache('alllist_answer'); ?>              
<? if(is_array($alllist_answer)) { foreach($alllist_answer as $index => $question) { ?>
              <? ++$index; ?>            <ul>
              <li class="ppTitle2 font14">·<a href="<?=SITE_URL?>?q-<?=$question['id']?>.html"  title="<?=$question['title']?>" target="_blank"><? echo cutstr($question['title'],30); ?></a>&nbsp;<span class="grayColor">[<a target="_blank" href="<?=SITE_URL?>?c-<?=$question['cid']?>.html" title="<?=$question['category_name']?>"><? echo cutstr($question['category_name'],10,''); ?></a>]</span></li>
              <li class="ppHf "> <?=$question['answers']?></li>
              <li class="ppT"> <a href='<?=SITE_URL?>?u-<?=$question['authorid']?>.html' target='_blank' title='<?=$question['author']?>'><?=$question['author']?></a></li>
              <li class="ppDate alignCenter"> <?=$question['format_time']?></li>
              <div class="clear"> </div>
            </ul>
           
<? } } ?>
          </div>
          <div class="pinpaiQuestion ulclass" id="UserReplyCommentListUnResolved" style="display: none">
          <? $nosolvelist_answer=$this->fromcache('nosolvelist_answer'); ?>              
<? if(is_array($nosolvelist_answer)) { foreach($nosolvelist_answer as $index => $question) { ?>
              <? ++$index; ?>            <ul>
              <li class="ppTitle2 font14">·<a href="<?=SITE_URL?>?q-<?=$question['id']?>.html"  title="<?=$question['title']?>" target="_blank"><? echo cutstr($question['title'],30); ?></a>&nbsp;<span class="grayColor">[<a target="_blank" href="<?=SITE_URL?>?c-<?=$question['cid']?>.html" title="<?=$question['category_name']?>"><? echo cutstr($question['category_name'],10,''); ?></a>]</span></li>
              <li class="ppHf "> <?=$question['answers']?></li>
              <li class="ppT"> <a href='<?=SITE_URL?>?u-<?=$question['authorid']?>.html' target='_blank' title='<?=$question['author']?>'><?=$question['author']?></a></li>
              <li class="ppDate alignCenter"> <?=$question['format_time']?></li>
              <div class="clear"> </div>
            </ul>
           
<? } } ?>
          </div>
          <div class="pinpaiQuestion ulclass" id="UserReplyCommentListResolved" style="display: none">
          <? $solvelist_answer=$this->fromcache('solvelist_answer'); ?>              
<? if(is_array($solvelist_answer)) { foreach($solvelist_answer as $index => $question) { ?>
              <? ++$index; ?>            <ul>
              <li class="ppTitle2 font14">·<a href="<?=SITE_URL?>?q-<?=$question['id']?>.html"  title="<?=$question['title']?>" target="_blank"><? echo cutstr($question['title'],30); ?></a>&nbsp;<span class="grayColor">[<a target="_blank" href="<?=SITE_URL?>?c-<?=$question['cid']?>.html" title="<?=$question['category_name']?>"><? echo cutstr($question['category_name'],10,''); ?></a>]</span></li>
              <li class="ppHf "> <?=$question['answers']?></li>
              <li class="ppT"> <a href='<?=SITE_URL?>?u-<?=$question['authorid']?>.html' target='_blank' title='<?=$question['author']?>'><?=$question['author']?></a></li>
              <li class="ppDate alignCenter"> <?=$question['format_time']?></li>
              <div class="clear"> </div>
            </ul>
           
<? } } ?>
          </div>
          <div class="pinpaiQuestion ulclass" id="UserEncourageComment" style="display: none">
          <? $rewardlist_answer=$this->fromcache('rewardlist_answer'); ?>              
<? if(is_array($rewardlist_answer)) { foreach($rewardlist_answer as $index => $question) { ?>
              <? ++$index; ?>            <ul>
              <li class="ppTitle2 font14">·<a href="<?=SITE_URL?>?q-<?=$question['id']?>.html"  title="<?=$question['title']?>" target="_blank"><? echo cutstr($question['title'],30); ?></a>&nbsp;<span class="grayColor">[<a target="_blank" href="<?=SITE_URL?>?c-<?=$question['cid']?>.html" title="<?=$question['category_name']?>"><? echo cutstr($question['category_name'],10,''); ?></a>]</span></li>
              <li class="ppHf "> <?=$question['answers']?></li>
              <li class="ppT"> <a href='<?=SITE_URL?>?u-<?=$question['authorid']?>.html' target='_blank' title='<?=$question['author']?>'><?=$question['author']?></a></li>
              <li class="ppDate alignCenter"> <?=$question['format_time']?></li>
              <div class="clear"> </div>
            </ul>
           
<? } } ?>
          </div>
          <script language="javascript" type="text/javascript">



     //去掉最后一个UL bottom的样式

$(document).ready(function(){

    $("#UserReplyCommentListUnResolved ul:last").css("border-bottom-style","none");

    $("#UserReplyCommentListResolved ul:last").css("border-bottom-style","none");

    $("#UserCommentAll ul:last").css("border-bottom-style", "none");

    $("#UserEncourageComment ul:last").css("border-bottom-style", "none");

    });  

    

     $("#liUserCommentAll").click(function(){  

          $("#aUserCommentMoreMore").attr("href","<?=SITE_URL?>?c-all.html");

    });

    $("#liUnResolved").click(function(){

         $("#aUserCommentMoreMore").attr("href","<?=SITE_URL?>?c-all/1.html");   

    });

    $("#liResolved").click(function(){

         $("#aUserCommentMoreMore").attr("href","<?=SITE_URL?>?c-all/2.html");

    });

    $("#liEncourage").click(function() {

        $("#aUserCommentMoreMore").attr("href","<?=SITE_URL?>?c-all/4.html");

    }); 

</script>
        </div>
        <!-- 网友问答结束 -->
        <!-- 最新问题开始-->
        <div class="mt10 blueLine wangyouAnswer"> <a name="CommentTab"></a>
          <div class="jiejueHeader">
            <div class="floatLeft font14 fontB">最新问题</div>
            <div class="newQuestionHeader floatLeft">
              <ul>
                <li class="tabQuestionb" id="CommentType" onclick="LocateCommentByType('0');" style="cursor: pointer" gsn="askCommentListNewCT"> 全&nbsp;&nbsp;部</li>
                <li class="tabQuestionb" id="CommentTypeA01" onclick="LocateCommentByType('A01');"

            style="cursor: pointer" gsn="askCommentListNewCTA01">维修保养</li>
                <li class="tabQuestionb" id="CommentTypeA02" onclick="LocateCommentByType('A02');"

            style="cursor: pointer" gsn="askCommentListNewCTA02">买车卖车</li>
                <li class="tabQuestionb" id="CommentTypeA03" onclick="LocateCommentByType('A03');"

            style="cursor: pointer" gsn="askCommentListNewCTA03">车险车务</li>
                <li class="tabQuestionb" id="CommentTypeA04" onclick="LocateCommentByType('A04');"

            style="cursor: pointer" gsn="askCommentListNewCTA04">其&nbsp;&nbsp;他</li>
                <li class="tabQuestionb" style="cursor: pointer" gsn="askCommentListNewCTWY"><a href="/UserCommentInfo.html"

            target="_blank">网友问答</a></li>
                <li class="tabQuestionbr">共<a href="/CList/0.html" id="linkNumMore">147561</a>条问题&nbsp;&nbsp;&nbsp;<a href="CList/0.html" target="_blank" id="MoreLink" gsn="askCommentListNewMore">更多&gt;&gt;</a></li>
                <div class="clear"> </div>
              </ul>
            </div>
            <div class="clear"></div>
          </div>
          <div id="ta1" style="display: block;">
            <div class="ppHeader">
              <ul>
                <li class="ppTitle blueColor">标题</li>
                <li class="ppHf blueColor">回复数</li>
                <li class="ppDate alignCenter blueColor">提问时间</li>
                <div class="clear"> </div>
              </ul>
            </div>
            <div class="pinpaiQuestion ulclass" id="CommentListNew">
            <? $newest_question=$this->fromcache('newest_question'); ?>              
<? if(is_array($newest_question)) { foreach($newest_question as $index => $question) { ?>
              <? ++$index; ?>              <ul>
                <li class="ppTitle font14">·<a href="<?=SITE_URL?>?q-<?=$question['id']?>.html"  title="<?=$question['title']?>" target="_blank"><? echo cutstr($question['title'],30); ?></a>&nbsp;<span class="grayColor">[<a target="_blank" href="<?=SITE_URL?>?c-<?=$question['cid']?>.html" title="<?=$question['category_name']?>"><? echo cutstr($question['category_name'],10,''); ?></a>]</span></li>
                </li>
                <li class="ppHf"><span> <?=$question['answers']?></span></li>
                <li class="ppDate alignCenter"> <?=$question['format_time']?></li>
                <div class="clear"> </div>
              </ul>
              
<? } } ?>
            </div>
            <div class="alignRight p10" > 没有找到合适的答案&nbsp;&nbsp;<span id="QuestionLinkBottomArea" class="redcolora"><a href="/submitquestion.html" target="_blank" gsn="askCommentListNewTW">我要提问</a></span></div>
          </div>
          <script type="text/javascript">



    //显示不同分类的留言

    function LocateCommentByType(type) {

        window.location = "../C/"+type+".html#CommentTab";

    }



    $("#CommentType").attr("class","tabQuestiona");

        

    $(document).ready(function() {

        var str = '';

        if (str != "") {

            if (str == "0")

            {

                $("#MoreLink").attr("href", "/CList/0.html");

                $("#linkNumMore").attr("href", "/CList/0.html");

                $("#CommentListNew").attr("gsn", "askCommentListNewCT");

            }

            if (str == "A01")

            {

                $("#MoreLink").attr("href", "/CList/A01.html");

                $("#linkNumMore").attr("href", "/CList01.html");

                $("#CommentListNew").attr("gsn", "askCommentListNewCTA01");

            }

            if (str == "A02")

            {

                $("#MoreLink").attr("href", "/CList/A02.html");

                $("#linkNumMore").attr("href", "/CList/A02.html");

                $("#CommentListNew").attr("gsn", "askCommentListNewCTA02");

            }

            if (str == "A03")

            {

                $("#MoreLink").attr("href", "/CList/A03.html");

                $("#linkNumMore").attr("href", "/CList/A03.html");

                $("#CommentListNew").attr("gsn", "askCommentListNewCTA03");

            }

            if (str == "A04")

            {

                $("#MoreLink").attr("href", "/CList/A04.html");

                $("#linkNumMore").attr("href", "/CList/A04.html");

                $("#CommentListNew").attr("gsn", "askCommentListNewCTA04");

            }

            if (str == "5")

            {

                $("#MoreLink").attr("href", "/CList/A01.html");

                $("#linkNumMore").attr("href", "/CList/A01.html");

            }

        }

    });

     

    //去掉最后一个UL bottom的样式

    $(document).ready(function(){

        $("#CommentListNew ul:last").css("border-bottom-style","none")

    });  



</script>
        </div>
        <!-- 最新问题结束-->
      </div>
      <div class="w270 floatRight">
        <div gsn="askWangYan" class="blueLine">
          <div class="eye">
            <div class="eye_img">
              <div id="focus_img"> <a href="http://www.webcars.com.cn/review/20121121/76812.html" target="_blank"> <img style="display: none;" alt="万车知道：专家解答冬季怎样热车才最科学" src="http://files.webcars.com.cn/CarsPhoto/PageEye/839bebb8-5aef-46a6-a560-604fb5fd8df5.jpg"

                            width="268" height="234"></a> <a href="http://www.webcars.com.cn/review/20121127/77039.html" target="_blank"> <img style="display: none;" alt="http://www.webcars.com.cn/review/20121127/77039.html" src="http://files.webcars.com.cn/CarsPhoto/PageEye/e2e6722d-1dd4-447a-8e70-ab5e30b88d08.jpg"

                            width="268" height="234"></a> <a href="http://www.webcars.com.cn/review/20110705/52403.html" target="_blank"> <img style="display: none;" alt="万车知道短信问答开通了！" src="http://files.webcars.com.cn/CarsPhoto/PageEye/a5c9c631-1450-40e8-b35c-b01573d3e852.jpg"

                            width="268" height="234"></a> <a href="http://www.webcars.com.cn/review/20121130/77143.html" target="_blank"> <img style="display: none;" alt="万车知道：一汽大众车型常见问题解答" src="http://files.webcars.com.cn/CarsPhoto/PageEye/2bde8bef-a820-4190-b990-6058cc31e262.jpg"

                            width="268" height="234"></a> <a href="http://ask.webcars.com.cn/King.html" target="_blank"> <img style="display: none;" alt="快来与武林王亲密接触吧" src="http://files.webcars.com.cn/CarsPhoto/PageEye/9459532b-4532-4c9c-9a27-31d942d7cde0.jpg"

                            width="268" height="234"></a> </div>
            </div>
            <div id="btn" class="eye_num clearfix">
              <div class="r"> <span class=""> 1</span> <span class=""> 2</span> <span class=""> 3</span> <span class=""> 4</span> <span class=""> 5</span> </div>
            </div>
          </div>
          <script type="text/javascript">

    $(function() { var a = 0; $("#btn span").mouseover(function() { a = $("#btn span").index(this); showImg(a) }); $(".eye_img").hover(function() { if (b) { clearInterval(b) } }, function() { b = setInterval(function() { showImg(a); a++; if (a == $("#btn").find("span").length) { a = 0 } }, 3200) }); var b = setInterval(function() { showImg(a); a++; if (a == $("#btn").find("span").length) { a = 0 } }, 3200) }); 

    function showImg(a) { $("#focus_img img").eq(a).stop(true, true).fadeIn(800).parent().siblings().find("img").hide(); $("#btn span").eq(a).addClass("cur").siblings().removeClass("cur") } $(document).ready(function() { $("#focus_img img:eq(0)").css("display", "block") });

</script>
        </div>
        <div style="margin-top: 10px; margin-bottom: -2px; *margin-bottom: -2px;"> <a gsn="askPostQs" href="javascript:postQuestion()" style="cursor: hand"> <img src="http://images.webcars.com.cn/askweb/images_2011/dztw.gif" /> </a> </div>
        <!-- 经销商售后专区-->
        <div gsn="askRightJXSZQ" class="mt10 bline" id="divDealerSaleAfter">
          <DIV class=hotHeader><SPAN class=floatLeft>经销商售后专区</SPAN>
            <DIV class=clear></DIV>
          </DIV>
          <DIV class=shcont>
            <UL>
              <LI>
                <P class=pleft>·<A title="" href="http://www.webcars.com.cn/review/20121130/77140.html" target=_blank>东风标致回馈促销尊享十重豪礼</A></P>
                <P class=pright><A href="http://dealer.webcars.com.cn/dealer/636/default.html" target=_blank>博瑞祥致</A></P>
                <DIV class=clear></DIV>
              <LI>
                <P class=pleft>·<A title="" href="http://www.webcars.com.cn/review/20121114/76521.html" target=_blank>广本石景山店二手车评估置换会</A></P>
                <P class=pright><A href="http://dealer.webcars.com.cn/dealer/10486/default.html" target=_blank>广本4S店</A></P>
                <DIV class=clear></DIV>
              <LI>
                <P class=pleft>·<A title="" href="http://www.webcars.com.cn/review/20121113/76476.html" target=_blank>运通兴宝宝马售后冬季关怀活动</A></P>
                <P class=pright><A href="http://dealer.webcars.com.cn/dealer/27512/default.html" target=_blank>运通兴宝</A></P>
                <DIV class=clear></DIV>
              <LI>
                <P class=pleft>·<A title="" href="http://www.webcars.com.cn/review/20120914/74255.html" target=_blank>宝辰欧雅沃尔沃推检测维修套餐</A></P>
                <P class=pright><A href="http://dealer.webcars.com.cn/dealer/10526/default.html" target=_blank>宝辰欧雅</A></P>
                <DIV class=clear></DIV>
              <LI>
                <P class=pleft>·<A title="" href="http://www.webcars.com.cn/review/20120921/74578.html" target=_blank>雪铁龙C5旧车评估增值万元补贴</A></P>
                <P class=pright><A href="http://dealer.webcars.com.cn/dealer/27467/default.html" target=_blank>雪铁龙</A></P>
                <DIV class=clear></DIV>
              <LI>
                <P class=pleft>·<A title="" href="http://www.webcars.com.cn/review/20121009/74998.html" target=_blank>铃木丽泽店庆维修感恩促销活动</A></P>
                <P class=pright><A href="http://dealer.webcars.com.cn/dealer/10533/default.html" target=_blank>北方新兴</A></P>
              </LI>
            </UL>
          </DIV>
        </div>
        <!-- 微博入口-->
        <div class="mt10"> <a href="http://weibo.com/wanchezhidao" target="_blank"><img src="http://images.webcars.com.cn/askweb/images_2011/img_weibo.gif"></a> </div>
        <div class="mt10"> <a href="http://itunes.apple.com/us/app/id463989044?mt=8" target="_blank"><img src="http://images.webcars.com.cn/askweb/images_2011/img_tiyan.gif"></a> </div>
        <!-- 本周热点专家-->
        <div class="bline mt10"  gsn="askRightRDZJ">
          <div class="hotHeader"><span class="floatLeft">本周热点专家</span><span class="floatRight redmorew"><a href="/ExpertsTeam.html" target="_blank">更多&gt;&gt;</a></span>
            <div class="clear"></div>
          </div>
          <? $expertlist=$this->fromcache('expertlist'); ?>          
<? if(is_array($expertlist)) { foreach($expertlist as $index => $expert) { ?>
          <? ++$index; ?>          <div class="zjtjContent">
            <ul class="leftl">
              <li><a href="<?=SITE_URL?>?u-<?=$expert['uid']?>.html" target="_blank"><img alt="<?=$expert['username']?>" title="<?=$expert['username']?>" src="<?=$expert['avatar']?>" class="imgBorder" style="width:96px;height:111px"></a></li>
            </ul>
            <ul class="rightr">
              <li><span><a href="<?=SITE_URL?>?u-<?=$expert['uid']?>.html" title="<?=$expert['username']?>" target="_blank"><?=$expert['username']?></a></span></li>
              <li><?=$expert['categoryname']?></li>
              <li>地址：北京市朝阳区东直门外左家庄路2号</li>
              <li>电话：800-810-1384</li>
              <li style="display:none"><span>答题数：</span>9248</li>
            </ul>
            <div class="clear"></div>
          </div>
          
<? } } ?>
          <div class="redmore"></div>
        </div>
        <div class="bannerTable"> </div>
        <div class="bline mt10" gsn="askRightRMBQ">
          <div class="hotHeader"> 热门标签</div>
          <div class="biaoqianContent">
            <ul class="clearfix">
            <? $hottag=$this->fromcache('hottag'); ?>          
<? if(is_array($hottag)) { foreach($hottag as $index => $tag) { ?>
          <? ++$index; ?>              <LI><A href="<?=SITE_URL?>?question/tag/<?=$tag['name']?>.html" target=_blank><?=$tag['name']?></A></LI>
              
<? } } ?>
              <DIV class=clear></DIV>
            </ul>
          </div>
        </div>
        <!-- 网友活跃度排行-->
        <div class="bline mt10">
          <!-- 网友活跃度-->
          <div class="hotHeader">网友活跃度排行</div>
          <div class="hydpaihangContent WangyouHyd">
          <? $alluserlist=$this->fromcache('alluserlist'); ?>            
<? if(is_array($alluserlist)) { foreach($alluserlist as $index => $alluser) { ?>
            <? ++$index; ?>            <ul>
              <li class="hydName"><a target="_blank" href="<?=SITE_URL?>?u-<?=$alluser['uid']?>.html"><?=$alluser['username']?></a></li>
              <li class="hydWeiwang"><?=$alluser['credit1']?></li>
              <li><a target="_blank" href="<?=SITE_URL?>?user/space_ask/<?=$alluser['uid']?>.html">查看问题</a></li>
            </ul>
            
<? } } ?>
          </div>
          <div class="clear"></div>
        </div>
        <!-- 本周专家关注排行-->
        <div class="ExpertsPaihang">
          <div class="tit_ExpertsPaihang">
            <h2> 本周专家排行榜</h2>
          </div>
          <div gsn="askExpertsPH">
            <ul class="tab_ExpertsPaihang">
              <li id="active" class="tabon">活 跃</li>
              <li id="prestige">威 望</li>
            </ul>
            <ul class="caption_ExpertPaihang">
              <li>专 家</li>
              <li><span id="typename">回答数</span></li>
            </ul>
            <div class="ExpertsPaihangList" id="activediv" >
            <? $expert_answer=$this->fromcache('expert_answer'); ?>              
<? if(is_array($expert_answer)) { foreach($expert_answer as $index => $expert) { ?>
              <? ++$index; ?>              <ul>
                <li><a href="<?=SITE_URL?>?u-<?=$expert['uid']?>.html" target="_blank"  title="<?=$expert['username']?>"><?=$expert['username']?></a></li>
                <li class="other"><?=$expert['answers']?></li>
              </ul>
              
<? } } ?>
            </div>
            <div class="ExpertsPaihangList" id="prestigediv" style="display:none">
              <? $expert_credit1=$this->fromcache('expert_credit1'); ?>              
<? if(is_array($expert_credit1)) { foreach($expert_credit1 as $index => $expert) { ?>
              <? ++$index; ?>              <ul>
                <li><a href="<?=SITE_URL?>?u-<?=$expert['uid']?>.html" target="_blank"  title="<?=$expert['username']?>"><?=$expert['username']?></a></li>
                <li class="other"><?=$expert['answers']?></li>
              </ul>
              
<? } } ?>
            </div>
          </div>
          <div class="clear"> </div>
        </div>
        <script language="javascript" type="text/javascript">

     $("#attention").click(function(){  

          $("#attention").addClass("tabon");

          $("#active").removeClass("tabon");

          $("#prestige").removeClass("tabon");

          $("#typename").html("粉 丝");

          $("#attentiondiv").show();

          $("#activediv").hide();

          $("#prestigediv").hide();

    });

    $("#active").click(function(){ 

         $("#active").addClass("tabon");

         $("#attention").removeClass("tabon");

         $("#prestige").removeClass("tabon");

         $("#typename").html("回答数");

         $("#attentiondiv").hide();

         $("#activediv").show();

         $("#prestigediv").hide();

    });

    $("#prestige").click(function(){

         $("#prestige").addClass("tabon");

         $("#attention").removeClass("tabon");

         $("#active").removeClass("tabon");

         $("#typename").html("威 望");

         $("#attentiondiv").hide();

         $("#activediv").hide();

         $("#prestigediv").show();

    });

</script>
      </div>
      <div class="clear"></div>
    </div>
  </div>
  <!--问题数flash 首屏延迟加载-->
  <div id="count" style="display: none;">
    <div id="askcount" style="width: 220px; height: 80px; overflow: hidden;">
      <object width="220" height="80">
        <param name="movie" value="swf/TimerGetServerValue.swf" />
        <param name="menu" value="false" />
        <param name="wmode" value="transparent" />
        <embed src="/pic/TimerGetServerValue.swf" wmode="transparent" menu="false" width="220"

                    height="80" />
      </object>
    </div>
    <input name="hdVersion" id="hdVersion" type="hidden" value="10517" />
  </div>
  <!--问题数flash结束-->
  <!--快速提问弹出层-->
  <div id="MyLoveMask" class="mask"> </div>
  <div id="ShowDiv" class="out">
    <iframe id="FrameWindows" src="about:blank" frameborder="0" scrolling="no" width="660" style="position:absolute;top:-50px;left:0;z-index:1; height:408px;_height:413px;"></iframe>
    <!--品牌列表-->
    <div id="ShowWindow" style="position:absolute;top:-50px;left:0px;width:640px;z-index:2;display: none;">
      <div class="qcBox">
        <div class="qc">
          <div class="qc_header">
            <ul class="clearfix">
              <li class="fl cur">汽车问题快速提问</li>
              <li class="fl"><a gsn="askFQGJTW" target="_blank" href="/submitquestion.html">高级提问&gt;&gt;</a></li>
              <li class=" fr"><a gsn="askFQClose" href="#" class="close" onclick="ColseWindow();"></a></li>
            </ul>
          </div>
          <div class="qc_cont">
            <ul class="clearfix">
              <li class="qc_cont_fl fl"><span class="redColor">*</span>标题：</li>
              <li class="qc_cont_fr fl">
                <input id="txtTitle" onfocus="showTip(1);" onblur="showTip(-1);" onkeyup="checkSize(this);"

                                    name="" type="text" />
                <div class="alignRight c9"> 您还可以输入<span id="spanCount" class="redColor">25</span>个字</div>
              </li>
              <div id="tipTitle" class="qc_pos" style="display: none">
                <ul>
                  <li><strong>完整的标题才能得到回复！</strong></li>
                  <li class="dui">例如：<span class="redColor">“伊兰特挂倒挡发出咔咔异响怎么办？”</span>尽量在标题中加入品牌车型和疑问词，这样的标题会优先得到专家回复。</li>
                  <li class="cuo">例如：<span class="redColor">“倒挡异响”</span>这种标题过于简单，会被忽略得不到专家回复。</li>
                </ul>
                <div class="qc_posn"> </div>
              </div>
            </ul>
            <ul class="clearfix">
              <li class="qc_cont_fl fl"><span class="redColor">*</span>问题描述：</li>
              <li class="qc_cont_fr fl">
                <textarea id="txtContent" onfocus="showTip(0);" onblur="showTip(-1);" name="" cols=""

                                    rows=""></textarea>
                <div class="alignRight c9"> 填写详细问题描述，可获得系统奖励2个威望</div>
              </li>
              <div id="tipContent" class="qc_pos" style="display: none">
                <ul>
                  <li><strong>详细的故障描述有助于问题解决！</strong></li>
                  <li class="dui">例如：<span class="redColor">“原来挂挡一直正常，昨天发现挂倒车档有异常‘咔、咔’响声，异响时有时无，挂挡也能正常挂上，前进档都正常，请专家指教。”</span></li>
                  <li class="cuo">例如：<span class="redColor">“倒挡异响什么原因啊？”</span></li>
                </ul>
                <div class="qc_posn"> </div>
              </div>
            </ul>
            <ul class="clearfix" id="ulLogin">
              <input name="hidMemberLinkUrl" id="hidMemberLinkUrl" type="hidden" value="http://my.webcars.com.cn/" />
              <li class="qc_cont_fl fl"><span class="redColor">*</span>用户名：</li>
              <li class="qc_cont_fr fl">
                <input id="txtUserName" name="txtUserName"  style="width:108px;" />
                &nbsp;&nbsp;&nbsp;<span class="f14">密码：</span>
                <input type="password" id="txtPassword" name="txtPassword" style="width:108px;" />
                <a gsn="askFQLosePwd" target="_blank" href="http://my.webcars.com.cn/GetBackPassword.html"> 忘记密码</a>&nbsp;<span class="blueColor">|</span>&nbsp;<a gsn="askFQReg" target="_blank" href="http://my.webcars.com.cn/Register.html?RegisterSource=1">注册</a> </li>
            </ul>
            <ul class="clearfix">
              <li class="qc_cont_fl fl"><span class="redColor">*</span>验证码：</li>
              <input name="hidVerifyCode" id="hidVerifyCode" type="hidden" value="0" />
              <li class="qc_cont_fr fl">
                <input maxlength="4" id="VerifyCode_UserControl2009" size="4" class="yzm" title="看不清? 点击“更换验证码”"

                                    maxlength="4" id="VerifyCode_UserControl2009" size="4">
                <img id="VerifyPic_UserControl2009" onclick="javascript:document.getElementById('VerifyPic_UserControl2009').src='/ashx/GetVerifyCodePic.ashx?'+Math.random()"

                                    src="/ashx/GetVerifyCodePic.ashx" /> <a href="###" onclick="javascript:document.getElementById('VerifyPic_UserControl2009').src='/ashx/GetVerifyCodePic.ashx?'+Math.random();return false;"> 看不清，换一个？</a></li>
            </ul>
            <div>
              <input gsn="askFQSubmit" name="" onclick="submitQuestion();" type="button" value="确认提交" class="tj" />
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script type="text/javascript">

        $(function() {

            var tabs = "#divFriendLink #tabs span";

            var contents = "#divFriendLink .tabcontents2";

            var hover = "hover6";

            //标签切换

            $(tabs).each(function(i, obj) {

                $(obj).mouseover(function() {

                    var cs = $(contents);

                    var ts = $(tabs);

                    $(cs).hide();

                    $(cs.get(i)).show();



                    $(ts).removeClass(hover);

                    $(ts.get(i)).addClass(hover);

                    lastTab = i;

                });

            });

        });

        

        if('0'==1)

        {

            $("#divFriendLink").hide();

        }

        

    </script>
  <script src="js/NCRefreshSelect.js" type="text/javascript"></script>
  <script src="js/NCMRefreshSelect.js" type="text/javascript"></script>
  <script src="js/overlay.js" type="text/javascript"></script>
  <link id="ctl00_ExternalFileReference2" href="style/overlay.v201211260.css" type="text/css" rel="stylesheet" />
  <script src="js/AreaClick.js" type="text/javascript"></script>
  <script src="<?=SITE_URL?>js/HomePageCommentLoad.js" type="text/javascript"></script>
  <script src="Js/1039.js" type="text/javascript"></script>
  <script src="js/gsn/askIndex.js" type="text/javascript"></script>
  <script src="js/jquery.validate.js" type="text/javascript"></script>
  <script src="js/QuickLoginReg.js" type="text/javascript"></script>
  <script src="js/jQueryPngFix.js" type="text/javascript"></script>
  <script src="js/QuickQuestions.js" type="text/javascript"></script>
  <div id="qkregunauthSina">
    <div class="qkru1" id="qkregAuth" style="display: none">
      <div class="qkru2">
        <div class="zhuceYanzheng">
          <div class="alignRight"> <a href="javascript:void(0)" class="close">关闭</a></div>
          <center class="font18 fontblack mt20">
            “<span id="qkruNickSina"></span>”您好，您还没有通过邮箱验证，<br />
            暂时无法使用万车网的各项服务
          </center>
          <div class="zhuceCheng">
            <dl>
              <dt>我们已经向您的注册邮箱<span id="qkruEmailSina"></span>发送了一封验证邮件，请登录并点击邮件中的链接进行验证。验证成功后，您将享受以下尊贵服务：</dt>
            </dl>
            <ul>
              <li>· 立即获得<span class="redcolora fontB">20</span>积分</li>
              <li>· 获得优惠活动通知</li>
              <li>· 获得幸运抽奖机会</li>
              <li>· 丢失密码后快速找回功能</li>
              <div class="clear"> </div>
            </ul>
            <div style="clear: both"> · 及时收到万车知道专家对您的问题回复</div>
          </div>
          <center class="mt20">
            <input id="qkruAuth" type="button" class="zhucebutton" value="邮箱认证">
            &nbsp;&nbsp;&nbsp;
            <input id="qkruEmail" type="button" onclick="PostMail();" class="zhucebutton3" value="重新发送认证邮件">
            &nbsp;&nbsp;&nbsp;
            <input id="qkruReg" type="button" class="zhucebutton" onclick="location.href='http://my.webcars.com.cn/Register.html'"

                                value="重新注册" name="">
          </center>
        </div>
      </div>
    </div>
    <div class="sblog" id="sinaLoginDIV" style="display: none">
      <h2> <span class="floatLeft">登录</span><span class="floatRight"><a href="javascript:void(0);"

                        class="close"><img src="http://my.webcars.com.cn/images_2010/sprites/icobx.gif"></a></span>
        <div

                            class="clear"> </div>
      </h2>
      <div class="sblogt"> <span id="sinaName" class="redcolora fontB">老虎日记</span> 您好！现在可以连接万车网了！</div>
      <div class="sblogin">
        <div class="sbloginhe">
          <ul>
            <li id="li0" class="sblogina" onclick="changeLi('0');" style="cursor: pointer;"><span

                                id="span0" class="redcolora fontB">万车会员</span> 使用已有用户名连接</li>
            <li id="li1" class="sbloginb" onclick="changeLi('1');" style="cursor: pointer;"><span

                                id="span1" class="fontB">非万车会员</span> 选择新用户名连接</li>
            <div class="clear"> </div>
          </ul>
        </div>
        <!--会员登录-->
        <div id="valDiv" class="sbloglo" style="display: none">
          <form id="fsianLogin" action="" method="post">
            <ul>
              <li id="loginerror" class="newLoginT" style="display: none"></li>
            </ul>
            <dl>
              <dt>登录帐号：</dt>
              <dd>
                <input id="valEmail" type="text" name="valEmail" />
              </dd>
              <div class="clear"> </div>
            </dl>
            <dl>
              <dt>密码：</dt>
              <dd>
                <input id="valPass" type="password" name="valPass" />
              </dd>
              <div class="clear"> </div>
            </dl>
            <center>
              <input type="button" class="blogbutton" value="连接万车网" onclick="sinaLogin()" name="" />
            </center>
          </form>
        </div>
        <!--非会员登录-->
        <div id="regDiv" class="sbloglo" style="display: none">
          <form id="fsianregster" action="post">
            <ul>
              <li id="regerror" class="newLoginT" style="display: none"></li>
            </ul>
            <dl>
              <dt>登录帐号：</dt>
              <dd>
                <input id="regEmail" type="text" name="regEmail" onclick="" />
              </dd>
              <div class="clear"> </div>
            </dl>
            <dl>
              <dt>密码：</dt>
              <dd>
                <input id="regPass" type="password" name="regPass" />
              </dd>
              <div class="clear"> </div>
            </dl>
            <dl>
              <dt>昵称：</dt>
              <dd>
                <input id="regName" type="text" name="regName" />
              </dd>
              <div class="clear"> </div>
            </dl>
            <center>
              <input type="button" onclick="sinaRegister()" class="blogbutton" value="连接万车网" name="" />
            </center>
          </form>
        </div>
        <!--非会员登录成功-->
        <div id="sucDiv" class="sbloglo" style="display: none">
          <ul>
            <li><span class="fontB redcolora">[<span id="sucName">老虎日记</span>]注册成功&nbsp;&nbsp;&nbsp;&nbsp;恭喜您成为万车网的一员</span></li>
            <li>请登录您的注册邮箱进行邮箱认证</li>
            <li>认证邮箱后您可以：</li>
            <li class="fontB">·获得优惠活动通知 ·获得幸运抽奖机会</li>
            <li class="fontB">·收到专家问题回复 ·丢失密码邮件找回</li>
          </ul>
          <center>
            <input id="btnValEmail" type="button" class="blogbutton" value="邮箱验证" name="">
            <input type="button" class="blogbutton ml20" value="连接万车网" name="" onclick="linkWebcars();">
          </center>
        </div>
      </div>
    </div>
  </div>
  <a id="test" href="#qkregunauthSina" rel="facebox"></a>
</div>
<? include template('footer'); ?>
