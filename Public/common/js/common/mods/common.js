define("common",["jquery"], function($){

	function Common(){};
		
	Common.prototype = {

		tabView : function(controler,container){
			var tabs = $(controler).children("li");
			var panels = $(container).children("div");
			$(container).children("div").css("display","none");
			$(container).children("div:first").css("display","block");
			$(tabs).hover(function(){
				var index = $.inArray(this,tabs);
				tabs.removeClass("current").eq(index).addClass("current");
				panels.css("display","none").eq(index).css("display","block");
			});
		},

		say : function(){
			alert("Say something from common js module!");
		}
	};

	return {
		Common : Common
	};

})
