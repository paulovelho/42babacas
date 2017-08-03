<?php

include(__DIR__."/Base/CycleBase.php");

class Cycle extends CycleBase {
  public $robot = "";
  
  public function SetAction($act) {
    $this->action = $act;
    return $this;
  }

  public function Add($log) {
    if(is_array($log)) {
      $this->log = implode("; ", $log);
    } else {
      $this->log = $log;
    }
    return $this;
  }
}

class CycleControl extends CycleControlBase {

}

?>
