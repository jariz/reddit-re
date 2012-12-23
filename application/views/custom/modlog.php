<? $row = $query->row(); $bot = $this->db->query("SELECT * FROM bots WHERE ID = {$row->botid}")->row(); ?> 
<h2><span class="modactions <?=$row->action?>"></span> <a href="<?=base_url()?>u/<?=$row->username?>"><?=$row->username?></a> <?=$row->desc?> in <a href="/r/<?=$bot->src_sub?>"><?=$bot->src_sub?></a> <?=strtolower(timespan($row->timestamp))?> ago</h2>
<p><strong>Time:</strong> <?=timespan($row->timestamp)?> ago</p>
<p><strong>Reported by bot:</strong> <a href="<?=base_url()?>b/<?=$bot->usr?>"><?=$bot->usr?></a></p>
<p><strong>In subreddit:</strong> <a href="/r/<?=$bot->src_sub?>"><?=$bot->src_sub?></a></p>
<p><strong>Tag:</strong> <a href="<?=base_url()?>t/<?=$row->action?>"><span class="badge badge-<?=$this->jariz->getTag($row->action)?>"><?=$row->action?></span></a></p>
<h6>
    <a href="<?=base_url().$row->hash?>">Permalink</a>
    <a href="http://reddit.com/submit?title=<?=urlencode("{$row->username} {$this->jariz->formatDesc($row->desc)} [{$row->action}]")?>&url=http://modlog.reddit.re/<?=$row->hash?>&resubmit=true<?=$row->hash?>">Share</a> 
    <a href="<?=base_url()?>r/<?=$bot->src_sub?>">Browse by all from this subreddit</a>
    <a href="<?=base_url()?>u/<?=$row->username?>">Browse by all from this moderator</a>
    <a href="<?=base_url()?>t/<?=$row->action?>">Browse by all with this tag</a>
    <a href="http://twitter.com/intent/tweet?related=<?=urlencode("@_jariz")?>&text=I+shared+a+reddit.re+modlog+entry&url=<?=urlencode("http://modlog.reddit.re/".$row->hash)?>&via=reddit_re">Tweet</a>
</h6>
<hr>