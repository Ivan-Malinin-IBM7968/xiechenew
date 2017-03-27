<? if(!defined('IN_TIPASK')) exit('Access Denied'); include template('header'); ?>
<div class="content">
    <div class="uleft">
        <div class="tximg"><img width="100px" height="100px" src="<?=$user['avatar']?>" alt="<?=$user['username']?>" title="<?=$user['username']?>"></div>
        <div class="txname">
            <?=$user['username']?>
            <? if($user['islogin']) { ?>            <img src="<?=SITE_URL?>css/default/online.gif" align="absmiddle" title="当前在线" alt="当前在线"/>
            <? } else { ?>            <img src="<?=SITE_URL?>css/default/outline.gif" align="absmiddle" title="当前离线" alt="当前离线"/>
            <? } ?>        </div>
        <div class="clr"></div>
        <div class="list">
            <h1 class="on"><a title="我的主页" target="_top" href="<?=SITE_URL?>?user/score.html"><img width="16" height="16" align="absmiddle" src="<?=SITE_URL?>css/default/myhome.gif" /> &nbsp;我的主页</a></h1>
            <h1><a title="我的问答" target="_top" href="<?=SITE_URL?>?user/ask/2.html"><img width="16px" height="15px" align="absmiddle" src="<?=SITE_URL?>css/default/myanswer.gif" /> &nbsp;我的问答</a></h1>            
            <h1 class=""><a title="我的消息" target="_top" href="<?=SITE_URL?>?message/new.html"><img width="16px" height="16px" align="absmiddle" src="<?=SITE_URL?>css/default/mymsg.gif" /> &nbsp;我的消息</a></h1>
            <h1 class=""><a title="我收藏的问题" target="_top" href="<?=SITE_URL?>?user/favorite.html"><img width="14" height="15" align="absmiddle" src="<?=SITE_URL?>css/default/mycollect.gif" /> &nbsp;我的收藏</a></h1>
        </div>
    </div>
    <div class="uright">
        <div class="tab2" id="tab">
            <ul>
                <li class="on">
                    <a href="<?=SITE_URL?>?user/profile.html">个人资料</a>
                </li>
                <li>
                    <a href="<?=SITE_URL?>?user/uppass.html">修改密码</a>
                </li>
                <li>
                    <a href="<?=SITE_URL?>?user/editimg.html">修改头像</a>
                </li>
            </ul>
            <div class="clr"></div>
        </div>
        <div class="grxx">
            <form name="upinfoForm"  action="<?=SITE_URL?>?user/profile.html" method="post">
                <div class="listx"><h3>用户名</h3><?=$user['username']?></div>
                <div class="clr"></div>
                <div class="listx"><h3>性别</h3>
                    <input type="radio" value="1" name="gender" <? if((1 == $user['gender'])) { ?> checked <? } ?> />男 
                    <input type="radio" value="0" name="gender" <? if((0 == $user['gender'])) { ?> checked <? } ?>/>女 
                    <input type="radio" value="2" name="gender" <? if((2 == $user['gender'])) { ?> checked <? } ?> />保密
                </div>
                <div class="clr"></div>
                <div class="listx"><h3>E-mail</h3>
                    <div class="jutix"><input type="text" class="input3" value="<?=$user['email']?>" size="62" id="email" name="email">
                    </div></div>
                <div class="clr"></div>
                <div class="listx"><h3>生日</h3>
                    <? $bdate=explode("-",$user['bday']); ?>                    <select id="birthyear" name="birthyear" onchange="showbirthday();">
                        <? $yearlist = range(1911,2010); ?>                        
<? if(is_array($yearlist)) { foreach($yearlist as $year) { ?>
                        <option value="<?=$year?>" <? if($bdate['0']==$year) { ?>selected<? } ?> ><?=$year?></option>
                        
<? } } ?>
                    </select> 年
                    <select id="birthmonth" name="birthmonth" onchange="showbirthday();">
                        <? $monthlist = range(1,12); ?>                        
<? if(is_array($monthlist)) { foreach($monthlist as $month) { ?>
                        <option value="<?=$month?>" <? if($bdate['1']==$month) { ?>selected<? } ?>><?=$month?></option>
                        
<? } } ?>
                    </select> 月
                    <select id="birthday" name="birthday">
                        <? $dayhlist = range(1,31); ?>                        
<? if(is_array($dayhlist)) { foreach($dayhlist as $day) { ?>
                        <option  value="<?=$day?>" <? if($bdate['3']==$day) { ?>selected<? } ?>><?=$day?></option>
                        
<? } } ?>
                    </select>日
                </div>
                <div class="clr"></div>
                <div class="listx"><h3>联系电话</h3>
                    <input type="text" class="input3" size="62" name="phone" value="<?=$user['phone']?>" />
                </div>
                <div class="clr"></div>
                <div class="listx"><h3>QQ</h3>
                    <input type="text" class="input3" size="20"  name="qq" value="<?=$user['qq']?>"/>
                </div>
                <div class="clr"></div>
                <div class="listx"><h3>MSN帐号</h3>
                    <input type="text" class="input3"  size="20" name="msn" value="<?=$user['msn']?>" />
                </div>
                <div class="clr"></div>
                <!--
                <div class="listx"><h3>隐私设定</h3>
                    <div class="jutix">
                        <input type="radio" name="Privacy" value="0">公开全部信息
                        <input type="radio" name="Privacy" value="1">公开部分信息(公开QQ/MSN/Email/个人主页)<br>
                        <input type="radio" name="Privacy" value="2">完全保密(别人只能查看你的昵称)
                    </div>
                </div>
                <div class="clr"></div>
                -->
                <div class="listx"><h3>消息设置</h3>
                    <div class="jutix">
                        <input type="checkbox" <? if(1 & $user['isnotify']) { ?>checked<? } ?> value="1" name="messagenotify" />&nbsp;站内消息&nbsp;
                               <input type="checkbox" <? if(2 & $user['isnotify']) { ?>checked<? } ?> value="2" name="mailnotify" />邮件通知
                    </div>
                </div>
                <div class="clr"></div>
                <div class="listx"><h3>自我简介</h3>
                    <div class="jutix">
                        <textarea  name="signature" id="signature" wrap="PHYSICAL" rows="4" cols="40" ><?=$user['signature']?></textarea><br>最多500个汉字。
                    </div>
                </div>
                <div class="clr"></div>
                <div class="shur2"><input type="submit" class="button4" value="提&nbsp;交" name="submit" />
                </div>
            </form>
        </div>
    </div>
</div>
<div class="clr"></div>
<? include template('footer'); ?>
