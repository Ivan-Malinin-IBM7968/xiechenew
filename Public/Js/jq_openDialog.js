(function($) {     
  //插件主要内容     
  $.fn.openDialog = function(options) {     
  	var opts = $.extend({}, $.fn.openDialog.defaults, options);
    return this.each(function()
    {  														
      var $this = $(this),
	  height = $(window).height() + 'px',
	  width = $(window).width() + 'px';
	  var dialogHtml="<div id='"+opts.closeBtn+"'>关闭</div>";
	  $this.prepend(dialogHtml);
	  var $closeBtn=$("#"+opts.closeBtn,$this);
	  //$dialogContent=$("#"+opts.dialogContentObj,$this);
	  //if(!opts.innerHTML){$dialogContent.html(opts.innerHTML)};
	  $closeBtn.click(function(){
		                        $this.css('z-index', -9999).hide();
                                $masker.remove();
								$closeBtn.remove();
								//$dialogContent.remove();
							});	  
      if (opts.mask) {
		  var maskerHtml="<div id='masker' style='display:none;'></div>";
	  	  $('body').append(maskerHtml);
	  	  var $masker=$("#masker");
          $.fn.openDialog.showMask($masker,width,height,'block');
      }	  
	  var leftPx=(parseFloat($(window).width())-parseFloat($this.outerWidth()))/2;
      var wh = $(window).height();
      var xy = getXY();  
      var topPx = (wh * 0.15 + xy.y) + 'px';
      $this.css("left",leftPx);
      $this.css("top", topPx);
      $this.css("z-index",9999);  
	  $this.show();
	});  // 保存JQ的连贯操作结束
	
	function getXY()
	{
       var y;
       if (document.body.scrollTop) {
          y = document.body.scrollTop
        } else {
                y = document.documentElement.scrollTop
               }
       return {y:y}		
    }//获取滚动条滚动的位置	
  };    
    $.fn.openDialog.defaults = {
        innerHTML:'',
		mask:true,
		closeBtn:"closeDialog",
		dialogContentObj:"dialogContent"
    };
	
    $.fn.openDialog.showMask=function(masker,width,height,display)
    {
      masker.css({
        "width": width,
        "height": height,
        "position":"absolute",
        "left":"0px",
        "top":"0px",		
		"filter":"alpha(opacity=60)",
		//"background":"#000000",
		"z-index":8888,
        "opacity":0.6,
		'display':display
    });
	 
  };
// 闭包结束     
})(jQuery); 