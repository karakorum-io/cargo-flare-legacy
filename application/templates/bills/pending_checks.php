<? include(TPL_PATH . "bills/menu.php"); ?>
<br/>
<br/>
<!--form starts-->
<div class="row">
    <div class="col-12 col-sm-4">
		<div class="alert-light p-4">
			<form action="<?= getLink("bills", "pending_checks") ?>" method="post">
				<div class="form-group">
					@search_bill@
				</div>
				<div class="form-group">
					<lable for="time_period"><input type="radio" name="ptype" value="1" id="ptype1" @ptype1ch@ /> Time Period:</lable>
					@time_period@
				</div>
				<div class="form-group">
					<lable for="start_date"><input type="radio" name="ptype" value="2" id="ptype2" @ptype2ch@ /> Date Range:</lable>
					<div class="row">
						<div class="col-6 col-sm-6">
							@start_date@
						</div>
						<div class="col-6 col-sm-6">
							@end_date@
						</div>
					</div>
				</div>
				<div class="form-group">
					<div class="row">
						<div class="col-6 col-sm-6">
							<lable for="time_period">Order By:</lable>
							<select name="order_by_field" class="form-box-combobox" id="order_by_field">
								<option value="ID">Select Order By</option>
								<option value="ID" <?php echo $_SESSION['order_by_field'] == "ID" ? "selected" : ""?>>ID</option>
								<option value="OrderID" <?php echo $_SESSION['order_by_field'] == "OrderID" ? "selected" : ""?>>OrderID</option>
								<option value="CarrierID" <?php echo $_SESSION['order_by_field'] == "CarrierID" ? "selected" : ""?>>CarrierID</option>
								<option value="CarrierName" <?php echo $_SESSION['order_by_field'] == "CarrierName" ? "selected" : ""?>>Carrier</option>
								<option value="Amount" <?php echo $_SESSION['order_by_field'] == "Amount" ? "selected" : ""?>>Amount</option>
								<option value="CheckID" <?php echo $_SESSION['order_by_field'] == "CheckID" ? "selected" : ""?>>Check Number</option>
								<option value="Term" <?php echo $_SESSION['order_by_field'] == "Term" ? "selected" : ""?>>Term</option>
								<option value="PaidDate" <?php echo $_SESSION['order_by_field'] == "PaidDate" ? "selected" : ""?>>Paid On</option>
								<option value="CreatedAt" <?php echo $_SESSION['order_by_field'] == "CreatedAt" ? "selected" : ""?>>Uploaded On</option>
							</select>
						</div>
						<div class="col-6 col-sm-6 text-center">
							<br/><br/>
							<input type="radio" name="asc_desc" value="ASC" id="order_asc" @order_asc@/> ASC
							&nbsp;&nbsp;
							<input type="radio" name="asc_desc" value="DESC" id="order_desc" @order_desc@/> DESC
							&nbsp;&nbsp;
							<input type="radio" name="asc_desc" value="NONE" id="order_none" @order_none@/> NONE
						</div>
					</div>
				</div>
				<div class="form-group text-right">
					<button type="submit" class="btn btn-primary">Get Bills</button>
				</div>
			</form>
		</div>
    </div>
    <div class="col-12 col-sm-8">
        &nbsp;
    </div>
