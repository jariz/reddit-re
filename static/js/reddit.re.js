var thiz;
function new_snap($sub, that) {
    $("#loading .modal-header h3").html("Creating new snapshot...");
    $("#loading .modal-body p:first-child").html("Creating new snapshot for subreddit '" + $sub + "'....");
    $("#loading").modal("show");
    $.getJSON("api/new-snap/" + $sub, undefined, function (data) {
        if (!data.error) {
            window.location = "";
        } else {
            $("#loading").modal("hide");
            $(that).popover({placement:"right", title:"<strong>Action failed</strong>", html:true, content:data.msg, trigger:"manual"});
            $(that).popover("show");
            thiz = that;
            setTimeout("$(thiz).popover(\"hide\")", 2000);
        }
    });
}
!function ($) {

    function getFilterName($filter) {
        switch ($filter) {
            case "r":
                return "With subreddit:";
            case "b":
                return "With bot:";
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
        switch ($action) {
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
        $(".filter .btn-primary").click(function () {
            var $filter = $($(this).parent().parent());
            if ($filter.attr("data-bsett") == 1) {
                var $input = $filter.children(".filter-input");
                var $val = $($input).children("input[type=text]").val();
                $($input).children("input[type=text]").val("");
                if ($val == "") {
                    $(this).popover({placement:"right", title:"<strong>You haven't entered anything</strong>", html:true, content:"Please fill in the 'Value' field", trigger:"manual"});
                    $(this).popover("show");
                    thiz = this;
                    setTimeout("$(thiz).popover(\"hide\")", 2000);
                    return;
                }
                var $type = $($input).children("select").val();

                var $insert = $(
                    "<div data-ftype=\"" + $type + "\" style=\display:none;\" data-fvalue=\"" + $val + "\" class=\"well well-small filter-item\">" +
                        "<div class=\"left\"> " + getFilterName($type) + " <span class=\"badge " + getTagClass($type) + "\">" + $val + "</span></div>" +
                        "<div class=\"right\">" +
                        "<a href=\"javascript:void(null)\" data-fdelete=\"1\" class=\"btn btn-danger btn-mini\"><i  class=\"icon-remove icon-white\"></i></a>" +
                        "</div>"
                ).insertAfter($input);
                $insert.children(".right").children(".btn").click(function () {
                    $(this).parent().parent().fadeOut('', function () {
                        $(this).remove();
                    });
                });
                $insert.fadeIn();
            }
        });

        $("#subreddits").change(function () {
            window.location = "?sub=" + $("#subreddits").val();
        })

        $("#bot-settings").submit(function () {
            var fstring = "";
            $.each($(this).children(".control-group").children(".filter").children(".filter-item"), function () {
                fstring += "/"+$(this).attr("data-ftype")+"/"+$(this).attr("data-fvalue");
            });
            console.log(fstring);
            $("#bot-settings .form-actions button").html("Loading....");
            $("#bot-settings .form-actions button").attr("class", "btn btn-primary disabled");
            $("#bot-settings .form-actions input").attr("class", "btn disabled");
            $.get("api/")
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