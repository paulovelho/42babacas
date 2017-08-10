<?php

require("inc/global.php");
include($magrathea_path."/MagratheaServer.php");

include("Services/ActionService.php");
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

  public function GetAction() {
    if( $_SERVER["argc"] > 2 ) {
      return $_SERVER["argv"][2];
    } else return false;
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
    $action = $this->GetAction();
    if ($action) {
      $this->RunAction($action);
    } else {
      $this->RunSomething();
    }
  }

  private function RunSomething() {
    $action = ActionService::Instance()->Action();
    $this->RunAction($action);
  }

  private function RunAction($action) {
    switch ($action) {
      case 'kibe':
        $this->Kibar();
        break;
      case 'history':
        $this->HistoricalKibe();
        break;
      case 'farm':
        $this->FarmFollower();
        break;
      case 'unfollow';
        $this->FarmUnfollow();
        break;
      case 'none':
        echo "no action! that was easy!";
        break;
      case 'test':
        $this->Test();
        break;
      default:
        echo "action invalid: {".$action."}";
        break;
    }
  }

  private function FarmFollower() {
    return FarmService::Farm()->FollowSomeone();
  }
  private function FarmUnfollow() {
    return FarmService::Farm()->UnfollowSomeone();
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
    $tweets = KibeService::Otariano()->GetTweets();
    if ( count($tweets) == 0 ) {
      $this->Kibar();
    }
    KibeService::Otariano()->ClearLog();
    KibeService::Otariano()->HistoricalKibe(); 
    KibeService::Otariano()->SaveLog();
  }

  public function SimulateKibe() {
    KibeService::Otariano()->Simulate()->Kibar();
    KibeService::Otariano()->Simulate()->HistoricalKibe(); 
  }

  public function Test() {
    LoggerService::Instance()->Log("test");
  }

}

$server = new TwitterServer();
$server->Run();
// $server->Start();

// cron job:
//
//  */30 * * * * wget --delete-after -q http://contato.website.com.br/server.php?post 
//

?>
