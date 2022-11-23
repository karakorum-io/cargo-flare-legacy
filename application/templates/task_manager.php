<style type="text/css">
    h3.order_heading {
        width: 100%;
    }
    .ui-button{
        background: #1b257d;
        color: white;
    }
    .swal2-popup.swal2-modal.swal2-show {
        z-index: 99999999;
    }
    .ui-timepicker-container{z-index: 1003 !important ;}
</style>
<script src="<?php echo SITE_IN; ?>styles/new/assets/vendors/general/bootstrap/dist/js/bootstrap.min.js" type="text/javascript"> </script>
<!-- <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script> -->

<!--Tasks For Entity UI Starts-->
<script type="text/javascript">
    // mark task as complete
    function mark_complete() {
        var task_id = [];
        $.each($("input[name='task_id']:checked"), function(){
            task_id.push($(this).val());
        });

        if(task_id.length > 0){
            $.ajax({
                type: "POST",
                url: "<?php echo SITE_IN ?>application/ajax/tasks.php",
                dataType: "json",
                data: {
                    action: "mark_complete",
                    task_ids: task_id
                },
                success: function (response) {
                    swal.fire("Task completed successfully.");
                    window.location.reload();
                },
                error: function (response) {
                    swal.fire("<p>Can't add task now. Try again later, please</p>");
                }
            });
        } else {
            swal.fire("Please select atleast one Task");
        }
    }

    // mark task as incomplete
    function mark_incomplete() {
        var task_id = [];
        $.each($("input[name='task_id']:checked"), function(){
            task_id.push($(this).val());
        });

        if(task_id.length > 0){
            $.ajax({
                type: "POST",
                url: "<?php echo SITE_IN ?>application/ajax/tasks.php",
                dataType: "json",
                data: {
                    action: "mark_incomplete",
                    task_ids: task_id
                },
                success: function (response) {
                    swal.fire("Task completed successfully.");
                    window.location.reload();
                },
                error: function (response) {
                    swal.fire("<p>Can't add task now. Try again later, please</p>");
                }
            });
        } else {
            swal.fire("Please select atleast one Task");
        }
    }

    // mark tasks as undeleted
    function undelete_task(){
        var task_id = [];
        $.each($("input[name='task_id']:checked"), function(){
            task_id.push($(this).val());
        });
        if(task_id.length > 0){
            $.ajax({
                type: "POST",
                url: "<?php echo SITE_IN ?>application/ajax/tasks.php",
                dataType: "json",
                data: {
                    action: "undelete",
                    task_ids: task_id
                },
                success: function (response) {
                    swal.fire("Task completed successfully.");
                    window.location.reload();
                },
                error: function (response) {
                    swal.fire("<p>Can't add task now. Try again later, please</p>");
                }
            });
        } else {
            swal.fire("Please select atleast one Task");
        }
    }

    // add tasks
    function addTask() {
        var errors = "";
        var task_message = $.trim($("#task").val());
		var entity_id = $.trim($("#task_entity_id").val());
        var task_date = $.trim($("#taskdate").val());
        var task_members = $("#taskmembers").val();

        var duedate = $("#duedate").val();
        var status = $("#status").val();
        var priority = $("#priority").val();
        var reminder = $("#reminder").val();

        if($("#reminder").is(":checked")){
            reminder = 1;
        } else if ( $(this).is(":not(:checked)") ) {
            reminder = 0;
        }

        var reminder_date = $("#reminder_date").val();
        var reminder_time = $("#reminder_time").val();

        var taskdata = $("#taskdata").val();

        if (task_message == "")
            errors += "<p>You must provide message for task</p>";
        if (task_date == "")
            errors += "<p>You must provide date for task</p>";
        if (task_members == null)
            errors += "<p>You must select at leas one member</p>";
        if (errors != "") {
            $("#task_errors").html(errors);
            $("#task_errors").slideDown().delay(3000).slideUp();
        } else {
            $.ajax({
                type: "POST",
                url: "<?=SITE_IN?>application/ajax/tasks.php",
                dataType: "json",
                data: {
                    action: "create",
                    date: encodeURIComponent(task_date),
                    message: encodeURIComponent(task_message),
                    receivers: task_members.join(","),
					entity_id: entity_id,
                    duedate: encodeURIComponent(duedate),
                    status: encodeURIComponent(status),
                    priority: encodeURIComponent(priority),
                    reminder: encodeURIComponent(reminder),
                    reminder_date: encodeURIComponent(reminder_date),
                    reminder_time: encodeURIComponent(reminder_time),
                    taskdata: encodeURIComponent(taskdata)
                },
                success: function (response) {
                    if (response.success == true) {
                        $("#task").val("");
                        $("#taskdate").val("");
                        $("#taskmembers").val([]);

                      /*  $("#taskmembers").multiselect('refresh');*/

                        $("#taskmembers").remove();

                        swal.fire("Task successfully created.");
                        window.location.reload();
                    } else {
                        $("#task_errors").html("<p>Can't add task now. Try again later, please</p>");
                        $("#task_errors").slideDown().delay(2000).slideUp();
                    }
                },
                error: function (response) {
                    $("#task_errors").html("<p>Can't add task now. Try again later, please</p>");
                    $("#task_errors").slideDown().delay(2000).slideUp();
                }
            });
        }
    }

    function Processing_show()
    {
        KTApp.blockPage({
            overlayColor: '#000000',
            type: 'v2',
            state: 'success',
            message: '.'
        });

    }

    // KTApp.unblockPage();
    // function to show description of loaded tasks
    function show_description_loaded(id) {

        $("#task-subject").html($("#task-subject-load-" + id).val());
        $("#task-body").html($("#task-description-load-" + id).val());
        $("#task-reminder").html($("#task-reminder_date-load-" + id).val() + " " + $("#task-reminder_time-load-" + id).val());
        $("#task-created-by").html($("#task-created-by-load-" + id).val());

        $("#t_description").modal();
    }

    $(document).ready(function () {
        $("#todays_tasks").click(function () {
            $("#todays_tasks_dialog").html("");
            if (tasks.length > 0) {
                for (i in tasks) {
                    $("#todays_tasks_dialog").append(tasks[i].message + "<hr/>");
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

<div style="background:#fff;border:1px solid #ebedf2;" class="mb-5">

	<div class="card-title hide_show" >
		<h3 class="shipper_detail text-left" style="padding-left:15px;">Task Management</h3>
	</div>

	<div id="task_manage_ment_new" style="padding-left:20px; padding-right:20px; padding-bottom:10px;">

		<div class="row">
			<div class="col-12">

				<? include(TPL_PATH . "orders/task_menu.php"); ?>

			</div>

			<?php

                $member = new Member($this->daffny->DB);
                if ($_GET['task'] == 2) {
                // when completed
            ?>
			<div class="col-12">
				<table class="table table-bordered">
					<tr>
						<td></td>
						<td>Created On</td>
						<td >Subject</td>
						<td>Assigned User</td>
						<td >Completed By</td>
						<td colspan="2" >Actions</td>
					</tr>
					<?php foreach ($this->tasks as $i => $task) {?>
					<tr id="row-<?php echo $task['data']['id'] ?>">
						<td><input type="checkbox" name="task_id" class="task_id" value="<?php echo $task['data']['id']; ?>"></td>
						<td><?php echo date('m-d-Y', strtotime($task['data']['date'])); ?></td>
						<td onclick="show_description_loaded(<?php echo $task['data']['id']; ?>);">
							<a href="#">
								<?php echo $task['data']['message']; ?>
							</a>
						</td>
						<td>
							<?php foreach ($task['assigned_user'] as $ass) {echo $ass . ", ";}?>
						</td>
						<td><?php echo $member->load($task['data']['completed_by'])->contactname; ?></td>
						<td align="center"><?php echo editIcon('javascript:editTask(' . $task['data']['id'] . ')') ?></td>
						<td align="center"><?php echo deleteIcon(getLink("tasks", "delete", "id", $task['data']['id']), "row-" . $task['data']['id']) ?></td>
						<input type='hidden' id='task-subject-load-<?php echo $task['data']['id']; ?>' value='<?php echo $task['data']['message']; ?>'>
						<input type='hidden' id='task-reminder_date-load-<?php echo $task['data']['id']; ?>' value='<?php echo $task['data']['reminder_date']; ?>'>
						<input type='hidden' id='task-reminder_time-load-<?php echo $task['data']['id']; ?>' value='<?php echo $task['data']['reminder_time']; ?>'>
						<input type='hidden' id='task-description-load-<?php echo $task['data']['id']; ?>' value='<?php echo $task['data']['taskdata']; ?>'>
						<input type='hidden' id='task-created-by-load-<?php echo $task['data']['id']; ?>' value='<?php echo $member->load($task['data']['sender_id'])->contactname; ?>'>
					</tr>
					<?php }?>
				</table>
			</div>
			<?php
} elseif ($_GET['task'] == 3) {
    // when deleted
    ?>
			<div class="col-12">
				<table class="table table-bordered">
					<tr>
						<td>&nbsp;</td>
						<td>Created On</td>
						<td>Subject</td>
						<td>Assigned User</td>
						<td>Deleted By</td>
					</tr>
					<?php foreach ($this->tasks as $i => $task) {?>
					<tr id="row-<?php echo $task['data']['id'] ?>">
						<td><input type="checkbox" name="task_id" class="task_id" value="<?php echo $task['data']['id']; ?>"></td>
						<td><?php echo date('m-d-Y', strtotime($task['data']['date'])); ?></td>
						<td onclick="show_description_loaded(<?php echo $task['data']['id']; ?>);">
							<a href="#">
								<?php echo $task['data']['message']; ?>
							</a>
						</td>
						<td>
						<?php
foreach ($task['assigned_user'] as $ass) {
        echo $ass . ", ";
    }
        ?>
						</td>
						<td><?php echo $member->load($task['data']['deleted_by'])->contactname; ?></td>
						<input type='hidden' id='task-subject-load-<?php echo $task['data']['id']; ?>' value='<?php echo $task['data']['message']; ?>'>
						<input type='hidden' id='task-reminder_date-load-<?php echo $task['data']['id']; ?>' value='<?php echo $task['data']['reminder_date']; ?>'>
						<input type='hidden' id='task-reminder_time-load-<?php echo $task['data']['id']; ?>' value='<?php echo $task['data']['reminder_time']; ?>'>
						<input type='hidden' id='task-description-load-<?php echo $task['data']['id']; ?>' value='<?php echo $task['data']['taskdata']; ?>'>
						<input type='hidden' id='task-created-by-load-<?php echo $task['data']['id']; ?>' value='<?php echo $member->load($task['data']['sender_id'])->contactname; ?>'>
					</tr>
							<?php
}
    ?>
				</table>
			</div>
			<?php
} else {
    // when assigned
    ?>
			<div class="col-12">
				<table class="table table-bordered" >
					<tr>
						<td>&nbsp;</td>
						<td>Created On</td>
						<td>Subject</td>
						<td>Assigned User</td>
						<td>Created By</td>
						<td colspan="2">Actions</td>
					</tr>
					<?php foreach ($this->tasks as $i => $task) {?>
					<tr id="row-<?php echo $task['data']['id'] ?>">
						<td>
							<label class="kt-checkbox kt-checkbox--bold kt-checkbox--success">
								<input type="checkbox" name="task_id" class="task_id" value="<?php echo $task['data']['id']; ?>"><span></span>
							</label>
						</td>
						<td><?php echo date('m-d-Y', strtotime($task['data']['date'])); ?></td>
						<td onclick="show_description_loaded(<?php echo $task['data']['id']; ?>);">
							<a href="#">
								<?php echo $task['data']['message']; ?>
							</a>
						</td>
						<td>
						<?php
foreach ($task['assigned_user'] as $ass) {
        echo $ass . ", ";
    }
        ?>
						</td>
						<td><?php echo $member->load($task['data']['sender_id'])->contactname; ?></td>
						<td align="center">
							<?php echo editIcon('javascript:editTask(' . $task['data']['id'] . ')') ?>
						</td>
						<td align="center">
							<?php echo deleteIcon(getLink("tasks", "delete", "id", $task['data']['id']), "row-" . $task['data']['id']) ?>
						</td>
						<input type='hidden' id='task-subject-load-<?php echo $task['data']['id']; ?>' value='<?php echo $task['data']['message']; ?>'>
						<input type='hidden' id='task-reminder_date-load-<?php echo $task['data']['id']; ?>' value='<?php echo $task['data']['reminder_date']; ?>'>
						<input type='hidden' id='task-reminder_time-load-<?php echo $task['data']['id']; ?>' value='<?php echo $task['data']['reminder_time']; ?>'>
						<input type='hidden' id='task-description-load-<?php echo $task['data']['id']; ?>' value='<?php echo $task['data']['taskdata']; ?>'>
						<input type='hidden' id='task-created-by-load-<?php echo $task['data']['id']; ?>' value='<?php echo $member->load($task['data']['sender_id'])->contactname; ?>'>
					</tr>
					<?php }?>
				</table>
			</div>
			<?php }?>
		</div>

	</div>
</div>
<!--Tasks For Entity UI Ends-->



<!-- Edit Task popup -->
<div class="modal fade" id="edit_task_dialog" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="edit_task">Edit Task</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i class="fa fa-times" aria-hidden="true"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="error" style="display: none;"></div>
                <div class="row">
                    <div class="col-md-12 col-12">
                        <div  class="form-group">

                           <label>Re-Assign</label>
                            <select id="edit_task_assigned" class="form-control" multiple="multiple"  style="width: 100%" >
                                <?php foreach ($this->company_members as $company_member): ?>
                                    <?php
if ($company_member->status == "Active") {
    ?>
                                        <option value="<?=$company_member->id?>"><?=$company_member->contactname?></option>
                                        <?php
}
?>
                                <?php endforeach;?>
                            </select>


                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-md-12 col-12">
                        <div  class="form-group">
                            <label> Entity Id</label>
                            <input type="text" class="form-box-textfield" id="edit_entity_id" name="edit_entity_id" value="<?php echo $_GET['id']?>" readonly>
                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-md-12 col-12">
                        <div  class="form-group">
                            <label>  Subject</label>
                            <input type="text" class="form-box-textfield" id="edit_task_message" name="edit_task_message" />

                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 col-12">
                        <div  class="form-group">

                            <label> Message:</label>
                            <textarea class="form-box-textarea"name="edit_task_message_data" id="edit_task_message_data" style="height: 100px;"></textarea>
                        </div>
                    </div>
                </div>


                     <div  class="row">
                        <div class="col-2">
                        <div  class="form-group">
                            <input type="checkbox"  name="edit_reminder"  id="edit_reminder" value="1"/> Set Reminder
                            </div>

                    </div>

                        <div class="col-6">
                            <input type="text" class="form-box-textfield datepicker" id="edit_task_reminder_date"  style="width: 233px" />

                        </div>


                        <div class="col-4">
                             <input type="text" name="edit_reminder_time" class="form-control" id="edit_reminder_time" >
                            <script>

                        /*$(function () {
                        $('#edit_reminder_time').datetimepicker({
                        format: 'LT'
                        });
                        });*/

                            </script>
                        </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-dark btn-sm" data-dismiss="modal">Close</button>
                <button type="button" id="save" value="edit_task" onclick="saveeditTask(this.value)" class="btn btn_dark_blue  btn-sm">Save</button>
            </div>
        </div>
    </div>
</div>

<!--end::Modal-->
<script type="text/javascript">
    date_settings = datepickerSettings;

    var dateToday = new Date();
    date_settings["minDate"] = dateToday;

    $(function () {
        $('#taskdate').datepicker();
        $('#duedate').datepicker(datepickerSettings);
        $('#reminder_date').datepicker(datepickerSettings);
        $('#report_start_date').datepicker(datepickerSettings);
        $('#report_end_date').datepicker(datepickerSettings);
    });

    $(document).ready(function() {
        $('#taskmembers').select2();
    });

    let selectedTaskId = 0;
    function editTask(task_id) {
        $.ajax({
            type: "POST",
            url: '<?=SITE_IN?>application/ajax/tasks.php',
            dataType: 'json',
            data: {
                action: 'getTask',
                id: task_id
            },
            success: function (response) {
               
                if (response.success) {
                    $("#edit_task_date").val(response.data['date']);
                    $("#edit_task_message").val(decodeURIComponent(response.data['message']));
                    $("#edit_task_assigned").val(response.data['assigned']);
                    $("#edit_task_due_date").val(response.data['duedate']);
                    $("#edit_task_status").val(decodeURIComponent(response.data['status']));
                    $("#edit_task_priority").val(decodeURIComponent(response.data['priority']));

                    if (response.data['reminder'] == 1){
                        $("#edit_reminder"). prop("checked", true);
                    }

                    selectedTaskId = task_id;

                    $("#edit_task_reminder_date").val(response.data['reminder_date']);
                    $("#edit_reminder_time").val(response.data['reminder_time']);
                    $("#edit_task_message_data").val(decodeURIComponent(response.data['taskdata']));
                    $("#edit_task_dialog .error").html("");
                    $("#edit_task_dialog .error").hide();
                    $("#edit_task_dialog").modal();

                    $("#edit_task_assigned").select2();
                } else {
                    if (response.error != undefined) {
                        swal.fire(response.error);
                    } else {
                        swal.fire("Can't load task data. Try again later, please.");
                    }
                }
            },
            error: function (response) {

                swal.fire("Can't load task data. Try again later, please.");
            }
        });
    }

    function saveeditTask(){  
        var task_id = selectedTaskId;
        selectedTaskId = 0;
        var message = $.trim($("#edit_task_message").val());
        var assigned = $("#edit_task_assigned").val();
        var entity_id = $("#edit_entity_id").val();
        var duedate = $("#edit_task_due_date").val();
        var status = $("#edit_task_status").val();
        var priority = $("#edit_task_priority").val();
        var reminder = $("#edit_reminder").val();

        if($("#edit_reminder").is(":checked")){
            reminder = 1;
        } else if ( $(this).is(":not(:checked)") ) {
            reminder = 0;
        }

        var reminder_date = $("#edit_task_reminder_date").val();
        var reminder_time = $("#edit_reminder_time").val();
        var taskdata = $("#edit_task_message_data").val();

        var error = "";
        if (message == "")
            error += "<p>Task Message required</p>";
        if (assigned == null)
            error += "<p>You must select at least one member for task</p>";
        if (error != "") {
            $("#edit_task_dialog .error").html(error);
            $("#edit_task_dialog .error").slideDown(500).delay(2000).slideUp(500);
            return;
        }

        $.ajax({
            type: "POST",
            url: '<?= SITE_IN ?>application/ajax/tasks.php',
            dataType: 'json',
            data: {
                action: 'editTask',
                id: task_id,
                entity_id: $("#edit_entity_id").val(),
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
            success: function (response) {
                KTApp.unblockPage();
                if (response.success) {
                        Swal.fire(
                            ' Updated Successfully!',
                            'Update Task Successfully!',
                            'success'
                        )
                        Swal.showLoading()
                        window.location.reload();
                } else {
                    Swal.fire("Can't save task. Try again later, please");
                }
            },
            error: function (response) {
                Swal.fire("Can't save task. Try again later, please");
            }
        });
    }

    $(document).ready(function () {
        $("#reportmembers").select2({
            noneSelectedText: 'Select User',
            selectedText: '# users selected',
            selectedList: 1
        });
        $("#edit_task_assigned").select2({
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
            showOn: 'button',
            minDate: dateToday,
        });
    });
</script>
<!-- Edit Task popup ends-->
<!--Add Task UI-->

<div class="modal fade" id="add_task" tabindex="-1" role="dialog" aria-labelledby="add_task_model" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="add_task_model">Add Tasks</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i class="fa fa-times" aria-hidden="true"></i>
                </button>
            </div>
            <div class="modal-body">
                <?php print formBoxStart("");?>
                    <div id="task_errors"></div>

                    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">
                    <script src="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>

                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                @taskdate@
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label> Users(s):</label>
                                <select id="taskmembers" multiple="multiple" name="taskmembers" class="form-control">
                                <?php foreach ($this->company_members as $company_member): ?>
                                <?php
                                    if ($_SESSION['member']['id'] === $company_member->id) {
                                        echo '<option selected value="' . $company_member->id . '">' . $company_member->contactname . '</option>';
                                    } else {
                                        echo '<option value="' . $company_member->id . '">' . $company_member->contactname . '</option>';
                                    }
                                ?>
                                <?php endforeach;?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                            @task_entity_id@
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                            @task@
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                            @taskdata@
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                @reminder@
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                @reminder_date@
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <input type="text" autocomplete="off" autocorrect="off" spellcheck="false" name="reminder_time" id="reminder_time" class="form-control timepicker">
                            </div>
                        </div>
                    </div>
                <?php echo formBoxEnd(); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-dark btn-sm" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn_dark_green btn-sm" onclick="addTask()" >Add Task</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

    function add_tasks() {
      $("#add_task").modal();
    }

    $(document).ready(function() {
        $('#taskmembers').select2();
        $('.ui-button').addClass('butt');
        $('#edit_task_reminder_date').datepicker({
        startDate: dateToday,
        });
        $("#edit_task_assigned").select2();
        $('#reminder_time').timepicker();
        $('#edit_reminder_time').timepicker();
    });
</script>
<!--Add task UI Ends-->