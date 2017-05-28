<?php

## FILE GENERATED BY MAGRATHEA.
## SHOULD NOT BE CHANGED MANUALLY

class ThinkersBase extends MagratheaModel implements iMagratheaModel {

	public $id, $twitter_id, $name, $ideas_stolen, $english;
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
		$this->dbTable = "thinkers";
		$this->dbPk = "id";
		$this->dbValues["id"] = "int";
		$this->dbValues["twitter_id"] = "string";
		$this->dbValues["name"] = "string";
		$this->dbValues["ideas_stolen"] = "int";
		$this->dbValues["english"] = "int";


		$this->dbAlias["created_at"] =  "datetime";
		$this->dbAlias["updated_at"] =  "datetime";
	}

	// >>> relations:

}

class ThinkersControlBase extends MagratheaModelControl {
	protected static $modelName = "Thinkers";
	protected static $dbTable = "thinkers";
}
?>