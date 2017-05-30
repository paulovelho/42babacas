<?php

require("inc/global.php");
include($magrathea_path."/MagratheaServer.php");

MagratheaModel::IncludeAllModels();
include("Controls/FarmController.php");
include("Controls/TwitterController.php");

//error_reporting(E_ALL ^ E_STRICT);

class TwitterServer extends MagratheaServer{


  public function Run() {
    $this->FarmFollower();
  }

  public function FarmFollower() {
    $farmController = new FarmController();
    $farmController->Seed();
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
