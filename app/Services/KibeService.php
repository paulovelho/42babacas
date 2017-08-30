<?php

class KibeService {

  private $tweets = array();
  private $twitter;
  private $simulate = false;
  private $historical = false;
  private $log = array();

  private $dead_thinkers = [
    "_pavan_", "bomdiaporque", "microcontoscos",
    "bomsenhor", "joaoluisjr", "paulovelho", 
    "brunafeia", "SeuQualquer", "thiagomava"];
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

  public function GetTweets() {
    return $this->tweets;
  }

  public function PrintTweets() {
    foreach ($this->tweets as $tw) {
      echo $tw."\n";
    }
  }

  private function Post($status, $delay=0) {
    if ( empty($status->id) ) {
      $this->Log("ERROR: Trying to post an empty tweet...");
      return false;
    }
    $tweet = new Tweet();
    $tweet->Build($status);
    if( $this->simulate ) {
      $this->Log("SIMULATING ONLY: posting tweet {".$tweet->text."} (rate: ".$status->rate.") from @".$tweet->user_name.", originally posted on {".$status->TweetDate()."} ".
        ($delay > 0 ? "with delay of ".$delay : ""));
      return true;
    }
    sleep($delay);
    $postedStatus = $this->twitter->PostTweet($tweet->text);
    if ($postedStatus && $postedStatus->id) {
      $this->Log("posted tweet {".$tweet->text."}[rate: ".$tweet->rate."] from @".$tweet->user_name.", originally posted on {".$status->TweetDate()."}");
      return $tweet->Log($status->log)->Post($postedStatus->id);
    } else {
      $this->Log("failed posting tweet {".$tweet->text."}");
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
      $delay = rand(0, 1800);
      if( $tweet->rate < 20 ) {
        $this->Log("low rate tweet: {".$tweet->text."}");
        return false;
      }
      return $this->Post($tweet, $delay);
    }
  }

  // we check in our database if we already posted this status
  private function WasAlreadyPosted($status) {
    $this->Log("checking if tweet {".$status->id."} was already posted...");
    $existing = TweetsControl::GetByOriginalId($status->id);
    if ($existing->id) {
      $this->Log("tweet with text {".$status->text."} already exists with id {".$existing->id."}");
      return true;
    }
    return false;
  }

  /* GETTING TWEETS */
  public function GetInspiration($thinkers) {
    foreach ($thinkers as $arroba) {
      $this->Log("looking for inspiration on @".$arroba."'s twitter...");
      $this->tweets = array_merge($this->tweets, $this->GetTweetsFrom($arroba));
    }
  }
  public function GetTweetsFrom($arroba) {
    return $this->twitter->GetTweetsFrom($arroba, 10);
  }

  /* KIBAR */ 
  public function Kibar() {
    $this->historical = false;
    $inspiration = ThinkersControl::GetRandomThinkers(5);
    $this->GetInspiration($inspiration);
    $this->tweets = $this->RateTweets($this->tweets);
    if( count($this->tweets) == 0 ) {
      $this->Log("no good tweets found... closing...");
      return false;
    }
    $this->Log("got ".count($this->tweets)." tweets with maximum rate of ".$this->tweets[0]->rate." and minimum of ".$this->tweets[count($this->tweets)-1]->rate);
    $this->PrintTweets();
    if ($this->PickupTweetToPost()) {
      $this->Log("transaction ended: successfully posted");
      return true;
    } else {
      $this->Log("transaction ended: no more tweets available");
      $this->HistoricalKibe();
    }
  }

  /* HISTORY */
  public function HistoricalKibe($word_seed=null) {
    if (empty($word_seed)) {
      $word_seed = $this->GetPopularWord();
      $this->Log("Getting Historical Kibe - TOP WORD: [".$word_seed."]");
    } else {
      $this->Log("Getting Historical Kibe - WORD SEED: [".$word_seed."]");
    }
    if (empty($word_seed)) {
      $this->Log("empty seed; ending process.... ");      
      return false;
    }
    $this->historical = true;
    $dead_thinker = $this->dead_thinkers[array_rand($this->dead_thinkers, 1)];
    $this->tweets = $this->GetHistoryFrom($dead_thinker, $word_seed);
    $this->Log("HISTORICAL TWEETS: got ".count($this->tweets)." tweets".
        " from [".$dead_thinker."]".
        " with maximum rate of ".$this->tweets[0]->rate.
        " and minimum of ".$this->tweets[count($this->tweets)-1]->rate);
    $this->PrintTweets();
    if ($this->PickupTweetToPost()) {
      $this->Log("transaction ended: successfully posted");
      return true;
    } else {
      $this->Log("transaction ended: no historical tweets available");
    }
  }
  public function GetPopularWord() {
    $words = $this->GenerateWordCloud();
    $words = $this->LimitWords($words);
    return key($words);
  }
  public function GetHistoryFrom($arroba, $query) {
    $tweets = $this->twitter->BrowserSearchFrom($arroba, $query, 5);
    return $this->RateTweets($tweets);
  }

  public function GenerateWordCloud() {
    $cloud = array();
    $all_status = "";
    foreach ($this->tweets as $st) {
      $all_status .= " ".$st->text;
    }
    preg_match_all('(\w{5,})u', $all_status, $match_arr);
    $word_arr = $match_arr[0];
    foreach ($word_arr as $word) {
      $word = htmlentities(strtolower($word));
      if (empty($word)) continue;
      if ( empty($cloud[$word]) ) $cloud[$word] = 0;
      $cloud[$word] ++;
    }
    arsort($cloud);
    return $cloud;
  }
  public function LimitWords($cloud) {
    foreach ($cloud as $key => $value) {
      if ($value < 3) {
        unset($cloud[$key]);
      }
    }
    return $cloud;
  }

  /* RATE SYSTEM */
  public function RateTweets($tweets) {
    $filteredTw = array();
    foreach ($tweets as $tw) {
      if( $tw->Rate($this->historical) > 0 ) {
        array_push($filteredTw, $tw);
      }
    }
    usort($filteredTw, array($this, "compareRates"));
    return $filteredTw;
  }
  private function compareRates($a, $b) {
    return strcmp($b->rate, $a->rate);
  }
  private function compareWords($a, $b) {
    return strcmp($b["count"], $a["count"]); 
  }

  /* LOG */
  public function Log($l) {
    array_push($this->log, $l);
    LoggerService::Instance()->Log($l);
    return $this;
  }
  public function ClearLog() {
    LoggerService::Instance()->Log("\n--*--\n");
    unset($this->log);
    $this->log = array();
    return $this;
  }
  public function SaveLog() {
    $c = new Cycle();
    $c->SetAction($this->historical ? "historical" : "post")
      ->Add($this->log)
      ->Save();
  }
}

?>
