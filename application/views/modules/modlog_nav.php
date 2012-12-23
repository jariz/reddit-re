<div class="subnav">
    <ul class="nav nav-pills">
        <li><a href="<?=base_url()?>">Create your own</a></li>
        <?if(!$this->jariz->loggedin()) { ?><li><a href="<?=base_url()?>login">Login</a></li><? } else { ?>
        <li><a href="<?=base_url()?>account">Bot settings</a></li>
        <li><a href="<?=base_url()?>logout">Log out</a></li><? } ?>
        <li><a href="<?=base_url()?>r">Browse trough all logs</a></li>
    </ul>
  </div>