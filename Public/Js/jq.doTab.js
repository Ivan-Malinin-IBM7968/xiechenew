// 基于JQ的doTab插件
// 创建一个闭包     
(function($) {     
  //插件主要内容     
  $.fn.doTab = function(options) {     

    // 处理默认参数   
    var opts = $.extend({}, $.fn.doTab.defaults, options);     
    // 保存JQ的连贯操作    
    return this.each(function() {  														
         var $this = $(this);     
			var $tabObject=$this.find(opts.tabBox+" "+opts.tabTag);//tab按钮对象
			var $tabContent=$this.find(opts.tabContent+" > "+opts.tabShowBox);
			
			$tabObject.mouseover(function(){
		    $(this).addClass(opts.curClass).siblings(opts.tabTag).removeClass(opts.curClass);
			 $tabContent.eq($tabObject.index(this)).show().siblings(opts.tabShowBox).hide();
			});//经过事件处理
    
    }); 
		// 保存JQ的连贯操作结束
  };
	 //插件主要内容结束
    
  // 插件的defaults     
  $.fn.doTab.defaults = {     
      tabBox:'.tabBox',
		tabTag:'li',
		tabContent:'.tabContent',
		tabShowBox:'div',
		curClass:'on'		
  };     
// 闭包结束     
})(jQuery); 

$(function(){
  $(".tab").doTab({tabShowBox:".newUL"});
});