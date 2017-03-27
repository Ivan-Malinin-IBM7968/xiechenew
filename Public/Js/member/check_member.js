$(function(){
	$("#username").blur(function(){
		var username = $("#username").val();
		if(username){
			if(/^[0-9]\d*$/.test(username)){
				$("#check_username").html("用户名不能以数字开头！");
				return false;
			}
			$.ajax({
				type: "POST",
				url: "/index.php/member/check_user_info",
				cache: false,
				data: "username="+username,
				success: function(data){
					if (data==1){
						$("#check_username").html("用户已存在，请重新输入！");
					}else{
						$("#check_username").html("");
					}
				}
			})
		}else{
			$("#check_username").html("用户名不能为空!");
		}
	})

	$("#password").blur(function(){
		var password = $("#password").val();
		if(password){
			$("#check_password").html("");
		}else{
			$("#check_password").html("密码不能为空!");
		}
	})

	$("#repassword").blur(function(){
		var repassword = $("#repassword").val();
		if(repassword){
			var password = $("#password").val();
			if(repassword==password){
				$("#check_repassword").html("");
			}else{
				$("#check_repassword").html("两次输入的密码不一样!");
			}
		}else{
			$("#check_repassword").html("确认密码不能为空!");
		}
	})
	
	$("#email").blur(function(){
		var email = $("#email").val();
		if(email){
			if(/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/.test(email)){
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
			}else{
				$("#check_mobile").html("手机号格式不正确！");
			}
		}else{
			$("#check_mobile").html("手机号不能为空!");
		}
	})
})