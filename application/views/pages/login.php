<?
    $this->form_validation->set_rules("usr", "Username", "required|alpha_dash");
    $this->form_validation->set_rules("pwd", "Password", "required");
    $this->form_validation->set_error_delimiters('<div class="alert alert-error">', '</div>');
    if($this->form_validation->run()) {
        $q = $this->db->query("SELECT * FROM users WHERE usr = \"{$this->db->escape_str(set_value("usr"))}\"");
        if($q->num_rows() > 0) {
            $row = $q->row();
            if(set_value("pwd") == $this->jariz->decrypt($row->pwd)) {
                $this->session->set_userdata("uid", $row->ID);
                if(isset($_GET["r"]) && @base64_decode($_GET["r"]) != false) {
                    header("Location: ".base64_decode($_GET["r"]));
                } else header("Location: account");
            } else $error = "Username/password not found";
        } else $error = "Username/password not found";
    }

    if(isset($_GET["r"])) { @$_GET["r"] = htmlentities(@$_GET["r"]); $g = "?r=".$_GET["r"]; }
    else $g = "";

    echo validation_errors();
    if(isset($error)) echo "<div class=\"alert alert-error\">".$error."</div>";
    echo form_open("login".$g, array("class" => "form-horizontal"));
?>
<div class="alert alert-info alert-block">
    <strong>This is not your reddit username/password</strong><br>
    Please enter your reddit.re username/password, not your reddit.com one.<br><a href="register">You can register here</a>
</div>
<div class="control-group">
    <label class="control-label" for="usr">Reddit.re username</label>
    <div class="controls">
        <input type="text" name="usr" placeholder="Reddit.re username">
    </div>
</div>
<div class="control-group">
    <label class="control-label" for="pwd">Password</label>
    <div class="controls">
        <input type="password" name="pwd" placeholder="Password">
    </div>
</div>
<div class="form-actions">
    <button type="submit" class="btn btn-primary">Sign in</button>
    <a href="<?=base_url()?>" class="btn btn-success">Register</a>
    </div>
</form>