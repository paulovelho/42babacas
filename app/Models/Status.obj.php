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

  function __toString() {
    $string = "";
    $string .= "TWEET {".$this->text."} ~ ".$this->TweetDate()." \n";
    $string .= "author: @".$this->arroba." | ".$this->author_followers." followers \n";
    $string .= "Rate: ".$this->rate." (".$this->retweets." retweets | ".$this->likes." likes)\n";
    return $string;
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

  public function Rate($time_rate=true) {
    $this->rate = RateService::Instance()
      ->Load($this)
      ->IncludeTime($time_rate)
      ->Rate();
    $this->Log(RateService::Instance()->GetLog());
    return $this->rate;
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