Bitauto.UserCars = {
    url: 'http://api.baa.bitauto.com/ibt/ViewedCarsHandler.ashx',                                       //公有属性：用户请求Handler地址
    relation: { "collectcar": 1, "plancar": 2, "owncar": 3, "viewedcar": 4 },                           //公有属性: 用户车关系
    owncar: { "arrowncar": [], "message": "" },                                                          //公有属性：用户拥有车对象
    plancar: { "arrplancar": [], "message": "" },                                                        //公有属性：用户计划购买车对象
    collectcar: { "arrplancar": [], "message": "" },                                                     //公有属性：用户收藏车对象
    viewedcar: { "showNum": 6, "arrviewedcar": [], "currentDomain": document.domain },                  //公有属性：浏览车的私有属性

    initViewedCars: function () {
        var arr = document.cookie.match(new RegExp("(^| )csids=([^;]*)(;|$)"));

        if (arr != null) {
            this.viewedcar.arrviewedcar = decodeURIComponent(arr[2]).split("_");
        }
        var domainArray = document.domain.split('.');
        this.viewedcar.currentDomain = domainArray[domainArray.length - 2] + "." + domainArray[domainArray.length - 1];
    }, //公有方法：添加用户拥有的车
    addOwnCar: function (cs_id, bsic_id, cityId, buyTime, callBackFun) {
        this.addUserCar(this.relation.owncar, cs_id, callBackFun, bsic_id, cityId, buyTime);
    }, //公有方法：添加用户计划购买的车
    addPlanCar: function (cs_id, callBackFun) {
        this.addUserCar(this.relation.plancar, cs_id, callBackFun);
    }, //公有方法：添加用户计划购买的车
    addCollectCar: function (cs_id, callBackFun) {
        this.addUserCar(this.relation.collectcar, cs_id, callBackFun);
    }, //公有方法：添加用户浏览的车
    addViewedCars: function (cs_id) {
        this.addUserCar(this.relation.viewedcar, cs_id);
    }, //公有方法：获取用户浏览车型
    getViewedCars: function (num, callBackFun) {
        this.getUserCar(this.relation.viewedcar, callBackFun, num);
    }, //公有方法：获取用户拥有车型
    getOwnCar: function (callBackFun) {
        this.getUserCar(this.relation.owncar, callBackFun);
    }, //公有方法：获取用户计划购买车型
    getPlanCar: function (callBackFun) {
        this.getUserCar(this.relation.plancar, callBackFun);
    }, //公有方法：获取用户收藏车型
    getCollectCar: function (callBackFun) {
        this.getUserCar(this.relation.collectcar, callBackFun);
    },
    setCookie: function (content, expires, domain) {
        document.cookie = "csids=" + encodeURIComponent(content) + "; path=" + "/" + ";expires=" + expires + ";domain=" + domain;
    },
    notLoginAddUserCarHandler: function () {
        location.href = "http://i.qichetong.com/authenservice/login.aspx?returnurl=" + location.href;
    },
    notLoginGetUserCarHandler: function () {
        location.href = "http://i.qichetong.com/authenservice/login.aspx?returnurl=" + location.href;
    },
    //私有方法：根据用户和车的关系，添加车信息
    addUserCar: function (relation, cs_id, callBackFun, bsic_id, cityId, buyTime) {
        var src = "";
        if (relation == this.relation.viewedcar) {
            var Days = 90; //此 cookie 将被保存 90 天
            var exp = new Date();
            exp.setTime(exp.getTime() + Days * 24 * 60 * 60 * 1000);

            if (this.viewedcar.arrviewedcar.length > 0) {
                //是否已经存在请求添加的车型
                var inArray = false;
                for (var i = 0; i < this.viewedcar.arrviewedcar.length; i++) {
                    if (this.viewedcar.arrviewedcar[i] == cs_id) {
                        this.viewedcar.arrviewedcar.splice(i, 1);
                        inArray = true;
                    }
                }

                //当用户浏览车型总数小于6,直接添加车型，否则，删除最早记录的车型，并添加当前浏览的车型
                if (this.viewedcar.arrviewedcar.length < this.viewedcar.showNum) {
                    this.viewedcar.arrviewedcar.unshift(cs_id);
                    this.setCookie(this.viewedcar.arrviewedcar.join("_"), exp.toGMTString(), this.viewedcar.currentDomain);
                }
                else {
                    this.viewedcar.arrviewedcar.pop();
                    this.viewedcar.arrviewedcar.unshift(cs_id);
                    this.setCookie(this.viewedcar.arrviewedcar.join("_"), exp.toString(), this.viewedcar.currentDomain);
                }
                //判断用户是否登录，如果登录，handler处理
                if (this.isLogin()) {
                    src = this.url + "?m=" + Math.random() + "&r=" + this.relation.viewedcar + "&o=w" + "&ids=" + encodeURIComponent(this.viewedcar.arrviewedcar.join("_"));
                    this.persistUserCarsHandler(src);
                }
            }
            else {
                this.setCookie(cs_id, exp.toString(), this.viewedcar.currentDomain);
                this.viewedcar.arrviewedcar.unshift(cs_id);
                //判断用户是否登录，如果登录，handler处理
                if (this.isLogin()) {
                    src = this.url + "?m=" + Math.random() + "&r=" + this.relation.viewedcar + "&o=w" + "&ids=" + encodeURIComponent(cs_id);
                    this.persistUserCarsHandler(src);
                }
            }
        }
        else {
            if (this.isLogin()) {
                src = this.url + "?m=" + Math.random() + "&r=" + relation + "&o=w" + "&ids=" + encodeURIComponent(cs_id);

                if (relation == this.relation.owncar) {
                    src += "&bsicid=" + bsic_id + "&cityId=" + cityId + "&buyTime=" + buyTime;
                }

                this.persistUserCarsHandler(src, relation, callBackFun);
            }
            else {
                this.notLoginAddUserCarHandler();
            }
        }
    }, //私有方法：根据relation获取用户车型
    getUserCar: function (relation, callBackFun, num) {
        var src = this.url + "?m=" + Math.random() + "&r=" + relation + "&o=r";
        if (relation == this.relation.viewedcar) {
            if (this.isLogin()) {
                this.persistUserCarsHandler(src, this.relation.viewedcar, callBackFun, num);
            } else {
                if (this.viewedcar.arrviewedcar.length > 0) {
                    if (parseInt(num) < this.viewedcar.arrviewedcar.length) {
                        var delNum = this.viewedcar.arrviewedcar.length - parseInt(num);

                        for (var i = 0; i < delNum; i++) {
                            this.viewedcar.arrviewedcar.pop();
                        }
                    }
                } 
                if (callBackFun) callBackFun();
            }

        }
        else {
            if (this.isLogin()) {
                this.persistUserCarsHandler(src, relation, callBackFun);
            }
            else {
                this.notLoginGetUserCarHandler();
            }
        }
    },
    isLogin: function () {
        if (Bitauto.Login.result.isLogined) {
            return true;
        }
        return false;
    },
    persistUserCarsHandler: function (src, relation, callBackFun, num) {
        var jsActive = document.createElement("script");
        jsActive.setAttribute("charset", 'utf-8');
        jsActive.setAttribute("src", src);
        jsActive.setAttribute("type", "text/javascript");
        jsActive.onloadDone = false;
        jsActive.onload = function () {
            jsActive.onloadDone = true;

            if (relation == Bitauto.UserCars.relation.viewedcar) {
                if (parseInt(num) < Bitauto.UserCars.viewedcar.arrviewedcar.length) {
                    var delNum = Bitauto.UserCars.viewedcar.arrviewedcar.length - parseInt(num);
                    for (var i = 0; i < delNum; i++) {
                        Bitauto.UserCars.viewedcar.arrviewedcar.pop();
                    }
                }
            }
            if (callBackFun) { callBackFun(); }
        }
        jsActive.onreadystatechange = function () {
            if (("loaded" === jsActive.readyState || "complete" === jsActive.readyState) && !jsActive.onloadDone) {
                jsActive.onloadDone = true;
                if (relation == Bitauto.UserCars.relation.viewedcar) {
                    if (parseInt(num) < Bitauto.UserCars.viewedcar.arrviewedcar.length) {
                        var delNum = Bitauto.UserCars.viewedcar.arrviewedcar.length - parseInt(num);
                        for (var i = 0; i < delNum; i++) {
                            Bitauto.UserCars.viewedcari.arrviewedcar.pop();
                        }
                    }
                }
                if (callBackFun) { callBackFun(); }
            }
        }
        document.body.appendChild(jsActive);
    }
}
Bitauto.UserCars.initViewedCars(); //获取用户访问过的车型