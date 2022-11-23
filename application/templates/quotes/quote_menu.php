<script type="text/javascript">
    var anim_busy = false;

    function setStatus(status) {
        $.ajax({
            type: "POST",
            url: "<?= SITE_IN ?>application/ajax/entities.php",
            dataType: "json",
            data: {
                action: 'setStatus',
                entity_id: <?= $this->entity->id ?>,
                status: status
            },
            success: function(result) {
                if (result.success == true) {

                    swal.fire("Quote action Successful");
                    window.location.reload();
                } else {
                    swal.fire("Quote action failed. Try again later, please.");
                }
            },
            error: function(result) {
                swal.fire("Quote action failed. Try again later, please.");
            }
        })
    }

    function reassign() {
        $("#reassign_dialog").modal();
    }

    function reassign_send() {
        var assign_id = $("#reassign_dialog select").val();
        $.ajax({
            type: "POST",
            url: "<?= SITE_IN ?>application/ajax/entities.php",
            dataType: "json",
            data: {
                action: 'reassign',
                entity_id: <?= $this->entity->id ?>,
                assign_id: assign_id
            },
            success: function(result) {
                if (result.success == true) {
                    window.location.reload();
                } else {
                    $("#reassign_dialog div.error").html("<p>Can't reassign quote. Try again later, please.</p>");
                    $("#reassign_dialog div.error").slideDown().delay(2000).slideUp();
                }
            },
            error: function(result) {
                $("#reassign_dialog div.error").html("<p>Can't reassign quote. Try again later, please.</p>");
                $("#reassign_dialog div.error").slideDown().delay(2000).slideUp();
            }
        })
    }

    function split() {
        $("#split_dialog").dialog({
            width: 400,
            modal: true,
            resizable: false,
            title: "Split Quote",
            buttons: [{
                    text: "Split",
                    click: function() {
                        if ($("#split_dialog .vehicle_ids:checked").size() == 0) {
                            $(this).dialog("close");
                            return;
                        }
                        var vehicle_ids = [];
                        $("#split_dialog .vehicle_ids:checked").each(function() {
                            vehicle_ids.push($(this).val());
                        });
                        $.ajax({
                            type: "POST",
                            url: "<?= SITE_IN ?>application/ajax/entities.php",
                            dataType: "json",
                            data: {
                                action: 'split',
                                entity_id: <?= $this->entity->id ?>,
                                vehicle_ids: vehicle_ids.join(',')
                            },
                            success: function(result) {
                                if (result.success == true) {
                                    if ($("#split_dialog input[name='after_split']").val() == 1) {
                                        window.location.href = "<?= SITE_IN ?>application/quotes/show/id/" + result.data;
                                        $(this).dialog("close");
                                    } else {
                                        window.location.reload();
                                    }
                                } else {
                                    alert("Split failed. Try again later, please.");
                                }
                            },
                            error: function(result) {
                                alert("Split failed. Try again later, please.");
                            }
                        });
                        $(this).dialog("close");
                    }
                },
                {
                    text: "Cancel",
                    click: function() {
                        $(this).dialog("close");
                    }
                }
            ]
        }).dialog("open");
    }

    function convertToOrder() {
        $.ajax({
            type: "POST",
            url: "<?= SITE_IN ?>application/ajax/entities.php",
            dataType: "json",
            data: {
                action: "toOrder",
                entity_id: <?= $this->entity->id ?>
            },
            success: function(result) {
                if (result.success == true) {
                    Swal.fire('Please wait');
                    Swal.showLoading();
                    window.location.href = '<?= SITE_IN ?>application/orders/edit/id/<?= $this->entity->id ?>';
                } else {
                    swal.fire("Can't convert Quote. Try again later, please");
                }
            },
            error: function(result) {
                swal.fire("Can't convert Quote. Try again later, please");
            }
        });
    }
</script>

