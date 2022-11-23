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
            alert("You must select member to assign");
            return;
        }
        if ($(".order-checkbox:checked").size() == 0) {
            alert("Order not selected");
            return;
        }

        //var entity_id = $(".order-checkbox:checked").val();        
        var entity_ids = [];
        $(".order-checkbox:checked").each(function() {

            var entity_id = $(this).val();

            entity_ids.push(entity_id);

        });
        //entity_ids.push(entity_id); 
        $("#reassignCompanyDiv").nimbleLoader('show');
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
                    $("#reassignCompanyDiv").nimbleLoader('hide');
                    alert("Reassign failed. Try again later, please.");
                }
            },
            error: function(response) {
                $("#reassignCompanyDiv").nimbleLoader('hide');
                alert("Reassign failed. Try again later, please.");
            },
            complete: function(res) {

                $("#reassignCompanyDiv").nimbleLoader('hide');

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
        <div id="notes_add">
            <div id="notes_add_title"></div>
            <div id="notes_container_new" style="overflow-y:scroll;  max-height:280px; background-color:#ffffff; margin:5px; padding:5px;font-size:12px;"></div>
            <br />
            <p></p>
            <textarea class="form-box-textarea" name="add_note_text" style="padding-left:3px;font-size:11px;line-height:14px;color:#555;"></textarea>
            <div style="float:right;">
                <table cellspacing="0" cellpadding="0" border="0">
                    <tr>
                        <td style="color:#FFFFFF;">Quick Notes&nbsp;</td>
                        <td>
                            <select name="quick_notes" id="quick_notes" onchange="addQuickNote();">
                                <option value="">--Select--</option>
                                <option value="Document Upload: Release(s) attached.">Document Upload: Release(s) attached.</option>
                                <option value="Document Upload: Gate Pass(es) attached.">Document Upload: Gate Pass(es) attached.</option>
                                <option value="Document Upload: Dock Receipt attached.">Document Upload: Dock Receipt attached.</option>
                                <option value="Document Upload: Photos attached.">Document Upload: Photos attached.</option>
                                <option value="Phoned: Bad Mobile.">Phoned: Bad Number.</option>
                                <option value="Phoned: No Voicemail.">Phoned: No Voicemail.</option>
                                <option value="Phoned: Left Message.">Phoned: Left Message.</option>
                                <option value="Phoned: No Answer.">Phoned: No Answer.</option>
                                <option value="Phoned: Spoke to Customer.">Phoned: Spoke to Customer.</option>
                                <option value="Phoned: Spoke to carrier about pick-up.">Phoned: Spoke to carrier about pick-up.</option>
                                <option value="Phoned: NSpoke to carrier about drop-off.">Phoned: Spoke to carrier about drop-off.</option>
                                <option value="Phoned: Customer requested carrier info.">Phoned: Customer requested carrier info.</option>
                                <option value="Phoned: Customer reported  damage.">Phoned: Customer reported damage.</option>
                                <option value="Phoned: Customer canceled, late pick-up.">Phoned: Customer canceled, late pick-up.</option>
                                <option value="Phoned: Customer canceled, no reason given.">Phoned: Customer canceled, no reason given.</option>
                                <option value="Phoned: Customer canceled, through e-Mail.">Phoned: Customer canceled, through e-Mail.</option>
                                <option value="Phoned: Customer was happy with the transport.">Phoned: Customer was happy with the transport.</option>
                                <option value="Phoned: Customer was un-happy with the transport.">Phoned: Customer was un-happy with the transport.</option>
                                <option value="Phoned: Customer want a refund.">Phoned: Customer want's a refund.</option>
                                <option value="Phoned: Not Interested.">Phoned: Not Interested.</option>
                                <option value="Phoned: Do Not Call.">Phoned: Do Not Call.</option>
                            </select>
                        </td>
                        <td style="color:#FFFFFF;">
                            <div style="float:left; padding:2px;">
                                &nbsp;&nbsp;&nbsp;Priority&nbsp;
                            </div>
                            <div style="float:left; padding:2px;">
                                <select name="priority_notes" id="priority_notes">
                                    <option value="0">--Select--</option>
                                    <option value="2">High</option>
                                    <option value="1">Low</option>

                                </select>
                            </div>
                        </td>
                        <td>
                            <?= functionButton('Add Note', 'addNote()') ?>
                        </td>
                        <td>
                            <?= functionButton('Cancel', 'closeAddNotes()') ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <div style="padding-top: 10px;">
            <?php include_once("menu.php"); ?>
                <div style="clear: both"></div> @content@</div>

        <div class="alert-pack">
            <div class="lightModalOverlaydefault">&nbsp;</div>
            <div class="alert-container">
                <div class="alert-wrap">
                    <div class="alert-img">
                        <div class="x-mark"><a href="javascript:void(0);" id="close-alert-message"><img alt="" src="<?= SITE_IN ?>images/icons/x-mark.png" width="45" height="45"></a></div>
                        <div class="exclamation"><img alt="" src="<?= SITE_IN ?>images/icons/error.png" width="170" height="170"></div>
                    </div>
                    <div class="alert-message"></div>
                </div>
            </div>
        </div>
        <script type="text/javascript">
            $(document).ready(function() {
                $("#close-alert-message").click(function() {
                    $(".alert-pack").hide();
                });
            });
        </script>