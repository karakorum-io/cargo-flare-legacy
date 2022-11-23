<style type="text/css">
    .swal2-popup.swal2-modal.swal2-show {
    z-index: 99999999;
}
td.disabled.day {
    /* color: red !important; */
    background: #9a8f8f21 !important;
}
</style>

<script type="text/javascript">
    // mark task as complete

     //KTApp.unblockPage();

    function mark_complete() {
        var task_id = [];
        $.each($("input[name='task_id']:checked"), function () {
            task_id.push($(this).val());
        });

        if (task_id.length > 0) {
            $.ajax({
                type: "POST",
                url: "<?php echo SITE_IN ?>application/ajax/tasks.php",
                dataType: "json",
                data: {
                    action: "mark_complete",
                    task_ids: task_id
                },
                success: function (response) {
                    
                    Swal.fire(
                    'Successfully!',
                    'Task completed successfully.!',
                    'success'
                    )
                    window.location.reload();
                },
                error: function (response) {
                     Swal.fire("Can't add task now. Try again later, please");
                }
            });
        } else {
           
            Swal.fire('Please select atleast one Task')
        }
    }

    // mark task as incomplete
    function mark_incomplete() {
        var task_id = [];
        $.each($("input[name='task_id']:checked"), function () {
            task_id.push($(this).val());
        });

        if (task_id.length > 0) {
            $.ajax({
                type: "POST",
                url: "<?php echo SITE_IN ?>application/ajax/tasks.php",
                dataType: "json",
                data: {
                    action: "mark_incomplete",
                    task_ids: task_id
                },
                success: function (response) {
                        Swal.fire(
                        'Successfully!',
                        'Task completed successfully.!',
                        'success'
                        )
                    window.location.reload();
                },
                error: function (response) {
                    Swal.fire("<p>Can't add task now. Try again later, please</p>");
                }
            });
        } else {
            Swal.fire("Please select atleast one Task");
        }
    }

    // mark tasks as undeleted
    function undelete_task() {
        var task_id = [];
        $.each($("input[name='task_id']:checked"), function () {
            task_id.push($(this).val());
        });
        if (task_id.length > 0) {
            $.ajax({
                type: "POST",
                url: "<?php echo SITE_IN ?>application/ajax/tasks.php",
                dataType: "json",
                data: {
                    action: "undelete",
                    task_ids: task_id
                },
                success: function (response) {
                     Swal.fire(
                        'Successfully!',
                        'Task completed successfully.!',
                        'success'
                        )
                    window.location.reload();
                },
                error: function (response) {
                    Swal.fire("<p>Can't add task now. Try again later, please</p>");
                }
            });
        } else {
            Swal.fire("Please select atleast one Task");
        }
    }

    // add tasks
    function addTask() {

        var errors = "";
        var task_message = $.trim($("#task").val());
        var entity_id = $.trim($("#entity_id").val());
        var task_date = $.trim($("#taskdate").val());
        var task_members = $("#taskmembers").val();

        var duedate = $("#duedate").val();
        var status = $("#status").val();
        var priority = $("#priority").val();
        var reminder = $("#reminder").val();
        var reminder_date = $("#reminder_date").val();
        var reminder_time = $("#reminder_time").val();

        if($("#reminder").is(":checked")){
            reminder = 1;
        } else if ( $(this).is(":not(:checked)") ) {
            reminder = 0;
        }

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
                url: "<?= SITE_IN ?>application/ajax/tasks.php",
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
                            Swal.fire(
                            'Successfully created!',
                            'Task successfully created.',
                            'success'
                            )
                           Swal.showLoading()
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

<div class="kt-subheader kt-grid__item" id="kt_subheader">
	<div class="kt-subheader__main">
		<h3 class="kt-subheader__title">Welcome, &nbsp;@companyname@</h3>
	</div>
</div>

<?= formBoxStart("&nbsp;Activity Summary") ?>
	<div class="kt-portlet__body kt-portlet__body--fit">
		<div class="kt-widget17">
			<div class="kt-widget17__stats activity_summary_blocks_info">
			
				<div class="kt-widget17__items">
				
					<div class="kt-widget17__item">
						<a href="<?= getLink('leads') ?>">
							<span class="kt-widget17__icon">
								 <img style="height:50px;" src="<?= SITE_IN ?>images/home/leads.png"  />
							</span>
							<span class="kt-widget17__subtitle">
								New Leads
							</span>
							<span class="kt-widget17__desc">
								@new_leads@
							</span>
						</a>
					</div>
					
					<div class="kt-widget17__item">
						<a href="<?= getLink('orders') ?>">						
							<span class="kt-widget17__icon">
								<img style="height:50px;" src="<?= SITE_IN ?>images/home/order_icon_5.png"  />
							</span>
							<span class="kt-widget17__subtitle">
								Orders
							</span>
							<span class="kt-widget17__desc">
								@orders_qty@
							</span>						
						</a>
					</div>
					
					<div class="kt-widget17__item">
						<a href="<?= getLink('orders', 'posted') ?>">
							<span class="kt-widget17__icon">
								<img style="height:50px;" src="<?= SITE_IN ?>images/home/cd.png"  />
							</span>
							<span class="kt-widget17__subtitle">
								Posted to FB
							</span>
							<span class="kt-widget17__desc">
								@orders_posted@
							</span>
						</a>
					</div>
				
					<div class="kt-widget17__item">
						<a href="<?= getLink('orders', 'notsigned') ?>">
							<span class="kt-widget17__icon">
								<img style="height:50px;" src="<?= SITE_IN ?>images/home/not-signed.png"  />
							</span>
							<span class="kt-widget17__subtitle">
								Not Signed
							</span>
							<span class="kt-widget17__desc">
								@orders_notsigned@
							</span>
						</a>
					</div>
			
					<div class="kt-widget17__item">
						<a href="<?= getLink('orders', 'dispatched') ?>">
							<span class="kt-widget17__icon">
								<img style="height:50px;" src="<?= SITE_IN ?>images/home/carrier_dispatched_icon_5.png"  />
							</span>
							<span class="kt-widget17__subtitle">
								Dispatched
							</span>
							<span class="kt-widget17__desc">
								@orders_dispatched@
							</span>
						</a>
					</div>
					
					<div class="kt-widget17__item">
						<a href="<?= getLink('orders', 'pickedup') ?>">
							<span class="kt-widget17__icon">
								<img style="height:50px;" src="<?= SITE_IN ?>images/home/carrier_icon_5.png"  />
							</span>
							<span class="kt-widget17__subtitle">
								Picked Up
							</span>
							<span class="kt-widget17__desc">
								@orders_picked@
							</span>
						</a>
					</div>
					
					<div class="kt-widget17__item">
						<a href="<?= getLink('orders', 'issues') ?>">
							<span class="kt-widget17__icon">
								<img style="height:50px;" src="<?= SITE_IN ?>images/home/money_icon_5.png"  />
							</span>
							<span class="kt-widget17__subtitle">
								Pending Payments
							</span>
							<span class="kt-widget17__desc">
								@orders_issue@
							</span>
						</a>
					</div>
					
					<div class="kt-widget17__item">
						<a href="<?= getLink('orders', 'delivered') ?>">
							<span class="kt-widget17__icon">
								<img style="height:50px;" src="<?= SITE_IN ?>images/home/happy.png"  />
							</span>
							<span class="kt-widget17__subtitle">
								Delivered
							</span>
							<span class="kt-widget17__desc">
								@orders_delivered@
							</span>
						</a>
					</div>
					
				</div>
			</div>
		</div>
	</div>
<?= formBoxEnd() ?>

<?php
/**
 * HTML view file for track and trace functionality
 * 
 * @author Chetu Inc.
 * @version 1.0
 */

/**
 * Controller Variable Management
 */
$tracks = $this->daffny->tpl->tracks;

$oLat = $this->daffny->tpl->oLat;
$oLng = $this->daffny->tpl->oLng;
$dLat = $this->daffny->tpl->dLat;
$dLng = $this->daffny->tpl->dLng;

$route = array();

for ($i = 0; $i < count($tracks); $i++) {
    $route[$i]['lat'] = $tracks[$i]['lat'];
    $route[$i]['lng'] = $tracks[$i]['lng'];
}

$lastPointReached = $route[0];
$lastPointReached = json_encode($lastPointReached);
$lastPointReached = str_replace('"', "", $lastPointReached);

$route = json_encode($route);
$route = str_replace('"', "", $route);
?>

<!-- Loading external JS file-->
<script src="<?= SITE_IN ?>jscripts/track_n_trace.js"></script>
<!-- Loading external CSS file-->
<link rel = "stylesheet" type="text/css" 	href="<?= SITE_IN ?>styles/track_n_trace.css" />

<!-- Adding Order Menus-->
<div style="padding-top:15px;">
    <?php include('order_menu.php'); ?>
</div>

<div id="map-div">

    <center>
        <h3>Loading Map Data...!</h3>
    </center>
    
</div>

<!--Tasks UI Started-->
<!-- <table width="100%" cellpadding="0" cellspacing="0" border="0">
    <tr>
        <td width="100%" valign="top" align="right">
            <div id="add_task" style="display: none;">
                <?php print formBoxStart(""); ?>
                <h4 style="color:#3B67A6">Add new Task</h4> 
                <div id="task_errors"></div>

                <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">
                <script src="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>

                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td>@taskdate@</td>
                    </tr>
                    <tr><td colspan="4">&nbsp;</td></tr>
                    <tr>
                        <td>Users(s):</td>
                        <td>
                            <select id="taskmembers" multiple="multiple" name="taskmembers">
                                <?php foreach ($this->company_members as $company_member) : ?>
                                    <?php
                                    if($company_member->status == "Active"){
                                        if ($_SESSION['member']['id'] === $company_member->id) {
                                            echo '<option selected value="' . $company_member->id . '">' . $company_member->contactname . '</option>';
                                        } else {
                                            echo '<option value="' . $company_member->id . '">' . $company_member->contactname . '</option>';
                                        }
                                    }
                                    ?>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <tr><td colspan="4">&nbsp;</td></tr>
                    <tr>
                        <td>@entity_id@</td>
                    </tr>
                    <tr><td colspan="4">&nbsp;</td></tr>
                    <tr>
                        <td>@task@</td>
                    </tr>
                    <tr><td colspan="4">&nbsp;</td></tr>
                    <tr><td valign='top'>@taskdata@</td></tr>
                    <tr><td colspan="4">&nbsp;</td></tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td>
                            <table width="100%" cellpadding="1" cellspacing="1">
                                <tr>
                                    <td>@reminder@</td>
                                    <td>@reminder_date@</td>
                                  
									<style>
										.ui-timepicker-container{z-index: 1003 !important ;}
									</style>
									<td><input type="text" name="reminder_time" id="reminder_time" class="timepicker"></td>
									<script>
										$('.timepicker').timepicker({
											timeFormat: 'h:mm a',
											interval: 15,
											minTime: '12:00am',
											maxTime: '11:59pm',
											defaultTime: '11',
											startTime: '10:00',
											dynamic: false,
											dropdown: true,
											scrollbar: true
										});
									</script>
								</tr>
							</table>
						</td>
					</tr>
				</table>

				<?php echo formBoxEnd(); ?>
			</div>            
			
		</td>
	</tr>
</table> -->


<script src="<?php echo SITE_IN; ?>styles/new/assets/vendors/general/bootstrap/dist/js/bootstrap.min.js" type="text/javascript"></script> 
<!--begin::Modal-->
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

             <?php print formBoxStart(""); ?>
                <h4 >Add new Task</h4> 
                <div id="task_errors"></div>

                 <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">
                <script src="//cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script> 



            <div   class="row">
                <div class="col-12">
                    <div class="form-group">
                    @taskdate@
                    </div>
                </div>
            </div>
            <script type="text/javascript">
           

            </script>

                  <div   class="row">
                    <div class="col-12">
                        <div class="form-group">
                      <label> Users(s):</label>
                        <select id="taskmembers" multiple="multiple" class="form-control taskmembers" name="taskmembers" style="width: 100%">
                        <?php foreach ($this->company_members as $company_member) : ?>
                        <?php
                            if($company_member->status == "Active"){
                            if ($_SESSION['member']['id'] === $company_member->id) {
                             echo '<option selected value="' . $company_member->id . '">' . $company_member->contactname . '</option>';
                            } else {
                              echo '<option value="' . $company_member->id . '">' . $company_member->contactname . '</option>';
                            }
                            }
                        ?>
                        <?php endforeach; ?>
                        </select>

                      </div>
                    </div>
                  </div>


        <div   class="row">
            <div class="col-12">
                <div class="form-group">
                @entity_id@
                </div>
            </div>
        </div>

        <div   class="row">
            <div class="col-12">
                <div class="form-group">
                  @task@
                </div>
            </div>
        </div>

         <div   class="row">
            <div class="col-12">
                <div class="form-group">
                  @taskdata@
                </div>
            </div>
        </div>

        <div   class="row">
            <div class="col-2">
                <div class="form-group">
                  @reminder@
                                 
                </div>
            </div>

            <div class="col-5">
                <div class="form-group">
                  @reminder_date@
                </div>
            </div>

             <div class="col-5">
                <div class="form-group">
                    <input type="text" autocomplete="off" autocorrect="off" spellcheck="false" name="reminder_time" id="reminder_time" class="form-control timepicker">
				</div>
            </div>
        </div>
            
                    
   <?php echo formBoxEnd(); ?>
           
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-dark btn-sm" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn_light_green btn-sm" onclick="addTask()" >Add Task</button>
        </div>
    </div>
</div>
</div>

<script type="text/javascript">
    $(".taskmembers").select2({

    });
</script>
<script type="text/javascript">
            $('.timepicker').timepicker();
</script>














<script type="text/javascript">
function add_tasks() {
$("#add_task").modal();
}
            </script>
<script type="text/javascript">
    //<![CDATA[

    date_settings = datepickerSettings;

    var dateToday = new Date();
    date_settings["minDate"] = dateToday;
    $(function () {
        $('#taskdate,reminder_date').attr('autocomplete','off');
        $('#taskdate,reminder_date').attr('autocorrect','off');
        $('#taskdate,reminder_date').attr('spellcheck','false');
          $('#taskdate').datepicker({
               startDate: dateToday,
               autoclose : true
          });

        $('#duedate').datepicker(datepickerSettings);
        $('#reminder_date').datepicker({
               startDate: dateToday,
               autoclose : true
        });


        $('#report_start_date').datepicker(datepickerSettings);
        $('#report_end_date').datepicker(datepickerSettings);
    });

   /* $(document).ready(function () {
        $("#taskmembers").multiselect({
            noneSelectedText: 'Select User',
            selectedText: '# users selected',
            selectedList: 1
        });
    });*/


        function Processing_show() 
        {
            KTApp.blockPage({
            overlayColor: '#000000',
            type: 'v2',
            state: 'success',
            message: '.'
            });

        }

    function saveeditTask(){

       
        var task_id = $("#save").val();
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



    function editTask(task_id) {
        $.ajax({    
            type: "POST",
            url: '<?= SITE_IN ?>application/ajax/tasks.php',
            dataType: 'json',
            data: {
                action: 'getTask',
                id: task_id
            },
            success: function (response) {

                if (response.success) {
                    $("#edit_task_date").val(response.data['date']);
                    $("#edit_task_message").val(decodeURIComponent(response.data['message']));
                    //$("#edit_task_assigned").val(response.data['assigned']);
                    $('#edit_task_assigned').val(response.data['assigned']);
                    $("#edit_task_assigned").select2();

                    $("#edit_entity_id").val(response.data['entity_id']);
                    /*$("#edit_task_assigned").multiselect('refresh');*/

                    $("#edit_task_due_date").val(response.data['duedate']);
                    $("#edit_task_status").val(decodeURIComponent(response.data['status']));
                    $("#edit_task_priority").val(decodeURIComponent(response.data['priority']));

                    if (response.data['reminder'] == 1){
                    	$("#edit_reminder"). prop("checked", true);
                    }
                    //$("#edit_reminder").val(response.data['reminder']);
                    $("#edit_task_reminder_date").val(response.data['reminder_date']);
                    $("#edit_reminder_time").val(response.data['reminder_time']);
                    $("#edit_task_message_data").val(decodeURIComponent(response.data['taskdata']));

                    $("#edit_task_dialog .error").html("");
                    $("#edit_task_dialog .error").hide();
                    $('#edit_task_dialog').modal();

                     $('#save').val(task_id,entity_id);

                } else {
                    if (response.error != undefined) {
                        Swal.fire(response.error);
                    } else {
                        Swal.fire("Can't load task data. Try again later, please.");
                    }
                }
            },
            error: function (response) {
                KTApp.unblockPage();
                Swal.fire("Can't load task data. Try again later, please.");
            }
        });
      }


    $(document).ready(function () {
        /*$("#reportmembers").multiselect({
            noneSelectedText: 'Select User',
            selectedText: '# users selected',
            selectedList: 1
        });
        $("#edit_task_assigned").multiselect({
            noneSelectedText: 'Select User',
            selectedText: '# users selected',
            selectedList: 1
        });*/
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
            startDate: dateToday,
            autoclose : true
        });
    });

    //]]>
