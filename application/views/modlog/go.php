<?
    if (!$this->jariz->loggedin()) show_error("In order to keep track of your bots, you need to have an account.<br>Creating a account is surprisingly easy, We only want a username and a password.<br><a href='http://reddit.re/register?r=".base64_encode(current_url())."' class='btn btn-primary btn-enormous' style='margin-top:20px;'>Register now!</a><br><a href='http://reddit.re/login?r=".base64_encode(current_url())."' class='btn btn-success btn-enormous' style='margin-top:5px;'>Log in</a>", 403, "Not logged in");

    $this->form_validation->set_rules("src_sub", "Modlog subreddit", "required|alpha_dash|is_unique[bots.src_sub]|callback_subreddit_exists");
    $this->form_validation->set_rules("dst_sub", "Destination subreddit", "required|alpha_dash|callback_subreddit_exists");

    $this->form_validation->set_rules("usr", "Username", "alpha_dash|is_unique[bots.usr]");
    $this->form_validation->set_rules("pwd", "Password", "");

    $this->form_validation->set_rules("self", "Selfposting", "required|less_than[2]");
    $this->form_validation->set_rules("publicmodlog", "Use your own account", "less_than[2]");

    $success = "";
    $this->form_validation->set_error_delimiters('<div class="alert alert-error">', '</div>');
    if(set_value("publicmodlog") == "1") { $_POST["usr"] = $this->config->item("bot_usr"); $_POST["pwd"] = $this->jariz->decrypt($this->config->item("bot_pwd")); }
    if($this->form_validation->run()) {
        if(set_value("src_sub") != set_value("dst_sub")) 
            if($this->reddit->login(set_value("usr"), set_value("pwd")))
                if($this->reddit->isMod(set_value("usr"), set_value("dst_sub")))
                    if($this->reddit->isMod(set_value("usr"), set_value("src_sub"))) {
                        $success = "<div class=\"alert alert-success\">This account has passed all verifications and is now added to our database.<br>If you want to change any settings later, please log in.</div>";
                        $this->db->query(sprintf("INSERT INTO bots VALUES (NULL, '%s', '%s', '%s', '%s', '%s', '0', NULL)", $this->db->escape_str(set_value("usr")), $this->jariz->encrypt($this->db->escape_str(set_value("pwd"))), $this->db->escape_str(set_value("src_sub")), $this->db->escape_str(set_value("dst_sub")), $this->db->escape_str(set_value("self"))));
                    } else $error = "This account isn't moderator of the source subreddit";
                else $error = "This account isn't moderator of the destination subreddit (it needs to be moderator so posts won't be marked as spam)";
            else $error = "Invalid reddit login";
        else $error = "Destination subreddit can't be the same as the modlog subreddit";
    }
?>
<div class="page-header">
    <h1>Create your own <small>Set it up with 1 simple step</small></h1>
</div>

<?=validation_errors();?>
<?if(isset($error)) echo "<div class=\"alert alert-error\">$error</div>";?>
<?=$success?>


<?=form_open("go", array("class" => "form-horizontal"))?>
<legend>Subreddits</legend>
<div class="control-group">
    <label class="control-label" for="src_sub">Modlog subreddit</label>
    <div class="controls">
        <input type="text" name="src_sub" value="<?=set_value("src_sub")?>" placeholder="mysubreddit">
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="dst_sub">Destination subreddit</label>
    <div class="controls">
        <input type="text" name="dst_sub" value="<?=set_value("dst_sub")?>" placeholder="mysubreddit_modlog">
    </div>
</div>

<legend>Login information</legend>
<div class="control-group">
    <label class="checkbox">
        <input type="checkbox" id="publicmodlog" name="publicmodlog" value="1" <?=set_checkbox("publicmodlog", "1", true)?>> Use the <a href="http://reddit.com/u/PublicModlog">PublicModlog</a> account instead of entering your own login details<br><small>(Make sure the add <a href="http://reddit.com/u/PublicModlog">PublicModlog</a> as moderator to your subreddit<b>s</b>)</small>
    </label>
</div>
<div id="usrinfo" style="display:none;">
    <div class="control-group">
        <label class="control-label" for="usr">Username</label>
        <div class="controls">
            <input type="text" name="usr" value="<?=set_value("usr")?>" placeholder="MySubreddit_BOT">
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="pwd">Password</label>
        <div class="controls">
            <input type="password" name="pwd" value="<?=set_value("pwd")?>" >
        </div>
    </div>

    <div class="alert-block alert">
        <p><strong>It's important that you read this.</strong></p>
        <p>Reddit requires accounts to have about 5-10 karma or else it will start showing captcha's when submitting which means we can't submit stuff.<br>
            <i>TL;DR Make sure your bot has about 5+ link karma.</i></p>
    </div>
</div>

<!--<legend>Misc settings</legend>
<div class="alert underconstruction"><i class="icon-warning-sign icon-white"></i> UNDER CONSTRUCTION</div>
<div class="control-group">
<label class="checkbox">
<input type="radio" name="self" value="0" <?=set_radio("self", "0", true)?>> Use reddit.re links (more detailed, beter overview)<br>
</label>
</div>

<div class="control-group">
<label class="checkbox">
<input type="radio" name="self" disabled="" value="1" <?=set_radio("self", "1")?>> Selfpost (less detailed, bad overview)
</label>
</div>-->
<input type="hidden" name="self" value="0">

<div class="form-actions">
    <button type="submit" class="btn btn-primary">Create!</button>
    <button type="reset" class="btn">Cancel</button>
    </div>
</form>