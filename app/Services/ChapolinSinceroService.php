<?php

class ChapolinSinceroService {

  public static function NormalizeTweet($tweet) {
    return self::UncapitalizeIt($tweet);
  }

  public static function UncapitalizeIt($tweet) {
    if ( self::IsAllCaps($tweet) ) {
      return ucfirst(mb_strtolower($tweet, 'UTF-8'));
    }
    return $tweet;
  }
  public static function IsAllCaps($str) {
    return (strtoupper($str) == $str);
  }

  /* DEPRECATED: our tweet entities already have this information... */
  public static function CheckForMentions($tweet) {
    if ( preg_match("/(^|[^a-z0-9_])@([a-z0-9_]+)/i", $tweet) ) {
      return true;
    } else {
      return false;
    }
  }


}

?>
