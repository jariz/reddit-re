<? if (!$this->jariz->loggedin()) show_error("You're not logged in", 403, "<a href=\"http://reddit.re/login\">Please log in here</a>"); ?>
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
        <? foreach ($this->db->query("SELECT * FROM bots WHERE uid = {$this->session->userdata("uid")}")->result() as $bot) { ?>
        <tr<?=$bot->disabled == 1 ? " class=\"error\">" : ""?>>
            <td><?=$bot->usr?></td>
            <td><?=$bot->src_sub?></td>
            <td><?=$bot->dst_sub?></td>
            <td><?=$bot->disabled == 0 ? "Enabled" : "<strong>Disabled</strong>"?></td>
            <td><?=$this->db->query("SELECT ID FROM entries WHERE botid = {$bot->ID}")->num_rows()?></td>
            <td><?=$bot->lastcrawl == 0 ? "Never" : timespan($bot->lastcrawl)." ago"?></td>
            <td><a href="#" class="btn btn-warning"><i class="icon-remove-circle icon-white"></i></a> <a class="btn btn-success"><i class="icon-edit icon-white"></i></a> </td>
        </tr>
            <? } ?>
        </tbody>
    </table>
    <a href="http://modlog.reddit.re/go" class="btn btn-primary btn-large"><i class="icon-plus icon-white"></i> Add new modlog bot</a>
    <a href="http://modlog.reddit.re" class="btn btn-warning btn-large"><i class="icon-question-sign icon-white"></i> What are modlog bots?</a>
</section>

    <section class="">

    </section>