<script type="text/javascript" src="<?= SITE_IN ?>jscripts/dropzone.js"></script>
<link href="<?= SITE_IN ?>application/assets/css/dropzone.css" type="text/css" rel="stylesheet"/>

<style>
    UnCleared{
        color:red;
    }
    Cleared{
        color:green;
    }

    .error{
        margin-left: 10px;
        color: red;
        font-size: 8px;
        font-weight: 200;
    }

    .error input{
        border: 1px solid red;
    }

    .form-steps {
        display: none;
    }

    .step {
        height: 15px;
        width: 15px;
        margin: 0 2px;
        background-color: #bbbbbb;
        border: none;
        border-radius: 50%;
        display: inline-block;
        opacity: 0.5;
    }

    .step.active {
        background-color: #008ec2;
    }

    .new_form-group label{
        margin-top:9px !important;
    }
</style>

<button class="btn btn-primary" data-toggle="modal" data-backdrop="static" data-target="#uploaderModal">Upload New</button>

<br><br>
<?php
    // fetching default age from settings
    $sql = "SELECT carrier_invoice_terms FROM app_defaultsettings WHERE `owner_id` = ".$_SESSION['member']['parent_id'];
    $AgeFromSettings = $this->daffny->DB->query($sql);
    $AgeFromSettings = mysqli_fetch_assoc($AgeFromSettings)['carrier_invoice_terms'];
?>

