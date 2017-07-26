<?php

require(__DIR__."/../lib/twitteroauth-0.7.2/autoload.php");
include_once(__DIR__."/../Models/Status.obj.php");
use Abraham\TwitterOAuth\TwitterOAuth;

class TwitterService {
  
  private $connection;

  // singleton:
  protected static $inst = null;

  // Singleton instance:
  public static function Instance(){
    if(!isset(self::$inst)){
      self::$inst = new TwitterService();
    }
    return self::$inst;
  }
  function __construct() {
    $this->Connect();
  }

  public function Connect() {
    $consumer_key = MagratheaConfig::Instance()->GetFromDefault("twitter_key");
    $consumer_token = MagratheaConfig::Instance()->GetFromDefault("twitter_secret");
    $access_token = MagratheaConfig::Instance()->GetFromDefault("twitter_token");
    $access_token_secret = MagratheaConfig::Instance()->GetFromDefault("twitter_token_secret");
    $this->connection = new TwitterOAuth($consumer_key, $consumer_token, $access_token, $access_token_secret);
  }

  public function CheckOk($status) {
    if ($status->errors) {
      $error = $status->errors[0];
      echo "ERROR POSTING STATUS: {[".$error->code."]: ".$error->message."}\n";
      return false;
    }
    return true;
  }

  public function Credentials() {
    return $this->connection->get("account/verify_credentials");
  }

  public function Post($tweet) {
    $postData = $this->connection->post("statuses/update", ["status" => $tweet]);
    if ($this->CheckOk($postData)) {
      return new Status($postData);
    } else {
      return false;
    }
  }

  public function GetTrending() {
    $brasil_id = "23424768";
    return $this->connection->get("trends/place", ["id" => $brasil_id]);
  }

  /* USERS */
  public function GetUserInfo($arroba) {
    return $this->connection->get("users/show", ["screen_name" => $arroba]);
  }

  public function GetUserId($arroba) {
    $user = $this->GetUserInfo($arroba);
    return $user->id;
  }

  /* GET TWEETS */
  public function GetTweetsFrom($user, $count) {
    $data = array('count' => $count);
    if( is_int($user) ) {
      $data["user_id"] = $user;
    } else {
      $data["screen_name"] = $user;
    }
    $tweets = $this->connection->get("statuses/user_timeline", $data);
    return $this->ConvertIntoStatuses($tweets);
  }

  public function GetTweet($tweet_id) {
    $tweet = $this->connection->get("statuses/show", ["id" => $tweet_id, "include_entities" => true]);
    return new Status($tweet);
  }

  /* FOLLOWING */
  public function Follow($user_id) {
    return $this->connection->post("friendships/create", ["user_id" => $user_id]);
  }

  public function Unfollow($user_id) {
    return $this->connection->post("friendships/destroy", ["user_id" => $user_id]);
  }

  /* HELPERS */
  private function ConvertIntoStatuses($status){
    $tweets = array();
    foreach ($status as $st) {
      array_push($tweets, new Status($st));
    }
    return $tweets;
  }
}

?>