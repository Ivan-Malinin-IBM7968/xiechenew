<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <HEAD>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <TITLE>『<?php echo (C("web_name")); ?>管理平台』By ThinkPHP <?php echo (THINK_VERSION); ?></TITLE>
        <link rel="stylesheet" type="text/css" href="__CSS__/blue.css" />
        <style>
            html{overflow-x : hidden;}
        </style>
        <base target="main" />
    </HEAD>

    <body >
        <div id="menu" class="menu">
            <TABLE class="list shadow" cellpadding=0 cellspacing=0 >
                <tr>
                    <td height='5' colspan=7 class="topTd" ></td>
                </tr>
                <TR class="row" >
                    <th class="tCenter space"><IMG SRC="__IMG__/home.gif" WIDTH="16" HEIGHT="16" BORDER="0" ALT="" align="absmiddle"> <?php if(isset($_GET['title'])): echo ($_GET['title']); endif; if(!isset($_GET['title'])): ?>后台首页<?php endif; ?> </th>
                </TR>
                <?php if(is_array($menu)): $i = 0; $__LIST__ = $menu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i; if(($item['group_id']) == $menuTag): if(($item['access']) == "1"): ?><TR class="row " >
<!--                                <TD><div style="margin:0px 5px"><IMG SRC="__IMG__/comment.gif" WIDTH="9" HEIGHT="9" BORDER="0" align="absmiddle" ALT=""> <A HREF="__APP__/<?php echo ($item['name']); ?>/index/" id="<?php echo ($key); ?>"><?php echo ($item['title']); ?></A></div></TD>-->
                                <TD><div style="margin:0px 5px"><IMG SRC="__IMG__/comment.gif" WIDTH="9" HEIGHT="9" BORDER="0" align="absmiddle" ALT=""> <A HREF="__APP__/<?php echo ($item['name']); ?>/<?php echo ($item['action']); ?>/" id="<?php echo ($key); ?>"><?php echo ($item['title']); ?></A></div></TD>

                            </TR><?php endif; endif; endforeach; endif; else: echo "" ;endif; ?>
                <tr>
                    <td height='5' colspan=7 class="bottomTd"></td>
                </tr>
            </TABLE>
        </div>
        <script language="JavaScript">
            <!--
            function refreshMainFrame(url){
                parent.main.document.location = url;
            }
            
            if (document.getElementsByTagName("a")[0].href)
	{
refreshMainFrame(document.getElementsByTagName("a")[0].href);		
	}
            //-->
        </script>
    </body>
</html>