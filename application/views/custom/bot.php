<?
if(!$this->jariz->loggedin()) show_error("You are not logged in", 403, "Log in <a href=\"http://modlog.reddit.re/login\">here</a>");

$q =

    $this->form_validation->set_rules("action", "Action", "required|alpha");
if($this->form_validation->run()) {
    switch(set_value("action")) {
        case "delete":
            $bid = $this->session->userdata("botid");
            $this->db->query("DELETE FROM bots WHERE id = $bid");
            $this->db->query("DELETE FROM entries WHERE botid = $bid");
            $this->session->unset_userdata('botid');
            show_error("Your bot was deleted, You can't access the bot settings anymore.", 403, "Your bot was succesfully deleted");
            break;
        case "disable":
            $this->db->query("UPDATE bots SET disabled = '1' WHERE id = {$this->session->userdata("botid")}");
            $msg = "Bot successfully disabled.";
            break;
        case "enable":
            $this->db->query("UPDATE bots SET disabled = '0' WHERE id = {$this->session->userdata("botid")}");
            $msg = "Bot successfully enabled.";
            break;
    }
}

$row = $this->db->query("SELECT * FROM bots WHERE ID = {$this->session->userdata("botid")}")->row();

?>
<div class="modal hide fade">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3>Are you sure?</h3>
    </div>
    <div class="modal-body">
        <div class="alert alert-danger">
            <strong>Deleting a bot will also delete its modog entries</strong>
        </div>
    </div>
    <div class="modal-footer">
        <a href="#" class="btn">Close</a>
        <a href="#" class="btn btn-primary">Save changes</a>
    </div>
</div>
<h1>logged in as: <strong><?=strtolower($row->usr)?></strong></h1>
<? if(isset($msg)) echo "<div style=\"margin-top:40px;\" class=\"alert alert-success\">$msg</div>";?>
<?=validation_errors("<div style=\"margin-top:40px;\" class=\"alert alert-error\">", "</div");?>
<h2 class="settings">Bot stats</h2>
<p><strong>Logging from: </strong> <a href="<?=base_url()?>r/<?=$row->src_sub?>"><?=$row->src_sub?></a></p>
<p><strong>Posting into: </strong> <a href="<?=base_url()?>r/<?=$row->dst_sub?>"><?=$row->dst_sub?></a></p>
<p><strong>Entries submitted:</strong> <?=$this->db->query("SELECT ID FROM entries WHERE botid = {$this->session->userdata("botid")}")->num_rows()?></p>
<p><strong>Last crawl:</strong> <?if($row->lastcrawl != 0) echo timespan($row->lastcrawl)." ago"; else echo "Never";?></p>
<?if($row->disabled == "1") echo "<div class=\"badge badge-warning\">Warning</div> The bot is disabled. It won't check for new entries in the modlog, neither will it post anymore";?>

<h2 class="settings">Bot checks</h2>
<? $q = $this->db->query("SELECT * FROM checks WHERE botid = {$row->ID}");
if($q->num_rows > 0) if(time()-$q->row()->timestamp <= 3600) $fromcache = true;

if(@!$fromcache) { ?>
<p>Bot's login is still valid: <?$this->jariz->printResult("login", @$this->reddit->login($row->usr, $this->jariz->decrypt($row->pwd)))?></p>
<p>Bot's source subreddit still exists: <?$this->jariz->printResult("src_sub_exists", @$this->reddit->subredditExists($row->src_sub))?></p>
<p>Bot's destination subreddit still exists: <?$this->jariz->printResult("dst_sub_exists", @$this->reddit->subredditExists($row->dst_sub))?></p>
<p>Bot is still moderator in source subreddit: <?$this->jariz->printResult("src_sub_mod", @$this->reddit->isMod($row->usr, $row->src_sub))?></p>
<p>Bot is still moderator in destination subreddit: <?$this->jariz->printResult("dst_sub_mod", @$this->reddit->isMod($row->usr, $row->dst_sub))?></p>
<h6>Last refreshed: NOW</h6>
<? } else { ?>
<p>Bot's login is still valid: <?$this->jariz->printResult("login")?></p>
<p>Bot's source subreddit still exists: <?$this->jariz->printResult("src_sub_exists")?></p>
<p>Bot's destination subreddit still exists: <?$this->jariz->printResult("dst_sub_exists")?></p>
<p>Bot is still moderator in source subreddit: <?$this->jariz->printResult("src_sub_mod")?></p>
<p>Bot is still moderator in destination subreddit: <?$this->jariz->printResult("dst_sub_mod")?></p>
<h6>Last refreshed: <?=timespan($this->db->query("SELECT timestamp FROM checks WHERE botid = {$this->session->userdata("botid")}")->row()->timestamp)?> ago</h6>
<? } ?>
<h2 class="settings">Bot actions</h2>
<?if($row->disabled == "0") { echo form_open("account", array("class" => "inline-form"))?><input type="hidden" name="action" value="disable"><button id="disable" type="submit" class="btn btn-warning btn-large">Disable bot</button></form><? }
else { echo form_open("account", array("class" => "inline-form"))?><input type="hidden" name="action" value="enable"><button id="disable" type="submit" class="btn btn-success btn-large">Enable bot</button></form><? } ?>
<?=form_open("account", array("class" => "inline-form"))?><input type="hidden" name="action" value="delete"><button id="delete" type="submit" class="btn btn-danger btn-large">Delete bot</button></form>
<a href="<?=base_url()?>b/<?=strtolower($row->usr)?>" class="btn btn-primary btn-large">See all entries posted by bot</a>