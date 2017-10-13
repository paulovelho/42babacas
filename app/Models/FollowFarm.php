<?php

include(__DIR__."/Base/FollowFarmBase.php");

class FollowFarm extends FollowFarmBase {
	// your code goes here!
}

class FollowFarmControl extends FollowFarmControlBase {
  public static function GetByTwitterId($id) {
    $query = MagratheaQuery::Select()
      ->Obj( new FollowFarm() )
      ->Where( array('user_id' => $id) );
    $follow = self::RunRow($query->SQL());
  }

  public static function UserExists($id) {
    $follow = $this->GetByTwitterId($id);
    return !empty($follow->id);
  }

  public static function GetToUnfollow() {
    $query = MagratheaQuery::Select()
      ->Obj( new FollowFarm() )
      ->Where( array('unfollowed_on' => null) )
      ->Limit(1)
      ->Order("followed_on");
    return self::RunRow($query->SQL());
  }
}

?>