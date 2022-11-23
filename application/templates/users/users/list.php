<? include(TPL_PATH."users/menu.php"); ?>

<div class="kt-portlet">
	<div class="kt-portlet__body">
		<div class="row">
			<div class="col-12">
				<div style="width: 100%">
			
					<div class="row mt-3 mb-3">
					<? if ($this->daffny->action == "users" && $_GET['users']!="history"){?>
						<img style="vertical-align:middle;" src="<?=SITE_IN?>images/icons/add.gif" alt="Add" width="16" height="16" /> <a href="<?=getLink("users", "edit")?>">&nbsp;&nbsp;Add User</a>
					<?}?>
					<? if ($this->daffny->action == "users_groups"){?>
						<img style="vertical-align:middle;" src="<?=SITE_IN?>images/icons/add.gif" alt="Add" width="16" height="16" /> <a href="<?=getLink("users_groups", "edit")?>">&nbsp;&nbsp;Add Group</a>
					<?}?>
					</div>
					<table id="users" class="table table-bordered">
						<thead>
						<tr >
							<th><?=$this->order->getTitle("contactname", "Name")?></th>
							<th><?=$this->order->getTitle("phone", "Phone")?></th>
							<th><?=$this->order->getTitle("lead_multiple", "Lead Multiple")?></th>
							<th style="width: 150px;"><?=$this->order->getTitle("last_login", "Last Login")?></th>
							<th style="width: 150px;"><?=$this->order->getTitle("reg_date", "Reg. date")?></th>
							<th style="width: 70px;" class="grid-head-left"><?=$this->order->getTitle("status", "Status")?></th>
							<th >Actions</th>
						</tr>
						</thead>
						<? 	$countOnline 	= 0;
							$id				= array();

							foreach ($this->data as $i => $data) {
								if ( ($data['id'] != $_SESSION['member_id'])){
									$id['userid'][] = $data['id'];
									if(($data['online_status']=='1'))
										$countOnline++;
								}
								?>
								<tr class="grid-body<?=($i == 0 ? " " : "")?>" id="row-<?=$data['id']?>">
									<td  class="grid-body-left"><?=$data['contactname']?></td>
									<td align="center"><?=$data['phone']?></td>
									<td align="center"><?=$data['lead_multiple']?></td>
									<td align="center"><?=$data['last_login']?></td>
									<td align="center"><?=$data['reg_date']?></td>
									<? $data['status'] = ($data['status']=='Active'?"<span style=\"color:green\">Active</span>":"<span style=\"color:black\">Inactive</span>"); ?>
									<td align="center"><?=$data['status']?></td>
									<td style="width: 16px;">
										<div class="row">
											<div  class="col-3">
											<?=infoIcon(getLink("users", "show", "id", $data['id']), "Details")?>
											</div>
											<div  class="col-3">
												<?=editIcon(getLink("users", "edit", "id", $data['id']))?>
											</div>
											<div  class="col-3">
													<?php if ($data['id'] != $_SESSION['member_id']) {?>
										<img src="<?php echo SITE_IN ?>/images/icons/delete.png" title="Delete" alt="Delete" class="pointer" onclick="return deleteUser('<?=$data['id']?>', 'row-<?=$data['id']?>');" width="16" height="16" style="margin-top:-3px">
										<?php }?>
											</div>
										</div>
									</td>
								</tr>
						<?  }	?>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function() {
		$('#users').DataTable({
			"lengthChange": false,
			"paging": false,
			"bInfo" : false,
			'drawCallback': function (oSettings) {
				$("#users_wrapper").children('.row:first').children('.col-sm-12:first').html('<div class="form-group row"><label class="col-form-label">Show: </label><select class=" records_per_page form-box-combobox form-control " style="width:100px;" onchange="setPagerLimit(this.value)"> <option value="10">10</option> <option value="25">25</option> <option value="50" selected="selected">50</option> <option value="100">100</option> <option value="200">200</option> <option value="500">500</option> </select></div>');
				$("#users_wrapper").children('.row:nth-child(3)').children('.col-sm-12:first').html($('.pager').clone());
				$('.pages_div').remove();
			}
		});
	});
</script>

@pager@

<!--begin::Modal-->
<div class="modal fade" id="delete-user-dialog" tabindex="-1" role="dialog" aria-labelledby="delete-user-dialog_modal" aria-hidden="true">
	<div class="modal-dialog modal-sm" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="delete-user-dialog_modal">Delete User</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				</button>
			</div>
			<div class="modal-body">
					<div id="delete-user-dialog">
					<p>You must re-assign this user’s Leads, Quotes and Orders first. Please select user(s).</p>
					<table class="delete-user-grid">
						<tr>
							<td align="right"><label for="delete-leads-assign">Leads: </label></td>
							<td><select id="delete-leads-assign" class="form-control"></select></td>
						</tr>
						<tr>
							<td align="right"><label for="delete-quotes-assign">Quotes: </label></td>
							<td><select id="delete-quotes-assign" class="form-control"></select></td>
						</tr>
						<tr>
							<td align="right"><label for="delete-orders-assign">Orders: </label></td>
							<td><select id="delete-orders-assign" class="form-control"></select></td>
						</tr>
					</table>
				</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancal</button>
				<button type="button" id="users" class="btn btn-primary" onclick="deleteUser_save()">Ok</button>
			</div>
		</div>
	</div>
</div>
<!--end::Modal-->
<script>
	var userId, userRow;

	function deleteUser_save()
	{

		$("#delete-user-dialog").find('#users').addClass('kt-spinner kt-spinner--right kt-spinner--md kt-spinner--light');
		$("#delete-user-dialog").find('#users').attr("disabled", true);

	 	$.ajax({
			url: '<?php echo SITE_PATH ?>users/delete',
			type: 'POST',
			dataType: 'json',
			data: {
				id: userId,
				leads_id: $('#delete-leads-assign').val(),
				quotes_id: $('#delete-quotes-assign').val(),
				orders_id: $('#delete-orders-assign').val()
			},
			success: function(res) {
				$("#delete-user-dialog").find('#users').removeClass('btn btn-primary kt-spinner kt-spinner--right kt-spinner--md kt-spinner--light');
				$("#delete-user-dialog").find('#users').attr("disabled", false);
				$('#delete-user-dialog').modal('hide');
				if (!res.success) {
				$("#delete-user-dialog").find('#users').removeClass('btn btn-primary kt-spinner kt-spinner--right kt-spinner--md kt-spinner--light');
				swal.fire('Failed to delete user. Try again later');
				return;
				}
				afterDelete($('#'+userRow));
			},
			error: function() {
				  $("#delete-user-dialog").find('#users').removeClass('btn btn-primary kt-spinner kt-spinner--right kt-spinner--md kt-spinner--light');
				swal.fire('Failed to delete user. Try again later');
			}
		});
	}

	function deleteUser(id, row) {
		userId = id;
		userRow = row;
		$.ajax({
			type: 'POST',
			url: '<?php echo SITE_PATH ?>users/prepareDelete',
			data: {
				id: id
			},
			dataType: 'json',
			success: function(res) {
				if (!res.success) {
					swal.fire('Failed to delete user. Try again later');
					return;
				}
				var options = '';
				for (i in res.members) {
					options += '<option value="'+res.members[i].id+'">'+res.members[i].name+'</option>';
				}
				$('#delete-leads-assign').html(options);
				$('#delete-quotes-assign').html(options);
				$('#delete-orders-assign').html(options);
				$('#delete-user-dialog').modal();
			},
			error: function() {
				swal.fire('Failed to delete user. Try again later');
			}
		});
	}

</script>