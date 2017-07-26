<?php

class KibeService {

  private $tweets = array();
  private $twitter;
  private $log = "";

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
    $this->twitter = new TwitterService();
  }

  public function Kibar() {
    $this->GetInspiration(array("harpias", "braunermegda", "oiluiz", "rodpocket", "ulissesmattos"));
//    $this->GetInspiration(array("harpias"));
    $this->tweets = $this->RateTweets($this->tweets);
    $this->Log("got ".count($this->tweets)." tweets with maximum rate of ".$this->tweets[0]->rate." and minimum of ".$this->tweets[count($this->tweets)-1]->rate);
    if ($this->PickupTweetToPost()) {
      $this->Log("transaction ended: successfully posted");
    } else {
      $this->Log("transaction ended: no more tweets available");
    }
  }

  private function PickupTweetToPost() {
    if (count($this->tweets) == 0) return false;
    $key = 0;
    $tweet = $this->tweets[$key];
    if ( !$this->Post($tweet) ) {
      array_splice($this->tweets, $key, 1);
      return $this->PickupTweetToPost();
    } else {
      return true;
    }
  }

  private function Post($status) {
    $existing = TweetsControl::GetByTweetId($status->id);
    if ($existing->id) {
      return false;
    }
    $tweet = new Tweet();
    $tweet->Build($status);
    $postedStatus = $this->twitter->Post($status->text);
    if ($postedStatus && $postedStatus->id) {
      $this->Log("posted tweet {".$status->text."} with id ".$postedStatus->id);
      return $tweet->Log($this->log)->Post($postedStatus->id);
    } else {
      $this->Log("failed posting tweet {".$status->text."}");
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
    $l = "::- ".$l."\n";
    echo $l;
    $this->log .= $l;
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
