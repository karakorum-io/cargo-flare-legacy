
<? include(TPL_PATH."settings/menu.php"); ?>

<div class="row m_btm_10">
	<div class="col-12">
		<div class="kt-section__info m_btm_10">
			Complete the form below and click "Save" Lane when finished. Required fields are marked with a <span style="color:#ff0000;">*</span>.
			<div class="pull-right m_btm_10">
				<img style="vertical-align:middle;" src="<?=SITE_IN?>images/icons/back.png" alt="Back" width="16" height="16" /> <a href="<?=getLink("autoquoting", "lanes", "sid", (int)get_var("sid"))?>">&nbsp;Back to the list</a>
			</div>
		</div>
	</div>
</div>


<?php if ($_GET['id'] == 0) { ?>
<div id="additional_prices_dialog" style="display: none">&#8203;</div>
<div id="additionalLanesDialog" style="display: none">
	<table>
		<tr>
			<td>@additional_origins@</td>
		</tr>
		<tr>
			<td>@additional_destinations@</td>
		</tr>
		<tr>
			<td colspan="2">
				<table>
					<tr>
						<td>@additional_season_switcher@</td>
						<td style="padding-bottom: 5px;"><label for="additional_season_switcher">Apply the following rates for other seasons</label></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr id="additional_season_row" style="display: none">
			<td colspan="2">
				<table style="width:100%">
					<?php for ($i = 1; $i <= $this->seasons_count; $i++) { ?>
					<tr><td>@additional_seasons_<?=$i?>@</td><td style="text-align: right">@additional_seasons_price_<?=$i?>@</td></tr>
					<?php } ?>
				</table>
			</td>
		</tr>
	</table>
</div>
<a href="#" onclick="additionalLanes()">Create another lane(s) based on following rates</a>
<?php } ?>

