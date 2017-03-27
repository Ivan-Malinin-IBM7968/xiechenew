function check_login(){
	var txtUserNameTopHeader = $("#txtUserNameTopHeader").val();
	var txtUserPwdHeader = $("#txtUserPwdHeader").val();
	if (txtUserNameTopHeader == ""){
		window.location.href="/myhome";
	}
	$.ajax({
		type: "POST",
		url: "/index.php/public/logincheck",
		cache: false,
		dataType:"text",
		data:"username="+txtUserNameTopHeader+"&password="+txtUserPwdHeader+"&logintype=top",			
		success: function(data){
			if(data==2){
				alert("登陆成功");
			}else if(data==3){
				alert("帐号错误");
			}else if(data==4){
				alert("密码必须");
			}else if(data==0){
				alert("登陆失败");
			}else{
				//var login_html = '<ul><!--login--><li>您好,</li><li onmouseout="" onmouseover="" class="bit_link"><strong><a href="/index.php/myhome" id="aHeaderUserName" target="_blank">'+data+'<em></em></a><sub></sub></strong></li><li onmouseout="" onmouseover="" class="bit_link" id="myfocuscar"><strong><a href="/index.php/myhome/mycarlist">我的车辆<em></em></a><sub></sub></strong></li><li class="lastB"><a href="/index.php/public/logout" target="_self">[退出]</a></li><!--/login--></ul>';
				//$("#divLoginDivID").html(login_html);
				 location.reload();
				//url = url+"/membercoupon_id/"+data;
				//window.location.href=url;
			}
		}
	})
}