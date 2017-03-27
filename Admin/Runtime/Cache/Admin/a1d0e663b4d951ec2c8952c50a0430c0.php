<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<HEAD>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<TITLE>『<?php echo (C("web_name")); ?>管理平台』By ThinkPHP <?php echo (THINK_VERSION); ?></TITLE>
<link rel="stylesheet" type="text/css" href="__CSS__/blue.css" />
 <script type="text/javascript" src="__JS__/jquery-1.9.1.js"></script>
<script type="text/javascript" src="__JS__/common.js"></script>
<script type="text/javascript" src="__PUBLIC__/Js/Base.js"></script><script type="text/javascript" src="__PUBLIC__/Js/prototype.js"></script><script type="text/javascript" src="__PUBLIC__/Js/mootools.js"></script><script type="text/javascript" src="__PUBLIC__/Js/Think/ThinkAjax.js"></script><script type="text/javascript" src="__PUBLIC__/Js/Form/CheckForm.js"></script><script type="text/javascript" src="__PUBLIC__/Js/Util/ImageLoader.js"></script>



<SCRIPT LANGUAGE="JavaScript">
//<!--
//指定当前组模块URL地址 
var URL = '__URL__';
var APP	 =	 '__APP__';
var PUBLIC = '__PUBLIC__';
ThinkAjax.image = [	 '__IMG__/loading2.gif', '__IMG__/ok.gif','__IMG__/update.gif' ]
ImageLoader.add("__IMG__/bgline.gif","__IMG__/bgcolor.gif","__IMG__/titlebg.gif");
ImageLoader.startLoad();

//-->
</SCRIPT>


</HEAD>