<form action="<?=getLink("autoquoting", "editlane", "id", get_var("id"), "sid", (int)get_var("sid"))?>" method="post" onsubmit="return laneSubmit();" id="lane_form">
	
	<div class="kt-portlet">
	
		<div class="kt-portlet__head">
			<div class="kt-portlet__head-label">
				 <?=formBoxStart(((int)get_var("id") > 0 ? "Edit Lane" : "Add New Lane"))?>
			</div>
		</div>
		
		<div class="kt-portlet__body">
			
			<div class="row">
			
				<div class="col-3">
					<div class="form-group">
						@name@
					</div>
				</div>
				<div class="col-3">
					<div class="form-group">
						<label class="w-100">&nbsp;</label>
						@price_type@
					</div>
				</div>
				
				<div class="col-3 cpm" style="display:none;">
					<div class="form-group">
						@cpm_price@
					</div>
				</div>
				
				<div class="col-3 base" style="display:none">
					<div class="form-group">
						@base_price@
						<span class="like-link viewsample">?</span>
						<div class="sample-info">
							Base price to charge for this lane
						</div>
					</div>
				</div>
				
				<div class="col-3">
					<div class="form-group select_wdh_100_per">
						@origin_new@
					</div>
				</div>
				
				<div class="col-3">
					<div class="form-group select_wdh_100_per">
						@destination_new@
					</div>
				</div>
				
				<div class="col-3">
					<div class="form-group input_wdh_100_per">
						@origin_radius@ miles <span class="like-link viewsample">?</span>
						<div class="sample-info">
							A radius for matching origin cities (0-50 miles, whole miles only)
						</div>
					</div>
				</div>
				
				<div class="col-3">
					<div class="form-group">
						@inop_surcharge@ <span class="like-link viewsample">?</span>
						<div class="sample-info">
							Extra amount to charge for inoperable vehicles
						</div>
					</div>
				</div>
				
				<div class="col-3">
					<div class="form-group input_wdh_100_per">
						@destination_radius@ miles <span class="like-link viewsample">?</span>
						<div class="sample-info">
							A radius for matching destination cities (0-50 miles, whole miles only)
						</div>
					</div>
				</div>
				
				<div class="col-3">
					<div class="form-group">
						@encl_surcharge@ <span class="like-link viewsample">?</span>
						<div class="sample-info">
							Extra amount or percentage of the base price to charge for enclosed transportation
						</div>
					</div>
				</div>
				
				<div class="col-3">
					<div class="form-group">
						@calculate_price@  % of low surcharge <span class="like-link viewsample">?</span>
						<div class="sample-info">
							If city surcharge is set for both origin and destination, the price is calculated as higher surcharge plus provided percentage of lower surcharge. For example, you could set this value to "0" to apply only the higher surcharge, or to "100" to have them added.
						</div>
					</div>
				</div>
				
				<div class="col-3">
					<div class="form-group select_wdh_100_per">
						@status@
					</div>
				</div>
				
				<div class="col-3">
					<div class="form-group">
						@round_total_to@
					</div>
				</div>
				
			</div>
			
		</div>
		
		<?=formBoxEnd()?>
		
	</div>		
	
	
	<div class="kt-portlet">
	
		<div class="kt-portlet__head">
			<div class="kt-portlet__head-label">
				<?=formBoxStart("Vehicle Type Surcharges")?>
			</div>
		</div>
		
		<div class="kt-portlet__body">
			<div class="row">
			
				<div class="col-4">
					<div class="form-group">
						@v_surcharge[1]@
					</div>
				</div>
				
				<div class="col-4">
					<div class="form-group">
						@v_surcharge[2]@
					</div>
				</div>
				
				<div class="col-4">
					<div class="form-group">
						@v_surcharge[3]@
					</div>
				</div>
				
				<div class="col-4">
					<div class="form-group">
						@v_surcharge[4]@
					</div>
				</div>
				
				<div class="col-4">
					<div class="form-group">
						@v_surcharge[5]@
					</div>
				</div>
				
				<div class="col-4">
					<div class="form-group">
						@v_surcharge[6]@
					</div>
				</div>
				
				<div class="col-4">
					<div class="form-group">
						@v_surcharge[7]@
					</div>
				</div>
				
				<div class="col-4">
					<div class="form-group">
						@v_surcharge[8]@
					</div>
				</div>
				
				<div class="col-4">
					<div class="form-group">
						@v_surcharge[9]@
					</div>
				</div>
				
				<div class="col-4">
					<div class="form-group">
						@v_surcharge[10]@
					</div>
				</div>
				
				<div class="col-4">
					<div class="form-group">
						@v_surcharge[11]@
					</div>
				</div>
				
				<div class="col-4">
					<div class="form-group">
						@v_surcharge[12]@
					</div>
				</div>
				
				<div class="col-4">
					<div class="form-group">
						@v_surcharge[13]@
					</div>
				</div>
				
				<div class="col-4">
					<div class="form-group">
						@v_surcharge[14]@
					</div>
				</div>
				
				<div class="col-4">
					<div class="form-group">
						@v_surcharge[15]@
					</div>
				</div>
				
				<div class="col-4">
					<div class="form-group">
						@v_surcharge[16]@
					</div>
				</div>
				
				<div class="col-4">
					<div class="form-group">
						@v_surcharge[17]@
					</div>
				</div>
				
				<div class="col-4">
					<div class="form-group">
						@v_surcharge[18]@
					</div>
				</div>
				
				<div class="col-4">
					<div class="form-group">
						@v_surcharge[19]@
					</div>
				</div>
				
				<div class="col-4">
					<div class="form-group">
						@v_surcharge[20]@
					</div>
				</div>
				
				<div class="col-4">
					<div class="form-group">
						@v_surcharge[21]@
					</div>
				</div>
				
				<div class="col-4">
					<div class="form-group">
						@v_surcharge[-1]@
					</div>
				</div>
				
				<div class="col-4">
					<div class="form-group">
						@v_surcharge[22]@
					</div>
				</div>
				
			</div>
	
		</div>
		
		<?=formBoxEnd()?>
		
	</div>
	
	<div class="kt-portlet">
	
		<div class="kt-portlet__head">
			<div class="kt-portlet__head-label">
				<?=formBoxStart("Orig/Dest City Surcharges")?>
			</div>
		</div>
		
		<div class="kt-portlet__body">
			
			<table width="100%" class="table table-bordered">
				<tr>
					<td valign="top" width="50%">
						<table class="grid table table-bordered" id="origin_table">
							<thead>
								<tr>
									<th colspan="2">Origin</th>
									<th>State</th>
									<th width="100">Surcharge</th>
								</tr>
							</thead>
							<tbody>
							<? $prevstate = "";?>
							<? if (count($this->origin) > 0){?>
								<? foreach($this->origin as $key=>$c){?>
									<? if ($prevstate != $c['state']){?>
									<? $prevstate = $c['state']; ?>
										<tr>
											<td colspan="4" style="background:#e1e1e1;" class="grid-body-left grid-body-right"><strong><?=$c['state']?></strong></td>
										</tr>
									<? } ?>
									<tr>
										<td><input type="checkbox" name="origin[<?=$c['city_id']?>]" id="origin_<?=$c['city_id']?>" value="<?=$c['city_id']?>" <?=($c['is_active'])?' checked="checked"':''?> /></td>
										<td><label for="origin_<?=$c['city_id']?>"><?=htmlspecialchars($c['city'])?></label></td>
										<td><?=$c['state']?></td>
										<td>$ <input type="text" class="form-box-textfield money" name="o_surcharge[<?=$c['city_id']?>]" value="<?=$c['surcharge']?>" /></td>
									</tr>
								<?}?>
							<?}else{?>
								<tr id="o_nocities">
									<td colspan="4" align="center">No cities yet. Please add a state.</td>
								</tr>
							<?}?>
							</tbody>
						</table>
						<table cellpadding="0" cellspacing="5" border="0" valign="middle">
							<tr bgcolor="#fff">
								<td style="vertical-align: middle;">@o_state@</td>
								<td style="vertical-align: middle;"><?=functionButton("Add", "addState('origin', this)")?></td>
							</tr>
						</table>
					</td>
					<td valign="top">
						<table class="grid table table-bordered" id="destination_table">
							<thead>
								<tr>
									<th colspan="2">Destination</th>
									<th>State</th>
									<th>Surcharge</th>
								</tr>
							</thead>
							<tbody>
								<? $prevstate = "";?>
								<? if (count($this->destination) > 0){?>
									<? foreach($this->destination as $key=>$c){?>
										<? if ($prevstate != $c['state']){?>
										<? $prevstate = $c['state']; ?>
											<tr class="grid-body">
												<td colspan="4" style="background:#e1e1e1;" class="grid-body-left grid-body-right"><strong><?=$c['state']?></strong></td>
											</tr>
										<? } ?>
										<tr class="grid-body">
											<td class="grid-body-left"><input type="checkbox" name="destination[<?=$c['city_id']?>]" id="destination_<?=$c['city_id']?>" <?=($c['is_active'])?' checked="checked"':''?> /></td>
											<td><label for="destination_<?=$c['city_id']?>"><?=htmlspecialchars($c['city'])?></label></td>
											<td align="center"><?=$c['state']?></td>
											<td align="right" class="grid-body-right">$ <input type="text" class="form-box-textfield money" name="d_surcharge[<?=$c['city_id']?>]" value="<?=$c['surcharge']?>" /></td>
										</tr>
									<?}?>
								<?}else{?>
									<tr class="grid-body">
										<td colspan="4" align="center">No cities yet. Please add a state.</td>
									</tr>
								<?}?>
							</tbody>
						</table>
						<table cellpadding="0" cellspacing="5" border="0">
							<tr bgcolor="#fff">
								<td style="vertical-align: middle;">@d_state@</td>
								<td style="vertical-align: middle;"><?=functionButton("Add", "addState('destination', this)")?></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</div>
		
		<?=formBoxEnd()?>
		
	</div>
	
    <br/>
	
    <?=submitButtons(getLink("autoquoting","lanes","sid", (int)get_var("sid")), "Save")?>
	<div id="hiddenElements" style="display: none"></div>
