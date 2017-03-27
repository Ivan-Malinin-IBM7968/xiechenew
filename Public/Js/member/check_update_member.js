$(function(){
	$("#email").blur(function(){
		var email = $("#email").val();
		if(email){
			if(/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/.test(email)){
				var old_emil = $("#old_email").val();
				if(email!=old_emil){
					$.ajax({
						type: "POST",
						url: "/index.php/member/check_user_info",
						cache: false,
						data: "email="+email,
						success: function(data){
							if (data==1){
								$("#check_email").html("邮箱已存在，请重新输入！");
							}else{
								$("#check_email").html("");
							}
						}
					})
				}
			}else{
				$("#check_email").html("邮箱格式不正确！");
			}
		}else{
			$("#check_email").html("邮箱不能为空!");
		}
	})

	$("#mobile").blur(function(){
		var mobile = $("#mobile").val();
		if(mobile){
			if(/^1\d{10}$/.test(mobile)){
				var old_mobile = $("#old_mobile").val();
				if(mobile!=old_mobile){
					$.ajax({
						type: "POST",
						url: "/index.php/member/check_user_info",
						cache: false,
						data: "mobile="+mobile,
						success: function(data){
							if (data==1){
								$("#check_mobile").html("手机号已存在，请重新输入！");
							}else{
								$("#check_mobile").html("");
							}
						}
					})
				}
			}else{
				$("#check_mobile").html("手机号格式不正确！");
			}
		}else{
			$("#check_mobile").html("手机号不能为空!");
		}
	})
})