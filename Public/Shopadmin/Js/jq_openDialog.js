(function(jQuery) {     
  //插件主要内容     
  jQuery.fn.openDialog = function(options) {     
  	var opts = jQuery.extend({}, jQuery.fn.openDialog.defaults, options);
    return this.each(function()
    {  														
      var jQuerythis = jQuery(this),
	  height = jQuery(window).height() + 'px',
	  width = jQuery(window).width() + 'px';
	  var dialogHtml="<div id='"+opts.closeBtn+"'>关闭</div>";
	  jQuerythis.append(dialogHtml);
	  var jQuerycloseBtn=jQuery("#"+opts.closeBtn,jQuerythis);
	  //jQuerydialogContent=jQuery("#"+opts.dialogContentObj,jQuerythis);
	  //if(!opts.innerHTML){jQuerydialogContent.html(opts.innerHTML)};
	  jQuerycloseBtn.click(function(){
		                        jQuerythis.css('z-index', -9999).hide();
                                jQuerymasker.remove();
								jQuerycloseBtn.remove();
								//jQuerydialogContent.remove();
							});	  
      if (opts.mask) {
		  var maskerHtml="<div id='masker' style='display:none;'></div>";
	  	  jQuery('body').append(maskerHtml);
	  	  var jQuerymasker=jQuery("#masker");
          jQuery.fn.openDialog.showMask(jQuerymasker,width,height,'block');
      }	  
	  var leftPx=(parseFloat(jQuery(window).width())-parseFloat(jQuerythis.outerWidth()))/2;
      var wh = jQuery(window).height();
      var xy = getXY();  
      var topPx = (wh * 0.15 + xy.y) + 'px';
      jQuerythis.css("left",leftPx);
      jQuerythis.css("top", topPx);
      jQuerythis.css("z-index",9999);  
	  jQuerythis.show();
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
    jQuery.fn.openDialog.defaults = {
        innerHTML:'',
		mask:true,
		closeBtn:"closeDialog",
		dialogContentObj:"dialogContent"
    };
	
    jQuery.fn.openDialog.showMask=function(masker,width,height,display)
    {
      masker.css({
        "width": width,
        "height": height,
        "position":"absolute",
        "left":"0px",
        "top":"0px",		
		"filter":"alpha(opacity=60)",
		"background":"#000000",
		"z-index":8888,
        "opacity":0.6,
		'display':display
    });
	 
  };
// 闭包结束     
})(jQuery); 