</form>
<script type="text/javascript">//<![CDATA[
<?= $this->states_o ?>
<?= $this->states_d ?>
var sid = '<?php echo $_GET['sid'] ?>';
var checkAdditionalLanes = true;

function additionalLanes() {
	$('#additionalLanesDialog').dialog('open');
}

function laneSubmit() {
	if (!checkAdditionalLanes) return true;
	var sid = '<?php echo $_GET['sid'] ?>';
	additionalLanesDialog = $('#additionalLanesDialog');
	var additionalLanesList = [];
	var originNew = $('#origin_new');
	var destinationNew = $('#destination_new');
	var addOrigins = [originNew.find('option:selected')];
	var addDestinations = [destinationNew.find('option:selected')];
	$('#additional_origins').find('option:selected').each(function () {
		addOrigins.push($(this));
	});
	$('#additional_destinations').find('option:selected').each(function () {
		addDestinations.push($(this));
	});
	var i,j;
	var lanesAdded = {};
	for (i = 0; i < addOrigins.length; i++) {
		for (j = 0; j < addDestinations.length; j++) {
			var newLaneEl = addOrigins[i].val()+'-'+addDestinations[j].val();
			if (addOrigins[i].val() != addDestinations[j].val() &&
				/*!(addOrigins[i].val() == originNew.val() && addDestinations[j].val() == destinationNew.val()) &&*/
				lanesAdded[newLaneEl] == undefined
				) {
				lanesAdded[newLaneEl] = true;
				additionalLanesList.push({
					origin: {
						id: addOrigins[i].val(),
						label: addOrigins[i].text()
					},
					destination: {
						id: addDestinations[j].val(),
						label: addDestinations[j].text()
					}
				});
			}
		}
	}
	if (additionalLanesList.length <= 1) return true;
	var priceTypeName = ($('input[name="price_type"]:checked').val() == 'base')?'Base':'CPM';
	var priceValue = priceTypeName == 'Base' ? $('#base_price').val() : $('#cpm_price').val();
	var header = '<tr><th>Lane</th><th>'+priceTypeName+' Price</th></tr>';
	var body = '';
	var additionalSeasonsList = [{
		id: sid,
		label: 'Current Season'
	}];
	$('#additional_season_row').find('input:checked').each(function() {
		additionalSeasonsList.push({
			id: $(this).val(),
			label: $('label[for="'+$(this).attr('id')+'"]').text()
		});
	});
	for (i = 0; i < additionalSeasonsList.length; i++) {
		if (additionalSeasonsList[i].id != sid) {
			priceValue = $('input[name="additional_seasons_price['+additionalSeasonsList[i].id+']"]').val();
		}
		body += '<tr><th colspan="2" class="season-label">'+additionalSeasonsList[i].label+'</th></tr>';
		for (j = 0; j < additionalLanesList.length; j++) {
			if (sid == additionalSeasonsList[i].id && originNew.val() == additionalLanesList[j].origin.id && destinationNew.val() == additionalLanesList[j].destination.id) {
				continue;
			}
			body += addAdditionalLaneLine(additionalLanesList[j], additionalSeasonsList[i].id, priceValue);
		}
	}
	var additionalPricesDialog = $('#additional_prices_dialog');
	additionalPricesDialog.html('<table class="additional-lane-prices-table">'+header+body+'</table>');
	additionalPricesDialog.find('input').maskMoney({thousands:"", decimal:".", allowZero: true});
	additionalPricesDialog.dialog('open');
	return false;
}

