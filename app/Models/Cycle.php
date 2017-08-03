<?php

include(__DIR__."/Base/CycleBase.php");

class Cycle extends CycleBase {
  public function SetAction($act) {
    $this->action = $act;
    return $this;
  }

  public function Add($log) {
    if(is_array($log)) {
      $this->robot = implode("; ", $log);
    } else {
      $this->robot = $log;
    }
    return $this;
  }
}

class CycleControl extends CycleControlBase {

}

?>