</div>
<!--form ends-->
<br>
<a id="select_unselect" onclick="select_all(0);" style="color:#008ec2; cursor:pointer;">Select All</a>
@pager@
<div id="" style="overflow: auto;">
<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <td>Order ID</td>
            <td>Uploaded On</td>
            <td>CarrierID</td>
            <td>Carrier</td>
            <td>Amount</td>
            <td>Doc</td>
            <td>Term</td>
            <td>Age</td>
            <td>Expected date of Payment</td>
            <td>Uploader</td>
            <td width="70">Action</td>
        </tr>
    </thead>
    <tbody>
        <?php
            foreach ($this->data as $i => $data) {
        ?>
            <tr>
                <td>
                    <input type="checkbox" name="InvoiceID" selection="not-done" onclick="validateSelection($(this))" class="InvoiceID" value="<?php echo $data['ID']?>">
                    <a href="/application/orders/show/id/<?php echo $data['EntityID']?>" target="_blank">
                        <?php echo $data['OrderID']?>
                    </a>
                </td>
                <td><?php echo date('m-d-Y', strtotime($data['CreatedAt']));?></td>
                <td align="center">
                    <a href="/application/accounts/details/id/<?php echo $data['CarrierID'];?>" target="_blank">
                        <?php echo $data['CarrierID'];?>
                    </a>
                </td>
                <td><?php echo $data['CarrierName'];?></td>
                <td align="right"><?php echo $data['Amount'];?></td>
                <td align="center">
                    <?php
                        if($data['Invoice'] == "In New Table"){
                            $rs = $this->daffny->DB->query("SELECT * FROM invoice_documents WHERE `invoice_id`= " . $data['ID']);
                            while ($r = mysqli_fetch_assoc($rs)) {
                    ?>
                        <a href="/uploads/Bills/<?php echo $r['path']?>" target="_blank">Download</a>
                        <br/>
                    <?php
                            }
                        } else {
                    ?>
                        <a href="/uploads/Invoices/<?php echo $data['Invoice']?>" target="_blank">
                            <?php echo "Download";?>
                        </a>
                    <?php
                        }
                    ?>
                </td>
                <td align="right"><?php echo $data['Term'];?></td>
                <td align="right"><?php echo $data['Age'];?></td>
                <td align="center"><?php echo date('m-d-Y', strtotime($data['MaturityDate']));?></td>
                <td><?php echo $data['UploaderName'];?></td>
                <td align="center">
                    <?php
                        if($data['Hold'] == 0){
                    ?>
                        <a href="/application/bills/hold_unhold/InvoiceID/<?php echo $data['ID'];?>">
                            <img src="/images/icons/on.png" width="16" height="16">
                        </a>
                    <?php
                        } else {
                    ?>
                        <a href="/application/bills/hold_unhold/InvoiceID/<?php echo $data['ID'];?>">
                            <img src="/images/icons/off.png" width="16" height="16">
                        </a>
                    <?php
                        }
                    ?>
                    <img src="/images/icons/edit.png" onclick="edit_invoice('<?php echo $data['ID'];?>')" width="16" height="16">
                    <a href="/application/bills/delete/InvoiceID/<?php echo $data['ID'];?>">
                        <img src="/images/icons/delete.png" width="16" height="16">
                    </a>
                </td>
            </tr>
        <?php
            }
        ?>
    </tbody>
</table>
</div>
@pager@

<div class="row">
    <div class="col-12 col-sm-12 text-right">
        <?php
            $memberId = (int) $_SESSION['member_id'];
            $inp = $this->daffny->DB->selectRow('id,value', 'members_type_value', "WHERE member_id='".$memberId."' and type='QBPRINT'");
        ?>
        <input type="checkbox" name="NotifyCarrierPayment" id="NotifyCarrierPayment">
        <b>Once the payment is recorded CargoFlare will notify carrier via email on file</b><br><br>
        Start Check Sequence: <input type="text" id="startNumber" value="<?php echo ($inp['value']);?>"> &nbsp;&nbsp;
        <button id="" onclick="print_check()" class="btn btn-primary" style="width:150px;">
            Batch Print Check
        </button>
    </div>
</div>
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
                    <button id="printProceedBtn" class="btn btn-primary" onclick="check_empty_ledger()">Proceed</button>
                </div>
            </div>
        </div>
    </div>
