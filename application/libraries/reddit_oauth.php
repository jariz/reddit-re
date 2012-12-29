<?
/* JariZ's awesome codeiginiter reddit wrapper */

class reddit_oauth {

    protected $reddit;
    function __construct($params) {
        $ci = &get_instance();
        require_once 'api/RedditOAuth.php';
        $this->reddit = new RedditOAuth($params["oauth_id"], $params["oauth_secret"], $params["redirect_url"]);
    }

    function __call($method, $args) {
        return call_user_func_array(array($this->reddit, $method), $args);
    }
}