<? if(!defined('IN_TIPASK')) exit('Access Denied'); include template('header'); ?>
<div class="content">
    <div class="cleftbox">
        <div class="tag">
            <div class="tagtitle">
                <ul>
                    <li class="tga1"></li>
                    <li class="tga2">&nbsp;标签大全&nbsp;&nbsp;</li>
                    <li class="tga3"></li>
                </ul>
            </div>
            <div class="tagcon">
                <div id="tab_c">
                    <dl>
                        <? if(1==$user['groupid']) { ?>                        <form name="tagform" action="<?=SITE_URL?>?index/deletetag.html" method="POST">
                            
<? if(is_array($taglist)) { foreach($taglist as $tag) { ?>
                            <dt><input type="checkbox" name="tid[]"  value="<?=$tag['id']?>"/>&nbsp;<a href="<?=SITE_URL?>?question/tag/<?=$tag['name']?>.html"><?=$tag['name']?></a><span class="dlnum">(<?=$tag['questions']?>)</span></dt>
                            
<? } } ?>
                        </form>
                        <? } else { ?>                        
<? if(is_array($taglist)) { foreach($taglist as $tag) { ?>
                        <dt><a href="<?=SITE_URL?>?question/tag/<?=$tag['name']?>.html"><?=$tag['name']?></a><span class="dlnum">(<?=$tag['questions']?>)</span></dt>
                        
<? } } ?>
                        <? } ?>                    </dl>
                    <br class="clear">
                </div>
            </div>
            <div class="tagbuttom">
                <ul>
                    <li class="tgba1"></li>
                    <li class="tgba2"></li>
                    <li class="tgba3"></li>
                </ul>
            </div>
        </div>
        <? if(1==$user['groupid']) { ?><div style="text-align: left;"><input type="button" name="sumit" value="提交删除" class="button1" onclick="javascript:if(confirm('确认删除？')) document.tagform.submit();" /></div><? } ?>        <div class="pages"><div class="scott"><?=$departstr?></div></div>

    </div>
    <div class="right1">
        <div class="gg">
            <div class="ggtitle">
                <ul>
                    <li class="gga11"></li>
                    <li class="gga21">
                        <div class="juzhong">
                            <div class="qico" >
                                <div class="ivote"></div>
                            </div>
                            积分排行榜
                        </div>
                    </li>
                    <li class="gga31"></li>
                </ul>
            </div>
            <div class="clr"></div>
            <div class="ggcon">
                <div class="l_b_m_m">
                    <div class="yshy" id="alltop">
                        <ul>
                            <? $alluserlist=$this->fromcache('alluserlist'); ?>                            
<? if(is_array($alluserlist)) { foreach($alluserlist as $index => $alluser) { ?>
                            <? ++$index; ?>                            <li>
                                <span class="hyname"><img align="absmiddle" src="<?=SITE_URL?>css/default/num<?=$index?>.gif"> <a target="_blank" href="<?=SITE_URL?>?u-<?=$alluser['uid']?>.html"><?=$alluser['username']?></a></span>
                                <img align="absmiddle" src="<?=SITE_URL?>css/default/up.gif"><?=$alluser['credit1']?>
                            </li>
                            
<? } } ?>
                        </ul>
                    </div>
                    <div class="listcon"></div>
                    <div class="more"><a href="<?=SITE_URL?>?us-2.html" title="查看更多排行榜" ttarget="_top">查看更多排行榜&gt;&gt;</a></div>
                    <div class="clr"></div>
                </div>
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
