<script src="<?php echo SITE_IN; ?>styles/new/assets/vendors/general/bootstrap/dist/js/bootstrap.min.js" type="text/javascript"></script>
<style type="text/css">
    .task-tab-contents{
        padding:10px;
    }
    .modal .modal-content .modal-header .close:before {
   display: none;
}
.swal2-container.swal2-center.swal2-fade.swal2-shown {
    z-index: 9999999999999999999;
}

   
</style>
<content>
    <!--Tasks listing popup-->
    <!-- <div id="tasks-listing-popup" style="display: none;">
        <br>
        <h3>Manage tasks from here!</h3>
        <center>
        <table cellspacing="0" cellpadding="0" border="0" class="form-table" style="white-space:nowrap;">
            <tbody>
                <tr>
                    <td>
                        <input 
                            name="search_listing_popup" 
                            type="text" 
                            maxlength="255" 
                            class="form-box-textfield" 
                            id="search_listing_popup" 
                            value="<?php echo $_POST['search_listing_popup'] == NULL ? "" : $_POST['search_listing_popup']; ?>"
                            placeholder = "Search your tasks here"
                        >
                        &nbsp;
                        <input type="button" value="Search" onclick="tasks_listings(1)" class="searchform-button searchform-buttonhover">
                    </td>
                </tr>
            </tbody>
        </table>
        </center>
       
        <div class="tab-panel-container">
            <ul class="tab-panel">
                <li onclick="open_task_tabs(1);" class="tab task-tab-1 active">
                    <a href="#">Current Task</a>
                </li>
                <li onclick="open_task_tabs(2);" class="tab task-tab-2">
                    <a href="#">Reminders</a>
                </li>
            </ul>
           
        </div>
        <br>
        
        <div class="task-tab-contents current-task-tab">
                <table width="100%" cellpadding="0" cellspacing="0" border="0" class="grid">
                    <thead>
                            <tr class="grid-head">
                                <td>ID</td>
                                <td>Order ID</td>
                                <td>Created On</td>
                                <td>Subject</td>
                                <td>Assigned User(s)</td>
                                <td style="white-space: nowrap;">Created By</a></td>
                            </tr>
                    </thead>
                    <tbody class="today-tasks"></tbody>
                </table>
        </div>
        <div class="task-tab-contents reminder-tab" style="display: none;">
            <table width="100%" cellpadding="0" cellspacing="0" border="0" class="grid">
                <thead>
                    <tr class="grid-head">
                        <td>ID</td>
                        <td>Order ID</td>
                        <td>Reminder</td>
                        <td>Subject</td>
                        <td>Assigned User(s)</td>
                        <td style="white-space: nowrap;">Created By</a></td>
                    </tr>
                </thead>
                <tbody class="reminer-tasks"></tbody>
            </table>
        </div>
      
    </div> -->
   
</content>





<!--begin::Modal-->
                            <div class="modal fade" id="tasks-listing-popup" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel">Task Manager</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <i class="fa fa-times" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                        <div class="modal-body">

                                <h4 class="kt-font-boldest"> 
                                <strong>Manage tasks from here!</strong>
                                </h4>

                    <div  class="row justify-content-end">
                        <div class="col-md-6 col-12">
                            <div class="input-group">
                                <input 
                                    name="search_listing_popup" 
                                    type="text" 
                                    maxlength="255" 
                                    class="form-control" 
                                    id="search_listing_popup" 
                                    value="<?php echo $_POST['search_listing_popup'] == NULL ? "" : $_POST['search_listing_popup']; ?>"
                                    placeholder = "Search your tasks here"
                                    >
                                <div class="input-group-append">
                                    <button id="search_hist" class="btn btn_bright_blue" type="button" value="Search" onclick="tasks_listings(1)">Search!</button>
                                </div>
                            </div>
                        </div> 
                        


                    </div>
                                           

                               
                    

                            <ul class="nav nav-tabs nav-tabs-line nav-tabs-bold nav-tabs-line-3x nav-tabs-line-success mb-0 mt-3" role="tablist" >
                            <li class="nav-item" onclick="open_task_tabs(1);">
                            <a class="nav-link active" data-toggle="tab"  role="tab">Current Task</a>
                            </li>

                            <li class="nav-item" onclick="open_task_tabs(2);" >
                            <a class="nav-link" data-toggle="tab" href="#kt_tabs_6_3" role="tab">Reminders</a>
                            </li>
                            </ul>

                    <!--Tabs-->
                       <br>
                    <!--tabs content pane-->
                    <div class="task-tab-contents current-task-tab">
                            <table  class="table table-bordered" >
                                <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Order ID</th>
                                            <th>Created On</th>
                                            <th>Subject</th>
                                            <th>Assigned User(s)</th>
                                            <th style="white-space: nowrap;">Created By</a></th>
                                        </tr>
                                </thead>
                                <tbody class="today-tasks"></tbody>
                            </table>
                    </div>
                                <div class="task-tab-contents reminder-tab" style="display: none;">
                                    <table class="table table-bordered" >
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Order ID</th>
                                                <th>Reminder</th>
                                                <th>Subject</th>
                                                <th>Assigned User(s)</th>
                                                <th style="white-space: nowrap;">Created By</a></th>
                                            </tr>
                                        </thead>
                                        <tbody class="reminer-tasks"></tbody>
                                    </table>
                                </div>
                           </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div>
                                    </div>
                                </div>
                            </div>

                            <!--end::Modal-->








