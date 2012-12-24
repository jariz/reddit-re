<?
    $this->form_validation->set_rules("usr", "Username", "required|alpha_dash|is_unique[users.usr]");
    $this->form_validation->set_rules("pwd", "Password", "required");
    $this->form_validation->set_error_delimiters('<div class="alert alert-error">', '</div>');
    if($this->form_validation->run()) {
        $q = $this->db->query(sprintf("INSERT INTO users VALUES (NULL, '%s', '%s', 0)", $this->db->escape_str(set_value("usr")), $this->jariz->encrypt($this->db->escape_str(set_value("pwd")))));
        if(isset($_GET["r"]) && @base64_decode($_GET["r"]) != false) {
            header("Location: ".base64_decode($_GET["r"]));
        } else header("Location: account");
    }

    echo validation_errors();
    if(isset($error)) echo "<div class=\"alert alert-error\">".$error."</div>";
    
    if(isset($_GET["r"])) { @$_GET["r"] = htmlentities(@$_GET["r"]); $g = "?r=".$_GET["r"]; }
    else $g = "";
    
    echo form_open("register".$g, array("class" => "form-horizontal"));
?>
<div class="control-group">
    <label class="control-label" for="usr">Reddit.re username</label>
    <div class="controls">
        <input type="text" name="usr" value="<?=set_value("usr")?>" placeholder="Reddit.re username">
    </div>
</div>
<div class="control-group">
    <label class="control-label" for="pwd">Password</label>
    <div class="controls">
        <input type="password" value="<?=set_value("pwd")?>" name="pwd" placeholder="Password">
    </div>
</div>
<div class="form-actions">
    <button type="submit" class="btn btn-primary">Register</button>
    <a href="<?=base_url()?>login" class="btn btn-success">Sign in</a>
    </div>
</form>