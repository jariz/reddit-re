<? if (!$this->jariz->loggedin()) show_error("You're not logged in", 403, "<a href=\"http://reddit.re/login\">Please log in here</a>");

//init redditoauth
$accesstoken = $this->jariz->getProp("accesstoken");
if(!empty($accesstoken)) {
    $this->reddit_oauth->setAccessToken($accesstoken);
    $res = $this->reddit_oauth->fetch("api/v1/me.json");
    if($res['code'] == 200) {
        $name = $res["result"]["name"];
    }
}
    ?>
<h1>logged in as <?=strtolower($this->jariz->getProp("usr"))?></h1>
<section class="accountsection">
    <h2>Modlog Bots</h2>
    <table class="table table-hover">
        <thead>
        <tr>
            <th>Username</th>
            <th>Logging from</th>
            <th>Logging into</th>
            <th>State</th>
            <th>Entries</th>
            <th>Last crawl</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <? $z = $this->db->query("SELECT * FROM bots WHERE uid = {$this->session->userdata("uid")}"); foreach ($z->result() as $bot) { ?>
        <tr<?=$bot->disabled == 1 ? " class=\"error\"" : ""?>>
            <td><?=$bot->usr?></td>
            <td><?=$bot->src_sub?></td>
            <td><?=$bot->dst_sub?></td>
            <td><?=$bot->disabled == 0 ? "Enabled" : "<strong>Disabled</strong>"?></td>
            <td><?=$this->db->query("SELECT ID FROM entries WHERE botid = {$bot->ID}")->num_rows()?></td>
            <td><?=$bot->lastcrawl == 0 ? "Never" : timespan($bot->lastcrawl)." ago"?></td>
            <td><a href="#" class="btn btn-warning"><i class="icon-remove-circle icon-white"></i></a> <a href="<?=base_url()?>modbot/<?=$bot->ID?>" class="btn btn-success"><i class="icon-edit icon-white"></i></a> </td>
        </tr>
            <? } ?>
        </tbody>
    </table>
    <? if($z->num_rows == 0) { ?><h6 class="dust">dust</h6><? } ?>
    <a href="http://modlog.reddit.re/go" class="btn btn-primary btn-large"><i class="icon-plus icon-white"></i> Add new modlog bot</a>
    <a href="http://modlog.reddit.re" class="btn btn-warning btn-large"><i class="icon-question-sign icon-white"></i> What are modlog bots?</a>
</section>

<section class="accountsection">
    <h2>Template manager</h2>
    <p>Connected account: <?=isset($name) ? $name : "<i>None</i>"?></p>
    <a href="http://template.reddit.re/auth" class="btn btn-success btn-large"><i class="icon-user icon-white"></i> Authorize reddit account to my reddit.re account</a>
    <a href="http://template.reddit.re/revoke" class="btn btn-danger btn-large"><i class="icon-remove-circle icon-white"></i> Revoke authorization</a>
</section>