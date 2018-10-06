$().ready(function () {

    // 设置主题颜色
    $(".sideImg").addClass("mdui-color-theme");

    var theme_color = $("meta[name='theme-color']").attr('content');

    // 初始化文章目录
    $('.PostMain article').mdxToc({
        // 活动状态发生改变时的事件
        activeChange: function (elements, active_element) {
            $(elements).css("cssText", "color: #2f2f2f; border-left-color: #cacaca;");
            $(active_element).css("cssText", "color: " + theme_color + " !important; border-left-color: " + theme_color + " !important;");
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
            } else if ($(".PostTitleFillPage").length > 0) {
                return $(".PostTitleFillPage").height() + 10;
            }
            return $(".PostTitleFill").height() + 10;
        },
        // 获取距离右侧的距离
        getLeft: function () {
            return $(".PostMain article").offset().left + $(".PostMain article").width() + parseFloat($(".PostMain article").css("margin-right")) + 10;
        }
    });
});