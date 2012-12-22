<!DOCTYPE html>
<html>
    <head>
        <link href="<?=base_url()?>static/css/bootstrap.min.css" rel='stylesheet' type='text/css'>
        <link href="<?=base_url()?>static/css/reddit.re.css" rel='stylesheet' type='text/css'>
        <script type="application/javascript" src="http://static.jariz.pro/js/jquery.min.js"></script>
        <script type="application/javascript" src="<?=base_url()?>static/js/bootstrap.min.js"></script>
        <script type="application/javascript" src="<?=base_url()?>static/js/reddit.re.js"></script>
        <title>Reddit.re - The ultimate collection of reddit modtools</title>
    </head>
    <body>
        <div class="navbar navbar-fixed-top">
            <div class="navbar-inner">
                <div class="container">
                    <a class="brand" href="http://reddit.re/">Reddit.re</a>
                    <ul class="nav">
                        <li><a href="http://modlog.reddit.re">Public modlog</a></li>
                        <? if(!$this->jariz->loggedin()) { ?><li><a href="http://reddit.re/login">Login</a></li>
                        <li><a href="http://reddit.re/register">Register</a></li><? } ?>
                    </ul>
                    {usersettings}
                </div>
            </div>
        </div>

        <div class="container">
            {content}
        </div>
    </body>
</html>