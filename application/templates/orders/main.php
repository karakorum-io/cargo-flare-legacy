<script type="text/javascript">
    var notes = [];
    notes[<?= Note::TYPE_TO ?>] = [];
    notes[<?= Note::TYPE_FROM ?>] = [];
    notes[<?= Note::TYPE_INTERNAL ?>] = [];
    var notesIntervalId = undefined;
    var add_entity_id;
    var add_notes_type;
    var add_busy = false;

    function mapIt(entity_id) {
        $.ajax({
            type: 'POST',
            url: '<?= SITE_IN ?>application/ajax/map.php',
            data: {
                action: 'getRoute',
                entity_id: entity_id
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    window.open(decodeURIComponent(response.data), "_blank");
                }
            }
        });
    }

    function chageStatus(status) {
        if ($(".order-checkbox:checked").size() == 0) {
            alert("You have no selected orders.");
        } else {
            var entity_ids_ids = [];
            $(".order-checkbox:checked").each(function() {
                entity_ids_ids.push($(this).val());
            });
            $.ajax({
                type: 'POST',
                url: '<?= SITE_IN ?>application/ajax/entities.php',
                dataType: 'json',
                data: {
                    action: 'chageStatus',
                    status: status,
                    entity_ids: entity_ids.join(',')
                },
                success: function(response) {
                    if (response.success) {
                        window.location.reload();
                    }
                }
            });
        }
    }

    function placeOnHold() {
        changeStatus(<?= Entity::STATUS_ONHOLD ?>);
    }

    function restore() {
       
        changeStatus(<?= Entity::STATUS_ACTIVE ?>);
    }

    function cancel() {
        changeStatus(<?= Entity::STATUS_ARCHIVED ?>);
    }

    function reassignOrders(member) {
        var member_id = 0;
        member_id = member;
        if (member_id == 0) {
            swal.fire("You must select member to assign");
            return;
        }
        if ($(".order-checkbox:checked").length == 0) {
            swal.fire("Order not selected");
            return;
        }

        //var entity_id = $(".order-checkbox:checked").val();        
        var entity_ids = [];
        $(".order-checkbox:checked").each(function() {

            var entity_id = $(this).val();
            entity_ids.push(entity_id);

        });
        //entity_ids.push(entity_id); 
         
        $.ajax({
            type: 'POST',
            url: '<?= SITE_IN ?>application/ajax/entities.php',
            dataType: "json",
            data: {
                action: 'reassign',
                assign_id: member_id,
                entity_ids: entity_ids.join(',')
            },
            success: function(response) {
                if (response.success) {
                    window.location.reload();
                } else {
                    /*$("#reassignCompanyDiv").nimbleLoader('hide');*/
                    
                    
                    $("#reassignCompanyDiv").find('.reassignCompanyDiverror').html('Reassign failed. Try again later, please.');
                    $(".reassignCompanyDiverror").css('display','block');
                }
            },
            error: function(response) {
                  $("#reassignCompanyDiv").find('.reassignCompanyDiverror').html('Reassign failed. Try again later, please.');
                  $(".reassignCompanyDiverror").css('display','block');
            },
            complete: function(res) {

/*                $("#reassignCompanyDiv").nimbleLoader('hide');*/

            }
        });
    }

    function printOrders(printWindow, entity_ids) {
        if (entity_ids.length > 0) {
            $.ajax({
                type: "POST",
                url: "<?= SITE_IN ?>application/ajax/entities.php",
                dataType: "json",
                data: {
                    action: 'print',
                    entity_ids: entity_ids
                },
                success: function(response) {
                    if (response.success) {
                        printWindow.document.write('<html><head><title>Orders</title>');
                        printWindow.document.write('<link rel="stylesheet" href="<?= SITE_IN ?>styles/application_print.css" type="text/css" />');
                        printWindow.document.write('</head><body><table cellspacing="0" cellpadding="3" border="1" width="100%">');
                        printWindow.document.write('<tr><th>ID</th><th>Created</th><th>Shipper</th><th>Vehicle</th><th>Origin/Destination</th><th>Tariff</th><th>Est. Ship</th></tr>');
                        for (i in response.data) {
                            printWindow.document.write('<tr>');
                            printWindow.document.write('<td class="nowrap">' + response.data[i].id + '</td>');
                            printWindow.document.write('<td>' + response.data[i].ordered + '</td>');
                            printWindow.document.write('<td>' + response.data[i].shipper + '</td>');
                            printWindow.document.write('<td>' + response.data[i].vehicle + '</td>');
                            printWindow.document.write('<td>' + response.data[i].origin_dest + '</td>');
                            printWindow.document.write('<td class="nowrap">' + response.data[i].tariff + '</td>');
                            printWindow.document.write('<td>' + response.data[i].est_ship + '</td>');
                            printWindow.document.write('</tr>');
                        }
                        printWindow.document.write('</table></body></html>');
                        printWindow.print();
                        printWindow.close();
                    } else {
                        printWindow.close();
                    }
                }
            });
        } else {
            printWindow.alert('You have no orders to print');
            printWindow.close();
        }
    }
