var bitAdFrame = {
    $: function (id) { return document.getElementById(id) }
	, tagArr: function (o, name) { return o.getElementsByTagName(name) }
	, nameArr: function (name) { return document.getElementsByName(name) }
	, att: function (o, name, fun) { return document.all ? o.attachEvent(name, fun) : o.addEventListener(name.substr(2), fun, false); }
	, style: function (o) { return o.currentStyle || document.defaultView.getComputedStyle(o, null); }
	, scroll: function (type) { return document.documentElement[type] || document.body[type] }
	, client: function (type) { return document.documentElement[type] || document.body[type] }
	, buildTag: function (id, tagName, arr, object) {
	    var obj = document.createElement(tagName);
	    if (id) { obj.id = id; }
	    if (arr) {
	        for (i = 0; i < arr.length; i++) {
	            obj.style[arr[i][0]] = arr[i][1];
	        }
	    }
	    object.appendChild(obj);
	}
	, buildHtml: function (con, close, clickurl, counturl) {
	    var str = '';
	    var col = close ? ('<div style="font-size:12px;cursor:pointer;" onclick="this.parentNode.style.display=\'none\'">关闭</div>') : '';
	    if (con.type == 'image') {
	        str = '<a href="' + con.link + '" target="_blank"><img src="' + con.url + '" style="border:none; width:' + con.width + 'px;height:' + con.height + 'px"/></a>';
	    } else {
	        str += '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0" width="' + con.width + '" height="' + con.height + '">';
	        str += '  <param name="movie" value="' + con.url + '" />';
	        str += '  <param name="quality" value="high" />';
	        str += ' <param value="transparent" name="wmode"/>';
	        str += '  <embed src="' + con.url + '" quality="high"  wmode="transparent" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="' + con.width + '" height="' + con.height + '"></embed>';
	        str += '</object>';
	    }
	    if (clickurl && clickurl != '') {
	        str += '<div style="margin: -' + con.height + 'px 0px 0px; cursor: pointer; background-image: url(http://gimg.bitauto.com/resourcefiles/bg.gif); position: relative; width: ' + con.width + 'px; height: ' + con.height + 'px; z-index: 10; left: 0px;" onclick="RedirectOnClick(\'' + clickurl + '\',\'' + counturl + '\',true,true);"></div>';
	    }
	    str += col;
	    return str;
	}
	, roll: function (id, top) {
	    var obj = bitAdFrame.$(id);
	    var space = top + bitAdFrame.scroll('scrollTop'), objTop = parseInt(bitAdFrame.style(obj).top), pro = this, a;
	    if (objTop < space) {
	        a = (space - objTop) * 0.01;
	        obj.style.top = objTop + a * 20 + 'px';
	    } else if (objTop > space) {
	        a = (objTop - space) * 0.01;
	        obj.style.top = objTop - a * 20 + 'px';
	    }
	    setTimeout(function () { pro.roll(id, top) }, 10);
	}
};
function RedirectOnClick(url, newurl, recHit, newWin) {
    window.open(url);
    if (recHit) SendToPage(newurl + '&r=' + Math.random());
}
function SendToPage(url) {
    var SendPageImg = new Image();
    SendPageImg.src = url;
}
function getvalue(paras) {
    var url = location.href;
    var paraString = url.substring(url.indexOf("?") + 1, url.length).split("&");
    var paraObj = {};
    for (var i = 0; i < paraString.length; i++) {
        var j = paraString[i];
        if (j.indexOf("=") > 0) {
            paraObj[j.substring(0, j.indexOf("=")).toLowerCase()] = j.substring(j.indexOf("=") + 1, j.length);
        }
    }
    var returnTempValue = "";
    var returnValue = paraObj[paras.toLowerCase()];
    if (typeof (returnValue) == "undefined") {
        returnTempValue = "";

    } else {
        returnTempValue = returnValue;
    }
    if (returnTempValue == "") {
        if (window.parent) {
            returnTempValue = getvalueParent(paras);
        }
    }
    return returnTempValue;
}
function getvalueParent(paras) {
    var url = document.referrer;
    var paraString = url.substring(url.indexOf("?") + 1, url.length).split("&");
    var paraObj = {};
    for (var i = 0; i < paraString.length; i++) {
        var j = paraString[i];
        if (j.indexOf("=") > 0) {
            paraObj[j.substring(0, j.indexOf("=")).toLowerCase()] = j.substring(j.indexOf("=") + 1, j.length);
        }
    }
    var returnTempValue = "";
    var returnValue = paraObj[paras.toLowerCase()];
    if (typeof (returnValue) == "undefined") {
        returnTempValue = "";

    } else {
        returnTempValue = returnValue;
    }
    return returnTempValue;
}

