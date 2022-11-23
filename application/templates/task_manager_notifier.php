<style type="text/css">
    .swal2-popup.swal2-modal.swal2-show {
        z-index: 99999999;
    }
    .ui-timepicker-standard {
        z-index: 10000 !important;
    }
</style>


<script>
    // show description
    function show_description(id) {

        $("#task-subject").html($("#task-subject-" + id).val());
        $("#task-body").html($("#task-description-" + id).val());
        $("#task-reminder").html($("#task-reminder_time-" + id).val() + " " + $("#task-reminder_time-" + id).val());
        $("#task-created-by").html($("#task-created-by-" + id).val());
        $("#t_description").modal();
    }



    // trigger snooze tasks
    function trigger_snooze(id) {
        $("#trigger_snooze").val(id);
        $("#snooze-time-box").modal();

    }

    function trigger_snooze_save(id) {
        var task_id = [];
        
        $.each($("input[name='task_id']:checked"), function() {
            task_id.push($(this).val());
        });

        if(task_id.length == 0){
            $engine.notify("Select atleast one task");
            return false;
        }

        $.ajax({
            type: "POST",
            url: "<?=SITE_IN?>/application/ajax/tasks.php",
            dataType: "json",
            data: {
                action: "snooze",
                task_ids: task_id,
                snooze_time: $("#snooze_time").val()
            },
            success: function(response) {
                Swal.fire(
                    'Add Snooze Successfully!',
                    'Add Snooze',
                    'success'
                )
                Swal.showLoading();
                window.location.reload();
            },
            error: function(response) {
                Swal.fire("Can't add task now. Try again later, please");

            }
        });
    }
</script>
<!--Snooze time popup-->
<!-- <div id="snooze-time-box" style="display:none;">
    <table id="snooze-time-table" width="100%" cellpadding="0" cellspacing="0" border="0">
        <thead>
            <tr>
                <td>Select Snoozing time:</td>
                <td>
                    <select class="form-box-textfield" id="snooze_time">
                        <option value="00:05:00">5 Minutes</option>
                        <option value="00:10:00">10 Minutes</option>
                        <option value="00:15:00">15 Minutes</option>
                        <option value="00:30:00">30 Minutes</option>
                        <option value="01:00:00">60 Minutesr</option>
                    </select>
                </td>
            </tr>
        </thead>
    </table>
</div> -->


<!--begin::Modal-->
<div class="modal fade" id="snooze-time-box" tabindex="-1" role="dialog" aria-labelledby="snooze-time-table1" aria-hidden="true" style="z-index: 1053">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="snooze-time-table1">Select Snoozing Time</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i class="fa fa-times" aria-hidden="true"></i>
                </button>
            </div>
            <div class="modal-body">
                <label>Select Snoozing time:</label>
                <select class="form-box-textfield" class="form-control" id="snooze_time">
                    <option value="00:05:00">5 Minutes</option>
                    <option value="00:10:00">10 Minutes</option>
                    <option value="00:15:00">15 Minutes</option>
                    <option value="00:30:00">30 Minutes</option>
                    <option value="01:00:00">60 Minutesr</option>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" id="trigger_snooze" value="" onclick="trigger_snooze_save(this.value)" class="btn btn_dark_green btn-sm">Snooze</button>
            </div>
        </div>
    </div>
</div>

<!--end::Modal-->

<!-- notification Task  -->
<div class="modal fade12" id="notification-box" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" data-backdrop="static" aria-hidden="true" style="z-index: 1051">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title_add">Alert! You have pending tasks to do.</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i class="fa fa-times" aria-hidden="true"></i>
                    </button>
            </div>
            <div class="modal-body">

                <div id="tasks-list">
                    <div id="notification-scroller">
                        <table id="notification-table" class="table table-bordered" >
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Subject</th>
                                    <th>Reminder Time</th>
                                    <th>Created By</th>
                                </tr>
                            </thead>
                            <tbody id="notifications">
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-dark btn-sm" data-dismiss="modal">Close</button>
                <button type="button" id="snooze" onclick="task_snooze()" class="btn btn-dark btn-sm">Snooze</button>
                <button type="button" id="complete" class="btn btn_dark_green btn-sm" value="complete" onclick="mark_complete()">Complete</button>
            </div>
        </div>
    </div>
</div>
<!--  -->


<!-- <div id="notification-box" style="display: none;">
    <div id="tasks-list">
        <div id="notification-scroller">
            <table id="notification-table" width="100%" cellpadding="0" cellspacing="0" border="0">
                <thead>
                    <tr class="grid-head">
                        <td>ID</td><td>Subject</td><td>Reminder Time</td><td>Created By</td>
                    </tr>
                </thead>
                <tbody id="notifications"></tr></tbody>
            </table>
        </div>
    </div>
