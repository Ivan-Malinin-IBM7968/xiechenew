require.config( {
	paths: {
            jquery: "libs/jquery/jquery-1.9.1",
            jquerymobile: "libs/jqmobile/jquery.mobile-1.4.0.min",
            ga : "pamb_ga"
      }
} );

require(["jquery","jquerymobile","ga"], function( $) {
	$(document).ready(function() {
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
	
		var latitude,longitude;
	    // get geolocation
		if(navigator.geolocation){
	     	navigator.geolocation.getCurrentPosition(function(pos){
	     		var crd = pos.coords,
	     			fs_id = $('#hide_fsid').val(),
					fsName = $('#hide_fsname').val(),
	     			latitude = crd.latitude,
	     			longitude = crd.longitude;
				
				if (fsName == "undefined"){
					fsName = "";
				}
	     		$("#sort-block-b a").tap(function(e){
	     			e.preventDefault();
	     			window.open('/Pamobile-index-lat-'+crd.latitude+'-long-'+crd.longitude+'-order-commment'+'-fs_id-'+fs_id + '-fs_Name-' + fsName, "_self");
	     		})

	     		$("#sort-block-c a").tap(function(e){
	     			e.preventDefault();
	     			window.open('/Pamobile-index-order-distance-lat-'+crd.latitude+'-long-'+crd.longitude+"-fs_id-"+fs_id + '-fs_Name-' + fsName,"_self")
	     		});

	     	},function() {} ,{
				enableHighAccuracy: true,
				timeout: 5000,
				maximumAge: 0
	     	});
	     	
		}

		/*$(".back").click(function() {
			history.go(-1);
		});*/

		$("#brand-list li a").tap(function(e){
			var $this = $(this);
			e.preventDefault()
			var fsId = $(this).attr("data-fsid");
			var	fsName = $(this).attr("data-fsname");
			if(navigator.geolocation){
				navigator.geolocation.getCurrentPosition(function(pos){
					var crd = pos.coords;
					
					if($this.data("index") ){
						window.open('/Pamobile-index-lat-'+crd.latitude+'-long-'+ crd.longitude + '-fs_id-'+fsId + '-fs_Name-' + fsName , "_self");
					}else{
						window.open('/Pamobile-coupon_list-lat-'+crd.latitude+'-long-'+ crd.longitude + '-fs_id-'+fsId + '-fs_Name-' + fsName , "_self")
					}
					
				},function(){},{
					enableHighAccuracy: true,
					timeout: 5000,
					maximumAge: 0
				})
			}
			
	 	})
		

	


});
