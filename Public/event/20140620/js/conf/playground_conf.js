require.config( {

	paths: {
            jquery: "http://event.xieche.com.cn/20140620/js/libs/jquery",
            jquerymobile: "http://event.xieche.com.cn/20140620/js/libs/jquery.mobile-1.3.2.min"
      }
} );

require(["jquery","jquerymobile"], function( $) {

	$(document).ready(function(){

		function send_verify_show(i){
			if(i>='0'){
				$("#send_verify").attr("disabled",true); 
				$("#send_verify").html(i+"秒后重新发送");
				i--;
				setTimeout(function(){send_verify_show(i);},1000);
			}else{
				$("#send_verify").attr("disabled",false); 
				$("#send_verify").html("发送手机验证码");
			}
		}

		

	var initValue = $("#hide_Quiz_count").val();
				
	if(initValue == 0){
		// 首次进入 没有分享				
		
	}else if(initValue == 1){
		// 竞猜过一次，但是没有分享

		$("#popup-tpl").popup({
			history: false ,
		  	beforeposition: function( event, ui ) {
		  		$(this).find(".popup-title").html("亲！您已经竞猜过一次了哦！")
		  		$(this).find(".popup-desc").html("分享到朋友圈或者发送给朋友，您可以获得第二次竞猜机会！");
		  		$(this).find(".popup-left-btn").html("直接购买").attr('href','/Explosion-coupon_list');
		  	}
		});

		$("#popup-tpl").popup("open");

	}else if(initValue == 2){
		//竞猜过一次，分享过 - get one more chance

	}else if(initValue == 3){
		// 分享过 竞猜过 - no more chance
		
		$("#popup-tpl").popup({
			history: false ,
		  	beforeposition: function( event, ui ) {
		  		$(this).find(".popup-title").html("亲！每天只有两次竞猜机会哦！")
		  		$(this).find(".popup-desc").html("每天只有两次竞猜机会哦亲！请明天再来！");
		  		$(this).find(".popup-left-btn").html("查看我的竞猜").attr('href','/Explosion-result');
		  		
		  	}
		});

		$("#popup-tpl").popup("open");
		

	}else if(initValue == 'weixinnone'){
		
		$("#popup-login").popup();
		$("#popup-login").popup("open");

	};

	$("#send_verify").attr("disabled",false);
		$("#send_verify").click(function(){
			
			var mobile = $('#mobile').val();
			var second = "60";
			if(/^1\d{10}$/.test(mobile)){
				$.ajax({
					type: "POST",
					url: "/Explosion-send_verify",
					cache: false,
					data:"mobile="+mobile,
					dataType: "json",
					success: function(data){
						if(data == '1'){
							$("#send_verify").show();
							send_verify_show(second);
						}else if(data == '-1'){
							send_verify_show(second);
							alert("发送失败！发送过于频繁，请一分钟后再尝试。");
							return false;
						}
					},
					error: function(){
						$("#send_verify").show();
						send_verify_show(second);
					}
				})
			}
		})
	
	
	


	$(".popup-tpl-close-btn").on("click",function(){
		$("#popup-tpl").popup("close");
	});

	$(".popup-login-close-btn").on("click" ,function(){
		$("#popup-login").popup("close");
	});



	// 微信share function
	
	$(".share-trigger-btn").on('click', function(e){
		e.preventDefault();
		$('body,html').animate({
			scrollTop: 0
		}, 800);
		$(".share-arrow").show()
	})
	})
	
})


		