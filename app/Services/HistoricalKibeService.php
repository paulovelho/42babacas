<?php

class HistoricalKibeService extends KibeService {

  // singleton:
  private static $inst = null;

  // Singleton instance:
  public static function Otariano(){
    if(!isset(self::$inst)){
      self::$inst = new HistoricalKibeService();
    }
    return self::$inst;
  }
  private function __construct(){
    $this->twitter = TwitterService::Instance();
  }

  /* HISTORY */
  public function Kibar($word_seed=null) {
    if (empty($word_seed)) {
      $word_seed = $this->GetDeadWord();
      $this->Log("Getting Historical Kibe - TOP WORD: [".$word_seed."]");
    } else {
      $this->Log("Getting Historical Kibe - WORD SEED: [".$word_seed."]");
    }
    if (empty($word_seed)) {
      $this->Log("empty seed; ending process.... ");      
      return false;
    }
    $this->historical = true;
    $dead_thinker = $this->GetDeadThinker();
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

  /* getting data from files */
  public function GetDeadThinker() {
    $thinkers = $this->LoadFromFile("dead_thinkers.txt");
    return $thinkers[array_rand($thinkers, 1)];
  }
  public function GetDeadWord() {
    $words = $this->LoadFromFile("dead_words.txt");
    return $words[array_rand($words, 1)];
  }

  public function LoadFromFile($file) {
    $path = MagratheaConfig::Instance()->GetFromDefault("site_path");
    $fileData = file($path."/".$file, FILE_IGNORE_NEW_LINES);
    return $fileData;
  }


}

?>
