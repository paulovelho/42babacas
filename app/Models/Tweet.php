<?php

include(__DIR__."/Base/TweetBase.php");

class Tweet extends TweetBase {
  public function Build($status) {
    $this->text = $this->NormalizeTweet($status->text);
    $this->original_id = $status->id;
    $this->rate = $status->rate;
    $this->user_id = $status->user;
    $this->user_name = $status->arroba;
    $this->translate_from = $status->translated;
    return $this;
  }

  public function NormalizeTweet($tweet) {
    return ChapolinSinceroService::NormalizeTweet($tweet);
  }

  public function Log($log) {
    if(is_array($log)) {
      $this->log = implode("; ", $log);
    } else {
      $this->log = $log;
    }
    return $this;
  }

  public function Post($tweet_id) {
    $this->tweet_id = $tweet_id;
    $this->Save();
    ThinkersControl::OneMoreFrom($this->user_id, $this->user_name);
    return true;
  }
}

class TweetsControl extends TweetControlBase {
  public static function GetByTweetId($id) {
    $query = MagratheaQuery::Select()
      ->Obj( new Tweet() )
      ->Where( array('tweet_id' => $id) );
    return self::RunRow($query->SQL());
  }

  public static function GetByOriginalId($id) {
    $query = MagratheaQuery::Select()
      ->Obj( new Tweet() )
      ->Where( array('original_id' => $id) );
    return self::RunRow($query->SQL());
  }

}

?>
