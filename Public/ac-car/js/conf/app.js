requirejs.config({
	baseUrl: 'http://statics.xieche.net/ac-car/js/libs',
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

	 $(document).on("pageshow","#ac-form", function (event) {

		$("#brand-list li a").on("click", function (e) {
			
			var fsName = $(this).attr("data-fsname"),
				fsId = $(this).attr("data-fsid");

			$("#hide_fsid").val(fsId);
			$("#select-brand-btn").html(fsName + " >");



		})
	 })

	 $(document).ready(function(e){
		$("#ac-form-submit-btn").on("click", function(e){
			e.preventDefault();
			$("#ac-form-frame").submit()
		})
	 })


	 // 事故理赔中心列表页面
 	$( document ).on("pageinit", "#f-place", function( event ) {
		
 		var latitude,longitude;

 		function getShopList(lan,lon){
 			
 			var shopListURL;

 			if(!lan || !lon){
 				shopListURL = "http://www.xieche.com.cn/Accident-ajax_shop_list?order=distance";
 			}else{
 				shopListURL ="http://www.xieche.com.cn/Accident-ajax_shop_list?order=distance&lat=" + lan + "&long=" + lon;
 			}

 			tpl_f_place = $.trim($('#tpl-f-place').html());

 			$.ajax({
 				url : shopListURL,
 				dataType : "json",
 				success : function(data){
 					var flag ='';
 					$.each(data, function(index, value){
 						flag +=tpl_f_place
								.replace(/{{shop-name}}/ig,value.shop_name)
								.replace(/{{shop-address}}/ig,value.shop_address)
								.replace(/{{shop-distance}}/ig,value.distance)
							    .replace(/{{id}}/ig,value.id)
								.replace(/{{latitude}}/ig,value.lat)
								.replace(/{{longitude}}/ig,value.long)
								.replace(/{{worktime}}/ig,value.worktime)
								/*.replace(/{{shop-phonecall}}/ig,value.shop_mobile)*/
								
 					})
 					$("#f-list").empty().append(flag);
 				}
 			})
 		}


	    // get geolocation, if geoloacation enabled, then set lan,lon to the variable;
		if(navigator.geolocation){
	     	navigator.geolocation.getCurrentPosition(function(pos){
	     		var crd = pos.coords,
	     			latitude = crd.latitude,
	     			longitude = crd.longitude;
	     			
	     			getShopList(latitude,longitude)

 			

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
	     		getShopList();

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