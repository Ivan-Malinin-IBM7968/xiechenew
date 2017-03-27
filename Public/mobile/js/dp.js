var DP = window.DP || {};
DP.namespace = function () {
    var b = arguments,
        c = null,
        a, d, e;
    for (a = 0; a < b.length; ++a) {
        e = b[a].split(".");
        c = DP;
        for (d = "DP" === e[0] ? 1 : 0; d < e.length; ++d) c[e[d]] = c[e[d]] || {}, c = c[e[d]]
    }
    return c
};
DP.namespace("util");
DP.namespace("Cookie");
DP.util = {
    uniqArray: function (b) {
        var c = [],
            a = {}, d, e, f = b.length;
        if (2 > f) return b;
        for (d = 0; d < f; d++) e = b[d], 1 !== a[e] && (c.push(e), a[e] = 1);
        return c
    },
    isGeolocationSupported: function () {
        return navigator.geolocation ? !0 : !1
    },
    isLocalStorageSupported: function () {
        try {
            return "localStorage" in window && null !== window.localStorage
        } catch (b) {
            return !1
        }
    }
};
DP.Cookie = {
    set: function (b, c, a, d) {
        var e = new Date;
        e.setTime(e.getTime() + 6E4 * a);
        document.cookie = b + "=" + encodeURIComponent(c) + ";expires=" + e.toGMTString() + ";domain=" + document.domain.substring(1) + ";path=" + d + ";"
    },
    get: function (b) {
        b = document.cookie.match(RegExp("(^| )" + b + "=([^;]*)(;|$)"));
        return null != b ? decodeURIComponent(b[2]) : null
    }
};
! function (b, c, a) {
    function d(a, b) {
        var e = c.createElement(a || "div"),
            d;
        for (d in b) e[d] = b[d];
        return e
    }

    function e(a) {
        for (var b = 1, c = arguments.length; b < c; b++) a.appendChild(arguments[b]);
        return a
    }

    function f(b, c) {
        var e = b.style,
            d, f;
        if (e[c] !== a) return c;
        c = c.charAt(0).toUpperCase() + c.slice(1);
        for (f = 0; f < i.length; f++)
            if (d = i[f] + c, e[d] !== a) return d
    }

    function g(b, a) {
        for (var c in a) b.style[f(b, c) || c] = a[c];
        return b
    }

    function h(b) {
        for (var c = 1; c < arguments.length; c++) {
            var e = arguments[c],
                d;
            for (d in e) b[d] === a && (b[d] = e[d])
        }
        return b
    }

    function j(b) {
        for (var a = {
            x: b.offsetLeft,
            y: b.offsetTop
        }; b = b.offsetParent;) a.x += b.offsetLeft, a.y += b.offsetTop;
        return a
    }
    var i = ["webkit", "Moz", "ms", "O"],
        k = {}, l, p, b = d("style", {
            type: "text/css"
        });
    e(c.getElementsByTagName("head")[0], b);
    p = b.sheet || b.styleSheet;
    var m = {
        lines: 12,
        length: 6,
        width: 3,
        radius: 7,
        rotate: 7,
        corners: 1,
        color: "#555",
        speed: 1,
        trail: 100,
        opacity: 0.25,
        fps: 20,
        zIndex: 2E9,
        className: "spinner",
        top: "auto",
        left: "auto",
        position: "relative"
    }, n = function u(b) {
            if (!this.spin) return new u(b);
            this.opts = h(b || {},
                u.defaults, m)
        };
    n.defaults = {};
    h(n.prototype, {
        spin: function (b) {
            this.stop();
            var a = this,
                c = a.opts,
                e = a.el = g(d(0, {
                    className: c.className
                }), {
                    position: c.position,
                    width: 0,
                    zIndex: c.zIndex
                }),
                f = c.radius + c.length + c.width,
                h, i;
            b && (b.insertBefore(e, b.firstChild || null), i = j(b), h = j(e), g(e, {
                left: ("auto" == c.left ? i.x - h.x + (b.offsetWidth >> 1) : parseInt(c.left, 10) + f) + "px",
                top: ("auto" == c.top ? i.y - h.y + (b.offsetHeight >> 1) : parseInt(c.top, 10) + f) + "px"
            }));
            e.setAttribute("aria-role", "progressbar");
            a.lines(e, a.opts);
            if (!l) {
                var k = 0,
                    p = c.fps,
                    n = p / c.speed,
                    m = (1 - c.opacity) / (n * c.trail / 100),
                    o = n / c.lines;
                (function r() {
                    k++;
                    for (var b = c.lines; b; b--) {
                        var d = Math.max(1 - (k + b * o) % n * m, c.opacity);
                        a.opacity(e, c.lines - b, d, c)
                    }
                    a.timeout = a.el && setTimeout(r, ~~ (1E3 / p))
                })()
            }
            return a
        },
        stop: function () {
            var b = this.el;
            b && (clearTimeout(this.timeout), b.parentNode && b.parentNode.removeChild(b), this.el = a);
            return this
        },
        lines: function (b, a) {
            function c(b, e) {
                return g(d(), {
                    position: "absolute",
                    width: a.length + a.width + "px",
                    height: a.width + "px",
                    background: b,
                    boxShadow: e,
                    transformOrigin: "left",
                    transform: "rotate(" + ~~(360 / a.lines * f + a.rotate) + "deg) translate(" + a.radius + "px,0)",
                    borderRadius: (a.corners * a.width >> 1) + "px"
                })
            }
            for (var f = 0, h; f < a.lines; f++) {
                h = d();
                var i = 1 + ~(a.width / 2) + "px",
                    j = a.hwaccel ? "translate3d(0,0,0)" : "",
                    n = a.opacity,
                    m;
                if (m = l) {
                    m = a.opacity;
                    var o = a.trail,
                        q = f,
                        s = a.lines,
                        t = ["opacity", o, ~~ (100 * m), q, s].join("-"),
                        q = 0.01 + 100 * (q / s),
                        s = Math.max(1 - (1 - m) / o * (100 - q), m),
                        r = l.substring(0, l.indexOf("Animation")).toLowerCase(),
                        r = r && "-" + r + "-" || "";
                    k[t] || (p.insertRule("@" + r + "keyframes " + t + "{0%{opacity:" +
                        s + "}" + q + "%{opacity:" + m + "}" + (q + 0.01) + "%{opacity:1}" + (q + o) % 100 + "%{opacity:" + m + "}100%{opacity:" + s + "}}", p.cssRules.length), k[t] = 1);
                    m = t + " " + 1 / a.speed + "s linear infinite"
                }
                h = g(h, {
                    position: "absolute",
                    top: i,
                    transform: j,
                    opacity: n,
                    animation: m
                });
                a.shadow && e(h, g(c("#000", "0 0 4px #000"), {
                    top: "2px"
                }));
                e(b, e(h, c(a.color, "0 0 1px rgba(0,0,0,.1)")))
            }
            return b
        },
        opacity: function (b, a, c) {
            a < b.childNodes.length && (b.childNodes[a].style.opacity = c)
        }
    });
    var o = function (b, a) {
        return d("<" + b + ' xmlns="urn:schemas-microsoft.com:vml" class="spin-vml">',
            a)
    }, b = g(d("group"), {
            behavior: "url(#default#VML)"
        });
    !f(b, "transform") && b.adj ? (p.addRule(".spin-vml", "behavior:url(#default#VML)"), n.prototype.lines = function (b, a) {
        function c() {
            return g(o("group", {
                coordsize: h + " " + h,
                coordorigin: -f + " " + -f
            }), {
                width: h,
                height: h
            })
        }

        function d(b, h, i) {
            e(j, e(g(c(), {
                rotation: 360 / a.lines * b + "deg",
                left: ~~h
            }), e(g(o("roundrect", {
                arcsize: a.corners
            }), {
                width: f,
                height: a.width,
                left: a.radius,
                top: -a.width >> 1,
                filter: i
            }), o("fill", {
                color: a.color,
                opacity: a.opacity
            }), o("stroke", {
                opacity: 0
            }))))
        }
        var f = a.length + a.width,
            h = 2 * f,
            i = 2 * -(a.width + a.length) + "px",
            j = g(c(), {
                position: "absolute",
                top: i,
                left: i
            });
        if (a.shadow)
            for (i = 1; i <= a.lines; i++) d(i, -2, "progid:DXImageTransform.Microsoft.Blur(pixelradius=2,makeshadow=1,shadowopacity=.3)");
        for (i = 1; i <= a.lines; i++) d(i);
        return e(b, j)
    }, n.prototype.opacity = function (a, b, c, e) {
        a = a.firstChild;
        e = e.shadow && e.lines || 0;
        if (a && b + e < a.childNodes.length && (a = (a = (a = a.childNodes[b + e]) && a.firstChild) && a.firstChild)) a.opacity = c
    }) : l = f(b, "animation");
    "function" == typeof define && define.amd ?
        define(function () {
            return n
        }) : DP.Spinner = n
}(window, document);
(function (b) {
    b.fn.slideDown = function (c) {
        var a = {
            callback: function () {},
            ease: "ease-in-out",
            duration: 300
        };
        b.extend(a, c);
        this.show();
        c = this.height();
        return this.css("height", "0").animate({
            height: c
        }, a.duration, a.ease, function () {
            a.callback()
        })
    };
    b.fn.slideUp = function (c) {
        var a = this,
            d = {
                callback: function () {},
                ease: "ease-in-out",
                duration: 300
            };
        b.extend(d, c);
        c = this.height();
        return this.css("height", c).animate({
            height: "0"
        }, d.duration, d.ease, function () {
            a.hide().css("height", "100%");
            a.attr("style", "");
            d.callback()
        })
    }
})(Zepto);
(function (b, c, a) {
    function d(b) {
        this._options = a.extend({
            mode: "msg",
            text: "提示信息",
            useTap: !1,
            useBtns: !1,
            useClose: !1
        }, b || {});
        this._init()
    }
    a.extend(d.prototype, {
        _init: function () {
            var c = this,
                d = c._options,
                g = a(b);
            htmlEle = a('<div class="pop-main"><div class="pop-con"><div class="warn"></div><div class="pop-btn"><button class="ok">确定</button><button class="del">取消</button></div></div></div>');
            closeEle = a('<div class="close icon-close"><div class="icon-plus-circle"></div><div class="icon-plus-line-1"></div><div class="icon-plus-line-2"></div></div>');
            warnEle = htmlEle.find(".warn");
            btnsEle = htmlEle.find(".pop-btn");
            sureEle = htmlEle.find(".pop-btn .ok");
            cancelEle = htmlEle.find(".pop-btn .del");
            sureText = d.sureText;
            content = d.content;
            mode = d.mode;
            text = d.text;
            cb = d.callback;
            cancelCb = d.cancelCallback;
            useBtns = d.useBtns;
            useClose = d.useClose;
            bkColor = d.background;
            action = d.useTap ? "tap" : "click";
            oldClassText = htmlEle.attr("class");
            newClassText = oldClassText.replace(/(msg|alert|confirm)Mode/i, mode + "Mode");
            htmlEle.attr("class", newClassText);
            bkColor && htmlEle.css("background",
                bkColor);
            text && warnEle.html(text);
            content && warnEle.append(content);
            useBtns && btnsEle.css("display", "block");
            sureText && sureEle.text(sureText);
            useClose ? (htmlEle.append(closeEle), closeEle.on("click", function () {
                c.close()
            })) : htmlEle.children().eq(0).css("padding-right", 15);
            a("body").append(htmlEle);
            sureEle.on("click", function () {
                a.extend(d, {
                    text: warnEle,
                    content: content
                });
                cb && cb.call(c, d, true)
            });
            cancelEle.on("click", function () {
                c.close();
                cancelCb && cancelCb.call(c, d, true)
            });
            g.on("resize", function () {
                setTimeout(function () {
                        c._pos()
                    },
                    500)
            })
        },
        _pos: function () {
            var a = c.documentElement,
                b = c.body;
            this.isHide() || htmlEle.css({
                top: b.scrollTop + (a.clientHeight - htmlEle.height()) / 2,
                left: b.scrollLeft + (a.clientWidth - htmlEle.width()) / 2
            })
        },
        _cbShow: function () {
            var a = this._options.onShow;
            htmlEle.css("opacity", "1").addClass("show");
            a && a.call(this)
        },
        _cbHide: function () {
            var a = this._options.onHide;
            this._options.useClose && closeEle.css("opacity", "0");
            htmlEle.css("opacity", "0").addClass("hide");
            a && a.call(this)
        },
        show: function () {
            var a = this;
            a.isShow() ? a._cbShow() :
                (htmlEle.css("opacity", "0").removeClass("hide"), a._pos(), setTimeout(function () {
                a._cbShow()
            }, 300), setTimeout(function () {
                htmlEle.animate({
                    opacity: "1"
                }, 300, "linear")
            }, 1))
        },
        hide: function () {
            var a = this;
            a.isHide() ? a._cbHide() : (htmlEle.css("opacity", "1").removeClass("show"), setTimeout(function () {
                a._cbHide()
            }, 300), setTimeout(function () {
                htmlEle.animate({
                    opacity: "0"
                }, 300, "linear")
            }, 1))
        },
        flash: function (a) {
            var b = this;
            b._options.onShow = function () {
                setTimeout(function () {
                    b.close()
                }, a)
            };
            b.show()
        },
        isShow: function () {
            return htmlEle.hasClass("show")
        },
        isHide: function () {
            return htmlEle.hasClass("hide")
        },
        close: function () {
            htmlEle.remove()
        }
    });
    DP.Notification = new function () {
        this.flashBox = function (a, b) {
            var c = new d({
                mode: "msg",
                text: a
            });
            c.flash(b || 2E3);
            return c
        };
        this.ajaxBox = function (a, b) {
            var c = new d({
                mode: "msg",
                text: a,
                useClose: b
            });
            c.show();
            return c
        };
        this.formBox = function (b, c) {
            var g = new d(a.extend({
                mode: "msg",
                text: b
            }, c || {}));
            g.show();
            return g
        }
    }
})(window, document, Zepto);
Zepto(function (b) {
    var c = {
        contShow: b("#swipeTop"),
        swipeTabCont: b("#swipeTopCont"),
        bodyCont: b("body"),
        rightCate: b("#rightCate"),
        category: b("#J_category"),
        left: -290,
        height: b("#contShow").height(),
        bodyH: b("#bodyCont").height(),
        init: function () {
            var a = this;
            a.contShow.find(".nearby-distance a").each(function (c, e) {
                b(e).click(function () {
                    b(this).addClass("distance-cur").siblings().removeClass("distance-cur");
                    b("#swipeCont-" + (c + 1)).show().siblings().hide();
                    a.refresh()
                })
            })
        },
        start: function (a) {
            b("#type-search-" +
                a).size() && b("#type-search-" + a).removeClass("hide").siblings().addClass("hide");
            b("#swipeCont-" + a).show().siblings().hide()
        },
        showLeft: function () {
            var a = this;
            window.scroll(0, 0);
            a.rightCate.show();
            a.contShow.css({
                "-webkit-backface-visibility": "hidden",
                "-webkit-prespective": "1000",
                "-webkit-transform-style": "preserve-3d"
            });
            a.bodyCont.css({
                "min-height": a.swipeTabCont.height() + 200
            });
            a.rightCate.css({
                height: a.bodyCont.height()
            });
            a.bodyCont.append('<div id="leftmask" class="right-mask"><div class="infor"><div class="cont"><span class="l"></span><span class="r"></span></div></div></div>');
            a.translation(a.contShow[0], {
                x: 0,
                duration: "0.4s"
            }, function () {
                b("#leftmask").css("height", a.bodyCont.height())
            })
        },
        hideLeft: function () {
            var a = this;
            window.scroll(0, 0);
            a.category.removeClass("on").addClass("hide");
            a.translation(a.rightCate[0], {
                x: 275,
                duration: "0.4s"
            }, function () {
                b("#leftmask").remove();
                a.rightCate.hide();
                a.bodyCont.attr("style", "position:relative");
                a.rightCate.attr("style", "");
                a.contShow.attr("style", "");
                a.onHide()
            })
        },
        refresh: function () {
            var a = this;
            setTimeout(function () {
                var b = a.swipeTabCont.height() +
                    200;
                a.bodyCont.css({
                    "min-height": b
                });
                a.rightCate.css({
                    "min-height": b
                })
            }, 500)
        },
        translation: function (a, c, e) {
            c = b.extend({
                duration: "0.4s",
                origin: "0 0"
            }, c || {});
            a = a.style;
            !a.webkitTransitionProperty && (a.webkitTransitionProperty = "-webkit-transform");
            a.webkitTransitionDuration !== c.duration && (a.webkitTransitionDuration = c.duration);
            a.webkitTransformOrigin !== c.origin && (a.webkitTransformOrigin = c.origin);
            "hidden" !== a.webkitBackfaceVisibility && (a.webkitBackfaceVisibility = "hidden");
            "preserve-3d" !== a.webkitTransformStyle &&
                (a.webkitTransformStyle = "preserve-3d");
            if (null != c.x || null != c.y) a.webkitTransform = "translate(" + (c.x ? c.x + "px," : "0,") + (c.y ? c.y + "px" : "0") + ")", setTimeout(e, 1500 * parseFloat(c.duration))
        },
        onHide: function () {},
        onShow: function () {},
        showPic: function (a, c) {
            var e = this;
            if (0 == e.bodyCont.find("#leftmask").length) {
                var f = b("#" + c),
                    g = f.children(".pic-nearby");
                0 == g.children("#slider").children(".fluxslider").length && (window.f = new flux.slider(g.children("#slider"), {
                    pagination: !0,
                    autoplay: !1,
                    transitions: ["slide"],
                    controls: !0,
                    width: 280,
                    height: 180,
                    captions: !0
                }));
                f.show();
                var h = document.documentElement,
                    j = document.body;
                f.css({
                    top: j.scrollTop + (h.clientHeight - f.height()) / 2,
                    left: j.scrollLeft + (h.clientWidth - f.width()) / 2
                });
                g.css({
                    "-webkit-backface-visibility": "hidden",
                    "-webkit-prespective": "1000",
                    "-webkit-transform-style": "preserve-3d"
                });
                e.bodyCont.css({
                    "min-height": g.height() + 200
                });
                e.bodyCont.append('<div id="leftmask" class="right-mask"></div>');
                e.translation(g[0], {
                    x: 0,
                    duration: "0.4s"
                }, function () {
                    b("#leftmask").css("height",
                        e.bodyCont.height())
                })
            }
        },
        hidePic: function (a) {
            var c = this,
                e = b("#" + a),
                f = e.children(".pic-nearby");
            c.translation(e[0], {
                x: 0,
                duration: "0s"
            }, function () {
                c.bodyCont.find("#leftmask").remove();
                e.hide();
                c.bodyCont.attr("style", "position:relative");
                e.attr("style", "");
                f.attr("style", "")
            })
        }
    };
    DP.overlay = c
});

