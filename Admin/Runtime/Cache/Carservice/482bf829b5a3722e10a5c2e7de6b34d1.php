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

<style>
    #main{
        width:100% ;
        
    }
    
    tr.techorder {
        
        border-bottom: 2px solid red ;
    }
    
    .techname{
        width: 100px ;
        font-size: 30px ;
        color : red ;
    }
    
    #orderinfo{
      width: 100% ;
      height: 100% ;
    }
    
    .changebg{
        background: #cdd ;
    }
    
    .red{
        background: red ;
    }
    
    #code{
        width: 300px ;
        height: 25px ;
        margin-top: 5px ;
        margin-right: 20px ;
    }
    
    
    
    #myform{
        height: 40px ;
        padding-bottom : 3px;
    }
    
    #fixp{
        height: 40px ;
        width: 99% ;
        margin: 0 ;
        padding:  0 ;
        position: fixed ;
         background: none repeat scroll 0 0 #f5f5f5;
        
        
    }
    
    #msg{
      color: red ;
      font-size:  22px ;
      display: none ;
        
    }
    
    #btn{
        float:right ;
         margin-right: 200px;
        margin-top: 10px;
    }
    .p_title{
        clear: both;
        height: 40px;
        width: 98%;
        margin: 0 ;
        color: red;
        font-size: 20px;
        
    }
    
</style>

<script type='text/javascript' src='__WEB__/Public/Admin/Js/Xheditor/jquery/jquery-1.4.4.min.js'></script>

<script type='text/javascript'>
 
 $(function(){
    //绑定按钮事件
    $('#bind').bind('click',function(){
        //未绑定配件字符串
        var  nobindstr = '' ;
        //选取没有绑定的配件
        //var  nobindfit =  $(".techorder").find("span[name='']").not("[id='']");
        var  nobindfit =  $("span[name='']").not("[id='']");
        
        $.each(nobindfit,function(k,v){
            var idAndname = '' ;
            idAndname += $(v).attr('id')+ '@' + $.trim($(v).html())+'@'+$(v).attr('type') +',';
            nobindstr += idAndname ;
        });
        
        $('#nobind_fit').val(nobindstr);
        //提交表单 
        $('#myform').attr('action','__URL__/nobind_fit');  
        $('#myform').attr('target','_blank'); 
        $('#myform').submit();      
            
    });      
     
        

     //按钮提交事件
     $('#btn').bind('click',function(){
         //如何获取和保存漏件信息
         //漏件集合字符串
        var lack_fit = '';
        
        //orderid 单元格对象集合
        var orderidArr = $("#list").find('.orderid') ;
        //循环对象集合
        $.each(orderidArr,function(k,v){
            //返回当前orderid单元格之后的没有高亮的兄弟节点集合
            var fitArr = $(v).nextAll().not('.changebg').find("span").not("[name='']");
            //判断对象集合是否为空
            if(fitArr.length){
                //获取订单id
                var orderid = $.trim($(v).html());
                
                var fitname = '|';
                $.each(fitArr,function(key,value){
                   fitname += $.trim($(value).html())+'@'+ $.trim($(value).attr('name'))+ '|';
                });
                //单个单元格无高亮字符串
                var $orderidStr = orderid + fitname ;  
                 //累加漏件集合字符串
                lack_fit += $orderidStr + ',' ; 
            }
        });
        

        //alert(lack_fit);
        $('#lack_fit').val(lack_fit);
        
        //提交表单 
        $('#myform').attr('action','__URL__/error_fit');  
        $('#myform').attr('target','_blank'); 
        $('#myform').submit();   
         
     });
     
     
     
     
     //错件信息字符串变量
     var error_fit = '';
     
     //回车自动提交表单 。在中文输入法下面无法运行。
    document.onkeydown = function(evt){
        
        var evt = window.event?window.event:evt;
        if(evt.keyCode==13)
        {
            //获取code值
            var code = $.trim($("#code").val());
            //给code添加分隔符
            var newcode = code + '@' ;
            //用newcode替换文本框原本字符串
            $('#code').val(newcode);

           //分割newcode获取最后一个元素
           var splitstr = new Array();
           splitstr = newcode.split('@');
           //记住，这里一定是减2。
           var index = splitstr.length - 2 ; 
           //获取最后一个二维码
           var lastcode = splitstr[index] ; 
           
           //判断元素集合是否为空
           // jq获取元素集合,返回所有符合条件但没有changebg元素的集合,判断集合是否为空。
           if($("#list").find("span[name='"+lastcode+"']").parent().not(".changebg").length){
                //获取返回集合的第一个元素。
                var highlight = $("span[name='"+lastcode+"']").parent().not(".changebg").eq(0); 
           }else{
               //显示错件信息
               $('#msg').show(500).delay(5000).hide(500) ;
               //如何保存错件信息
               error_fit += lastcode + '@' ;
               $('#error_fit').val(error_fit);
               
                return false ;
           }
            

           //窗口可见高度
           var availHeight  =  $(window).height();  
            
           //获取某元素距离文档顶端的距离.此方法不通用，html如发生改变，要重新计算。
                //取元素相对于表格的offset().top
                var  highlight_top = highlight.offset().top ; 
                //取第一行的盒子高度
               // var rowouter = $('.row').outerHeight() ;
                //取表单的盒子高度
                var myform = $('#myform').outerHeight() ;
                //元素相对于doc的高度
                var  eletodoc = highlight_top + myform ;  
            
            
            //获取元素已经滚上去的高度
            var scrollTop = document.documentElement.scrollTop || document.body.scrollTop;
            
          
            
            if(eletodoc >= (scrollTop + availHeight)){
                //如果元素处于可见区域下方，向上移动滚动条 。
                var y = eletodoc - scrollTop - availHeight + availHeight/2 ; 
                var newy = scrollTop + y ;
                
                //使文本框失去焦点
                 $('#code')[0].blur();
                 window.scrollTo(0,newy);
                 //让文本框重新获取焦点
                 $('#code')[0].focus();
            }else if(eletodoc <= scrollTop){
                //如果元素位于可见区域上方 ,向下移动滚动条
                var y = scrollTop - eletodoc + availHeight/2 ; 
                var newy = scrollTop - y ;
                
                //使文本框失去焦点
                 $('#code')[0].blur();
                 window.scrollTo(0,newy);
                 //让文本框重新获取焦点
                 $('#code')[0].focus();
            }
            
             
            //先添加红色背景，5秒移除。
            highlight.addClass("red"); 
            setTimeout(function(){highlight.removeClass("red");},5000);
            
            //为元素添加changebg样式
            highlight.addClass("changebg") ;      

            return false ;
        }
        
       
    }
    
    
            
 });
 

 </script>

