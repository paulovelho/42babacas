<?php

require("inc/global.php");
include($magrathea_path."/MagratheaServer.php");

MagratheaModel::IncludeAllModels();
MagratheaController::IncludeAllControllers();

//error_reporting(E_ALL ^ E_STRICT);

class TwitterServer extends MagratheaServer{


  public function Run() {
    $this->Test();
  }

  public function FarmFollower() {
    $farmController = new FarmController();
    $farmController->Seed();
  }

  public function Kibar() {
    KibeController::Otariano()->Kibar();
  }

  public function Test() {
    $twitter = new TwitterController();
    $info = $twitter->GetTweet(890060733031043072);
    print_r($info);
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
