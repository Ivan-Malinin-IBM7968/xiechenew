<? if(!defined('IN_TIPASK')) exit('Access Denied'); $setting=$this->setting;$user=$this->user; ?><!--相关已解决--><? if(isset($userinfo)) { ?><div onmouseout="this.style.display='none';" onmouseover="this.style.display='';" class="userinfo">
    <div class="wtleft">
        <div class="touxiang"><a href="<?=SITE_URL?>?u-<?=$userinfo['uid']?>.html"><img align="absmiddle" src="<?=$userinfo['avatar']?>" alt="<?=$userinfo['username']?>" /></a></div>
        <div class="hyxinxi">
            <a href="<?=SITE_URL?>?u-<?=$userinfo['uid']?>.html" target="top"><?=$userinfo['username']?></a><br />
            <span><?=$userinfo_group['grouptitle']?></span>
        </div>
    </div>
    <div class="userinfoR">
        UID:<?=$userinfo['uid']?>
        <? if($userinfo['islogin']) { ?>        <img src="<?=SITE_URL?>css/default/online.gif" align="absmiddle" title="当前在线" alt="当前在线"/>
        <? } else { ?>        <img src="<?=SITE_URL?>css/default/outline.gif" align="absmiddle" title="当前离线" alt="当前离线"/>
        <? } ?>        <? if($userinfo['expert']) { ?>        <img src="<?=SITE_URL?>css/default/expert.gif" align="absmiddle" title="专家" />
        <? } ?>        <br />
        <span> 提问：<?=$userinfo['questions']?>&nbsp;&nbsp回答：<?=$userinfo['answers']?>&nbsp;&nbsp已采纳：<?=$userinfo['adopts']?></span><br />
        <span> 经验：<?=$userinfo['credit1']?>&nbsp;&nbsp财富：<?=$userinfo['credit2']?>&nbsp;&nbsp;魅力：<?=$userinfo['credit3']?></span>
        <? if($user['uid'] != $userinfo['uid']) { ?>        <span>
            <input  type="button" class="button1" onclick="javascript:sendmsg('<?=$userinfo['username']?>');" value="发短消息"/>
            <input type="button" class="button1" value="向TA提问" onclick="javascript:document.location='<?=SITE_URL?>?question/add/<?=$userinfo['uid']?>.html'"/>
        </span>
        <? } ?>        <div style="color:#777777;"><? echo cutstr($userinfo['signature'],100,'') ?></div>
    </div>
</div><? } ?>