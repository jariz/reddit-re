var thiz;
!function ($) {

    function fstring2array(fstring) {
        var arr = fstring.split("/");
        for(var i = 0; i < arr.length; i++) {

        }
    }

    function array2fstring(array) {

    }

    function getFilterName($filter) {
        switch ($filter) {
            case "r":
                return "With subreddit:";
            case "b":
                return $z = "With bot:";
                break;
            case "u":
                return "With user:";
            case "t":
                return "With tag:";
            default:
                return "";
        }
    }

    function getTagClass($action) {
        switch($action) {
            case "banuser":
            case "removelink":
            case "uninvitemoderator":
            case "acceptmoderatorinvite":
            case "removecontributor":
            case "removemoderator":
            case "removecomment":
            case "marknsfw":
            case "wikibanned":
            case "removewikicontributor":
                return "badge-important";

            case "approvelink":
            case "wikicontributor":
            case "approvecomment":
            case "addmoderator":
            case "invitemoderator":
            case "addcontributor":
            case "unbanuser":
            case "wikiunbanned":
                return "badge-success";

            case "editsettings":
            case "editflair":
            case "wikirevise":
            case "wikipermlevel":
            case "distinguish":
                return "badge-warning";

            default:
                return "badge-info";
        }
    }

    $(window).load(function () {
        /*$('#re-s').submit(function () {
            window.location = segment + "/" + $("#re-s input").val();
            return false;
        });*/
        $("#publicmodlog").change(function () {
            if (!$("#publicmodlog").is(":checked")) {
                $("#usrinfo").animate({"opacity":"1", "height":"220px"}, "500")
                .css({"display":"block"});
            }
            else {
                $("#usrinfo").animate({"opacity":"0", "display":"none", "height":"0px"}, '500', function () {
                    $("#usrinfo").css({"display":"none"});
                });
            }
        }).change();
        $("#deleteform").submit(function () {
            if ($("#yolo").is(".in"))
                return true;
            else {
                $("#yolo").modal();
                return false;
            }
        });
        $('a[rel="tooltip"]').tooltip();
        $(".filter .btn-primary").click(function() {
            var $filter = $($(this).parent().parent());
            if($filter.attr("data-bsett") == 1) {
                var $input = $filter.children(".filter-input");
                var $val = $($input).children("input[type=text]").val();
                $($input).children("input[type=text]").val("");
                if($val == "") {
                    $(this).popover({placement:"right", title:"<strong>You haven't entered anything</strong>", html:true, content:"Please fill in the 'Value' field", trigger:"manual"});
                    $(this).popover("show");
                    thiz = this;
                    setTimeout("$(thiz).popover(\"hide\")", 2000);
                    return;
                }
                var $type = $($input).children("select").val();

                var $insert = $(
                    "<div data-ftype=\""+$type+"\" style=\display:none;\" data-fvalue=\""+$val+"\" class=\"well well-small filter-item\">"+
                    "<div class=\"left\"> "+getFilterName($type)+" <span class=\"badge "+getTagClass($type)+"\">" +$val+  "</span></div>"+
                    "<div class=\"right\">"+
                        "<a href=\"javascript:void(null)\" data-fdelete=\"1\" class=\"btn btn-danger btn-mini\"><i  class=\"icon-remove icon-white\"></i></a>"+
                    "</div>"
                ).insertAfter($input);
                $insert.children(".right").children(".btn").click(function() {
                    $(this).parent().parent().fadeOut('', function() { $(this).remove(); });
                })
                $insert.fadeIn();
            }
        });

        $("#bot-setting").submit(function() {
            $.each($(this).children("filter-item"), function() { console.log(this); });
            return false;
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