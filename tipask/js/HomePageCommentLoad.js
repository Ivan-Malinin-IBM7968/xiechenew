var hpclPath = "/ashx/UserCommentLoad.ashx?jsoncallback=?&Date=" + new Date() + "&type=";
var hpclT1 = "<ul><li class='ppTitle2 font14'>·";
//Title
var hpclT2 = "</li><li class='ppHf'>";
//ReplyDes
var hpclT3 = "</li><li class='ppT'>";
//NickName
var hpclT4 = "</li><li class='ppDate alignCenter'>";
//PostTime
var hpclT5 = "</li></ul>";
//String Buffer
var hpclBuff;


function hpclDataBind(type, container) {
    if ($(container)[0].innerHTML == "") {
        //UnResolved HTML build
        $.getJSON(hpclPath + type, function(data) {
            hpclBuff = "";
            $.each(data, function(k, v) {
                hpclBuff = hpclBuff + (hpclT1 + v.Title + hpclT2 + v.ReplyDes + hpclT3 + v.NickName + hpclT4 + v.PostTime + hpclT5)
            });

            $(container).html(hpclBuff);
            hpclSwitch(container);
            //5 rows split
            $("div.ulclass").each(function() {
                $(this).find("ul").each(function(i) {
                    if (i % 5 == 0 && i != 0) {
                        $(this).css("padding-top", "10px")
                    }
                })
            })
            //
        });
    } else {
        hpclSwitch(container);
    }
}

function hpclSwitch(container) {
    $("#UserCommentAll").css("display", "none");
    $("#UserReplyCommentListUnResolved").css("display", "none");
    $("#UserReplyCommentListResolved").css("display", "none");
    $("#UserEncourageComment").css("display", "none");
    $(container).css("display", "block");
}

$(document).ready(function() {
    $("#UserReplyCommentListUnResolved ul:last").css("border-bottom-style", "none");
    $("#UserReplyCommentListResolved ul:last").css("border-bottom-style", "none");
    $("#UserCommentAll ul:last").css("border-bottom-style", "none");
    $("#UserEncourageComment ul:last").css("border-bottom-style", "none");
});
//全部
$("#liUserCommentAll").click(function() {
    $("#UserCommentAll").css("display", "block");
    $("#UserReplyCommentListUnResolved").css("display", "none");
    $("#UserReplyCommentListResolved").css("display", "none");
    $("#UserEncourageComment").css("display", "none");
    $("#liResolvedPerson").html("提问人");
    $("#liResolvedDateTime").html("提问时间");
    document.getElementById("liUnResolved").className = "tabQuestionb";
    document.getElementById("liResolved").className = "tabQuestionb";
    document.getElementById("liEncourage").className = "tabQuestionb";
    document.getElementById("liUserCommentAll").className = "tabQuestiona";
    $("#aUserCommentMore").attr("href", "/UserCommentInfo_C0.html");
});
//未解决
$("#liUnResolved").click(function() {
    hpclDataBind("UnResolved", "#UserReplyCommentListUnResolved");

    $("#liResolvedPerson").html("提问人");
    $("#liResolvedDateTime").html("提问时间");

    document.getElementById("liUserCommentAll").className = "tabQuestionb";
    document.getElementById("liResolved").className = "tabQuestionb";
    document.getElementById("liEncourage").className = "tabQuestionb";
    document.getElementById("liUnResolved").className = "tabQuestiona";
    $("#aUserCommentMore").attr("href", "/?c-all/1.html");
});
//已解决
$("#liResolved").click(function() {
    hpclDataBind("Resolved", "#UserReplyCommentListResolved");

    //$("#liResolvedPerson").html("回复人");
    //$("#liResolvedDateTime").html("回复时间");

    document.getElementById("liUnResolved").className = "tabQuestionb";
    document.getElementById("liUserCommentAll").className = "tabQuestionb";
    document.getElementById("liEncourage").className = "tabQuestionb";
    document.getElementById("liResolved").className = "tabQuestiona";
    $("#aUserCommentMore").attr("href", "/?c-all/2.html");
});
//悬赏
$("#liEncourage").click(function() {
    hpclDataBind("Encourage", "#UserEncourageComment");

    $("#liResolvedPerson").html("提问人");
    $("#liResolvedDateTime").html("提问时间");

    document.getElementById("liUserCommentAll").className = "tabQuestionb";
    document.getElementById("liResolved").className = "tabQuestionb";
    document.getElementById("liUnResolved").className = "tabQuestionb";
    document.getElementById("liEncourage").className = "tabQuestiona";

    $("#aUserCommentMore").attr("href", "/?c-all/4.html");
});

