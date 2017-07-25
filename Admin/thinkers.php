<?php

	MagratheaModel::IncludeAllModels();


/*
	@$brewid = $_POST["brewery"];
	$beerControl = new BeerControl();

	if( empty($brewid) ){
		$beers = $beerControl->GetAllFull();
	} else {
		$brew = new Brewery($brewid);
		$beers = $brew->GetBeers();
	}

	$beer_list = array();
	foreach ($beers as $b) {
		$b_item = array();
		$b_item["id"] = $b->id;
		$b_item["name"] = $b->name;
		$b_item["brewery"] = $b->Brewery->name;
		$b_item["category"] = $b->BeerCategory->title;
		$b_item["active"] = $b->active;
		array_push($beer_list, $b_item);
	}

	$breweries = BreweryControl::GetAll();
	$brews = array();
	$brews[0] = "- - - Todas - - -";
	foreach ($breweries as $b) {
		$brews[$b->id] = $b->name;
	}
*/

	$thinkers = ThinkersControl::GetAll();

?>

<div class="row-fluid">
	<div class="span12 mag_section">
		<header class="hide_opt">
			<span class="breadc">Thinkers</span>&nbsp;|&nbsp;<span class="breadc"><b>Edit</b></span>
			<span class="arrow toggle" style="display: none;"><a href="#"><i class="icon-chevron-down"></i></a></span>
		</header>
		<content>
			<div class='row-fluid'>
				<br/>
				<button class="btn btn-success" onclick="showEditBox();">
					Kibar
				</button>
				<br/>

				<br/><br/>
				<div class="row-fluid">
					<div class="span12 mag_section">
						<table class="table table-striped">
							<thead>
							<tr>
								<th>ID</th>
								<th>Name</th>
								<th>Kibes</th>
								<th>english?</th>
								<th>Active</th>
								<th>&nbsp;</th>
							</tr>
							</thead>
							<tbody>
							<?php
								foreach ($thinkers as $t) {
									?>
									<tr>
										<td><?=$t["id"]?></td>
										<td><?=$t["name"]?></td>
										<td><?=$t["brewery"]?></td>
										<td><?=$t["english"]?></td>
										<td>
											<i class="<?=(($t['active'] == true) ? "i-active" : "i-inactive")?>">&#25CF;</i>
										</td>
										<td>
										</td>
									</tr>
									<?php
								} 
							?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</content>
	</div>
</div>

<script type="text/javascript">
function showEditBox(id){
	var page = "thinkers_edit.php";
	ColorBox(page, { data: { "kibe_id": id } });
}

</script>
