/* jquery-fn-accordion v0
* Based on jQuery JavaScript Library v3
* http://jquery.com/
*
* The author of the following code: miqi2214 , wbpbest
* Blog:eycbest.cnblogs.com , miqi2214.cnblogs.com
* Date: 2010-3-10
*/
//ע�⣺������Գ������������õ�jquery�汾�ţ���ǰ���ð汾Ϊ1.3
//����˵����
//tabList:����ѡ��ĸ�����
//tabTxt :�������ݲ�ĸ�����
//options.currentTab:����ѡ������к�
//options.defaultClass:��ǰѡ�����״̬��ʽ����Ĭ������Ϊ��current��
//isAutoPlay:�Ƿ��Զ��л�
//stepTime:�л����ʱ��
//switchingMode:�л���ʽ��'c'��ʾclick�л�;'o'��ʾmouseover�л���
//���÷�ʽ �籾ҳ���·�����
$.fn.tabs = function(tabList, tabTxt, options) {
    var _tabList = $(this).find(tabList);
    var _tabTxt = $(this).find(tabTxt);

    //Ϊ�˼򻯲�����ǿ�ƹ涨ѡ�������li��ǩʵ��

    var tabListLi = _tabList.find("li");
    var defaults = { currentTab: 0, defaultClass: "current", isAutoPlay: false, stepTime: 2000, switchingMode: "c" };
    var o = $.extend({}, defaults, options);
    var _isAutoPlay = o.isAutoPlay;
    var _stepTime = o.stepTime;
    var _switchingMode = o.switchingMode;
    _tabList.find("li:eq(" + o.currentTab + ")").addClass(o.defaultClass);

    //ǿ�ƹ涨���ݲ������div��ʵ��
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
				        _tabTxt.children("div").eq(i).css({ "display": "block" }).siblings().css({ "display": "none" }); //��׼��ʽ
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
//�����û�����ʾ��ӭ��Ϣ
function hello(_name) {
    alert("hello," + _name);
} //*=============================================================
//*   ���ܣ� �޸� window.setInterval ��ʹ֮���Դ��ݲ����Ͷ������   
//*   ������ setInterval (�ص�����,ʱ��,����1,,����n)  ������Ϊ����:�������
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
//*   ���ܣ� �޸� window.setInterval ��ʹ֮���Դ��ݲ����Ͷ������   
//*   ������ setInterval (�ص�����,ʱ��,����1,,����n)  ������Ϊ����:�������
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



