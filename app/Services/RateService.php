<?php

class RateService {
  // singleton:
  protected static $inst = null;
  protected $historical = true;
  private $rate;
  private $status;
  public $log = "";

  // Singleton instance:
  public static function Instance(){
    if(!isset(self::$inst)){
      self::$inst = new RateService();
    }
    return self::$inst;
  }
  private function __construct(){
  }

  private function Initialize() {
    $this->rate = 50;
    $this->ClearLog();
  }

  public function Load($status) {
    $this->Initialize();
    $this->status = $status;
    return $this;
  }

  /* 
   * this is a fucking complex rate system...
   * it was developed by @paulovelho in 2017-07-25,
   *   during a particular boring workday
   * the logic was taken out from my own ass
   *   but basically:
   *    - if it have an entity (image, url) or it's a reply, it gets zero rating.
   *    - we have to measure the popularity. We measure it by [likes] and [retweets].
   *    - we want a popular tweet to have higher rate.
   *    - we want a EXTREMELY popular tweet to have a lower rate.
   *    - hashtags should lower the rating (they are boring)
   *    - foreign tweets higher the rating (they might seem more original)
   */
  public function Rate() {
    $this->Log("rating [".$this->status->text."] from @".$this->status->arroba." {entities: ".$this->status->has_entities.", reply_to: ".$this->status->reply_to."} ");

    if(!$this->IsRateable()) return 0;
    $this->EntitiesRate();
    $this->LikabilityRate();
    $this->AuthorRate();
    $this->NormalizeRate();
    $this->Log("final rate: ".$this->rate);
    return $this->rate;
  }
  public function HistoryRate() {
    $this->Rate();
    $this->EntitiesRate(); // entities will be counted twice
    $this->TimeRate();
    return $this->rate;
  }

  public function IsRateable() {
    if ( 
        $this->status->has_entities || 
        !empty($this->status->reply_to) ||
        !$this->status->in_portuguese 
      ) {
      $this->rate = 0;
      return false;
    }
    return true;
  }

  public function LikabilityRate() {
    $log = "\n--- likability rate: ";
    $rate = $this->rate;
    // rate with retweet:
    if ( $this->status->retweets > 1000 ) {
      $rate = $rate - ($this->status->retweets / 200);
    } else {
      if ( $this->status->retweets > 500 ) {
        $rate = $rate + ($this->status->retweets / 70);
      } else {
        if ( $this->status->retweets < 5 ) {
          $rate = $rate - 30;
        } else {
          $rate = $rate + ($this->status->retweets / 20);
        }
      }
    }
    $log .= "retweets: ".$this->status->retweets.", rating ".$rate."; ";

    // rate with like:
    if ( $this->status->likes > 1000 ) {
      $rate = $rate - ($this->status->likes / 80);
    } else {
      $rate = $rate + ($this->status->likes / 40);
    }
    $log .= "likes: ".$this->status->likes.", rating ".$rate."; ";
    $log .= "[rate: ".$rate.";]";
    $this->Log($log);
    $this->rate = $rate;
    return $rate;
  }
  public function AuthorRate() {
    $log = "\n--- authors rate: ";
    $rate = $this->rate;
    $log .= "followers: ".$this->status->author_followers."; ";
    // lower rate for popular authors
    $author_rate = ($this->status->author_followers / 5000);
    if ($author_rate > 90) $author_rate = 90;
    $rate = $rate - $author_rate;
    $log .= "[rate: ".$rate.";]";
    $this->Log($log);
    $this->rate = $rate;
    return $rate;
  }
  public function EntitiesRate() {
    $log = "\n--- entities rate: ";
    $rate = $this->rate;
    // hashtags should lower the rate as well (-20 points per hashtag):
    $rate = $rate - (count($this->status->entities["hashtags"]) * 20);
    $log .= "hashtags: ".count($this->status->entities["hashtags"])."; ";

    $log .= "[rate: ".$rate.";]";
    $this->Log($log);
    $this->rate = $rate;
    return $rate;
  }
  public function TimeRate() {
    $log = "\n--- time rate: ";
    $rate = $this->rate;
    // older the tweet, higher the rate
    $now = time();
    $timeDifference = $now - $this->status->createdAt;
    $six_months = 15770000;
    $log .= "time difference: ".$timeDifference.", (".floor($timeDifference/86400)." days ago); ";
    // don't change for the last 6 months
    if ($timeDifference < $six_months) return $rate;

    // if have hashtags, it doesn't get time rates
    if (count($this->status->entities["hashtags"]) > 0) return $rate; 

    $time_rate = ($timeDifference - $six_months) / 1000000;
    if($time_rate > 75) $time_rate = 75;
    $rate += $time_rate;
    $log .= "[rate: ".$rate.";]";
    $this->Log($log);
    $this->rate = $rate;
    return $rate;
  }

  public function NormalizeRate() {
    if( $this->rate > 100 ) $this->rate = 100;
    if( $this->rate < 0 ) $this->rate = 0;
    $log .= "[rate: ".$this->rate.";]";
    $this->Log($log);
    return $this->rate;
  }

  public function GetLog() {
    return $this->log;
  }
  private function ClearLog() {
    $this->log = "";
  }
  private function Log($log) {
    $this->log .= "{".$log."}; ";
  }


}

?>
