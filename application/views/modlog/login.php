<?
    $this->form_validation->set_rules("usr", "Username", "required|alpha_dash");
    $this->form_validation->set_rules("pwd", "Password", "required");
    $this->form_validation->set_error_delimiters('<div class="alert alert-error">', '</div>');
    if($this->form_validation->run()) {
        $q = $this->db->query("SELECT * FROM bots WHERE usr = \"{$this->db->escape_str(set_value("usr"))}\"");
        if($q->num_rows() > 0) {
            $row = $q->row();
            if(set_value("pwd") == $this->jariz->decrypt($row->pwd)) {
                $this->session->set_userdata("botid", $row->ID);
                header("Location: account");
            } else $error = "Username/password not found";
        } else $error = "Username/password not found";
    }
    var_dump(base64_encode($_GET["r"]));
    
    echo validation_errors();
    if(isset($error)) echo "<div class=\"alert alert-error\">".$error."</div>";
    echo form_open("login", array("class" => "form-horizontal"));
?>
<div class="control-group">
    <label class="control-label" for="usr">Bot username</label>
    <div class="controls">
        <input type="text" name="usr" placeholder="Bot username">
    </div>
</div>
<div class="control-group">
    <label class="control-label" for="inputPassword">Password</label>
    <div class="controls">
        <input type="password" name="pwd" placeholder="Password">
    </div>
</div>
<div class="form-actions">
    <button type="submit" class="btn btn-primary">Sign in</button>
    <a href="<?=base_url()?>register" class="btn btn-success">Register</a>
    </div>
</form>