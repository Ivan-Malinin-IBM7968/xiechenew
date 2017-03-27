require.config( {
	paths: {
            jquery: "libs/jquery/jquery-1.9.1",
            jquerymobile: "libs/jqmobile/jquery.mobile-1.4.0.min",
            xemobile_ua : "xcmobile_ua"
      }
} );

require(["jquery","jquerymobile", "xemobile_ua"], function( $) {
	$(document).ready(function() {
		// on document ready, make the menu btn opacity .3 when the document is scrolled
		var scrollTimer = 250;
		$(window).touchmove(function() {
			clearTimeout(scrollTimer);
			$("#popup-btn").css("opacity",".3");
			scrollTimer = setTimeout(function(){
		    	$("#popup-btn").css("opacity","1");
		    },250)
		});

		$(window).touchend(function(){
			$("#popup-btn").css("opacity","1");
		});

		$("#loading-overlay").fadeOut("slow");
	})

    $( document ).on( "mobileinit", function() {
		$.mobile.linkBindingEnabled = false;
		$.mobile.hashListeningEnabled = false;
		
	});

    // set lan&lon variable to get value if geolocation enabled,
    // get fsId&fsName if they are set value 
	var latitude="",longitude="",
		fs_id = $("#hide_fsid").val() | "",
		fsName = $("#hide_fsname").val() | "";
   
    // get geolocation, if geoloacation enabled, then set lan,lon to the variable;
	if(navigator.geolocation){
     	navigator.geolocation.getCurrentPosition(function(pos){
     		var crd = pos.coords,
     			latitude = crd.latitude,
     			longitude = crd.longitude;
     			$html1 = '<div style="display:none" id="use_latitude">'+latitude+'</div>';
     			$html2 = '<div style="display:none" id="use_longitude">'+longitude+'</div>';
     			$('#sort-block-a').append($html1);
     			$('#sort-block-b').append($html2);
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
	
	$("#sort-block-b a").click(function(e){
		e.preventDefault();
		latitude = $('#use_latitude').text();
		longitude = $('#use_longitude').text();
		window.open('/Mobile-index-lat-'+latitude+'-long-'+longitude+'-order-commment'+'-fs_id-'+fs_id + '-fs_Name-' + fsName, "_self");
	})

	$("#sort-block-c a").click(function(e){
		e.preventDefault();
		latitude = $('#use_latitude').text();
		longitude = $('#use_longitude').text();
		window.open('/Mobile-index-order-distance-lat-'+latitude+'-long-'+longitude+"-fs_id-"+fs_id + '-fs_Name-' + fsName,"_self")
	});

	$("#brand-list li a").click(function(e){
		var $this = $(this);
		e.preventDefault()
		var fsId = $(this).attr("data-fsid");
		var	fsName = $(this).attr("data-fsname");
		latitude = $('#use_latitude').text();
		longitude = $('#use_longitude').text();
		if($this.data("index") ){
			window.open('/Mobile-index-lat-'+latitude+'-long-'+ longitude + '-fs_id-'+fsId + '-fs_Name-' + fsName , "_self");
		}else{
			window.open('/Mobile-coupon_list-lat-'+latitude+'-long-'+ longitude + '-fs_id-'+fsId + '-fs_Name-' + fsName , "_self")
		}
		
 	})
		

	


});