<div id="split_dialog" style="display:none">
    <p><strong>Select Vehicle(s) for new Quote:</strong></p>
    <table cellspacing="0" cellpadding="0" border="0" class="grid" width="100%">
        <?php foreach ($this->entity->getVehicles() as $key => $vehicle) : ?>
            <tr class="grid-body">
                <td><label for="vehicle_ids_<?= $key ?>"><?= $vehicle->make ?> / <?= $vehicle->model ?>
                        / <?= $vehicle->year ?> / <?= $vehicle->type ?></label></td>
                <td width="20"><input type="checkbox" class="vehicle_ids" id="vehicle_ids_<?= $key ?>" name="vehicle_ids[<?= $key ?>]" value="<?= $vehicle->id ?>" /></td>
            </tr>
        <?php endforeach; ?>
    </table>
    <br />

    <p><strong>After Split:</strong></p>
    <table cellspacing="0" cellpadding="0" border="0" class="grid">
        <tr>
            <td><input type="radio" name="after_split" value="1" id="go_new_quote" checked="checked" /></td>
            <td><label for="go_new_quote"><strong>Go to new Quote</strong></label></td>
            <td><input type="radio" name="after_split" value="2" id="stay_here" /></td>
            <td><label for="stay_here"><strong>Stay Here</strong></label></td>
        </tr>
    </table>
</div>

<div class="modal fade" id="reassign_dialog" tabindex="-1" role="dialog" aria-labelledby="reassign_dialog_modal" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reassign_dialog_modal">Reassign Quote</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i class="fa fa-times" aria-hidden="true"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="error" style="display:none;"></div>
                <label>Assign to:</label>
                <select class="form-box-combobox form-control">
                    <?php foreach ($this->company_members as $member) : ?>
                        <?php if ($member->status == "Active") {
                            $activemember .= "<option value= '" . $member->id . "'>" . $member->contactname . "</option>";
                        }
                        ?>
                    <?php endforeach; ?>
                    <optgroup label="Active User">
                        <?php echo $activemember; ?>
                    </optgroup>
                    <!--optgroup label="InActive User">
                    <?php //echo $inactivemember; 
                    ?>
                    </optgroup-->
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" id="reassign_dialog_modal" onclick="reassign_send()" class="btn btn-primary">Assign</button>
            </div>
        </div>
    </div>
</div>

<div class="alert alert-light alert-elevate" role="alert">
    <div class="col-10" style="padding-left:0;">
        <ul class="nav nav-tabs nav-tabs-line nav-tabs-line-3x nav-tabs-line-success pull-left" role="tablist" style="margin-bottom: 0px">

            <li class="nav-item custom_set">
                <a class="nav-link <?= (@$_GET['quotes'] == 'show') ? " active" : "" ?>" href="<?= SITE_IN ?>application/quotes/show/id/<?= $this->entity->id ?>">Quote Details</a>
            </li>

            <?php if (($this->entity->status != Entity::STATUS_ARCHIVED) && !$this->entity->readonly) : ?>
                <li class="nav-item">
                    <a class="nav-link <?= (@$_GET['quotes'] == 'edit') ? " active" : "" ?>" href="<?= SITE_IN ?>application/quotes/edit/id/<?= $this->entity->id ?>">Edit Quote</a>
                </li>
            <?php endif; ?>

            <li class="nav-item">
                <a class="nav-link <?= (@$_GET['quotes'] == 'history') ? " active" : "" ?>" href="<?= SITE_IN ?>application/quotes/history/id/<?= $this->entity->id ?>">Quote History</a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?= (@$_GET['quotes'] == 'uploads') ? " active" : "" ?>" href="<?= SITE_IN ?>application/quotes/uploads/id/<?= $this->entity->id ?>">Documents</a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?= (@$_GET['quotes'] == 'mail_history') ? " active" : "" ?>" href="<?= SITE_IN ?>application/quotes/mail_history/id/<?= $this->entity->id ?>">Mail History</a>
            </li>
        </ul>
    </div>
    <?php
        if (is_array($_SESSION['searchData']) && $_SESSION['searchCount'] > 0) {
        $eid = $_GET['id'];
        $indexSearchData = array_search($eid, $_SESSION['searchData']);

        $nextSearch = $indexSearchData + 1;
        $_SESSION['searchShowCount'] = $indexSearchData;
        $prevSearch = $indexSearchData - 1;

        $entityPrev = $_SESSION['searchData'][$prevSearch];
        $entityNext = $_SESSION['searchData'][$nextSearch];
    ?>
    <div class="col-2">
        <div style="float:left;">
            <?php if ($_SESSION['searchShowCount'] == 0) { ?>
                <img src="<?= SITE_IN ?>images/arrow-down-gray.png" width="40" height="40" />
            <?php } else { ?>
                <a href="<?= SITE_IN ?>application/quotes/show/id/<?= $entityPrev ?>">
                    <img src="<?= SITE_IN ?>images/arrow-down.png" width="40" height="40" />
                </a>
            <?php } ?>
        </div>
        <div style="float:left; padding-top:10px;">
            <h3><?php print $_SESSION['searchShowCount'] + 1; ?> - <?php print $_SESSION['searchCount']; ?></h3>
        </div>
        <div style="float:left;width:50px;">
            <?php if ($_SESSION['searchShowCount'] == ($_SESSION['searchCount'] - 1)) { ?>
                <img src="<?= SITE_IN ?>images/arrow-up-gray.png" width="40" height="40" />
            <?php } else { ?>
                <a href="<?= SITE_IN ?>application/quotes/show/id/<?= $entityNext ?>">
                    <img src="<?= SITE_IN ?>images/arrow-up.png" width="40" height="40" />
                </a>
            <?php } ?>
        </div>
    </div>
    <?php 
        }
    ?>
