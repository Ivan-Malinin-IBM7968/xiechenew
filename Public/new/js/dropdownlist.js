/**
*===============================================================
*车型下拉列表实现文件
*<pre>
*1.支持按钮点击跳转
*2.支持默认值
*3.支持子级无数据不可用
*4.支持跳转类型
*5.支持自定义初始描述
*6.支持跳转url自定义
*7.支持自定义取数据字段，默认value:id,text:name
*</pre>
*@version: 3.0
*@author:  sk(songkai@bitauto.com)
*@date:    2012-04-20
*===============================================================
*/
var requestDatalist = {};
var browserName = navigator.userAgent.toLowerCase();
var BitA = BitA || {};
BitA.Tools = {
	extend: function () {
		var options, name, src, copy, copyIsArray, clone, target = arguments[0] || {}, i = 1, length = arguments.length, deep = false;
		if (typeof target === "boolean") {
			deep = target;
			target = arguments[1] || {};
			i = 2;
		}
		if (typeof target !== "object" && typeof target != "function") {
			target = {};
		}
		if (length === i) {
			target = this;
			--i;
		}
		for (; i < length; i++) {
			if ((options = arguments[i]) != null) {
				for (var name in options) {
					src = target[name];
					copy = options[name];
					if (target === copy) {
						continue;
					}
					if (deep && copy && (copy instanceof Object || (copyIsArray = copy instanceof Array)) && !(copy instanceof Function)) {
						if (copyIsArray) {
							copyIsArray = false;
							clone = src && src instanceof Array ? src : [];
						} else {
							clone = src && src instanceof Object ? src : {};
						}
						target[name] = BitA.Tools.extend(deep, clone, copy);
					} else if (copy !== undefined) {
						target[name] = copy;
					}
				}
			}
		}
		return target;
	},
	format: function () {
		if (arguments.length == 0)
			return null;
		var str = arguments[0];
		var obj = arguments[1];
		for (var key in obj) {
			var re = new RegExp('\\{' + key + '\\}', 'gi');
			str = str.replace(re, obj[key]);
		}
		return str;
	},
	addEvent: function (elm, type, fn, useCapture) {
		if (elm.addEventListener) {
			elm.addEventListener(type, fn, useCapture);
			return true;
		} else if (elm.attachEvent) {
			var r = elm.attachEvent('on' + type, fn);
			return r;
		} else {
			elm['on' + type] = fn;
		}
	},
	removeEvent: function (elem, type, fn) {
		if (elem.removeEventListener) {
			elem.removeEventListener(type, fn, false);
		} else {
			elem.detachEvent("on" + type, fn);
			elem[type + fn] = null;
			elem["e" + type + fn] = null;
		}
	},
	browser: {
		version: (browserName.match(/.+(?:rv|it|ra|ie)[\/: ]([\d.]+)/) || [0, '0'])[1],
		safari: /webkit/i.test(browserName) && !this.chrome,
		opera: /opera/i.test(browserName),
		firefox: /firefox/i.test(browserName),
		ie: /msie/i.test(browserName) && !/opera/.test(browserName),
		mozilla: /mozilla/i.test(browserName) && !/(compatible|webkit)/.test(browserName) && !this.chrome,
		chrome: /chrome/i.test(browserName) && /webkit/i.test(browserName) && /mozilla/i.test(browserName)
	}
};
var loadJS = {
	lock: false, ranks: []
    , callback: function (startTime, callback) {
    	//载入完成
    	this.lock = false;
    	callback && callback(new Date().valueOf() - startTime.valueOf()); //回调	
    	this.read(); //解锁，在次载入
    }
    , read: function () {
    	//读取
    	if (!this.lock && this.ranks.length) {
    		var head = document.getElementsByTagName("head")[0];

    		if (!head) {
    			ranks.length = 0, ranks = null;
    			throw new Error('HEAD不存在');
    		}

    		var wc = this, ranks = this.ranks.shift(), startTime = new Date, script = document.createElement('script');

    		this.lock = true;

    		script.onload = script.onreadystatechange = function () {
    			if (script && script.readyState && script.readyState != 'loaded' && script.readyState != 'complete') return;

    			script.onload = script.onreadystatechange = script.onerror = null, script.src = ''
		            , script.parentNode.removeChild(script), script = null; //清理script标记

    			wc.callback(startTime, ranks.callback), startTime = ranks = null;
    		};

    		script.charset = ranks.charset || 'gb2312';
    		script.src = ranks.src;

    		head.appendChild(script);
    	}
    }
    , push: function (src, charset, callback) {
    	//加入队列
    	this.ranks.push({ 'src': src, 'charset': charset, 'callback': callback });
    	this.read();
    }
};
var DomHelper = {
	cancelClick: function (e) {
		if (window.event && window.event.cancelBubble
            && window.event.returnValue) {
			window.event.cancelBubble = true;
			window.event.returnValue = false;
			return;
		}
		if (e && e.stopPropagation && e.preventDefault) {
			e.stopPropagetion();
			e.preventDefault();
		}
	},
	createOption: function (obj, defValue) {
		if (obj == null) return null;
		var optionItem = document.createElement("OPTION");
		optionItem.setAttribute("value", obj["value"]);
		if (defValue && obj["value"] == defValue) {
			optionItem.selected = true;
		}
		optionItem.appendChild(document.createTextNode(obj["text"]));
		if (obj["bgcolor"] != null) optionItem.style.backgroundColor = obj["bgcolor"];
		return optionItem;
	},
	createGroupOption: function (obj) {
		if (obj == null) return;
		var optionItem = document.createElement("OPTGROUP");
		optionItem.label = obj["text"];
		optionItem.style.fontStyle = obj["style"];
		optionItem.style.background = obj["color"];
		optionItem.style.textAlign = obj["align"];
		return optionItem;
	},
	getSelectElementValue: function (obj) {
		if (obj == null || !obj) return;
		if (obj.length == 0) return;
		var value = obj.options[obj.selectedIndex].value;
		return value;
	},
	setSelected: function (objSelect, value) {
		if (objSelect == null || !objSelect || objSelect.length == 0) {
			return;
		}
		for (var i = 0; i < objSelect.options.length; i++) {
			if (objSelect.options[i].value == value) {
				if (BitA.Tools.browser.ie && BitA.Tools.browser.version == "6.0") {
					objSelect.options[i].setAttribute("selected", "true");
				} else {
					objSelect.options[i].selected = true;
				}
				break;
			}
		}
	},
	clearDomObject: function (obj) {
		if (!obj) return;
		var eachGroup = obj.firstChild;
		while (eachGroup != null) {
			obj.removeChild(eachGroup);
			eachGroup = obj.firstChild;
		}
		obj.disabled = true; //初始重选，下拉列表不可用。by songkai
	}
}
function BindSelect(options) {
	this.defaults = {
		url: "http://car.bitauto.com/Interface/LeveldropdownlistData/MasterBrandToSerialNew.aspx",
		encode: "utf-8",
		container: { producer: "bit_producer1", master: "bit_master1", brand: "bit_brand1", serial: "bit_serial1", cartype: "bit_cartype1" },
		serias: "m",
		include: {},
		dvalue: {},
		checkdata: null,
		field: { deffield: { value: "id", text: "name"} },
		datatype: 4,
		condition: "type={type}&pid={pid}&rt={rt}&serias={serias}&key={rt}_{pid}_{type}_{serias}",
		background: "#efefef",
		groupoprtionstyle: { "style": "normal", "color": "#efefef", "align": "center" },
		abbreviation: { "producer": "p", "master": "m", "brand": "b", "serial": "s", "cartype": "t" },
		deftext: {
			producer: { "value": "0", "text": "请选择厂商" },
			master: { "value": "0", "text": "请选择品牌" },
			brand: { "value": "0", "text": "请选择品牌" },
			serial: { "value": "0", "text": "请选择系列" },
			cartype: { "value": "0", "text": "请选择车款" }
		},
		btn: {
			car: {
				id: "bit_def_btnCar",
				url: {
					cartype: {},
					serial: { "url": "{defurl}{spell}/", params: { "spell": "urlSpell"} },
					master: { "url": "{defurl}{spell}/", params: { "spell": "urlSpell"} }
				},
				defurl: { "url": "http://car.bitauto.com/" }
			},
			price: {
				id: "bit_def_btnPrice",
				url: {
					cartype: {},
					serial: { "url": "http://car.bitauto.com/{param1}/baojia/", params: { "param1": "urlSpell" }, defparams: {} },
					master: { "url": "http://price.bitauto.com/keyword.aspx?mb_id={param1}", params: { "param1": "id"} }
				},
				defurl: { "url": "http://price.bitauto.com/" }
			}
		},
		gotype: 1,
		bind: null
	};
	BitA.Tools.extend(true, this.defaults, options); //对默认配置深度复制
	//容器不需要深度复制
	if (options["container"]) {
		this.defaults["container"] = options["container"];
	} else {
		//查找默认id
		var defcontainer = {};
		for (var type in this.defaults.container) {
			var obj = document.getElementById(this.defaults.container[type]);
			if (obj)
				defcontainer[type] = this.defaults.container[type];
		}
		this.defaults["container"] = defcontainer;
	}
	this.Data = {};
	var pro = this;
	//事件绑定
	if (this.defaults.bind != null) {
		for (var key in this.defaults.btn) {
			var control = document.getElementById(this.defaults.btn[key]["id"]);
			if (!control) continue;
			var fnName = "anonymous_" + key;
			//BitA.Tools.removeEvent(control, this.defaults.bind, fnName);
			BitA.Tools.addEvent(control, this.defaults.bind, fnName = function (tkey) { return function () { pro.Click(tkey); } } (key), false);
		}
	}
}
//绑定下拉列表
BindSelect.prototype.BindList = function (type, parentDataId) {
	if (type == null || type == "") {
		for (var index in this.defaults.container) {
			type = index; break;
		}
		this.BindList(type, 0);
		return;
	}
	var typeObj = this.Data[type];
	//如果没有父ID并且此类型对象存在，则直接绑定对象属性
	if (parentDataId == 0
            && typeof typeObj != 'undefined'
            && typeObj != null) {
		this.AddOption(type, typeObj);
		return;
	}
	else if (parentDataId == 0)//如果父ID还是等于0
	{
		this.GetDataList(type, 0); //通地createscript方式得到数据
		return;
	}

	/*以下为parentDataId不为零的情况*/
	var isOptionObject = {}; //定义一个要绑定的对象
	var preType = this.getRelatObjctType(type, -1); //得到他上一级的类型
	var thumType = this.defaults.abbreviation[preType];
	var currentthumType = this.defaults.abbreviation[type];
	//如果数据对象不存在，或者上一级的对象不类型不存在，或者上一级的对象不存在
	if (this.Data == null
            || typeof this.Data[preType] == 'undefined'
            || this.Data[preType] == null) return;
	//得到父ID对照的对象
	var preObject = this.Data[preType][thumType + parentDataId];
	if (typeof preObject == 'undefined' || preObject == null) return;

	//如果父对象中不包含子对象数组
	if (preObject["nl"] == null || preObject["nl"].length < 1) {
		this.GetDataList(type, parentDataId);
		return;
	}
	//如果父对象中包含存在子对象数组
	if (typeof typeObj == 'undefined' || typeObj == null) return;
	//赋值要绑定的对象
	var ilength = preObject["nl"].length;
	for (var i = 0; i < ilength; i++) {
		var typeId = currentthumType + preObject["nl"][i];
		var nodeObject = typeObj[typeId];
		if (typeof nodeObject == 'undefined' || nodeObject == null) continue;
		isOptionObject[typeId] = nodeObject;
	}
	this.AddOption(type, isOptionObject);
}
//添加options项
BindSelect.prototype.AddOption = function (type, dataObj) {
	type = type.toLowerCase();
	var control = document.getElementById(this.defaults.container[type]);
	if (!control || control.nodeName.toLowerCase() != "select") return;
	this.BindDefaultValue(type);

	var selectObj = this.defaults.container[type];
	var abb = this.defaults.abbreviation[type]; //得到类型的前缀缩写
	if (dataObj == null) return;
	control.disabled = false; //有数据，更改下拉列表可用。by songkai
	//要找到要绑定的组
	var tempParentList = {}; //定义一个组对象
	var thorld = 0; //定义组的阈值
	for (var entity in dataObj) {
		var obj = dataObj[entity];
		var groupObj = obj["goid"]; //得到要绑定的对象
		if (typeof groupObj == 'undefined' || groupObj == null)
		{ continue; }
		else {
			var existObj = tempParentList[groupObj];
			if (typeof existObj != 'undefined') { continue; }
			else {
				tempParentList[groupObj] = 1
				thorld++;
			}
		}
	}
	tempParentList = {};
	var optionarea = document.createDocumentFragment(); //创建一个document片段
	var thorldValue = 0;
	var thorldList = {};
	//绑定对象
	for (var entity in dataObj) {
		var obj = dataObj[entity]; //得到要绑定的对象
		if (obj == null) continue;
		var ftype = this.defaults.field[type] ? type : "deffield";
		var value = obj[this.defaults.field[ftype]["value"]];
		var text = obj[this.defaults.field[ftype]["text"]];
		//如果该类型为第一级
		if (type == "master" || type == "producer") {
			text = obj["tSpell"] + " " + text;
		}
		if (value == null || value == "" || text == null || text == "") continue;
		var preObj = obj["goid"]; //得到要绑定组
		//判断元素类型
		if ((type == "master" || type == "producer") && thorldList[obj["tSpell"]] == null) {
			thorldList[obj["tSpell"]] = "";
			thorldValue++;
		}
		var optionObj = { "value": value, "text": text };
		//判断当前元素是否，并给当前元素加背景色
		if ((type == "master" || type == "producer")
                 && thorldValue % 2 == 0) optionObj["bgcolor"] = this.defaults.background;
		//如果父类超过1个，并且没有创建组
		if (thorld >= 1 && tempParentList[preObj] == null) {
			tempParentList[preObj] = 1; //添加已经绑定的组对象
			var groupObject = this.defaults.groupoprtionstyle;
			groupObject["text"] = obj["goname"];
			optionarea.appendChild(DomHelper.createGroupOption(groupObject));
		}
		optionarea.appendChild(DomHelper.createOption(optionObj));
	}
	control.appendChild(optionarea);
	//判断该下拉列表是否有下一级，如果有则绑定onchange事件
	var preObjType = this.getRelatObjctType(type, 1);
	var pro = this;
	if (preObjType != null) {
		control.disabled = false; //有下一级，更改下拉列表可用。 by songkai
		control.onchange = function () {
			pro.DropDownChange(type, "id");
		}
		this.BindDefaultValue(preObjType);
		if (typeof this.defaults.dvalue[type] != "undefined" && this.defaults.dvalue[type] != null) {
			this.BindList(preObjType, this.defaults.dvalue[type]);
		}
	} else {
		this.setDefaultValue();
	}
}
//根据拼接url获取数据
BindSelect.prototype.GetDataList = function (type, parentDataId) {
	//得到查询条件
	var include = "";
	var conditions = BitA.Tools.format(this.defaults.condition, { "pid": parentDataId, "type": "" + this.defaults.datatype + "", "rt": "" + type + "", "serias": "" + this.defaults.serias + "" });
	if (typeof this.defaults.include != 'undefined' && typeof this.defaults.include[type] != "undefined") {
		conditions += "&include=" + this.defaults.include[type];
	}
	var objName = type + "_" + parentDataId + "_" + this.defaults.datatype + "_" + this.defaults.serias;
	//如果对象包含向上指引
	if (typeof this.defaults.include[type] != 'undefined'
         && this.defaults.include[type] != null) {
		//objName = objName + "_" + this.defaults.include[type];
	}
	var url = this.defaults.url + "?" + conditions;
	var pro = this;
	loadJS.push(url, this.defaults.encode, function () { pro.CallBackOpertion(type, parentDataId, objName); });
}
//处理异步请求成功后，程序要进行的过程
BindSelect.prototype.CallBackOpertion = function (type, parentDataId, objName) {
	//得到返回的数据对象
	var data = requestDatalist[objName];
	var thumType = this.defaults.abbreviation[type];
	if (typeof data == 'undefined' || data == null) {
		this.BindDefaultValue(type);
		return;
	}
	//定义结果数组
	var result = [];
	//当数据筛选的方法不为空时
	if (this.defaults.checkdata != null) result = this.defaults.checkdata(data, thumType);
	else {//如果筛选为空，则把数据变成数组
		var pattern = RegExp(thumType, "gi");
		for (var id in data) {
			result.push(id.replace(pattern, ""));
		}
	}
	//用于绑定的数据
	var bindData = {};
	//赋值用于绑定的数据,并将它添加到控件缓存数据中
	var ilength = result.length;
	for (var i = 0; i < ilength; i++) {
		var index = result[i];
		var entity = data[thumType + index];
		if (typeof entity == 'undefined' || entity == null) continue;
		bindData[thumType + index] = entity;
		if (typeof this.Data[type] == 'undefined' || this.Data[type] == null) this.Data[type] = {};
		this.Data[type][thumType + index] = entity;
	}

	//如果父id大于0,给上一级的数据链表赋值
	if (parentDataId > 0) {
		var preType = this.getRelatObjctType(type, -1);
		var thumType = this.defaults.abbreviation[preType];
		this.Data[preType][thumType + parentDataId]["nl"] = result;
	}
	//添加下拉列表元素
	this.AddOption(type, bindData);

}
//得到绑定值
BindSelect.prototype.GetValue = function (type) {
	if (this.defaults.container == null || type == "") return;
	var obj = this.defaults.container[type.toLowerCase()];
	if (obj == null || obj == "") return;
	var controlObj = document.getElementById(obj);
	if (!controlObj) return;
	return DomHelper.getSelectElementValue(controlObj);

}
//得到绑定值对象
BindSelect.prototype.GetValueObject = function (type) {
	var value = this.GetValue(type);
	var valuedesc = "id";
	var selectObj = this.Data[type];
	var preContent = this.defaults.abbreviation[type.toLowerCase()];
	var pattern = new RegExp(preContent, "g");
	for (var index in selectObj) {
		if (selectObj[index][valuedesc] == value) {
			return selectObj[index];
		}
	}
}
//得到上级或者下级的对象
BindSelect.prototype.getRelatObjctType = function (type, step) {
	var threshold = this.getIndexType(type);
	var list = [];
	for (var obj in this.defaults.container) {
		list.push(obj);
	}
	threshold += step;
	if (threshold < 0 || threshold > list.length) return null;
	return list[threshold];
}
//得到类型的索引
BindSelect.prototype.getIndexType = function (type) {
	var index = 0;
	for (var obj in this.defaults.container) {
		if (obj == type.toLowerCase()) return index;
		index++;
	}
}
//通过数据类型得到数据数组
BindSelect.prototype.getDataListByType = function (type, parentDataId) {
	if (this.Data == null || this.Data[type] == null) return;
	var list = [];
	for (var index in this.Data[type]) {
		list.push(index);
	}
	return list;
}
//获取下拉列表默认值
BindSelect.prototype.getDefaultValue = function (type) {
	switch (type) {
		case "producer":
			return this.defaults.deftext.producer;
		case "master":
			return this.defaults.deftext.master;
		case "brand":
			return this.defaults.deftext.brand;
		case "serial":
			return this.defaults.deftext.serial;
		case "cartype":
			return this.defaults.deftext.cartype;
		default: return null;
	}
}
//绑定列表改变
BindSelect.prototype.DropDownChange = function (type, valuedesc) {
	var value = this.GetValue(type);
	var nexttype = this.getRelatObjctType(type, 1);
	var selectObj = this.Data[type];
	var preContent = this.defaults.abbreviation[type.toLowerCase()];
	var pattern = new RegExp(preContent, "g");
	for (var index in selectObj) {
		if (selectObj[index][valuedesc] == value) {
			this.BindList(nexttype, index.replace(pattern, ""));
			return;
		}
	}
	this.BindList(type, 0);
}
//设置选中
BindSelect.prototype.setDefaultValue = function () {
	if (this.defaults.dvalue != null) {
		for (var type in this.defaults.container) {
			var control = document.getElementById(this.defaults.container[type]);
			if (control != null) {
				if (this.defaults.dvalue[type] != "0") {
					DomHelper.setSelected(control, this.defaults.dvalue[type]);
				}
				this.defaults.dvalue[type] = null;
			}
		}
	}
}
//绑定默认值
BindSelect.prototype.BindDefaultValue = function (type) {
	var obj = this.defaults.container[type];
	if (obj == null) { return; }
	else if (obj == "") {
		this.BindDefaultValue(this.getRelatObjctType(type, 1));
		return;
	}
	var control = document.getElementById(obj);
	if (!control) {
		this.BindDefaultValue(nexttype);
		return;
	}
	var nexttype = this.getRelatObjctType(type, 1);
	DomHelper.clearDomObject(control);
	control.appendChild(DomHelper.createOption(this.getDefaultValue(type)));
	this.BindDefaultValue(nexttype);
}
//处理对象绑定事件
BindSelect.prototype.Click = function (obj) {
	if (obj == "") return;
	var opObj = this.defaults.btn[obj]; //按钮操作对象
	if (opObj == null) return;
	var gourl = "";
	for (var type in opObj["url"]) {
		var userSelectValue = this.GetValue(type);
		if (typeof userSelectValue == 'undefined' || parseInt(userSelectValue) == 0) continue;
		/*拼接用户需要达到的链接*/
		gourl = this.BindUrl(opObj, opObj["url"][type], type);
		break;
	}
	//如果地址不为空，跳转
	if (gourl == "") {
		gourl = BitA.Tools.format(opObj["defurl"]["url"], opObj["defurl"]["defparams"]);
	} //return;
	switch (this.defaults.gotype) {
		case 1: window.open(gourl, "", "", ""); break;
		case 2: window.location = gourl; break;
		default: window.location.replace(gourl); break;
	}
}
//根据数据参数替换URL
BindSelect.prototype.BindUrl = function (btnObj, urlObj, type) {
	if (urlObj == null || btnObj == null || type == "") return "";
	var urltemp = BitA.Tools.format(urlObj["url"], { "defurl": "" + btnObj["defurl"]["url"] + "" }); //得到链接模板
	var bindObject = this.GetValueObject(type); //得到绑定值对应的对象
	if (bindObject == null) return "";
	var pattern;
	//默认值替换
	urltemp = BitA.Tools.format(urltemp, urlObj["defparams"]);
	//参数替换
	for (var param in urlObj["params"]) {
		pattern = new RegExp("\\{" + param + "\\}", "gi");
		var value = bindObject[urlObj["params"][param]];
		if (typeof value == 'undefined' || value == null) continue;
		var rg = new RegExp("\/", "gi");
		value = value.replace(rg, "");
		urltemp = urltemp.replace(pattern, value); //得到要绑定的url
	}
	//含有父级参数替换
	for (var param in urlObj["parent"]) {
		var parentObject = this.GetValueObject(param);
		for (var valueparam in urlObj["parent"][param]) {
			//要替换的规则
			pattern = new RegExp("\\{" + valueparam + "\\}", "gi");
			var value = parentObject[urlObj["parent"][param][valueparam]];
			if (typeof value == 'undefined' || value == null) continue;
			if (valueparam == "name") value = value;
			urltemp = urltemp.replace(pattern, value);
		}
	}
	return urltemp;
}
//调用下拉列表对象
BitA.DropDownList = function (options) {
	if (!options) options = {};
	new BindSelect(options).BindList();
};