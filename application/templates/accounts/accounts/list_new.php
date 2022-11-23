<style type="text/css">
    .btn-group.action-buttons i {
    padding: 4px;
}
</style>
<? include(TPL_PATH."accounts/accounts/menu.php"); ?>
<?php
$shipperAccess = 0;
if ($_SESSION['member']['access_shippers'] == 1 ||
        $_SESSION['member']['parent_id'] == $_SESSION['member_id']) {
    $shipperAccess = 1;
}
?>

<h3>Accounts</h3>
<div class="row">
    <div class="col-6">
        <?= formBoxStart() ?>
        <form method="post">
                <div class="row mb-4">
                <div class="col-8">
                @searchval@
                </div>
                <div class="col-4">
                    <div class="input-group-append">
                    <?= submitButtons("", "Search", " submit_button", "submit") ?>
                    </div>
                </div>
                </div>
        </form>

        <?= formBoxEnd() ?>
    </div>
	
	<div class="col-6 text-right">
		<span class="green import-toggle" style="font-size: 1.2em;font-weight: bold">Import <?php echo ucfirst($_GET['accounts']) ?></span>
	</div>
	
</div>


<script language="javascript" type="text/javascript">
    function filterAll() {
        var shipper_type = document.getElementById('shipper_type').value;
        document.form_search.action = document.form_search.action + '/shipper_type/' + shipper_type;
        document.form_search.submit();
    }
    function salesAll() {
        var salesman = document.getElementById('salesman').value;
        document.form_sales.action = document.form_sales.action + '/salesman/' + salesman;
        document.form_sales.submit();
    }


</script>

	
	<?php if (in_array($_GET['accounts'], array('carriers', 'locations', 'shippers')) && $_SESSION['member']['parent_id'] == $_SESSION['member_id']) { ?>
	<?= formBoxStart() ?>
	<div class="import-hidden">
	
		<div style="height:0px;" class="clear"></div>
		
		<div class="kt-portlet">
		
			<div class="kt-portlet__head">
				<div class="kt-portlet__head-label">
					<h3 class="kt-portlet__head-title">Import Shippers</h3>
				</div>
			</div>
			
			<div class="kt-portlet__body">
				<form method="post" enctype="multipart/form-data">
				
					<span class="kt-section__info">Allowed formats: XLS, XLSX, CSV (double quoted enclosure)</span>&nbsp;
					<a href="<?php echo SITE_IN ?>data/<?php echo $_GET['accounts'] ?>_import.xlsx">Download Sample</a>
					
					<div style="width:345px;" class="import-hidden">&#8203;</div>
					
					<div class="row">
						<div class="col-6">
							<input class="form-control" type="file" name="import" id="import" accept="application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,text/csv"/>
						</div>
						<div class="col-6">
							<div class="form-box-buttons" style="text-align:left;">
								<span id="submit_button-submit-btn">
									<input type="submit" id="submit_button" value="Import" class="btn btn-sm btn_dark_green" onclick="return confirm('Are you sure you want to upload data to the system?');">
								</span>
							</div>
						</div>
					</div>
				</form>
				<?php if (count($_FILES)) { ?>
				<div>
					<strong>Import results:</strong><br/>
					Success: @success@<br/>
					Failed: @failed@<br/>
				</div>							
				<?php } ?>
				
				<div style="height:20px;" class="clear"></div>
				
				<div class="alert alert-warning">
					<strong>ATTENTION : </strong> Please ensure the data you are uploading is correct before importing into your database. Please Follow the template provided in the "Download Sample" link.
				</div>
				
				<div class="text-right">
					<button class="btn btn-sm btn-dark import-toggle">Close</button>
				</div>
				
			</div>					
		</div>
	</div>
	<?= formBoxEnd() ?>
	<?php } ?>



	
<div class="kt-portlet">
<div  class="kt-portlet__body">
<div class="row">
 <div class="col-12">



