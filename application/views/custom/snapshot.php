<div class="alert alert-info"><p><strong>This link is public</strong><br>You can send this URL to other people.</p><input type="text" class="input-xxlarge" readonly="readonly" value="<?=current_url()?>"> </div>
<ul class="thumbnails" style="margin-left:0px">
    <il>
        <div class="thumbnail">
            <div class="caption">
                <h3><i class="icon-facetime-video"></i> Snapshot from <?=$row->sub?> taken <?=timespan($row->timestamp)?> ago</h3>
            </div>
            <img src="http://snapshot.reddit.re/<?=$row->snap?>">
            <div class="caption">
                <p>Taken by: <?=$this->db->query("SELECT usr FROM users WHERE ID = {$row->uid}")->row()->usr?>
                <br>At: <?=date(DATE_RFC822, $row->timestamp)?></p>
                <a href="#" class="btn btn-primary btn-large btn-block"><i class="icon-arrow-right icon-white"></i> Restore snapshot</a>
                <a href="#" class="btn btn-success btn-large btn-block"><i class="icon-forward icon-white"></i> Restore snapshot on another subreddit</a>
                <h2>CSS</h2>
                <pre><?=$row->css?></pre>
            </div>
        </div>
    </il>
</ul>