<script language="javascript" type="text/javascript">
    var secs =10; //倒计时的秒数
    var URL ;
    function Load(url){
        URL =url;
        for(var i=secs;i>=0;i--)
        {
            window.setTimeout('doUpdate(' + i + ')', (secs-i) * 1000);
        }
    }

    function doUpdate(num)
    {
        document.getElementById("ShowDiv").innerHTML = '将在<strong><font color=red> '+num+' </font></strong>秒后自动转入<a  href="http://www.xieche.com.cn" class="link_1">携车网首页</a>，请稍候...' ;
        if(num == 0) { window.location=URL;  }
    }
    $(function(){
	Load('http://www.xieche.com.cn');
    })
  </script>

<style>
    .error{margin: 60px auto 300px;}
    .error h2{margin: 0 auto; text-align: center; font-size: 18px; font-weight: normal; }
    .error #ShowDiv{ margin: 20px; font-size: 14px; font-weight: normal; text-align: center;}
</style>
<div class="error" >
	<h2>抱歉，您请求的页面现在无法打开！</h2>
	<div id="ShowDiv">将在<strong><font color="red">10</font></strong>秒后自动转入<a  href="http://www.xieche.com.cn" class="link_1">携车网首页</a>，请稍候...</div>
</div>