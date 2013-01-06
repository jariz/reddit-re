<?php

namespace RedditApiClient;

require_once 'Account.php';
require_once 'Comment.php';
require_once 'HttpRequest.php';
require_once 'HttpResponse.php';
require_once 'Link.php';
require_once 'RedditException.php';
require_once 'Subreddit.php';

/**
 * Reddit
 *
 * The main class of the API client, handling its state and the sending of
 * queries to the API
 *
 * @author    Henry Smith <henry@henrysmith.org>
 * @copyright 2011 Henry Smith
 * @license   GPLv2.0
 * @package   Reddit API Client
 * @version   0.5.2
 */
class Reddit
{

    /**
     * If logged in, stores the value of the reddit_session cookie
     *
     * @access private
     * @var    string
     */
    private $sessionCookie;

    /**
     * Stores the last seen modhash value
     *
     * The modhash is an anti-XSRF measure. It's a value returned with each
     * set of data, different each time, that must be passed along with the
     * next request.
     *
     * @access private
     * @var    string
     */
    private $modHash;

    /**
     * Creates the API client instance
     *
     * If given a username and password, will attempt to login.
     *
     * @access public
     * @param  string $username [optional]  The username to login with
     * @param  string $password [optional]  The password to login with
     */
    public function __construct($username = null, $password = null)
    {
        if ($username && $password && !$this->login($username, $password)) {
            $message = 'Unable to login to Reddit';
            $code = RedditException::UNABLE_TO_LOGIN;
            throw new RedditException($message, $code);
        }
    }

    /**
     * Tries to login to Reddit
     *
     * @access public
     * @return boolean
     */
    public function login($username, $password)
    {
        //var_dump(array($username, $password));
        $request = new HttpRequest;
        $request->setUrl('http://www.reddit.com/api/login/' . $username);
        $request->setHttpMethod('POST');
        $request->setPostVariable('user', $username);
        $request->setPostVariable('passwd', $password);
        $request->setPostVariable('api_type', 'json');

        $request->setHeader("User-Agent", "Reddit.re Modtools, Session " . random_string());

        $response = $request->getResponse();

        if ($response == null) $this->redditFail();
        $resp = $response->getBody();
        $body = json_decode($resp);

        if (isset($body->json->data->modhash) && isset($body->json->data->cookie)) {
            $this->modHash = $body->json->data->modhash;
            $this->sessionCookie = $body->json->data->cookie;
            return true;
        }

        return false;
    }

    /**
     * Indicates whether the client is logged in as a Reddit user
     *
     * @access public
     * @return boolean
     */
    public function isLoggedIn()
    {
        if ($this->sessionCookie === null) {
            return false;
        }

        return true;
    }

    /**
     * Sends a request to Reddit and returns the response received
     *
     * @access public
     * @param  string $verb  'GET', 'POST', ...
     * @param  string $url   'http://www.reddit.com/comments/6nw57.json'
     * @param  string $body
     * @return array
     */
    public function sendRequest($verb, $url, $body = '', $nullerror = true)
    {
        $request = new HttpRequest;
        $request->setUrl($url);
        $request->setHttpMethod($verb);

        $request->setHeader("User-Agent", "Reddit.re Modtools, Session " . random_string());

        if ($verb === 'POST' && is_array($body)) {
            foreach ($body as $name => $value) {
                $request->setPostVariable($name, $value);
            }
        }

        if ($this->sessionCookie !== null) {
            $request->setCookie('reddit_session', $this->sessionCookie);
        }

        $response = $request->getResponse();

        if ($nullerror) {
            if ($response == null) $this->redditFail();
            if (!($response instanceof HttpResponse)) $this->redditFail();
        }

        $responseBody = $response->getBody();
        $response = json_decode($responseBody, true);

        if (isset($response['data']['modhash'])) {
            $this->modHash = $response['data']['modhash'];
        } elseif (isset($response[0]['data']['modhash'])) {
            $this->modHash = $response[0]['data']['modhash'];
        }

        return $response;
    }

    public function isMod($user, $sub, $override = false)
    {
        $resp = $this->sendRequest("GET", "http://www.reddit.com/r/$sub/about/moderators.json", '', false);
        if($resp == null) return false;
        foreach ($resp["data"]["children"] as $entry) {
            if (strtolower($entry["name"]) == strtolower($user)) return true;
        }

        if (!$override) {
            // Apperently we're not moderator. Let's try to accept any invitations
            $this->acceptModeratorInvite($sub);
            // Try again
            return $this->isMod($user, $sub, true);
        }
    }

