!function ($) {

    $(window).load(function () {
        $('#re-s').submit(function () {
            window.location = segment + "/" + $("#re-s input").val();
            return false;
        });
        $("#publicmodlog").change(function () {
            if (!$("#publicmodlog").is(":checked")) {
                $("#usrinfo").animate({"opacity":"1", "height":"220px"}, "500");
                $("#usrinfo").css({"display":"block"});
            }
            else {
                $("#usrinfo").animate({"opacity":"0", "display":"none", "height":"0px"}, '500', function () {
                    $("#usrinfo").css({"display":"none"});
                });
            }
        });
        $("#publicmodlog").change();
        $("#deleteform").submit(function () {
            if ($("#yolo").is(".in"))
                return true;
            else {
                $("#yolo").modal();
                return false;
            }
        });
    });
    $(function () {
        //stolen from bootswatch.com :D

        // fix sub nav on scroll
        var $win = $(window)
            , $nav = $('.subnav')
            , navHeight = $('.navbar').first().height()
            , navTop = $('.subnav').length && $('.subnav').offset().top - navHeight
            , isFixed = 0

        processScroll()

        $win.on('scroll', processScroll)

        function processScroll() {
            var i, scrollTop = $win.scrollTop()
            if (scrollTop >= navTop && !isFixed) {
                isFixed = 1
                $nav.addClass('subnav-fixed')
            } else if (scrollTop <= navTop && isFixed) {
                isFixed = 0
                $nav.removeClass('subnav-fixed')
            }
        }

    })

}(window.jQuery)