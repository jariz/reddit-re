<div class="pagination pagination-centered">
    <ul>
        <li class="<?=$prev_enabled?>"><a href="<?=$prev?>">Prev</a></li>
        <? $n = 0; $g = 0; for($i = 0; $i < $entries; $i++) { $g++; if($g == 10) { $n++; if($page == $n) $c = " class=\"active\""; else $c = ""; echo "<li$c><a href=\"?page=$n\">$n</a></li>"; $g = 0; }  } if($g != 0) { $n++; if($page == $n) $c = " class=\"active\""; else $c = ""; echo "<li$c><a href=\"?page=$n\">$n</a></li>"; }  ?>
        <li class="<?=$next_enabled?>"><a href="<?=$next?>">Next</a></li>
    </ul>
</div>