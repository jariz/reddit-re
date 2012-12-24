<? if ($this->jariz->loggedin()) if ($this->jariz->getProp("dev") == 1) { ?>

<div class="well">
    <?=read_file("error.txt")?>
</div>

<? } else show_error("You're not supposed to be here....");