<div class="filter"<?if(isset($bsett)) echo "style=\"margin-top:25px;\""; ?> data-bsett="<?=(int)isset($bsett)?>" data-fstring="<?=$filter?>">
    <p>Filters:</p>

    <div class="filter-input">
        <select>
            <?if (!isset($bsett)) { ?><option value="r">With subreddit:</option> <? } ?>
            <option value="u">With user:</option>
            <option value="t">With tag:</option>
            <?if (!isset($bsett)) { ?>
            <option value="r">With bot:</option><? } ?>
        </select>
        <input type="text" placeholder="Value">
        <a class="btn btn-primary">Add</a>
    </div>
    <? $afilter = explode("/", $filter);
    //var_dump($afilter);
    $i = -1;
    $x = -1;
    foreach ($afilter as $f) {
        $i++;
        $x++;
        if ($i == 1) {
            //var_dump($afilter[$i - 1], $f);
            switch ($afilter[$x - 1]) {
                case "r":
                    if (!isset($bsett)) $z = "With subreddit:";
                    else $z = "";
                    break;
                case "b":
                    if (!isset($bsett)) $z = "With bot:";
                    else $z = "";
                    break;
                case "u":
                    $z = "With user:";
                    break;
                case "t":
                    $z = "With tag:";
                    break;
                default:
                    $z = "";
                    break;
            }
            if (!empty($z)) {
                ?>
                <div data-ftype="<?=$afilter[$x - 1]?>" data-fvalue="<?=$f?>" class="well well-small filter-item">
                    <div class="left"> <?=$z?> <?
                        $a = $this->jariz->getTag($f);
                        if (!empty($a)) $a = " badge-" . $a;
                        else $a = "badge-info";
                        $f = "<span class=\"badge " . $a . "\">" . $f . "</span>";

                        echo $f ?>
                    </div>
                    <div class="right">
                        <a href="javascript:void(null)" data-fdelete="1" class="btn btn-danger btn-mini"><i
                                class="icon-remove icon-white"></i></a>
                    </div>
                </div>
                <?
            }
            $i = -1;
        }
    }
    ?>
</div>