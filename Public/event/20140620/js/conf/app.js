requirejs.config({
	baseUrl: 'js/libs',
    paths: {
    	apps : "../apps",
    	conf : "../conf"
    }
});

require(["jquery","jquery.mobile-1.4.2.min"], function($, mobile){
	$( document ).on( "mobileinit",
		// Set up the "mobileinit" handler before requiring jQuery Mobile's module
		function() {
			// Prevents all anchor click handling including the addition of active button state and alternate link bluring.
			$.mobile.linkBindingEnabled = false;

			// Disabling this will prevent jQuery Mobile from handling hash changes
			$.mobile.hashListeningEnabled = false;
		}
	);


	 // 加载保险公司列表页面 
	 $( document ).on("pageinit", "#ic-list", function( event ) {
		  
	});



	 // 事故理赔中心列表页面
 	$( document ).on("pageinit", "#f-place", function( event ) {
		
 		var latitude,longitude;

	    // get geolocation, if geoloacation enabled, then set lan,lon to the variable;
		if(navigator.geolocation){
	     	navigator.geolocation.getCurrentPosition(function(pos){
	     		var crd = pos.coords,
	     			latitude = crd.latitude,
	     			longitude = crd.longitude;

	     			$.ajax({
	     				url : "http://www.xieche.com.cn/?callback=Accident-ajax_shop_list?order=distance&lat="+ latitude + "&long=" + longitude ,
	     				dataType: 'json',
	     				success : function(data){
	     					console.log(data)
	     				}
	     			})

	     	},function(err) {
	     		var errorMessage = "";
	     		switch(err.code){
	     			case 0:
	     			errorMessage = "发生未知错误,获取地理位置失败!";
	     			break;
	     			case 1:
	     			errorMessage = "地理位置请求被拒绝, 请在设置中打开!";
	     			break;
	     			case 2:
	     			errorMessage = "获取地理位置失败, 请稍后尝试!";
	     			break;
	     			case 3:
	     			errorMessage = "获取地理位置超时, 请稍后尝试!"
	     			break;

	     		}
	     		alert('错误信息(' + err.code + '): ' + errorMessage);
	     	} ,{
				enableHighAccuracy: false,
				timeout: 10000,
				maximumAge: 0
	     	});
	     	
		}else{
			// No geolocation data, then set value to "";
			latitude = "";
				longitude = "";
		}

	});

	 


});