<!--upload invoice modal-->
<div class="modal fade" id="uploaderModal" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="text-center">Upload Bills</h3>
            </div>
            <div class="modal-body">
                <!--steps-->
                <div style="text-align:center;margin-bottom:20px; margin-top:10px;">
                    <span class="step step-1"></span>
                    <span class="step step-2"></span>
                    <span class="step step-3"></span>
                </div>

                <div class="form-steps form-step-1">
                    <div id="upload-form" style="padding-left:20px;padding-right:20px;padding-bottom:20px;">
                        <form id="UploadPrintCheckForm" method="post">
                            <label>Please upload our carrier bill here to utilize our advance carrier payout system</label>
                            <?php
                                $ageThreshold = $this->daffny->DB->query('SELECT carrier_invoice_terms FROM app_defaultsettings WHERE owner_id =' . $_SESSION['member']['parent_id']);
                                $ageThreshold = mysqli_fetch_assoc($ageThreshold)['carrier_invoice_terms'];
                            ?>
                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <div class="new_form-group">
                                        <label for="date_received_carrier"><span class="required">*</span>Order ID:</label>
                                        <input tabindex="1" name="OrderID" type="text" class="form-control" id="OrderID" onblur="getInvoiceData(this.value, 1)">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="new_form-group">
                                        <label for="date_received_carrier"><span class="required">*</span>Entity ID:</label>
                                        <input tabindex="2" name="EntityID" type="text" class="form-control" id="EntityID" onblur="getInvoiceData(this.value, 1)">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="new_form-group">
                                        <label for="date_received_carrier"><span class="required">*</span>Account:</label>
                                        <input tabindex="1" name="Account" type="text" class="form-control" id="Account">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="new_form-group">
                                        <label for="date_received_carrier"><span class="required">*</span>Payment Type:</label>
                                        <select class="form-control" name="PaymentType" id="PaymentType" onchange="check_ach(this)">
                                            <option value="24">Billing - ACH</option>
                                            <option value="13">Billing - Check</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="new_form-group">
                                        <label><span class="required">*</span>Upload Date:</label>
                                        <input name="UploadDate" type="text" class="form-control" id="UploadDate" value="<?php echo date('m/d/Y'); ?>">
                                        <script>
                                            $(function() {$( "#UploadDate" ).datepicker({dateFormat: "mm/dd/yy"})});
                                        </script>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="ach-processing col-12 col-sm-6">
                                    <div class="new_form-group">
                                        <?php
                                            $dc_option = array(
                                                "Select One",
                                                "No Fees",
                                                "3% processing Fee + $12 ACH Fee",
                                                "5% processing Fee + $12 ACH Fee",
                                                "3% processing Fee + No ACH Fee",
                                                "5% processing Fee + No ACH Fee"
                                            );
                                        ?>
                                        <label><span class="required">*</span>Fee Type:</label>
                                        <select class="form-control" name="FeesType" onchange="calculate_processing_fees(this, 'ProcessingFees', 'Amount')" id="FeesType">
                                            <?php
                                                foreach($dc_option as $k => $v){
                                            ?>
                                                <option value="<?php echo $k;?>"><?php echo $v;?></option>
                                            <?php
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="ach-processing col-12 col-sm-6">
                                    <div class="new_form-group">
                                        <label><span class="required">*</span>Processing Fees:</label>
                                        <input name="ProcessingFees" type="text" class="form-control" id="ProcessingFees" value="0" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <div class="new_form-group">
                                        <label><span class="required">*</span>Amount:</label>
                                        <input type="text" class="form-control" name="Amount" id="Amount" value="<?php echo $this->carrierRemains; ?>" required/>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="new_form-group">
                                        <label><span class="required">*</span>Age:</label>
                                        <input type="text" class="form-control" id="Age" name="Age" value="<?php echo $ageThreshold; ?>"/>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-12 text-right">
                                    <div class="new_form-group">
                                    <button type="button" onclick="cancelUploaded(true)" class="btn btn-sm btn-danger">Cancel</button>
                                        <button type="button" id="addInfoBtn" onclick="addInfo()" class="btn btn-sm btn_dark_green">Next</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <br/>
                    </div>
                </div>

                <div class="form-steps form-step-2">
                    <div style="padding:20px;">
                        <em><strong> Allowed Files: pdf, doc, docx, xls, xlsx, jpg, jpeg, png, tiff, wpd.</strong>
                        </em>
                        <p>Upload your document(s) by dropping your files into the box below.</p>
                        <div action="#" id="dropzdoc" class="dropzone"></div>
                        <input type="hidden" name="invoiceID" id="uploadedInvoiceID" value="0">
                        <div id="uploadedVideos"></div>
                        <script>
                            Dropzone.autoDiscover = false;
                            let docCount = 0;
                            var myDropzone = new Dropzone("#dropzdoc",{
                                url: '<?php echo getLink("bills", "upload_file", "id")?>'+"/"+$("#uploadedInvoiceID").val(),
                                acceptedFiles: ".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.tiff,.wpd",
                                createImageThumbnails:true
                            });

                            myDropzone.on("error", function(file, response) {
                                alert('Invalid file extension.\nAllowed file extensions: pdf, doc, docx, xls, xlsx, jpg, jpeg, png, tiff, wpd');
                            });

                            myDropzone.on("processing", function(file, progress) {
                                $('#upload_process').fadeIn();
                                $('#nodocs').hide();
                            });

                            myDropzone.on("success", function(file,response) {
                                let r = JSON.parse(response);
                                $('#upload_process').fadeOut();
                                docCount++;
                                let html = "<a href='/uploads/Bills/"+r.file+"' target='_blank'>Document "+docCount+"</a>";
                                $('#uploadedVideos').append(html);
                            });


                            myDropzone.on("addedfile", function(file) {
                                if (this.files.length) {
                                    var _i, _len;
                                    for (_i = 0, _len = this.files.length; _i < _len - 1; _i++) // -1 to exclude current file
                                    {
                                        if(this.files[_i].name === file.name)
                                        {
                                            this.removeFile(file); 
                                        }
                                    }
                                }
                                $('.dz-preview').css('display','none');
                                $('.dz-default').css('display','block');
                            });
                        </script>

                        <div class="row">
                            <div class="col-12 col-sm-12 text-right">
                                <div class="new_form-group">
                                    <br/>
                                    <button type="button" onclick="cancelUploaded(false)" class="btn btn-sm btn-danger">Previous</button>
                                    <button type="button" class="btn btn-sm btn_dark_green" onclick="previewBill()">Next</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-steps form-step-3">
                    <center id="billPreviewPanel"></center>
                    <center>
                        <br>
                            <button type="button" onclick="cancelUploaded(false)" class="btn btn-sm btn-danger">Cancel</button> 
                            <button type="button" class="btn btn-sm btn_dark_green" onclick="closeUploaderRefresh()">Done</button> 
                        <br><br>
                    </center>
                </div>
                <!---->
            </div>
        </div>
    </div>
</div>

<!--edit invoice modal-->
<div class="modal fade" id="editInvoicePopup" role="dialog">
    <div class="modal-dialog modal-dialog-centered  modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Upload Invoice</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fa fa-times" aria-hidden="true"></i></button>
            </div>
            <div class="modal-body">
                <!--steps-->
                <div style="text-align:center;margin-bottom:20px; margin-top:10px;">
                    <span class="step step-1"></span>
                    <span class="step step-2"></span>
                    <span class="step step-3"></span>
                </div>

                <div class="form-steps form-step-1">
                    <div id="edit-form" style="padding-left:20px;padding-right:20px;padding-bottom:20px;">
                        <form id="UpdateCarrierInvoiceValue" method="post">
                            <label>Please upload our carrier bill here to utilize our advance carrier payout system</label>
                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <div class="new_form-group">
                                        <label for="date_received_carrier"><span class="required">*</span>Carrier Name:</label>
                                        <input type="hidden" id="UpdateInvoiceID" name="UpdateInvoiceID" value="">
                                        <input type="hidden" id="UpdateInvoiceEntityID" name="UpdateInvoiceEntityID" value="">
                                        <input tabindex="1" name="CarInvoiceName" type="text" class="form-control" id="CarInvoiceName" required>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="new_form-group">
                                        <label for="date_received_carrier"><span class="required">*</span>Payment Type:</label>
                                        <select class="form-control" name="EditCarPayType" id="EditCarPayType" onchange="check_ach(this)">
                                            <option value="24">Billing - ACH</option>
                                            <option value="13">Billing - Check</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="ach-processing col-12 col-sm-6">
                                    <div class="new_form-group">
                                        <?php
                                            $dc_option = array(
                                                "Select One",
                                                "No Fees",
                                                "3% processing Fee + $12 ACH Fee",
                                                "5% processing Fee + $12 ACH Fee",
                                                "3% processing Fee + No ACH Fee",
                                                "5% processing Fee + No ACH Fee"
                                            );
                                        ?>
                                        <label><span class="required">*</span>Fee Type:</label>
                                        <select class="form-box-combobox edit" tabindex="7" name="FeesType" onchange="calculate_processing_fees(this, 'EditProcessingFees','CarInvoiceAmount')" id="EditFeesType">
                                            <?php
                                                foreach($dc_option as $k => $v){
                                            ?>
                                                <option value="<?php echo $k;?>"><?php echo $v;?></option>
                                            <?php
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="ach-processing col-12 col-sm-6">
                                    <div class="new_form-group">
                                        <label><span class="required">*</span>Processing Fees:</label>
                                        <input name="ProcessingFees" type="text" class="form-control" id="EditProcessingFees" value="0" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <div class="new_form-group">
                                        <label><span class="required">*</span>Amount:</label>
                                        <input type="text" class="form-control" name="CarInvoiceAmount" id="CarInvoiceAmount" value="<?php echo $this->carrierRemains; ?>" required/>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="new_form-group">
                                        <label><span class="required">*</span>Age:</label>
                                        <input type="text" class="form-control" id="CarInvoiceAge" name="CarInvoiceAge" value="<?php echo $ageThreshold; ?>"/>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="new_form-group">
                                        <label><span class="required">*</span>Upload Date:</label>
                                        <input name="CarInvoiceCreated" type="text" class="form-control" id="CarInvoiceCreated" value="<?php echo date('m/d/Y'); ?>">
                                        <script>
                                            $(function() {$( "#CarInvoiceCreated" ).datepicker({dateFormat: "mm/dd/yy"})});
                                        </script>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-12 text-right">
                                    <div class="new_form-group">
                                    <button type="button" data-dismiss="modal" aria-label="Close" class="btn btn-sm btn-danger">Cancel</button>
                                        <button type="button" id="updateInfoBtn" onclick="updateInfo()" class="btn btn-sm btn_dark_green">Next</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <br/>
                    </div>
                </div>

                <div class="form-steps form-step-2">
                    <div style="padding:20px;">
                        <em><strong> Allowed Files: pdf, doc, docx, xls, xlsx, jpg, jpeg, png, tiff, wpd.</strong>
                        </em>
                        <p>Upload your document(s) by dropping your files into the box below.</p>
                        <div action="#" id="editdropzdoc" class="dropzone"></div>
                        <input type="hidden" name="invoiceID" id="editUploadedInvoiceID" value="0">
                        <div id="editUploadedVideos"></div>
                        <script>

                            //Dropzone.autoDiscover = false;
                            
                        </script>

                        <div class="row">
                            <div class="col-12 col-sm-12 text-right">
                                <div class="new_form-group">
                                    <br/>
                                    <button type="button" onclick="cancelUploaded(false)" class="btn btn-sm btn-danger">Previous</button>
                                    <button type="button" class="btn btn-sm btn_dark_green" onclick="previewBillUpdated()">Next</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-steps form-step-3">
                    <center id="billPreviewPanelEdit"></center>
                    <center>
                        <br>
                            <button type="button" class="btn btn-sm btn_dark_green" onclick="closeUploaderRefresh()">Done</button> 
                        <br><br>
                    </center>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="alert alert-light alert-elevate mb-0" role="alert">
	<ul class="nav nav-tabs  nav-tabs-line nav-tabs-line-3x nav-tabs-line-success" role="tablist" style="margin-bottom: 0px">
		<li class="nav-item">
			<a class="nav-link <?= (@$_GET['bills'] == '' || @$_GET['bills'] == 'uploaded') ? "active" : "" ?>" href="<?= SITE_IN ?>application/bills/uploaded/">
                Upoaded Bills (<uploadedCount><?php echo $this->daffny->tpl->count['uploaded'];?></uploadedCount>)
            </a>
		</li>
		<li class="nav-item">
			<a class="nav-link <?= (@$_GET['bills'] == 'pending_checks') ? " active" : "" ?>" href="<?= SITE_IN ?>application/bills/pending_checks/">
                Pending Check Payments (<checkCount><?php echo $this->daffny->tpl->count['pending_check'];?></checkCount>)
            </a>
		</li>
        <li class="nav-item ">
        	<a class="nav-link <?= (@$_GET['bills'] == 'pending_ach') ? " last active" : "" ?>" href="<?= SITE_IN ?>application/bills/pending_ach/">
                Pending Payments ACH (<achCount><?php echo $this->daffny->tpl->count['pending_ach'];?></achCount>)
            </a>
        </li>
        <li class="nav-item ">
        	<a class="nav-link <?= (@$_GET['bills'] == 'on_hold') ? " last active" : "" ?>" href="<?= SITE_IN ?>application/bills/on_hold/">
                Bills on Hold (<holdCount><?php echo $this->daffny->tpl->count['on_hold'];?></holdCount>)
            </a>
        </li>
        <li class="nav-item ">
        	<a class="nav-link <?= (@$_GET['bills'] == 'paid') ? " last active" : "" ?>" href="<?= SITE_IN ?>application/bills/paid/type/cleared_check">
                Paid Bills (<paidCount><?php echo $this->daffny->tpl->count['paid'];?></paidCount>)
            </a>
        </li>
	</ul>
</div>

<script>
    $(document).ready(()=>{
        $(".form-step-1").show();
        $(".step-1").addClass('active');
    });

    let next = (step) => {
        disableTabs();
        $(".form-step-"+step).show();
        $(".step-"+step).addClass('active');
    }

    let previous = (step) => {
        disableTabs();
        $(".form-step-"+step).show();
        $(".step-"+step).addClass('active');
    }

    let disableTabs = () => {
        $(".form-steps").hide();
        $(".step").removeClass('active');
    }

    let closeUploader = (closeModal) => {
        $("#Age").val("");
        $("#billPreviewPanel").html("");
        $('#uploadedVideos').html("");
        previous(1); 
        if(closeModal){
            $("#uploaderModal").modal('hide');
        }
    }

    let closeUploaderRefresh = () => {
        $("#Age").val("");
        $("#billPreviewPanel").html("");
        $('#uploadedVideos').html("");
        location.reload();
    }

    let cancelUploaded = (closeModal) => {

        let uploadedId = Number($("#uploadedInvoiceID").val());
        if(uploadedId != 0 || uploadedId != ""){
            $engine.confirm("Uploaded changes will be lost. Continue?", action => {
                if (action === "confirmed") {
                    closeUploader(closeModal);
                    deleteInvoiceUploaded(uploadedId);
                }
            });
        } else {
            closeUploader(closeModal);
        }
    }

    let deleteInvoiceUploaded = (invoiceId) => {
        $engine.asyncPost(BASE_PATH + "application/bills/deleteUploaded/", {
            invoiceId: invoiceId
        }, (response) => {
            let r = JSON.parse(response);
            if(!r){
                alert("Unable to upload right now");
            }
            $engine.notify("Progress deleted!");
        });
    }

    let informationId = null;
    let addInfo = () => {
        $("#addInfoBtn").attr('disabled',true);
        $engine.asyncPost(BASE_PATH + "application/bills/step1", {
            OrderID: $("#OrderID").val(),
            EntityID: $("#EntityID").val(),
            Account: $("#Account").val(),
            PaymentType: $("#PaymentType").val(),
            UploadDate: $("#UploadDate").val(),
            FeesType: $("#FeesType").val(),
            ProcessingFees: $("#ProcessingFees").val(),
            Amount: $("#Amount").val(),
            Age: $("#Age").val(),
        }, (response) => {
            if(!response.success){
                alert("Unable to upload right now");
            }
            informationId = Number(response.id);
            $("#uploadedInvoiceID").val(informationId);
            myDropzone.options.url = '<?php echo getLink("bills", "upload_file", "id")?>'+"/"+$("#uploadedInvoiceID").val()+"/entity/"+'<?php echo $_GET['id'];?>';
            next(2);
            $("#addInfoBtn").attr('disabled',false);
        });
    }

    let previewBill = () => {
        $engine.asyncPost(BASE_PATH + "application/bills/getInvoiceData/", {
            id: $("#uploadedInvoiceID").val()
        }, (response) => {
            $("#billPreviewPanel").html(response);
            next(3);
        });
    }
</script>

<script>
    upload_invoice = () => {
        $("#UploadInvoiceForm").dialog({
            title: "Upload Invoice",
            width: 400,
            modal: true,
            resizable: false,
            draggable: true,
            buttons: [{
                text: "Cancel",
                click: function () {
                    $("#Account").val("");
                    $("#CarrierName").val("");
                    $("#OrderID").val("");
                    $("#EntityID").val("");
                    $("#AccountID").val("");
                    $("#Amount").val("");
                    $(this).dialog('close');
                }
            }, {
                text: "Upload",
                click: function () {
                    $("#uploadInvoicesForm").nimbleLoader('show');
                    $("#uploadInvoicesForm").submit();
                }
            }]
        }).dialog('open');
    }

    getInvoiceData = (ref, searchFrom) => {
        let searchValue = ref;

        $.ajax({
            type: "POST",
            url: BASE_PATH + 'application/ajax/entities.php',
            data: {
                action: 'GetInvoiceData',
                searchValue: searchValue,
                searchFrom: searchFrom
            },
            dataType: "json",
            success: function (response) {
                if(response.Exists == 1){
                    if(confirm("Bill already added for this order. Continue?")){
                        $("#Account").val(response.AccountName);
                        $("#CarrierName").val(response.CarrierName);
                        $("#OrderID").val(response.OrderID);
                        $("#EntityID").val(response.EntityID);
                        $("#AccountID").val(response.AccountID);
                        $("#Amount").val(response.Amount);
                    } else {
                        return false;
                    }
                } else {
                    $("#Account").val(response.AccountName);
                    $("#CarrierName").val(response.CarrierName);
                    $("#OrderID").val(response.OrderID);
                    $("#EntityID").val(response.EntityID);
                    $("#AccountID").val(response.AccountID);
                    $("#Amount").val(response.Amount);    
                }
            }
        });
    }

    edit_invoice = (id) => {
        $.ajax({
            type: "POST",
            url: BASE_PATH + 'application/ajax/entities.php',
            data: {
                action: 'GetInvoiceDataByID',
                ID: id
            },
            dataType: "json",
            success: function (response) {
                $(".edit").val("");

                // setting value
                $("#CarInvoiceName").val(response.InvoiceData[0].CarrierName);
                $("#CarInvoiceAmount").val(response.InvoiceData[0].Amount);
                $("#EditCarPayType").val(response.InvoiceData[0].PaymentType);

                if(response.InvoiceData[0].PaymentType == 24){
                    $(".ach-processing").show();
                    $("#EditProcessingFees").val(response.InvoiceData[0].ProcessingFees);
                    $("#EditFeesType").val(response.InvoiceData[0].FeesType);
                } else {
                    $(".ach-processing").hide();
                }
                
                $("#CarInvoiceAge").val(response.InvoiceData[0].Age);
                $("#CarInvoiceCreated").val(response.InvoiceData[0].CreatedAt);
                $("#InvoiceDocPreview").attr("href", window.location.origin + "/uploads/Invoices/" + response.InvoiceData[0].Invoice);
                $("#UpdateInvoiceID").val(response.InvoiceData[0].ID);
                $("#UpdateInvoiceEntityID").val(response.InvoiceData[0].EntityID);

                if (response.success == true) {
                    $("#editInvoicePopup").modal("show");
                } else {
                    alert("Unable to edit, Please try again later!");
                }
            }
        });
    }

    check_ach = (ref) => {
        if(ref.value == 24){
            $(".ach-processing").show();
        } else {
            $(".ach-processing").hide();
        }
    }

    calculate_processing_fees = (ref, targetRef, amtRef) => {
        
        if($("#"+amtRef).val() == 0 || $("#"+amtRef).val() == ""){
            alert("Enter Invoice Amount");
            return false;
        }

        let feeType = ref.value;
        let amount = 0;
        
        if(feeType == 0){
            amount = 0;
        }

        if(feeType == 1){
            amount = 0;
        }

        if(feeType == 2){
            amount = (Number($("#"+amtRef).val()) * 0.03) + 12;
        }

        if(feeType == 3){
            amount = (Number($("#"+amtRef).val()) * 0.03) + 0;
        }

        if(feeType == 4){
            amount = (Number($("#"+amtRef).val()) * 0.05) + 12;
        }

        if(feeType == 5){
            amount = (Number($("#"+amtRef).val()) * 0.05) + 0;
        }

        $("#"+targetRef).val(amount.toFixed(2));
    }

    $(document).ready(()=>{
        $("#start_date, #end_date").datepicker({
            dateFormat: 'mm/dd/yy'
        });
    });

    updateInfo = () => {
        $engine.asyncPost(BASE_PATH + "application/bills/save_step1", {
            UpdateInvoiceID: $("#UpdateInvoiceID").val(),
            CarInvoiceName: $("#CarInvoiceName").val(),
            CarInvoiceAmount: $("#CarInvoiceAmount").val(),
            CarPayType: $("#EditCarPayType").val(),
            ProcessingFees: $("#EditProcessingFees").val(),
            FeesType: $("#EditFeesType").val(),
            CarInvoiceAge: $("#CarInvoiceAge").val(),
            CarInvoiceCreated: $("#CarInvoiceCreated").val(),
        }, (response) => {
            if(!response.success){
                alert("Unable to upload right now");
            }

            if(response.success){
                let docCount = 0;
                var myDropzone2 = new Dropzone("#editdropzdoc",{
                    acceptedFiles: ".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.tiff,.wpd",
                    createImageThumbnails:true
                });

                myDropzone2.on("error", function(file, response) {
                    alert('Invalid file extension.\nAllowed file extensions: pdf, doc, docx, xls, xlsx, jpg, jpeg, png, tiff, wpd');
                });

                myDropzone2.on("success", function(file,response) {
                    let r = JSON.parse(response);
                    $('#upload_process').fadeOut();
                    docCount++;
                    let html = "<preview id='upload-"+r.id+"'><a href='/uploads/Bills/"+r.file+"' target='_blank'>Document "+docCount+"</a> <i style='color:red;' onclick='deleteUploaded("+r.id+")'>Remove</i> </preiew>";
                    $('#editUploadedVideos').append(html);
                });


                myDropzone2.on("addedfile", function(file) {
                    if (this.files.length) {
                        var _i, _len;
                        for (_i = 0, _len = this.files.length; _i < _len - 1; _i++) // -1 to exclude current file
                        {
                            if(this.files[_i].name === file.name)
                            {
                                this.removeFile(file); 
                            }
                        }
                    }
                    $('.dz-preview').css('display','none');
                    $('.dz-default').css('display','block');
                });
                
                myDropzone2.options.url = '<?php echo getLink("bills", "upload_file", "id")?>'+"/"+$("#UpdateInvoiceID").val()+"/entity/"+'<?php echo $_GET['id'];?>';
                next(2);
            }
        });
    }

    let deleteUploaded = (uploadId) => {
        $engine.asyncPost(BASE_PATH + "application/bills/deleteUploadedFiles", {
            id: uploadId,
        }, (response) => {
            if(!response){
                alert("Unable to upload right now");
            }

            $("#upload-uploadId").remove();
        });
    }

    let previewBillUpdated = () => {
        $engine.asyncPost(BASE_PATH + "application/bills/getInvoiceData/", {
            id: $("#UpdateInvoiceID").val()
        }, (response) => {
            $("#billPreviewPanelEdit").html(response);
            next(3);
        });
    }
</script>