<?php

require("inc/global.php");
include($magrathea_path."/MagratheaServer.php");

include("Services/FarmService.php");
include("Services/KibeService.php");
include("Services/LoggerService.php");
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
    $this->Kibar();
  }

  private function FarmFollower() {
    $farmController = new FarmService();
    $farmController->Seed();
  }

  private function Kibar() {
    KibeService::Otariano()->Kibar();
  }

  public function SimulateKibe() {
    KibeService::Otariano()->Simulate()->Kibar();
  }

  public function Test() {
    $th = new Thinkers();
    $th->name = "paulovelho";
    $th->twitter_id = "31196133";
    $th->ideas_stolen = 0;
    $th->active = true;
    $th->Save();
    print_r($th);
//    $info = KibeService::Otariano()->Simulate()->GetHistoryFrom("bomdiaporque", "amor");
//    print_r($info);
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
