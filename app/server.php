<?php

require("inc/global.php");
include($magrathea_path."/MagratheaServer.php");

include("Services/FarmService.php");
include("Services/KibeService.php");
include("Services/LoggerService.php");
include("Services/RateService.php");
include("Services/TwitterService.php");

MagratheaModel::IncludeAllModels();

//error_reporting(E_ALL ^ E_STRICT);

class TwitterServer extends MagratheaServer{

  private $terminal;

  public function GetAuth() {
    if( $_SERVER["argc"] > 1 ) {
      return $_SERVER["argv"][1];
    }
    return $_REQUEST["auth"];
  }

  public function ValidateAuth(){
    $key = $this->GetAuth();
    if ($key == "simulate") {
      $this->SimulateKibe();
      die("---\n---\nSIMULATION ENDED");
    }
    $secret = MagratheaConfig::Instance()->GetFromDefault("access_key");
    if($key != $secret) {
      $this->Json(array("success" => false, "error" => 403, "message" => "Authorization failed."));
      LoggerService::Instance()->Log("Authorization failed.");
      die;
    } else return true;
  }

  public function Run() {
    $this->terminal = (PHP_SAPI === 'cli');
    if (!$this->terminal) echo "<pre>";
    $this->validateAuth();
    $this->RunSomething();
  }

  private function RunSomething() {
    if ($this->ChanceOf( 100 )) {
      $this->FarmFollower();
    } else {
      $this->PostATweet();
    }
  }

  private function FarmFollower() {
    FarmService::Farm()->FollowSomeone();
  }

  private function PostATweet() {
    $posted = $this->Kibar();
    if ($this->ChanceOf($posted ? 25 : 75)) {
      $this->HistoricalKibe();
    }
  }

  private function Kibar() {
    KibeService::Otariano()->Kibar();
    KibeService::Otariano()->SaveLog();
  }
  private function HistoricalKibe() {
    KibeService::Otariano()->ClearLog();
    KibeService::Otariano()->HistoricalKibe(); 
    KibeService::Otariano()->SaveLog();
  }

  public function SimulateKibe() {
    KibeService::Otariano()->Simulate()->Kibar();
  }

  public function ChanceOf($chance) {
    return (rand(1,100) <= $chance);
  }

  public function Test() {
    FarmService::Farm()->FollowSomeone();
  }
}

$server = new TwitterServer();
$server->Test();
// $server->Start();

// cron job:
//
//  */30 * * * * wget --delete-after -q http://contato.website.com.br/server.php?post 
//

?>
