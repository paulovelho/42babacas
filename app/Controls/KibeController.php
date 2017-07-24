<?php

class KibeController {

  private $tweets = array();
  private $twitter;

  // singleton:
  protected static $inst = null;

  public static function Otariano(){
    if(!isset(self::$inst)){
      self::$inst = new KibeController();
    }
    return self::$inst;
  }
  private function __construct(){
    $this->twitter = new TwitterController();
  }

  public function Kibar() {
    $this->GetInspiration(array("harpias", "braunermegda", "oiluiz", "rodpocket"));
    $this->tweets = $this->RateTweets($this->tweets);
    p_r($this->tweets);
  }

  public function GetInspiration($thinkers) {
    foreach ($thinkers as $arroba) {
      $this->tweets = array_merge($this->tweets, $this->GetTweetsFrom($arroba));
    }
  }

  public function GetTweetsFrom($arroba) {
    return $this->twitter->GetTweetsFrom($arroba, 10);
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
