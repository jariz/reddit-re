<? if($this->jariz->loggedin()) if(($token = $this->reddit_oauth->Auth()) != false) {
    $this->jariz->setProp("accesstoken", $token);
    header("Location: ".base_url());
} else {} else show_error("<a href=\"http://reddit.re\">Please log in</a>", 403, "Not logged in");