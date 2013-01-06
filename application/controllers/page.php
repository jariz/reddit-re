<?

    class page extends CI_Controller {
        public function __construct() {
            parent::__construct();

            $this->load->library(array("parser", "session", "form_validation", "reddit", "jariz"));
            $this->load->helper(array("url", "file", "form", "string", "date", "security"));
            //$this->output->enable_profiler(TRUE);
            $this->load->library("reddit_oauth", array("oauth_id" => $this->config->item("oauth_id"), "oauth_secret" => $this->config->item("oauth_secret"), "redirect_url" => "http://template.reddit.re/auth"));
            $this->load->database();
        }

        public function go($page) {
            if(in_array("$page.php", get_filenames("application/views/pages"))) {
                $view = $this->load->view("pages/$page", "", true);
            } else show_404();

            $this->parser->parse("custom/template", array(
                "content" => $view, "usersettings" => $this->load->view("modules/usersettings", "", true)
            ));
        }

        public function addStuffToView($view) {
            if(isset($_GET["hide"]))
                $this->session->set_userdata("hide", true);

            $view = $this->load->view("modules/modlog_nav", "", true).$view;

            if(!$this->session->userdata("hide"))
                $view = $this->load->view("modules/annoyingreminder", "", true).$view;

            return $view;
        }

        public function modlog($page) {
            if(in_array("$page.php", get_filenames("application/views/modlog"))) {
                $view = $this->load->view("modlog/$page", "", true);
            } else {
                $q = $this->db->query("SELECT * FROM entries WHERE hash = \"{$this->db->escape_str($page)}\"");
                if($q->num_rows > 0) $view = $this->load->view("custom/modlog", array("query" => $q), true);
                else show_404();
            }

            $view = $this->addStuffToView($view);

            $this->parser->parse("custom/template", array(
                "content" => $view, "usersettings" => $this->load->view("modules/usersettings", "", true)
            ));
        }

        public function _404() {
            $this->go("404");
        }

        public function modLogsWithFilter($filters) {
            if(is_int(@$_GET["page"]))
                $page = $this->db->escape_str(@$_GET["page"]);
            else $page = 1;

            //construct query
            $selectors = "";
            foreach($filters as $field) {
                if($field["bot_table"]) {
                    $qqq = $this->db->query("SELECT * FROM bots WHERE {$field["field"]} = \"{$field["value"]}\"");
                    if($qqq->num_rows > 0)
                        if(strlen($selectors) == 0) $selectors = "WHERE botid = {$qqq->row()->ID}";
                        else $selectors .= " AND botid = {$qqq->row()->ID}";
                } else {
                    if(strlen($selectors) == 0) $selectors = "WHERE {$field["field"]} = \"{$field["value"]}\"";
                    else $selectors .= " AND {$field["field"]} = \"{$field["value"]}\"";
                }
            }

            $c = $this->db->query("SELECT COUNT(*) FROM entries $selectors")->row_array();
            $count = $c["COUNT(*)"];

            if($page > 0 && $page <= (int)ceil($count/10)) {
                //page = page
            } else $page = 1;

            if(strlen($selectors) > 0)
                $q2 = $this->db->query("SELECT * FROM entries $selectors ORDER BY timestamp DESC LIMIT ".(($page-1)*10).",10");
            else $q2 = $this->db->query("SELECT * FROM entries LIMIT 0,0");

            $view = $this->load->view("modules/modlog_filter", array("filter" => uri_string()), true);
            $n = 0;
            foreach($q2->result() as $entry) {
                $n++;
                $view .= $this->load->view("custom/modlog", array("query" => $this->db->query("SELECT * FROM entries WHERE guid = \"{$entry->guid}\"")), true);
            }

            if($n == 0) $view .= "<h6>No results found</h6>";

            $view = $this->addStuffToView($view);

            if($n != 0) {
                if(($page*10) == (int)ceil($count/10)*10) $next_enabled = " disabled";
                else $next_enabled = "";

                if($page == 1) $prev_enabled = " disabled";
                else $prev_enabled = "";

                if(empty($next_enabled)) $next = "?page=".($page+1);
                else $next = "javascript:void(null)";

                if(empty($prev_enabled)) $prev = "?page=".($page-1);
                else $prev = "javascript:void(null)";

                $view .= $this->load->view("modules/pagination", array("entries" => $count, "page" => $page, "prev" => $prev, "next" => $next, "next_enabled" => $next_enabled, "prev_enabled" => $prev_enabled), true);
            }

            $this->parser->parse("custom/template", array(
                "content" => $view, "usersettings" => $this->load->view("modules/usersettings", "", true)
            ));
        }

        public function modbot() {
            $this->parser->parse("custom/template", array(
                "content" => $this->load->view("custom/modbot", null, true), "usersettings" => $this->load->view("modules/usersettings", "", true)
            ));
        }

        public function filter() {
            $f = 0;
            $filters = array();
            for($i = 1; ($c = $this->uri->segment($i)) != false; $i++) {
                $f++;
                if($f == 2) {
                    $f = 0;
                    $filter = array();
                    switch($this->uri->segment($i-1)) {
                        case "b":
                            $filter["bot_table"] = true;
                            $filter["field"] = "usr";
                            break;
                        case "u":
                            $filter["bot_table"] = false;
                            $filter["field"] = "username";
                            break;
                        case "r":
                            $filter["bot_table"] = true;
                            $filter["field"] = "src_sub";
                            break;
                        case "t":
                            $filter["bot_table"] = false;
                            $filter["field"] = "action";
                            break;
                        default:
                            $filter = null;
                            break;
                    }
                    if($filter != null) {
                        $filter["value"] = $this->db->escape_str($c);
                        array_push($filters, $filter);
                    }
                }
            }
            $this->modLogsWithFilter($filters);
        }

        public function index() {
            switch($_SERVER['HTTP_HOST']) {
                case "modlog.reddit.re":
                    $this->modlog("home");
                    break;
                case "template.reddit.re":
                    $this->template("home");
                    break;
                default:
                    $this->go("home");
                    break;
            }
        }

        function subreddit_exists($sub) {
            if(!$this->reddit->subredditExists($sub)) {
                $this->form_validation->set_message('subreddit_exists', 'The %s does not exist');
                return false;
            } else return true;
        }
}