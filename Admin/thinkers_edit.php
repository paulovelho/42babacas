<?php
	$id = @$_POST["brewery_id"];
	$action = @$_POST["action"];
	$brewery = new Brewery($id);

	if( $action == "save" ) {
		$brewery->name = $_POST["brewery_name"];
		$brewery->city = $_POST["brewery_city"];
		$brewery->website = $_POST["brewery_website"];
		$brewery->description = $_POST["brewery_description"];
		$brewery->active = $_POST["brewery_active"];
		$brewery->slug = strtolower($_POST["brewery_slug"]);
		$brewery->facebook_page = $_POST["facebook_page"];
		$brewery->image_id = $_POST["image_id"] ? $_POST["image_id"] : 0;
		$brewery->Save();
	}
	if( $action == "delete" ) {
		$brewery->Delete();
		echo "DELETED!";
		$brewery = new Brewery();
	}

?>

<br/>
<form name="brewery_form" id="brewery_form" onSubmit="return false;" class="admin_form">
<div class="row-fluid">
	<div class="span12 mag_section">
		<header><?=(empty($brewery->id)? "New Brewery" : "Edit Brewery")?></header>
		<content>
			<div class='row-fluid'>
				<div class='span3 right'>
					Id:
				</div>
				<div class='span9'>
					<input type="hidden" id="brewery_id" name="brewery_id" value="<?=$brewery->id?>" />
					<input type="hidden" id="action" name="action" value="save" />
					<?=(empty($brewery->id) ? "-" : $brewery->id)?>
				</div>
			</div>
			<div class='row-fluid'>
				<div class='span3 right'>
					Nome:
				</div>
				<div class='span9'>
					<input type="text" id="brewery_name" name="brewery_name" value="<?=$brewery->name?>" />
				</div>
			</div>
			<div class='row-fluid'>
				<div class='span3 right'>
					Cidade:
				</div>
				<div class='span9'>
					<input type="text" id="brewery_city" name="brewery_city" value="<?=$brewery->city?>" />
				</div>
			</div>
			<div class='row-fluid'>
				<div class='span3 right'>
					Website:
				</div>
				<div class='span9'>
					<input type="text" id="brewery_website" name="brewery_website" value="<?=$brewery->website?>" />
				</div>
			</div>
			<div class='row-fluid'>
				<div class='span3 right'>
					Descrição:
				</div>
				<div class='span9'><textarea name="brewery_description" id="brewery_description"><?=$brewery->description?></textarea></div>
			</div>
			<div class='row-fluid'>
				<div class='span3 right'>
					Slug:
				</div>
				<div class='span9'>
					<input type="text" id="brewery_slug" name="brewery_slug" value="<?=$brewery->slug?>" />
				</div>
			</div>
			<div class='row-fluid'>
				<div class='span3 right'>
					Facebook Page:
				</div>
				<div class='span9'>
					<input type="text" id="facebook_page" name="facebook_page" value="<?=$brewery->facebook_page?>" />
				</div>
			</div>
			<div class='row-fluid'>
				<div class='span3 right'>
					Ativo:
				</div>
				<div class='span9'>
					<input type="hidden" name="brewery_active" value="0">
					<input class="ibutton" type="checkbox" name="brewery_active" id="brewery_active" value="1" <?=($brewery->active == 1) ? "checked" : ""?>/>
				</div>
			</div>
			<div class='row-fluid'>
				<div class='span3 right'>
					Profile Image:
					<br/>
					<input type="button" class="btn btn-default" onClick="openGallery('image_id', 'thumb_image');" value="Select"/>
					<input type="hidden" name="image_id" id="image_id" value="<?=$brewery->image_id?>"/>
					<br/>Recomendado: 210 x 140
				</div>
				<div class='span9' id='thumb_image'>
					<?php if (isset($brewery->image_id)) {?>
					<br/><input type="button" class="btn btn-default" onClick="showImageInDiv(<?=$brewery->image_id?>,'thumb_image');" value="Show Image"/>
					<?php } ?>
				</div>
			</div>
	        <div class="clear"></div>
			<div class='row-fluid'>
				<div class='span9'>&nbsp;</div>
				<div class='span3'>
					<button class="btn btn-success" onClick="saveBrewery();"><i class="fa fa-save"></i>&nbsp;Salvar</button>
				</div>
			</div>
			<?php
			if( !empty($brewery->id) ) {
			?>
			<div class='row-fluid'>
				<div class='span9'>&nbsp;</div>
				<div class='span3'>
					<br/>
					<button class="btn btn-danger" onClick="deleteBrewery(<?=$brewery->id?>);"><i class="fa fa-trash-o"></i>&nbsp;Excluir</button>
				</div>
			</div>
			<?php
			}
			?>
		</content>
	</div>
</div>
</form>

<script type="text/javascript">
function saveBrewery(){
	var form = $("#brewery_form").serialize();
	MagratheaPost("cervejarias_edit.php", form);
}

function deleteBrewery(id){
	if(confirm("Deseja excluir esta cervejaria?")){
		MagratheaPost("cervejarias_edit.php", {
			brewery_id: id,
			action: "delete"
		});
	}	
}
</script>
