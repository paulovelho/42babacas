<?php

class ActionService {
  // singleton:
  protected static $inst = null;
  private $hour;

  // Singleton instance:
  public static function Instance(){
    if(!isset(self::$inst)){
      self::$inst = new ActionService();
    }
    return self::$inst;
  }
  function __construct() {
    $this->hour = date("G");
  }

  public function Action() {
    // noble time: between 15 and 20
    if ($this->hour >= 15 && $this->hour <= 20) {
      return $this->NobleTime();
    }
    // empty time: between 03 and 08
    if ($this->hour >= 3 && $this->hour <= 8) {
      return $this->EmptyTime();
    }
    if ($this->hour < 13) {
      return $this->Morning();
    }
    return $this->Day();
  }

  public function NobleTime() {
    if ($this->ChanceOf(20)) {
      return "farm";
    } else {
      return "kibe";
    }
  }

  public function EmptyTime() {
    if($this->ChanceOf(20)) {
      return "history";
    } else {
      return "unfollow";
    }
  }

  public function Morning() {
    if ($this->ChanceOf(40)) {
      if ($this->ChanceOf(20)) {
        return "unfollow";
      } else {
        return "farm";
      }
    } else {
      if ($this->ChanceOf(20)) {
        return "history";
      } else {
        return "kibe";
      }
    }
  }

  public function Day() {
    if ($this->ChanceOf(20)) {
      if ($this->ChanceOf(20)) {
        return "unfollow";
      } else {
        return "farm";
      }
    } else {
      if ($this->ChanceOf(20)) {
        return "history";
      } else {
        return "kibe";
      }
    }
  }

  public function ChanceOf($chance) {
    return (rand(1,100) <= $chance);
  }

}

?>
