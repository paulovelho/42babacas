<?php

include(__DIR__."/Base/FollowFarmBase.php");

class FollowFarm extends FollowFarmBase {
	// your code goes here!
}

class FollowFarmControl extends FollowFarmControlBase {
  public static function UserExists($id) {
    $query = MagratheaQuery::Select()
      ->Obj( new FollowFarm() )
      ->Where( array('user_id' => $id) );
    $follow = self::RunRow($query->SQL());
    return !empty($follow->id);
  }
}

?>