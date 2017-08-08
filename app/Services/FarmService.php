<?php

class FarmService {
  
  private $twitter;

  // singleton:
  protected static $inst = null;
  // Singleton instance:
  public static function Farm(){
    if(!isset(self::$inst)){
      self::$inst = new FarmService();
    }
    return self::$inst;
  }
  function __construct() {
    $this->twitter = new TwitterService();
    return $this;
  }

  public function GetSDV() {
    $trending = $this->twitter->GetTrending();
    $sdvs = array();
    foreach ($trending[0]->trends as $trend) {
      $tt = strtolower($trend->name);
      if (strpos($tt, "sdv") !== false) {
        array_push($sdvs, $trend);
      }
    }
    return $sdvs;
  }
  public function GetSDVHash() {
    $trending = $this->GetSDV();
    if(count($trending) == 0) return false;
    return $trending[0]->name;
  }

  public function GetTweetsFromHash($hash) {
    return $this->twitter->GetSearch($hash);
  }

  public function FollowSomeone() {
    $this->Log("Following someone... ");
    $hash = $this->GetSDVHash();
    if(!$hash) return false;
    $this->Log("Getting someone using the hash [".$hash."]");
    $result = $this->GetTweetsFromHash($hash);
    $tonto = $result[0];
    return $this->Follow($tonto->user);
  }

  public function Follow($user_id) {
    $this->Log("following ".$user_id);
    if( FollowFarmControl::UserExists($user_id) ){
      $this->Log("we already follow this guy");
      return false;
    }
    $follow = new FollowFarm();
    $follow->user_id = $user_id;
    $follow->followed_on = now();
    $twFollow = $this->twitter->Follow($user_id);
    if (empty($twFollow) ) return false;
    $this->Log("followed ".$twFollow->screen_name);
    return $follow->Save();
  }

  /* LOG */
  public function Log($l) {
    LoggerService::Instance()->Log($l);
    return $this;
  }

}

?>