    private $modlog_res;

    public function getModLog($sub)
    {
        //v1 : parse as html because json sucks and doesn't give anything usual (parameters sr and mod can't be looked up)
        $request = new HttpRequest;
        $request->setUrl("http://www.reddit.com/r/$sub/about/log");
        $request->setHttpMethod("GET");
        $request->setCookie('reddit_session', $this->sessionCookie);
        if ($this->sessionCookie !== null) {
            $request->setCookie('reddit_session', $this->sessionCookie);
        }
        $response = $request->getResponse();
        $this->modlog_res = $response;
        if (!($response instanceof HttpResponse)) return array();
        $body = $response->getBody();
        //print($body);
        preg_match_all('/(?-i:<tr class="modactions" style="background-color: rgb\(255,255,255\)" data-fullname="ModAction_)([\da-f]{8}-[\da-f]{4}-[\da-f]{4}-[\da-f]{4}-[\da-f]{12})(?-i:"><td class="timestamp whitespace:nowrap"><time title=")[^\n\r"]+(?-i:" datetime=")([^\n\r"]+)">[^\n\r<]+(?-i:<\/time>)[^\n\r<]+(?-i:<\/td><td class="whitespace:nowrap"><a href=")(\bhttp:\/\/www\.reddit\.com(?:\/[]\d!"#$%&\'()*+,.:;<=>?@[\\\\_`a-z{|}~^-]+){3}\/[]\d!"#$%&\'()*+,.:;<=>?@[\\\\_`a-z{|}~^-]+(?:\?[]\d!"#$%&\'()*+,.\/:;<=>?@[\\\\_`a-z{|}~^-]*)?) >([^\n\r<]{12,})(?-i:<\/a><\/td><td class="button"><a href=")(\bhttp:\/\/www\.reddit\.com(?:\/[]\d!"#$%&\'()*+,.:;<=>?@[\\\\_`a-z{|}~^-]+){3}\/[]\d!"#$%&\'()*+,.:;<=>?@[\\\\_`a-z{|}~^-]+(?:\?[]\d!"#$%&\'()*+,.\/:;<=>?@[\\\\_`a-z{|}~^-]*)?)(?-i:" class="modactions )(\p{Ll}+)(?-i:" ><\/a><\/td><td class="description">)(.+?)(?-i:<\/td><\/tr>)/iu', $body, $result, PREG_PATTERN_ORDER);
        return $result;
    }

    /**
     * Fetches and returns the link with the given ID
     *
     * @access public
     * @param  string $linkId
     * @return \RedditApiClient\Link
     */
    public function getLink($linkId, $withComments = false)
    {
        $verb = 'GET';

        if ($withComments) {
            $url = "http://www.reddit.com/comments/{$linkId}.json";
        } else {
            $url = "http://www.reddit.com/by_id/t3_{$linkId}.json";
        }

        $response = $this->sendRequest($verb, $url);

        $link = null;

        if (!$withComments && isset($response['data']['children'][0])) {

            $link = new Link($this);
            $link->setData($response['data']['children'][0]['data']);
        } elseif ($withComments && isset($response[0]['data']['children'][0]['data'])) {

            $link = new Link($this);
            $link->setData($response[0]['data']['children'][0]['data']);
        }

        $comments = array();

        if (isset($response[1]['data']['children'])) {

            foreach ($response[1]['data']['children'] as $data) {

                $comment = new Comment($this);
                $comment->setData($data['data']);

                if (isset($comment['author'])) {
                    $comments[] = $comment;
                }
            }
        }

        if (($link instanceof Link) && $withComments) {
            $link->setComments($comments);
        }

        return $link;
    }

    /**
     * Fetches and returns a user account
     *
     * @access public
     * @param  string $name
     * @return \RedditApiClient\Account
     */
    public function getAccountByUsername($name)
    {
        $verb = 'GET';
        $url = "http://www.reddit.com/user/{$name}/about.json";

        $response = $this->sendRequest($verb, $url);

        $account = new Account($this);
        $account->setData($response['data']);

        return $account;
    }

