<?php

require(__DIR__."/../lib/SimpleHTMLDom/simple_html_dom.php");
include_once(__DIR__."/../Models/Status.obj.php");
use PHPHtmlParser\Dom;

class BrowserTwitterService {
  
  // singleton:
  protected static $inst = null;

  // Singleton instance:
  public static function Instance(){
    if(!isset(self::$inst)){
      self::$inst = new BrowserTwitterService();
    }
    return self::$inst;
  }
  function __construct() {
  }

  public function SearchFrom($arroba, $search) {
    $query = "from%3A%40".$arroba."%20".$search;
    return $this->Search($query);
  }
  public function Search($query) {
    $url = "http://twitter.com/search?q=".str_replace(" ", "%20", $query);
    $search_result = $this->CrawlSearchPage($url);
    return $search_result;
  }

  public function FilterHistory($tweets, $count) {
    $tweets_result = array();
    for ($i=0; $i < floor($count/2); $i++) {
      array_push($tweets_result, array_pop($tweets));
    }
    usort($tweets, array($this, "OrderClosestTo500"));
    return array_merge($tweets_result, array_slice($tweets, 0, ($count-$i)));
  }

  private function OrderClosestTo500($a, $b) {
    return strcmp(abs($a->retweets - 500), abs($b->retweets - 500) );
  }

  public function CrawlSearchPage($url) {
    LoggerService::Instance()->Log("crawling [".$url."]");
    $html = file_get_contents($url);
    $dom = str_get_html($html);
    $tweets = array();
    foreach($dom->find(".js-stream-item") as $tweet) {
      $original_id = $tweet->getAttribute("data-item-id");
      if(empty($original_id)) continue;

      // is it a reply?
      $reply = (count($tweet->find(".ReplyingToContextBelowAuthor")) > 0);
      if($reply) continue;
      $user_data = $tweet->find(".js-profile-popup-actionable")[0];
      if( empty($user_data) ) continue;
      $tweet_text = $tweet->find(".tweet-text")[0];
      $text = html_entity_decode($tweet_text->plaintext);

      $status = @new Status();
      $status->id = $original_id;
      $status->text = $text;
      $status->user = $user_data->getAttribute("data-user-id");
      $status->arroba = $user_data->getAttribute("data-screen-name");

      $status->createdAt = $tweet->find(".js-short-timestamp")[0]->getAttribute("data-time");
      $status->retweets = $tweet->find(".ProfileTweet-action--retweet .ProfileTweet-actionCount")[0]->getAttribute("data-tweet-stat-count");
      $status->likes = $tweet->find(".ProfileTweet-action--favorite .ProfileTweet-actionCount")[0]->getAttribute("data-tweet-stat-count");

      array_push($tweets, $status);
    }
    return $tweets;
  }


}

?>