</PrintChecks>
<script>
    let print_check = () => {
        let invoiceIDs = [];
        $("input:checkbox[name=InvoiceID]:checked").each(function () {
            invoiceIDs.push($(this).val());
        });

        let popupConfig;

        if (invoiceIDs.length <= 0) {
            $("#printProceedBtn").attr("disabled",true);
            $("#bulkprintwrapper").html("").html("<center><h3>Please select atleast one bill to pay!</h3></center>");
        } else {
            $("#printProceedBtn").removeAttr("disabled");
            $("#bulkprintwrapper").html("").html("<center><h3>" + invoiceIDs.length + " Invoices ready to process, Proceed?</h3></center>");
        }

        open_popup();
    }

    let check_empty_ledger = () => {
        let inVoiceToBeValidated = new Array();
        $.each($("input[name='InvoiceID']:checked"), function () {
            inVoiceToBeValidated.push($(this).val());
        });

        $("PrintProgress").html("Making Sure No Previous Check Payments...");

        $.ajax({
            type: "POST",
            url: BASE_PATH + 'application/ajax/entities.php',
            data: {
                action: 'VALIDATE_EMPTY_LEDGER',
                InvoiceIDs: inVoiceToBeValidated
            },
            dataType: "json",
            success: function (response) {
                if(response.success == true){
                    var presence = response.Presence;
                    if(presence > 0){
                        let progress = presence+ " Check has been cut in the past from the selected checks";
                        progress += "<br><br><button onclick='invoiceManager.closeBulkPrintCheckPopup()'>Cancel</button> &nbsp; &nbsp;";
                        progress += "<button onclick='skip_already_printed()'>Skip Printed</button>";
                        $("PrintProgress").html(progress);
                    } else {
                        let progress = " No previous checks found for selected Orders... Please continue";
                        progress += "<br><br><button class='btn btn-default' onclick='invoiceManager.closeBulkPrintCheckPopup()'>Cancel</button> &nbsp; &nbsp;";
                        progress += "<button class='btn btn-primary' onclick='skip_already_printed()'>Continue</button>";
                        $("PrintProgress").html(progress);
                    }
                }
            }
        });
    }

    let skip_already_printed = () => {

        let inVoiceToBeSkipped = new Array();
        $.each($("input[name='InvoiceID']:checked"), function () {
            inVoiceToBeSkipped.push($(this).val());
        });

        $.ajax({
            type: "POST",
            url: BASE_PATH + 'application/ajax/entities.php',
            data: {
                action: 'REMOVE_PAID_CHECK_ORDERS',
                InvoiceIDs: inVoiceToBeSkipped
            },
            dataType: "json",
            success: function (response) {
                if(response.success == true){
                    let presence = response.toPrint.length;
                    
                    if(presence == 0){
                        let progress = "No New Check to Print";
                        $("PrintProgress").html(progress);
                    } else {
                        generate_check_pdf(response.toPrint);
                    }
                }
            }
        });
    }

    let generate_check_pdf = (InvoiceIDs) => {

        let selectedInvoices = new Array();
        for(let i=0; i<InvoiceIDs.length; i++){
            selectedInvoices.push(InvoiceIDs[i].ID);
        }

        console.log("Invoice That are undergoing PRINT:",selectedInvoices);

        let send_carrier_mail = 0;
        if ($("input:checkbox[name=NotifyCarrierPayment]").prop('checked') == true) {
            send_carrier_mail = 1;
        }

        let printType = $("#printType").val();
        let url = "";
        let data = "";

        if (printType == 0) {
            url = BASE_PATH + 'external/PrintChecks.php';
            data = {
                selectedInvoices: selectedInvoices,
                startNumber: $("#startNumber").val(),
                send_carrier_mail: send_carrier_mail
            }
        } else {
            url = BASE_PATH + 'application/ajax/entities.php';
            data = {
                action: 'GenerateCheckPDFs',
                selectedInvoices: selectedInvoices,
                startNumber: $("#startNumber").val(),
                send_carrier_mail: send_carrier_mail
            }
        }

        if (selectedInvoices.length > 0) {
            $.ajax({
                type: "POST",
                url: url,
                data: data,
                dataType: "json",
                success: function (response) {
                    location.reload();
                    window.open( BASE_PATH + "uploads/Invoices/Checks/" + response.URL);
                }
            });
        } else {
            $("#bulkprintwrapper").html("").html("<center><h3>No Invoice Selected!</h3></center>");
        }
    }

    let open_popup = () => {
        $("#BulkPrintCheckPopup").modal('show');
    }

    let selections = 0;
    let limit = 50;

    let select_all = () => {
        $("input:checkbox[name=InvoiceID]").each(function () {
            
            if(selections >= limit){
                alert("Maximum "+ limit+" allowed to select.");
                $(ref).prop('checked',false);
                return false;
            }

            $(this).attr("checked",true);
            $("#select_unselect").attr("onclick","unselect_all()");
            $("#select_unselect").html("UnSelect All");
            selections++;
        });
    }

    let unselect_all = () => {
        $("input:checkbox[name=InvoiceID]").each(function () {
            $(this).removeAttr("checked");
            $("#select_unselect").attr("onclick","select_all()");
            $("#select_unselect").html("Select All");
            selections = 0;
        });
    }

    let validateSelection = (ref) => {
        
        if(selections >= limit){
            alert("Maximum "+ limit+" allowed to select.");
            $(ref).prop('checked',false);
            return false;
        }

        if($(ref).attr("selection") === "done"){
            $(ref).attr("selection","not-done");
            selections--;
        } else {
            $(ref).attr("selection","done");
            selections++;
        }
    }
</script>