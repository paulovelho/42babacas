<?php

  $magratheaSingle = true; 
  include("../app/inc/config.php");
  include($magrathea_path."/LOAD.php");

  function initConfig() {
    MagratheaConfig::Instance()->setPath(__DIR__."/..");
  }
  function getSitePath() {
    return "../app/";
  }
  initConfig();

  require_once(MagratheaConfig::Instance()->GetMagratheaPath()."/libs/simpletest/autorun.php");
  require_once(MagratheaConfig::Instance()->GetMagratheaPath()."/libs/simpletest/web_tester.php");
  SimpleTest::prefer(new TextReporter());

  include("kibeTester.php");


?>
