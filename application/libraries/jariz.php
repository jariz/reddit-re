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