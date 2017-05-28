<?php

  session_start();
//  error_reporting(1);

  $path = realpath(__DIR__.'/../../lib');
  set_include_path(get_include_path().PATH_SEPARATOR.$path);

  include("config.php");
 
  // looooooaaaadddiiiiiinnnnnnggggg.....
  include($magrathea_path."/LOAD.php");

  // debugging settings:
  // options: dev; debug; log; none;
  MagratheaDebugger::Instance()->SetType(MagratheaDebugger::LOG)->LogQueries(false);
 
?>
