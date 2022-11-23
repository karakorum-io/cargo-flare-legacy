<? include(TPL_PATH . "bills/menu.php"); ?>
<br>
<br>
<? include(TPL_PATH . "bills/paid_menu.php"); ?>
<br>
<br/>
<!--form starts-->
<div class="row">
    <div class="col-12 col-sm-4" style="border:1px solid #CCC; padding:30px 30px 0px 30px;">
        <form action="<?php echo getLink("bills", "paid","type","cleared_ach") ?>" method="post">
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
    <div class="col-12 col-sm-8">
        &nbsp;
    </div>
</div>
<!--form ends-->
<div class="row">
    <div class="col-12 col-sm-6">
        <br/>
        <a id="select_unselect" onclick="select_all(0);"  style="color:#008ec2; cursor:pointer;">Select All</a>&nbsp;
        <?php
            if($_GET['view_type'] == 1){
        ?>
        <a href="<?php echo getLink("bills","paid/type/uncleared_check");?>">Show Pagination</a><br><br>
        <?php
            } else {
        ?>
        <a onclick="show_all_data()">Show All</a><br><br>
        <script>
            let show_all_data = () => {
                $("#filter_form").attr('action','<?php echo getLink("bills","paid/type/uncleared_check","view_type/1");?>');
                $("#submit_button").trigger('click');
            }
        </script>
        <?php
            }
        ?>
    </div>
    <div class="col-12 col-sm-6 text-right">
        Total Number of Checks <TotalChecks>0</TotalChecks> &nbsp; Total Carrier Cost <TotalCosts>0</TotalCosts>
        &nbsp;&nbsp;
        <button onclick="unclear_bills()" class="btn btn-danger">UnClear <UnclearButton></UnclearButton></button>
    </div>
</div>
<?php
    if($_GET['view_type'] != 1){
?>
    @pager@
<?php
    }
?>
<div id="" style="overflow: auto; height:400px;">
<table id="invoice-data-table" class="table table-bordered table-stripeds">
    <thead>
        <tr>
            <td>Order ID</td>
            <td>Uploaded On</td>
            <td>CarrierID</td>
            <td>Carrier</td>
            <td>Carrier Fee</td>
            <td>Processing Fee</td>
            <td>Actual Fee</td>
            <td>Doc</td>
            <td>
                <?php
                    $sorting = "";
                    if($_GET['view_type'] == 1){
                        $sorting = 'onclick="sort_by_check_txn()"';
                    }
                ?>
                <a <?php echo $sorting;?>>Transaction ID</a>
            </td>
            <td>Term</td>
            <td>Paid On</td>
            <td>Payment Cleared</td>
            <td>Uploader</td>
        </tr>
    </thead>
    <tbody>
        <?php
            foreach ($this->data as $i => $data) {
        ?>
            <tr>
                <td>
                    <input type="checkbox" onclick="make_selection(this,<?php echo $data['ID']?>,<?php echo $data['Amount'];?>, <?php echo $data['EntityID'];?>, <?php echo $data['PaymentID'];?>)" Amount="<?php echo $data['Amount'];?>" EntityID="<?php echo $data['EntityID'];?>" PaymentID="<?php echo $data['PaymentID'];?>" name="InvoiceID" InvoiceID="<?php echo $data['ID']?>">
                    <a href="/application/orders/show/id/<?php echo $data['EntityID']?>" target="_blank">
                        <?php echo $data['OrderID']?>
                    </a>
                </td>
                <td ><?php echo date('m-d-Y', strtotime($data['CreatedAt']));?></td>
                <td align="center">
                    <a href="/application/accounts/details/id/<?php echo $data['CarrierID'];?>" target="_blank">
                        <?php echo $data['CarrierID'];?>
                    </a>
                </td>
                <td ><?php echo $data['CarrierName'];?></td>
                <td align="right"><?php echo $data['Amount'];?></td>
                <td align="right"><?php echo $data['ProcessingFees'];?></td>
                <td align="right"><?php echo $data['Amount'] - $data['ProcessingFees'];?></td>
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
                <td align="right"><?php echo $data['TxnID'];?></td>
                <td align="right"><?php echo $data['Term'];?></td>
                <td align="center"><?php echo date('m-d-Y', strtotime($data['PaidDate']));?></td>
                <td align="center">
                    <?php echo $data['Clear'] == 0 ? "<uncleared>Un-Cleared</uncleared>" : "<cleared>Cleared</cleared>";?>
                </td>
                <td ><?php echo $data['UploaderName'];?></td>
            </tr>
        <?php
            }
        ?>
    </tbody>
</table>
</div>
<?php
    if($_GET['view_type'] != 1){
?>
    @pager@
<?php
    }
?>