<? if(!defined('IN_TIPASK')) exit('Access Denied'); $setting=$this->setting; ?><!--相关已解决--><? if($questionlist) { ?><div class="clr"></div>
<div class="tw_t">看看以下回答是否解决了您的疑问：</div>
<div class="askfgx"></div>
<div class="xgwt">
    <ul>
        <!--相关已解决结束-->
        
<? if(is_array($questionlist)) { foreach($questionlist as $question) { ?>
        <li>
            <h1><span class="descask">问</span>：<a target="_blank" href="<?=SITE_URL?>?q-<?=$question['id']?>.html"><?=$question['title']?></a></h1>
            <p><span class="descanswer">答</span>：<? echo cutstr(strip_tags($question['bestanswer']),240) ?>[<a href="<?=SITE_URL?>?q-<?=$question['id']?>.html" taget="_blank">详情</a>]</p>
        </li>
        
<? } } ?>
    </ul>
</div><? } ?>