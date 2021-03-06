<?php

## FILE GENERATED BY MAGRATHEA.
## SHOULD NOT BE CHANGED MANUALLY

class TweetBase extends MagratheaModel implements iMagratheaModel {

	public $id, $tweet_id, $text, $retweets, $likes, $rate, $original_id, $user_id, $user_name, $translate_from, $log;
	public $created_at, $updated_at;
	protected $autoload = null;

	public function __construct(  $id=0  ){ 
		$this->MagratheaStart();
		if( !empty($id) ){
			$pk = $this->dbPk;
			$this->$pk = $id;
			$this->GetById($id);
		}
	}
	public function MagratheaStart(){
		$this->dbTable = "tweets";
		$this->dbPk = "id";
		$this->dbValues["id"] = "int";
		$this->dbValues["tweet_id"] = "string";
		$this->dbValues["text"] = "string";
		$this->dbValues["retweets"] = "int";
		$this->dbValues["likes"] = "int";
		$this->dbValues["rate"] = "int";
		$this->dbValues["original_id"] = "string";
		$this->dbValues["user_id"] = "string";
		$this->dbValues["user_name"] = "string";
		$this->dbValues["translate_from"] = "string";
		$this->dbValues["log"] = "text";


		$this->dbAlias["created_at"] =  "datetime";
		$this->dbAlias["updated_at"] =  "datetime";
	}

	// >>> relations:

}

class TweetControlBase extends MagratheaModelControl {
	protected static $modelName = "Tweet";
	protected static $dbTable = "tweets";
}
?>