<!-- 主页面开始 -->


<div id="main"  >
    <FORM METHOD=POST  id="myform" >
        <p id="fixp">
            二维码框： <input TYPE="text"   NAME="code" id="code" style="ime-mode:Disabled;">
            <span id="msg" name="msg"> 错件信息提示 ：查无此配件  </span>
            
                <input TYPE="hidden"   NAME="nobind_fit"  id="nobind_fit" value="">
                <input TYPE="button"   NAME="bind"  id="bind" value="绑定未绑定配件">
            
                <input TYPE="hidden"   NAME="error_fit"  id="error_fit" value="">
                <input TYPE="hidden"   NAME="lack_fit"  id="lack_fit" value="">
                <input TYPE="button"   NAME="btn"  id="btn" value="查看错件和漏件">
        </p>

    </FORM>

<!-- 主体内容  -->
<div class="content" >


<!-- 列表显示区域  -->
<div class="list" >
    
   
<table class="list" id="list">
<tr class="row">
    <th> 技师姓名</th>
    <th>
        <table class="list" >
        <tr class="row"> 
            <th width='20%'> 订单id</th>
            <th width='20%'> 机油</th>
            <th width='20%'>  机油滤清器  </th>
            <th width='20%'> 空气滤清器  </th>
            <th width='20%'> 空调滤清器   </th>
            
        </tr>     
        </table>
    </th>
</tr>

