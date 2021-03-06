<? class jariz {
    public function encrypt($decrypted) {
        $ci = & get_instance();
        $salt = $ci->config->item("salt");
        $password = $ci->config->item("password");
        // Build a 256-bit $key which is a SHA256 hash of $salt and $password.
        $key = hash('SHA256', $salt . $password, true);
        // Build $iv and $iv_base64.  We use a block size of 128 bits (AES compliant) and CBC mode.  (Note: ECB mode is inadequate as IV is not used.)
        srand(); $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC), MCRYPT_RAND);
        if (strlen($iv_base64 = rtrim(base64_encode($iv), '=')) != 22) return false;
        // Encrypt $decrypted and an MD5 of $decrypted using $key.  MD5 is fine to use here because it's just to verify successful decryption.
        $encrypted = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $decrypted . md5($decrypted), MCRYPT_MODE_CBC, $iv));
        // We're done!
        return $iv_base64 . $encrypted;
    }

    public function printResult($field, $bool=null, $bid) {
        $ci = & get_instance();
        if(!isset($bool)) {
            //load from cache
            $q = $ci->db->query("SELECT $field FROM checks WHERE botid = $bid")->row_array();
            if((bool)$q[$field]) echo "<span class=\"badge badge-success\">Yes</span>";
            else echo "<span class=\"badge badge-important\">NO!</span>";
        } else {
            //load realtime & put into cache
            $q = $ci->db->query("SELECT * FROM checks WHERE botid = $bid");
            
            if($q->num_rows() == 0) $ci->db->query(sprintf("INSERT INTO checks (ID, botid, timestamp, $field) VALUES (NULL, %s, %s, %s)", $bid, (string)time(), (string)(int)$bool));
            else $ci->db->query(sprintf("UPDATE checks SET timestamp = %s, $field = %s WHERE botid = %s", (string)time(), (string)(int)$bool, $bid));
            
            if($bool) echo "<span class=\"badge badge-success\">Yes</span>";
            else echo "<span class=\"badge badge-important\">NO!</span>";
        }
    }

    public function loggedin() {
        $ci = & get_instance();
        $s = $ci->session->userdata("uid");
        if($s != false && !empty($s)) return $ci->db->query("SELECT id FROM users WHERE id = $s")->num_rows() > 0;
        else return false;
    }
    
    public function getProp($prop) {
        $ci = & get_instance();
        $s = $ci->session->userdata("uid");
        $x = $ci->db->query("SELECT $prop FROM users WHERE ID = $s")->row_array();
        return $x["$prop"];
    }

    public function setProp($prop,$val) {
        $ci = & get_instance();
        $s = $ci->session->userdata("uid");
        $x = $ci->db->query("UPDATE users SET $prop = \"{$ci->db->escape_str($val)}\" WHERE ID = $s");
    }

    public function getSubredditData($sub) {

        //OUTDATED: god bless the reddit api gods for better methods to get this now
        /*
        $r = @file_get_contents("http://www.reddit.com/r/$sub/about/stylesheet");
        if($r == false) return false;
        $s = explode("<code class=\"language-css\">", $r);
        if(!isset($s[1])) return false;
        $b = explode("</code>", $s[1]);
        return $b[0];
        */

        $r = @file_get_contents("http://www.reddit.com/r/$sub/about/stylesheet.json");
        $c = @file_get_contents("http://www.reddit.com/r/$sub/about.json");
        if($r == false || $c == false) return false;
        $stylesheet = json_decode($r);
        $about = json_decode($c);
        $final["css"] = $stylesheet->data->stylesheet;
        $img_path = $_SERVER['DOCUMENT_ROOT']."/static/header/";
        write_file($img_path.($hdr=random_string().".png"), @file_get_contents($about->data->header_img));
        $final["header"] = $hdr;
        $img_path = $_SERVER['DOCUMENT_ROOT']."/static/images/";
        foreach($stylesheet->data->images as $image) {
            write_file($img_path.($img=random_string().".png"), @file_get_contents($image->url));
            $final["images"][] = (object)array("image" => $img, "name" => $image->name);
        }
        return (object)$final;
    }

    public function decrypt($encrypted) {
        $ci = & get_instance();
        $salt = $ci->config->item("salt");
        $password = $ci->config->item("password");
        // Build a 256-bit $key which is a SHA256 hash of $salt and $password.
        $key = hash('SHA256', $salt . $password, true);
        // Retrieve $iv which is the first 22 characters plus ==, base64_decoded.
        $iv = base64_decode(substr($encrypted, 0, 22) . '==');
        // Remove $iv from $encrypted.
        $encrypted = substr($encrypted, 22);
        // Decrypt the data.  rtrim won't corrupt the data because the last 32 characters are the md5 hash; thus any \0 character has to be padding.
        $decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, base64_decode($encrypted), MCRYPT_MODE_CBC, $iv), "\0\4");
        // Retrieve $hash which is the last 32 characters of $decrypted.
        $hash = substr($decrypted, -32);
        // Remove the last 32 characters from $decrypted.
        $decrypted = substr($decrypted, 0, -32);
        // Integrity check.  If this fails, either the data is corrupted, or the password/salt was incorrect.
        if (md5($decrypted) != $hash) return false;
        // Yay!
        return $decrypted;
    }

    public function var_string($var) {
        ob_start();
        var_dump($var);
        return ob_get_clean();
    }

    public function redditFail() {
        show_error("There was a problem communicating with the reddit server, please try again later", 500, "Internal Error");
    }

    public function renderScreenshot($url) {
        $f = $_SERVER['DOCUMENT_ROOT']."/static/snapshots/";
        $img = random_string().".png";
        $cmd = "xvfb-run --server-args=\"-screen 0, 1366x960x24\" wkhtmltoimage --use-xserver --width 1360 --height 960 $url $f$img";
        exec($cmd, $out);
        //var_dump($cmd, $out);
        return $img;
    }

    public function getTag($action) {
        switch($action) {
            case "banuser":
            case "removelink":
            case "uninvitemoderator":
            case "acceptmoderatorinvite":
            case "removecontributor": 
            case "removemoderator":
            case "removecomment":
            case "marknsfw":
            case "wikibanned":
            case "removewikicontributor":
                return "important";

            case "approvelink":
            case "wikicontributor":
            case "approvecomment":
            case "addmoderator":
            case "invitemoderator":
            case "addcontributor":
            case "unbanuser":
            case "wikiunbanned":
                return "success";

            case "editsettings": 
            case "editflair": 
            case "wikirevise": 
            case "wikipermlevel":
            case "distinguish":
                return "warning";
            default:
                return "";
        }
    }

    public function formatDesc($desc) {
        $modded_desc = html_entity_decode($desc);
        $modded_desc = preg_replace('/\s+/', ' ', preg_replace('/<[^>]*>/', ' ', $modded_desc));
        $modded_desc = preg_replace('/[^a-zA-Z0-9_ %\[\]\.\(\)%&-]/s', '', $modded_desc);
        $modded_desc = str_replace("&quot", "\"", $modded_desc);
        return $modded_desc;
    }
}