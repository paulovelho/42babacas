<?php
  include("inc/global.php");
  include($magrathea_path."/MagratheaAdmin.php"); // $magrathea_path should already be declared
 
  $admin = new MagratheaAdmin(); // adds the admin file
  $admin->Load(); // load!
?>
