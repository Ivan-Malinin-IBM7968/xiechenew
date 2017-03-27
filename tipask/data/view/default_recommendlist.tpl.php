<? if(!defined('IN_TIPASK')) exit('Access Denied'); include template('header'); ?>
<div class="content">
    
<? if(is_array($topiclist)) { foreach($topiclist as $topic) { ?>
    <div class="contzt">
        <div class="contzt1">
            <div class="juzhong">&nbsp;<?=$topic['title']?><a name="<?=$topic['id']?>"></a></div>            
        </div>
        <div class="contzt2">
            <div class="jianjie"><img src="<?=SITE_URL?><?=$topic['image']?>" width="270px" height="222px"/></div>
            <div class="contzt2con">
                <div class="contzt2contt"><?=$topic['describtion']?></div>
                <div class="wtlist">
                    <ul>
                        
<? if(is_array($topic['questionlist'])) { foreach($topic['questionlist'] as $question) { ?>
                        <li><a class="hotTitscontlia" title="<?=$question['title']?>" target="_blank" href="<?=SITE_URL?>?q-<?=$question['id']?>.html"><? echo cutstr($question['title'],38); ?></a> </li>
                        
<? } } ?>
                    </ul>
                </div>                
                <div class="clr"></div>
            </div>
            <div class="clr"></div>
        </div>
        <div class="contzt3"></div>        
    </div>
    <div class="clr"></div>
    
<? } } ?>
    <div class="pages"><div class="scott"><?=$departstr?></div></div>
</div>
<div class="clr"></div>
<? include template('footer'); ?>
