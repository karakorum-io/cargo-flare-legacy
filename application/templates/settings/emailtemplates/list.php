<? include(TPL_PATH."settings/menu.php"); ?>

<div class="row m_btm_10">
	<div class="col-12">
		<div class="kt-section__info m_btm_10">
			<strong>Below is a list of e-Mail Templates. Click on a pencil edit a template. To view a preview your email, click the envelope button.</strong>
			<div class="pull-right m_btm_10">
				<img style="vertical-align:middle;" src="<?=SITE_IN?>images/icons/add.gif" alt="Add" width="16" height="16" /> <a href="<?=getLink("emailtemplates", "edit")?>">Add New Template</a>
			</div>
		</div>
	</div>
</div>


<div class="kt-portlet">
	
	<div class="kt-portlet__body">
		<div class="row">
			<div class="col-12 ml-2 mr-2">
				<table id="email_templates" class="table table-bordered">
					<thead>
						<tr>
							<th><?=$this->order->getTitle("name", "Name")?></th>
							<th><?=$this->order->getTitle("description", "Description")?></th>
							<th><?=$this->order->getTitle("send_type", "Send Email Using")?></th>
							<th>Actions</th>
						</tr>
					</thead>
					<tbody>
						<? if (count($this->data)>0){?>
							<? foreach ($this->data as $i => $data) { ?>
							<tr id="row-<?=$data['id']?>">
								<td><?=htmlspecialchars($data['name']);?></td>
								<td><?=htmlspecialchars($data['description'])?></td>
								<td><?=$data['send_type']?></td>
								<td >

									<div class="row">
										<div class="col-4">
											<?=editIcon(getLink("emailtemplates", "edit", "id", $data['id']))?>
										</div>

										<div class="col-4">
											<?=previewIcon(getLink("emailtemplates", "show", "id", $data['id']))?>
										</div>

										<div class="col-4">
											<? if ($data['is_system'] != 1){?>
											<?=deleteIcon(getLink("emailtemplates", "delete", "id", $data['id']), "row-".$data['id'])?>
											<?}else{ ?>
										
											<?}?>
										</div>
									</div>

								</td>
								
							</tr>
							<? } ?>
						<?}else{?>
							<tr id="row-">
								<td>&nbsp;</td>
								<td align="center" colspan="4">No records found.</td>
								<td>&nbsp;</td>
							</tr>
						<? } ?>
					</tbody>
				</table>
			</div>
		</div>

		<div class="col-12">
			@pager@
		</div>
		
	</div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
    $('#email_templates').DataTable({
    "lengthChange": false,
    "paging": false,
    "bInfo" : false,
    'drawCallback': function (oSettings) {
     
        $("#email_templates_wrapper").children('.row:first').children('.col-sm-12:first').html('<div class="form-group"><label class="col-form-label pull-left">Show: </label><select class=" records_per_page form-box-combobox form-control " style="width:100px;" onchange="setPagerLimit(this.value)"> <option value="10">10</option> <option value="25">25</option> <option value="50" selected="selected">50</option> <option value="100">100</option> <option value="200">200</option> <option value="500">500</option> </select></div>');
        $("#email_templates_wrapper").children('.row:nth-child(3)').children('.col-sm-12:first').html($('.pager').clone());
        $('.pages_div').remove();
        $("#email_templates_wrapper").find('form-group row').css("margin-left", "1px");

    }
    });
} );
</script>