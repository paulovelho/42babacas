<?php
	$id = @$_POST["thinker_id"];
	$action = @$_POST["action"];
	$thinker = new Thinkers($id);

	if (empty($id)) {
		$thinker->ideas_stolen = 0;
	}

	if( $action == "save" ) {
		$thinker->twitter_id = $_POST["twitter_id"];
		$thinker->name = $_POST["name"];
		$thinker->active = $_POST["active"];
		$thinker->Save();
		echo "saved!";
	}

?>

<br/>
<form name="thinker_form" id="thinker_form" onSubmit="return false;" class="admin_form">
<div class="row-fluid">
	<div class="span12 mag_section">
		<header><?=(empty($thinker->id)? "New Thinker" : "Edit Thinker")?></header>
		<content>
			<div class='row-fluid'>
				<div class='span3 right'>
					Id:
				</div>
				<div class='span9'>
					<input type="hidden" id="thinker_id" name="thinker_id" value="<?=$thinker->id?>" />
					<input type="hidden" id="action" name="action" value="save" />
					<?=(empty($thinker->id) ? "-" : $thinker->id)?>
				</div>
			</div>
			<div class='row-fluid'>
				<div class='span3 right'>
					Arroba:
				</div>
				<div class='span9'>
					<input type="text" id="name" name="name" value="<?=$thinker->name?>" />
				</div>
			</div>
			<div class='row-fluid'>
				<div class='span3 right'>
					Twitter Id:
				</div>
				<div class='span9'>
					<input type="text" id="twitter_id" name="twitter_id" value="<?=$thinker->twitter_id?>" />
				</div>
			</div>
			<div class='row-fluid'>
				<div class='span3 right'>
					Ativo:
				</div>
				<div class='span9'>
					<input class="ibutton" type="checkbox" name="active" id="active" value="1" <?=($thinker->active == 1) ? "checked" : ""?>/>
				</div>
			</div>

	        <div class="clear"></div>
			<div class='row-fluid'>
				<div class='span9'>&nbsp;</div>
				<div class='span3'>
					<button class="btn btn-success" onClick="saveThinker();"><i class="fa fa-save"></i>&nbsp;Salvar</button>
				</div>
			</div>
		</content>
	</div>
    <div class="clear"></div>
	<div class='row-fluid'>
		<div class='span12'>
			<button class="btn btn-success" onClick="openThinkers();">&nbsp;Voltar</button>
		</div>
	</div>
</div>
</form>

<script type="text/javascript">
function saveThinker(){
	var form = $("#thinker_form").serialize();
	MagratheaPost("thinkers_edit.php", form);
}
function openThinkers(){
	MagratheaPost("thinkers.php");
}
</script>
