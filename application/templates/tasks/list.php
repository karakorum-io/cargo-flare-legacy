<link href="<?php echo SITE_IN; ?>styles/new/assets/vendors/custom/datatables/datatables.bundle.css" rel="stylesheet" type="text/css" />
<script src="<?php echo SITE_IN; ?>styles/new/assets/vendors/custom/datatables/datatables.bundle.js" type="text/javascript"></script>

<style type="text/css">
	li#task_history_next {
    display: none;
}li#task_history_previous {
    display: none;
}
.dataTables_info{
	display: none;
}
</style>


<script type="text/javascript">
	function editTask(task_id) {
		Processing_show();
		$.ajax({
			type: "POST",
			url: '<?= SITE_IN ?>application/ajax/tasks.php',
			dataType: 'json',
			data: {
				action: 'getTask',
				id: task_id
			},
			success: function(response) {
				// 	
				if (response.success) {
					$("#edit_task_date").val(response.data['date']);
					$("#edit_task_message").val(decodeURIComponent(response.data['message']));
					$("#edit_task_assigned").val(response.data['assigned']);
					$("#edit_task_assigned").Select2('refresh');
					$("#edit_task_dialog .error").html("");
					$("#edit_task_dialog .error").hide();
					$("#edit_task_dialog").dialog({
						title: 'Edit Task',
						width: 400,
						resizable: false,
						draggable: true,
						modal: true,
						buttons: [{
							text: "Cancel",
							click: function() {
								$(this).dialog('close');
							}
						},{
							text: "Save",
							click: function() {
								var date = $.trim($("#edit_task_date").val());
								var message = $.trim($("#edit_task_message").val());
								var assigned = $("#edit_task_assigned").val();
								var error = "";
								if (date == "") error += "<p>Task Date required</p>";
								if (message == "") error += "<p>Task Message required</p>";
								if (assigned == null) error += "<p>You must select at least one member for task</p>";
								if (error != "") {
									$("#edit_task_dialog .error").html(error);
									$("#edit_task_dialog .error").slideDown(500).delay(2000).slideUp(500);
									return;
								}
								$(".ui-dialog").nimbleLoader('show');
								$.ajax({
									type: "POST",
									url: '<?= SITE_IN ?>application/ajax/tasks.php',
									dataType: 'json',
									data: {
										action: 'editTask',
										id: task_id,
										date: date,
										message: encodeURIComponent(message),
										assigned: assigned.join(',')
									},
									success: function(response) {
										$(".ui-dialog").nimbleLoader('hide');
										if (response.success) {
											$("#row-"+task_id).html(response.data);
											$("#edit_task_dialog").dialog('close');
										} else {
											alert("Can't save task. Try again later, please");
										}
									},
									error: function(response) {
										$(".ui-dialog").nimbleLoader('hide');
										alert("Can't save task. Try again later, please");
									}
								});
							}
						}]
					}).dialog('open');
				} else {
					if (response.error != undefined) {
						alert(response.error);
					} else {
						alert("Can't load task data. Try again later, please.");
					}
				}
			},
			error: function(response) {
				$("body").nimbleLoader('hide');
				alert("Can't load task data. Try again later, please.");
			}
		});
	}
	$(document).ready(function(){
		$("#edit_task_assigned").Select2({
			noneSelectedText: 'Select User',
			selectedText: '# users selected',
			selectedList: 1
		});
		$("#edit_task_date").datepicker({
			changeMonth: true,
		    dateFormat: 'mm/dd/yy',
		    changeYear: true,
		    duration: '',
		    buttonImage: BASE_PATH + 'images/icons/calendar.gif',
		    buttonImageOnly: true,
		    showOn: 'button'
		});
	});
</script>


