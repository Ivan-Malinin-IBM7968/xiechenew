/* jquery-fn-accordion v0
* Based on jQuery JavaScript Library v3
* http://jquery.com/
*
* The author of the following code: miqi2214 , wbpbest
* Blog:eycbest.cnblogs.com , miqi2214.cnblogs.com
* Date: 2010-3-10
*/
//注意：如果调试出错，请检查您引用的jquery版本号，当前引用版本为1.3
//参数说明：
//tabList:包裹选项卡的父级层
//tabTxt :包裹内容层的父级层
//options.currentTab:激活选项卡的序列号
//options.defaultClass:当前选项卡激活状态样式名，默认名字为“current”
//isAutoPlay:是否自动切换
//stepTime:切换间隔时间
//switchingMode:切换方式（'c'表示click切换;'o'表示mouseover切换）
//调用方式 如本页最下方代码
$.fn.tabs = function(tabList, tabTxt, options) {
    var _tabList = $(this).find(tabList);
    var _tabTxt = $(this).find(tabTxt);

    //为了简化操作，强制规定选项卡必须用li标签实现

    var tabListLi = _tabList.find("li");
    var defaults = { currentTab: 0, defaultClass: "current", isAutoPlay: false, stepTime: 2000, switchingMode: "c" };
    var o = $.extend({}, defaults, options);
    var _isAutoPlay = o.isAutoPlay;
    var _stepTime = o.stepTime;
    var _switchingMode = o.switchingMode;
    _tabList.find("li:eq(" + o.currentTab + ")").addClass(o.defaultClass);

    //强制规定内容层必须以div来实现
    _tabTxt.children("div").each(function(i) {
        $(this).attr("id", "wp_div" + i);
    }).eq(o.currentTab).css({ "display": "block" });


    tabListLi.each(
		function(i) {
		    $(tabListLi[i]).mouseover(
				function() {
				    if (_switchingMode == "o") {
				        $(this).click();
				    }
				    _isAutoPlay = false;
				}
			);
		    $(tabListLi[i]).mouseout(
				function() {
				    _isAutoPlay = true;
				}
			)
		}
	);

    _tabTxt.each(
		function(i) {
		    $(_tabTxt[i]).mouseover(
				function() {
				    _isAutoPlay = false;
				}
			);
		    $(_tabTxt[i]).mouseout(
				function() {
				    _isAutoPlay = true;
				}
			)
		});

    // }
    //    else {
    tabListLi.each(
		function(i) {
		    $(tabListLi[i]).click(
				function() {
				    if ($(this).className != o.defaultClass) {
				        $(this).addClass(o.defaultClass).siblings().removeClass(o.defaultClass);
				    }
				    if ($.browser.msie) {
				        _tabTxt.children("div").eq(i).siblings().css({ "display": "none" });
				        _tabTxt.children("div").eq(i).fadeIn(600);
				    } else {
				        _tabTxt.children("div").eq(i).css({ "display": "block" }).siblings().css({ "display": "none" }); //标准样式
				    }
					if ($.isFunction(window.init)){
						init();
					}
					if ($.isFunction(window.$.cookie)){
						$.cookie('tab_n', i, { expires:1,path:'/'});
					}
					
				}
			)
		}
	);

    // }
    function selectMe(oo) {

        if (oo != null && oo.html() != null && _isAutoPlay) {
            oo.click();
        }
        if (oo.html() == null) {
            selectMe(_tabList.find("li").eq(0));

        } else {
            window.setTimeout(selectMe, _stepTime, oo.next());
        }
    }
    if (_isAutoPlay) {
        //alert("_isAutoPlay:" + _isAutoPlay);
        selectMe(_tabList.find("li").eq(o.currentTab));
    }
    //alert(_isAutoPlay);
    return this;
};




var userName = "wbpbest";
//根据用户名显示欢迎信息
function hello(_name) {
    alert("hello," + _name);
} //*=============================================================
//*   功能： 修改 window.setInterval ，使之可以传递参数和对象参数   
//*   方法： setInterval (回调函数,时间,参数1,,参数n)  参数可为对象:如数组等
//*============================================================= 

var __sti = setInterval;
window.setInterval = function(callback, timeout, param) {
    var args = Array.prototype.slice.call(arguments, 2);
    var _cb = function() {
        callback.apply(null, args);
    }
    __sti(_cb, timeout);
}
//window.setInterval(hello,3000,userName);

//*=============================================================
//*   功能： 修改 window.setInterval ，使之可以传递参数和对象参数   
//*   方法： setInterval (回调函数,时间,参数1,,参数n)  参数可为对象:如数组等
//*=============================================================

var __sto = setTimeout;
window.setTimeout = function(callback, timeout, param) {
    var args = Array.prototype.slice.call(arguments, 2);
    var _cb = function() {
        callback.apply(null, args);
    }
    __sto(_cb, timeout);
}








/*$(document).ready(function() {
    $("#ex02").tabs(".tabTitle", ".tabBox", { currentTab: 0, isAutoPlay: false, switchingMode: 'c' });
	$("#ex01").tabs(".tabTitle", ".tabBox", { currentTab: 0, isAutoPlay: false, switchingMode: 'c' });
});*/



