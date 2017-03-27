require(["../config",],function(config){
	require(["jquery"], function ($) {
		$(document).ready(function(){

		    $(".input-field").each(function(index, element) {
			      var $element = $(element);
			      var defaultValue = $element.val();
			      $element.css('color', '#999999');
			      $element.focus(function() {
			          var actualValue = $element.val();
			          if (actualValue == defaultValue) {
			              $element.val('');
			              $element.css('color', '#333');
			          }
			      });

			      $element.blur(function() {
			          var actualValue = $element.val();
			          if (!actualValue) {
			              $element.val(defaultValue);
			              $element.css('color', '#999999');
			          }
			      });
			  });

		    $("#refreshCode, #img-code").on("click", function(e){
		    	e.preventDefault();
		    	var newTime = new Date().getTime();
		    	$("#img-code").attr("src","http://www.xieche.com.cn/common/verify/" + newTime);
		    });
		});
	})
})