function Base64() {
    _keyStr = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
    this.encode = function (b) {
        for (var c = "", a, d, e, f, g, h, j = 0, b = _utf8_encode(b); j < b.length;) a = b.charCodeAt(j++), d = b.charCodeAt(j++), e = b.charCodeAt(j++), f = a >> 2, a = (a & 3) << 4 | d >> 4, g = (d & 15) << 2 | e >> 6, h = e & 63, isNaN(d) ? g = h = 64 : isNaN(e) && (h = 64), c = c + _keyStr.charAt(f) + _keyStr.charAt(a) + _keyStr.charAt(g) + _keyStr.charAt(h);
        return c
    };
    this.decode = function (b) {
        for (var c = "", a, d, e, f, g, h = 0, b = b.replace(/[^A-Za-z0-9\+\/\=]/g, ""); h < b.length;) a =
            _keyStr.indexOf(b.charAt(h++)), d = _keyStr.indexOf(b.charAt(h++)), f = _keyStr.indexOf(b.charAt(h++)), g = _keyStr.indexOf(b.charAt(h++)), a = a << 2 | d >> 4, d = (d & 15) << 4 | f >> 2, e = (f & 3) << 6 | g, c += String.fromCharCode(a), 64 != f && (c += String.fromCharCode(d)), 64 != g && (c += String.fromCharCode(e));
        return c = _utf8_decode(c)
    };
    _utf8_encode = function (b) {
        for (var b = b.replace(/\r\n/g, "\n"), c = "", a = 0; a < b.length; a++) {
            var d = b.charCodeAt(a);
            128 > d ? c += String.fromCharCode(d) : (127 < d && 2048 > d ? c += String.fromCharCode(d >> 6 | 192) : (c += String.fromCharCode(d >>
                12 | 224), c += String.fromCharCode(d >> 6 & 63 | 128)), c += String.fromCharCode(d & 63 | 128))
        }
        return c
    };
    _utf8_decode = function (b) {
        for (var c = "", a = 0, d = c1 = c2 = 0; a < b.length;) d = b.charCodeAt(a), 128 > d ? (c += String.fromCharCode(d), a++) : 191 < d && 224 > d ? (c2 = b.charCodeAt(a + 1), c += String.fromCharCode((d & 31) << 6 | c2 & 63), a += 2) : (c2 = b.charCodeAt(a + 1), c3 = b.charCodeAt(a + 2), c += String.fromCharCode((d & 15) << 12 | (c2 & 63) << 6 | c3 & 63), a += 3);
        return c
    }
}
DP.namespace("app");
DP.app = {
    hidePhoneAddrBar: function () {
        setTimeout(function () {
            window.scrollTo(0, 1)
        }, 100)
    },
    initDeskScreen: function () {
        0 <= navigator.userAgent.toLowerCase().indexOf("iphone") && !DP.Cookie.get("iphoneaddscreen") && ($("body").append('<div id="addMscreen"><b class="ico_close"></b><p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;请按下方按钮<br>选择 "添加到主屏幕"<br>即可快速浏览我们了啦!</p><div class="addThree"></div></div>'), $("#addMscreen").css("display", "block"));
        $(".ico_close").on("click", function () {
            $("#addMscreen").remove();
            DP.Cookie.set("iphoneaddscreen",
                1, 5256E4, "/")
        });
        $("#addMscreen").on("click", function () {
            $(this).remove();
            DP.Cookie.set("iphoneaddscreen", 1, 5256E4, "/")
        })
    },
    enterComputer: function () {
        var b = $("#j-computer"),
            c = $("body"),
            a = function (a) {
                var b = "",
                    a = a.substring(1);
                return b = -1 != a.indexOf(".dianping.com") ? "http://www" + a : "http://w" + a
            };
        b.on("click", function () {
            _gaq.push(["_trackEvent", "home_footer_pc", "click", DP.cityId]);
            _hip.push(["mv", {
                module: "home_footer_pc",
                action: "click"
            }]);
            if (DP.Cookie.get("pcvisithistory")) document.cookie = "vmod=pc; domain=" +
                document.domain.substring(1) + ";path=/;", window.setTimeout(function () {
                    window.location.href = a(document.domain)
                }, 300);
            else {
                var b = document.documentElement,
                    e = document.body;
                c.append('<div class="mask-layer"></div><form id="j-form" action="/pcfeedback_submit" method="post"><div class="question">\t\t\t<a href="javascript:;" class="mask-close" id="j-close"></a>\t\t\t<div class="question-wrap">\t\t\t\t<h3>您到电脑版是想使用：</h3>\t\t\t\t<ul class="question-list">\t\t\t\t\t<li><input type="checkbox" id="reason-1"/><label for="reason-1">社区</label></li>\t\t\t\t\t<li><input type="checkbox" id="reason-2"/><label for="reason-2">精选内容（榜单）</label></li>\t\t\t\t\t<li><input type="checkbox" id="reason-3"/><label for="reason-3">同城活动（试吃、体验）</label></li>\t\t\t\t\t<li><input type="checkbox" id="reason-4"/><label for="reason-4">会员卡相关</label></li>\t\t\t\t\t<li><input type="checkbox" id="reason-5"/><label for="reason-5">精选优惠</label></li>\t\t\t\t\t<li><input type="checkbox" id="reason-6"/><label for="reason-6">电脑版找商户更方便</label></li>\t\t\t\t</ul>\t\t\t\t<div class="feed-tips">其他原因（选填）</div>\t\t\t\t<textarea id="j-message" name="feedbackMessage" cols="" rows="" class="feed-tar default" style="margin-bottom:10px;height:60px;"></textarea>\t\t\t\t<a id="q-btn" href="javascript:;" title="提交" class="icon-btn icon-btn-orange">提交</a>\t\t\t</div>\t\t</div></form>');
                $(".mask-layer").css("height", e.scrollHeight);
                var f = $(".question");
                f.css({
                    top: e.scrollTop + (b.clientHeight - f.height()) / 2,
                    left: e.scrollLeft + (b.clientWidth - f.width()) / 2
                });
                var b = $("#q-btn"),
                    g = $("#j-form"),
                    e = $("#j-close");
                DP.Cookie.set("pcvisithistory", 1, 5256E4, "/");
                b.on("click", function () {
                    $("#reason-1").attr("checked") && _gaq.push(["_trackEvent", "feedback_diaoyan_社区", "click", ""]);
                    $("#reason-2").attr("checked") && _gaq.push(["_trackEvent", "feedback_diaoyan_精选内容", "click", ""]);
                    $("#reason-3").attr("checked") &&
                        _gaq.push(["_trackEvent", "feedback_diaoyan_同城活动", "click", ""]);
                    $("#reason-4").attr("checked") && _gaq.push(["_trackEvent", "feedback_diaoyan_会员卡", "click", ""]);
                    $("#reason-5").attr("checked") && _gaq.push(["_trackEvent", "feedback_diaoyan_精选优惠", "click", ""]);
                    $("#reason-6").attr("checked") && _gaq.push(["_trackEvent", "feedback_diaoyan_方便", "click", ""]);
                    "" === $("#j-message").val().trim() && _gaq.push(["_trackEvent", "feedback_diaoyan_空", "click", ""]);
                    document.cookie = "vmod=pc; domain=" + document.domain.substring(1) + ";path=/;";
                    window.setTimeout(function () {
                        g.submit()
                    }, 100)
                });
                e.on("click", function () {
                    _gaq.push(["_trackEvent", "feedback_diaoyan_cancel", "click", ""]);
                    document.cookie = "vmod=pc; domain=" + document.domain.substring(1) + ";path=/;";
                    window.setTimeout(function () {
                        window.location.href = a(document.domain)
                    }, 100)
                })
            }
        })
    },
    slideHeader: function () {
        var b = $("#banner");
        if (DP.Cookie.get("show_download_bar")) b.remove();
        else {
            var c = $("#J_close");
            b.size() && new $.slide({
                panel: b.find("ul"),
                tab: b.find("ol"),
                easing: "ease",
                direction: "left",
                isLoop: !0,
                duration: 0.2
            });
            c.click(function (a) {
                a && a.preventDefault();
                _gaq.push(["_trackEvent", "index_DLbanner_cancel", "click"]);
                DP.Cookie.set("show_download_bar", 1);
                $(this).parent().remove()
            })
        }
    },
    download: function (b) {
        function c() {
            500 > (new Date).getTime() - d && (window.location = "http://m.api.dianping.com/downloadlink?from=3112")
        }
        var a = document.getElementById("mobileFrame"),
            d;
        b.click(function () {
            "index" == $(this).attr("data-type") ? _gaq.push(["_trackEvent", "index_DLbanner_huge", "click", "下载"]) : _gaq.push(["_trackEvent",
                "index_DLbanner_small", "click", "下载"
            ]);
            a.src = "dianping://home";
            d = (new Date).getTime();
            setTimeout(c, 400)
        })
    },
    downloadBar: function () {
        var b = $("#download"),
            c = $("#J_bar"),
            a = $("#J_close");
        DP.app.download(b);
        DP.Cookie.get("show_download_bar") ? c.addClass("hide") : ($("body").addClass("index-box"), c.removeClass("hide"));
        a.click(function (a) {
            a && a.preventDefault();
            _gaq.push(["_trackEvent", "index_DLbanner_cancel", "click"]);
            DP.Cookie.set("show_download_bar", 1, 5256E4, "/");
            c.addClass("hide");
            $("body").removeClass("index-box");
            c.remove()
        })
    },
    returnHistory: function (b) {
        b.click(function (b) {
            b && b.preventDefault();
            "" != document.referrer && /(m.dianping.com|m.51ping.com)/.test(document.referrer) ? window.history.go(-1) : window.location.href = "/"
        })
    },
    toTop: function () {
        var b = null,
            c = $(window),
            a = $("#srcollTop");
        c.on("scroll", function () {
            b && clearTimeout(b);
            b = setTimeout(function () {
                0 < window.scrollY ? a.show() : a.hide()
            }, 400)
        });
        a.on("click", function () {
            window.scroll(0, 0)
        })
    },
    initHomeSearch: function () {
        var b = DP.cityId;
        $("#J_search_input").focus(function (a) {
            a &&
                a.stopPropagation();
            scrollTo(0, 0);
            DP.Cookie.get("show_download_bar") || $("#J_bar").addClass("hide");
            c();
            _gaq.push(["_trackEvent", "index_keyword", "click", b]);
            _hip.push(["mv", {
                module: "index_keyword",
                action: "click",
                note: b
            }]);
            DP.status = !0;
            DP.app.search()
        });
        $("#search-cancel").click(function () {
            $("#search-cancel").hide();
            $("header").show();
            $("nav").show();
            $("#j-entry").show();
            $("#banner").show();
            $("#search-div").css("margin-left", "0px");
            $("#classify").show();
            $("#hot-trade").show();
            $("footer").show();
            $("#search-list").show();
            $(".classify-seek").show();
            $(".group-activity").show();
            $(".restaurant").show();
            $(".footer-nav").show();
            $("#J_search_ul").hide();
            DP.status = !1;
            DP.st && clearTimeout(DP.st);
            setTimeout(function () {
                $("#keyword-list").hide()
            }, 50);
            DP.Cookie.get("show_download_bar") || $("#J_bar").removeClass("hide")
        });
        $(".voice-input").click(function () {
            c();
            DP.status = !0;
            DP.app.search()
        });
        var c = function () {
            $("#classify").hide();
            $("header").hide();
            $("#j-entry").hide();
            $("nav").hide();
            $("#hot-trade").hide();
            $("footer").hide();
            $("#banner").hide();
            $(".classify-seek").hide();
            $(".group-activity").hide();
            $(".restaurant").hide();
            $(".footer-nav").hide();
            $("#search-div").css("margin-left", "65px");
            $("#search-cancel").show();
            $("#search-list").show();
            $("#keyword-list").show();
            $("#J_search_ul").show()
        }
    },
    foodSearch: function () {
        $("#J_search").click(function (a) {
            a && a.preventDefault();
            b();
            $("footer").hide();
            $("#search-div").css("margin-left", "65px");
            $("#search-cancel").show();
            $("#search-div").show()
        });
        $("#search-cancel").click(function (a) {
            a && a.preventDefault();
            c();
            $("footer").show();
            $("#search-cancel").hide();
            $("#search-div").hide()
        });
        $("#J_food").click(function (a) {
            a && a.preventDefault();
            b();
            $("#J-food-category").show();
            $("#food-category").show()
        });
        $("#categroy-cancel").click(function (a) {
            a && a.preventDefault();
            c();
            $("#J-food-category").hide();
            $("#food-category").hide()
        });
        var b = function () {
            $(".shop-action").hide();
            $("#J_main_header").hide();
            $(".shopOverlay-action").hide();
            $(".action-mod").hide();
            $(".tuan-action").hide();
            $(".chosen-action").hide();
            $(".footer-search").hide();
            $(".footer-tags").hide();
            $(".shop-download-bg").hide();
            $(".footer-tags-box").hide();
            $(".footer-nav").hide()
        }, c = function () {
                $("#J_main_header").show();
                $(".shop-action").show();
                $(".action-mod").show();
                $(".tuan-action").show();
                $(".chosen-action").show();
                $(".footer-search").show();
                $(".footer-tags").show();
                $(".shop-download-bg").show();
                $(".footer-tags-box").show();
                $(".footer-nav").show()
            }
    },
    refresh: function () {
        /iphone|ipad/gi.test(navigator.appVersion) && Boolean(navigator.userAgent.match(/OS [3-4]_\d[_\d]* like Mac OS X/i)) &&
            $(".loc-bar").hide();
        $("#j-refresh").click(function (b) {
            b && b.preventDefault();
            DP.util.isGeolocationSupported() && navigator.geolocation.getCurrentPosition(function (b) {
                var a = b.coords.longitude;
                DP.Cookie.set("locallat", b.coords.latitude, 15, "/");
                DP.Cookie.set("locallng", a, 15, "/");
                window.location.reload()
            }, function () {}, {
                enableHighAccuracy: !0,
                maximumAge: 3E4,
                timeout: 5E3
            })
        })
    },
    locCity: function () {
        var b = $(".position-city"),
            c = $(".position-city").attr("data-type");
        if (DP.util.isGeolocationSupported()) {
            var a = {
                enableHighAccuracy: !0,
                maximumAge: 3E4,
                timeout: 5E3
            }, d = function (a, e) {
                    $.ajax({
                        type: "POST",
                        url: "/getlocalcityid",
                        data: {
                            lat: a,
                            lng: e
                        },
                        dataType: "json",
                        beforeSend: function () {},
                        success: function (d) {
                            if (200 === d.code) {
                                var f = d.message,
                                    d = f.cityid,
                                    f = f.cityname;
                                DP.Cookie.set("localcityid", d, 15, "/");
                                DP.Cookie.set("locallat", a, 15, "/");
                                DP.Cookie.set("locallng", e, 15, "/");
                                c = c.replace("c0.csf", "c" + d + ".csf");
                                b.html("<h5>当前定位城市：<a onclick=\"_gaq.push(['_trackEvent', 'citylist_locate', 'click', '" + f + '\'])" href="' + c + '"><strong>' + f + "</strong></a></h5>")
                            }
                        },
                        error: function () {}
                    })
                }, e = function (a) {
                    d(a.coords.latitude, a.coords.longitude)
                }, f = function (a) {
                    switch (a.code) {
                    case a.TIMEOUT:
                        b.html("<h5>链接超时，请重试</h5>");
                        break;
                    case a.PERMISSION_DENIED:
                        b.html("<h5>您拒绝了使用位置共享服务，查询已取消</h5>")
                    }
                };
            DP.Cookie.get("localcityid") ? (a = DP.Cookie.get("locallat"), e = DP.Cookie.get("locallng"), d(a, e)) : navigator.geolocation.getCurrentPosition(e, f, a)
        }
    },
    newSwitchCity: function (b) {
        function c(a, b) {
            DP.util.isGeolocationSupported() && navigator.geolocation.getCurrentPosition(function (c) {
                var e = c.coords.latitude,
                    d = c.coords.longitude;
                $.ajax({
                    type: "POST",
                    url: "/getlocalcityid",
                    data: {
                        lat: e,
                        lng: d
                    },
                    dataType: "json",
                    success: function (b) {
                        200 === b.code && (DP.Cookie.set("locallat", e, 15, "/"), DP.Cookie.set("locallng", d, 15, "/"), a(b.message))
                    },
                    error: function () {
                        b()
                    }
                })
            }, function (a) {
                switch (a.code) {
                case a.TIMEOUT:
                    b();
                    break;
                case a.PERMISSION_DENIED:
                    b();
                    break;
                case a.POSITION_UNAVAILABLE:
                    b()
                }
            }, {
                enableHighAccuracy: !0,
                maximumAge: 3E4,
                timeout: 4E3
            })
        }

        function a() {
            if (!DP.Cookie.get("switchcityflashtoast")) {
                var a = $("#J_toast");
                a.removeClass("hide");
                window.setTimeout(function () {
                    a.addClass("hide")
                }, 3E3);
                DP.Cookie.set("switchcityflashtoast", "1", 5256E4, "/")
            }
        }
        var d = DP.Cookie.get("visitflag"),
            e = DP.Cookie.get("cityid"),
            f = function () {
                var a = DP.Cookie.get("localcityid"),
                    c = DP.Cookie.get("localcityname"),
                    d = DP.Cookie.get("localcitypinyin");
                if (!DP.Cookie.get("noswitchcity") && (e || b) && a && a != e && c && d) c = (new Base64).decode(c), DP.Notification.formBox('<p style="text-align:center;">您目前定位是在<strong>' + c + "</strong></p>", {
                    content: '<p style="text-align:center;">是否切换到<strong>' +
                        c + "</strong>？</p>",
                    useBtns: !0,
                    sureText: "切换",
                    callback: function () {
                        var b = new Base64;
                        DP.Cookie.set("cityid", a, 5256E4, "/");
                        DP.Cookie.set("citypinyin", d, 5256E4, "/");
                        DP.Cookie.set("cityname", b.encode(c), 5256E4, "/");
                        var b = document.location.href,
                            e = b.indexOf("shoplist/"); - 1 != e ? (b = b.substring(e + 9), b = b.substring(b.indexOf("/")), document.location.href = "/shoplist/" + a + b) : document.location.href = "/" + d
                    },
                    cancelCallback: function () {
                        DP.Cookie.set("noswitchcity", "1", 15, "/")
                    }
                })
            };
        if (0 == d) c(function (a) {
            var b = a.cityname,
                c =
                    a.citypinyin;
            DP.Cookie.set("localcityid", a.cityid, 15, "/");
            a = new Base64;
            DP.Cookie.set("localcityname", a.encode(b), 15, "/");
            DP.Cookie.set("localcitypinyin", c, 15, "/");
            DP.Cookie.set("visitflag", 1, 5256E4, "/");
            f()
        }, function () {
            a()
        });
        else if (1 == d) {
            var d = function (a) {
                var b = a.cityname;
                DP.Cookie.set("localcityid", a.cityid, 15, "/");
                a = new Base64;
                DP.Cookie.set("localcityname", a.encode(b), 15, "/");
                DP.Cookie.set("localcitypinyin", citypinYin, 15, "/");
                f()
            }, g = function () {
                    a()
                };
            DP.Cookie.get("locallat") ? f() : c(d, g)
        }
    },
    search: function () {
        function b(a,
            b) {
            var c = localStorage.getItem(a),
                c = c ? c.split(",") : [];
            c.push(b);
            c = DP.util.uniqArray(c);
            c.length = 25 > c.length ? c.length : 25;
            localStorage.setItem(a, c.join(","))
        }

        function c(a) {
            18 < a.length && (a = a.substring(0, 18) + "...");
            return a
        }

        function a() {
            var e = $("#J_search_input"),
                k = $.trim(e.val());
            if ("" === k)
                if (j = "", DP.isBSG) DP.status && (f.empty(), $("#keyword-list").show());
                else {
                    var l = localStorage.getItem(h);
                    f.empty();
                    l && (l = l.split(","), 0 !== l.length && (l.forEach(function (a) {
                        $('<li><a class="J_history" href="javascript:void(0)">' +
                            a + "</a></li>").prependTo(f).find(".J_history").click(function (a) {
                            a && a.preventDefault();
                            a = $(this).text();
                            d.val(a);
                            g.submit()
                        })
                    }), $('<li><a id="J_clearHistory" class="last" href="javascript:void(0)">清除搜索记录</a></li>').appendTo(f).find("#J_clearHistory").click(function (a) {
                        a && a.preventDefault();
                        localStorage.removeItem(h);
                        f.empty();
                        d.val("")
                    })))
                } else j != k && ($("#keyword-list").hide(), $.ajax({
                    type: "POST",
                    url: e.attr("data-url") || "/searchsuggest",
                    data: {
                        keyword: k
                    },
                    dataType: "json",
                    success: function (a) {
                        var d = [];
                        f.html("");
                        j = k;
                        200 === a.code ? (d = a.message.result, d.forEach(function (a) {
                            $('<li><a href="javascript:;">' + c(a.keyword) + '</a><span class="fruit-numb">约' + a.num + "个结果</span></li>").prependTo(f).find("a").on("click", function (c) {
                                c && c.preventDefault();
                                e.val(a.keyword);
                                b(h, a.keyword);
                                g.submit()
                            })
                        })) : f.prepend("<li>查找“" + k + "”</li>")
                    }
                }));
            DP.st = setTimeout(function () {
                a()
            }, 500)
        }
        if (DP.util.isLocalStorageSupported()) {
            var d = $("#J_search_input"),
                e = $("#J_search_btn"),
                f = $("#J_search_ul"),
                g = $("#searchForm"),
                h = "searchstorage",
                j = "";
            a();
            e.click(function (a) {
                var c = $.trim(d.val());
                _gaq.push(["_trackEvent", "index_keyword_success", "click", c, DP.cityId]);
                _hip.push(["mv", {
                    module: "index_keyword_success",
                    action: "click",
                    note: DP.cityId,
                    content: c
                }]);
                "" === c ? (DP.Notification.flashBox("请输入搜索关键字"), a && a.preventDefault()) : (b(h, c), g.submit())
            });
            g.submit(function (a) {
                var c = $.trim(d.val());
                _gaq.push(["_trackEvent", "index_keyword_success", "click", c]);
                _hip.push(["mv", {
                    module: "index_keyword_success",
                    action: "click",
                    content: c
                }]);
                "" === c ? (DP.Notification.flashBox("请输入搜索关键字"),
                    a && a.preventDefault()) : b(h, c)
            })
        }
    },
    ajaxFold: function (b, c) {
        b.each(function (a, b) {
            var e = $(b),
                f = e.attr("data-id"),
                g = !0,
                h = e.attr("data-cityid"),
                j = "category" == c ? "/getchildrencategory" : "/getchildrenregion",
                i = "category" == c ? {
                    categoryid: f
                } : {
                    regionid: f
                }, k = "";
            e.on("click", function (a) {
                a && a.preventDefault();
                "0" === e.attr("status") ? ($.ajax({
                        type: "POST",
                        url: j,
                        data: i,
                        dataType: "json",
                        beforeSend: function () {
                            e.after('<div style="height:40px;" class="spin"></div>');
                            (new DP.Spinner).spin(e.next()[0])
                        },
                        success: function (a) {
                            if (200 ===
                                a.code) {
                                var a = "category" == c ? a.message.category : a.message.region,
                                    b = "category" == c ? "/c/" : "/r/",
                                    d = a.length,
                                    f = d % 4,
                                    d = parseInt(d / 4),
                                    g = document.referrer,
                                    j = null;
                                null != g && 0 < g.indexOf("/food") && (j = "food");
                                for (var g = function (a) {
                                    var d = "_gaq.push(['_trackEvent', '" + ("category" == c ? c : "area") + "_more', 'click',  '" + a[c + "Name"] + "'])";
                                    return null != j && "food" == j ? '<div><a onclick="' + d + '" href="javascript:window.location.href=\'/shoplist/' + h + b + a[c + "Id"] + "/c/10/s/s_-1'\">" + a[c + "Name"] + '</a><div class="sx"></div></div>' : '<div><a onclick="' +
                                        d + '" href="javascript:window.location.href=\'/shoplist/' + h + b + a[c + "Id"] + "'\">" + a[c + "Name"] + '</a><div class="sx"></div></div>'
                                }, i = 1; i <= d; i++) {
                                    var l = '<div class="dp-3gcon-index">' + g(a[4 * (i - 1)]) + g(a[4 * i - 3]) + g(a[4 * i - 2]) + g(a[4 * i - 1]) + "</div>";
                                    k += l
                                }
                                0 < f && (a = '<div class="dp-3gcon-index">' + g(a[4 * d]) + (a[4 * d + 1] ? g(a[4 * d + 1]) : "<div></div>") + (a[4 * d + 2] ? g(a[4 * d + 2]) : "<div></div>") + (a[4 * d + 3] ? g(a[4 * d + 3]) : "<div></div>") + "</div>", k += a);
                                e.parent().find(".spin").remove();
                                e.after('<div class="son-classify">' + k + "</div>")
                            }
                        }
                    }),
                    e.parent().siblings().find(".son-classify").addClass("hide"), e.parent().siblings().find("i").removeClass("sify-up").addClass("sify-down"), e.find("i").removeClass("sify-down").addClass("sify-up"), e.attr("status", "1"), g = !0) : g ? (e.find("i").removeClass("sify-up").addClass("sify-down"), e.next().addClass("hide"), g = !1) : (e.parent().siblings().find(".son-classify").addClass("hide"), e.parent().siblings().find("i").removeClass("sify-up").addClass("sify-down"), e.find("i").removeClass("sify-down").addClass("sify-up"),
                    e.next().removeClass("hide"), g = !0)
            })
        })
    },
    addComment: function () {
        var b = $(".comm-star"),
            c = $("#J_star_holder"),
            a = $("#J_submit");
        b.each(function (a, e) {
            $(e).click(function () {
                var e = $(this).attr("data-star");
                b.removeClass("star-cur");
                c.val(e);
                for (e = 0; e <= a; e++) $(b[e]).addClass("star-cur")
            })
        });
        a.click(function (a) {
            a && a.preventDefault();
            _gaq.push(["_trackEvent", "addreview_add_submit", "click"]);
            var a = $(".shop-mycomment select"),
                c = a.length,
                f = $("#J_average"),
                g = $("#J_describe");
            if ($(b[0]).hasClass("star-cur")) {
                for (var h =
                    0; h < c; h++) {
                    var j = $(a[h]),
                        i = j.prev().text();
                    if ("-1" === j.val()) {
                        DP.Notification.flashBox(i.replace("：", "") + "项不能为空", 2E3);
                        return
                    }
                }
                "" != f.val() && !/^[0-9]+$/.test(f.val()) ? DP.Notification.flashBox("请输入正确的人均", 1E3) : 999999 < parseInt(f.val()) ? DP.Notification.flashBox("人均不超过999999", 1E3) : 30 > g.val().length ? DP.Notification.flashBox("评价最少为30个字", 1E3) : $("#form").submit()
            } else DP.Notification.flashBox("请点选评价星级", 1E3)
        })
    },
    shopOverlay: function (b, c) {
        var a = $(".J_title"),
            d = a.next(),
            e = $(".toggler-bar");
        a.click(function () {
            var a =
                $(this);
            nextEle = a.next();
            iconEle = a.find(".icon");
            iconEle.hasClass("nearby-down") ? iconEle.removeClass("nearby-down").addClass("nearby-up") : iconEle.removeClass("nearby-up").addClass("nearby-down");
            nextEle.hasClass("hide") ? nextEle.removeClass("hide") : nextEle.addClass("hide");
            e.parent().find("i").removeClass("nearby-up").addClass("nearby-down");
            e.parent().find(".distan-news").addClass("hide")
        });
        e.each(function (b, c) {
            var e = $(c),
                j = e.next(),
                i = j.next();
            e.click(function () {
                e.parent().siblings().find("i").removeClass("nearby-up").addClass("nearby-down");
                e.parent().siblings().find(".distan-news").addClass("hide");
                "nearby-down" === j.attr("class") ? j.removeClass("nearby-down").addClass("nearby-up") : j.removeClass("nearby-up").addClass("nearby-down");
                i.hasClass("hide") ? i.removeClass("hide") : i.addClass("hide");
                a.find(".icon").removeClass("nearby-up").addClass("nearby-down");
                d.addClass("hide")
            })
        });
        (b && 0 < b.size() ? b : $(".shop-search li")).each(function (a, b) {
            var c = $(b);
            c.on("click", function () {
                if ("J_shop_distance" === c.attr("id")) {
                    var b = $(".nearby-distance a");
                    b.removeClass("distance-cur");
                    b.each(function (a, b) {
                        c.text() === $(b).text() && $(b).addClass("distance-cur")
                    })
                }
                DP.overlay.start(a + 1);
                DP.overlay.showLeft();
                $("body > div").click(function (a) {
                    a = a.target || a.srcElement;
                    "DIV" === a.nodeName.toUpperCase() && "leftmask" == $(a).attr("id") && ($(a).remove(), DP.overlay.hideLeft(), window.scroll(0, 0))
                });
                $("#leftmask .infor").click(function () {
                    $(this).remove();
                    DP.overlay.hideLeft();
                    window.scroll(0, 0)
                })
            })
        });
        c && c()
    },
    shopDetail: function () {
        var b = $(".shop-rec");
        b.size() && b.click(function () {
            var b =
                $(this),
                a = $("#J_recommend li"),
                d = $("#J_arrow"),
                e = b.attr("data-shopId"),
                b = b.attr("data-shopName");
            _gaq.push(["_trackEvent", "shopinfo_dish", "click", e + "|" + b]);
            d.hasClass("sify-down") && d.removeClass("sify-down");
            a.each(function (a, b) {
                var c = $(b);
                "..." === c.text() && c.remove();
                c.hasClass("hide") && c.removeClass("hide")
            })
        });
        b = $(".comment-list li");
        b.size() && b.click(function () {
            _gaq.push(["_trackEvent", "shopinfo_reviewdetail", "click"]);
            _hip.push(["mv", {
                module: "shopinfo_reviewdetail",
                action: "click"
            }]);
            var b = $(this),
                a = b.find(".comment-entry"),
                b = b.find(".J_see_more"),
                d = b.attr("data-content");
            b.remove();
            a.html(d)
        })
    },
    collect: function () {
        function b(a) {
            var b = a.parent(),
                d = a.attr("data-shopId"),
                h = a.attr("data-shopType"),
                j = a.attr("data-shopName");
            _gaq.push(["_trackEvent", "shopinfo_favo", "click", "添加收藏|" + d + "|" + j]);
            _hip.push(["mv", {
                module: "shopinfo_favo",
                action: "click",
                shopid: d,
                content: "添加收藏"
            }]);
            $.ajax({
                type: "POST",
                url: "/favorite/add",
                data: {
                    shopId: d,
                    shopName: j,
                    shopType: h
                },
                dataType: "json",
                success: function (i) {
                    var k = i.code;
                    200 === k ? (a.remove(), b.append('<a id="J_cancelCollect" href="javascript:;" title="已收藏" data-shopId="' + d + '" data-shopType="' + h + '" data-shopName="' + j + '"><i class="head-already"></i>已收藏</a>'), DP.Notification.flashBox(i.message.message, 1E3), $("#J_cancelCollect").on("click", function () {
                        c($(this))
                    })) : 500 === k ? DP.Notification.flashBox(i.message.message, 1E3) : 301 === k && (window.location.href = "/login?redir=" + i.redir)
                }
            })
        }

        function c(a) {
            var c = a.parent(),
                d = a.attr("data-shopId"),
                h = a.attr("data-shopType"),
                j = a.attr("data-shopName");
            _gaq.push(["_trackPageview", "shopinfo_favo_cancel"]);
            _gaq.push(["_trackEvent", "shopinfo_favo", "click", "取消收藏|" + d + "|" + j]);
            _hip.push(["mv", {
                module: "shopinfo_favo",
                action: "click",
                shopid: d,
                content: "取消收藏"
            }]);
            $.ajax({
                type: "POST",
                url: "/favorite/remove",
                data: {
                    shopId: d
                },
                dataType: "json",
                success: function (i) {
                    var k = i.code;
                    200 === k ? (a.remove(), c.append('<a id="J_collect" href="javascript:;" title="收藏" data-shopId="' + d + '" data-shopType="' + h + '" data-shopName="' + j + '"><i class="head-collect"></i>收藏</a>'), DP.Notification.flashBox(i.message.message,
                        1E3), $("#J_collect").on("click", function () {
                        b($(this))
                    })) : 500 === k ? DP.Notification.flashBox(i.message.message, 1E3) : 301 === k && (window.location.href = "/login?redir=" + i.redir)
                }
            })
        }
        var a = $("#J_collect"),
            d = $("#J_cancelCollect");
        d.size() && d.on("click", function (a) {
            a && a.preventDefault();
            a = $(this);
            c(a)
        });
        a.size() && a.on("click", function (a) {
            a && a.preventDefault();
            a = $(this);
            b(a)
        })
    },
    sendSMS: function () {
        var b = $("#J_sendSMS"),
            c = b.attr("data-promoId");
        b.on("click", function () {
            _gaq.push(["_trackEvent", "coupon_detail_download",
                "click"
            ]);
            var a = DP.Notification.formBox("<p>请输入手机号</p>", {
                content: '<p><input type="number" name="" class="inp-stor"  placeholder="输入用户手机号"></p><p class="msg-holder"></p>',
                useBtns: !0,
                sureText: "提交",
                callback: function (b) {
                    var e = $(b.text),
                        b = e.find("input").val(),
                        e = e.find(".msg-holder");
                    "" === b.trim() ? e.text("手机号码不能为空") : /^(1[3-9][0-9])\d{8}$/.test(b) ? $.ajax({
                        type: "POST",
                        url: "/promo/sendSMS",
                        data: {
                            mobile: b,
                            promoid: c
                        },
                        dataType: "json",
                        success: function (b) {
                            var c = b.code;
                            200 === c ? (a.close(), DP.Notification.flashBox(b.message.message,
                                1E3)) : 500 === c && (a.close(), DP.Notification.flashBox(b.message.message, 1E3))
                        }
                    }) : e.text("请输入正确的手机号码格式")
                }
            })
        })
    },
    shopError: function () {
        var b = $("#j-content"),
            c = $("#j-email"),
            a = $("#j-flag");
        $("#j-submit").on("click", function () {
            var d = $(this).attr("data-id");
            "" == b.val().trim() ? DP.Notification.flashBox("内容不能为空", 1E3) : "" == c.val().trim() ? DP.Notification.flashBox("邮箱不能为空", 1E3) : /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/.test(c.val().trim()) ? $.ajax({
                type: "POST",
                url: "/shop/feedback",
                data: {
                    content: b.val().trim(),
                    email: c.val().trim(),
                    flag: a.val(),
                    shopId: d
                },
                dataType: "json",
                success: function (a) {
                    var b = a.code;
                    200 === b ? (_gaq.push(["_trackEvent", "feedbackshop_success", "click", 1]), DP.Notification.flashBox(a.message.message, 3E3), window.setTimeout(function () {
                        window.location.href = "/shop/" + d
                    }, 3E3)) : 400 === b && (_gaq.push(["_trackEvent", "feedbackshop_success", "click", 0]), DP.Notification.flashBox(a.message.message, 1E3))
                }
            }) : DP.Notification.flashBox("邮箱格式有误", 1E3)
        })
    },
    shopInfoEdit: function () {
        var b = $("#j-shopname"),
            c = $("#j-channel"),
            a = $("#j-address"),
            d = $("#j-phone"),
            e = $("#j-businessHour"),
            f = $("#j-email"),
            g = $("#j-flag");
        $("#j-submit").on("click", function () {
            var h = $(this).attr("data-id");
            "" == b.val().trim() ? DP.Notification.flashBox("商户名不能为空", 1E3) : "" == c.val().trim() ? DP.Notification.flashBox("频道不能为空", 1E3) : "" == a.val().trim() ? DP.Notification.flashBox("地址不能为空", 1E3) : d.val().trim() && !/^(\d|-)+$/.test(d.val().trim()) ? DP.Notification.flashBox("电话号码或手机号码格式不正确", 1E3) : f && f.val().trim() && !/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/.test(f.val().trim()) ?
                DP.Notification.flashBox("邮箱格式有误", 1E3) : $.ajax({
                    type: "POST",
                    url: "/shop/feedback",
                    data: {
                        shopName: b.val().trim(),
                        categoryId: c.attr("data-id"),
                        address: a.val().trim(),
                        phone: d.val().trim(),
                        businessHour: e.val().trim(),
                        email: f.val().trim(),
                        flag: g.val(),
                        shopId: h
                    },
                    dataType: "json",
                    success: function (a) {
                        var b = a.code;
                        200 === b ? (_gaq.push(["_trackEvent", "editshop_success", "click", 1]), DP.Notification.flashBox(a.message.message, 3E3), window.setTimeout(function () {
                            window.location.href = "/shop/" + h
                        }, 3E3)) : 400 === b && (_gaq.push(["_trackEvent",
                            "editshop_success", "click", 0
                        ]), DP.Notification.flashBox(a.message.message, 1E3))
                    }
                })
        })
    },
    shopInfoAdd: function () {
        var b = $("#j-shopname"),
            c = $("#j-channel"),
            a = $("#j-address"),
            d = $("#j-phone"),
            e = $("#j-businessHour");
        $("#j-submit").on("click", function () {
            $(this).attr("data-id");
            "" == b.val().trim() ? DP.Notification.flashBox("商户名不能为空", 1E3) : "" == c.val().trim() ? DP.Notification.flashBox("频道不能为空", 1E3) : "" == a.val().trim() ? DP.Notification.flashBox("地址不能为空", 1E3) : "" != d.val().trim() && !/^(\d|-)+$/.test(d.val().trim()) ? DP.Notification.flashBox("电话号码或手机号码格式不正确",
                1E3) : $.ajax({
                type: "POST",
                url: "/shop/addshop_submit",
                data: {
                    shopName: b.val().trim(),
                    categoryId: c.attr("data-id"),
                    address: a.val().trim(),
                    phone: d.val().trim(),
                    businessHours: e.val().trim()
                },
                dataType: "json",
                success: function (a) {
                    var b = a.code;
                    200 === b ? (_gaq.push(["_trackEvent", "addshop_success", "click", 1]), DP.Notification.flashBox(a.message.message, 3E3), window.setTimeout(function () {
                        window.location.href = DP.myURL
                    }, 3E3)) : 400 === b && (_gaq.push(["_trackEvent", "addshop_success", "click", 0]), DP.Notification.flashBox(a.message.message,
                        1E3))
                }
            })
        })
    },
    closeShop: function () {
        $("#j-shop-close").on("click", function () {
            var b = $(this).attr("data-id");
            window.confirm("确认商户已关闭？") && (DP.overlay.hideLeft(), $.ajax({
                type: "POST",
                url: "/shop/feedback",
                data: {
                    shopId: b,
                    flag: 2
                },
                dataType: "json",
                success: function (b) {
                    var a = b.code;
                    200 === a ? (DP.Notification.flashBox("提交成功！感谢您对点评的支持。", 3E3), window.setTimeout(function () {
                        window.location.reload()
                    }, 3E3)) : 400 === a && DP.Notification.flashBox(b.message.message, 1E3)
                }
            }))
        })
    },
    getDimensions: function () {
        var b = 0,
            c = 0;
        !DP.Cookie.get("winwidth") &&
            (!DP.Cookie.get("winheight") && document.documentElement && document.documentElement.clientHeight && document.documentElement.clientWidth) && (b = document.documentElement.clientWidth, c = document.documentElement.clientHeight, DP.Cookie.set("winwidth", b, 5256E4, "/"), DP.Cookie.set("winheight", c, 5256E4, "/"))
    },
    duplicateShop: function () {
        $("#j-duplicate").on("click", function () {
            var b = $(this).attr("data-id");
            window.confirm("确认商户重复？") && (DP.overlay.hideLeft(), $.ajax({
                type: "POST",
                url: "/shop/feedback",
                data: {
                    shopId: b,
                    flag: 3
                },
                dataType: "json",
                success: function (b) {
                    var a = b.code;
                    200 === a ? (DP.Notification.flashBox("提交成功！感谢您对点评的支持。", 3E3), window.setTimeout(function () {
                        window.location.reload()
                    }, 3E3)) : 400 === a && DP.Notification.flashBox(b.message.message, 1E3)
                }
            }))
        })
    },
    mapShop: function () {
        $("#j-location").on("click", function () {
            var b = $(this).attr("data-id");
            window.confirm("确认商户位置错误？") && (DP.overlay.hideLeft(), $.ajax({
                type: "POST",
                url: "/shop/feedback",
                data: {
                    shopId: b,
                    flag: 4
                },
                dataType: "json",
                success: function (b) {
                    var a = b.code;
                    200 === a ? (DP.Notification.flashBox("提交成功！感谢您对点评的支持。",
                        3E3), window.setTimeout(function () {
                        window.location.reload()
                    }, 3E3)) : 400 === a && DP.Notification.flashBox(b.message.message, 1E3)
                }
            }))
        })
    },
    mylistDetailShowAndPackup: function (b) {
        $("#mylist_header").click(function () {
            var c = $("#mylist_introduce"),
                a = $("#mylist_introduce_action");
            if (a) {
                var d = a.attr("data-content-all"),
                    e = a.attr("data-content-part"),
                    f = a.attr("actionType"),
                    a = a.clone();
                "showAll" == f ? (_gaq.push(["_trackEvent", "album_moreinfo", "click", "展开", b]), a.attr("actionType", "showPart"), a.attr("class", "J_see_more sify-up"),
                    c.empty(), c.append(d), c.append(a)) : "showPart" == f && (_gaq.push(["_trackEvent", "album_moreinfo", "click", "收起", b]), a.attr("actionType", "showAll"), a.attr("class", "J_see_more sify-down"), c.empty(), c.append(e), c.append(a))
            }
        });
        $(".bulletin-detail li .J_see_more").click(function () {
            var c = $(this).find(".push-reason"),
                a = $(this),
                d = a.attr("data-content-all"),
                e = a.attr("data-content-part"),
                f = a.attr("actionType"),
                g = a.find("i");
            "showAll" == f ? (_gaq.push(["_trackEvent", "album_iteminfo", "click", "展开", b]), a.attr("actionType",
                "showPart"), g.attr("class", "sify-up"), c.empty(), c.append("<strong>推荐理由：</strong>"), c.append(d), c.append('<span class="bare"></span>')) : "showPart" == f && (_gaq.push(["_trackEvent", "album_iteminfo", "click", "收起", b]), a.attr("actionType", "showAll"), g.attr("class", "sify-down"), c.empty(), c.append("<strong>推荐理由：</strong>"), c.append(e), c.append('<span class="bare"></span>'))
        })
    },
    metroFold: function (b) {
        b.each(function (b, a) {
            var d = $(a);
            d.on("click", function (a) {
                a && a.preventDefault();
                $(".son-classify").toggle(!1);
                "0" ===
                    d.attr("status") ? ($("i.sify-up").removeClass("sify-up").addClass("sify-down"), $(this).next(".son-classify").toggle(!0), $(this).find("i").removeClass("sify-down").addClass("sify-up"), $(".sify-bg").attr("status", 0), d.attr("status", "1")) : ($("i.sify-up").removeClass("sify-up").addClass("sify-down"), $(".sify-bg").attr("status", 0))
            })
        })
    },
    scoreQuestionnaire: function () {
        $("#symbol-q").click(function () {
            b()
        });
        $("em.close").click(function () {
            b()
        });
        $("#score-q").click(function () {
            b()
        });
        var b = function () {
            $(".desc-help").remove();
            $(".abnormal-verify-succ").remove();
            DP.Cookie.set("scoreque", 1, 5256E4, "/")
        }
    },
    localStreet: function (b, c) {
        var a = $(".loc-bar");
        $.ajax({
            type: "get",
            url: "/getlocalstreet",
            data: {
                lat: b,
                lng: c
            },
            dataType: "json",
            beforeSend: function () {},
            success: function (b) {
                200 === b.code && (a.html('<div class="loc-place">' + b.message.street + '</div><a onclick="DP.app.refresh()" href="javascript:;" class="loc-refresh-btn" id="j-refresh"></a>'), a.toggle(!0))
            },
            error: function () {}
        })
    },
    picOverlay: function (b, c, a) {
        b.on("click", function () {
            DP.overlay.showPic(this,
                c);
            $("body > div").click(function (a) {
                a = a.target || a.srcElement;
                "DIV" === a.nodeName.toUpperCase() && "leftmask" == $(a).attr("id") && ($(a).remove(), DP.overlay.hidePic(c))
            })
        });
        a && a()
    },
    shareLink: function (b, c) {
        ZeroClipboard.setMoviePath(c);
        clip = new ZeroClipboard.Client;
        clip.setHandCursor(!0);
        clip.setText(location.href);
        clip.addEventListener("complete", function () {
            DP.Notification.flashBox("已复制", 1E3)
        });
        clip.glue(b)
    },
    initPopShare: function (b, c, a) {
        function d() {
            f.css({
                left: (e.width() - f.width()) / 2,
                top: (e.height() - f.height()) /
                    2
            })
        }
        var e = $(window),
            f = c || $("div.pop-main"),
            g = a || $("div.pop-main-wrap");
        b.on("click", function () {
            f.show();
            g.show();
            d()
        });
        f.find("#share_close").click(function () {
            f.hide();
            g.hide()
        });
        e.on("resize", d)
    },
    choosePicCate: function (b) {
        $(".genre-icon").remove();
        "菜" == $("#genre" + b).html() ? $("#priceSpan").show() : $("#priceSpan").hide();
        $("<i class='genre-icon'></i>").appendTo($("#genre" + b))
    },
    addPicinfo: function (b) {
        $("img").parent().parent().removeClass("cur");
        $("#imghead" + b).parent().parent().addClass("cur");
        $(".upload-lay-box").css("top",
            $(".cur").position().top + $(".cur").height() + "px");
        $(".upload-lay-box").css("position", "static").show();
        $("#priceSpan").hide();
        $("#picInfoCnt").val(b)
    },
    addPicSingleInfoSub: function () {
        var b = $(".genre-icon").closest("li");
        b.length && ($("#tagType").val(b.find("input").val()), $("#tagTypeName").val(b.find("a").text()));
        $("#photoTitle").val($("#photoTitlelay").val());
        $("#price").val($("#pricelay").val());
        $(".upload-lay-box").hide()
    },
    addPicInfoSub: function () {
        var b = $("#picInfoCnt").val();
        if (null != $(".genre-icon").parent().html()) {
            var c =
                $(".genre-icon").parent().html().split("<i")[0],
                a = $(".genre-icon").parent().next().val();
            $("#tagName" + b).val(c);
            $("#tagNametype" + b).val(a)
        } else $("#tagName" + b).val(-1), $("#tagNametype" + b).val(-1);
        null != $("#pricelay").val() && "" != $("#pricelay").val() && $("#price" + b).val($("#pricelay").val());
        null != $("#photoTitlelay").val() && "" != $("#photoTitlelay").val() && $("#photoTitle" + b).val($("#photoTitlelay").val());
        $("img").parent().parent().addClass("cur");
        $(".upload-lay-box").hide();
        $(".genre-icon").remove();
        $("#photoTitlelay").val("输入图片名称");
        $("#pricelay").val("价格")
    },
    removepic: function () {
        $("#imghead" + count).parent().parent().remove();
        $("#imgDiv" + count).remove()
    },
    addPicSingle: function () {
        function b() {
            c.click()
        }
        var c = $("#file");
        navigator.userAgent.match("Android") ? setTimeout(b, 0) : b();
        c.on("change", DP.app.uploadIframe)
    },
    addPic: function () {
        function b() {
            $("#file" + count).click()
        }
        var c = $("<div id='imgDiv" + count + "'><input class='uploadfile' accept='image/*' type='file'  name='file' id='file" + count + "'/><input id='tagNametype" +
            count + "' name='tagNametype' value='-1' /><input id='tagName" + count + "' name='tagName' value='-1' /><input id='price" + count + "' name='price'  value='价格' /><input id='photoTitle" + count + "'  name='photoTitle' placeholder='输入图片名称' value='输入图片名称' /></div>").appendTo($("form")).find(".uploadfile");
        navigator.userAgent.match("Android") ? setTimeout(b, 0) : b();
        c.on("change", DP.app.previewImage)
    },
    getImageSize: function (b, c) {
        var a = new Image;
        a.onload = function () {
            c({
                height: a.height,
                width: a.width
            })
        };
        a.src = b
    },
    showLoading: function () {
        if (!DP.app.loadingPanel) {
            $(".pop-main-wrap").removeClass("hide");
            var b = $('<div class="pop-main"><div class="pop-con" style="padding:10px"><div class="loading"></div></div></div>');
            b.appendTo($("body"));
            b.css({
                position: "absolute",
                top: ($(window).height() - b.height()) / 2,
                left: ($(window).width() - b.width()) / 2
            });
            DP.app.loadingPanel = b
        }
    },
    removeLoading: function () {
        $(".pop-main-wrap").addClass("hide");
        DP.app.loadingPanel && DP.app.loadingPanel.remove();
        DP.app.loadingPanel = null
    },
    uploadIframe: function () {
        document.getElementById("preview");
        var b = $("#upload-iframe"),
            c = $("#file")[0];
        b.length || (b = $('<iframe id="upload-iframe" name="upload-iframe"/>').css("visibility", "hidden").appendTo($("body")));
        c.files.length && c.files[0].name && (window._iframeUploadDone = DP.app.iframeUploadDone, $("form")[0].submit(), DP.app.iframeTimeout && clearTimeout(DP.app.iframeTimeout), DP.app.iframeTimeout = setTimeout(function () {
            DP.app.iframeUploadDone({
                code: 500,
                msg: "上传超时"
            })
        }, 9E4), DP.app.showLoading())
    },
    iframeUploadDone: function (b) {
        var c = b.code,
            a = b.msg,
            d = b.url,
            b = b.picid;
        clearTimeout(DP.app.iframeTimeout);
        DP.app.removeLoading();
        var e = $("form");
        200 == c ? (c = $("<li><img src='" + d + "'/></li>"), c.find("img").css({
            width: 90,
            height: 90
        }), e.attr("target", ""), e.attr("action", "/shop/uploadpic/single/addinfo"), $("#preview").empty().append(c), $(".upload-lay-box").css("position", "static").show(), $("#picId").val(b)) : ($("#file").val(""), alert(a))
    },
    previewImage: function () {
        document.getElementById("preview");
        var b = $("#file" + count)[0];
        if (b.files && 0 < b.files.length)
            for (var c = 0; c < b.files.length; c++) {
                var a = new FileReader;
                a.onload = function (a) {
                    return function (b) {
                        a.name.match(/\.(bmp|png|jpeg|jpg)$/) ?
                            DP.app.getImageSize(b.target.result, function (a) {
                                if (300 > a.width || 300 > a.height) this.value = null, $("#file" + count).remove(), $("#imgDiv" + count).remove(), alert("图片尺寸不低于300*300");
                                else {
                                    $("<li class='cur'><div class='pic'><img id='imghead" + count + "' src='" + b.target.result + "'/><a href='javascript:;' id='closepic" + count + "' class='pic-close'></a></div><p><a href='javascript:;' id='addpicinfo" + count + "'  class='picture-tips'>添加图片信息</a></p><span class='picture-hide'></span></li>").insertBefore($("#addSign"));
                                    var c =
                                        count;
                                    $("a#addpicinfo" + c).click(function (a) {
                                        a && a.preventDefault();
                                        DP.app.addPicinfo(c)
                                    });
                                    $("a#closepic" + c).click(function (a) {
                                        a && a.preventDefault();
                                        DP.app.removepic(c)
                                    });
                                    var d = document.getElementById("imghead" + c);
                                    d.onload = function () {
                                        var a = DP.app.clacImgZoomParam(90, 90, d.offsetWidth, d.offsetHeight);
                                        d.width = a.width;
                                        d.height = a.height;
                                        d.style.marginLeft = a.left + "px";
                                        d.style.marginTop = a.top + "px"
                                    };
                                    count++;
                                    9 <= $(".uploadfile").size() && $("#addSign").addClass("hide")
                                }
                            }) : ($("#file" + count).remove(), $("#imgDiv" +
                                count).remove(), alert("请选择图片文件"))
                    }
                }(b.files[c]);
                a.readAsDataURL(b.files[c])
            } else $("#imgDiv" + count).remove()
    },
    clacImgZoomParam: function (b, c, a, d) {
        var e = {
            top: 0,
            left: 0,
            width: a,
            height: d
        };
        if (a > b || d > c) rateWidth = a / b, rateHeight = d / c, rateWidth > rateHeight ? (e.width = b, e.height = Math.round(d / rateWidth)) : (e.width = Math.round(a / rateHeight), e.height = c);
        e.left = Math.round((b - e.width) / 2);
        e.top = Math.round((c - e.height) / 2);
        return e
    }
};