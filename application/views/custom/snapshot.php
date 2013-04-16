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
                <pre class="prettyprint lang-css pre-scrollable"><?=$row->css?></pre>
                <h2>Images</h2>
                <ul class="thumbnails">
                    <?$i = -1; foreach($images as $image) { $i++; if($i == 3) { $i = 0; echo "</ul><ul class=\"thumbnails\">"; } ?>
                    <li class="span3 thumbnail sr-image">
                        <div class="sr-image-holder">
                            <img class="thumb" src="<?=base_url("static/images/".$image->file)?>"/>
                        </div>
                        <h5><?=$image->name?></h5>
                    </li>
                    <? } ?>
                </ul>
                <h2>Header Image</h2>
                <img src="<?=base_url("static/header/{$row->headerimg}")?>">
            </div>
        </div>
    </il>
</ul>