<script type="text/javascript">
    // task listing popup tabs management
    function open_task_tabs(tab_id) {
        if (tab_id == 1) {
            $(".task-tab-2").removeClass("active");
            $(".task-tab-1").addClass("active");
            $(".current-task-tab").show();
            $(".reminder-tab").hide();
        } else {
            $(".task-tab-2").addClass("active");
            $(".task-tab-1").removeClass("active");
            $(".reminder-tab").show();
            $(".current-task-tab").hide();
        }
    }

    // task listing data and popup management
    function tasks_listings(search_flag) {

      $("#search_hist").addClass('kt-spinner kt-spinner--right kt-spinner--sm kt-spinner--light');

        let search_string = "";
        if(search_flag == 1){
            search_string = $("#search_listing_popup").val();
        } else {
            search_string = "";
        }

        $.ajax({
            type: "POST",
            url: "<?php echo SITE_IN; ?>application/ajax/tasks.php",
            dataType: "json",
            data: {
                action: "today_n_reminder_tasks",
                search: search_string
            },
            success: function (response) {
                  $("#search_hist").removeClass('kt-spinner kt-spinner--right kt-spinner--sm kt-spinner--light');

                let today_tasks_ui = "";
                for (let i = 0; i < response.today_tasks.length; i++) {
                    let task_data = response.today_tasks;
                    today_tasks_ui += "<tr class='grid-body'>";
                    today_tasks_ui += "<td class='kt-badge kt-badge--brand kt-badge--inline kt-badge--pill mt-2'>" + task_data[i].id + "</td>";
                    today_tasks_ui += "<td>" + task_data[i].entity_id + "</td>";
                    today_tasks_ui += "<td class='kt-badge kt-badge--warning kt-badge--inline kt-badge--pill mt-2'>" + task_data[i].date + "</td>";
                    today_tasks_ui += "<td> <a href='#'  style='color:#008ec2;' onclick='show_description_from_list(1, "+ task_data[i].id +")'>" + task_data[i].message + "</a></td>";
                    today_tasks_ui += "<td class='kt-font-bold kt-font-primary'>" + task_data[i].assigned + "</td>";
                    today_tasks_ui += "<td>" + task_data[i].sender_id + "</td>";
                    today_tasks_ui += "<input type='hidden' id='tsk-ls-id-" + task_data[i].id + "' value='" + task_data[i].id + "'>";
                    today_tasks_ui += "<input type='hidden' id='tsk-ls-msg-" + task_data[i].id + "' value='" + task_data[i].message + "'>";
                    today_tasks_ui += "<input type='hidden' id='tsk-ls-desc-" + task_data[i].id + "' value='" + task_data[i].taskdata+"'>";
                    today_tasks_ui += "<input type='hidden' id='tsk-ls-datetime-" + task_data[i].id + "' value='"+task_data[i].dtime+"'>";
                    today_tasks_ui += "<input type='hidden' id='tsk-ls-sndr-" + task_data[i].id+"' value='" + task_data[i].sender_id + "'>";
                    today_tasks_ui += "</tr>";
                }

                let reminder_task_ui = "";
                for (let i = 0; i < response.reminder.length; i++) {
                    let task_data = response.reminder;
                    reminder_task_ui += "<tr class='grid-body'>";
                    reminder_task_ui += "<td class='kt-badge kt-badge--brand kt-badge--inline kt-badge--pill mt-2' >" + task_data[i].id + "</td>";
                    reminder_task_ui += "<td>" + task_data[i].entity_id + "</td>";
                    reminder_task_ui += "<td class='kt-badge kt-badge--warning kt-badge--inline kt-badge--pill mt-2'>" + task_data[i].dtime + "</td>";
                    reminder_task_ui += "<td> <a href='#'   onclick='show_description_from_list(2, "+ task_data[i].id +")'>" + task_data[i].message + "</a></td>";
                    reminder_task_ui += "<td class='kt-font-bold kt-font-primary'>" + task_data[i].assigned + "</td>";
                    reminder_task_ui += "<td class='kt-font-bold kt-font-primary'>" + task_data[i].sender_id + "</td>";
                    reminder_task_ui += "<input type='hidden' id='rmndr-ls-id-"+task_data[i].id+"' value='"+task_data[i].id+"'>";
                    reminder_task_ui += "<input type='hidden' id='rmndr-ls-msg-"+task_data[i].id+"' value='"+task_data[i].message+"'>";
                    reminder_task_ui += "<input type='hidden' id='rmndr-ls-desc-"+task_data[i].id+"' value='"+task_data[i].taskdata+"'>";
                    reminder_task_ui += "<input type='hidden' id='rmndr-ls-datetime-"+task_data[i].id+"' value='"+task_data[i].dtime+"'>";
                    reminder_task_ui += "<input type='hidden' id='rmndr-ls-sndr-"+task_data[i].id+"' value='"+task_data[i].sender_id+"'>";
                    reminder_task_ui += "</tr>";
                }

                $(".today-tasks").html(today_tasks_ui);
                $(".reminer-tasks").html(reminder_task_ui);
            },
            error: function (response) {
                 $("#search_hist").removeClass('kt-spinner kt-spinner--right kt-spinner--sm kt-spinner--light');
                let error_html = "<tr><td colspan='6'>" + response + "</td></tr>";
                $(".today-tasks").html(error_html);
                $(".reminer-tasks").html(error_html);
            }
        });

         $('#tasks-listing-popup').modal();

       /* $("#tasks-listing-popup").dialog({
            title: 'Task Manager',
            width: 900,
            resizable: false,
            draggable: false,
            modal: true,
            buttons: [{
                    text: "Close",
                    click: function () {
                        $(this).dialog('close');
                    }
                }]
        }).dialog('open');*/
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
    
    function show_description_from_list(flag, id){

        let message = "";
        let description = "";
        let reminder_date_time = "";
        let creator = "";
        
        if(flag == 1){
            id = $("#tsk-ls-id-" + id).val();
            message = $("#tsk-ls-msg-" + id).val();
            description = $("#tsk-ls-desc-" + id).val();
            reminder_date_time = $("#tsk-ls-datetime-" + id).val();
            creator = $("#tsk-ls-sndr-" + id).val();
        } else {
            id = $("#rmndr-ls-id-" + id).val();
            message = $("#rmndr-ls-msg-" + id).val();
            description = $("#rmndr-ls-desc-" + id).val();
            reminder_date_time = $("#rmndr-ls-datetime-" + id).val();
            creator = $("#rmndr-ls-sndr-" + id).val();
        }
        
        $("#task-subject").html(message);
        $("#task-body").html(description);
        $("#task-reminder").html(reminder_date_time);
        $("#task-created-by").html(creator);
        $('#snooze').val(id);
        $('#complete').val(id);
        $("#t_description").modal();

    }

   function show_description_from_list_save(id)
     {

        $.ajax({
                type: "POST",
                url: "<?php echo SITE_IN; ?>application/ajax/tasks.php",
                dataType: "json",
                data: {
                    action: "mark_complete_single",
                    task_id: id
                },
                success: function (response) {
                   
                    Swal.fire(
                    'successfully!',
                    'Task completed successfully.!',
                    'success',
                     '3000'
                    )  
                   
                     Swal.showLoading();
                     window.location.reload();
                },
                error: function (response) {
                    console.log("Dd");
                     Swal.fire("Can't add task now. Try again later, please");
                     $('#t_description').modal('hide');
                   
                }
            });

    }
</script>