    /**
     * Returns an array of links posted by the account with the given username
     *
     * @access public
     * @param  string $name
     * @return array
     */
    public function getLinksByUsername($name)
    {
        $verb = 'GET';
        $url = "http://www.reddit.com/user/{$name}.json";

        $response = $this->sendRequest($verb, $url);

        $links = array();

        foreach ($response['data']['children'] as $child) {

            $link = new Link($this);
            $link->setData($child['data']);

            $links[] = $link;
        }

        return $links;
    }

    /**
     * Returns an array of the links in a subreddit
     *
     * @access public
     * @param  string $subredditName  Plain-text name
     * @return array
     */
    public function getLinksBySubreddit($subredditName)
    {
        $verb = 'GET';
        $url = "http://www.reddit.com/r/{$subredditName}.json";

        $response = $this->sendRequest($verb, $url);

        $links = array();

        if (!isset($response['data']['children'])
            || !is_array($response['data']['children'])
        ) {
            $message = "No such subreddit {$subredditName}";
            $code = RedditException::NO_SUCH_SUBREDDIT;
            throw new RedditException($message, $code);
        }

        foreach ($response['data']['children'] as $child) {

            $link = new Link($this);
            $link->setData($child['data']);

            $links[] = $link;
        }

        return $links;
    }

    /**
     * Fetches and returns an array of the subreddits to which the logged-in user
     * is subscribed
     *
     * @access public
     * @return array
     */
    public function getMySubreddits()
    {
        if (!$this->isLoggedIn()) {
            $message = 'No user is logged in to list subreddit subscriptions';
            $code = RedditException::LOGIN_REQUIRED;
            throw new RedditException($message, $code);
        }

        $verb = 'GET';
        $url = 'http://www.reddit.com/reddits/mine.json';

        $response = $this->sendRequest($verb, $url);

        $subreddits = array();

        foreach ($response['data']['children'] as $child) {

            $subreddit = new Subreddit($this);
            $subreddit->setData($child['data']);

            $subreddits[] = $subreddit;
        }

        return $subreddits;
    }

    public function subredditExists($subreddit)
    {
        $request = new HttpRequest;
        $request->setUrl("http://www.reddit.com/r/$subreddit.json");
        ;
        $request->setHttpMethod("GET");
        $resp = $request->getResponse();
        if ($resp == null) return false;
        $hdrs = $resp->getHeaders();
        return substr($hdrs[0], 9, 3) != '302';
    }

    /**
     * Posts a comment in reply to a link or comment
     *
     * @access public
     * @param  string $parentId
     * @param  string $text
     * @return boolean
     */
    public function comment($parentId, $text)
    {
        if (!$this->isLoggedIn()) {
            $message = 'Cannot post a comment without a valid login';
            $code = RedditException::LOGIN_REQUIRED;
            throw new RedditException($message, $code);
        }

        $verb = 'POST';
        $url = 'http://www.reddit.com/api/comment';
        $data = array(
            'thing_id' => $parentId,
            'text' => $text,
            'uh' => $this->modHash,
        );

        $response = $this->sendRequest($verb, $url, $data);

        if (!is_array($response) || !isset($response['jquery'])) {
            return false;
        }

        foreach ($response['jquery'] as $element) {

            if (isset($element[3][0][0])) {
                continue;
            }

            if (strpos($element[3][0], '.error') === true) {
                return false;
            }
        }


        return true;
    }

