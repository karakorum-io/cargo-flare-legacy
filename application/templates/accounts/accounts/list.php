<?php include(TPL_PATH . "accounts/accounts/menu.php"); ?>

<style type="text/css">
    .action-buttons a, .action-buttons div{
        margin-left: 3px;
        margin-right: 3px;
    }
    .col-6.table_b {
    color: white;
    flex: right;
}
li#allacount_previous {
    display: none;
}li#allacount_next {
    display: none;
}
</style>
<h3>Account</h3>

	<div class="row">
	
		<div class="col-6">
			<?= formBoxStart() ?>
			<form method="post">
				<div class="row">
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
		
		<div class="col-6">		
			<?php if (in_array($_GET['accounts'], array('carriers', 'locations', 'shippers'))) { ?>
			<div class="text-right">
				<span class="green import-toggle" style="font-size: 1.2em;font-weight: bold">Import <?php echo ucfirst($_GET['accounts']) ?></span>
			</div>
			<?php } ?>
		</div>
		
	</div>
		
	
	
	<?= formBoxStart() ?>
	<div class="import-hidden">
	
		<div style="height:20px;" class="clear"></div>
		
		<div class="kt-portlet">
		
			<div class="kt-portlet__head">
				<div class="kt-portlet__head-label">
					<h3 class="kt-portlet__head-title">Import Carriers</h3>
				</div>
			</div>
			
			<div class="kt-portlet__body">
				<form method="post" enctype="multipart/form-data">
				
					<span class="kt-section__info">Allowed formats: XLS, XLSX, CSV (double quoted enclosure)</span>&nbsp;
					<a href="<?php echo SITE_IN ?>data/<?php echo $_GET['accounts'] ?>_import.xlsx">Download Sample</a>
					
					<div style="width:345px;" class="import-hidden">&#8203;</div>
					
					<div class="row">
						<div class="col-6">
							<input type="file" name="import" id="import" class="form-control" accept="application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,text/csv"/>
						</div>
						<div class="col-6">
							<div class="form-box-buttons" style="text-align:left;">
								<span id="submit_button-submit-btn">
									<input type="submit" id="submit_button" value="Import" class="btn btn-sm btn_dark_green" onclick="return confirm('Are you sure you want to upload data to the system?');" />
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
					<strong>ATTENTION : </strong> Please ensure the data you are uploading is correct before importing into your database.
					Please Follow the template provided in the "Download Sample" link.
				</div>
				
				<div class="text-right">
					<button class="btn btn-sm btn-dark import-toggle">Close</button>
				</div>
				
			</div>					
		</div>
	</div>
	<?= formBoxEnd() ?>


<script language="javascript" type="text/javascript">
    function filterAll() {
        var shipper_type = document.getElementById('shipper_type').value;
        document.form_search.action = document.form_search.action + '/shipper_type/' + shipper_type;
        document.form_search.submit();
    }
