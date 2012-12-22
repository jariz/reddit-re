<!DOCTYPE html>
<html>
    <head>
        <link href="http://reddit.re/static/css/bootstrap.min.css" rel='stylesheet' type='text/css'>
        <link href="http://reddit.re/static/css/reddit.re.css" rel='stylesheet' type='text/css'>
        <title>Reddit.re - Internal Error</title>
    </head>
    <body>
        <div class="navbar navbar-fixed-top">
            <div class="navbar-inner">
                <div class="container">
                    <a class="brand" href="http://reddit.re/">Reddit.re</a>
                </div>
            </div>
        </div>
        
        <div class="container">
            <h1><?=$heading?></h1><hr>
            <?=str_replace("<p>", "<p><span class=\"badge badge-important\">Error</span> ", $message)?>
        </div>
    </body>
</html>