</script>
<!--Tasks UI Ended-->

<? include(TPL_PATH . "home_menu.php"); ?>
  

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
                                <?php foreach ($this->company_members as $company_member) : ?>
                                    <?php
                                    if($company_member->status == "Active"){
                                        ?>
                                        <option value="<?= $company_member->id ?>"><?= $company_member->contactname ?></option>
                                        <?php
                                    }
                                    ?>
                                <?php endforeach; ?>
                            </select>


                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-md-12 col-12">
                        <div  class="form-group">
                            <label> Entity Id</label>
                            <input type="text" class="form-box-textfield" id="edit_entity_id" name="edit_entity_id" >


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
                            $('#edit_reminder_time').timepicker({
                                timeFormat: 'h:mm a',
                                interval: 15,
                                minTime: '12:00am',
                                maxTime: '11:59pm',
                                defaultTime: '11',
                                startTime: '10:00',
                                dynamic: false,
                                dropdown: true,
                                scrollbar: true
                            });
                            </script>
                        </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-dark btn-sm" data-dismiss="modal">Close</button>
                <button type="button" id="save" value="edit_task" onclick="saveeditTask(this.value)" class="btn btn_dark_green btn-sm">Save</button>
            </div>
        </div>
    </div>
</div>

<!--end::Modal-->



