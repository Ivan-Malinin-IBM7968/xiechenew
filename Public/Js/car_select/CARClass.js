
      
    /**  
     * 事件处理工具类  
     *  
     * @author bao110908  
     */  
    var Event = function(){}  
      
    Event = {  
      
      /**  
       * 为 element 使用 handler 处理程序添加至 event 事件  
       * 兼容 IE 及 Firefox 等浏览器  
       *  
       * 例如为 botton 对象添加 onclick 事件，使用 clickEvent  
       * 方法作为处理程序：  
       *   Event.addEvent(botton, 'click', clickEvent);  
       *  
       * @param element  需要添加事件的对象（Object）  
       * @param event    需要添加的事件名称（String），不加“on”  
       * @param handler  需要添加的方法引用（Function）  
       */  
      addEvent : function(element, event, handler) {  
        if(element.attachEvent) {  
          element.attachEvent('on' + event, handler);  
        } else if (element.addEventListener) {  
          element.addEventListener(event, handler, false);  
        } else {  
          element['on' + event] = handler;  
        }  
      },  
      
      /**  
       * 添加事件处理程序时，只能添加一个方法的引用，并不能给  
       * 方法加上参数。比如定义了 clickEvent(str) 这个方法，现  
       * 在要将其作为 obj 的 onclick 的事件处理程序，就可以用：  
       * obj.onclick = Event.getFuntion(null, clickEvent, str);  
       */  
      getFunction : function(obj, fun) {  
        var args = [];  
        objobj = obj || window;  
        for(var i = 2; i < arguments.length; i++) {  
          args.push(arguments[i]);  
        }  
        return function() {  
            fun.apply(obj, args);  
          };  
      }  
    }  
      
    /**  
     * 联动处理类  
     *  
     * @param first   第一个 select 的 ID  
     * @param second  第二个 select 的 ID  
     * @param dataSet 第二个 select 的数据  
     *  
     * @author bao110908  
     */  
    var Linkage = function(first, second, data_n) {  
      this.first = document.getElementById(first);  
      this.second = document.getElementById(second);  
	  this.data_n = data_n;
    }  
      
    /**  
     * 初始化第一个 select 的数据  
     */  
    Linkage.initFirst = function(first, dataSet) {  
      var base = document.getElementById(first);    
      base.options.length = 1;  
      for(var k in dataSet) {  
        var opt = new Option(dataSet[k], k);  
        base.options[base.options.length] = opt;  
      }  
    }  
      
    Linkage.prototype = {  
      
      // 初始化  
      init : function() {  
        this.addOnChange();  
      },  
      
      // 为第一个 select 添加 onchange 事件  
      addOnChange : function() {  
        Event.addEvent(this.first, 'change', Event.getFunction(this, this._onChangeEvent));  
      },  
      
      // onchange 事件处理  
      _onChangeEvent : function() {  
        this._defaultSelect();  
        var data = this._getData(this.first.value,this.data_n);  
        if(!data) {  
          return;  
        }  
        this.second.options.length = 1;  
        for(var k in data) {  
          var opt = new Option(data[k], k);  
          this.second.options[this.second.options.length] = opt;  
        }  
      },  
      
      // 获取数据，如果采用 Ajax 方式，需要进行更改  
      // Ajax 应返回 { '101' : {'101' : 'a101', '102', 'a102' }} 这样的数据格式  
      // 然后再使用 eval('(' + ajaxData + ')'); 转为 JSON 对象  
      _getData : function(value,data_n) {  
        return data_n[value];  
      },  
      
      // 重新选择时的处理  
      _defaultSelect : function() {  
        this.second.selectedIndex = 0;  
        this.second.options.length = 1;  
        if(this.second.fireEvent) {  
          // IE  
          this.second.fireEvent('onchange');  
        } else {  
          // DOM 2  
          var event = document.createEvent('HTMLEvents');  
          event.initEvent('change', false, true);  
          this.second.dispatchEvent(event);  
        }  
      }  
    }  