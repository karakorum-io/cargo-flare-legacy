<?php   define('SITE_IN', "http://localhost/freightdragon/"); ?>

<script type="text/javascript">
	function addTask() {
		var errors = "";
		var task_message = $.trim($("#task").val());
		var task_date = $.trim($("#taskdate").val());
		var task_members = $("#taskmembers").val();
		
		var duedate 		= $("#duedate").val();
		var status 			= $("#status").val();
		var priority 		= $("#priority").val();
		var reminder 		= $("#reminder").val();
		var reminder_date 	= $("#reminder_date").val();
		var reminder_time 	= $("#reminder_time").val();
		var taskdata 		= $("#taskdata").val();
		
		if (task_message == "") errors += "<p>You must provide message for task</p>";
		if (task_date == "") errors += "<p>You must provide date for task</p>";
		if (task_members == null) errors += "<p>You must select at least one member</p>";
		if (errors != "") {
			$("#task_errors").html(errors);
			$("#task_errors").slideDown().delay(3000).slideUp();
		} else {
			$.ajax({
				type: "POST",
				url: "<?= SITE_IN ?>application/ajax/tasks.php",
				dataType: "json",
				data: {
					action: "create",
					date: encodeURIComponent(task_date),
					message: encodeURIComponent(task_message),
					receivers: task_members.join(","),
					
					duedate: encodeURIComponent(duedate),
					status: encodeURIComponent(status),
					priority: encodeURIComponent(priority),
					reminder: encodeURIComponent(reminder),
					reminder_date: encodeURIComponent(reminder_date),
					reminder_time: encodeURIComponent(reminder_time),
					taskdata: encodeURIComponent(taskdata)
				},
				success: function(response) {
					if (response.success == true) {
						$("#task").val("");
						$("#taskdate").val("");
						$("#taskmembers").val([]);
						$("#taskmembers").multiselect('refresh');
						alert("Task successfully created.");
						window.location.reload();
					} else {
						$("#task_errors").html("<p>Can't add task now. Try again later, please</p>");
						$("#task_errors").slideDown().delay(2000).slideUp();
					}
				},
				error: function(response) {
					$("#task_errors").html("<p>Can't add task now. Try again later, please</p>");
					$("#task_errors").slideDown().delay(2000).slideUp();
				}
			});
		}
	}
	$(document).ready(function(){
		$("#todays_tasks").click(function(){
			$("#todays_tasks_dialog").html("");
			if (tasks.length > 0) {
				for (i in tasks) {
					$("#todays_tasks_dialog").append(tasks[i].message+"<hr/>");
				}
			} else {
				$("#todays_tasks_dialog").html("You have no tasks for today.");
			}
			$("#todays_tasks_dialog").dialog({
				title: "Today's tasks",
				width: 400,
				modal: true,
				resizable: false,
				draggable: true
			}).dialog('open');
		});
	});