<body onload="loadBar(0)">
<div id="loader" >页面加载中...</div>
<style>
  .popbox{width:600px; height: 440px font-size: 14px; border: 1px solid #aaaaaa; border-radius: 5px; background: #FFFFFF; position: fixed; left: 50%; margin-left: -300px; top: 50%; margin-top:-220px; _position:absolute; display: none;}
  .popbox .popboxtop{height: 40px; border-bottom: 1px solid #aaaaaa; line-height: 40px;}
  .popbox .popboxtop a{float: right; height: 40px; width: 40px; text-align: center; border-left: 1px solid #aaa; font-size: 18px; cursor: pointer;}
  .popbox .popboxtop a:hover{background: #ccc;}
  .popbox .popboxtop h3{font-size: 14px; margin: 0 0 0 10px; padding: 0; line-height: 40px;}
  .popbox .popboxcon{height: 400px; overflow-y: auto;}
  .popbox .popboxcon ul{margin: 0; padding: 0;}
  .popbox .popboxcon ul li{list-style: none; border-bottom: 1px dashed #ccc; line-height: 30px; color: #555555; font-size: 12px; text-indent: 10px;}
  .popbox .popboxcon ul li span{display: inline-block; width: 155px; text-indent: 0px;}
</style>

<div class="popbox" id="showlog">
  <div class="popboxtop"><a id="popclose" onclick="jQuery(this).parent().parent().hide()">×</a><h3>操作日志</h3></div>
  <div class="popboxcon" id="showlogcontent"></div>
</div>

<script>
function showlog(controller,id){
	jQuery.post('/Admin/index.php/Admin/Showlog/index',{c:controller,id:id},function(data){
		if(data){
			var html_log = '<ul>';
			jQuery.each(data,function(index,val){
				html_log += '<li>'+val.log+'</li>';
			})
			html_log += '</ul>';
			jQuery('#showlog').show();
			jQuery('#showlogcontent').html(html_log);
		}else{
	  jQuery('#showlog').show();
	  jQuery('#showlogcontent').html("<ul><li>暂无日志</li></ul>");
    }
	},'json')
}
</script>
<div id="main" class="main" >
    <div class="content">
        <div class="title">编辑<?php if(($vo["level"]) == "1"): ?>应用<?php endif; if(($vo["level"]) == "2"): ?>模块<?php endif; if(($vo["level"]) == "3"): ?>操作<?php endif; ?> [ <A HREF="__URL__">返回列表</A> ]</div>
        <FORM METHOD=POST  id="form1" >
            <TABLE cellpadding=3 cellspacing=3 >
                <TR>
                    <TD class="tRight" ><?php if(($vo["level"]) == "1"): ?>应用<?php endif; if(($vo["level"]) == "2"): ?>模块<?php endif; if(($vo["level"]) == "3"): ?>操作<?php endif; ?>名：</TD>
                    <TD class="tLeft" >
                        <input TYPE="text" class="medium bLeftRequire" check='Require' warning="<?php if(($vo["level"]) == "1"): ?>应用<?php endif; if(($vo["level"]) == "2"): ?>模块<?php endif; if(($vo["level"]) == "3"): ?>操作<?php endif; ?>名称不能为空,且不能含有空格"  NAME="name" value="<?php echo ($vo["name"]); ?>"></TD>
                </TR>
                <TR>
                    <TD class="tRight" >显示名：</TD>
                    <TD class="tLeft" ><input TYPE="text" class="medium bLeftRequire" check='Require' warning="显示名称必须" NAME="title" value="<?php echo ($vo["title"]); ?>"></TD>
                </TR>
                <?php if(($vo["level"]) == "2"): ?><TR>
                    <TD class="tRight" >组件名：</TD>
                    <TD class="tLeft" ><input TYPE="text" class="medium bLeft"  NAME="module" value="<?php echo ($vo["module"]); ?>">
                    </TD>
                </TR>
                <TR>
                    <TD class="tRight" >分 组：</TD>
                    <TD class="tLeft" >
                        <SELECT class="medium bLeft" NAME="group_id">
                            <option value="0">首页</option>
                            <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$group): $mod = ($i % 2 );++$i;?><option value="<?php echo ($group["id"]); ?>" <?php if(($group["id"]) == $vo['group_id']): ?>selected<?php endif; ?>><?php echo ($group["title"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                        </SELECT>
                    </TD>
                </TR>
                <TR>
                    <TD class="tRight">是否显示：</TD>
                    <TD class="tLeft"><SELECT class="small bLeft"  NAME="is_show">
                            <option <?php if(($vo["is_show"]) == "1"): ?>selected<?php endif; ?> value="1">显示</option>
                            <option <?php if(($vo["is_show"]) == "0"): ?>selected<?php endif; ?> value="0">不显示</option>
                        </SELECT></TD>
                </TR><?php endif; ?>
                <TR>
                    <TD class="tRight">状态：</TD>
                    <TD class="tLeft">
                        <SELECT class="small bLeft"  NAME="status">
                            <option <?php if(($vo["status"]) == "1"): ?>selected<?php endif; ?> value="1">启用</option>
                            <option <?php if(($vo["status"]) == "0"): ?>selected<?php endif; ?> value="0">禁用</option>
                        </SELECT>
                    </TD>
                </TR>
                <TR>
                    <TD class="tRight tTop">描 述：</TD>
                    <TD class="tLeft"><TEXTAREA class="large bLeft" NAME="remark"  ROWS="5" COLS="57"><?php echo ($vo["remark"]); ?></TEXTAREA></TD>
                </TR>
                <TR>
                    <TD ></TD>
                    <TD class="center">
                        <div style="width:85%;margin:5px"><input TYPE="hidden" name="id" value="<?php echo ($vo["id"]); ?>" >
                            <input TYPE="hidden" name="ajax" value="1">
                            <input TYPE="hidden" name="pid" value="<?php echo ($vo["pid"]); ?>">
                            <div class="impBtn fLeft"><input TYPE="button" value="保存" onclick="sendForm('form1','__URL__/update/','tips')" class="save imgButton"></div>
                            <div class="impBtn fRig"><input TYPE="reset" onclick="history.back(-1)" class="reset imgButton" value="取消" ></div>
                        </div></TD>
                </TR>
            </TABLE>
        </FORM>
    </div>
</div>