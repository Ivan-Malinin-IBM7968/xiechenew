Bitauto.ViewNewsCarSerial = (function (jQuery) {
    var planCar, ownCar; //依次是关注的车，我的爱车
    var orderCarId = ""; //当前添加的车型的顺序
    //依次是 是否已初始化，是否已被回调，推荐是否已统计，爱车是否已统计
    var IsInit = 0, IsCalled = 0, IsRecStat = 0, IsFavStat = 0;
    //根据（得到的CsIds与CityId）或（编辑推荐的）获取车型具体信息
    function GetReCarSerialInfo(csIds, cityId) {
        if (Bitauto.UserCars.isLogin()) {
            Bitauto.UserCars.getPlanCar(function () {
                Bitauto.UserCars.getOwnCar(function () {
                    if (IsCalled == 0) {
                        planCar = Bitauto.UserCars.plancar.arrplancar; //关注的车 
                        ownCar = Bitauto.UserCars.owncar.arrowncar; //我的爱车
                        GetCarSerialInfoLoad(csIds, cityId);
                        IsCalled++;
                    }
                });
            });
        }
        else
            GetCarSerialInfoLoad(csIds, cityId);
    };
    //加载编辑推荐的车型具体信息
    function LoadDefaultCars(csIds, cityId) {
        var newCsIds = "";
        var defaultCsIds = csIds.split(",");
        for (i = 0; i < 6; i++) {
            newCsIds += defaultCsIds[i] + ",";
        }
        $.ajax({
            url: "http://api.car.bitauto.com/CarInfo/SerialJsonInfo.aspx?csids=" + newCsIds + "&cityid=" + cityId,
            cache: true,
            dataType: "jsonp",
            jsonpCallback: "callback",
            success: function (data) {
                if (IsInit == 1)
                    InitChooseCar();
                var serialHtml = '<div id="car_random_box" class="car_random_box">';
                serialHtml += GetSerialHtml(data, planCar, ownCar, 0, true);
                serialHtml += '<div class="clear"></div></div>';
                serialHtml += '<div class="car_random_a car_random_al" style="display:none;"><a href="#"></a></div>';
                serialHtml += '<div class="car_random_a car_random_ar" style="display:none;"><a href="#"></a></div>';
                jQuery('#chooseCar').html(serialHtml);
                ShowRoll(false);
            }
        });
    }
    //获取初始化加载车型的信息
    function GetCarSerialInfoLoad(csIds, cityId) {
        Bitauto.UserCars.getViewedCars(6, function () {
            var csIdsArry = Bitauto.UserCars.viewedcar.arrviewedcar;
            //浏览与竞品车型信息csIdsArry.length > 0
            if (csIdsArry.length > 0) {
                GetReCoCarSerialIds(csIdsArry, cityId);
            }
            //编辑推荐车型信息
            else {
                LoadDefaultCars(csIds, cityId);
            }
        });
    };
    //获取浏览与竞品车型信息前的分配方式
    function GetReCoCarSerialIds(csIdsArry, cityId) {
        var reViewCsIds = "";
        if (csIdsArry.length >= 3) {
            reViewCsIds = csIdsArry[0] + "," + csIdsArry[1] + "," + csIdsArry[2];
            GetReCoCarSerialInfo(reViewCsIds, 3, 1, cityId);
        }
        if (csIdsArry.length == 2) {
            reViewCsIds = csIdsArry[0] + "," + csIdsArry[1];
            GetReCoCarSerialInfo(reViewCsIds, 2, 2, cityId)
        }
        if (csIdsArry.length == 1) {
            reViewCsIds = csIdsArry[0].toString();
            GetReCoCarSerialInfo(reViewCsIds, 1, 5, cityId)
        }
    };
    //根据分配方式获取浏览与竞品车型信息
    function GetReCoCarSerialInfo(reViewCsIds, numLook, numCom, cityId) {
        var reCsIdArry = reViewCsIds.split(',');
        var reCoCsIds = "";
        for (i = 0; i < numLook; i++) {
            var tempCom = numCom;
            if (reCoCsIds.indexOf(reCsIdArry[i]) < 0) {
                reCoCsIds += reCsIdArry[i] + ",";
                //添加投放广告的车型
                if (adCsIds.length > 0) {
                    for (var adNum = 0; adNum < adCsIds.length; adNum++) {
                        var s = Date.parse(adCsIds[adNum].SDate);
                        var e = Date.parse(adCsIds[adNum].EDate);
                        var now = Date.parse(new Date());
                        if (now >= s && now < e) {
                            if (reCsIdArry[i] == adCsIds[adNum].SerId) {
                                //去重
                                if (reCoCsIds.indexOf(adCsIds[adNum].AdSerId) < 0) {
                                    reCoCsIds += adCsIds[adNum].AdSerId + ","
                                    tempCom--;
                                }
                            }
                        }
                    }
                }
                var tempReCsId = SerialCompareJson[reCsIdArry[i]];
                if (tempReCsId) {
                    if (tempReCsId.length > 0) {
                        var n = 0;
                        for (var j = 0; j < tempReCsId.length; j++) {
                            if (n == tempCom) {
                                break;
                            }
                            //去重
                            if (reCoCsIds.indexOf(tempReCsId[j]) < 0 && reViewCsIds.indexOf(tempReCsId[j]) < 0) {
                                reCoCsIds += tempReCsId[j] + ",";
                                n++;
                            }
                        }
                    }
                }
            }
        }

        var tempOldCsId = reCsIds.split(',');
        var tempCoCsId = reCoCsIds.split(',');
        if (tempCoCsId.length < 7) {
            //补编辑推荐车型
            for (i = 0; i < tempOldCsId.length; i++) {
                //去重
                if (reCoCsIds.indexOf(tempOldCsId[i]) < 0) {
                    reCoCsIds += tempOldCsId[i] + ',';
                }
                if (reCoCsIds.split(',').length > 6)
                    break;
            }
        }
        $.ajax({
            url: "http://api.car.bitauto.com/CarInfo/SerialJsonInfo.aspx?csids=" + reCoCsIds + "&cityid=" + cityId,
            cache: true,
            dataType: "jsonp",
            jsonpCallback: "callback",
            success: function (data) {
                if (IsInit == 1) {
                    InitChooseCar();
                    jQuery('#re_garage_info_hover').html("<a href='JavaScript:Bitauto.ViewNewsCarSerial.RecommendCarSerialInfo()'>您看过的车及竞品推荐</a>");
                }
                var serialHtml = '<div id="car_random_box" class="car_random_box">';
                serialHtml += GetSerialHtml(data, planCar, ownCar, 0, false);
                serialHtml += '<div class="clear"></div></div>';
                serialHtml += '<div class="car_random_a car_random_al" style="display:none;"><a href="#"></a></div>';
                serialHtml += '<div class="car_random_a car_random_ar" style="display:none;"><a href="#"></a></div>';
                jQuery('#chooseCar').html(serialHtml);
                ShowRoll(false);
            }
        });
    };
    //根据获取的CsIds和CityId组合成所需的HTML
    function GetSerialHtml(data, planCar, ownCar, isFav, isTuiJian) {
        var csIdsArry = Bitauto.UserCars.viewedcar.arrviewedcar;
        var csIdsArrys = "";
        if (csIdsArry.length > 0) {
            for (i = 0; i < csIdsArry.length; i++) {
                csIdsArrys += csIdsArry + ",";
            }
        }
        var logType = 0, locUrl = document.location, refUrl = document.referrer, params = "";
        var commenUrl = "http://log.bitauto.com/IndexStat/ClickLogStat.aspx?refUrl=" + refUrl;
        var position = 0;
        var serialHtml = "";
        for (var i = 0; i < data.length; i++) {
            if (i > 5)
                break;
            position++;
            serialHtml += '<div class="car_random">';
            var serialUrl = "http://car.bitauto.com/" + unescape(data[i].CsAllSpell) + "/";
            serialHtml += '<a target="_blank" href="' + commenUrl + "&serialId=" + data[i].CsID + "&position=" + position + "&linkType=2&toUrl=" + serialUrl;
            serialHtml += '" onfocus="this.blur();"><img width="210px" height="140px" src="' + unescape(data[i].CsImg) + '" /></a>';
            serialHtml += '<div class="car_random_t">';
            serialHtml += '<h4 class="car_ranname">';
            var csShowName = unescape(data[i].CsShowName);
            var rgex = /[\u0000-\u00ff]/g;
            var strLength = csShowName.length + csShowName.replace(rgex, "").length;
            if (strLength > 14) {
                csShowName = csShowName.replace("(进口)", "").replace("(海外)", "");
                strLength = csShowName.length + csShowName.replace(rgex, "").length;
                if (strLength > 14) {
                    var getLength = 0, len = 0;
                    for (n = 0; n < csShowName.length; n++) {
                        var c = csShowName.charAt(i);
                        if (rgex.test(c)) {
                            getLength++;

                        }
                        else {
                            getLength += 2;
                        }
                        len++;
                        if (getLength >= 14)
                            break;
                    }
                    csShowName = csShowName.substring(0, len);
                }
            }
            var txtUrl = commenUrl + "&serialId=" + data[i].CsID + "&position=" + position + "&linkType=1&toUrl=";
            serialHtml += '<a target="_blank" href="' + txtUrl + serialUrl + '">' + csShowName + '</a></h4>';
            if (unescape(data[i].CsPrice) == "")
                serialHtml += '<em>暂无</em>';
            else
                serialHtml += '<em><a target="_blank" href="' + txtUrl + serialUrl + 'baojia/">' + unescape(data[i].CsPrice) + '</a></em>';
            serialHtml += '</div>';
            serialHtml += '<p><a target="_blank" href="' + txtUrl + unescape(data[i].CsLink.PeiZhi) + '">参数</a> ';
            serialHtml += '| <a target="_blank" href="' + txtUrl + unescape(data[i].CsLink.TuPian) + '">图片</a> ';
            serialHtml += '| <a target="_blank" href="' + txtUrl + serialUrl + 'koubei/">口碑</a> ';
            serialHtml += '| <a target="_blank" href="' + txtUrl + unescape(data[i].CsLink.PingCe) + '">评测</a> ';
            serialHtml += '| <a target="_blank" href="' + txtUrl + unescape(data[i].CsLink.BBS) + '">论坛</a></p>';
            serialHtml += '<span><a target="_blank" href="' + txtUrl + unescape(data[i].CsLink.XunJia) + '">询&nbsp;价</a> ';
            if (unescape(data[i].CsLink.ZhiHuan) != "")
                serialHtml += '<a target="_blank" href="' + txtUrl + unescape(data[i].CsLink.ZhiHuan) + '">置 换</a>';
            else
                serialHtml += '<span class="nolink">置 换</span>';
            var secondUcar = unescape(data[i].CsLink.Ucar).replace("paesf0bxc", "paesf" + cityId + "bxc");
            serialHtml += '<a target="_blank" href="' + txtUrl + secondUcar + '">二手车</a>';

            var gouMai = unescape(data[i].CsLink.GouMai).replace("goumai", "guanzhu");
            if (csIdsArrys.indexOf(data[i].CsID) > -1)
                logType = 4; //用户浏览  
            else
                logType = 5; //竞品 
            //判断是否已登录
            if (Bitauto.UserCars.isLogin()) {
                var isOwn = false, isFouse = false;
                for (j = 0; j < ownCar.length; j++) {
                    if (ownCar[j].csid == data[i].CsID) {
                        isOwn = true;
                        if (isFav == 1)//是否为我的爱车
                            logType = 1;
                        break;
                    }
                }
                for (j = 0; j < planCar.length; j++) {
                    if (planCar[j] == data[i].CsID) {
                        isFouse = true;
                        if (isFav == 1)//是否为我的关注
                            logType = 2;
                        break;
                    }
                }
                if (isOwn) {
                    gouMai = gouMai.substring(0, gouMai.lastIndexOf('car/') + 4);
                    serialHtml += '<a target="_blank" href="' + txtUrl + gouMai + '">我的爱车</a> ';
                }
                else if (isFouse) {
                    gouMai = gouMai.substring(0, gouMai.lastIndexOf('car/') + 4);
                    serialHtml += '<a target="_blank" href="' + txtUrl + gouMai + '">已关注</a> ';
                }
                else
                    serialHtml += '<a target="_blank" href="' + txtUrl + gouMai + '">加关注</a> ';
            }
            else
                serialHtml += '<a target="_blank" href="' + txtUrl + gouMai + '">加关注</a> ';
            serialHtml += '</span>';
            serialHtml += '<h4><a target="_blank" href="' + serialUrl + 'xinwen/">车型资讯</a><span>' + data[i].CsNewsCount + '篇</span></h4>';
            serialHtml += '<div class="text_list"><ul>';
            if (data[i].CsNewsLink.length > 0 && data[i].CsNewsLink[0].FaceTitle != "") {
                serialHtml += '<li><a target="_blank" href="' + unescape(data[i].CsNewsLink[0].Url) + '">' + unescape(data[i].CsNewsLink[0].FaceTitle) + '</a></li>';
            } else {
                serialHtml += '<li>' + cityName + '暂无' + unescape(data[i].CsShowName) + '车型资讯</li>';
            }
            if (data[i].CsNewsLink.length > 1 && data[i].CsNewsLink[1].FaceTitle != "") {
                serialHtml += '<li><a target="_blank" href="' + unescape(data[i].CsNewsLink[1].Url) + '">' + unescape(data[i].CsNewsLink[1].FaceTitle) + '</a></li>';
            }
            if (data[i].CsNewsLink.length > 2 && data[i].CsNewsLink[2].FaceTitle != "") {
                serialHtml += '<li><a target="_blank" href="' + unescape(data[i].CsNewsLink[2].Url) + '">' + unescape(data[i].CsNewsLink[2].FaceTitle) + '</a></li>';
            }
            serialHtml += '</ul></div>';
            serialHtml += '<h4><a target="_blank" href="' + unescape(data[i].CsLink.BBS) + '">网友评车</a><span>' + data[i].CsForumCount + '篇</span></h4>';
            serialHtml += '<div class="text_list  text_list2"><ul>';
            if (data[i].CsForumLink.length > 0 && data[i].CsForumLink[0].Title) {
                serialHtml += '<li><a target="_blank" href="' + unescape(data[i].CsForumLink[0].Url) + '">' + unescape(data[i].CsForumLink[0].Title) + '</a></li>';
            } else {
                serialHtml += '<li>' + cityName + '暂无' + unescape(data[i].CsShowName) + '网友评车</li>';
            }
            if (data[i].CsForumLink.length > 1 && data[i].CsForumLink[1].Title) {
                serialHtml += '<li><a target="_blank" href="' + unescape(data[i].CsForumLink[1].Url) + '">' + unescape(data[i].CsForumLink[1].Title) + '</a></li>';
            }
            serialHtml += '</ul></div>';
            serialHtml += '<h4><a target="_blank" href="' + serialUrl + 'hangqing/' + currentCityEName + '/">行情报价</a><span>' + data[i].CsHangQingCount + '篇</span></h4>';
            serialHtml += '<div class="text_list text_list2"><ul>';
            if (data[i].CsHangQingLink.length > 0 && data[i].CsHangQingLink[0].FaceTitle) {
                serialHtml += '<li><a target="_blank" href="' + unescape(data[i].CsHangQingLink[0].Url) + '">' + unescape(data[i].CsHangQingLink[0].FaceTitle) + '</a></li>';
            } else {
                serialHtml += '<li>' + cityName + '暂无' + unescape(data[i].CsShowName) + '行情报价</li>';
            }
            if (data[i].CsHangQingLink.length > 1 && data[i].CsHangQingLink[1].FaceTitle) {
                serialHtml += '<li><a target="_blank" href="' + unescape(data[i].CsHangQingLink[1].Url) + '">' + unescape(data[i].CsHangQingLink[1].FaceTitle) + '</a></li>';
            }
            serialHtml += '</ul></div></div>';

            if (isTuiJian)
                logType = 0;
            //统计RUL
            params += data[i].CsID + "," + logType + "," + position + "|";
        }
        if (isFav == 1 || IsInit == 1) {
            $.ajax({
                url: "http://log.bitauto.com/IndexStat/IndexLogStat.aspx?locUrl=" + locUrl + "&refUrl=" + refUrl + "&params=" + params,
                async: false,
                dataType: "jsonp"
            });
        }
        return serialHtml;
    };

    //车型信息左右滚动
    function ShowRoll(isCalled) {
        var click_key = true;
        var vlong = 720;
        var nowleft = 0;
        var boxa = "";
        var left_a = "";
        var right_a = "";
        boxa = $("#car_random_box");
        left_a = $(".car_random_al");
        right_a = $(".car_random_ar");

        if ($("#car_random_box div.car_random").size() > 3) {
            right_a.show();
        }
        if (isCalled) {
            boxa.css("left", "-720px");
            nowleft = -720;
            left_a.show();
            right_a.hide();
        }
        left_a.click(function () {
            if (!click_key) return false;
            click_key = false;
            $(this).hide();
            //动画						
            boxa.animate({ left: vlong + nowleft }
					, 500
					, function () {
					    click_key = true;
					    if (boxa.css("left") == "0px") {
					        left_a.hide();
					        right_a.show();
					    }
					    if (boxa.css("left") == "-720px") {
					        right_a.hide();
					        left_a.show();
					    }
					});
            nowleft += vlong;
            return false;
        });
        right_a.click(function () {
            if (!click_key) return false;
            click_key = false;
            $(this).hide();
            //动画
            boxa.animate({ left: -vlong + nowleft }
					, 500
					, function () {
					    click_key = true;
					    if (boxa.css("left") == "0px") {
					        left_a.hide();
					        right_a.show();
					    }
					    if (boxa.css("left") == "-720px") {
					        right_a.hide();
					        left_a.show();
					    }
					});
            nowleft -= vlong;
            return false;
        });
    };
    function InitChooseCar() {
        var mainHtml = '';
        mainHtml += '<div class="col-con car_interest_box">';
        mainHtml += '<div class="line_box car_interest"> ';
        mainHtml += '<div class="car_interest_top">';
        mainHtml += ' <ul>';
        mainHtml += '<li id="re_garage_info_hover" class="current"><a href="JavaScript:Bitauto.ViewNewsCarSerial.RecommendCarSerialInfo()">您看过的车及竞品推荐</a></li>';
        mainHtml += '<li id="garage_info_hover" class="garage">';
        //判断是否已登录  
        var isLogin = Bitauto.UserCars.isLogin();
        if (isLogin)
            mainHtml += '<a href="JavaScript:Bitauto.ViewNewsCarSerial.FavoriteCarSerialInfo(0)" >您的车库</a></li>';
        else
            mainHtml += '<a href="JavaScript:Bitauto.ViewNewsCarSerial.LoginBitAuto()">创建您的车库</a></li>';
        mainHtml += '<li class="tips2"><a target="_blank" href="http://www.bitauto.com/bendixuanche/' + cityEName + '/">' + cityName + '选车 &gt;&gt;</a></li>';
        mainHtml += '</ul>';
        mainHtml += '<div id="topDescripting" class="tips"> 您还可以 <a target="_blank" href="http://car.bitauto.com">按条件选车&gt;&gt;</a></div>';
        mainHtml += '</div>';
        mainHtml += '<!--随机开始-->';
        mainHtml += '<div id="chooseCar"></div>';
        mainHtml += '<!--随机结束--></div></div>';
        jQuery("#divChooseCarForYou").html(mainHtml);
        jQuery('body').append('<div id="overlay" style="display:none; position: fixed; top: 0px; left: 0px; height:' + window.screen.height + 'px; width: 100%;z-index: 10000001; opacity: 0.4;filter:alpha(opacity=40); background-color: #000000;">');
    };

    //创建添加车型 Iframe
    function CreateIframe(iframeId, iframeUrl, marginTop) {
        if (jQuery("#" + iframeId).length == 0) {
            var iframeHtml = "";
            iframeHtml += '<iframe width="500px" scrolling="no" frameborder="0"';
            iframeHtml += ' id="' + iframeId + '"';
            iframeHtml += ' src="' + iframeUrl + '"';
            iframeHtml += ' style="display:none; border: 0px none; overflow: hidden;left:50%; margin-left:-250px; top:50%;';
            iframeHtml += ' margin-top:-' + (marginTop + 40) / 2 + 'px; z-index: 10000002; position: fixed;"></iframe>';
            jQuery('body').append(iframeHtml);
        }
    };
    //显示添加车型 Iframe
    function ShowIframeInfo(divId, iframeId, heigthFF, heigthIE) {
        jQuery('#' + iframeId).css('height', heigthFF);
        if (navigator.userAgent.indexOf("MSIE") > 0) {
            jQuery('#' + iframeId).css('height', heigthIE);
            if (/MSIE/ig.test(navigator.appVersion)) {
                var top = 0;
                jQuery('#' + divId).css('position', 'absolute');
                jQuery('#' + iframeId).css('position', 'absolute');
                if (document.documentElement && document.documentElement.scrollTop) {
                    top = document.documentElement.scrollTop;
                } else if (document.body) {
                    top = document.body.scrollTop;
                }
                jQuery('#' + divId).css('top', top);
                jQuery('#' + iframeId).css('top', top + (window.screen.height - 404 + 180) / 2);
                jQuery('body').css('overflow', 'hidden');
                jQuery('html').css('overflow', 'hidden');
            }
        }
        jQuery('#' + divId).show();
        jQuery('#' + iframeId).show();
    };
    //关闭蒙板
    function CloseIframe(divId, iframeId) {
        if (navigator.userAgent.indexOf("MSIE") > 0) {
            if (/MSIE/ig.test(navigator.appVersion)) {
                jQuery('body').css('overflow', 'auto');
                jQuery('html').css('overflow', 'auto');
            }
        }
        jQuery('#' + divId).hide();
        jQuery('#' + iframeId).hide();
    };
    //回调登录后初始化的数据信息
    function CallBackLoginInit() {
        //回调汽车生活
        //Bitauto.carLife.getCarLifeHtml();
        //回调汽车社区 
        bitLoadScript("http://js.inc.baa.bitautotech.com/bitautoforum/userforum.js", null, "utf-8");
    };
    return {
        //初始化最出页面
        CarSerialInfoInit: function () {
            ShowRoll(false);
            IsInit = 1;
            GetReCarSerialInfo(reCsIds, cityId);
            Bitauto.Login.onlogined = function () {
                IsInit = 1;
                GetReCarSerialInfo(reCsIds, cityId);
                //回调登录后初始化的数据信息
                CallBackLoginInit();
            };
        },
        //为您推荐
        RecommendCarSerialInfo: function () {
            IsInit = 0; IsCalled = 0;
            GetReCarSerialInfo(reCsIds, cityId);
            jQuery("#topDescripting").html('您还可以 <a target="_blank" href="http://car.bitauto.com">按条件选车&gt;&gt;</a>');
            jQuery("#re_garage_info_hover").removeClass().addClass("current");
            jQuery("#garage_info_hover").removeClass().addClass("garage");
        },
        //拥有的车
        FavoriteCarSerialInfo: function (callBackCsId) {
            IsInit = 0; IsFavStat++;
            var csIds = "";
            for (i = 0; i < ownCar.length; i++)
                csIds += ownCar[i].csid + ","; //我的爱车
            if (planCar.length > 0)
                csIds += planCar + ","; //关注的车            
            if (callBackCsId > 0) {
                if (orderCarId != "") {
                    orderCarId = orderCarId.replace(callBackCsId, "") + "," + callBackCsId;
                    csIds = orderCarId;
                }
                else {
                    csIds = csIds.replace(callBackCsId, "") + callBackCsId;
                    orderCarId = csIds;
                }
            }
            var serialHtml = '<div id="car_random_box" class="car_random_box">';
            if (csIds.length > 0) {
                $.ajax({
                    url: "http://api.car.bitauto.com/CarInfo/SerialJsonInfo.aspx?csids=" + csIds + "&cityid=" + cityId,
                    cache: true,
                    dataType: "jsonp",
                    jsonpCallback: "callback",
                    success: function (data) {
                        serialHtml += GetSerialHtml(data, planCar, ownCar, IsFavStat, false);
                        if (data.length < 6) {
                            serialHtml += '<div class="car_random car_random_add">';
                            if (ownCar.length < 3) {
                                serialHtml += '<a href="JavaScript:Bitauto.ViewNewsCarSerial.AddMyCar()">+ 添加我的爱车</a>';
                                CreateIframe('PopPageBoxOwnCar', 'http://i.bitauto.com/Ajax/carbarn/addowncarforindex.aspx?callback=Bitauto.ViewNewsCarSerial.CalledFavoriteCarSerial&popclose=Bitauto.ViewNewsCarSerial.CloseOwnCarIframe&iframeid=PopPageBoxOwnCar', 404);
                            }
                            serialHtml += '<a href="JavaScript:Bitauto.ViewNewsCarSerial.AddFocusCar()">+ 添加我关注的车</a></div>';
                            CreateIframe('PopPageBoxFocusCar', 'http://i.bitauto.com/ajax/carbarn/addconcernedcarforindex.aspx?callback=Bitauto.ViewNewsCarSerial.CalledFavoriteCarSerial&popclose=Bitauto.ViewNewsCarSerial.CloseFocusCarIframe&iframeid=PopPageBoxFocusCar', 264);
                        }
                        serialHtml += '<div class="clear"></div></div>';
                        serialHtml += '<div class="car_random_a car_random_al" style="display:none;"><a href="#"></a></div>';
                        serialHtml += '<div class="car_random_a car_random_ar" style="display:none;"><a href="#"></a></div>';
                        jQuery('#chooseCar').html(serialHtml);

                        if (callBackCsId > 0 && data.length > 3)
                            ShowRoll(true);
                        else
                            ShowRoll(false);
                    }
                });
            }
            else {
                serialHtml += '<div class="car_random car_random_add">';
                serialHtml += '<a href="JavaScript:Bitauto.ViewNewsCarSerial.AddMyCar()">+ 添加我的爱车</a>';
                serialHtml += '<a href="JavaScript:Bitauto.ViewNewsCarSerial.AddFocusCar()">+ 添加我关注的车</a></div>';
                serialHtml += '<div class="clear"></div></div>';
                serialHtml += '<div class="car_random_a car_random_al" style="display:none;"><a href="#"></a></div>';
                serialHtml += '<div class="car_random_a car_random_ar" style="display:none;"><a href="#"></a></div>';
                jQuery('#chooseCar').html(serialHtml);
                ShowRoll(false);
            }
            jQuery("#topDescripting").html('这是您的爱车和关注的车，您可以在 <a target="_blank" href="http://i.bitauto.com/u10000/car">我的车库</a> 修改');
            jQuery("#garage_info_hover").removeClass().addClass("current");
            jQuery("#re_garage_info_hover").removeClass().addClass("garage");
        },
        //显示登录页面
        LoginBitAuto: function () {
            var serialHtml = '<iframe src="http://i.bitauto.com/authenservice/index/login.aspx?callback=Bitauto.ViewNewsCarSerial.CalledFavoriteCarSerial" frameborder="0" class="baa_login_iframe"></iframe>';
            jQuery('#chooseCar').html(serialHtml);
            jQuery("#topDescripting").html('');
            jQuery("#garage_info_hover").removeClass().addClass("current");
            jQuery("#re_garage_info_hover").removeClass().addClass("garage");
        },
        //登录后页面加载 
        CalledFavoriteCarSerial: function (callResult) {
            //回调登录后初始化的数据信息 
            if (Bitauto.UserCars.isLogin()) {
                Bitauto.UserCars.getPlanCar(function () {
                    Bitauto.UserCars.getOwnCar(function () {
                        planCar = Bitauto.UserCars.plancar.arrplancar; //关注的车 
                        ownCar = Bitauto.UserCars.owncar.arrowncar; //我的爱车
                        if (callResult)
                            Bitauto.ViewNewsCarSerial.FavoriteCarSerialInfo(callResult.car.serialId);
                        else
                            Bitauto.ViewNewsCarSerial.FavoriteCarSerialInfo(0);
                        //添加的ID
                        jQuery('#garage_info_hover').html('<a href="JavaScript:Bitauto.ViewNewsCarSerial.FavoriteCarSerialInfo(0)" >您的车库</a></li>');
                        Bitauto.ViewNewsCarSerial.CloseOwnCarIframe();
                        Bitauto.ViewNewsCarSerial.CloseFocusCarIframe();
                        CallBackLoginInit();
                    });
                });
            }
        },
        //显示添加爱车
        AddMyCar: function () {
            ShowIframeInfo('overlay', 'PopPageBoxOwnCar', 406, 427);
        },
        //显示添加关注的车
        AddFocusCar: function () {
            ShowIframeInfo('overlay', 'PopPageBoxFocusCar', 264, 273);
        },
        //关闭我的爱车蒙板
        CloseOwnCarIframe: function () {
            CloseIframe('overlay', 'PopPageBoxOwnCar');
        },
        //关闭关注的车蒙板
        CloseFocusCarIframe: function () {
            CloseIframe('overlay', 'PopPageBoxFocusCar');
        }
    }
})(jQuery);
Bitauto.ViewNewsCarSerial.CarSerialInfoInit();