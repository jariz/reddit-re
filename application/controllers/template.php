<?php
class template extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->library(array("parser", "session", "jariz"));
        $this->load->helper(array("file", "url", "date", "form"));
        $this->load->library("reddit_oauth", array("oauth_id" => $this->config->item("oauth_id"), "oauth_secret" => $this->config->item("oauth_secret"), "redirect_url" => base_url() . "auth"));
        $this->load->database();
    }

    public function index()
    {
        $this->go("home");
    }

    public function go($page)
    {
        if (in_array("$page.php", get_filenames("application/views/template"))) {
            $view = $this->load->view("template/$page", "", true);
        } else show_404();

        $this->parser->parse("custom/template", array(
            "content" => $view, "usersettings" => $this->load->view("modules/usersettings", "", true)
        ));
    }

    public function apiOutput($error, $msg, $extra_params = null)
    {
        $arr = array("error" => $error, "msg" => $msg);
        if (isset($extra_params))
            foreach ($extra_params as $name => $value) {
                $arr[$name] = $value;
            }
        die(json_encode($arr));
    }

    public function getSubreddits()
    {
        $accesstoken = $this->jariz->getProp("accesstoken");
        if (!empty($accesstoken)) {
            $this->reddit_oauth->setAccessToken($accesstoken);
            $res = $this->reddit_oauth->fetch("reddits/mine/moderator.json");
            if ($res['code'] == 200) {
                $children = $res["result"]["data"]["children"];
                foreach ($children as $sub) $subreddits[] = $sub["data"]["display_name"];
                return $subreddits;
            } else return false;
        } else return false;
    }

    public function snapshot($snap) {
        $q = $this->db->query("SELECT * FROM snapshots WHERE hash = \"{$this->db->escape_str($snap)}\"");
        if($q->num_rows > 0)
            $this->parser->parse("custom/template", array(
                "content" => $this->load->view("custom/snapshot", array("row" => $q->row()), true), "usersettings" => $this->load->view("modules/usersettings", "", true)
            ));
        else show_404();
    }

    public function api()
    {
        $function = $this->uri->segment(2);
        $params = $this->uri->segment_array();
        $this->output->set_content_type("json");
        if (!$this->jariz->loggedin()) $this->apiOutput(true, "You're not logged in (anymore)");
        switch ($function) {
            case "new-snap":
                if (isset($params[3])) {
                    if (($subreddits = $this->getSubreddits()) != false) {
                        if (in_array($params[3], $subreddits)) {
                            $snap = $this->jariz->renderScreenshot("http://reddit.com/r/{$params[3]}");
                            $css = $this->jariz->getSubredditCSS($params[3]);
                            $this->db->query("INSERT INTO snapshots VALUES (NULL, \"{$this->db->escape_str($params[3])}\", \"{$this->db->escape_str($css)}\", \"$snap\", ".time().", {$this->session->userdata("uid")}, \"".random_string('alnum', 5)."\")");
                            $this->apiOutput(false, null);
                        } else $this->apiOutput(true, "This is not your subreddit");
                    } else $this->apiOutput(true, "Invalid session");
                } else $this->apiOutput(true, "Invalid input");
                break;
            default:
                $this->apiOutput(true, "Unknown API function");
        }
    }
}