<?php if(is_array($technicianAndOrder)): $i = 0; $__LIST__ = $technicianAndOrder;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr  class="techorder">
    <td class="techname" > <?php echo ($vo["truename"]); ?> </td>   
    <td>
        <table class="list">
        <?php if(is_array($vo['order'] )): $i = 0; $__LIST__ = $vo['order'] ;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$sub): $mod = ($i % 2 );++$i;?><tr class="row"> 
            <td width='20%' class="orderid"> <?php echo ($sub["id"]); ?> (所属技师：<?php echo ($vo["truename"]); ?>) </td>
            <td width='20%' class="fitcode"> 
                <span name='<?php echo ($sub["fitnameAndCode"]["oil_code"]); ?>' id='<?php echo ($sub["fitnameAndCode"]["oil_id"]); ?>' type="oil">
                    <?php if($sub[fitnameAndCode][oil_name]): echo ($sub["fitnameAndCode"]["oil_name"]); endif; ?>
                </span>
            </td>
            <td width='20%' class="fitcode"> 
                <span name='<?php echo ($sub["fitnameAndCode"]["filter_code"]); ?>' id='<?php echo ($sub["fitnameAndCode"]["filter_id"]); ?>' type="filter">
                    <?php if($sub[fitnameAndCode][filter_name]): echo ($sub["fitnameAndCode"]["filter_name"]); endif; ?>
                </span>
            </td>
            <td width='20%' class="fitcode"> 
                <span name='<?php echo ($sub["fitnameAndCode"]["kongqi_code"]); ?>' id='<?php echo ($sub["fitnameAndCode"]["kongqi_id"]); ?>' type="kongqi_filter">
                    <?php if($sub[fitnameAndCode][kongqi_name]): echo ($sub["fitnameAndCode"]["kongqi_name"]); endif; ?>
                </span>
            </td>
            <td width='20%' class="fitcode">
                <span name='<?php echo ($sub["fitnameAndCode"]["kongtiao_code"]); ?>' id='<?php echo ($sub["fitnameAndCode"]["kongtiao_id"]); ?>' type="kongtiao_filter">
                    <?php if($sub[fitnameAndCode][kongtiao_name]): echo ($sub["fitnameAndCode"]["kongtiao_name"]); endif; ?>
                 </span>
            </td>
            
            
        </tr><?php endforeach; endif; else: echo "" ;endif; ?>
        </table>
        
    </td>       
</tr><?php endforeach; endif; else: echo "" ;endif; ?>

</table>

<br>
<br>
<br>
<p class="p_title"> 未分配技师订单  </p>   
<table class="list">
	<tr class="row"> 
		<th width='20%'> 订单id</th>
		<th width='20%'> 机油</th>
		<th width='20%'>  机油滤清器  </th>
		<th width='20%'> 空气滤清器  </th>
		<th width='20%'> 空调滤清器   </th>
				
	</tr> 
	<?php if(is_array($unAllocateOrder)): $i = 0; $__LIST__ = $unAllocateOrder;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$sub): $mod = ($i % 2 );++$i;?><tr class="row"> 
		<td width='20%' class="orderid"> <?php echo ($sub["id"]); ?>  </td>
		<td width='20%' class="fitcode"> 
			<span name='<?php echo ($sub["fitnameAndCode"]["oil_code"]); ?>' id='<?php echo ($sub["fitnameAndCode"]["oil_id"]); ?>' type="oil">
				<?php if($sub[fitnameAndCode][oil_name]): echo ($sub["fitnameAndCode"]["oil_name"]); endif; ?>
			</span>
		</td>
		<td width='20%' class="fitcode"> 
			<span name='<?php echo ($sub["fitnameAndCode"]["filter_code"]); ?>' id='<?php echo ($sub["fitnameAndCode"]["filter_id"]); ?>' type="filter">
				<?php if($sub[fitnameAndCode][filter_name]): echo ($sub["fitnameAndCode"]["filter_name"]); endif; ?>
			</span>
		</td>
		<td width='20%' class="fitcode"> 
			<span name='<?php echo ($sub["fitnameAndCode"]["kongqi_code"]); ?>' id='<?php echo ($sub["fitnameAndCode"]["kongqi_id"]); ?>' type="kongqi_filter">
				<?php if($sub[fitnameAndCode][kongqi_name]): echo ($sub["fitnameAndCode"]["kongqi_name"]); endif; ?>
			</span>
		</td>
		<td width='20%' class="fitcode">
			<span name='<?php echo ($sub["fitnameAndCode"]["kongtiao_code"]); ?>' id='<?php echo ($sub["fitnameAndCode"]["kongtiao_id"]); ?>' type="kongtiao_filter">
				<?php if($sub[fitnameAndCode][kongtiao_name]): echo ($sub["fitnameAndCode"]["kongtiao_name"]); endif; ?>
			 </span>
		</td>
	</tr><?php endforeach; endif; else: echo "" ;endif; ?>
</table>
        
    
</div>


<!-- 列表显示区域结束 -->
</div>
<!-- 主体内容结束 -->
</div>
<!-- 主页面结束 -->