</script>
<style>
ul.ul-tags {
    list-style: none outside none;
	width:50px;
}
ul.ul-tags li {
    background: -moz-linear-gradient(center top , #FFFFFF, #CCCCCC) repeat scroll 0 0 rgba(0, 0, 0, 0);
    border: 1px solid #AAAAAA;
    border-radius: 10px;
	-moz-border-radius: 10px;
    -webkit-border-radius: 10px;
    -khtml-border-radius: 10px;
    display: block;
    float: left;
    font-size: 20px;
    margin: 2px;
    min-width: 25px;
    padding: 10px;
}

.text_down{ 
     font-size:11px; 
	 
}
.text_black{ 
     font-size:11px;
	 color:#000;
	 
}
</style>
</br>
<div id="todays_tasks_dialog" style="display: none; width:800px;"></div>
<h1 style="font-size:20px; color:#008EC2">Welcome, @companyname@</h1>

<?=formBoxStart("Activity Summary")?>
<table width="100%" cellpadding="1" cellspacing="1" border="0">
<tbody>
   <tr>
     <td align="center" width="10%">
       <table width="100%" cellpadding="1"  cellspacing="1">
       <tr>
         <td align="center" > 
          <a href="<?=getLink('leads')?>"><img style="width:52px;height:52px;" src="<?= SITE_IN ?>images/home/leads.png"  /></a>          </td>
         </tr>
         <tr>
         <td align="center"> 
          <a href="<?=getLink('leads')?>"><span class="text_down">New Leads(</span><span class="text_black">@new_leads@</span><span class="text_down">)</span></a>
         </td>
         </tr>
       </table>   
   </td>
   <!---<td align="center" width="11%">
       <table width="100%" cellpadding="1"  cellspacing="1">
         <tr>
         <td align="center" >
            <a href="<?=getLink('quotes', 'followup')?>"><img src="<?= SITE_IN ?>images/home/follow_up.png"  /></a>        
            </td>
         </tr>
         <tr>
         <td align="center"> 
          <a href="<?=getLink('quotes', 'followup')?>"><span class="text_down">Quotes Follow up(</span><span class="text_black">@quotes_follow@</span><span class="text_down">)</span></a>
         </td>
         </tr>
       </table>   
   </td>
   <td align="center" width="10%" >
       <table width="100%" cellpadding="1"  cellspacing="1">
         <tr>
         <td align="center" >  
            <a href="<?=getLink('quotes', 'onhold')?>"><img src="<?= SITE_IN ?>images/home/on_hold.png"  /></a>           
            </td>
         </tr>
         <tr>
         <td align="center"> 
          <a href="<?=getLink('quotes', 'onhold')?>"><span class="text_down">Quotes on Hold(</span><span class="text_black">@quotes_hold@</span><span class="text_down">)</span></a>
          
         </td>
         </tr>
       </table>   
   </td>---->
     <td align="center" >
       <table width="100%" cellpadding="1"  cellspacing="1">
         <tr>
         <td align="center">  
           <a href="<?=getLink('orders')?>"><img style="width:52px;height:52px;" src="<?= SITE_IN ?>images/home/order_icon_5.png"  /></a>           
           </td>
         </tr>
         <tr>
         <td align="center"> 
          <a href="<?=getLink('orders')?>"><span class="text_down">Orders(</span><span class="text_black">@orders_qty@</span><span class="text_down">)</span></a>
          
         </td>
         </tr>
       </table>   
   </td>
   <td align="center" >
       <table width="100%" cellpadding="1"  cellspacing="1">
         <tr>
         <td align="center">    
           <a href="<?=getLink('orders', 'posted')?>"><img style="width:58px;height:52px;" src="<?= SITE_IN ?>images/home/cd.png"  /></a>          
           </td>
         </tr>
         <tr>
         <td align="center"> 
          <a href="<?=getLink('orders', 'posted')?>"><span class="text_down">Posted Loads(</span><span class="text_black">@orders_posted@</span><span class="text_down">)</span></a>
         </td>
         </tr>
       </table>   
   </td>
   <td align="center">
       <table width="100%" cellpadding="1"  cellspacing="1">
         <tr>
           <td align="center">  
            <a href="<?=getLink('orders', 'notsigned')?>"><img style="width:52px;height:52px;" src="<?= SITE_IN ?>images/home/not-signed.png"  /></a>         
           </td>
         </tr>
         <tr>
           <td align="center"> 
            <a href="<?=getLink('orders', 'notsigned')?>"><span class="text_down">Not Signed(</span><span class="text_black">@orders_notsigned@</span><span class="text_down">)</span></a>
           </td>
         </tr>
       </table>   
   </td>
    <td align="center">
       <table width="100%" cellpadding="1"  cellspacing="1">
         <tr>
         <td align="center"> 
             <a href="<?=getLink('orders', 'dispatched')?>"><img style="width:139px;height:52px;" src="<?= SITE_IN ?>images/home/carrier_dispatched_icon_5.png"  /></a>           </td>
         </tr>
         <tr>
         <td align="center"> 
          <a href="<?=getLink('orders', 'dispatched')?>"><span class="text_down">Dispatched(</span><span class="text_black">@orders_dispatched@</span><span class="text_down">)</span></a>
         
         </td>
         </tr>
       </table>   
   </td>
   <td align="center">
       <table width="100%" cellpadding="1"  cellspacing="1">
         <tr>
          <td align="center"> 
             <center><a href="<?=getLink('orders', 'pickedup')?>"><img style="width:139px;height:52px;" src="<?= SITE_IN ?>images/home/carrier_icon_5.png"  /></a></center>        
          </td>
         </tr>
         <tr>
         <td align="center"> 
          <a href="<?=getLink('orders', 'pickedup')?>"><span class="text_down">Picked Up(</span><span class="text_black">@orders_picked@</span><span class="text_down">)</span></a>
         </td>
         </tr>
       </table>   
   </td>
   
   <td align="center">
       <table width="100%" cellpadding="1"  cellspacing="1">
         <tr>
         <td align="center">
             <a href="<?=getLink('orders', 'issues')?>"><img style="width:52px;height:52px;" src="<?= SITE_IN ?>images/home/money_icon_5.png"  /></a>          </td>
         </tr>
         <tr>
         <td align="center"> 
          <a href="<?=getLink('orders', 'issues')?>"><span class="text_down">Pending Payments(</span><span class="text_black">@orders_issue@</span><span class="text_down">)</span></a>
         </td>
         </tr>
       </table>   
   </td>
   <td align="center">
       <table width="100%" cellpadding="1"  cellspacing="1">
         <tr>
         <td align="center">   
            <a href="<?=getLink('orders', 'delivered')?>"><img style="width:50px;height:50px;" src="<?= SITE_IN ?>images/home/happy.png"  /></a>          </td>
         </tr>
         <tr>
         <td align="center"> 
           <a href="<?=getLink('orders', 'delivered')?>"><span class="text_down">Delivered(</span><span class="text_black">@orders_delivered@</span><span class="text_down">)</span></a> 
         </td>
         </tr>
       </table>   
   </td>

   
  </tr>
  </tbody>
   
</table>
<?=formBoxEnd()?>
<br /><?php /*<!---
<?=formBoxStart("Add new Task")?>
<div id="task_errors"></div>
<!--table width="100%" cellpadding="0" cellspacing="0" border="0">
    <tr>
		<td>@taskdate@</td>
		<td>@task@</td>
		<td>@taskmembers@</td>
		<td><br /><?=functionButton('Add', 'addTask()')?></td>
    </tr>
</table--><!---
<table width="100%" cellpadding="0" cellspacing="0" border="0">
  <tr>
     <td width="30%">
       <img src="<?= SITE_IN ?>images/task.png"  /> 
     </td>
     <td>
        <table width="100%" cellpadding="0" cellspacing="0" border="0">
            <tr>
               <td >@task@</td>
            </tr>
            <tr><td colspan="4">&nbsp;</td></tr>
            <tr>
                <td>@taskdate@</td>
                <td>@status@</td>
            </tr>
            <tr><td colspan="4">&nbsp;</td></tr>
            <tr>
                <td>@duedate@</td>
                <td>@priority@</td>
            </tr>
            <tr><td colspan="4">&nbsp;</td></tr>
            <tr>    
                <td colspan="4">
                   <table width="100%" cellpadding="1" cellspacing="1">
                    <tr>
                      <td>@reminder@</td>
                      <td>@reminder_date@</td>
                      <td>@reminder_time@</td>
                      <td>@taskmembers@</td>
                     </tr>
                   </table>   
                </td>
            </tr>
            <tr><td colspan="4">&nbsp;</td></tr>
            <tr><td valign='top'>@taskdata@</td></tr>  
            <tr><td colspan="4">&nbsp;</td></tr>
            <tr>  
                <td><br /><?=functionButton('Add', 'addTask()')?></td>
            </tr>
        </table>
      </td>
     </tr>
  </table>      
<?=formBoxEnd()?>--->*/ ?>

<script type="text/javascript">
//<![CDATA[
$(function(){
    $('#taskdate').datepicker(datepickerSettings);
	$('#duedate').datepicker(datepickerSettings);
	$('#reminder_date').datepicker(datepickerSettings);
});

$(document).ready(function(){
	$("#taskmembers").multiselect({
		 noneSelectedText: 'Select User',
		 selectedText: '# users selected',
		 selectedList: 1
	});
});


	function editTask(task_id) {
		$("body").nimbleLoader("show");
		$.ajax({
			type: "POST",
			url: '<?= SITE_IN ?>application/ajax/tasks.php',
			dataType: 'json',
			data: {
				action: 'getTask',
				id: task_id
			},
			success: function(response) {
				$("body").nimbleLoader('hide');
				if (response.success) {
					$("#edit_task_date").val(response.data['date']);
					$("#edit_task_message").val(decodeURIComponent(response.data['message']));
					$("#edit_task_assigned").val(response.data['assigned']);
					$("#edit_task_assigned").multiselect('refresh');
					
					$("#edit_task_due_date").val(response.data['duedate']);
					$("#edit_task_status").val(decodeURIComponent(response.data['status']));
					$("#edit_task_priority").val(decodeURIComponent(response.data['priority']));
					
					if(response.data['reminder']==1)
					   $("#edit_reminder").attr('checked','checked');
					//$("#edit_reminder").val(response.data['reminder']);
					$("#edit_task_reminder_date").val(response.data['reminder_date']);
					$("#edit_reminder_time").val(response.data['reminder_time']);
					$("#edit_task_message_data").val(decodeURIComponent(response.data['taskdata']));
					
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
								var duedate = $("#edit_task_due_date").val();
								var status = $("#edit_task_status").val();
								var priority = $("#edit_task_priority").val();
								var reminder = $("#edit_reminder").val();
								var reminder_date = $("#edit_task_reminder_date").val();
								var reminder_time = $("#edit_reminder_time").val();
								var taskdata = $("#edit_task_message_data").val();							

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
										assigned: assigned.join(','),
										duedate: encodeURIComponent(duedate),
										status: encodeURIComponent(status),
										priority: encodeURIComponent(priority),
										reminder: encodeURIComponent(reminder),
										reminder_date: encodeURIComponent(reminder_date),
										reminder_time: encodeURIComponent(reminder_time),
										taskdata: encodeURIComponent(taskdata)
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
		$("#edit_task_assigned").multiselect({
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
		
		$("#edit_task_due_date").datepicker({
			changeMonth: true,
		    dateFormat: 'mm/dd/yy',
		    changeYear: true,
		    duration: '',
		    buttonImage: BASE_PATH + 'images/icons/calendar.gif',
		    buttonImageOnly: true,
		    showOn: 'button'
		});
		
		$("#edit_task_reminder_date").datepicker({
			changeMonth: true,
		    dateFormat: 'mm/dd/yy',
		    changeYear: true,
		    duration: '',
		    buttonImage: BASE_PATH + 'images/icons/calendar.gif',
		    buttonImageOnly: true,
		    showOn: 'button'
		});
	});

//]]>
</script>
<?php /*
<!---
<br />
<? include(TPL_PATH . "home_menu.php"); ?>

<div style="display: none;" id="edit_task_dialog">
	<div class="error" style="display: none;"></div>
	<table cellpadding="0" cellspacing="0" border="0" width="100%" class="form-table">
    
        <tr>
			<td>Subject:</td>
			<td><input type="text" class="form-box-textfield" id="edit_task_message" name="edit_task_message" style="width: 250px;"/></td>
		</tr>
		<tr>
			<td>Task Date:</td>
			<td><input type="text" class="form-box-textfield" id="edit_task_date" style="width: 100px;"/></td>
		</tr>
        <tr>
			<td>Due Date:</td>
			<td><input type="text" class="form-box-textfield" id="edit_task_due_date" style="width: 100px;"/></td>
		</tr>
        <tr>
			<td>Status:</td>
			<td><input type="text" class="form-box-textfield" id="edit_task_status" style="width: 100px;"/></td>
		</tr>
        <tr>
			<td>Priority:</td>
			<td><input type="text" class="form-box-textfield" id="edit_task_priority" style="width: 100px;"/></td>
		</tr>
        
        <tr>    
                <td colspan="2">
                   <table width="100%" cellpadding="1" cellspacing="1">
                    <tr>
                      <td><input type="checkbox" name="edit_reminder"  id="edit_reminder" value="1"/>&nbsp;&nbsp;Reminder</td>
                      <td><input type="text" class="form-box-textfield" id="edit_task_reminder_date" style="width: 100px;"/></td>
                      <td><select id="edit_reminder_time" class="form-box-combobox" name="edit_reminder_time" style="width:100px;">
                             <option selected="selected" value="">Time</option>
                            <?php foreach($this->timeArr as $key=>$value) : ?>
					             <option value="<?= $key ?>"><?= $value ?></option>
				             <?php endforeach; ?>
                          </select></td>
                    </tr>
                   </table>   
                </td>
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
				<textarea class="form-box-textarea"name="edit_task_message_data" id="edit_task_message_data" style="width: 340px;height: 100px;"></textarea>
			</td>
		</tr>
        
       
                   
            
            
	</table>
</div>
<table width="100%" cellpadding="0" cellspacing="0" border="0" class="grid">
    <tr class="grid-head">
        <td class="grid-head-left"><?= $this->order->getTitle("date", "Date") ?></td>
        <td>Task</td>
        <td>Users</td>
        <td style="white-space: nowrap;"><?= $this->order->getTitle("sender_id", "Created By") ?></td>
        <td><?= $this->order->getTitle("completed", "Status") ?></td>
        <td class="grid-head-right" colspan="2">Actions</td>
    </tr>
    <?php if (count($this->data) == 0) : ?>
    <tr class="grid-body first-row">
    	<td class="grid-body-left grid-body-right" align="center" colspan="7"><i>You have no tasks.</i></td>
	</tr>
    <?php endif; ?>
    <? foreach ($this->data as $i => $task) { ?>
    <tr class="grid-body<?= ($i == 0 ? " first-row" : "") ?>" id="row-<?= $task->id ?>">
        <td class="grid-body-left"><?= $task->getDate() ?></td>
        <td><?= $task->message ?></td>
        <td>
        	<?php $members = array(); foreach ($task->getMembers() as $member) { $members[] = $member->contactname; } ?>
        	<?= implode(', ', $members) ?>
		</td>
		<td><?= $task->getSender()->contactname ?></td>
		<td><?= Task::$status_name[$task->completed] ?></td>
        <td style="width: 16px;">
        	<?php if ($task->sender_id == $_SESSION['member_id']) : ?>
			<?=editIcon('javascript:editTask('.$task->id.')')?>
			<?php else : ?>
			&nbsp;
			<?php endif; ?>
		</td>
        <td style="width: 16px;" class="grid-body-right"><?=deleteIcon(getLink("tasks", "delete", "id", $task->id), "row-".$task->id)?></td>
    </tr>
    <? } ?>
</table>
@pager@--->*/ ?>