function addAdditionalLaneLine(additionalLane, season_id, val) {
	if (val == undefined) {
		val = '';
	}
	return '<tr><td>'+additionalLane.origin.label+' - '+additionalLane.destination.label+'</td><td><input class="form-box-textfield money" type="text" value="'+val+'" name="addPrice['+season_id+']['+additionalLane.origin.id+'_'+additionalLane.destination.id+']"/>';
}

function addState(type, el) {
	var button = $(el).parents('.form-box-buttons-new');
	if (type == "origin") {
		var o_state = $('#o_state').val();
		if (jQuery.inArray(o_state, states_o) == -1){
			$("body").nimbleLoader('show');
   			$.ajax({
				url: BASE_PATH+'application/ajax/getcities.php',
				data: {action: "get", state:o_state, type:type},
				type: 'POST',
				dataType: 'json',
				beforeSend: function() {},
				success: function(retData) {
					if (retData.cities != "" ){
						$('#origin_table').append(retData.cities);
						states_o.push($('#o_state').val());
						button.hide();
					}
				},
				complete: function() {
					$("body").nimbleLoader('hide');
				}
			});
		} else {
			alert("This state is already added.");
		}
	} else {
		var d_state = $('#d_state').val();
		if (jQuery.inArray(d_state, states_d) == -1){
			$("body").nimbleLoader('show');
   			$.ajax({
				url: BASE_PATH+'application/ajax/getcities.php',
				data: { action: "get", state:d_state, type:type},
				type: 'POST',
				dataType: 'json',
				beforeSend: function() {},
				success: function(retData) {
					if (retData.cities != "" ){
						$('#destination_table').append(retData.cities);
						states_o.push($('#d_state').val());
						button.hide();
					}
				},
				complete: function() {
					$("body").nimbleLoader('hide');
				}
			});
		} else {
			alert("This state is already added.");
		}
	}
}

	function applyPriceType() {
		var price_type = $('input[name="price_type"]:checked').val();
		switch (price_type) {
			case 'cpm':
				$('.base').hide();
				$('.cpm').show();
				break;
			case 'base':
				$('.cpm').hide();
				$('.base').show();
				break;
		}
	}

