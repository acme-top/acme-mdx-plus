$().ready(function () {
    // 初始化文章目录
    $('.PostMain article').mdxToc({
        // 目录激活时的样式，返回的格式必须为Jquery可用的cssText格式
        getActiveStyle: function () {
            var theme_color = $("meta[name='theme-color']").attr('content');
            return "color: " + theme_color + " !important; border-left-color: " + theme_color;
        },
        // 目录未激活时的样式，返回的格式必须为Jquery可用的cssText格式
        getNotActiveStyle: function () {
            return "color: #2f2f2f; border-left-color: #cacaca;";
        },
        // 获取固定位置是距离顶部的位置
        getFixedTop: function () {
            if ($(".titleBarGobal").hasClass("mdui-headroom-unpinned-top")) {
                return 10;
            }
            return $(".titleBarGobal").height() + 10;
        },
        // 获取距离顶部的距离
        getTop: function () {
            if ($(".PostTitleFillBack2").length > 0) {
                return $(".PostTitleFillBack2").height() + 10;
            }
            return $(".PostTitleFill").height() + 10;
        },
        // 获取距离右侧的距离
        getLeft: function () {
            return $(".PostMain article").offset().left + $(".PostMain article").width() + parseFloat($(".PostMain article").css("margin-right")) + 10;
        }
    });
});