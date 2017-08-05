<?php

include(__DIR__."/Base/ThinkersBase.php");

class Thinkers extends ThinkersBase {

}

class ThinkersControl extends ThinkersControlBase {
  public static function OneMoreFrom ($user_id, $user_name) {
    $thinker = self::GetThinkerFromId($user_id);
    if (empty($thinker->id)) {
      $thinker = new Thinkers();
      $thinker->twitter_id = $user_id;
      $thinker->ideas_stolen = 0;
    }
    $thinker->name = $user_name;
    $thinker->ideas_stolen ++;
    $thinker->Save();
  }

  public static function GetThinkerFromId ($user_id) {
    $query = MagratheaQuery::Select()
      ->Obj( new Thinkers() )
      ->Where( array('twitter_id' => $user_id) );
    return self::RunRow($query->SQL());
  }

  public static function GetRandomThinkers ($quantity) {
    $query = MagratheaQuery::Select("name")
      ->Table("thinkers")
      ->Order("RAND ()")
      ->Limit(5);
    $arr = MagratheaDatabase::Instance()
      ->QueryAll($query->SQL());
    $thinkers = [];
    foreach ($arr as $arroba) {
      array_push($thinkers, $arroba["name"]);
    }
    return $thinkers;

  }
}

?>
