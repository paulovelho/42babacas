<?php

  include("inc/global.php"); 
  include("Controls/_Controller.php");
  MagratheaController::IncludeAllControllers();
  MagratheaModel::IncludeAllModels();

  $controller = new TweetsController();
  $controller->Index();

  // Magrathea Route will get the path to the correct method in the right class:
  MagratheaRoute::Instance()
    ->Route($control, $action, $params);

  try{
    MagratheaController::Load("Tweets", "Index");
  } catch (Exception $ex) {
    Debug($ex);
  }


?>
