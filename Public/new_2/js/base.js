/******** Base.js ********/

(function(){
	// 导航栏个人中心下拉
	$('.login-box').hover(function(){
		
		$('.dropdown-arrow-down').css({
			'background-position' : "-223px -60px"
		})
		$('.account-dropdown').show();
	}, function(){
		$('.dropdown-arrow-down').css({
			'background-position' : "-206px -60px"
		})
		$('.account-dropdown').hide();
		
	});

	$("#wechat-xieche a").hover(function () {
		$("#wechat-qrcode").show()
	}, function(){
		$("#wechat-qrcode").hide()
	});

	$("#back-top a").click(function() {
	  $("html, body").animate({ scrollTop: 0 }, "normal");
	  return false;
	});
		
	if($("#dialog-con")){
		$("#dialog-con").dialog({
			 autoOpen : false,
			 closeOnEscape: true, 
			 title : "新增抵用券",
			 modal: true
		});
	}
	
	if($("#ticket-trigger")){
		$("#ticket-trigger").on("click", function(e){
			e.preventDefault()
			$("#dialog-con").dialog("open");
		})
	}
		



		
	
})();

function popup_box(e){

		$('.blackbox').show();
			$("#popup").css({
				"width" : e.data.width,
				"height" : e.data.height,
				"top": "150px",
	            "left": ( $(window).width() - parseInt(e.data.width)) / 2+$(window).scrollLeft() + "px"
			}).show();

			console.log(($(window).height() -parseInt(e.data.height))/ 2 +$(window).scrollTop() + "px" );
			console.log(( $(window).width() - parseInt(e.data.width))/ 2 +$(window).scrollLeft() + "px")

	};
	

$('.blackbox').not("#popup").on('click', function(){
	$('.blackbox').hide();
	$("#popup").hide()
})