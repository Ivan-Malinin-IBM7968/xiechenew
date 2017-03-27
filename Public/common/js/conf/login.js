requirejs.config({
	baseUrl: 'http://statics.xieche.net/common/js/common/mods',
    paths: {
    	"jquery" : "http://statics.xieche.net/common/js/libs/jquery/jquery-1.11.min",
        "common" : "common"
    }
});

require(['jquery','common'], function ($,c) {
   
    new c.Common().tabView(".user-box .controler", ".user-box .container");

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
