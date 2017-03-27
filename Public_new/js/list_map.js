"use strict";


$(function(){

	var map = new BMap.Map("inner-map"), // 初始化百度地图
		shopList = $('.single-shop'),  //获取所有顶铺List
		listLanArr = [];

	map.centerAndZoom("上海", 12);  
	map.enableScrollWheelZoom();    //启用滚轮放大缩小，默认禁用
	map.enableContinuousZoom();    //启用地图惯性拖拽，默认禁用
	map.setViewport(listLanArr)	   //根据相关point设置地图viewport

	shopList.each(function(index, list){
		
		var coorArr = $(list).data("lng").split(','),
			coor1 = parseFloat(coorArr[0]),
			coor2 = parseFloat(coorArr[1]),
			shopName = $(list).data("name"),
			shopAddress =$(list).data('address'),
			shopLink = $(list).data('href'),
			shopId = $(list).attr('id');

		var singleShop = {
			'name' : shopName,
			'address' : shopAddress,
			'link' : shopLink,
			'id' : shopId,
			'coor' : {
				"x" : coor1,
				"y" : coor2
			}
		}
			
		listLanArr.push(coor1 + "," +coor2);


		var point = new BMap.Point(coor1, coor2), //New Point类
			marker = new BMap.Marker(point); // New marker类

		addMarker(point, singleShop) // add marker 并且在地途中绑定houver事件
  		
  		function addMarker(point, shopObject){
			var marker = new BMap.Marker(point);
			map.addOverlay(marker);
			marker.addEventListener("mouseover", function(e){
				var sContent =
					"<div><h4 style='margin:0 0 5px 0;padding:0.2em 0'>"+shopObject.name+"</h4>" + 
					
					"<a href='"+shopObject.link+"'style='margin:0;line-height:1.5;font-size:13px;text-indent:2em'>"+shopObject.address+"</a>" + 
					"</div>";
				var infoWindow = new BMap.InfoWindow(sContent); 
				e.target.openInfoWindow(infoWindow, {
					"enableMessage" : false,
					"enableAutoPan" : true
				});
				// $("body,html").animate({
				// 	scrollTop : ( $("#"+shopObject.id).offset().top - 100 )
				// },500);
				$("#"+singleShop.id).css({
					// "background" : "#FFFFFF"
				})
			});	

			marker.addEventListener("click", function(e){
				var sContent =
					"<div><h4 style='margin:0 0 5px 0;padding:0.2em 0'>"+shopObject.name+"</h4>" + 
					
					"<a href='"+shopObject.link+"'style='margin:0;line-height:1.5;font-size:13px;text-indent:2em'>"+shopObject.address+"</a>" + 
					"</div>";
				var infoWindow = new BMap.InfoWindow(sContent); 
				e.target.openInfoWindow(infoWindow, {
					"enableMessage" : false,
					"enableAutoPan" : true
				});
				$("body,html").animate({
					scrollTop : ( $("#"+shopObject.id).offset().top - 100 )
				},500);

			});	

			marker.addEventListener("mouseout",function(){
				$("#"+singleShop.id).css({
					// "background" : "none"
				})
			})

  		}
	})

	shopList.hover(function(){
		var $this = $(this),
			coorArr = $this.data("lng").split(','),
			thisShop = {
				"coor1" : parseFloat(coorArr[0]),
				"coor2" : parseFloat(coorArr[1]),
				"shopName" : $this.data("name"),
				"shopAddress" : $this.data('address'),
				"shopLink" : $this.data('href'),
				"shopId" : $this.attr('id')
			}

		$this.css({

			// "background" : "#FFFFFF"

		})
		var point = new BMap.Point(thisShop.coor1, thisShop.coor2),
			marker = new BMap.Marker(point);
			map.addOverlay(marker);
			var sContent =
				"<div><h4 style='margin:0 0 5px 0;padding:0.2em 0'>"+thisShop.shopName+"</h4>" + 
				
				"<a class='list-shop-map' href='"+thisShop.shopLink+"'style='margin:0;line-height:1.5;font-size:13px;text-indent:2em'>"+thisShop.shopAddress+"</a>" + 
				"</div>";
			var infoWindow = new BMap.InfoWindow(sContent, {
				"enableMessage" : false,
				"enableAutoPan" : true
			}); 
			marker.openInfoWindow(infoWindow);
			marker.addEventListener("mouseover", function(e){
				var sContent =
					"<div><h4 style='margin:0 0 5px 0;padding:0.2em 0'>"+thisShop.shopName+"</h4>" + 
					
					"<a href='"+thisShop.shopLink+"'style='margin:0;line-height:1.5;font-size:13px;text-indent:2em'>"+thisShop.shopAddress+"</a>" + 
					"</div>";
				var infoWindow1 = new BMap.InfoWindow(sContent); 
				
				e.target.openInfoWindow(infoWindow1, {
					"enableMessage" : false,
					"enableAutoPan" : true
				});
				// $("body,html").animate({
				// 	scrollTop : ( $("#"+thisShop.shopId).offset().top - 100 )
				// },500);
				
				 $("#"+thisShop.shopId).css({
					// "background" : "#FFFFFF"
				})
				
			});	

			marker.addEventListener("click", function(e){
				var sContent =
					"<div><h4 style='margin:0 0 5px 0;padding:0.2em 0'>"+thisShop.shopName+"</h4>" + 
					
					"<a href='"+thisShop.shopLink+"'style='margin:0;line-height:1.5;font-size:13px;text-indent:2em'>"+thisShop.shopAddress+"</a>" + 
					"</div>";
				var infoWindow1 = new BMap.InfoWindow(sContent); 
				
				e.target.openInfoWindow(infoWindow1, {
					"enableMessage" : false,
					"enableAutoPan" : true
				});
				$("body,html").animate({
					scrollTop : ( $("#"+thisShop.shopId).offset().top - 100 )
				},500);
				
				
			});	

			marker.addEventListener("mouseout",function(){
				$("#"+thisShop.shopId).css({
					// "background" : "none"
				})
			})

	}, function(){
		var $this = $(this);
		$this.css({
		
			// "background" : "none"
		})
	});

	
	

}())



	

