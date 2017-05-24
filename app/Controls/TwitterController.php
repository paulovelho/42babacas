<?php

require("lib/twitteroauth-0.7.2/autoload.php");
use Abraham\TwitterOAuth\TwitterOAuth;

class TwitterController {
  
  private $connection;

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

  public function Credentials() {
    return $this->connection->get("account/verify_credentials");
  }

  public function Post($tweet) {
    $postData = $this->connection->post("statuses/update", ["status" => $tweet]);
    return new Tweet($postData);
  }
}

?>
