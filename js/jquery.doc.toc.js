/**
 * @description 在jquery.titleNav.js的基础上进行了修改，以适应MDX博客主题
 * @author acme.top
 * @version v1.0.0
 * @createtime 2018-09-30
 * @license MIT 可以复制和商用，但需要保留此段版权申明
 */

/**
 * @description 标题自动生成导航jQuery小插件
 * @author zhangxinxu(.com)
 * @version v1.0.0
 * @createtime 2018-04-27
 * @license MIT 可以复制和商用，但需要保留此段版权申明
 */

/**
 * 标题自动生成导航jQuery小插件
 * @param  Object options 可选参数
 * @return {[type]}         [description]
 */
$.fn.mdxToc = function (options) {

    var defaults = {
        // 默认导航元素
        nav: null,
        // 滚动容器
        container: $(window),
        // 目录激活时的样式，返回的格式必须为Jquery可用的cssText格式
        getActiveStyle: function () {
            return "color: #2f2f2f !important; border-left-color: #2f2f2f; font-weight: 500;";
        },
        // 目录未激活时的样式，返回的格式必须为Jquery可用的cssText格式
        getNotActiveStyle: function () {
            return "color: #8E8E8E; border-left-color: #cacaca; font-weight: 400;";
        },
        // 获取固定位置是距离顶部的位置
        getFixedTop: function () {
            return 20;
        },
        // 获取距离顶部的距离
        getTop: function () {
            return 200;
        },
        // 获取距离右侧的距离，优先级比getLeft()要低
        getRight: function () {
            return 10;
        },
        // 获取距离左侧的距离
        getLeft: null,
        // 计算位置
        // @param scrollTop 当前滚动的高度
        calPosition: function (scrollTop, defaultOffsetTop) {
            // 目前滚动的高度
            if (scrollTop == undefined) {
                scrollTop = $(this).scrollTop();
            }

            if (defaultOffsetTop == undefined) {
                defaultOffsetTop = this.getTop();
            }

            var offsetTop = this.getFixedTop();

            // 导航的fixed行为
            // 如果滚动足够多，固定定位
            if (scrollTop - defaultOffsetTop > -1 * offsetTop) {
                nav.css({
                    top: offsetTop,
                    position: 'fixed'
                });
            } else {
                nav.css({
                    top: defaultOffsetTop,
                    position: ''
                });

                if ($.isFunction(this.getLeft)) {
                    nav.css({
                        left: this.getLeft(),
                    });
                } else if ($.isFunction(this.getRight)) {
                    nav.css({
                        right: this.getRight()
                    });
                } else {
                    console.error("未定义getRight()或者getLeft()");
                }
            }
        }
    };

    var params = $.extend({}, defaults, options || {});

    // 切换类名
    var ACTIVE = 'active';

    // 激活状态下的样式
    var active_style = params.getActiveStyle();
    // 未激活状态下的样式
    var not_active_style = params.getNotActiveStyle();

    var element = $(this);

    // 滚动容器
    var container = params.container;

    if (!container.length) {
        return element;
    }

    if ($(element).find(":header").length == 0) {
        return element;
    }

    // 标题元素和导航元素
    var target = $(this).find("h" + params.start_level);

    var nav = params.nav;

    var isContainerWindow = (container[0] == window);

    if (!nav) {
        nav = $('<div class="doc-toc"></div>');
        nav_clone = $('<div class="doc-toc"></div>');
    }

    // 初始化位置
    params.calPosition();

    if (nav.html() == '') {

        var headers = {
            'h1': 1,
            'h2': 2,
            'h3': 3,
            'h4': 4,
            'h5': 5,
            'h6': 6
        };

        var el_ul = $("<ul></ul>");

        // 如果是空导航，自动创建
        $(element).find(":header").each(function () {
            var href = this.id ? ('#' + this.id) : 'javascript:;';

            var el_li = $('<li></li>').append($('<a href="' + href + '" title="' + (this.textContent || this.innerHTML) + '">' + this.innerHTML + '</a>').data('target', $(this)));

            el_ul.append(el_li);
        });

        nav.append(el_ul);

        target = $(element).find(":header");
    }

    // 导航append到页面中
    if (document.body.contains(nav[0]) == false) {
        if (isContainerWindow) {
            $('body').append(nav);
        } else {
            container.append(nav);
        }
    }

    // 偏移位置
    var defaultOffsetTop = params.getTop();

    // 改变窗口大小重新设置定位
    container.on('resize', function () {

        // 目前滚动的高度
        var scrollTop = $(this).scrollTop();

        defaultOffsetTop = params.getTop();

        // 重新计算位置
        params.calPosition(scrollTop, defaultOffsetTop);
    });

    // 滚动行为
    container.on('scroll', function () {

        var indexNav = 0;
        var rectTopContainer = isContainerWindow ? 0 : container[0].getBoundingClientRect().top;
        // 目前滚动的高度
        var scrollTop = $(this).scrollTop();
        // 滚动到底部，一定是最后一个
        // 容器内部高度
        var scrollHeight = document.documentElement.scrollHeight || document.body.scrollHeight;
        if (!isContainerWindow) {
            scrollHeight = container[0].scrollHeight;
        }
        if (container.height() + scrollTop >= scrollHeight - 1) {
            indexNav = target.length - 1;
        } else {
            // 遍历每个标题距离浏览器窗体上边缘的位置
            target.each(function (index) {
                var distanceToTop = this.getBoundingClientRect().top - rectTopContainer;
                if (distanceToTop >= 0 || index === target.length - 1) {
                    indexNav = index;
                    return false;
                }
            });
        }

        // 获取目前需要高亮的导航元素
        var elNavs = nav.find("li");
        var elTargetNav = elNavs.eq(indexNav);
        if (elTargetNav.hasClass(ACTIVE) == false) {
            elNavs.removeClass(ACTIVE).css("cssText", not_active_style);
            elTargetNav.addClass(ACTIVE).css("cssText", active_style);
        }

        // 重新计算位置
        params.calPosition(scrollTop, defaultOffsetTop);
    });

    // 导航点击行为
    $(nav).on('click', 'a', function (event) {
        var href = $(this).attr('href');
        var target = $(this).data('target') || $(href);
        // 导航索引
        var indexNav = target.find('a').index($(this));
        if (!target.length) {
            target = target.eq(indexNav);
        }
        if (/^#/.test(href)) {
            event.preventDefault();
        }

        var scrollHeight = document.documentElement.scrollHeight || document.body.scrollHeight;
        if (!isContainerWindow) {
            scrollHeight = container[0].scrollHeight;
        }

        var rectTopContainer = isContainerWindow ? 0 : container[0].getBoundingClientRect().top;
        var scrollTop = target.offset().top - rectTopContainer;
        // 一屏有多个标题，同时滚动到底部的处理
        if (scrollTop + container.height() > scrollHeight - 2 && indexNav !== target.find('a').length - 1) {
            scrollTop = scrollHeight - container.height() - 2;
        }

        var scrollContainer = isContainerWindow ? $('html, body') : container;

        // 以动画方式滚动定位
        scrollContainer.animate({
            scrollTop: scrollTop - params.getFixedTop()
        });
    });

    container.trigger('scroll');

    var nav_clone = nav.clone(true);

    // 初始化位置
    nav_clone.css({
        // 'display': 'none',
        'position': 'static',
    });

    $(element).prepend(nav_clone);

    return target;
};