</script>
<?php include_once("create_dispatch.php") ?>
    <?php include_once("create_payment.php") ?>
        <div id="print_container" style="display:none"></div>
        <div id="notes_container"></div>


 <div id="notes_add1">
       <div class="modal fade" id="kt_modal_4" tabindex="-1" role="dialog" aria-labelledby="notes_add12" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="notes_add12">
                        <div id="notes_add_title"> </div>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i class="fa fa-times" aria-hidden="true"></i>
                    </button>
                </div>

                <div class="modal-body">
                     
                    <div class="form-group" style="max-height:300px;overflow:auto;">
                    <div id="notes_container_new" class="notes_container_new_info"> </div>

                    </div>

                    <div class="form-group">
                        <label for="message-text" class="form-control-label">Add Internal Note:</label>
                        <textarea class="form-control"  class="form-box-textarea" name="add_note_text" ></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                 <label for="message-text" class="form-control-label">Quick Notes:</label>
                                <select name="quick_notes" class="form-control" id="quick_notes" onchange="addQuickNote();">
                                    <option value="">--Select--</option>
                                    <option value="Emailed: Customer.">Emailed: Customer.</option>
                                    <option value="Emailed: Bad e-mail.">Emailed: Bad e-mail.</option>
                                    <option value="Faxed: e-Sign.">Faxed: e-Sign.</option>
                                    <option value="Faxed: B2B.">Faxed: B2B.</option>
                                    <option value="Faxed: Invoice.">Faxed: Invoice.</option>
                                    <option value="Faxed: Recepit.">Faxed: Recepit.</option>
                                    <option value="Phoned: Bad Mobile.">Phoned: Bad Number.</option>
                                    <option value="Phoned: No Voicemail.">Phoned: No Voicemail.</option>
                                    <option value="Phoned: Left Message.">Phoned: Left Message.</option>
                                    <option value="Phoned: No Answer.">Phoned: No Answer.</option>
                                    <option value="Phoned: Spoke to Customer.">Phoned: Spoke to Customer.</option>
                                    <option value="Phoned: Spoke to carrier about pick-up.">Phoned: Spoke to carrier about pick-up.</option>
                                    <option value="Phoned: NSpoke to carrier about drop-off.">Phoned: Spoke to carrier about drop-off.</option>
                                    <option value="Phoned: Customer requested carrier info.">Phoned: Customer requested carrier info.</option>
                                    <option value="Phoned: Customer requested damage.">Phoned: Customer requested damage.</option>
                                    <option value="Phoned: Customer canceled, late pick-up.">Phoned: Customer canceled, late pick-up.</option>
                                    <option value="Phoned: Customer canceled, no reason given.">Phoned: Customer canceled, no reason given.</option>
                                    <option value="Phoned: Customer canceled, through e-Mail.">Phoned: Customer canceled, through e-Mail.</option>
                                    <option value="Phoned: Customer was happy with transport.">Phoned: Customer was happy with transport.</option>
                                    <option value="Phoned: Customer was un-happy with transport.">Phoned: Customer was un-happy with transport.</option>
                                    <option value="Phoned: Customer want a refund.">Phoned: Customer want's a refund.</option>
                                    <option value="Phoned: Not Interested.">Phoned: Not Interested.</option>
                                    <option value="Phoned: Do Not Call.">Phoned: Do Not Call.</option>
                                </select>
                            </div>

                        </div>

                        <div class="col-md-6">
                            <div  class="form-group">
                               <label for="message-text" class="form-control-label">Priority:</label>
                                <select name="priority_notes"  class="form-control" id="priority_notes" >
                                    <option value="0">--Select--</option>
                                    <option value="2">High</option>
                                    <option value="1">Low</option>
                                </select>
                            </div>
                        </div>
                    </div>      


                    <?= functionButton('Add Note', 'addNote()','','btn_bright_blue btn-sm') ?>
                    <?= functionButton('Cancel', 'closeAddNotes()','','btn-sm btn-dark') ?>
                </div>
            </div>
        </div>
    </div>
</div>










        
        <?php include_once("menu.php"); ?>

    <div class="quote-info accordion_main_info_new">
    <div class="row">           
        <div class="col-12">
            <div class="kt-portlet ">
               
                <div class="kt-portlet__body ">
                    <div class="row">
                        <div class="col-12 col-sm-12">
                           @content@
                        </div>
                    </div>
                   
                </div>
            </div>              
        </div>
    </div>
</div>
    

              

       