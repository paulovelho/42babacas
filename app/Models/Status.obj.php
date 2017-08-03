<?php

class Status {
  public $createdAt;
  public $id;
  public $text;
  public $user;
  public $arroba;
  public $author_followers;
  public $reply_to;
  public $entities;
  public $has_entities;
  public $retweets;
  public $likes;
  public $in_portuguese;
  public $translated;
  public $rate = 50;

  public $log = "";
  
  function __construct($data) {
    if(empty($data)) return $this;
    $this->fillUp($data);
    $this->IsRetweet($data->retweeted_status);
  }

  private function fillUp($data) {
    $this->id = $data->id;
    $this->createdAt = strtotime($data->created_at);
    $this->text = $data->text;
    $this->user = $data->user->id;
    $this->arroba = $data->user->screen_name;
    $this->author_followers = $data->user->followers_count;
    $this->reply_to = $data->in_reply_to_status_id;
    $this->retweets = $data->retweet_count;
    $this->likes = $data->favorite_count;
    $this->in_portuguese = ($data->lang == "pt");
    $this->GetEntities($data->entities);
  }

  /* 
   * this is a fucking complex rate system...
   * it was developed by @paulovelho in 2017-07-25,
   *   during a particular boring workday
   * the logic was taken out from my own ass
   *   but basically:
   *    - if it have an entity (image, url) or it's a reply, it gets zero rating.
   *    - we have to measure the popularity. We measure it by [likes] and [retweets].
   *    - we want a popular tweet to have higher rate.
   *    - we want a EXTREMELY popular tweet to have a lower rate.
   *    - hashtags should lower the rating (they are boring)
   *    - foreign tweets higher the rating (they might seem more original)
   */
  public function Rate() {
    $this->Log("rating [".$this->text."] from @".$this->arroba." {entities: ".$this->has_entities.", reply_to: ".$this->reply_to."} ");
    if ( 
        $this->has_entities || 
        !empty($this->reply_to) 
      ) {
      $this->rate = 0;
      return 0;
    }

    $rate = 50;
    $rate = $this->EntitiesRate($rate);
    $rate = $this->LikabilityRate($rate);
    $rate = $this->AuthorRate($rate);
    $rate = $this->TimeRate($rate);
    $this->rate = $this->NormalizeRate($rate);
    $this->Log("final rate: ".$this->rate);
    return $this->rate;
  }
  public function LikabilityRate($rate) {
    $log = "\n--- authors rate: ";
    // rate with retweet:
    if ( $this->retweets > 1000 ) {
      $rate = $rate - ($this->retweets / 200);
    } else {
      if ( $this->retweets > 500 ) {
        $rate = $rate + ($this->retweets / 70);
      } else {
        if ( $this->retweets < 5 ) {
          $rate = $rate - 30;
        } else {
          $rate = $rate + ($this->retweets / 20);
        }
      }
    }
    $log .= "retweets: ".$this->retweets.", rating ".$rate."; ";

    // rate with like:
    if ( $this->likes > 1000 ) {
      $rate = $rate - ($this->likes / 80);
    } else {
      $rate = $rate + ($this->likes / 40);
    }
    $log .= "likes: ".$this->likes.", rating ".$rate."; ";
    $log .= "[rate: ".$rate.";]";
    $this->Log($log);
    return $rate;
  }
  public function AuthorRate($rate) {
    $log = "\n--- authors rate: ";
    $log .= "followers: ".$this->author_followers."; ";
    // lower rate for popular authors
    $author_rate = ($this->author_followers / 5000);
    if ($author_rate > 90) $author_rate = 90;
    $rate = $rate - $author_rate;
    $log .= "[rate: ".$rate.";]";
    $this->Log($log);
    return $rate;
  }
  public function NormalizeRate($rate) {
    if( $rate > 100 ) $rate = 100;
    if( $rate < 0 ) $rate = 0;
    $log .= "[rate: ".$rate.";]";
    $this->Log($log);
    return $rate;
  }
  public function EntitiesRate($rate) {
    $log = "\n--- entities rate: ";
    // hashtags should lower the rate as well (-20 points per hashtag):
    $rate = $rate - (count($this->entities["hashtags"]) * 20);
    $log .= "hashtags: ".count($this->entities["hashtags"])."; ";
    // higher rate for foreign tweet
    if ( !$this->in_portuguese ) {
      $rate = $rate + 20;
    }
    $log .= "foreign language: ".!$this->in_portuguese."; ";
    $log .= "[rate: ".$rate.";]";
    $this->Log($log);
    return $rate;
  }
  public function TimeRate($rate) {
    $log = "\n--- time rate: ";
    // older the tweet, higher the rate
    $now = time();
    $timeDifference = $now - $this->createdAt;
    $six_months = 15770000;
    $log .= "time difference: ".$timeDifference;
    // don't change for the last 6 months
    if ($timeDifference < $six_months) return $rate;

    // if have hashtags, it doesn't get time rates
    if (count($this->entities["hashtags"]) > 0) return $rate; 

    $time_rate = ($timeDifference - $six_months) / 1000000;
    if($time_rate > 75) $time_rate = 75;
    $rate += $time_rate;
    $log .= "[rate: ".$rate.";]";
    $this->Log($log);
    return $rate;
  }

  public function TweetDate() {
    return date("Y-m-d h:i:s", $this->createdAt);
  }

  private function GetEntities($entities) {
    $this->entities = array();
    $this->has_entities = false;
    if ( count($entities->hashtags) > 0 ) {
      $this->GetHashtags($entities->hashtags);
    }
    if ( count($entities->urls) > 0 ) {
      $this->GetUrls($entities->urls);
      $this->has_entities = true;
    }
    if ( count($entities->user_mentions) > 0 ) {
      $this->GetMentions($entities->user_mentions);
      $this->has_entities = true;
    }
    if ( count($entities->media) > 0 ) {
      $this->GetMedia($entities->media);
      $this->has_entities = true;
    }
  }

  private function GetHashtags($hashtags) {
    $this->entities["hashtags"] = [];
    foreach ($hashtags as $hash) {
      array_push($this->entities["hashtags"], $hash->text);
    }
  }

  private function GetMentions($mentions) {
    $this->entities["mentions"] = [];
    foreach ($mentions as $mt) {
      $mention = array('arroba' => $mt->screen_name, 'id' => $mt->id);
      array_push($this->entities["mentions"], $mention);
    }
  }

  private function GetUrls($urls) {
    $this->entities["urls"] = [];
    foreach ($urls as $url) {
      array_push($this->entities["urls"], $url->expanded_url);
    }
  }

  private function GetMedia($medias) {
    $this->entities["images"] = [];
    foreach ($medias as $media) {
      $image = array('type' => $media->type, 'url' => $media->media_url);
      array_push($this->entities["images"], $image);
    }    
  }

  private function IsRetweet($retweeted_status) {
    if ( count($this->entities["mentions"]) == 1 ) {
      if ( $retweeted_status->user->id == $this->entities["mentions"][0]["id"] ) {
        $this->fillUp($retweeted_status);
        return true;
      }
    } else {
      return false;
    }
  }

  private function Log($log, $newLine = false) {
    $this->log .= "{".$log."}";
    if ($newLine) $this->log .= "\n";
  }



}

?>