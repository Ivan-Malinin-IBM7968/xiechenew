//网站首页用户的论坛推荐精华贴
var Bitauto = Bitauto || {};
Bitauto.Forum = Bitauto.Forum || {};
Bitauto.Forum.UserForum = Bitauto.Forum.UserForum || {
    init: function () {
        if (Bitauto.UserCars.isLogin()) {
            if (document.getElementById("userForumLogin")) {
                document.getElementById("userForumLogin").style.display = "none";
            }
        }
        else {
            if (document.getElementById("userForumLogin")) {
                document.getElementById("userForumLogin").style.display = "";
            }
        }
    }
}
//初始化
Bitauto.Forum.UserForum.init();