</div>
 -->
<!--Task Desciption Popup Starts-->
<!-- <div id="t_description" style="display:none;">
    <div id="tasks-description">
        <div>
            <p><b>Subject:</b></p>
            <p id="task-subject"></p>
            <p><b>Body:</b></p>
            <p id="task-body"></p>
            <table style="width:100%;">
                <tr>
                    <td  style="border:none;"><b>Reminder</b><span id="task-reminder"></span></td>
                    <td  style="border:none;"><b>Created By:</b><span id="task-created-by"></span></td>
                    <td  style="border:none;">&nbsp;</td>
                </tr>
            </table>
        </div>
    </div>
</div>
 -->

<style type="text/css">
    .fade12 .modal-backdrop {
        z-index: 1050 !important;
    }
</style>

<!--  -->
<div class="modal fade12" id="t_description" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" data-backdrop="static" aria-hidden="true" style="z-index: 1051">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title_add">Task Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i class="fa fa-times" aria-hidden="true"></i>
                </button>
            </div>
            <div class="modal-body">

                <div>
                    <p><b>Subject:</b>
                    </p>
                    <p id="task-subject"></p>
                    <p><b>Body:</b>
                    </p>
                    <p id="task-body"></p>

                    <div class="row">
                        <div class="col-md-6 col-12">
                            <b>Reminder :</b><span id="task-reminder"></span>
                        </div>
                        <div class="col-md-6 col-12">
                            <b>Created By :</b><span id="task-created-by"></span>
                        </div>
                    </div>

                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn_light_blue btn-sm" data-dismiss="modal">Show Task List</button>
                <button type="button" id="snooze" onclick="trigger_snooze(this.value)" class="btn btn-dark btn-sm">Snooze</button>
                <button type="button" id="complete" class="btn btn_dark_green btn-sm" value="complete" onclick="show_description_from_list_save(this.value)">Complete</button>
            </div>
        </div>
    </div>
</div>
<!--  -->



<!--Task Description popup Ends-->
<style type="text/css">
    #notification-box table td {
        padding: 5px;
        padding-left: 10px;
        padding-top: 10px;
        padding-right: 10px;
    }
    #notification-box table td {
        /*border: 1px solid #ccc;*/
    }
    #notification-box a {
        color: #2574a3;
    }
    #notification-scroller {
        width: 100%;
        height: 250px;
        overflow-y: auto;
    }
</style>

<script type="text/javascript">
    var ajax_status = true;
    // check new notification every hour
    setInterval(
        function() {
            check_notification();
        }, 9000
    );

    function check_notification() {
        if (ajax_status) {
            ajax_status = false;
            $.ajax({
                type: "POST",
                url: "<?=SITE_IN?>/application/ajax/tasks.php",
                dataType: "json",
                data: {
                    action: "get_notifications"
                },
                success: function(res) {
                    ajax_status = true
                    if (res.data.length > 0) {
                        var notification_html = "";
                        for (var i = res.data.length - 1; i >= 0; i--) {

                            notification_html += "<tr>";
                            notification_html += "<td><input type='checkbox' name='task_id' class='task_id' value='" + res.data[i].id + "'></td>";
                            notification_html += "<td onclick='show_description(" + res.data[i].id + ")'>" + res.data[i].message + "</td>";
                            notification_html += "<td onclick='show_description(" + res.data[i].id + ")'>" + res.data[i].reminder_date + " " + res.data[i].reminder_time + "</td>";
                            notification_html += "<td onclick='show_description(" + res.data[i].id + ")'>" + res.data[i].sender_id + "</td>";

                            notification_html += "<input type='hidden' id='task-subject-" + res.data[i].id + "' value='" + res.data[i].message + "'>";
                            notification_html += "<input type='hidden' id='task-reminder_date-" + res.data[i].id + "' value='" + res.data[i].reminder_date + "'>";
                            notification_html += "<input type='hidden' id='task-reminder_time-" + res.data[i].id + "' value='" + res.data[i].reminder_time + "'>";
                            notification_html += "<input type='hidden' id='task-description-" + res.data[i].id + "' value='" + res.data[i].description + "'>";
                            notification_html += "<input type='hidden' id='task-created-by-" + res.data[i].id + "' value='" + res.data[i].sender_id + "'>";

                            notification_html += "</tr>";
                        }

                        $("#notifications").html(notification_html);
                        $("#notification-box").modal();

                    }
                }
            });
        }
    }

    function task_snooze() {
        var task_id = [];
        $.each($("input[name='task_id']:checked"), function() {
            task_id.push($(this).val());
        });

        if (task_id.length > 0)
       {

         $("#snooze-time-box").modal();

        } else {
            Swal.fire("Please select atleast one Task");
        }
    }
</script>