<?php
$entity = new Entity($this->daffny->DB);

if ($taskType == "completed") {
    ?>
    <div class="kt-portlet__body" style="padding-top: 0px; padding-bottom: 0px !important"  >
        <div class="kt-section">
         <div class="kt-section__content">
         <table id="task_home"  class="table table-bordered" >
		 <thead>
			<tr>
				<th>&nbsp;</th>
				<th>ID</th>
				<th><?= $this->order->getTitle("date", "Created On") ?></th>
				<th>Subject</th>
				<th>Assigned Users</th>
				<th ><?= $this->order->getTitle("completed_by", "Completed By") ?></th>
				<th>Actions</th>
			</tr>
		</thead>
		<tbody>
        <?php if (count($this->data) == 0) : ?>
            <tr class="grid-body ">
                <td class="grid-body-left grid-body-right" align="center" colspan="7"><i>You have no tasks.</i></td>
            </tr>
        <?php endif; ?>
        <? foreach ($this->data as $i => $task) {
         
        	if($task->entity_id != 0){
	        	$entity->load($task->entity_id);
        	}
        ?>
            <tr class="grid-body<?= ($i == 0 ? " " : "") ?>" id="row-<?= $task->id ?>">
                <td align="center">
				
					<? /*<label class="kt-checkbox kt-checkbox--bold kt-checkbox--success" style="margin-bottom: 18px; >
					<input type="checkbox" name="task_id" class="task_id" value="<?php echo $task->id; ?>"> 
					<span></span>
					</label> */?>
					
					<label class="kt-checkbox kt-checkbox--bold kt-checkbox--success" style="width:18px;padding:0;margin-bottom:18px;">
						<input type="checkbox" name="task_id" value="<?php echo $task->id; ?>" class="order-checkbox entity-checkbox task_id" />
						<span></span>
					</label>
					
				</td>
                <td align="center">
                    <a target="_blank"  href="<?php echo getLink('orders', 'searchorders', 'type1', 'orderid', 'search_string', $task->entity_id); ?>">
						<?php echo $task->entity_id == 0 ? "" : $entity->prefix."-".$entity->number; ?>
                    </a>
                </td>
                <td>
                    <span><?= date('m-d-Y',strtotime($task->date)); ?></span>
                </td>

                <td onclick="show_description_loaded(<?php echo $task->id; ?>);">
                    <a style="color:#008ec2;cursor: pointer;">
                        <?= $task->message ?>	
                    </a>
                </td>
                <td class="kt-font-bold kt-font-success">
                    <span >
                    <?php
                    $members = array();
                    foreach ($task->getMembers() as $member) {
                        $members[] = $member->contactname;
                    }
                    ?>
                    <?= implode(', ', $members) ?>
                    </span>
                </td>
                <td class="kt-font-bold kt-font-primary"><?= $task->get_tast_completer()->contactname ?></td>
                <td >
                    <div class="row">
                    <div class="col-6">
                    <?= editIcon('javascript:editTask(' . $task->id . ')') ?>
                    </div>
                    <div class="col-6">
                    <?= deleteIcon(getLink("tasks", "delete", "id", $task->id), "row-" . $task->id) ?>
                    </div>
                    </div>
                </td>
                <input type='hidden' id='task-subject-load-<?php echo $task->id; ?>' value='<?php echo $task->message; ?>'>
                <input type='hidden' id='task-reminder-load-<?php echo $task->id; ?>' value='<?php echo $task->reminder; ?>'>
                <input  type='hidden' id='task-reminder_date-load-<?php echo $task->id; ?>' value='<?php echo date('m-d-Y',strtotime($task->reminder_date)); ?>' >
                <input type='hidden' id='task-reminder_time-load-<?php echo $task->id; ?>' value='<?php echo date('h:i a',strtotime($task->reminder_time)); ?>' >
                <input type='hidden' id='task-description-load-<?php echo $task->id; ?>' value='<?php echo $task->taskdata; ?>'>
                <input type='hidden' id='task-created-by-load-<?php echo $task->id; ?>' value='<?php echo $task->getSender()->contactname; ?>'>
            </tr>
        <? } ?>
		</tbody>
    </table>
</div>
</div>
</div>
    <?php
} elseif ($taskType == "deleted") {
    ?>
    <div class="kt-portlet__body" style="padding-top: 0px; padding-bottom: 0px !important" >
        <div class="kt-section">
     <div class="kt-section__content">
    <table class="table table-bordered" id="task_home" >
		<thead>
			<tr>
				<th>&nbsp;</th>
				<th >ID</th>
				<th><?= $this->order->getTitle("date", "Created On") ?></th>
				<th>Subject</th>
				<th>Assigned Users</th>
				<th ><?= $this->order->getTitle("deleted_by", "Deleted By") ?></th>
				<th class="grid-head-right" colspan="2">Actions</th>
			</tr>
		</thead>
		<tbody>
        <?php if (count($this->data) == 0) { ?>
            <tr class="grid-body ">
                <td class="grid-body-left grid-body-right" align="center" colspan="6"><i>You have no tasks.</i></td>
            </tr>
        <?php } else { ?>
            <? foreach ($this->data as $i => $task) { 
            	if($task->entity_id != 0){
		        	$entity->load($task->entity_id);
	        	}
            ?>
                <tr class="grid-body<?= ($i == 0 ? " " : "") ?>" id="row-<?= $task->id ?>">
                    <td align="center">
					
                        <label class="kt-checkbox kt-checkbox--bold kt-checkbox--success" style="margin-bottom:18px;">
							<input type="checkbox" name="task_id" class="order-checkbox entity-checkbox task_id" value="<?php echo $task->id; ?>"> 
							<span></span>
                        </label>
						
                    </td>
                    <td align="center">
	                    <a target="_blank" href="<?php echo getLink('orders', 'searchorders', 'type1', 'orderid', 'search_string', $task->entity_id); ?>">
	                        <?php echo $task->entity_id == 0 ? "" : $entity->prefix."-".$entity->number; ?>
	                    </a>
	                </td>
                    <td>
                        <span>
                        <?= date('m-d-Y',strtotime($task->date)); ?> </span>
                    </td>
                    <td onclick="show_description_loaded(<?php echo $task->id; ?>);">
                        <a style="color:#008ec2;cursor: pointer;">
                            <?= $task->message ?>   
                        </a>
                    </td>
                    <td  class="kt-font-bold kt-font-success">
                          <span>
                        <?php
                        $members = array();
                        foreach ($task->getMembers() as $member) {
                            $members[] = $member->contactname;
                        }
                        ?>
                        <?= implode(', ', $members) ?>
                    </span>
                    </td>
                    <td class="kt-font-bold kt-font-primary"><?= $task->get_tast_deleter()->contactname ?></td>
                    <td style="width: 16px;">
                        <?= editIcon('javascript:editTask(' . $task->id . ')') ?>
                    </td>
                    <td style="width: 16px;" class="grid-body-right">
                        <?= deleteIcon(getLink("tasks", "delete", "id", $task->id), "row-" . $task->id) ?>      
                    </td>
                    <input type='hidden' id='task-subject-load-<?php echo $task->id; ?>' value='<?php echo $task->message; ?>'>
                    <input type='hidden' id='task-reminder-load-<?php echo $task->id; ?>' value='<?php echo $task->reminder; ?>'>
                    <input  type='hidden' 
                        id='task-reminder_date-load-<?php echo $task->id; ?>' 
                        value='<?php echo date('m-d-Y',strtotime($task->reminder_date)); ?>'
                    >
                    <input  type='hidden' 
                        id='task-reminder_time-load-<?php echo $task->id; ?>' 
                        value='<?php echo date('h:i a',strtotime($task->reminder_time)); ?>'
                    >
                    <input type='hidden' id='task-description-load-<?php echo $task->id; ?>' value='<?php echo $task->taskdata; ?>'>
                    <input type='hidden' id='task-created-by-load-<?php echo $task->id; ?>' value='<?php echo $task->getSender()->contactname; ?>'>
                </tr>
            <? } ?>
        <? } ?>
		</tbody>
    </table>
    </div>
    </div>
    </div>
<?php
} elseif ($taskType == "history") {
?>

<div class="kt-portlet__body"  style="padding-top: 0px; padding-bottom: 0px !important">
        <div class="kt-section">
         <div class="kt-section__content">
    <table class="table table-bordered" id="task_home">
		<thead>
			<tr>
				<th>&nbsp;</th>
				<th >ID</th>
				<th>Subject</th>
				<th>Description</th>
				<th>Assigned Users</th>
				<th><?= $this->order->getTitle("sender_id", "Created By") ?></th>
				<th><?= $this->order->getTitle("completed_by", "Completed By") ?></th>
				<th><?= $this->order->getTitle("deleted_by", "Deleted By") ?></th>
			</tr>
		</thead>
		<tbody>
        <?php if (count($this->data) == 0) { ?>
            <tr class="grid-body ">
                <td class="grid-body-left grid-body-right" align="center" colspan="5"><i>You have no tasks.</i></td>
            </tr>
        <?php } else { ?>
            <? foreach ($this->data as $i => $task) {
            	if($task->entity_id != 0){
		        	$entity->load($task->entity_id);
	        	} 
        	?>
                <tr class="grid-body<?= ($i == 0 ? " " : "") ?>" id="row-<?= $task->id ?>">
                    <td align="center">					
						<label class="kt-checkbox kt-checkbox--bold kt-checkbox--success" style="margin-bottom: 18px;">
							<input type="checkbox" name="task_id" class="order-checkbox entity-checkbox task_id" value="<?php echo $task->id; ?>"> 
							<span></span>
						</label>						
                    </td>
                    <td align="center">
	                    <a target="_blank" href="<?php echo getLink('orders', 'searchorders', 'type1', 'orderid', 'search_string', $task->entity_id); ?>">
							<?php echo $task->entity_id == 0 ? "" : $entity->prefix."-".$entity->number; ?>
	                    </a>
	                </td>

                    <td onclick="show_description_loaded(<?php echo $task->id; ?>);">
                        <a href="#"><?= $task->message ?></a>
                    </td>
					
                    <td>
                        <?= $task->taskdata ?>
                    </td>
					
                    <td class="kt-font-bold kt-font-success">
						<span>
							<?php
							$members = array();
							foreach ($task->getMembers() as $member) {
								$members[] = $member->contactname;
							}
							?>
							<?= implode(', ', $members) ?>
						</span>
                    </td>
                    <td class="kt-font-bold kt-font-primary" ><?= $task->getSender()->contactname."<br>".$task->getDate(); ?></td>
                    <td class="kt-font-bold kt-font-primary">
                        <?= $task->get_tast_completer()->contactname."<br>".$task->get_completed_date() == 0 ? "" : $task->get_completed_date() ?>    
                    </td>
                    <td class="kt-font-bold kt-font-success">                        
                        <?= $task->get_tast_deleter()->contactname."<br>".$task->get_deleted_date() == 0 ? "" : $task->get_deleted_date()?>
                    </td>
                    <input type='hidden' id='task-subject-load-<?php echo $task->id; ?>' value='<?php echo $task->message; ?>'>
                    <input type='hidden' id='task-reminder_date-load-<?php echo $task->id; ?>' value='<?php echo $task->reminder_date; ?>'>
                    <input  type='hidden' 
                        id='task-reminder_time-load-<?php echo $task->id; ?>' 
                        value='<?php echo date('H:i a',strtotime($task->reminder_time)); ?>'
                    >
                    <input type='hidden' id='task-description-load-<?php echo $task->id; ?>' value='<?php echo $task->taskdata; ?>'>
                    <input type='hidden' id='task-created-by-load-<?php echo $task->id; ?>' value='<?php echo $task->getSender()->contactname; ?>'>
                </tr>
            <? } ?>
        <? } ?>
		</tbody>
    </table>

</div>
</div>
</div>
    
<?php
} else {
?>

        <div class="kt-portlet__body" style="padding-top: 0px !important;padding-bottom: 0px !important" >
        <div class="kt-section">
         <div class="kt-section__content">


      <table class="table table-bordered"  id="task_home" >
        <thead>
        <tr>
            <th>&nbsp;</th>
            <th >ID</th>
            <th><?= $this->order->getTitle("date", "Created On") ?></th>
            <th>Subject</th>
            <th>Assigned Users</th>
            <th width="200" style="white-space: nowrap;"><?= $this->order->getTitle("sender_id", "Created By") ?></th>
            <th class="grid-head-right" >Actions</th>
        </tr>
        </thead>
        <?php if (count($this->data) == 0) : ?>
            <tr class="grid-body ">
                <td class="grid-body-left grid-body-right" align="center" colspan="7"><i>You have no tasks.</i></td>
            </tr>
			<?php endif; ?>
			<? foreach ($this->data as $i => $task) {
        	if($task->entity_id != 0){
	        	$entity->load($task->entity_id);
        	}
			?>
            <tr class="grid-body<?= ($i == 0 ? " " : "") ?>" id="row-<?= $task->id ?>">
                <td align="center">
                    <?php /* ?> <input type="checkbox" name="task_id" class="task_id" value="<?php echo $task->id; ?>"> <?php */ ?>
					<label class="kt-checkbox kt-checkbox--bold kt-checkbox--success" style="    margin-bottom: 18px;">
                        <input type="checkbox" name="task_id" class="task_id" value="<?php echo $task->id; ?>" /> 
                        <span></span>
					</label>
                </td>
                <td align="center">
                    <a target="_blank" href="<?php echo getLink('orders', 'searchorders', 'type1', 'orderid', 'search_string', $task->entity_id); ?>">
                        <?php echo $task->entity_id == 0 ? "" : $entity->prefix."-".$entity->number; ?>
                    </a>
                </td>
                <td>
					<span><?= date('m-d-Y',strtotime($task->date)); ?></span>
                </td>
                <td onclick="show_description_loaded(<?php echo $task->id; ?>);">
                    <a style="color:#008ec2;cursor: pointer;">
                        <?= $task->message ?>	
                    </a>
                </td>
                <td class="kt-font-bold kt-font-success">
					<span>
					<?php
                    $members = array();
                    foreach ($task->getMembers() as $member) {
                        $members[] = $member->contactname;
                    } ?>
					</span>
					<?= implode(', ', $members) ?>
                </td>
                <td class="kt-font-bold kt-font-primary"><?= $task->getSender()->contactname ?></td>
                <td>
					<div class="pull-left" style="margin-right:15px;">
						<?= editIcon('javascript:editTask(' . $task->id . ')') ?>
					</div>
					<div class="pull-left">
						<?= deleteIcon(getLink("tasks", "delete", "id", $task->id), "row-" . $task->id) ?>
					</div>
                </td>


                <input type='hidden' id='task-subject-load-<?php echo $task->id; ?>' value='<?php echo $task->message; ?>'>
                <input type='hidden' id='task-reminder-load-<?php echo $task->id; ?>' value='<?php echo $task->reminder; ?>'>
                <input  type='hidden' 
                    id='task-reminder_date-load-<?php echo $task->id; ?>' 
                    value='<?php echo date('m-d-Y',strtotime($task->reminder_date)); ?>'
                >
                <input  type='hidden' 
                    id='task-reminder_time-load-<?php echo $task->id; ?>' 
                    value='<?php echo date('h:i a',strtotime($task->reminder_time)); ?>'
                >
                <input type='hidden' id='task-description-load-<?php echo $task->id; ?>' value='<?php echo $task->taskdata; ?>'>
                <input type='hidden' id='task-created-by-load-<?php echo $task->id; ?>' value='<?php echo $task->getSender()->contactname; ?>'>
            </tr>
        <? } ?>
    </table>
</div>
</div>
</div>
<?php
}
?>
<script>
    function show_description_loaded(id) 
    {
        $("#task-subject").html($("#task-subject-load-" + id).val());
        $("#task-body").html($("#task-description-load-" + id).val());
        let reminder_flag = $("#task-reminder-load-" + id).val();
        let loaded_popup_buttons;
        $("#task-created-by").html($("#task-created-by-load-" + id).val());
        $('#title_add').text('Full Task Description');
        $("#t_description").find('.modal-backdrop').addClass('strike');
        $('#t_description').addClass('strike');;
        $("#complete").val(id);
        $("#t_description").modal();
        $("#t_description").find('.modal-backdrop').addClass('strike');
        $('#t_description').addClass('strike');;
    }

</script>
<script type="text/javascript">
    $("#edit_task_assigned").select2({
    });
 </script>

<script type="text/javascript">
    $(document).ready(function() {
   $('#task_home').DataTable({
       "lengthChange": false,
       "paging": false,
       "bInfo" : false,
       'drawCallback': function (oSettings) {
           $('#task_home_wrapper').children('.row:first').children('.col-md-6:first').html('<div class="form-group row" style="margin-left:0;"><label class="col-form-label">Show: </label><select class=" records_per_page form-box-combobox form-control " style="width:100px;" onchange="setPagerLimit(this.value)"> <option value="10">10</option> <option value="25">25</option> <option value="50" selected="selected">50</option> <option value="100">100</option> <option value="200">200</option> <option value="500">500</option> </select></div>');
          
           
      }
   });
} );
</script>