<?php if ($_SESSION['member']['parent_id'] == $_SESSION['member_id']) { ?>
    
    <div class="row">
        <div class="col-6 mb-4 mt-4 ">
            Below is a list of accounts you've saved. Click the ID of any account to view or edit details.
        </div>
        <div class="col-6 text-right mb-4 mt-2">
            <div>
                <img style="vertical-align:middle;" src="<?= SITE_IN ?>images/icons/add.png" alt="Add" width="16" height="16" /> &nbsp;<a href="<?= getLink("accounts", "edit") ?>">Add New Account</a>
                <img style="vertical-align:middle;" src="<?= SITE_IN ?>images/icons/import.png" alt="Add" width="16" height="16" /> &nbsp;<a href="<?= getLink("accounts", "import") ?>">Import Carrier</a>
            </div>
        </div>
    </div>

<?php } ?>
<?php
if ($this->accountType == "shipper") {

$shipper_type_arr = explode("/shipper_type/",$_SERVER['REQUEST_URI']);
$sales_type_arr = explode("/salesman/",$_SERVER['REQUEST_URI']);


?>
   

      <div class="row">
        <div class="col-6 ">
            <div class="row">
            <div class="col-6 mb-4">
            <div >
                <form name="form_search" id="form_search" action="<?php print $shipper_type_arr[0];?>" method="post">@shipper_type@ </form>
            </div>

            </div>
            <div class="col-6 mt-4 ">
        <?php if($_SESSION['member']['parent_id']==$_SESSION['member_id']){?>
        <div class="mt-1">
        <form name="form_sales" id="form_sales" action="<?php print $sales_type_arr[0];?>" method="post"><select name="salesman" id="salesman" class="form-box-combobox" style="" onchange="salesAll();">
        <option value="all" >Filter By Salesman</option>

        <?php foreach ($this->company_members as $member) : ?>
        <option value="<?= $member->id ?>"
        <?php if ($_GET['salesman'] == $member->id) {
        print " selected=selected";
        } ?>><?= $member->contactname ?></option>

        <?php endforeach; ?>

        </select>
        </form>
        </div>


        <?php } ?>

            </div>
            </div>
           

        </div>
        <div class="col-6 text-right ">

            <?php
            //print_r($this->accountsActive);
            if (is_array($this->accountsActive) && sizeof($this->accountsActive) > 0) {
            $active = 0;
            $inactive = 0;
            for ($j = 0; $j < sizeof($this->accountsActive); $j++) {
            if ($this->accountsActive[$j]['status'] == 1 || $this->accountsActive[$j]['status'] == 2)
                $active = $this->accountsActive[$j]['number'];
            if ($this->accountsActive[$j]['status'] == 0)
                $inactive = $this->accountsActive[$j]['number'];
            }
            if ($active == 0)
            $active = 0;
            if ($inactive == 0)
            $inactive = 0;
            ?>
            <h4 style="color:#3B67A6;"><b>Active</b> <?php print $active; ?> <b>/ Inactive</b> <?php print $inactive; ?>  </b></h4>
            <?php }
            ?>
   
        </div>
    </div>


    <?php } ?>