</script>
<br />
<div class="kt-portlet">

	<div class="kt-portlet__body">
		
		<div class="row">
			<div class="col-6 mb-4 ">
				Below is a list of accounts you've saved. Click the ID of any account to view or edit details.
			</div>
			<div class="col-6 text-right mb-4">
				<div>
					<img style="vertical-align:middle;" src="<?= SITE_IN ?>images/icons/add.png" alt="Add" width="16" height="16" /> &nbsp;<a href="<?= getLink("accounts", "edit") ?>">Add New Account</a>
					<img style="vertical-align:middle;" src="<?= SITE_IN ?>images/icons/import.png" alt="Add" width="16" height="16" /> &nbsp;<a href="<?= getLink("accounts", "import") ?>">Import Carrier</a>

				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-12">
				<?php if ($this->accountType == "shipper") {
					$shipper_type_arr = explode("/shipper_type", $_SERVER['REQUEST_URI']);
					?><br />
					<div style="float:left;">
						<form name="form_search" id="form_search" action="<?php print $shipper_type_arr[0]; ?>" method="post">@shipper_type@ </form>
					</div>
					<div style="float:left; padding-left:20px;">
					<?php
					if (is_array($this->accountsActive) && sizeof($this->accountsActive) > 0) {
						$active = 0;
						$inactive = 0;
						for ($j = 0; $j <= sizeof($this->accountsActive); $j++) {
							if ($this->accountsActive[$j]['status'] == 1)
								$active = $this->accountsActive[$j]['number'];
							if ($this->accountsActive[$j]['status'] == 0)
								$inactive = $this->accountsActive[$j]['number'];
						}
						if ($active == "")
							$active = 0;
						if ($inactive == "")
							$inactive = 0;
					?>
					<h4 style="color:#3B67A6;"><b>Active</b> <?php print $active; ?> <b>/ Inactive</b> <b><?php print $inactive; ?></b></h4>
					<?php } ?>
					</div>
				<?php } ?>



				<table class="table table-bordered" id="allacount">
					<thead>
						<tr>
							<th >Num</th>
							<th><?= $this->order->getTitle("company_name", "Company Name") ?></th>
							<?php if ($this->accountType == "shipper") { ?>
								<th><?= $this->order->getTitle("first_name", "First Name") ?></th>
								<th><?= $this->order->getTitle("last_name", "Last Name") ?></th>
								<th><?= $this->order->getTitle("referred_by", "Referred By") ?></th>
								<th>Salesman</th>
							<?php } else { ?>
								<th><?= $this->order->getTitle("contact_name1", "Contact") ?></th>
								<th>Address</th>
							<?php } ?>

							<th>Phone/Email</th>
							<th>Type</th>
							<th class="grid-head-right">Actions</th>
						</tr>
					</thead>
					<tbody>
					<?
					$pageNum = isset($_GET['page']) ? $_GET['page'] : 1;
					$startNum = $_SESSION['per_page'] * ($pageNum - 1);
					$accountData = array();
					$accountData = $this->accounts;

					if (count($accountData) > 0) {
						?>
						<?php
						foreach ($accountData as $i => $account) {

							//print $account->status;
							$bgcolor = "";
							$contact_name = "--";
							$salesrep = 0;
							if ($this->accountType == "shipper") {
								if ($account->status == 0)
									$bgcolor = "#FF3333";

								$sql = "SELECT COUNT( ac.id ) as count_sales , m.contactname as contactname
							FROM app_commision ac
							LEFT JOIN app_accounts aa ON ac.shipper_id = aa.id
							LEFT JOIN members m ON ac.members_id = m.id where ac.shipper_id = '" . $account->id . "' ";
								$Sales = $this->daffny->DB->selectRows($sql);

								if (isset($Sales) && count($Sales)) {
									$contact_name = $Sales[0]['contactname'];
									$salesrepcount = $Sales[0]['count_sales'];
									if ($salesrepcount > 1)
										$contact_name = $salesrepcount;

									//print $account->id."---".$contact_name."----".$salesrepcount;
								}
							}
							?>
							<tr class="sdd<?= ($i == 0 ? " " : "") ?><?= ((!is_null($account->insurance_expirationdate) && (strtotime($account->insurance_expirationdate) < time())) ? ' highlight-red' : '') ?>" id="row-<?= $account->id ?>">
								<td class="grid-body-left"><a href="<?= getLink("accounts", "details", "id", $account->id); ?>"> <?= $startNum + $i + 1 ?></a></td>
								<td>
									<?php if ($this->accountType == "shipper") { ?><a href="<?= getLink("accounts", "details", "id", $account->id) ?>"><?= htmlspecialchars($account->company_name); ?></a>
									<?php } else { ?>
										<?= htmlspecialchars($account->company_name); ?>
									<?php } ?>
									<div class="status_info_new pull-right">
										<?= statusText(getLink("accounts", "status", "id", $account->id), Account::$status_name[$account->status]) ?>
									</div>
								</td>
								<?php if ($this->accountType == "shipper") { ?>
									<td ><?= htmlspecialchars($account->first_name); ?></td>
									<td ><?= htmlspecialchars($account->last_name); ?></td>
									<td ><?= htmlspecialchars($account->referred_by); ?></td>
									<td >
										<?php print $contact_name; ?>
									</td>
								<?php } else { ?>
									<td ><?= htmlspecialchars($account->contact_name1); ?></td>
									<td >
										<?= htmlspecialchars($account->address1); ?>
										<?= htmlspecialchars($account->address2); ?>
									</td>
								<?php } ?>

								<td><?= htmlspecialchars($account->phone1); ?><?= htmlspecialchars($account->email); ?></td>
								<td><?= htmlspecialchars($account->type); ?></td>
								<td>
									<div class="btn-group action-buttons">
										<?= infoIcon(getLink("accounts", "details", "id", $account->id)) ?>
										<?php
										$carrier_priv_cond = ($account->type == 'Carrier' && (in_array($_SESSION['member']['access_carriers'], [2, 3])));
										$location_priv_cond = ($account->type == 'Location' && (in_array($_SESSION['member']['access_locations'], [2, 3])));
										$shipper_priv_cond = ($account->type == 'Shipper' 
												&& ((in_array($_SESSION['member']['access_shippers'], [1, 2]) 
													&& $account->owner_id==$_SESSION['member']['id'] )
													|| $_SESSION['member']['access_shippers']==3 ));
										
										if ($carrier_priv_cond || $location_priv_cond || $shipper_priv_cond):
											echo editIcon(getLink("accounts", "edit", "id", $account->id));
										endif;
										?>  
										<?php
										if ($carrier_priv_cond || $location_priv_cond || $shipper_priv_cond):
											echo deleteIcon(getLink("accounts", "delete", "id", $account->id), "row-" . $account->id);
										endif;
										?>      
									</div>
								</td>
							</tr>
						<?php } ?>
					<? }else { ?>
						<tr class="grid-body " id="row-" >
							<td align="center" colspan="9">No records found.</td>
						</tr>
					<? } ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	
</div>

@pager@




   <script type="text/javascript">
        $(document).ready(function() {
        $('#allacount').DataTable({
        "lengthChange": false,
        "paging": false,
        "bInfo" : false,
        'drawCallback': function (oSettings) {

        $("#allacount_wrapper").children('.row:first').children('.col-sm-12:first').html('<div class="form-group row"><label class="col-form-label">Show: </label><select class=" records_per_page form-box-combobox form-control " style="width:100px;" onchange="setPagerLimit(this.value)"> <option value="10">10</option> <option value="25">25</option> <option value="50" selected="selected">50</option> <option value="100">100</option> <option value="200">200</option> <option value="500">500</option> </select></div>');
        $("#allacount_wrapper").children('.row:nth-child(3)').children('.col-sm-12:first').html($('.pager').clone());
        $('.pages_div').remove();


        }
        });
        } );
        </script>