var __bitauto_adPlay =
{
    CharSet: "gb2312",
    AdPlayUrl: "",
    DynamicLoadJavaScript: function () {
        var adplay_site_url = "http://g.bitauto.com/srv/";
        var _playTime = getvalue("adplay_time");
        var _playCityName = getvalue("adplay_cityname");
        var log = getvalue("log");
        this.AdPlayUrl = adplay_site_url + "getAdData.ashx";
        var _ipAreaID = '';
        var _ipCityName = '';
        if (typeof (bit_locationInfo) != 'undefined') {
            if (typeof (bit_locationInfo.cityName) != 'undefined') {
                _ipAreaID = bit_locationInfo.cityName;
                _ipCityName = bit_locationInfo.cityName;
            }
            if (typeof (bit_locationInfo.cityId) != 'undefined') {
                _ipAreaID = bit_locationInfo.cityId;
            }
        }
        var _CityName = "";
        var _AreaName = "";
        if (_playCityName == "") {
            if (typeof (adplay_CityName) == "undefined" || adplay_CityName == "") {
                if (typeof (BitautoCityForAd) == "undefined" || BitautoCityForAd == "") {
                    _CityName = _ipCityName;
                }
                else {
                    _CityName = BitautoCityForAd;
                }
            }
            else {
                _CityName = adplay_CityName;
            }
            if (typeof (adplay_AreaName) == "undefined" || adplay_AreaName == "") {
                if (typeof (BitautoCityForAd) == "undefined" || BitautoCityForAd == "") {
                    _AreaName = _ipAreaID;
                }
                else {
                    _AreaName = BitautoCityForAd;
                }
            }
            else {
                _AreaName = adplay_AreaName;
            }
        }
        else {
            _CityName = _playCityName;
            _AreaName = _playCityName;
        }

        var _IP = (typeof (adplay_IP) == "undefined") ? '' : adplay_IP;
        var _brandtype = (typeof (adplay_BrandType) == "undefined") ? '' : adplay_BrandType;
        var _BlockCode = (typeof (adplay_BlockCode) == "undefined") ? '' : adplay_BlockCode;
        var _BrandName = (typeof (adplay_BrandName) == "undefined") ? '' : adplay_BrandName;
        var _BrandID = (typeof (adplay_BrandID) == "undefined") ? '' : adplay_BrandID;
        this.AdPlayUrl += "?areaname=" + escape(_AreaName) + "&brandtype=" + escape(_brandtype) + "&cityname=" + escape(_CityName) + "&BlockCode=" + _BlockCode + "&playTime=" + _playTime + "&brandname=" + escape(_BrandName) + "&brandid=" + escape(_BrandID) + "&ip=" + escape(_IP) + "&c=" + this.CharSet;
        if (log == "1") { this.AdPlayUrl += "&log=1"; }
        adplay_IP = adplay_AreaName = adplay_BrandType = adplay_CityName = adplay_BlockCode = adplay_BrandName = adplay_BrandID = '';
        document.write("<sc" + "ript src='" + this.AdPlayUrl + "'><\/sc" + "ript>");
    },
    AppendHTML: function () {
        this.DynamicLoadJavaScript();
    }
};

// 浏览器类型
var __Browser = new Object();
__Browser.isIE = window.ActiveXObject ? true : false;
__Browser.isFirefox = (navigator.userAgent.toLowerCase().indexOf("firefox") != -1);
// 当前访问页面编码格式
if (__Browser.isIE) { __bitauto_adPlay.CharSet = document.charset; }
if (__Browser.isFirefox) { __bitauto_adPlay.CharSet = document.characterSet; }

// 输出[adplay]广告
__bitauto_adPlay.AppendHTML();