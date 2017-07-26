<?php

class KibeService {

  private $tweets = array();
  private $twitter;
  private $simulate = false;
  private $log = array();

  // singleton:
  protected static $inst = null;

  // Singleton instance:
  public static function Otariano(){
    if(!isset(self::$inst)){
      self::$inst = new KibeService();
    }
    return self::$inst;
  }
  private function __construct(){
    $this->twitter = TwitterService::Instance();
  }

  public function Simulate() {
    $this->simulate = true;
    return $this;
  }

  public function Kibar() {
    $this->GetInspiration(array("oiluiz", "rodpocket", "ulissesmattos"));
    $this->tweets = $this->RateTweets($this->tweets);
    if($this->simulate) {
      p_r($this->tweets);
    }
    $this->Log("got ".count($this->tweets)." tweets with maximum rate of ".$this->tweets[0]->rate." and minimum of ".$this->tweets[count($this->tweets)-1]->rate);
    if ($this->PickupTweetToPost()) {
      $this->Log("transaction ended: successfully posted");
      return true;
    } else {
      $this->Log("transaction ended: no more tweets available");
      return false;
    }
  }

  private function PickupTweetToPost() {
    if (count($this->tweets) == 0) return false;
    $key = 0;
    $tweet = $this->tweets[$key];
    if ( $this->WasAlreadyPosted($tweet) ) {
      array_splice($this->tweets, $key, 1);
      return $this->PickupTweetToPost();
    } else {
      return $this->Post($tweet);
    }
  }

  // we check in our database if we already posted this status
  private function WasAlreadyPosted($status) {
    $existing = TweetsControl::GetByTweetId($status->id);
    if ($existing->id) {
      $this->Log("tweet with text {".$status->text."} already exists with id {".$existing->id."}");
      return true;
    }
    return false;
  }

  private function Post($status) {
    $tweet = new Tweet();
    $tweet->Build($status);
    if( $this->simulate ) {
      $this->Log("SIMULATING ONLY: posting tweet {".$tweet->text."}");
      return true;
    }
    $postedStatus = $this->twitter->Post($tweet->text);
    if ($postedStatus && $postedStatus->id) {
      $this->Log("posted tweet {".$tweet->text."} with id ".$postedStatus->id);
      return $tweet->Log($this->log)->Post($postedStatus->id);
    } else {
      $this->Log("failed posting tweet {".$tweet->text."}");
      return false;
    }
  }

  public function GetInspiration($thinkers) {
    foreach ($thinkers as $arroba) {
      $this->tweets = array_merge($this->tweets, $this->GetTweetsFrom($arroba));
    }
  }

  public function GetTweetsFrom($arroba) {
    return $this->twitter->GetTweetsFrom($arroba, 10);
  }

  public function Log($l) {
    array_push($this->log, $l);
    LoggerService::Instance()->Log($l);
  }
  
  /* RATE SYSTEM */
  public function RateTweets($tweets) {
    $filteredTw = array();
    foreach ($tweets as $tw) {
      if( $tw->Rate() > 0 ) {
        array_push($filteredTw, $tw);
      }
    }
    usort($filteredTw, array($this, "compareRates"));
    return $filteredTw;
  }
  private function compareRates($a, $b) {
    return strcmp($b->rate, $a->rate);
  }

  /* DEPRECATED: our tweet entities already have this information... */
  public static function CheckForMentions($tweet) {
    if ( preg_match("/(^|[^a-z0-9_])@([a-z0-9_]+)/i", $tweet) ) {
      return true;
    } else {
      return false;
    }
  }

}

?>
