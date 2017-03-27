<? if(!defined('IN_TIPASK')) exit('Access Denied'); include template('header'); ?>
<div class="content">
    <div class="dh">
        <a title="" target="_blank" href="<?=SITE_URL?>"><?=$setting['site_name']?></a>&nbsp;&gt;&nbsp;搜索结果
    </div>

    <div class="jietab">
        <? if(isset($word)) { ?>        <ul>
            <? if(3==$status) { ?><li class="on">全部问答</li><? } else { ?><li class="current_none"><a href="<?=SITE_URL?>?question/search/3/<?=$encodeword?><?=$setting['seo_suffix']?>">全部问题</a></li><? } ?>            <? if(2==$status) { ?><li class="on"><font color="#1bbf00">√ </font>已解决</li><? } else { ?><li class="current_none"><font color="#1bbf00">√ </font><a href="<?=SITE_URL?>?question/search/2/<?=$encodeword?><?=$setting['seo_suffix']?>">已解决</a></li><? } ?>            <? if(1==$status) { ?><li class="on"><font color="#ff6600">？</font>待解决<? } else { ?><li class="current_none"><font color="#ff6600">？</font><a href="<?=SITE_URL?>?question/search/1/<?=$encodeword?><?=$setting['seo_suffix']?>">待解决</a></li><? } ?>        </ul>
        <? } else { ?>        <ul>
            <? if(3==$status) { ?><li class="on">全部问答</li><? } else { ?><li class="current_none"><a href="<?=SITE_URL?>?question/tag/<?=$encodeword?>/0/<?=$setting['seo_suffix']?>">全部问题</a></li><? } ?>            <? if(2==$status) { ?><li class="on"><font color="#1bbf00">√ </font>已解决</li><? } else { ?><li class="current_none"><font color="#1bbf00">√ </font><a href="<?=SITE_URL?>?question/tag/<?=$encodeword?>/2/<?=$setting['seo_suffix']?>">已解决</a></li><? } ?>            <? if(1==$status) { ?><li class="on"><font color="#ff6600">？</font>待解决<? } else { ?><li class="current_none"><font color="#ff6600">？</font><a href="<?=SITE_URL?>?question/tag/<?=$encodeword?>/<?=$setting['seo_suffix']?>">待解决</a></li><? } ?>        </ul>
        <? } ?>        <div class="clr"></div>
    </div>
    <div class="sstsl"> 包含<? if(isset($word)) { ?>关键词“<span class="highlight"><?=$word?></span>”<? } else { ?>标签“<span class="highlight"><?=$tag?></span>”<? } ?> 的搜索结果</div>
    <div class="ssts">
        <div class="sstsr">&nbsp;&nbsp;共找到 <span class="qcount"><?=$rownum?></span> 条结果</div>
    </div>
    <div class="clr"></div>
    <div class="searchleft">
        <div class="wtlb">
            <? if($rownum) { ?>            <ul>
                
<? if(is_array($questionlist)) { foreach($questionlist as $question) { ?>
                <li>
                    <h3><a href="<?=SITE_URL?>?q-<?=$question['id']?>.html" target="_blank" class="answer_link"><?=$question['title']?></a></h3>
                    <? if($question['description']) { ?><p class="jj"><? echo cutstr($question['description'],160); ?></p><? } ?>                    <p class="sm1"><a target="_blank" href="<?=SITE_URL?>?u-<?=$question['authorid']?>.html"><?=$question['author']?></a>&nbsp;-&nbsp;<?=$question['format_time']?>&nbsp;-&nbsp;浏览:<?=$question['views']?>&nbsp;-&nbsp;回答:<?=$question['answers']?>&nbsp;-&nbsp;<a href="<?=SITE_URL?>?c-<?=$question['cid']?>.html" target="_blank" title="<?=$question['category_name']?>"><?=$question['category_name']?></a></p>
                </li>
                
<? } } ?>
            </ul>
            <? } else { ?>            <center>没有相关查询内容！</center>
            <? } ?>        </div>
        <div class="pages"><div class="scott"><?=$departstr?></div></div>
    </div>	
    <div class="right1">
        <div class="gg">
            <div class="ggtitle">
                <ul>
                    <li class="gga11"></li>
                    <li class="gga21">
                        <div class="juzhong">热点关键词</div>
                    </li>
                    <li class="gga31"></li>
                </ul>
            </div>
            <div class="clr"></div>
            <div class="ggcon">
                
<? if(is_array($wordslist)) { foreach($wordslist as $hotword) { ?>
                <a <? if($hotword['qid']) { ?>href="<?=SITE_URL?>?q-<?=$hotword['qid']?>.html" <? } else { ?>href="<?=SITE_URL?>?question/search/3/<?=$hotword['w']?>.html"<? } ?>><?=$hotword['w']?></a>
                
<? } } ?>
   
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
    <div class="clr"></div>
</div>
<? include template('footer'); ?>
