<?php

class LoggerService {
  // singleton:
  protected static $inst = null;

  // Singleton instance:
  public static function Instance(){
    if(!isset(self::$inst)){
      self::$inst = new LoggerService();
    }
    return self::$inst;
  }
  function __construct() {
  }

  public function Log($data) {
    $date = @date("Y-m-d h:i:s");
    $line = "[".$date."] = ".$data."\n";
    echo $line;
    MagratheaDebugger::Instance()->Add($data);
  }
}

?>
