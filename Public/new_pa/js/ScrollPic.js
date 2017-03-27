(function(a){
	a.fn.jdSlide=function(k){
		var p=a.extend({
			width:null,height:null,pics:[],index:0,type:"num",current:"curr",delay1:100,delay2:5000
		}
		,k||{});
		var i=this;
		var g,f,d,h=0,e=true,b=true;
		var n=p.pics.length;
		var o=function(){
			var q="<ul style='position:absolute;top:0;left:0;'><li><a href='"+p.pics[0].href+"' target='_blank'><img src='"+p.pics[0].src+"' width='"+p.width+"' height='"+p.height+"' /></a></li></ul>";
			i.css({
				position:"relative"
			}).html(q);
			i.find("ul").css({
				width:n*p.width+"px",height:p.height+"px"
			});
			a(function(){
				c()
			})
		};
		o();
		var j=function(){
			var s=[];
			s.push("<div>");
			var r;
			var q;
			for(var t=0;t<n;t++){
				r=(t==p.index)?p.current:"";
				switch(p.type){
					case"num":q=t+1;
					break;
					case"string":q=p.pics[t].alt;
					break;
					case"image":q="<img src='"+p.pics[t].breviary+"' />";
					default:break
				}
				s.push("<span class='");
				s.push(r);
				s.push("'><a href='");
				s.push(p.pics[t].href);
				s.push("' target='_blank'>");
				s.push(q);
				s.push("</a></span>")
			}
			s.push("</div>");
			i.append(s.join(""));
			var x=[];
			x.push("<div id='goback' class='o-control'><a class='control'></a></div><div id='forward' class='o-control'><a class='control'></a></div>");
			i.append(x.join(""));
			i.find("#goback").bind("mouseover",function(){
				b=false;
				clearTimeout(g);
				clearTimeout(d)
			}).bind("click",function(){
				var u=p.index-1;
				if(u<0){
					u=t-1
				};
				l(u)
			}).bind("mouseleave",function(){
				b=true;
			});
			i.find("#forward").bind("mouseover",function(){
				b=false;
				clearTimeout(g);
				clearTimeout(d)
			}).bind("click",function(){
				var u=p.index+1;
				l(u)
			}).bind("mouseleave",function(){
				b=true;
			});
			i.find("span").bind("mouseover",function(){
				b=false;
				clearTimeout(g);
				clearTimeout(d);
				var u=i.find("span").index(this);
				if(p.index==u){
					return
				}
				else{
					d=setInterval(function(){
						if(e){
							l(u)
						}
					}
					,p.delay1)
				}
			}).bind("mouseleave",function(){
				b=true;
				clearTimeout(g);
				clearTimeout(d);
				g=setTimeout(function(){
					l(p.index+1,true)
				}
				,p.delay2)
			});
			i.bind("mouseover",function(){
				//$("#slide .o-control").show()
			}).bind("mouseleave",function(){
				//$("#slide .o-control").hide()
			})
		};
		var l=function(r,q){
			if(r==n){
				r=0
			}
			f=setTimeout(function(){
				i.find("span").eq(p.index).removeClass(p.current);
				i.find("span").eq(r).addClass(p.current);
				m(r,q)
			}
			,20)
		};
		var m=function(u,q){
			var s=parseInt(h);
			var v=Math.abs(s+p.index*p.width);
			var t=Math.abs(u-p.index)*p.width;
			var r=Math.ceil((t-v)/4);
			if(v==t){
				clearTimeout(f);
				if(q){
					p.index++;
					if(p.index==n){
						p.index=0
					}
				}
				else{
					p.index=u
				}
				e=true;
				if(e&&b){
					clearTimeout(g);
					g=setTimeout(function(){
						l(p.index+1,true)
					}
					,p.delay2)
				}
			}
			else{
				if(p.index<u){
					h=s-r;
					i.find("ul").css({
						left:h+"px"
					})
				}
				else{
					h=s+r;
					i.find("ul").css({
						left:h+"px"
					})
				}
				e=false;
				f=setTimeout(function(){
					m(u,q)
				}
				,20)
			}
		};
		var c=function(){
			var q=[];
			for(var r=1;r<n;r++){
				q.push("<li><a href='");
				q.push(p.pics[r].href);
				q.push("' target='_blank'><img src='");
				q.push(p.pics[r].src);
				q.push("' width='");
				q.push(p.width);
				q.push("' height='");
				q.push(p.height);
				q.push("' /></a></li>")
			}
			i.find("ul").append(q.join(""));
			g=setTimeout(function(){
				l(p.index+1,true)
			}


			,p.delay2);
			if(p.type){
				j()
			}
		}
	}
})(jQuery);


