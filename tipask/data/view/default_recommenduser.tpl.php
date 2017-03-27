<? if(!defined('IN_TIPASK')) exit('Access Denied'); include template('header'); ?>
<div class="content">
    <div class="leftbox">
        
<? if(is_array($famouslist)) { foreach($famouslist as $famous) { ?>
        <div class="zjtjt">
            <li class="zjtja1"><div class="juzhong">&nbsp;第<?=$famous['id']?>届推荐之星</div></li>
            <li class="zjtja2"><a href="<?=SITE_URL?>?question/add/<?=$famous['uid']?>.html" ><img  src="<?=SITE_URL?>css/default/ask_ta.png"></a></li>
            <li class="zjtja3"></li>
            <div class="clr"></div>
        </div>
        <div class="zjtjc">
            <div class="zjtjct">
                <div class="zjtjctimg">
                    <a title="<?=$famous['username']?>" target="_top" href="<?=SITE_URL?>?u-<?=$famous['uid']?>.html"><img  alt="<?=$famous['username']?>" src="<?=$famous['avatar']?>" width="80px" height="80px;"/></a>
                </div>
                <h1>本期知道之星：<?=$famous['username']?></h1>
                <p style="color:#4894F3;">提问数：<?=$famous['questions']?>&nbsp;&nbsp;回答数：<?=$famous['answers']?>&nbsp;&nbsp;经验：<?=$famous['credit1']?>&nbsp;&nbsp;财富：<?=$famous['credit2']?>&nbsp;&nbsp;魅力：<?=$famous['credit3']?></p>
                <p>
                    <? if($famous['signature']) { ?><?=$famous['signature']?><? } else { ?>TA还没有填写自我介绍!<? } ?>                    <span class="ckxx"><a  href="<?=SITE_URL?>?u-<?=$famous['uid']?>.html">查看详细介绍&gt;&gt;</a></span>
                </p>
                <img width="630px" height="6px" src="<?=SITE_URL?>css/default/gx.gif">
                <h3>获选理由：</h3>
                <p class="reason"><?=$famous['reason']?></p>
            </div>
            <div class="clr"></div>
            <div class="zjtjcpj">
                <span class="title1">精选解答:</span><br>
                <ul>
                    
<? if(is_array($famous['bestanswer'])) { foreach($famous['bestanswer'] as $answer) { ?>
                    <li><a href="<?=SITE_URL?>?q-<?=$answer['qid']?>.html" target='_blank' title="<?=$answer['title']?>"><? echo cutstr($answer['title'],28,''); ?></a></li>
                    
<? } } ?>
                </ul>
                <div class="clr"></div>
            </div>
        </div>
        <div class="zjtjb">
            <li class="zjtjc1"></li>
            <li class="zjtjc2"></li>
            <li class="zjtjc3"></li>
            <div class="clr"></div>
        </div>
        <div class="clr"></div>
        
<? } } ?>
        <div class="scott"><?=$departstr?></div>
        <div class="clr"></div>
    </div>
    <div class="right1">
        <div class="gg">
            <div class="ggtitle">
                <ul>
                    <li class="gga1"></li>
                    <li class="gga2">
                        <div class="juzhong">推荐之星最新解答</div>
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
