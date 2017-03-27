<? if(!defined('IN_TIPASK')) exit('Access Denied'); include template('header'); ?>
<div class="content">
    <div class="leftbox">
        
<? if(is_array($expertlist)) { foreach($expertlist as $expert) { ?>
        <div class="wenti">
            <div class="wttitle">
                <ul>
                    <li class="wta1"></li>
                    <li class="wta2">问答专家</li>
                    <li class="wta3"><a href="<?=SITE_URL?>?question/add/<?=$expert['uid']?>.html"><img style="padding-top:8px;"  src="<?=SITE_URL?>css/default/ask_expert.png" /></a></li>
                    <li class="wta4"></li>
                </ul>
                <div class="clr"></div> 
            </div>
            <div class="clr"></div>
            <div class="plc">
                <div class="expertcont">
                    <div class="zjtjct">
                        <div class="zjtjctimg">
                            <a href="http://localhost/Tipask2/tipask/?u-1.html" target="_top" title="admin"><img width="80px" height="80px;" src="<?=$expert['avatar']?>" alt="admin"></a>
                        </div>
                        <h1><a href="<?=SITE_URL?>?u-<?=$expert['uid']?>.html"><?=$expert['username']?></a></h1>
                        <p>回答数：<?=$expert['answers']?>&nbsp;&nbsp;&nbsp;&nbsp;提问数：<?=$expert['questions']?></p>
                        <p>简介：<? if($expert['signature']) { ?><?=$expert['signature']?><? } else { ?>TA还没有填写自我介绍!<? } ?></p>
                        <img width="700px" height="6px" src="<?=SITE_URL?>css/default/gx.gif">
                    </div>
                    <div class="clr"></div>
                    <div class="zjtjcpj">
                        <span class="title1">精选解答:</span><br>
                        <ul>
                            
<? if(is_array($expert['bestanswer'])) { foreach($expert['bestanswer'] as $answer) { ?>
                            <li><a href="<?=SITE_URL?>?q-<?=$answer['qid']?>.html" target='_blank' title="<?=$answer['title']?>"><? echo cutstr($answer['title'],28,''); ?></a></li>
                            
<? } } ?>
                        </ul>
                        <div class="clr"></div>
                    </div>
                </div>
            </div>
            <div class="clr"></div>
            <div class="qtbuttom">
                <ul>
                    <li class="qtba1"></li>
                    <li class="qtba2"></li>
                    <li class="qtba3"></li>
                    <li class="qtba4"></li>
                </ul>
                <div class="clr"></div>
            </div>
        </div>
        
<? } } ?>
        <div class="clr"></div>
    </div>
    <div class="right1">
        <div class="gg">
            <div class="ggtitle">
                <ul>
                    <li class="gga1"></li>
                    <li class="gga2">
                        <div class="juzhong">专家最近解答</div>
                    </li>
                    <li class="gga3"></li>
                </ul>
            </div>
            <div class="clr"></div>
            <div class="ggcon">
                <ul>
                    
<? if(is_array($questionlist)) { foreach($questionlist as $question) { ?>
                    <li> <a href="<?=SITE_URL?>?q-<?=$question['qid']?>.html" title="<?=$question['title']?>"><? echo cutstr($question['title'],24); ?></a></li>
                    
<? } } ?>
                </ul>
            </div>
            <div class="ggbuttom">
                <ul>
                    <li class="ggba1"></li>
                    <li class="ggba2"></li>
                    <li class="ggba3"></li>
                </ul>
            </div>
            <div class="clr"></div>
        </div>
    </div>
</div>
<div class="clr"></div>
<? include template('footer'); ?>
