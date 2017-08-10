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
  MagratheaDebugger::Instance()->SetType(MagratheaDebugger::DEV)->LogQueries(false);


	$Smarty = new Smarty;
	$Smarty->template_dir = $site_path."/app/Views/";
	$Smarty->compile_dir  = $site_path."/app/Views/_compiled";
	$Smarty->config_dir   = $site_path."/app/Views/configs";
	$Smarty->cache_dir    = $site_path."/app/Views/_cache";
//	$Smarty->debugging = true;
	
	// initialize the MagratheaView and sets it to Smarty	
	$Smarty->assign("View", MagratheaView::Instance());
	MagratheaView::Instance()->IsRelativePath(false); // for mod_rewrite

?>
