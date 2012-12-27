<?
if (!$this->jariz->loggedin()) show_error("You are not logged in", 403, "Log in <a href=\"http://modlog.reddit.re/login\">here</a>");
$qqq = $this->db->query("SELECT ID, uid FROM bots WHERE ID = {$this->uri->segment(2)}");
if($qqq->num_rows > 0) if($qqq->row()->uid == $this->session->userdata("uid")) $bid = $qqq->row()->ID;
else show_error("Invalid bot ID"); else show_error("Invalid bot ID");

$this->form_validation->set_rules("action", "Action", "required|alpha");
if ($this->form_validation->run()) {
    switch (set_value("action")) {
        case "delete":
            $this->db->query("DELETE FROM bots WHERE id = $bid");
            $this->db->query("DELETE FROM entries WHERE botid = $bid");
            header("Location http://reddit.re/account");
            break;
        case "disable":
            $this->db->query("UPDATE bots SET disabled = '1' WHERE id = {$bid}");
            $msg = "Bot successfully disabled.";
            break;
        case "enable":
            $this->db->query("UPDATE bots SET disabled = '0' WHERE id = {$bid}");
            $msg = "Bot successfully enabled.";
            break;
    }
}
$row = $this->db->query("SELECT * FROM bots WHERE ID = {$bid}")->row();

?>
<div class="modal hide fade" id="yolo">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3>Are you sure?</h3>
    </div>
    <div class="modal-body">
            <i class="icon-warning-sign"></i> Deleting a bot will also delete its modlog entries!
    </div>
    <div class="modal-footer">
        <a href="javascript:void(null)" onclick="$('#yolo').modal('hide')" class="btn">Close</a>
        <a href="javascript:void(null)" onclick="$('#deleteform').submit()" class="btn btn-danger">REMOVE BOT</a>
    </div>
</div>
<ul class="breadcrumb">
    <li><a href="<?=base_url()?>account">Dashboard</a> <span class="divider">/</span></li>
    <li class="active">Modbot #<?=$row->ID?></li>
</ul>
<h1>current modbot: <strong><?=strtolower($row->usr)?></strong></h1>
<? if (isset($msg)) echo "<div style=\"margin-top:40px;\" class=\"alert alert-success\">$msg</div>"; ?>
<?= validation_errors("<div style=\"margin-top:40px;\" class=\"alert alert-error\">", "</div")
; ?>
<h2 class="settings">Bot stats</h2>
<p><strong>Logging from: </strong> <a href="http://modlog.reddit.re/r/<?=$row->src_sub?>"><?=$row->src_sub?></a></p>
<p><strong>Posting into: </strong> <a href="http://modlog.reddit.re/r/<?=$row->dst_sub?>"><?=$row->dst_sub?></a></p>
<p><strong>Entries
    submitted:</strong> <?=$this->db->query("SELECT ID FROM entries WHERE botid = {$bid}")->num_rows()?>
</p>
<p><strong>Last
    crawl:</strong> <?if ($row->lastcrawl != 0) echo timespan($row->lastcrawl) . " ago"; else echo "Never";?></p>
<? if ($row->disabled == "1") echo "<div class=\"badge badge-warning\">Warning</div> The bot is disabled. It won't check for new entries in the modlog, neither will it post anymore"; ?>

<h2 class="settings">Bot checks</h2>
<? $q = $this->db->query("SELECT * FROM checks WHERE botid = {$row->ID}");
if ($q->num_rows > 0) if (time() - $q->row()->timestamp <= 3600) $fromcache = true;

if (@!$fromcache) {
    ?>
<p>Bot's login is still
    valid: <?$this->jariz->printResult("login", @$this->reddit->login($row->usr, $this->jariz->decrypt($row->pwd)), $bid)?></p>
<p>Bot's source subreddit still
    exists: <?$this->jariz->printResult("src_sub_exists", @$this->reddit->subredditExists($row->src_sub), $bid)?></p>
<p>Bot's destination subreddit still
    exists: <?$this->jariz->printResult("dst_sub_exists", @$this->reddit->subredditExists($row->dst_sub), $bid)?></p>
<p>Bot is still moderator in source
    subreddit: <?$this->jariz->printResult("src_sub_mod", @$this->reddit->isMod($row->usr, $row->src_sub), $bid)?></p>
<p>Bot is still moderator in destination
    subreddit: <?$this->jariz->printResult("dst_sub_mod", @$this->reddit->isMod($row->usr, $row->dst_sub), $bid)?></p>
<h6>Last refreshed: NOW</h6>
<? } else { ?>
<p>Bot's login is still valid: <?$this->jariz->printResult("login", null, $bid)?></p>
<p>Bot's source subreddit still exists: <?$this->jariz->printResult("src_sub_exists", null, $bid)?></p>
<p>Bot's destination subreddit still exists: <?$this->jariz->printResult("dst_sub_exists", null, $bid)?></p>
<p>Bot is still moderator in source subreddit: <?$this->jariz->printResult("src_sub_mod", null, $bid)?></p>
<p>Bot is still moderator in destination subreddit: <?$this->jariz->printResult("dst_sub_mod", null, $bid)?></p>
<h6>Last
    refreshed: <?=timespan($this->db->query("SELECT timestamp FROM checks WHERE botid = {$bid}")->row()->timestamp)?>
    ago</h6>
<? } ?>

<h2 class="settings">Bot settings <small><a href="javascript:void(null)" rel="tooltip" title="These settings will only be applied to new entries!">(!)</a></small></h2>
<?= form_open("modbot", array("class" => "form-horizontal", "id" => "bot-settings")) ?><input type="hidden" name="action" value="settings">
<div class="control-group">
    <label class="checkbox"><input type="checkbox" name="showmod" value="1" <?=$row->showmod?>> Show moderator name</label>
    <?=$this->load->view("modules/modlog_filter", array("filter" => $row->filter, "bsett" => true), true)?>
    <input type="hidden" name="fstring" value="<?=$row->filter?>">
</div>
<div class="form-actions">
    <button type="submit" class="btn btn-primary">Save</button>
    <input type="reset" class="btn" value="Cancel">
</div>
</form>
<h2 class="settings">Bot actions</h2>
<? if ($row->disabled == "0") {
    echo form_open("", array("class" => "inline-form")) ?><input type="hidden" name="action" value="disable">
<button id="disable" type="submit" class="btn btn-warning btn-large">Disable bot</button></form> <?
} else {
    echo form_open("", array("class" => "inline-form"))?><input type="hidden" name="action" value="enable">
<button id="disable" type="submit" class="btn btn-success btn-large">Enable bot</button></form> <? } ?>
 <?= form_open("", array("class" => "inline-form","id" => "deleteform")) ?><input type="hidden" name="action" value="delete">
<button id="delete" type="submit" class="btn btn-danger btn-large">Delete bot</button></form>
<a href="http://modlog.reddit.re/b/<?=strtolower($row->usr)?>" class="btn btn-primary btn-large">See all entries posted by
    bot</a>