</div>

<div class="kt-portlet">
    <div class="kt-portlet__body">
        <div class="row">
            <div class="col-12 col-sm-3">
                <h3 style="margin-top:5px;">Quote #<?= $this->entity->getNumber() ?></h3>
            </div>
            <div class="col-12 col-sm-9 new_btn_info_new_2 text-right">
                <?php if (($this->entity->status != Entity::STATUS_ARCHIVED) && !$this->entity->readonly) : ?>
                    <?= functionButton('Print', 'printSelectedQuoteForm()', '', 'btn-sm btn_bright_blue') ?>
                    <?= $this->form_templates; ?>
                    <?= functionButton('Email', 'emailSelectedQuoteFormNew()', '', 'btn-sm btn_bright_blue') ?>
                    <?php // functionButton('Email', 'emailSelectedQuoteForm()'); 
                    ?>
                    @email_templates@
                    <?php if (count($this->entity->getVehicles()) > 1) : ?>
                        <?= functionButton('Split', 'split()', '', 'btn-warning') ?>
                    <?php endif; ?>
                    <?= functionButton('Reassign', 'reassign()') ?>
                    <?php if ($this->entity->status == Entity::STATUS_ACTIVE) : ?>
                        <?= functionButton('Convert to Order', 'convertToOrder()', '', 'btn-sm btn_bright_blue') ?>
                        <?= functionButton('On Hold', 'setStatus(' . Entity::STATUS_ONHOLD . ')') ?>
                    <?php elseif ($this->entity->status == Entity::STATUS_ONHOLD) : ?>
                        <?= functionButton('Remove Hold', 'setStatus(' . Entity::STATUS_ACTIVE . ')') ?>
                    <?php endif; ?>
                    <?= functionButton('Cancel', 'setStatus(' . Entity::STATUS_ARCHIVED . ')', '', 'btn-sm btn-dark') ?>
                <?php endif; ?>
            </div>

            <script type="text/javascript">
                function emailSelectedQuoteForm() {
                    form_id = $("#email_templates").val();
                    console.log("ddd", form_id);

                    if (form_id == "") {
                        swal.fire("Please choose email template");
                    } else {
                        if (confirm("Are you sure want to send Email?")) {
                            /*$("body").nimbleLoader('show');*/
                            $.ajax({
                                type: "POST",
                                url: BASE_PATH + "application/ajax/entities.php",
                                dataType: "json",
                                data: {
                                    action: "emailQuote",
                                    form_id: form_id,
                                    entity_id: <?= $this->entity->id ?>
                                },
                                success: function(res) {
                                    if (res.success) {
                                        swal.fire("Email was successfully sent");
                                    } else {
                                        swal.fire("Can't send email. Try again later, please");
                                    }
                                },
                                complete: function(res) {
                                    /* $("body").nimbleLoader('hide');*/
                                }
                            });
                        }
                    }
                }

                function printSelectedQuoteForm() {
                    form_id = $("#form_templates").val();
                    if (form_id == "") {
                        swal.fire("Please choose form template");
                    } else {
                        $.ajax({
                            url: BASE_PATH + 'application/ajax/entities.php',
                            data: {
                                action: "print_quote",
                                form_id: form_id,
                                quote_id: '<?= $_GET["id"]; ?>',
                            },
                            type: 'POST',
                            dataType: 'json',
                            beforeSend: function() {},
                            success: function(retData) {
                                printQuote(retData.printform);
                            }
                        });
                    }
                }

                function createAndEmail() {
                    $("#co_send_email").val("1");
                    $("#submit_button").click();
                }

                function Processing_show() {
                    KTApp.blockPage({
                        overlayColor: '#000000',
                        type: 'v2',
                        state: 'primary',
                        message: '.'
                    });
                }

                function emailSelectedQuoteFormNew() {
                    form_id = $("#email_templates").val();
                    if (form_id == "") {
                        swal.fire("Please choose email template");
                    } else {
                        Processing_show();
                        $.ajax({
                            type: "POST",
                            url: BASE_PATH + "application/ajax/entities.php",
                            dataType: "json",
                            data: {
                                action: "emailQuoteNew",
                                form_id: form_id,
                                entity_id: <?= $this->entity->id ?>
                            },
                            success: function(res) {
                                console.log(res);
                                if (res.success) {
                                    $("#form_id").val(form_id);
                                    $("#mail_to_new").val(res.emailContent.to);
                                    $("#mail_subject_new").val(res.emailContent.subject);

                                    $("#mail_body_new").val(res.emailContent.body);

                                    CKEDITOR.instances['mail_body_new'].setData(res.emailContent.body);
                                    $("#maildivnew1").modal();
                                } else {
                                    swal.fire("Can't send email. Try again later, please");
                                }
                            },
                            complete: function(res) {
                                KTApp.unblockPage();
                            }
                        });
                    }
                }

                function maildivnew_send_old() {
                    $.ajax({
                        url: BASE_PATH + 'application/ajax/entities.php',
                        data: {
                            action: "emailQuoteNewSend",
                            form_id: $('#form_id').val(),
                            entity_id: <?= $this->entity->id ?>,
                            mail_to: $('#mail_to_new').val(),
                            mail_subject: $('#mail_subject_new').val(),
                            mail_body: $('#mail_body_new').val()
                        },
                        type: 'POST',
                        dataType: 'json',
                        beforeSend: function() {
                            if (!validateMailFormNew()) {
                                return false;
                            } else {
                                $('#maildivnew1').find(".modal-body").addClass('kt-spinner kt-spinner--lg kt-spinner--success');
                                $("maildivnew_send_close").attr('disabled', 'disabled');
                                $("maildivnew_send").attr('disabled', 'disabled');
                            }
                        },
                        success: function(response) {
                            $('#maildivnew1').find(".modal-body").removeClass('kt-spinner kt-spinner--lg kt-spinner--success');
                            if (response.success == true) {
                                clearMailForm();
                                $("#maildivnew1").modal('hide');
                            }
                        },
                        complete: function() {
                            $('#maildivnew1').find(".modal-body").removeClass('kt-spinner kt-spinner--lg kt-spinner--success');
                            $("#maildivnew1").modal('hide');
                        }
                    });
                }

                function maildivnew_send() { 
                    var sEmail = [$('#mail_to_new').val(), $('.optionemailextra').val(), $('#mail_cc_new').val(), $('#mail_bcc_new').val()];
                    if (validateEmail(sEmail) == false) {
                        Swal.fire('Invalid Email Address');
                        return false;
                    }
                    if ($('#attachPdf').is(':checked')) {
                        attach_type = $('#attachPdf').val();
                    } else {
                        attach_type = $('#attachHtml').val();
                    }

                    $.ajax({
                        url: BASE_PATH + 'application/ajax/entities.php',
                        data: {
                            action: "emailOrderNewSend",
                            form_id: $('#form_id').val(),
                            entity_id: <?= $this->entity->id ?>,
                            mail_to: $('#mail_to_new').val(),
                            mail_cc: $('#mail_cc_new').val(),
                            mail_bcc: $('#mail_bcc_new').val(),
                            mail_extra: $('.optionemailextra').val(),
                            mail_subject: $('#mail_subject_new').val(),
                            mail_body: $('#mail_body_new').val(),
                            attach_type: attach_type
                        },
                        type: 'POST',
                        dataType: 'json',
                        beforeSend: function () {
                            if ($('#mail_to_new').val() == "" || $('#mail_subject_new').val() == "" || $('#mail_body_new').val() == "") {
                                Swal.fire('Empty Field(s)');
                                return false;
                            }
                            ;
                        },
                        success: function (response) {
                            // $("body").nimbleLoader("hide");
                            if (response.success == true) {
                                // $("#maildivnew").dialog("close");
                                clearMailForm();
                            }

                            $("#maildivnew1").modal('hide');
                        },
                        complete: function () {
                            // $("body").nimbleLoader("hide");
                        }
                    });
                }

                function validateEmail(sEmail) {
                    var res = "", res1 = "", i;
                    var filter = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
                    for (i = 0; i < sEmail.length; i++) {
                        if (filter.test(sEmail[i])) {
                            res += sEmail[i];
                        } else {
                            res1 += sEmail[i];
                        }
                    }
                    if (res1 !== '') {
                        return false;
                    }
                }

            </script>

            <div class="modal fade" id="maildivnew1" tabindex="-1" role="dialog" aria-labelledby="maildivnew_modal" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="maildivnew_model">Email message</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <i class="fa fa-times" aria-hidden="true"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div style="float: left;">
                                <ul style="margin-top: 26px;">
                                    <li style="margin-bottom: 14px;">Form Type <input value="1" id="attachPdf" name="attachTpe" type="radio"/><label for="attachPdf" style="margin-right: 2px; cursor:pointer;"> PDF</label><input value="0" id="attachHtml"  name="attachTpe" type="radio"/><label for="attachHtml" style="cursor:pointer"> HTML</label></li>
                                    <li style="margin-bottom: 11px;">Attachment(s): <span style="color:#24709F;" id="mail_att_new"></span></li>
                                </ul>
                            </div>
                            <div style="text-align: right;">
                                <div style="text-align: right;">
                                    <img src="<?php echo SITE_IN;?>/images/icons/add.gif"> <span style="margin-bottom: 3px;cursor:pointer; position: relative;bottom:4px; color:#24709F;" class="add_one_more_field_" >Add a Field</span>
                                    <ul>
                                        <li id="extraEmailsingle" style="margin-bottom: 6px;"><span>Email:<span style="color:red">*</span></span> <input type="text" id="mail_to_new" name="mail_to_new" class="form-box-combobox" ></li>
                                        <li style="margin-bottom: 6px;margin-top: 6px;margin-left: 292px; position:relative; display: none;" id="mailexttra"><input name="optionemailextra" class="form-box-combobox optionemailextra" type="text"><a href="#" style="position: absolute;margin-left: 2px;margin-top: 8px;" class="remove_2sd_field"><img id="singletop" style="width: 12px;height: 12px;" src="<?php echo SITE_IN;?>/images/icons/delete.png"></a></li>
                                        <li style="margin-bottom: 6px;"><span style="margin-right: 18px;">CC:</span> <input type="text" id="mail_cc_new" name="mail_cc_new" class="form-box-combobox" ></li>
                                        <li style="margin-bottom: 12px;"><span style="margin-right: 9px;">BCC:</span> <input type="text" id="mail_bcc_new" name="mail_bcc_new" class="form-box-combobox" ></li>
                                    </ul>
                                </div>
                                <div class="edit-mail-content" style="margin-bottom: 8px;">
                                    <div class="edit-mail-row" style="margin-bottom: 8px;">
                                        
                                        <div class="form-group" >
                                            <label >Subject:<span>*</span></label>
                                            <input type="text" id="mail_subject_new" class="form-box-textfield" maxlength="255" name="mail_subject_new" ></div>
                                    </div>
                                    <div  style="width: 100%">
                                        <div class="form-group" >
                                            <label class="">Body:<span>*</span></label>
                                            <textarea class="form-box-textfield form-control" name="mail_body_new" id="mail_body_new"></textarea></div>
                                    </div>
                                </div>
                                <input type="hidden" name="form_id" id="form_id"  value=""/>
                                <input type="hidden" name="entity_id" id="entity_id"  value=""/>
                                <input type="hidden" name="skillCount" id="skillCount" value="1">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-dark btn-sm" data-dismiss="modal">Cancal</button>
                            <button type="button" class="btn btn-primary btn-sm" onclick="maildivnew_send();">Submit</button>
                        </div>
                    </div>
                </div>
            </div>

            <script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.11.4/ckeditor.js"></script>
            <script>
                CKEDITOR.replace('mail_body_new');
            </script>
        </div>
    </div>
</div>