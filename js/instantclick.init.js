InstantClick.on("change", function () {
    $(window).off("scroll", "**");
    $(window).trigger("load");
});

InstantClick.init("mousedown");

