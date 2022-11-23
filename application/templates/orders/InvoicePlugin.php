<!--Javascript Library-->
<script src="/jscripts/InvoiceManager.js"></script>
<script type="text/javascript" src="<?= SITE_IN ?>jscripts/dropzone.js"></script>
<link href="<?= SITE_IN ?>application/assets/css/dropzone.css" type="text/css" rel="stylesheet"/>
<script>
    let invoiceManager = new InvoiceManager();
</script>
<style>
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
<!--HTML UI Starts-->
<InvoicePlugin>
    <InvoiceUploader>
        <div class="row">
            <div class="col-12 col-sm-8">
                <div style="background:#fff;border:1px solid #ebedf2;" class="mb-5">
                    <div id="headingOne" class="hide_show">
                        <div class="card-title">
                            <h3 class="shipper_detail">Carrier's Bill</h3>
                        </div>
                    </div>
                    <div class="modal fade" id="uploaderModal" role="dialog">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h3 class="text-center">Upload Bills</h3>
                                </div>
                                <div class="modal-body">
                                    <!---->
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
                                                            <button type="button" id="addInfoBtn" class="btn btn-sm btn_dark_green">Next</button>
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

                    <div style="padding-left:20px;padding-right:20px;padding-bottom:20px;">
                        <div class="col-12">
                            <ul class="nav nav-tabs nav-tabs-line nav-tabs-line-3x nav-tabs-line-success" role="tablist" style="margin-bottom:0">
                                <li class="nav-item">
                                    <a class="nav-link pay-tab tab-bill-1" onclick="openActiveTab(this,1)">ACH (<?php echo $this->existanceACH;?>)</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link pay-tab tab-bill-2" onclick="openActiveTab(this,2)">Checks (<?php echo $this->existanceCheck;?>)</a>
                                </li>
                            </ul>
                        </div>
                        <div id="Added-Carrier-Ach" class="tab-panes">
                            <br/>
                            <div class="row">
                                <div class="col-8 col-sm-8">
                                    <button class="btn btn-sm btn_dark_green" onclick="print_ach();">Print Receipt(s)</button>
                                </div>
                                <div class="col-4 col-sm-4">
                                    <input type="text" class="form-box-textfield" name="TxnID" id="TxnIDField" onblur="update_txn_id()" placeholder="Update transaction id"/>
                                </div>
                            </div>
                            <br/>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <td>#</td>
                                        <td>Upload On</td>
                                        <td>Carrier</td>
                                        <td>Carrier Name</td>
                                        <td>Carrier Fee</td>
                                        <td>Processing Fee</td>
                                        <td>Actual Fee</td>
                                        <td>Doc</td>
                                        <td>Payment Status</td>
                                        <td>TxnID</td>
                                        <td>Status</td>
                                        <td>Uploader</td>
                                        <td>Action</td>
                                    </tr>
                                </thead>
                                <tbody id="UploadedInvoices">
                                <?php
                                    if(count($this->carrierInvoiceACH) > 0){
                                        foreach ($this->carrierInvoiceACH as $array) {
                                            echo "<tr>";

                                            echo "<td>";
                                            echo '<input type="checkbox" class="InvoiceID" paid="'.($array['Paid']).'" name="InvoiceID" value="' . $array['ID'] . '">&nbsp;';
                                            echo "</td>";

                                            echo "<td>" . date('m/d/Y', strtotime($array['CreatedAt'])) . "</td>";
                                            echo "<td><a target='_blank' href='" . getLink("accounts", "details", "id", $array['CarrierID']) . "'>" . $array['CarrierID'] . "</a></td>";
                                            echo "<td>" . $array['CarrierName'] . "</td>";
                                            echo "<td align='right'>$" . $array['Amount'] . "</td>";
                                            echo "<td align='right'>$" . $array['ProcessingFees'] . "</td>";
                                            echo "<td align='right'>$" . ($array['Amount'] - $array['ProcessingFees']) . "</td>";

                                            if($array['Invoice'] == "In New Table"){
                                                $rs = $this->daffny->DB->query("SELECT * FROM invoice_documents WHERE `invoice_id`= " . $array['ID']);
                                                echo "<td>";
                                                while ($r = mysqli_fetch_assoc($rs)) {
                                                    echo "<a href='" . SITE_IN . "uploads/Bills/" . $r['path'] . "' target='_blank'>Download</a>";
                                                    echo "<br>";
                                                }
                                                echo "</td>";
                                            } else {
                                                echo "<td align='center'><a href='" . SITE_IN . "uploads/Invoices/" . $array['Invoice'] . "' target='_blank'>Download</a></td>";
                                            }
                                            
                                            echo "<td align='center'>" . ($array['Paid'] == 0 ? '<b style="color:red;">Un-Paid</b>' : '<b style="color:green;">Paid</b>') . "</td>";
                                            echo "<td>" . $array['TxnID'] . "</td>";
                                            echo "<td>" . ($array['Hold'] == 0 ? 'Active' : 'On Hold') . "</td>";
                                            echo "<td>" . $array['UploaderName'] . "</td>";
                                            echo "<td align='center'>";
                                            if ($array['Hold'] == 0) {
                                                echo "<a href='" . getLink('orders', 'CarrierInvoiceStatusUpdate', 'id', $_GET['id'], 'InvoiceID', $array['ID']) . "'>";
                                                echo "<img src='/images/icons/on.png' width='16' height='16'>";
                                                echo "</a>";
                                            } else {
                                                echo "<a href='" . getLink('orders', 'CarrierInvoiceStatusUpdate', 'id', $_GET['id'], 'InvoiceID', $array['ID']) . "'>";
                                                echo "<img src='/images/icons/off.png' width='16' height='16'>";
                                                echo "</a>";
                                            }
                                            echo "&nbsp;<img src='/images/icons/edit.png' style='cursor:pointer;' onclick='editPopup(" . $array['ID'] . ")' width='16' height='16'>";
                                            echo "&nbsp;";
                                            echo "<a href='" . getLink('orders', 'DeleteCarrierInvoice', 'id', $_GET['id'], 'InvoiceID', $array['ID']) . "'>";
                                            echo "<img src='/images/icons/delete.png' width='16' height='16'>";
                                            echo "</a>";
                                            echo "</td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='13' align='center'>No bills available.</td></tr>";
                                    }
                                ?>
                                </tbody>
                            </table>
                        </div>
                        <div id="Added-Carrier-Checks" class="tab-panes" style="display:none;">
                            <br><br>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <td>#</td>
                                        <td>Upload On</td>
                                        <td>Carrier</td>
                                        <td>Carrier Name</td>
                                        <td>Amount</td>
                                        <td>Doc</td>
                                        <td>Payment Status</td>
                                        <td>Status</td>
                                        <td>Uploader</td>
                                        <td>Action</td>
                                    </tr>
                                </thead>
                                <tbody id="UploadedInvoices">
                                <?php
                                if(count($this->carrierInvoiceCheck) > 0){
                                    foreach ($this->carrierInvoiceCheck as $array) {
                                        echo "<tr>";

                                        echo "<td>";
                                        if ($array['Paid'] == 0) {
                                            echo '<input type="checkbox" class="InvoiceID" name="InvoiceID" value="' . $array['ID'] . '">&nbsp;';
                                        }
                                        echo "</td>";

                                        echo "<td>" . date('m/d/Y', strtotime($array['CreatedAt'])) . "</td>";
                                        echo "<td><a target='_blank' href='" . getLink("accounts", "details", "id", $array['CarrierID']) . "'>" . $array['CarrierID'] . "</a></td>";
                                        echo "<td>" . $array['CarrierName'] . "</td>";
                                        echo "<td align='right'>$" . $array['Amount'] . "</td>";
                                        
                                        if($array['Invoice'] == "In New Table"){
                                            $rs = $this->daffny->DB->query("SELECT * FROM invoice_documents WHERE `invoice_id`= " . $array['ID']);
                                            echo "<td>";
                                            while ($r = mysqli_fetch_assoc($rs)) {
                                                echo "<a href='" . SITE_IN . "uploads/Bills/" . $r['path'] . "' target='_blank'>Download</a>";
                                                echo "<br>";
                                            }
                                            echo "</td>";
                                        } else {
                                            echo "<td align='center'><a href='" . SITE_IN . "uploads/Invoices/" . $array['Invoice'] . "' target='_blank'>Download</a></td>";
                                        }

                                        echo "<td align='center'>" . ($array['Paid'] == 0 ? '<b style="color:red;">Un-Paid</b>' : '<b style="color:green;">Paid</b>') . "</td>";
                                        echo "<td>" . ($array['Hold'] == 0 ? 'Active' : 'On Hold') . "</td>";
                                        echo "<td>" . $array['UploaderName'] . "</td>";
                                        echo "<td align='center'>";

                                        if ($array['Hold'] == 0) {
                                            echo "<a href='" . getLink('orders', 'CarrierInvoiceStatusUpdate', 'id', $_GET['id'], 'InvoiceID', $array['ID']) . "'>";
                                            echo "<img src='/images/icons/on.png' width='16' height='16'>";
                                            echo "</a>";
                                        } else {
                                            echo "<a href='" . getLink('orders', 'CarrierInvoiceStatusUpdate', 'id', $_GET['id'], 'InvoiceID', $array['ID']) . "'>";
                                            echo "<img src='/images/icons/off.png' width='16' height='16'>";
                                            echo "</a>";
                                        }

                                        echo "<img src='/images/icons/edit.png' style='cursor:pointer;' onclick='editPopup(" . $array['ID'] . ")' width='16' height='16'>";
                                        echo "<a href='" . getLink('orders', 'DeleteCarrierInvoice', 'id', $_GET['id'], 'InvoiceID', $array['ID']) . "'>";
                                        echo "<img src='/images/icons/delete.png' width='16' height='16'>";
                                        echo "</a>";
                                        echo "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='10' align='center'>No bills available.</td></tr>";
                                }
                                ?>
                                </tbody>
                            </table>
                            <div class="row">
                                <?php
                                    $memberId = (int) $_SESSION['member_id'];
                                    $inp = $this->daffny->DB->selectRow('id,value', 'members_type_value', "WHERE member_id='" . $memberId . "' and type='QBPRINT'");
                                ?>
                                <div class="col-6 col-sm-6">
                                    <input type="checkbox" name="NotifyCarrierPayment" id="NotifyCarrierPayment">
                                    <b>Once the payment is recorded Cargoflare will notify carrier via email on file</b>
                                </div>
                                <div class="col-4 col-sm-4">
                                    Start Check Sequence: <input type="text" class="form-control" id="startNumber" value="<?php echo ($inp['value']); ?>">
                                </div>
                                <div class="col-2 col-sm-2 text-right">
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <button onclick="invoiceManager.triggerBatchPrint()" class="btn btn-sm btn_dark_green">Print Check</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr/>
                    <div style="padding-left:20px;padding-right:20px;">
                        <center>
                            <button type="button" class="btn btn-sm btn_dark_green" onclick="openModal()">Upload Invoice</button>
                        </center>
                        <br/>
                    </div>
                </div>
            </div>
        </div>
    </InvoiceUploader>

    <EditInvoice>
        <div class="modal fade" id="editInvoicePopup" role="dialog" aria-labelledby="Upload Invocie Modal" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Upload Invoice</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class="fa fa-times" aria-hidden="true"></i></button>
                    </div>
                    <div class="modal-body">
                        <form id="UpdateCarrierInvoiceValue" action="/application/bills/update_invoice" method="POST" enctype="multipart/form-data">
                            <table align="center">
                                <tr>
                                    <td>Carrier Name</td>
                                    <td>
                                        <input type="hidden" id="UpdateInvoiceID" name="UpdateInvoiceID" value="">
                                        <input type="hidden" id="UpdateInvoiceEntityID" name="UpdateInvoiceEntityID" value="">
                                        <input type="text" name="CarInvoiceName" id="CarInvoiceName" class="form-box-textfield edit" required>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Amount</td>
                                    <td><input type="text" name="CarInvoiceAmount" id="CarInvoiceAmount" class="form-box-textfield edit" required></td>
                                </tr>
                                <tr>
                                    <td>Payment Type</td>
                                    <td>
                                        <select class="form-box-textfield" name="CarPayType" id="EditCarPayType" onchange="check_ach(this)" class="form-box-textfield edit">
                                            <optgroup label="Broker is paying Carrier">
                                                <option value="13">Billing - Check</option>
                                                <option value="24">Billing - ACH</option>
                                            </optgroup>
                                        </select> 
                                    </td>
                                </tr>
                                <tr class="ach-processing" style="display:none;">
                                    <td><label for="Amount">Fee Type* </label></td>
                                    <td>
                                        <select class="form-box-combobox edit" tabindex="7" name="FeesType" onchange="calculate_processing_fees(this, 'EditProcessingFees','CarInvoiceAmount')" id="EditFeesType">
                                            <option value="0" selected="selected">Select One</option>
                                            <option value="1">No Fee</option>
                                            <option value="2">3% processing Fee + $12 ACH Fee</option>
                                            <option value="3">3% processing Fee + No ACH Fee</option>
                                            <option value="4">5% processing Fee + $12 ACH Fee</option>
                                            <option value="5">5% processing Fee + No ACH Fee</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr class="ach-processing" style="display:none;">
                                    <td><label for="Amount">Processing Fees* </label></td>
                                    <td>
                                        <input tabindex="8" name="ProcessingFees" type="text" class="form-box-textfield edit" id="EditProcessingFees" value="0" readonly>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Age</td>
                                    <td><input type="text" name="CarInvoiceAge" id="CarInvoiceAge" class="form-box-textfield edit" required></td>
                                </tr>
                                <tr>
                                    <td>Bill</td>
                                    <td>
                                        <input type="file" name="CarInvoiceDoc" id="CarInvoiceDoc" class="form-box-textfield edit">
                                        <br>
                                        <a target="_blank" style="color:red;" id="InvoiceDocPreview" href="#">Preview</a>
                                        <br><br>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Upload Date</td>
                                    <td>
                                        <input type="text" name="CarInvoiceCreated" id="CarInvoiceCreated" class="form-box-textfield edit hasdate" required>
                                        <script>
                                            $(function() {$( "#CarInvoiceCreated" ).datepicker({dateFormat: "mm/dd/yy"})});
                                        </script>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        &nbsp;
                                    </td>
                                    <td class="text-right">
                                        <button type="button" class="btn btn-dark btn-sm" data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn_light_green btn-sm">Update</button>
                                    </td>
                                </tr>
                            </table>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </EditInvoice>

    <PrintChecks>
        <div class="modal fade" id="BulkPrintCheckPopup" tabindex="-1" role="dialog" aria-labelledby="Bulk print check popup" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="text-center">Print Checks Wizard</h3>
                    </div>
                    <div class="modal-body">
                        <div id="bulkprintwrapper"></div>
                        <div>
                            <br>
                            <center>
                                Print Format: <select id="printType">
                                    <option value="0">Web Print</option>
                                    <!-- <option value="1">PDF Check</option> -->
                                </select>
                                <br><br>
                                <PrintProgress></PrintProgress>
                            </center>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-cancel" data-dismiss="modal">Cancel</button>
                        <button id="printProceedBtn" class="btn btn-primary" onclick="invoiceManager.validateEmptyCheckLedger()">Proceed</button>
                    </div>
                </div>
            </div>
        </div>
    </PrintChecks>

</InvoicePlugin>
<!--HTML UI Ends-->

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js" integrity="sha512-37T7leoNS06R80c8Ulq7cdCDU5MNQBwlYoy1TX/WUsLFC2eYNqtKlV0QjH7r8JpG/S0GUMZwebnVFLPd6SU5yg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/additional-methods.min.js" integrity="sha512-XZEy8UQ9rngkxQVugAdOuBRDmJ5N4vCuNXCh8KlniZgDKTvf7zl75QBtaVG1lEhMFe2a2DuA22nZYY+qsI2/xA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>

    let update_txn_id = () => {
        let invoiceIDSelected = [];

        $("input:checkbox[name=InvoiceID]:checked").each(function () {
            invoiceIDSelected.push($(this).val());
        });

        // when no ID is selected
        if (invoiceIDSelected.length == 0) {
            //alert("Please select atleast one Bill");
            return false;
        }

        // check if trying to update empty txnID
        if( $("#TxnIDField").val() == ""){
            //alert("TxnID cannot be empty");
            return false;
        }

        $.ajax({
            type: "POST",
            url: BASE_PATH + 'application/bills/update_txn_ids',
            data: {
                Invoices: invoiceIDSelected,
                TxnID: $("#TxnIDField").val()
            },
            dataType: "json",
            success: function (response) {
                location.reload();
                alert("Txn ID updated for selected Invoices");
            }
        });
    }

    // manage bill listing tabs
    let openActiveTab = (ref,tabName) => {
        $(".pay-tab").removeClass("active");
        $(ref).addClass("active");

        $(".tab-panes").hide();
        if(tabName == 1){
            $("#Added-Carrier-Ach").show();
        }

        if(tabName == 2){
            $("#Added-Carrier-Checks").show();
        }
    }

    function editPopup(id){
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

                // seeting values
                $("#CarInvoiceName").val(response.InvoiceData[0].CarrierName);
                $("#CarInvoiceAmount").val(response.InvoiceData[0].Amount);
                $("#CarPayType").val(response.InvoiceData[0].PaymentType);

                if(response.InvoiceData[0].PaymentType == 24){
                    $(".ach-processing").show();
                    $("#EditProcessingFees").val(response.InvoiceData[0].ProcessingFees);
                    $("#EditFeesType").val(response.InvoiceData[0].FeesType);
                } else {
                    $(".ach-processing").hide();
                }

                $("#CarInvoiceAge").val(response.InvoiceData[0].Age);
                $("#CarInvoiceCreated").val(response.InvoiceData[0].CreatedAt);
                $("#InvoiceDocPreview").attr("href",window.location.origin+"/uploads/Invoices/"+response.InvoiceData[0].Invoice);
                $("#UpdateInvoiceID").val(response.InvoiceData[0].ID);
                $("#UpdateInvoiceEntityID").val(response.InvoiceData[0].EntityID);

                if(response.success == true){
                    $("#editInvoicePopup").modal('show');
                } else {
                    alert("Unable to edit, Please try again later!");
                }
            }
        });
    }

    function processPrinting(paymentType){
        let invoiceIDs = [];
        $("input:checkbox[name=InvoiceID]:checked").each(function () {
            invoiceIDs.push($(this).val());
        });

        if(invoiceIDs.length > 0){
            validateAndProcess(invoiceIDs, paymentType);
        }
    }

    var existanceCheck = '<?php echo $this->existanceCheck; ?>';
    var existanceACH = '<?php echo $this->existanceACH; ?>';

    $("#addInfoBtn").click(()=>{
        if($("#Amount").val() == "" || $("#Amount").val() == 0){
            $engine.notify("Invalid Amount!");
            return false;
        }

        if($("#Age").val() == "" || $("#Age").val() == 0){
            $engine.notify("Invalid Age!");
            return false;
        }

        if($("#UploadDate").val() == ""){
            $engine.notify("Invalid Upload Date!");
            return false;
        }

        addInfo();
    });

    let informationId = null;
    let addInfo = () => {
        $("#addInfoBtn").attr('disabled',true);
        $engine.asyncPost(BASE_PATH + "application/orders/payments/id/<?php echo $_GET['id']?>", {
            invoiceUpload: true,
            PaymentType: $("#PaymentType").val(),
            UploadDate: $("#UploadDate").val(),
            FeesType: $("#FeesType").val(),
            ProcessingFees: $("#ProcessingFees").val(),
            Amount: $("#Amount").val(),
            Age: $("#Age").val(),
        }, (response) => {
            let r = JSON.parse(response);
            if(!r){
                alert("Unable to upload right now");
            }
            informationId = Number(response);
            $("#uploadedInvoiceID").val(informationId);
            myDropzone.options.url = '<?php echo getLink("bills", "upload_file", "id")?>'+"/"+$("#uploadedInvoiceID").val()+"/entity/"+'<?php echo $_GET['id'];?>';
            next(2);
            $("#addInfoBtn").attr('disabled',false);
        });
    }

    // Makes sures only one request sent to server at a time
    $("#invoiceUpload").click(function (e) {
        if (e.target) {
            var attr = $(this).attr('submitting');
            if (typeof attr !== 'undefined' && attr !== false) {
                $(this).prop('disabled', true);
                $(this).removeAttr("submitting");
                e.preventDefault();
            } else {
                $(this).attr("submitting", "true");
            }
        }
    });

    let previewBill = () => {

        if($("preview").length < 1){
            $engine.notify("Upload atleast one document!");
            return false;
        }

        $engine.asyncPost(BASE_PATH + "application/bills/getInvoiceData/", {
            id: $("#uploadedInvoiceID").val()
        }, (response) => {
            $("#billPreviewPanel").html(response);
            next(3);
        });
    }

    let check_ach = (ref) => {
        if(ref.value == 24){
            $(".ach-processing").show();
        } else {
            $(".ach-processing").hide();
        }
    }

    let calculate_processing_fees = (ref, targetRef, amtRef) => {
        
        if($("#"+amtRef).val() == ""){
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
            amount = (Number($("#"+amtRef).val()) * 0.05) + 12;
        }

        if(feeType == 4){
            amount = (Number($("#"+amtRef).val()) * 0.03) + 0;
        }

        if(feeType == 5){
            amount = (Number($("#"+amtRef).val()) * 0.05) + 0;
        }

        $("#"+targetRef).val(amount.toFixed(2));
    }

    let print_ach = () => {
        let invoiceIDSelected = [];

        $("input:checkbox[name=InvoiceID]:checked").each(function () {
            if($(this).attr("paid") == 0){
                invoiceIDSelected.push($(this).val());
            }
        });

        //when no ID is selected
        if (invoiceIDSelected.length == 0) {
            alert("Please select atleast one Unpaid Order");
            return false;
        }

        $.ajax({
            type: "POST",
            url: BASE_PATH + 'application/bills/print_ach_receipts',
            data: {
                IDs: invoiceIDSelected
            },
            dataType: "json",
            success: function (response) {
                location.reload();
                window.open(BASE_PATH + "uploads/Invoices/ACH/" + response.URL);
            }
        });
    }

    $(document).ready(function(){
        $("#PaymentType").val('<?php echo $this->entity->balance_paid_by;?>');
        check_ach(document.getElementById('PaymentType'));

        if(<?php echo $this->entity->balance_paid_by;?> == 24){
            $("#FeesType").val('<?php echo $this->entity->delivery_credit?>');
            //$('#FeesType option:not(:selected)').attr('disabled', true);
            calculate_processing_fees(document.getElementById('FeesType'), 'ProcessingFees','Amount');
            $(".tab-bill-1").trigger('click');
        } else {
            $(".tab-bill-2").trigger('click');
        }
    });

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
                    deleteInvoiceUploaded(uploadedId);
                    closeUploader(closeModal);
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

    let openModal = () => {
        if(existanceACH > 0 || existanceCheck > 0){

            $engine.confirm("Bill already added for this order. Continue?", action => {
                if (action === "confirmed") {
                    existanceACH = 0;
                    existanceCheck = 0;
                    $("#uploaderModal").modal("show");
                }
            });
        } else {
            $("#uploaderModal").modal("show");
        }
    }
</script> 
<script>
    Dropzone.autoDiscover = false;
    let docCount = 0;
    var myDropzone = new Dropzone("#dropzdoc",{
        url: '<?php echo getLink("bills", "upload_file", "id")?>'+"/"+$("#uploadedInvoiceID").val(),
        acceptedFiles: ".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.tiff,.wpd",
        createImageThumbnails:true,
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
        let html = "<preview id='upload-"+r.id+"'><a href='/uploads/Bills/"+r.file+"' target='_blank'>Document "+docCount+"</a> <i style='color:red;' onclick='deleteUploaded("+r.id+")'>Remove</i> </preiew>";
        //let html = "<a href='/uploads/Bills/"+r.file+"' target='_blank'>Document "+docCount+"</a> ";
        $('#uploadedVideos').append(html);

        mydropzone.disable();
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