<table id="Active_722" class="table table-bordered">
    <thead>
    <tr >
        <th class="grid-head-left">Num</th>
        <th><?=$this->order->getTitle("A.company_name", "Company Name")?></th>
        
        <?php if($this->accountType == "shipper"){?>
          <th><?=$this->order->getTitle("A.first_name", "First Name")?></th>
          <th><?=$this->order->getTitle("A.last_name", "Last Name")?></th>
          <th><?=$this->order->getTitle("A.referred_by", "Referred By")?></th>
          
        <?php }else{ ?>
        <th><?=$this->order->getTitle("A.contact_name1", "Contact")?></th>
        <?php }?>
        <th><?=$this->order->getTitle("M.contactname", "SalesMan")?></th>
        <th><?=$this->order->getTitle("A.status", "Active/Inactive")?></th>
        
        <th>Phone/Email</th>
        <th>Type</th>
        <th><?=$this->order->getTitle("A.last_order_date", "Last Order Date")?></th>
        <th>Actions</th>
    </tr>
    </thead>
    <?php
        $pageNum = isset($_GET['page']) ? $_GET['page'] : 1;
        $startNum = $_SESSION['per_page'] * ($pageNum - 1);
    ;
	
	?>

    <? 
    $accountData = array();
    //print "--".$this->accountType;
    if($this->accountType == "shipper"){
    /*print "----<pre>";
    print_r($this->accounts);
    print "</pre>";
    */
    $accountData = $this->accounts;
    }
    else
    $accountData = $this->accounts;

    if (count($accountData)>0){?>
    <? foreach ($accountData as $i => $account) { 



    if($this->accountType == "shipper"){
    $accountObj = $account['accounts'];
    // print_r($account);
    $AssignedName = $account['contactname'];
    $Orders = $account['Orders'];
    }
    else
    $accountObj = $account;
    //print $accountObj->status;
    $bgcolor = "";
    if($this->accountType == "shipper"){
    if($accountObj->status ==0)
    $bgcolor = "#ffffff";
    //$bgcolor = "#FF3333";
    }

    ?>
    <tr class="grid-body<?= ($i == 0 ? " first-row" : "") ?><?= ((!is_null($accountObj->insurance_expirationdate) && (strtotime($accountObj->insurance_expirationdate) < time())) ? ' highlight-red' : '') ?>" id="row-<?= $accountObj->id ?>">
        <td class="grid-body-left" bgcolor="<?php print $bgcolor; ?>"><a href="<?= getLink("accounts", "details", "id", $accountObj->id); ?>"><?= $startNum + $i + 1 ?></a></td>
        <td bgcolor="<?php print $bgcolor; ?>">
<?php if ($this->accountType == "shipper") { ?><a href="<?= getLink("accounts", "details", "id", $accountObj->id) ?>"><?= htmlspecialchars($accountObj->company_name); ?></a>
<?php } else { ?>
                <?= htmlspecialchars($accountObj->company_name); ?>
            <?php } ?>
            <!--br /-->
            <?php //print statusText(getLink("accounts", "status", "id", $accountObj->id), Account::$status_name[$accountObj->status]);?>
        </td>
            <?php if ($this->accountType == "shipper") { ?>
            <td bgcolor="<?php print $bgcolor; ?>"><?= htmlspecialchars($accountObj->first_name); ?></td>
            <td bgcolor="<?php print $bgcolor; ?>"><?= htmlspecialchars($accountObj->last_name); ?></td>
            <td bgcolor="<?php print $bgcolor; ?>"><?= htmlspecialchars($accountObj->referred_by); ?></td>

    <?php } else { ?>
            <td bgcolor="<?php print $bgcolor; ?>"><?= htmlspecialchars($accountObj->contact_name1); ?></td>
        <?php } ?>

        <td bgcolor="<?php print $bgcolor; ?>"><?= htmlspecialchars($Orders); ?></td>
        <td bgcolor="<?php print $bgcolor; ?>">
<?php //print $accountObj->status==1?"Active":"Inactive"; ?>
<?php print statusText(getLink("accounts", "status", "id", $accountObj->id), Account::$status_name[$accountObj->status]); ?>
        </td>
        <td bgcolor="<?php print $bgcolor; ?>"><?= htmlspecialchars($accountObj->phone1); ?><br /><a href="mailto:<?= $accountObj->email ?>"  TITLE="<?= $accountObj->email ?>"><?= htmlspecialchars($accountObj->email); ?></a></td>
        <td bgcolor="<?php print $bgcolor; ?>"><?= htmlspecialchars($accountObj->type); ?></td>
        <td bgcolor="<?php print $bgcolor; ?>"><?= date("m/d/y", strtotime($accountObj->last_order_date)); ?></td>
        <td  bgcolor="<?php print $bgcolor; ?>">
            <div class="btn-group action-buttons">
            <?= infoIcon(getLink("accounts", "details", "id", $accountObj->id)) ?>

                <?php

                if ($_SESSION['member']['access_shippers'] == 3 || 
                    ($accountObj->owner_id == $_SESSION['member']['id'] && 
                    in_array($_SESSION['member']['access_shippers'], [2, 1]))) {
                //print editIcon(getLink("accounts", "edit", "id", $accountObj->id));
                ?>
                <?= editIcon(getLink("accounts", "edit", "id", $accountObj->id)) ?>
                <?= deleteIcon(getLink("accounts", "delete", "id", $accountObj->id), "row-" . $accountObj->id) ?> </div> </td>
                <?php } else { ?>
                
                <?php } ?>

            
                <? } ?>
                <? }else{?>
                <tr class="grid-body first-row" id="row-" >
                <td align="center" colspan="9">No records found.</td>
                </tr>
                <? } ?>
            
      </tr>
    


</table>


</div>
</div>
</div>
</div>
@pager@

  <script type="text/javascript">
    $(document).ready(function() {
    $('#Active_722').DataTable({
    "lengthChange": false,
    "paging": false,
    "bInfo" : false,
    'drawCallback': function (oSettings) {

    $("#Active_722_wrapper").children('.row:first').children('.col-sm-12:first').html('<div class="form-group row"><label class="col-form-label">Show: </label><select class=" records_per_page form-box-combobox form-control " style="width:100px;" onchange="setPagerLimit(this.value)"> <option value="10">10</option> <option value="25">25</option> <option value="50" selected="selected">50</option> <option value="100">100</option> <option value="200">200</option> <option value="500">500</option> </select></div>');
    $("#Active_722_wrapper").children('.row:nth-child(3)').children('.col-sm-12:first').html($('.pager').clone());
    $("#Active_722_wrapper").children('.row:nth-child(3)').children('.col-sm-12:last').html($('.table_b'));
    $('.pages_div').remove();

    }
    });
    } );
    </script>