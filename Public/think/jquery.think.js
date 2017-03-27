var Think ={
    frag:{},
    msg:{},
    op:function(v){
        this.op.basePath=v.basePath||"/Public/think";//jquery.think所在路径（基路径）
        this.op.url=this.op.basePath+"/Think.xml";//xml文件所在路径
        this.op.jsdir=v.jsdir||this.op.basePath+"/ext/"//扩展目录（存放js）
        this.op.cssdir=this.op.basePath+"/style/";//风格样式目录（存放css）
        this.op.var_ajax="ajax";//ajax变量
        this.op.listen=function(p){};//定义监听事件， 在这里定义的监听事件对ajax返回内容也有效
        //提示框默认popup参数
        this.op.alert_0={
        	frag:"alert",
            pos:"00"
        };
        this.op.alert_1={
        	frag:"alert",
            pos:"00",
            fadeout:2000
        };
        this.op.alert_2={
        	frag:"alert",
            pos:"52!",
            x:10,
            y:10
        };
        this.op.popupz=9999;//弹出层的z-index
        this.op.popX=0;//弹出框位置，pos，5开始时使用
        this.op.popY=0;
        this.op.opacity=0.6;//笼罩层透明度
        //弹出层默认参数
        this.op.popup={
            base:null,//基点对象
            closeObj:null,//关闭监听对象
            listen:function(poplay){},//监听扩展函数， 默认会监带以下属性的元素：frag、close、inpop， 如果还需要监听其他元素，可用listen函数进行扩展。
            frag:'popup',//样式代码片段的id
            id:"",//弹出框id
            content:"",//显示在弹出框中的内容
            pos:'00',//基点方向
            drag:true,//拖动
            time:0,//多少时间后自动消失,单位毫秒，默认为0，为0表示不自动消失。
            cover:false,//是否笼罩
            infun:Think.popIn,//定义入场效果， 默认为Think.popIn
            outfun:Think.popOut,//定义出场效果，默认为Think.popOut
            fadein:0,//淡入，单位毫秒，infun为Think.popIn时才有效
            fadeout:0,//淡出，单位毫秒,outfun为Think.popOut时才有效
            x:0,//相对于基点偏移量x
            y:0//相对于基点偏移量y
        };
        //扩展配置
        this.op.ext={};
        //uploadify
        this.op.ext.uploadify={
            'uploader'  : this.op.jsdir+'uploadify/uploadify.swf',
            'script'    : this.op.jsdir+'uploadify/uploadify.php',//上传测试文件，不建议在实际项目时使用
            'cancelImg' : this.op.jsdir+'uploadify/cancel.png',
            'folder'    : this.op.jsdir+'uploadify/uploadfile',
            'multi' :true,
            'auto'      : true
        };

    },
    loadUrl:{},
    //判断是否为ie6
    isie6: function()
    {
        if ($.browser.msie && $.browser.version == "6.0")
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    ,
    //判断是否定义变量
    defined: function(variable)
    {
        return $.type(variable) == "undefined" ? false : true;
    }
    ,
    //ajax提交
    A: function(v)
    {
        //v可定义，url，from，method,callback
        callback = v.callback || Think.C;
        if (!$.isFunction(callback))
        {
            callback = eval(callback);
        }
        method = v.method || "GET";
        url = v.url;
        data = '';
        //判断是否定义from
        if (Think.defined(v.form))
        {
            //如果没有定义url，url为from的url
            $form = v.form;
            url = Think.defined(v.url) ? v.url : $form.attr("action");
            //method为form的method。
            method = $form.attr("method");
            //生成数据
            data = $form.serializeArray();
        }
        //标记ajax
        url += (url.search(/\?/) > 0 ? '&' : '?') + Think.op.var_ajax+'=1'
        //ajax提交
        $.ajax(
        {
            type: method,
            url: url,
            data: data,
            dataType: "xml",
            cache:false,
            success: callback,
            error: Think.ajaxError
        }
        );
        return false;

    }
    ,
    //iframe提交。
    I: function(v){
        //v可定义，form，callback
        callback=v.callback||Think.C;
        if (!$.isFunction(callback))
        {
            callback = eval(callback);
        }
        $form=v.form;
        $iframe = $("#think_calliframe");
        if ($iframe.size() == 0) {
            $iframe = $("<iframe id='think_calliframe' name='think_calliframe' src='about:blank' style='display:none'></iframe>").appendTo("body");
        }
        $form.attr("target","think_calliframe");
        $form.append('<input type="hidden" name="'+Think.op.var_ajax+'" value="1" />');
        $document = $(document);
        iframe=$iframe[0];
        $document.trigger("ajaxStart");
        $iframe.bind("load", function(){
            $iframe.unbind("load");
            $document.trigger("ajaxStop");

            if (iframe.src == "javascript:'%3Chtml%3E%3C/html%3E';" || // For Safari
                iframe.src == "javascript:'<html></html>';") { // For FF, IE
                return;
            }

            var doc = iframe.contentDocument ? iframe.contentDocument : window.frames[iframe.id].document;

            // fixing Opera 9.26,10.00
            if (doc.readyState && doc.readyState != 'complete') return;
            // fixing Opera 9.64
            if (doc.body && doc.body.innerHTML == "false") return;

            var response;

            if (doc.XMLDocument) {
                // response is a xml document Internet Explorer property
                response = doc.XMLDocument;
            } else if (doc.body){
                try{
                    response = $iframe.contents().find("body").html();
                    if (response) {
                        response = eval("(" + response + ")");
                    } else {
                        response = {};
                    }
                } catch (e){ // response is html document or plain text
                    response = doc.body.innerHTML;
                }
            } else {
                // response is a xml document
                response = doc;
            }

            callback(response);
        });

    }
    ,
    //默认的ajax的callback函数
    C: function(xml,callback)
    {
        //独立成功和失败的提示。
        var s = $(xml).find("status").text();
        if (s != "")
        {
        	var show=$(xml).find("showAlert").text();
            //判断是否弹出提示框
            if(show=="1"){
            Think.alertMsg({
                msg:$(xml).find("info").text(),
                type:s,
                timeOut:$(xml).find("waitSecond").text()
            });
            }	
            jumpUrl = $(xml).find("jumpUrl").text();
            if (jumpUrl != "")
            {
                //判断是跳转，还是ajax访问
                redirect = $(xml).find("redirect").text();
                if (redirect != "0")
                {
                    window.location = jumpUrl;
                }
                else
                {
                    Think.A(
                    {
                        url: jumpUrl
                    }
                    );
                }
            }
        }
        else
        {
            //分析片段,定义需要局部刷新层
            $(xml).find("frag").each(function()
            {
                //注意，不能让frag层为第一级原始。
                var frag = $(this).attr("id");
                $("[frag='" + frag + "']").replaceWith($($(this).text()).initUI());
            }
            );
        }
        if($.isFunction(callback)) callback(xml);

    }
    ,
    //ajax失败函数
    ajaxError: function(xhr, ajaxOptions, thrownError)
    {
        alert("Http status: " + xhr.status + " " + xhr.statusText +
            "\najaxOptions: " + ajaxOptions + "\nthrownError:" + thrownError +
            "\n" + xhr.responseText);
    }
    ,
    //移动滚动条,n可以是数字也可以是对象
    scrollTop:function(n,t){
        t=t||0;
        num=$.type(n)!="number"?n.offset().top:n;
        $( 'html, body' ).animate( {
            scrollTop: num
        }, t );
    },
    //添加扩展
    //@开始表示绝对路径
    include: function(file){
        var files=$.type(file)=="string"?[file]:file;
        for (var i = 0; i < files.length; i++) {
            var loadurl=name=$.trim(files[i]);
            //判断是否已经加载过
            if(Think.loadUrl[loadurl]){
                break;
            }
            //判断是否有@
            var at=false;
            if(name.indexOf("@")!=-1){
                at=true;
                name=name.substr(1);
            }
            var att = name.split('.');
            var ext = att[att.length - 1].toLowerCase();
            if(ext=="css"){
                //加载css
                var filepath=at?name:Think.op.cssdir+name;
                var newNode = document.createElement("link");
                newNode.setAttribute('type', 'text/css');
                newNode.setAttribute('rel', 'stylesheet');
                newNode.setAttribute('href', filepath);
                Think.loadUrl[loadurl] = 1;
            }else{
                var filepath=at?name:Think.op.jsdir+name;
                //$("<scri"+"pt>"+"</scr"+"ipt>").attr({src:filepath,type:'text/javascript'}).appendTo('head');
                var newNode = document.createElement("script");
                newNode.type = "text/javascript";
                newNode.src = filepath;
                newNode.id=loadurl;//实现批量加载
                newNode.onload = function () {
                    Think.loadUrl[this.id] = 1;
                };
                newNode.onreadystatechange = function () {
                    //针对ie
                    if((newNode.readyState == 'loaded' || newNode.readyState == 'complete')) {
                        Think.loadUrl[this.id] = 1;
                    }
                };
            }

            $("head")[0].appendChild(newNode);

        }

    }
    ,
    //提示信息框
    alertMsg: function(v)
    {
        //参数可以有msg，id,type(0失败，1，成功，2，确认)，timeOut，callback,弹出框参数
        //弹出框参数， Think.op 可配置。
        //获得默认op
        var op=Think.op['alert_'+v.type];
        //获得frag
        op.content=Think.frag['alert_'+v.type].replace("#msg#",v.msg);
        //设置时间
        if(Think.defined(v.id)) op.id="confirm_"+v.id;
        if(v.timeOut) op.time=parseInt(v.timeOut)*1000;
        //设置监听确认按钮
        if($.isFunction(v.callback)){
            op.listen=function(poplay){
                poplay.find("[ok]").click(function(){
                    v.callback();
                });
            };
        }
        //弹出框
        Think.popup(op);
    },
    //弹出层
    popup:function(v){
        //参数base
        var v=$.extend({},Think.op.popup,v);
        //读取frag
        var poplay=$('<div style="position:absolute;">'+Think.frag[v.frag]+"</div>");
        poplay.attr("popid",v.id);
        //加入内容
        poplay.find("[content]").html(v.content);
        //监听UI
        poplay.initUI();
        //监听拖动
        if(v.drag) poplay.find("[drag]").drag(poplay);
        //监听扩展
        if($.isFunction(v.listen)) v.listen(poplay);
        Think.op.popupz+=5;
        poplay.css("z-index",Think.op.popupz).mousedown(function(){
            //被选中的层最前
            Think.op.popupz+=5;
            $(this).css("z-index",Think.op.popupz);
        });
        //加入body
        poplay.appendTo("body");
        if(v.cover) Think.cover(v.fadein);
        //判断位置
        poplay.setPosition({
            pos:v.pos,
            x:v.x,
            y:v.y,
            pageX:v.pageX,
            pageY:v.pageY,
            base:v.base
        });
        //调用入场函数
        if($.isFunction(v.infun)) v.infun(poplay,v);
        var closepop=function(){
            //调用出场函数
            if($.isFunction(v.outfun)) v.outfun(poplay,v);
        };
        //监听关闭
        if(v.time){
            setTimeout(closepop,v.time);
        }
        poplay.find("[close]").click(function(){
            closepop();
        });
        poplay.find("[inpop]").closePop(function(){
            closepop();
        });
        if(v.closeObj) v.closeObj.closePop(function(){
            closepop();
        });
    },
    //end popup
    //默认弹出框入场函数
    popIn:function(poplay,v){
        poplay.hide();
        poplay.fadeIn(v.fadein);
    },
    //默认弹出框出场函数
    popOut:function(poplay,v){
        poplay.fadeOut(v.fadeout,function(){
            poplay.remove();
        });
        if(v.cover){
            //如果有笼罩，才关闭
            Think.closeCover(v.fadeout);
        }
    },
    //笼罩层
    cover:function(fadein){
        fadein=fadein||0;
        if(!Think.op.havecover){
            lay=$(Think.frag['coverlay']);
            if(Think.isie6()){
                var iframeSrc=/^https/i.test(window.location.href || '') ? 'javascript:false' : 'about:blank';
                var fr=$('<iframe class="blockUI" style="z-index:'+ (Think.op.popupz-2) +';border:none;margin:0;padding:0;position:absolute;width:100%;height:100%;top:0;left:0" src="'+iframeSrc+'"></iframe>');
                fr.css("opacity",0.0);
                fr.appendTo("body");
            }
            lay.hide().css({
                position:"fixed",
                opacity:Think.op.opacity,
                zIndex:Think.op.popupz-1
            }).appendTo("body");
            lay.fadeIn(fadein);
            Think.op.havecover=true;//标记已有笼罩层
            $.extend({
                _closeCover:function(fadeout){
                    lay.fadeOut(fadeout,function(){
                        lay.remove();
                        if(Think.isie6()){
                            fr.remove();
                        }
                    });
                    Think.op.havecover=false;
                }
            });
        }
    },
    //关闭笼罩层
    closeCover:function(fadeout){
        fadeout=fadeout||0;
        if($.isFunction($._closeCover)) $._closeCover(fadeout);
    },
    //笼罩层结束
    //初始化
    init: function(v)
    {
        //可配置,url,callbck
        v=v||{};
        Think.op(v);
        Think.op=$.extend(Think.op,v);
        //所有效果的默认配置，都可以在此配置。
        if (Think.isie6())
        {
            $("body,html").css({
                height:"100%",
                margin:0,
                padding:0
            });
            $("html").css("background-attachment", "fixed").css(
                "background-image", "url(thinkphp)");
        }
        //浮动效果,需要先定义top再定义fiexed

        $.cssHooks.position={
            set:function(elem,value){
                if(Think.isie6() && value=="fixed"){
                    elem.style.position="absolute";
                    elem.style.setExpression("top","documentElement.scrollTop+"+$(elem).position().top);
                }else{
                    return value;
                }
            }
        }
        //载入配置文件
        jQuery.ajax({
            type:'GET',
            url:Think.op.url,
            dataType:'xml',
            timeout: 50000,
            cache: false,
            error: Think.ajaxError,
            success: function(xml){
                $(xml).find("_PAGE_").each(function(){
                    var pageId = $(this).attr("id");
                    if (pageId) Think.frag[pageId] = $(this).text();
                });
                //加载主题包
                Think.include($(xml).find("_THEME_").text()+".css");
                $(xml).find("_MSG_").each(function(){
                    var msgId = $(this).attr("id");
                    if (msgId) Think.msg[msgId] = $(this).text();
                });
                //ajax等待效果。
                ajaxWait=$(Think.frag['ajaxWait']);
                ajaxWait.appendTo("body");
                ajaxWait.setPosition({pos:"02",x:10,y:10});
                ajaxWait.hide();
                $(document).ajaxStart(function(){
                    ajaxWait.show()
                }
                ).ajaxStop(function(){
                    ajaxWait.hide();
                }
                );
                //初始化UI
                $.initUI();
                if (jQuery.isFunction(Think.op.callback)) Think.op.callback();
            }
        });

    }
};


(function($){
    $.fn.extend({
        //初始化UI
        initUI:function(){
            $.initUI(this);
            return $(this);
        },
        //ajax访问
        sendAjax:function(v){
            //参数， goto，confirm，id如果在弹出框中id可以防止重复弹出,pop ,ajaxelem,ajax元素的选择符，默认为form，如果元素在 弹出框中，请在元素中加上 inpop属性
            var v=v||{};
            var $this=$(this);
            var re=false;
            //防止重复
        if($this.data("inajax")||(Think.defined(v.id) && ($("[popid='"+v.id+"']").size()>0 || $("[popid='confirm_"+v.id+"']").size()>0))) return false;
            if($this.isTag("form")){
                //判断validate
                if($.isFunction($this.valid)&&!$this.valid()){
                   Think.alertMsg({
                                        msg:Think.msg['validateError'],
                                        type:0
                                    });	
                return false;
            }
                //判断是否上传
                var fileInputs = $('input:file', this).length > 0;
                var mp = 'multipart/form-data';
                var multipart = ($this.attr('enctype') == mp || $this.attr('encoding') == mp);
                if(fileInputs||multipart){
                    var upload=true;
                    re=true;
                }else{
                    var upload=false;
                }
            }
            var  run=function(){
            	$this.data("inajax",true);//标记当前元素正在执行ajax,防止重复执行
                var   posf=function(xml){
                    Think.C(xml,function(xml){
                        if(Think.defined(v.goto)) Think.scrollTop($(v.goto));//位置移动
                        //判断是否在弹出框中
                        if(Think.defined($this.attr('inpop'))){
                            s = $(xml).find("status").text();
                            if(s!="0"){
                                //不为失败
                                $this.closePop();//关闭弹出框
                            }
                        }
                        //判断是否需要放在弹出框中。
                        if(Think.defined(v.pop)){
                        	if(Think.defined(v.id)) v.pop.id=v.id;
                        	//ajax元素的选择符
                                var ajaxelem=v.ajaxelem||"form";
                                var content="";
                                $(xml).find("frag").each(function(){
                                    content=content+$(this).text();
                                });
                            v.pop.content=$("<div>"+content+"</div>").find(ajaxelem).attr("inpop","1").end().html();
                            Think.popup(v.pop);
                        }
                        //删除标记
                        $this.removeData("inajax");
                    });
                };
                if($this.isTag("form")){
                    //表单处理
                    if(upload){
                        //iframe提交
                        Think.I({
                            form:$this,
                            callback:posf
                        });

                    }else{
                        //ajax提交,提交地址需要返回success或者error

                        Think.A({
                            form:$this,
                            callback:posf
                        });


                    }
                }else{
                    //其他元素处理,需要href属性
                    Think.A({
                        url:$this.attr('href'),
                        callback:posf
                    });
                }
            };
            //end run
            if(Think.defined(v.confirm)){
            	//todu 判断是否重复。
                Think.alertMsg({
                    msg:v.confirm,
                    type:2,
                    callback:run,
                    id:v.id
                });
            }else{
                run();
            }
            return re;


        },
        //拖动,选择控制层，传递拖动层。
        drag:function(obj){
            var draglay=obj||$(this);
            $(this).mousedown(function(e){
                var	y=e.pageY-draglay.offset().top;
                var	x=e.pageX-draglay.offset().left;
                $(document).mousemove(function(e){
                    $(draglay).offset({
                        top:e.pageY-y,
                        left:e.pageX-x
                    });
                });
                $(document).mouseup(function(){
                    $(document).unbind("mouseup");
                    $(document).unbind("mousemove");
                });
            });
        },
        //设置位置
        setPosition:function(v){
            var defaults={
                pos:"00",
                x:0,
                y:0,
                base:null
            };
            v=$.extend(defaults,v);
            //base为0的情况
            var $this=$(this);
            var baseX,baseY;
            var basePoint = parseInt(v.pos.substr(0, 1));
            var direction = parseInt(v.pos.substr(1, 1));
            var cw=$this.outerWidth(true);
            var ch=$this.outerHeight(true);
            //basePoint为0时，浮动位置
            if(basePoint==0){
                switch(direction){
                    case 0://居中
                        $this.css({
                            top:(($(window).height()-ch)/2+v.x)+"px",
                            left:(($(window).width()-cw)/2+v.y)+"px",
                            position:"fixed"
                        });
                        break;
                    case 1:
                        $this.css({
                            top:v.x,
                            left:v.y,
                            position:"fixed"
                        })
                        break;
                    case 2:
                        $this.css({
                            top:v.x,
                            right:v.y,
                            position:"fixed"
                        })
                        break;
                    case 3:
                        $this.css({
                            bottom:v.x,
                            right:v.y,
                            position:"fixed"
                        })
                        break;
                    case 4:
                        $this.css({
                            bottom:v.x,
                            left:v.y,
                            position:"fixed"
                        })
                        break;
                }
                return;
            }

            //basePoint不为0时
            //计算基点坐标
            if(basePoint!=5){
                var baseobj=v.base;
                var bh=baseobj.outerHeight();
                var bw=baseobj.outerWidth();
                var bt=baseobj.offset().top;
                var bl=baseobj.offset().left;
            }else{
                var bh=0;
                var bw=0;
            }
            
            switch(basePoint){
                case 1:
                    baseX=bl;
                    baseY=bt;
                    break;
                case 2:
                    baseX=bl+bw;
                    baseY=bt;
                    break;
                case 3:
                    baseX=bl+bw;
                    baseY=bt+bh;
                    break;
                case 4:
                    baseX=bl;
                    baseY=bt+bh;
                    break;
                case 5:
                    //相对应鼠标
                        baseX=Think.op.popX
                        baseY=Think.op.popY
            }
        //计算弹出层坐标
var x,y;
                switch(direction){
                    case 1:
                        x=baseX-cw-v.x;
                        y=baseY-ch-v.y;
                        break;
                    case 2:
                        x=baseX+v.x;
                        y=baseY-ch-v.y;
                        break;
                    case 3:
                        x=baseX+v.x;
                        y=baseY+v.y;
                        break;
                    case 4:
                        x=baseX-cw-v.x;
                        y=baseY+v.y;
                        break;
                }
                var important = v.pos.indexOf('!') != -1 ? 1 : 0;
                if(important){
                    //自动调整位置
                    if($.inArray(direction,[1,4])!=-1&&x<$(window).scrollLeft()){
                        x=baseX+v.x;
                        if($.inArray(basePoint,[1,4])!=-1) x+=bw;
                    }else if(x+cw>$(window).scrollLeft()+$(window).width() && baseX>=cw){
                        x=baseX-cw-v.x;
                        if($.inArray(basePoint,[2,3])!=-1) x-=bw;
                    }
                    if($.inArray(direction,[1,2])!=-1 && y<$(window).scrollTop()){
                        y=baseY+v.y;
                        if($.inArray(basePoint,[1,2])!=-1) y+=bh;
                    }else if(y+ch>$(window).scrollTop()+$(window).height() && baseY>=ch){
                        y=baseY-ch-v.y;
                        if($.inArray(basePoint,[4,3])!=-1) y-=bh;
                    }

                }
                //定位
                $this.offset({
                    top:y,
                    left:x
                });

        },
        //closePop事件
        closePop:function(fn){
            if(Think.defined(fn)){
                $(this).bind("closePop",fn);
            }else{
                $(this).trigger("closePop");
            }
        },
        //判断是否为某标签
        isTag:function(tn){
            if(!tn) return false;
            return $(this)[0].tagName.toLowerCase() == tn?true:false;
        }
    });

    $.extend({
     //调用函数，函数名可以为变量可以为字符串，参数可以是数组形式和字符串形式，还可以是对象形象{}
    F: function(func, args)
    {
        args=args||'';
        if ($.type(args) == "string")
        {
            args = args.split(",")
        }
        var argc = args.length, s = '',fun=$.type(func)=="string"?eval(func):func;
        if($.type(args)=="array"){
            for (i = 0; i < argc; i++)
            {
                s += ',args[' + i + ']';
            }
        }else{
            s=args;
        }

        if($.type(s)=="string"){
            fun(eval(s.substr(1)));
        }else{
            //支持对象参数
            fun(s);
        }
    }
    ,
        //初始化ui
        initUI:function(_box){
            var $p = $(_box || document);
            //validate
            if($("form[validate]", $p).size()>0){
                var run=function(){
                    $("form[validate]", $p).each(function(){
                        $(this).validate({
                            focusInvalid: false,
                            focusCleanup: true,
                            errorElement: "span",
                            ignore:".ignore",
                            invalidHandler: function(form, validator) {
                                var errors = validator.numberOfInvalids();
                                if (errors) {
                                	//错误提示
                                }
                            }
                        });
                    });
                };
                Think.include(["validate/jquery.validate.js","validate/messages_cn.js","validate/jquery.metadata.js"]);
                var check=function(){
                    if(Think.loadUrl['validate/jquery.validate.js']&&Think.loadUrl['validate/messages_cn.js']&&Think.loadUrl['validate/jquery.metadata.js']){
                        run();
                    }else{
                        setTimeout(check,50);
                    }
                };

                check();
            }
            //end validate
            //uploadify
            if($("[upload]", $p).size()>0){
                var run2=function(){
                    $("[upload]", $p).each(function(){
                        if(eval($(this).attr("upload"))){
                            op=eval($(this).attr("upload"));
                            arg=$.extend({},Think.op.ext.uploadify,op);
                        }else{
                            arg=Think.op.ext.uploadify;
                        }
                        $(this).uploadify(arg);
                    });
                };
                Think.include(["uploadify/swfobject.js","uploadify/jquery.uploadify.js","uploadify.css"]);
                check2=function(){
                    if(Think.loadUrl['uploadify/swfobject.js']&&Think.loadUrl['uploadify/jquery.uploadify.js']&&Think.loadUrl['uploadify.css']){
                        run2();
                    }else{
                        setTimeout(check2,50);
                    }
                };
                check2();
            }

            //end uploadify
            //imgthumb
            if($("img[thumb]",$p).size()>0){
                var run3=function(){
                    $("img[thumb]",$p).each(function(){
                        $(this).imgthumb({
                            width:parseInt($(this).attr("width")),
                            height:parseInt($(this).attr("height")),
                            loadimg:Think.op.jsdir+"imgthumb/loading.gif"
                        });
                    });
                };
                Think.include("imgthumb/jquery.imgthumb.js");
                check3=function(){
                    if(Think.loadUrl['imgthumb/jquery.imgthumb.js']){
                        run3();
                    }else{
                        setTimeout(check3,50);
                    }
                };
                check3();
            }
            //end imgthumb
            //locationSelect
            //属性:location,label,select
            if($("[location]",$p).size()>0){
                var run4=function(){
                    locArr=[];
                    $("[location]",$p).each(function(){
                        name=$(this).attr("location");
                        //判断当前值是否已经处理
                        if($.inArray(name,locArr)==-1){
                            //获取labels
                            var labs=[];
                            var selects=[];
                            $("[location='"+name+"']").each(function(){
                                labs.push($(this).attr('label'));
                                //获得默认值
                                select=$(this).attr("select");
                                if(Think.defined(select)){
                                    selects.push(select);
                                }
                            });
                            $("[location='"+name+"']").hide();
                            if(selects.length>0){
                                //默认值处理
                                var selectBack=function(){
                                    $.LocationSelect.all[name].select(selects);
                                }
                            }else{
                                var selectBack=com.elfvision.kit.LocationSelect.GeoDetec;

                            }
                            $("[location='"+name+"']").LocationSelect({
                                dataUrl:Think.op.jsdir+"locationSelect/areas_1.0.json",
                                name:name,
                                labels :labs,
                                detector:selectBack
                            });
                        }
                        locArr.push(name);
                    });
                };
                Think.include(["locationSelect/json2.js","locationSelect/LocationSelect.js"]);
                var check4=function(){
                    if(Think.loadUrl['locationSelect/json2.js']&& Think.loadUrl['locationSelect/LocationSelect.js']){
                        run4();
                    }else{
                        setTimeout(check4,50);
                    }
                };
                check4();
            }
            //end locationSelect
            //calendar
            //可以 jQuery.fn.simpleDatepicker.formatOutput定义输出格式，jQuery.fn.simpleDatepicker.defaults定义默认值。
            if($("input[cal]",$p).size()>0){
                var run5=function(){
                    $("input[cal]",$p).simpleDatepicker();
                };
                Think.include(["cal.js","calendar.css"]);
                var check5=function(){
                    if(Think.loadUrl['cal.js'] && Think.loadUrl['calendar.css']){
                        run5();
                    }else{
                        setTimeout(check5,50);
                    }
                };
                check5();
            }
            //end calendar
            //监听扩展
            if($.isFunction(Think.op.listen)) Think.op.listen($p);
        }
    //end initUI

    });

})(jQuery);