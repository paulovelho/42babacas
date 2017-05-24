<?php

class Tweet {
  public $createdAt;
  public $id;
  public $text;
  
  function __construct($data) {
    $this->id = $data->id;
    $this->createdAt = $data->created_at;
    $this->text = $data->text;
  }

}

?>