<?php

require("inc/global.php");
include($magrathea_path."/MagratheaServer.php");

MagratheaModel::IncludeAllModels();
include("Controls/TwitterController.php");

//error_reporting(E_ALL ^ E_STRICT);

class TwitterServer extends MagratheaServer{

  private $twitter;

  public function Run() {
    $this->Load()->post();
  }

  public function Load() {
    $this->twitter = new TwitterController();
    return $this;
  }

  public function Post() {
    $now = now();
    $tweet = $this->twitter->Post("now it's ".$now);
    print_r($tweet);
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