<div style="display: none;" id="edit_task_dialog">
	<div class="error" style="display: none;"></div>
	<table cellpadding="0" cellspacing="0" border="0" width="100%" class="form-table">
		<tr>
			<td>Date:</td>
			<td><input type="text" class="form-box-textfield" id="edit_task_date" style="width: 100px;"/></td>
		</tr>
		<tr>
			<td>Assigned to:</td>
			<td>
				<select id="edit_task_assigned" multiple="multiple">
				<?php foreach($this->company_members as $company_member) : ?>
					<option value="<?= $company_member->id ?>"><?= $company_member->contactname ?></option>
				<?php endforeach; ?>
				</select>
			</td>
		</tr>
		<tr>
			<td colspan="2">Message:</td>
		</tr>
		<tr>
			<td colspan="2">
				<textarea class="form-box-textarea" id="edit_task_message" style="width: 340px;height: 100px;"></textarea>
			</td>
		</tr>
	</table>
</div>



<!--   -->
<div class="kt-portlet ">
<div class="kt-portlet__body " >
	<div  class="">
		<div class="col-12 mt-5">
			<div  class="row">
				 <div  class="col-lg-6 col-12" style="padding-left: 0px">
				 	 <h3>Task History</h3>
				</div>
				 <div  class="col-lg-6 col-12 text-right">
				 	 <h3>All tasks are available here.</h3>
				</div>
			</div>
		
		
	    </div>
	



<table id="task_history" class="table table-striped table-bordered mb-5" style="width:100%">
	<thead>
    <tr >
    	<th>ID</th>
    	<th>Order ID</th>
        <th>Subject</th>
        <th>Description</th>
        <th>Assigned User(s)</th>
        <th ></th>
        <th >Completed By</th>
        <th>Deleted By</th>
    </tr>
    </thead>
     <tbody>
    <? foreach ($this->data as $i => $task) { ?>
    
    <tr <?= ($i == 0 ? " " : "") ?>" id="row-<?= $task->id ?>">
        <td><?= $task->id ?></td>
        <td>
        	<a href="<?php echo getLink("quotes", "show", "id", $task->entity_id);?>" target="_blank">
        		<?= $task->entity_id == 0? "" : $task->entity_id ?>    			
    		</a>
    	</td>
        <td><?= $task->message ?></td>
        <td><?= $task->taskdata ?></td>
        <td>
        	<?php $members = array(); foreach ($task->getMembers() as $member) { $members[] = $member->contactname; } ?>
        	<?= implode(', ', $members) ?>
		</td>
		<td><?= $task->getSender()->contactname."<br>".$task->getDate() ?></td>
		<td ><?= $task->get_tast_completer()->contactname == false? "-" : $task->get_tast_completer()->contactname."<br>".$task->get_completed_date() ?></td>
		<td ><?= $task->get_tast_deleter()->contactname == false ? "-" : $task->get_tast_deleter()->contactname."<br>".$task->get_deleted_date() ?></td>
    </tr>
  
    <? } ?>
     </tbody>
</table>


<div class="mt-5">
	@pager@
</div>
</div>
</div>
</div>



<script type="text/javascript">
  $(document).ready(function() {
   $('#task_history').DataTable({
       "lengthChange": false,
       "paging": false,
       "bInfo" : false,
       'drawCallback': function (oSettings) {
           $('#task_history_wrapper').children('.row:first').children('.col-md-6:first').html('<div class="form-group row" style="margin-left:0;"><label class="col-form-label">Show: </label><select class=" records_per_page form-box-combobox form-control " style="width:100px;" onchange="setPagerLimit(this.value)"> <option value="10">10</option> <option value="25">25</option> <option value="50" selected="selected">50</option> <option value="100">100</option> <option value="200">200</option> <option value="500">500</option> </select></div>');
           $('#task_history_wrapper').children('.row:last').children('.col-md-5').html($('.pager').clone()).addClass('text-left');
           $('#task_history_wrapper').children('.row:last').children('.col-md-7').html($('.table_b ').html()).addClass('text-right');
           $('.pages-div-custom').remove();
           
      }
   });

} );
</script>