    /**
     * Casts a vote for a comment or link
     *
     * @access public
     * @param  string  $thingId
     * @param  integer $direction  1 for upvote, -1 for down, 0 to remove vote
     * @return boolean
     */
    public function vote($thingId, $direction)
    {
        if (!$this->isLoggedIn()) {
            $message = 'Cannot vote without a valid login';
            $code = RedditException::LOGIN_REQUIRED;
            throw new RedditException($message, $code);
        }

        $verb = 'POST';
        $url = 'http://www.reddit.com/api/vote';
        $data = array(
            'thing_id' => $thingId,
            'dir' => $direction,
            'uh' => $this->modHash,
        );

        $response = $this->sendRequest($verb, $url, $data);

        if (empty($response)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Saves a link or comment
     *
     * @access public
     * @param  string $thingId
     * @return boolean
     */
    public function save($thingId)
    {
        if (!$this->isLoggedIn()) {
            $message = 'Cannot save things without being logged in';
            $code = RedditException::LOGIN_REQUIRED;
            throw new RedditException($message, $code);
        }

        $verb = 'POST';
        $url = 'http://www.reddit.com/api/save';
        $data = array(
            'id' => $thingId,
            'uh' => $this->modHash,
        );

        $response = $this->sendRequest($verb, $url, $data);

        if (empty($response)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Unsaves a link or comment
     *
     * @access public
     * @param  string $thingId
     * @return boolean
     */
    public function unsave($thingId)
    {
        if (!$this->isLoggedIn()) {
            $message = 'Cannot unsave things without being logged in';
            $code = RedditException::LOGIN_REQUIRED;
            throw new RedditException($message, $code);
        }

        $verb = 'POST';
        $url = 'http://www.reddit.com/api/unsave';
        $data = array(
            'id' => $thingId,
            'uh' => $this->modHash,
        );

        $response = $this->sendRequest($verb, $url, $data);

        if (empty($response)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Hides a link or comment
     *
     * @access public
     * @param  string $thingId
     * @return boolean
     */
    public function hide($thingId)
    {
        if (!$this->isLoggedIn()) {
            $message = 'Cannot hide things without being logged in';
            $code = RedditException::LOGIN_REQUIRED;
            throw new RedditException($message, $code);
        }

        $verb = 'POST';
        $url = 'http://www.reddit.com/api/hide';
        $data = array(
            'id' => $thingId,
            'uh' => $this->modHash,
        );

        $response = $this->sendRequest($verb, $url, $data);

        if (empty($response)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Unhides a link or comment
     *
     * @access public
     * @param  string $thingId
     * @return boolean
     */
    public function unhide($thingId)
    {
        if (!$this->isLoggedIn()) {
            $message = 'Cannot hide things without being logged in';
            $code = RedditException::LOGIN_REQUIRED;
            throw new RedditException($message, $code);
        }

        $verb = 'POST';
        $url = 'http://www.reddit.com/api/unhide';
        $data = array(
            'id' => $thingId,
            'uh' => $this->modHash,
        );

        $response = $this->sendRequest($verb, $url, $data);

        if (empty($response)) {
            return true;
        } else {
            return false;
        }
    }

    public function acceptModeratorInvite($r)
    {
        $this->sendRequest("POST", "http://www.reddit.com/api/accept_moderator_invite", array("executed" => "you are now a moderator. welcome to the team!",
            "r" => $r,
            "uh" => $this->modHash,
            "renderstyle" => "html"), false);

    }

    public function redditFail()
    {
        $ci =& get_instance();
        $ci->jariz->redditFail();
    }

    /**
     * Submits a link
     *
     * @access public
     * @param  string $subredditName   e.g. 'pics', *not* '/r/pics'
     * @param  string $linkType        either 'self' or 'link'
     * @param  string $title           The title of the submission
     * @param  string $linkUrl         Either the URL or the self-text
     * @return boolean
     */
    public function submit($subredditName, $linkType, $title, $linkUrl)
    {
        if (!$this->isLoggedIn()) {
            $message = 'Cannot submit links without being logged in';
            $code = RedditException::LOGIN_REQUIRED;
            throw new RedditException($message, $code);
        }

        $verb = 'POST';
        $url = 'http://www.reddit.com/api/submit';
        $data = array(
            'uh' => $this->modHash,
            'kind' => $linkType,
            'sr' => $subredditName,
            'title' => $title,
            'url' => $linkUrl,
        );

        $response = $this->sendRequest($verb, $url, $data);
        if (!is_array($response) || !isset($response['jquery'])) {
            return false;
        }

        // Possible errors that sending the request might return with
        // Unless I'm missing something, Reddit returns 'error.BAD_CAPTCHA.field-captcha'
        // no matter what, whether the post was successful or not.
        $errors_array = array('.error.SUBREDDIT_NOTALLOWED.field-sr', '.error.RATELIMIT.field-ratelimit', 'you aren\'t allowed to post there.', 'that reddit doesn\'t exist');

        foreach ($response['jquery'] as $element) {

            if (isset($element[3][1])) {
                continue;
            }

            foreach ($element[3] as $value) {

                $error = implode(" ", $errors_array);

                if ($value && strpos($error, $value)) {
                    return false;
                }
            }
        }

        return true;
    }

}

