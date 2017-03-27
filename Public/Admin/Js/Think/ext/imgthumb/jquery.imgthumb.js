/*
 *加载中图片http://ajaxload.info中生成，生成一个16*16的小图标。
 * Dev By luofei614<www.3g4k.com>
 */
;(function($){
jQuery.fn.imgthumb = function(options) {
	options = $.extend({
		 height:100,
		 width:100,
		 loadimg:"loading.gif"
	},options);
	var _self = $(this);
	_self.hide();
	var loadmarginl=parseInt((options.width-16)/2)
	var loadmargint=parseInt((options.height-16)/2)
	var loading = $("<img alt=\"加载中..\" title=\"加载中...\" src=\""+options.loadimg+"\" />"); 
	loading.css({paddingLeft:loadmarginl,paddingRight:loadmarginl,paddingTop:loadmargint,paddingBottom:loadmargint});
	_self.after(loading);
	var img = new Image();
	$(img).load(function(){
		imgDem = {};
		imgDem.w  = img.width;
		imgDem.h  = img.height;
		imgDem = $.imgResize({"w":options.width ,"h":options.height},{"w":imgDem.w,"h":imgDem.h});
		var imgMargins = $.imgCenter({"w":options.width,"h":options.height},{"w":imgDem.w,"h":imgDem.h});
		_self.css({width:imgDem.w,height:imgDem.h,paddingLeft:imgMargins.l,paddingRight:imgMargins.l,paddingTop:imgMargins.t,paddingBottom:imgMargins.t});
		_self.attr("src",$(this).attr("src"));
		loading.remove();
		_self.fadeIn("slow");
	}).attr("src",$(this).attr("src"));  //.atte("src",options.src)要放在load后面，
		
}
//重置图片宽度，高度插件 ( parentDem是父元素，imgDem是图片 )
jQuery.imgResize = function(parentDem,imgDem){
	if(imgDem.w>0 && imgDem.h>0){
		var rate = (parentDem.w/imgDem.w < parentDem.h/imgDem.h)?parentDem.w/imgDem.w:parentDem.h/imgDem.h;
		//如果 指定高度/图片高度  小于  指定宽度/图片宽度 ，  那么，我们的比例数 取 指定高度/图片高度。
		//如果 指定高度/图片高度  大于  指定宽度/图片宽度 ，  那么，我们的比例数 取 指定宽度/图片宽度。
		if(rate <= 1){   
			imgDem.w = parseInt(imgDem.w*rate); //图片新的宽度 = 宽度 * 比例数
		    imgDem.h = parseInt(imgDem.h*rate);
		}else{//  如果比例数大于1，则新的宽度等于以前的宽度。
			imgDem.w = imgDem.w;
			imgDem.h = imgDem.h;
		}
    }
	return imgDem;
}
//使图片在父元素内水平，垂直居中，( parentDem是父元素，imgDem是图片 )
jQuery.imgCenter = function(parentDem,imgDem){
	var left = parseInt((parentDem.w - imgDem.w)*0.5);
	var top = parseInt((parentDem.h - imgDem.h)*0.5);
	return { "l": left , "t": top};
}
})(jQuery);