$(function(){
	$("#origin_radius").mask("9?9",{placeholder:' '});
	$("#destination_radius").mask("9?9",{placeholder:' '});
	$("#calculate_price").mask("9?99",{placeholder:' '});
	$('input[name="price_type"]').change(applyPriceType);
	applyPriceType();
	var dialog = $('#additionalLanesDialog');
	dialog.find('select[multiple]').multiselect();
	dialog.dialog({
		dialogClass: "no-close",
		title: 'Additional Lanes',
		autoOpen: false,
		width: '320',
		draggable: true,
		resizable: false,
		closeOnEscape: false,
		modal: true,
		buttons: {
			'OK': function() {
				$(this).dialog('close');
			}
		}
	});
	$('#additional_prices_dialog').dialog({
		dialogClass: "no-close",
		title: 'Lanes Prices',
		autoOpen: false,
		width: '320',
		draggable: true,
		resizable: false,
		closeOnEscape: false,
		modal: true,
		buttons: {
			'OK': function() {
				$(this).dialog('close');
				var hiddenElements = $('#hiddenElements');
				$(this).find('input[type="text"]').each(function() {
					hiddenElements.append('<input type="hidden" name="'+$(this).attr('name')+'" value="'+$(this).val()+'"/>');
				});
				dialog.find('input[type="text"]').each(function() {
					hiddenElements.append('<input type="hidden" name="'+$(this).attr('name')+'" value="'+$(this).val()+'"/>');
				});
				dialog.find('select[multiple]').each(function() {
					var sel = $(this);
					sel.find('option:selected').each(function() {
						hiddenElements.append('<input type="hidden" name="'+sel.attr('name')+'" value="'+$(this).val()+'"/>');
					});
				});
				dialog.find('input:checked').each(function() {
					hiddenElements.append('<input type="hidden" name="'+$(this).attr('name')+'" value="'+$(this).val()+'"/>');
				});
				checkAdditionalLanes = false;
				$('#lane_form').find('input[type="submit"]').click();
			},
			'Cancel': function() {
				$(this).dialog('close');
				$(this).empty();
			}
		}
	});
	$('#additional_season_switcher').click(additionalSeasonSwitch);
	additionalSeasonSwitch();
});

function additionalSeasonSwitch() {
	if ($('#additional_season_switcher').is(':checked')) {
		$('#additional_season_row').show();
	} else {
		$('#additional_season_row').hide();
	}
}
//]]></script>