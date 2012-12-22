<? 

/* JariZ's awesome codeiginiter reddit wrapper */

class reddit {

    protected $reddit;
    function __construct() {
        require_once 'api/Reddit.php';
        $this->reddit = new \RedditApiClient\Reddit;
    }
    
    function __call($method, $args) {
        return call_user_func_array(array($this->reddit, $method), $args);
    }
}