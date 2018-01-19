<?php

class ThemeService {

  private $themes = array(
    "futebol" => array("gol", "flamengo", "corinthian", "arbitro", "campeonato brasileiro", "série A", "seleção"),
    "BBB" => array("bbb", "paredao", "eliminado"),
    "politica" => array("dilma", "lula", "aecio", "haddad", "kassab", "japones da federal")
  );
  private $theme = false;

  // Singleton instance:
  private static $inst = null;
  public static function Instance(){
    if(!isset(self::$inst)){
      self::$inst = new ThemeService();
    }
    return self::$inst;
  }
  private function __construct(){
  }

  public function getTheme($text) {
    $words = ChapolinSinceroService::GetWords($text);
    $this->lookEveryWord($words);
    return $this->theme;
  }

  private function lookEveryWord($words) {
    foreach ($words as $w) {
      if( $this->lookForTheme($w) ) return;
    }
  }

  private function lookForTheme($w) {
    foreach($this->themes as $theme => $keys) {
      foreach ($keys as $key) {
        if ( $key === $w ) {
          $this->theme = $theme;
          return true;
        }
      }
    }
    return false;
  }
}

?>
