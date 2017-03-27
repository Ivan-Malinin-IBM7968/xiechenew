require.config( {
	paths: {
            jquery: "libs/jquery/jquery-1.9.1",
            jquerymobile: "libs/jqmobile/jquery.mobile-1.4.0.min",
            xemobile_ua : "xcmobile_ua"
      }
} );

require(["jquery","jquerymobile","xemobile_ua"], function( $) {
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

	/*$(".back").click(function() {
		history.go(-1);
	});*/
	
		
		

	


});
