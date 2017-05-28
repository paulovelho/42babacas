<?php

## FILE GENERATED BY MAGRATHEA.
## SHOULD NOT BE CHANGED MANUALLY

class CycleBase extends MagratheaModel implements iMagratheaModel {

	public $id, $action, $robot;
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
		$this->dbTable = "cycle";
		$this->dbPk = "id";
		$this->dbValues["id"] = "int";
		$this->dbValues["action"] = "string";
		$this->dbValues["robot"] = "text";


		$this->dbAlias["created_at"] =  "datetime";
		$this->dbAlias["updated_at"] =  "datetime";
	}

	// >>> relations:

}

class CycleControlBase extends MagratheaModelControl {
	protected static $modelName = "Cycle";
	protected static $dbTable = "cycle";
}
?>