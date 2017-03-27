// 基于JQ的图片切换插件    
;(function($){    
   //插件主要内容 
  $.fn.imageAd = function(options) { 
    // 处理默认参数   
    var opts = $.extend({}, $.fn.imageAd.defaults, options);
	//初始条件构造  
	var $scrollObj=$(this).find(opts.scrollObj);
	var _num=$scrollObj.find("li").length;
	var _scrollHeight=$scrollObj.find("li").eq(0).height();//垂直滚动的高度
	var _scrollWidth=$scrollObj.find("li").eq(0).width();//水平滚动的宽度
	var $navObj,$targetLi,_scrollWH,_autoPlay,_stop = false,_index=0;
	if(opts.effect=="scrollY")
	{
		_scrollWH=_scrollHeight;
	}else if(opts.effect=="scrollX")
		{
			_scrollWH=_scrollWidth;
			$scrollObj.css("width",_num*_scrollWidth);
		}
	 else if(opts.effect=="fade")
		{
		 	$scrollObj.find('li').animate({opacity:0},1).eq(0).css("z-index",1).animate({opacity:1},1);
		}		 
	//保存JQ的连贯操作
    return this.each(function() {
		   //导航构造
		  var $this=$(this);
		  $this.append("<ul class='"+opts.navObj+"'></ul>");
		  $navObj=$("."+opts.navObj,$this);
		  var liHtml="";
		  for(var i=0;i<_num ;i++)
		  {
	          liHtml+='<li>'+(i+1)+'</li>';
	      }
		  $navObj.html(liHtml);
		  $targetLi=$navObj.find("li");
		  //行为添加
		  $targetLi.mouseenter(function()
	      {
				_index=$targetLi.index(this);
				$.fn.imageAd.effect[opts.effect]($scrollObj,$targetLi,_index,_scrollWH, opts);
		  }).eq(0).mouseenter();
		  
		  if(opts.autoPlay) 
		  {
			 _autoPlay = setInterval(autoPlayHandle,opts.autoPlayTimer);
			 $this.hover(function(){
					clearAutoPlay();
				},function(){
					clearAutoPlay();
					_autoPlay=setInterval(autoPlayHandle,opts.autoPlayTimer);
				});
		  }
			
		  function clearAutoPlay()
		  {
			  if(_autoPlay){clearInterval(_autoPlay);}
		  }
			
		  function autoPlayHandle()
		  {
			  $.fn.imageAd.effect[opts.effect]($scrollObj,$targetLi,_index,_scrollWH,opts);
			  _index=(_index+1)% _num;
		  }
		  
		  if(opts.btnPre)
		  {
			  $(opts.btnPre).click(function(){
				  	clearAutoPlay();
			  		if(!$scrollObj.is(":animated"))
			  		{
						_index=$targetLi.index($("."+opts.navObj+" li."+opts.currentClass,$this)[0]);
						if(_index==0)
					    {
							_index=_num-1;
						}else{_index=(_index-1)% _num;}
						$.fn.imageAd.effect[opts.effect]($scrollObj,$targetLi,_index,_scrollWH,opts);			
					}
			  });
		  }
		  
		  if(opts.btnNext)
		  {
			  $(opts.btnNext).click(function(){
				  	clearAutoPlay();
			  		if(!$scrollObj.is(":animated"))
			  		{
						_index=$targetLi.index($("."+opts.navObj+" li."+opts.currentClass,$this)[0]);
						_index=(_index+1)% _num;
						$.fn.imageAd.effect[opts.effect]($scrollObj,$targetLi,_index,_scrollWH,opts);			
					}
			  });
		  }	
		     					
    }); 
	
  };
  //插件主要内容结束 		
    
  // 插件的defaults     
  $.fn.imageAd.defaults = {     
        scrollObj:'.scrollObj',
		navObj:'navObj',
		currentClass:'current',
		btnPre:'',
		btnNext:'',
		autoPlay:true,
		speed:750,
		autoPlayTimer:3000,
		effect:'scrollX',
		easing:'easeInOutExpo'	
  }; 
  // 插件的defaults参数结束
  // 动作效果
   $.fn.imageAd.effect=
   {
		scrollY:function(contentObj,navObj,i,slideH,opts,callback){
			contentObj.stop().animate({"top":-i*slideH},opts.speed,opts.easing,callback);
			if (navObj) {
				navObj.eq(i).addClass(opts.currentClass).siblings().removeClass(opts.currentClass);
			}
		},
		scrollX:function(contentObj,navObj,i,slideW,opts,callback){
			contentObj.stop().animate({"left":-i*slideW},opts.speed,opts.easing,callback);
			if (navObj) {
				navObj.eq(i).addClass(opts.currentClass).siblings().removeClass(opts.currentClass);
			}
		},
		fade:function(contentObj,navObj,i,slideW,opts,callback){
			contentObj.find("li").eq(i).stop(true).animate({opacity:1},opts.speed).css({"z-index": "1"}).siblings("li").animate({opacity: 0},opts.speed).css({"z-index":"0"});
			if (navObj) {
				navObj.eq(i).addClass(opts.currentClass).siblings().removeClass(opts.currentClass); 
			}
		}		
  };        
  // 动作效果结束
})(jQuery);
//闭包结束