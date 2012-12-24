<? $ci =& get_instance(); ?><!DOCTYPE html>
<html>
    <head>
        <link href="<?=base_url()?>static/css/bootstrap.min.css" rel='stylesheet' type='text/css'>
        <link href="<?=base_url()?>static/css/reddit.re.css" rel='stylesheet' type='text/css'>
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
            <h1>Woops!</h1><hr>
            <p><i class="icon-exclamation-sign"></i> A internal error occured. We're looking into it!</p
            <?write_file("error.txt", $message."<br><b>STACKTRACE</b><p>".$ci->jariz->var_string(xdebug_get_function_stack()))?>
        </div>
    </body>
</html>