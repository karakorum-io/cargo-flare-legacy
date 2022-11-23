<? include(TPL_PATH . "bills/menu.php"); ?>
<br/>
<br/>
<!--form starts-->
<div class="row">
    <div class="col-12 col-sm-4">
		<div class="alert-light p-4">
			<form action="<?= getLink("bills", "uploaded") ?>" method="post">
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
							<label for="time_period">Order By:</label>
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
				<div class="form-group text-right mb-0">
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
@pager@
<div id="" style="overflow: auto;">
<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <td><?php echo $this->order->getTitle('ID', 'Order ID'); ?></td>
            <td><?php echo $this->order->getTitle('CreatedAt', 'Uploaded On'); ?></td>
            <td>CarrierID</td>
            <td>Carrier</td>
            <td>Amount</td>
            <td>Type</td>
            <td>Doc</td>
            <td>Term</td>
            <td>Age</td>
            <td>Payment Status</td>
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
            <td align="center"><?php echo $data['PaymentType'] == 13 ? "Check Payment":"ACH Payments";?></td>
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
            <td align="center"><?php echo $data['Paid'] == 0 ? "<UnCleared><b>Un-Paid</b></UnCleared>" : "<Cleared><b>Paid</b></Cleared>";?></td>
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