var sina={$:function(objName){if(document.getElementById){return eval('document.getElementById("'+objName+'")')}else{return eval('document.all.'+objName)}},isIE:navigator.appVersion.indexOf("MSIE")!=-1?true:false,addEvent:function(l,i,I){if(l.attachEvent){l.attachEvent("on"+i,I)}else{l.addEventListener(i,I,false)}},delEvent:function(l,i,I){if(l.detachEvent){l.detachEvent("on"+i,I)}else{l.removeEventListener(i,I,false)}},readCookie:function(O){var o="",l=O+"=";if(document.cookie.length>0){var i=document.cookie.indexOf(l);if(i!=-1){i+=l.length;var I=document.cookie.indexOf(";",i);if(I==-1)I=document.cookie.length;o=unescape(document.cookie.substring(i,I))}};return o},writeCookie:function(i,l,o,c){var O="",I="";if(o!=null){O=new Date((new Date).getTime()+o*3600000);O="; expires="+O.toGMTString()};if(c!=null){I=";domain="+c};document.cookie=i+"="+escape(l)+O+I},readStyle:function(I,l){if(I.style[l]){return I.style[l]}else if(I.currentStyle){return I.currentStyle[l]}else if(document.defaultView&&document.defaultView.getComputedStyle){var i=document.defaultView.getComputedStyle(I,null);return i.getPropertyValue(l)}else{return null}}};

