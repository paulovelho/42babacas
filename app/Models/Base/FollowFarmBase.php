<?php

## FILE GENERATED BY MAGRATHEA.
## SHOULD NOT BE CHANGED MANUALLY

class FollowFarmBase extends MagratheaModel implements iMagratheaModel {

	public $id, $user_id, $followed_on, $unfollowed_on;
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
		$this->dbTable = "followfarm";
		$this->dbPk = "id";
		$this->dbValues["id"] = "int";
		$this->dbValues["user_id"] = "string";
		$this->dbValues["followed_on"] = "datetime";
		$this->dbValues["unfollowed_on"] = "datetime";


		$this->dbAlias["created_at"] =  "datetime";
		$this->dbAlias["updated_at"] =  "datetime";
	}

	// >>> relations:

}

class FollowFarmControlBase extends MagratheaModelControl {
	protected static $modelName = "FollowFarm";
	protected static $dbTable = "followfarm";
}
?>