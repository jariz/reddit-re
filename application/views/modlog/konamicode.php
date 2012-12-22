<?
    $log = "<span style=\"color:#C00;\">Reddit.re KonamiCode 1.0 initializing....</span>\n";
    foreach($this->db->query("SELECT * FROM bots")->result() as $bot) {
        //var_dump($bot);
        $log .= "   Getting entries for bot #{$bot->ID} (<i>{$bot->usr}</i>)\n";
        if($bot->disabled == "0") {
            if($this->reddit->login($bot->usr, $this->jariz->decrypt($bot->pwd)) == FALSE) {
                $log .= "       Login invalid.\n";
            } else {
                $result = $this->reddit->getModLog($bot->src_sub);
                $i = -1;
                $n = 0;
                foreach($result[0] as $entry) {
                    $i++;
                    if($this->db->query("SELECT guid FROM entries WHERE guid = \"{$result[1][$i]}\"")->num_rows() == 0) {
                        $hash = random_string("alnum", 5);
                        $this->db->query("INSERT INTO entries VALUES (NULL, ".strtotime($result[2][$i]).", \"{$this->db->escape_str($result[4][$i])}\", \"{$this->db->escape_str($result[6][$i])}\", \"{$this->db->escape_str($result[7][$i])}\", {$bot->ID}, \"{$this->db->escape_str($result[1][$i])}\", \"{$hash}\")");
                        $row = $this->db->query("SELECT * FROM entries WHERE guid = \"{$result[1][$i]}\"")->row();
                        //$modded_desc = str_replace("&#32", "", $row->desc);
                        $modded_desc = $this->jariz->formatDesc($row->desc);
                        $this->reddit->submit($bot->dst_sub, "link", sprintf("%s %s [%s]", $row->username, $modded_desc, $row->action), "http://modlog.reddit.re/$hash");
                        $n++;
                    }
                }
                $this->db->query("UPDATE bots SET lastcrawl = ".time()." WHERE id = {$bot->ID}");
                $log .= "       Success. $n new entries added & posted.\n";
            }

            sleep(1); }
        else $log .= "       Bot disabled. Skipping.\n";
    }


    echo "<pre>$log</pre>";
?>