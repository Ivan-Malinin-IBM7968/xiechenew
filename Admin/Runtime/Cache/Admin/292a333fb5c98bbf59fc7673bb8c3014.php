<?php if (!defined('THINK_PATH')) exit();?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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
<!-- 菜单区域  -->

<!-- 主页面开始 -->
<div id="main" class="main" >
    <script type="text/javascript" src="Js.Util.JTree"></script> 
    <!-- 主体内容  -->
    <div class="content" >
        <div class="title"> 应用授权 [ <a href="__URL__">返回</a> ]</div>
        <!--  功能组区域  -->
        <SCRIPT LANGUAGE="JavaScript">
            <!--
            function saveAccess(){
                ThinkAjax.sendForm('form1','__URL__/setApp/');
            }
            //-->
        </SCRIPT>
        <div id="result" class="result none"></div>
        <script type="text/javascript" src="/Public/Js/Form/MultiSelect.js"></script>
        <FORM METHOD=POST id="form1">
            <TABLE class="select" style="width:265px" align="center">
                <tr><td height="5" colspan="3" class="topTd" ></td></tr>
                <TR><Th class="tCenter">应用授权 | <A HREF="?a=module">模块授权</A> | <A HREF="?a=action">操作授权</A>
                    </Th></TR>
                <TR><Td class="tRight">当前组：<select id="" name="groupId" onchange="location.href = '?groupId='+this.options[this.selectedIndex].value;" ondblclick="" class="medium" ><option value="" >选择组</option><?php  foreach($groupList as $key=>$val) { if(!empty($selectGroupId) && ($selectGroupId == $key || in_array($key,$selectGroupId))) { ?><option selected="selected" value="<?php echo $key ?>"><?php echo $val ?></option><?php }else { ?><option value="<?php echo $key ?>"><?php echo $val ?></option><?php } } ?></select>
                </Td></TR>
                <TR><TD >
                <select id="groupAppId" name="groupAppId[]" ondblclick="" onchange="" multiple="multiple" class="multiSelect" size="15" ><?php  foreach($appList as $key=>$val) { if(!empty($groupAppList) && ($groupAppList == $key || in_array($key,$groupAppList))) { ?><option selected="selected" value="<?php echo $key ?>"><?php echo $val ?></option><?php }else { ?><option value="<?php echo $key ?>"><?php echo $val ?></option><?php } } ?></select>
                </td>
                </tr>
                <tr>
                    <td  class="row tCenter" >
                        <input TYPE="button" onclick="allSelect()" value="全 选" class="submit  ">&nbsp;
                        <input TYPE="button" onclick="InverSelect()" value="反 选" class="submit  ">&nbsp;
                        <input TYPE="button" onclick="allUnSelect()" value="全 否" class="submit ">&nbsp;
                        <input TYPE="button" onclick="saveAccess()" value="保 存" class="submit  ">&nbsp;
                        <input TYPE="hidden" NAME="groupId" VALUE="<?php echo ($_GET['id']); ?>" >
                        <input TYPE="hidden" NAME="module" value="Node">
                        <input TYPE="hidden" name="ajax" VALUE="1">
                    </td>
                </tr>
                <tr>
                    <td height="5" class="bottomTd" >
                    </td>
                </tr>
            </TABLE>
        </FORM>
    </div>
    <!-- 主体内容结束 -->
</div>
<!-- 主页面结束 -->