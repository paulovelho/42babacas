<?php

class FarmService {
  
  private $twitter;

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

  public function Seed() {
    $sdvs = $this->GetSDV();
    print_r($sdvs);
  }

}

?>
