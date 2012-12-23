<? if($this->jariz->loggedin() != true) { echo form_open("http://reddit.re/login?r=".base64_encode(current_url()), array("class" => "navbar-form pull-right"));?>
    <input name="usr" class="span2" type="text" placeholder="Username">
    <input name="pwd" class="span2" type="password" placeholder="Password">
    <button type="submit" class="btn">Login</button>
    </form><? } else { ?>
    <ul class="nav pull-right">
        <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                <?=$this->jariz->getProp("usr");?>
                <b class="caret"></b>
            </a>
            <ul class="dropdown-menu">
                <li><a href="http://reddit.re/logout"><i class="icon-remove"></i> Log out</a></li>
            </ul>
        </li>
    </ul><? } ?>