//滚动图片构造函数
//UI&UE Dept. mengjia
//080623
function ScrollPic(scrollContId,arrLeftId,arrRightId,dotListId){this.scrollContId=scrollContId;this.arrLeftId=arrLeftId;this.arrRightId=arrRightId;this.dotListId=dotListId;this.dotClassName="dotItem";this.dotOnClassName="dotItemOn";this.dotObjArr=[];this.pageWidth=0;this.frameWidth=0;this.speed=10;this.space=10;this.pageIndex=0;this.autoPlay=true;this.autoPlayTime=5;var _autoTimeObj,_scrollTimeObj,_state="ready";this.stripDiv=document.createElement("DIV");this.listDiv01=document.createElement("DIV");this.listDiv02=document.createElement("DIV");if(!ScrollPic.childs){ScrollPic.childs=[]};this.ID=ScrollPic.childs.length;ScrollPic.childs.push(this);this.initialize=function(){if(!this.scrollContId){throw new Error("必须指定scrollContId.");return};this.scrollContDiv=sina.$(this.scrollContId);if(!this.scrollContDiv){throw new Error("scrollContId不是正确的对象.(scrollContId = \""+this.scrollContId+"\")");return};this.scrollContDiv.style.width=this.frameWidth+"px";this.scrollContDiv.style.overflow="hidden";this.listDiv01.innerHTML=this.listDiv02.innerHTML=this.scrollContDiv.innerHTML;this.scrollContDiv.innerHTML="";this.scrollContDiv.appendChild(this.stripDiv);this.stripDiv.appendChild(this.listDiv01);this.stripDiv.appendChild(this.listDiv02);this.stripDiv.style.overflow="hidden";this.stripDiv.style.zoom="1";this.stripDiv.style.width="32766px";this.listDiv01.style.cssFloat="left";this.listDiv02.style.cssFloat="left";sina.addEvent(this.scrollContDiv,"mouseover",Function("ScrollPic.childs["+this.ID+"].stop()"));sina.addEvent(this.scrollContDiv,"mouseout",Function("ScrollPic.childs["+this.ID+"].play()"));if(this.arrLeftId){this.arrLeftObj=sina.$(this.arrLeftId);if(this.arrLeftObj){sina.addEvent(this.arrLeftObj,"mousedown",Function("ScrollPic.childs["+this.ID+"].rightMouseDown()"));sina.addEvent(this.arrLeftObj,"mouseup",Function("ScrollPic.childs["+this.ID+"].rightEnd()"));sina.addEvent(this.arrLeftObj,"mouseout",Function("ScrollPic.childs["+this.ID+"].rightEnd()"))}};if(this.arrRightId){this.arrRightObj=sina.$(this.arrRightId);if(this.arrRightObj){sina.addEvent(this.arrRightObj,"mousedown",Function("ScrollPic.childs["+this.ID+"].leftMouseDown()"));sina.addEvent(this.arrRightObj,"mouseup",Function("ScrollPic.childs["+this.ID+"].leftEnd()"));sina.addEvent(this.arrRightObj,"mouseout",Function("ScrollPic.childs["+this.ID+"].leftEnd()"))}};if(this.dotListId){this.dotListObj=sina.$(this.dotListId);if(this.dotListObj){var pages=Math.round(this.listDiv01.offsetWidth/this.frameWidth+0.4),i,tempObj;for(i=0;i<pages;i++){tempObj=document.createElement("span");this.dotListObj.appendChild(tempObj);this.dotObjArr.push(tempObj);if(i==this.pageIndex){tempObj.className=this.dotClassName}else{tempObj.className=this.dotOnClassName};tempObj.title="第"+(i+1)+"页";sina.addEvent(tempObj,"click",Function("ScrollPic.childs["+this.ID+"].pageTo("+i+")"))}}};if(this.autoPlay){this.play()}};this.leftMouseDown=function(){if(_state!="ready"){return};_state="floating";_scrollTimeObj=setInterval("ScrollPic.childs["+this.ID+"].moveLeft()",this.speed)};this.rightMouseDown=function(){if(_state!="ready"){return};_state="floating";_scrollTimeObj=setInterval("ScrollPic.childs["+this.ID+"].moveRight()",this.speed)};this.moveLeft=function(){if(this.scrollContDiv.scrollLeft+this.space>=this.listDiv01.scrollWidth){this.scrollContDiv.scrollLeft=this.scrollContDiv.scrollLeft+this.space-this.listDiv01.scrollWidth}else{this.scrollContDiv.scrollLeft+=this.space};this.accountPageIndex()};this.moveRight=function(){if(this.scrollContDiv.scrollLeft-this.space<=0){this.scrollContDiv.scrollLeft=this.listDiv01.scrollWidth+this.scrollContDiv.scrollLeft-this.space}else{this.scrollContDiv.scrollLeft-=this.space};this.accountPageIndex()};this.leftEnd=function(){if(_state!="floating"){return};_state="stoping";clearInterval(_scrollTimeObj);var fill=this.pageWidth-this.scrollContDiv.scrollLeft%this.pageWidth;this.move(fill)};this.rightEnd=function(){if(_state!="floating"){return};_state="stoping";clearInterval(_scrollTimeObj);var fill=-this.scrollContDiv.scrollLeft%this.pageWidth;this.move(fill)};this.move=function(num,quick){var thisMove=num/5;if(!quick){if(thisMove>this.space){thisMove=this.space};if(thisMove<-this.space){thisMove=-this.space}};if(Math.abs(thisMove)<1&&thisMove!=0){thisMove=thisMove>=0?1:-1}else{thisMove=Math.round(thisMove)};var temp=this.scrollContDiv.scrollLeft+thisMove;if(thisMove>0){if(this.scrollContDiv.scrollLeft+thisMove>=this.listDiv01.scrollWidth){this.scrollContDiv.scrollLeft=this.scrollContDiv.scrollLeft+thisMove-this.listDiv01.scrollWidth}else{this.scrollContDiv.scrollLeft+=thisMove}}else{if(this.scrollContDiv.scrollLeft-thisMove<=0){this.scrollContDiv.scrollLeft=this.listDiv01.scrollWidth+this.scrollContDiv.scrollLeft-thisMove}else{this.scrollContDiv.scrollLeft+=thisMove}};num-=thisMove;if(Math.abs(num)==0){_state="ready";if(this.autoPlay){this.play()};this.accountPageIndex();return}else{this.accountPageIndex();setTimeout("ScrollPic.childs["+this.ID+"].move("+num+","+quick+")",this.speed)}};this.next=function(){if(_state!="ready"){return};_state="stoping";this.move(this.pageWidth,true)};this.play=function(){if(!this.autoPlay){return};clearInterval(_autoTimeObj);_autoTimeObj=setInterval("ScrollPic.childs["+this.ID+"].next()",this.autoPlayTime*1000)};this.stop=function(){clearInterval(_autoTimeObj)};this.pageTo=function(num){if(_state!="ready"){return};_state="stoping";var fill=num*this.frameWidth-this.scrollContDiv.scrollLeft;this.move(fill,true)};this.accountPageIndex=function(){this.pageIndex=Math.round(this.scrollContDiv.scrollLeft/this.frameWidth);if(this.pageIndex>Math.round(this.listDiv01.offsetWidth/this.frameWidth+0.4)-1){this.pageIndex=0};var i;for(i=0;i<this.dotObjArr.length;i++){if(i==this.pageIndex){this.dotObjArr[i].className=this.dotClassName}else{this.dotObjArr[i].className=this.dotOnClassName}}}};