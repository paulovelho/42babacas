<?php

class FarmService {
  
  private $twitter;
  private $cycle;

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
    $this->cycle = new Cycle();
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
    $this->cycle->SetAction("follow");
    $this->Log("Following someone... ");
    $hash = $this->GetSDVHash();
    if(!$hash) return $this->Finish();
    $this->Log("Getting someone using the hash [".$hash."]");
    $result = $this->GetTweetsFromHash($hash);
    $tonto = $result[0];
    return $this->Follow($tonto->user);
  }

  public function UnfollowSomeone() {
    $this->cycle->SetAction("unfollow");
    $this->Log("Unfollowing someone...");
    $user = FollowFarmControl::GetToUnfollow();
    if (!$user->user_id) {
      $this->Log("no one to unfollow...");
      return $this->Finish();
    }
    $twResponse = $this->twitter->Unfollow($user->user_id);
    if (empty($twResponse)) return false;
    $this->Log("unfollowed ".$twResponse->screen_name);
    $user->unfollowed_on = now();
    $this->EndCycle();
    return $user->Save();
  }

  public function Follow($user_id) {
    $this->Log("following ".$user_id);
    $userToFollow = FollowFarmControl::GetByTwitterId($user_id);
    if( !empty($userToFollow->id); ){
      $this->Log("we already follow this profile");
      $this->RemoveFarmUser($userToFollow);
      return $this->Finish();
    }
    $follow = new FollowFarm();
    $follow->user_id = $user_id;
    $follow->followed_on = now();
    $twFollow = $this->twitter->Follow($user_id);
    if (empty($twFollow) ) return $this->Finish();
    $this->Log("followed ".$twFollow->screen_name);
    $this->EndCycle();
    return $follow->Save();
  }

  public function RemoveFarmUser($user) {
    if( !empty($user->unfollowed_on) ) {
      $this->Log("we already unfollow this profile as well (unfollowed on ".$user->unfollowed_on.")");
      $unfollowDate = new DateTime($user->unfollowed_on);
      $now = now();
      if($unfollowDate->diff($now)->days > 30) {
        $this->Log("deleting him...");
        return $user->Delete();
      }
    }
    return false;
  }

  /* LOG */
  public function Log($l) {
    $this->cycle->Add($l);
    LoggerService::Instance()->Log($l);
    return $this;
  }

  public function Finish() {
    $this->EndCycle();
    return false;
  }
  public function EndCycle() {
    $this->cycle->Save();
    return $this;
  }

}

?>
