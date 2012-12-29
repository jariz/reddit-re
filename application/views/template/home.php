<?
    if($this->jariz->loggedin()) $accesstoken = $this->jariz->getProp("accesstoken");
    else $accesstoken = "";

    if(!empty($accesstoken)) {
        $this->reddit_oauth->setAccessToken($accesstoken);
        $res = $this->reddit_oauth->fetch("reddits/mine/moderator.json");
        if($res['code'] == 200) {
            $children = $res["result"]["data"]["children"];
            foreach($children as $sub) $subreddits[] = $sub["data"]["display_name"];
        }
    }

if(isset($subreddits) && isset($subreddits[0]))
    if(@isset($_GET["sub"])) if(in_array($_GET["sub"], $subreddits)) $ssub = $_GET["sub"];
    else $ssub = $subreddits[0]; else $ssub = $subreddits[0];
//var_dump($this->reddit_oauth->fetch("api/r/TheOffspring/about/stylesheet.json"));
?>

<div class="modal hide fade" id="loading">
    <div class="modal-header">
        <h3></h3>
    </div>
    <div class="modal-body">
        <p></p>
        <p><div class="progress progress-success progress-striped active"><div class="bar" style="width: 100%;"></div></div></p>
    </div>
</div>

<div class="page-header">
    <h1>Template Manager
        <small>Quickly manage your subreddit's styles!</small>
    </h1>
</div>
<h2>What is it?</h2>
<p>Our template manager allows you to quickly control your subreddit's styles.<br>We've got a few 'templates' which allow you to change your subreddit's style instantly<br>Next up, there are the snapshots. You can save your current subreddit style into a 'snapshot'.<br>You can restore any snapshot at any time you want! This allows you to always have a nice little backup whenever something goes wrong!</p>

<h2 class="settings">Authorization</h2>
<?if(isset($subreddits)) { ?>
<h2 class="medium">Logged in as: <?$f = $this->reddit_oauth->fetch("api/v1/me.json"); echo $f["result"]["name"]?><br>
    <?=$f["result"]["name"]?>'s amount of subreddits: <?=count($subreddits)?></h2>
<p>
    <a href="<?=base_url()?>revoke" class="btn btn-danger btn-large"><i class="icon-remove-circle icon-white"></i> Revoke authorization</a>
</p><? } else { ?>
<p>No reddit account authorized.</p>
<p>
    <a href="auth" class="btn btn-success btn-large"><i class="icon-user icon-white"></i> Authorize reddit account to my reddit.re account</a>
    <a href="<?=base_url()?>revoke" class="btn btn-danger btn-large"><i class="icon-remove-circle icon-white"></i> Revoke authorization</a>
</p>
<? } ?>
<h2 class="settings">Snapshots
    <div class="pull-right">
    <? if(isset($subreddits)) { ?>
        <select id="subreddits"> <? foreach($subreddits as $sub) { ?>
            <option<?=$sub == $ssub ? " selected=\"selected\"" : "";?>><?=$sub?></option>
        <? } ?></select>
    <? }?>
    </div>
</h2>
<?if(!isset($subreddits) && !isset($subreddits[0])) { ?><h2><small>Nothing to display</small></h2><? } else { ?>

<ul class="thumbnails">
    <? foreach ($this->db->query("SELECT * FROM snapshots WHERE sub = \"{$ssub}\" AND uid = {$this->session->userdata("uid")}")->result() as $row) { ?>
    <li class="span4">
            <div class="thumbnail">
                <a href="<?=base_url()?>/snapshot/<?=$row->hash?>"><img src="http://snapshot.reddit.re/small/<?=$row->snap?>" alt=""></a>
                <div class="caption">
                    <h3><?=$row->sub?>  <small></small></h3>
                    <p>Taken <?=timespan($row->timestamp)?> ago</p>
                    <?if(isset($subreddits)) { ?>
                    <p>
                    <div class="btn-group">
                        <a href="javascript:void(null)" class="btn btn-mini btn-primary">
                            <i class="icon-facetime-video icon-white"></i> Restore
                        </a>
                    </div>
                    <div class="btn-group">
                        <a class="btn btn-mini btn-success dropdown-toggle" data-toggle="dropdown" href="#">
                            <i class="icon-forward icon-white"></i> Restore on another
                            <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <?foreach($subreddits as $sub) { ?>
                            <li><a href="javascript:void(null)" onclick="snapshotAndApply('<?=$sub?>', <?=$row->ID?>)">/r/<?=$sub?></a> </li><? } ?>
                        </ul>
                    </div>
                    </p><?}?>
                </div>
            </div>
    </li>
    <? } ?>
</ul>

<p><a onclick="new_snap('<?=$ssub?>', this)" href="javascript:void(null)" class="btn btn-primary btn-large"><i class="icon-plus icon-white"></i> Add snapshot</a></p><? } ?>

<h2 class="settings">Templates</h2>
<ul class="thumbnails">
<? foreach ($this->db->query("SELECT * FROM templates")->result() as $row) { ?>
    <li class="span4">
        <div class="thumbnail">
            <img src="http://snapshot.reddit.re/small/<?=$row->snapshot?>.png" alt="">
            <div class="caption">
                <h3><?=$row->name?> <?=!empty($row->variation) ? "<small>{$row->variation}</small>" : ""?></h3>
                <p>Made by: <a href="<?=$row->authorurl?>"><?=$row->author?></a> </p>
                <?if(isset($subreddits)) { ?>
                <p>
                    <div class="btn-group">
                        <a class="btn btn-mini btn-success dropdown-toggle" data-toggle="dropdown" href="#">
                            <i class="icon-ok icon-white"></i> Apply
                            <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <?foreach($subreddits as $sub) { ?>
                            <li><a href="javascript:void(null)" onclick="apply(<?=$row->ID?>)">/r/<?=$sub?></a> </li><? } ?>
                        </ul>
                    </div>

                <div class="btn-group">
                    <a class="btn btn-mini btn-primary dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="icon-facetime-video icon-white"></i> Apply, take snapshot first
                        <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <?foreach($subreddits as $sub) { ?>
                        <li><a href="javascript:void(null)" onclick="snapshotAndApply('<?=$sub?>', <?=$row->ID?>)">/r/<?=$sub?></a> </li><? } ?>
                    </ul>
                </div>
                </p><?}?>
            </div>
        </div>
    </li>
<? } ?>
</ul>
