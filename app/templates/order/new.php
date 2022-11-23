<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>@title@</title>
		<link rel="shortcut icon" href="<?php echo SITE_IN ?>styles/favicon.ico" />
    	<link rel="stylesheet" type="text/css" href="<?php echo SITE_IN ?>styles/styles.css"/>
		<link rel="stylesheet" type="text/css" href="<?php echo SITE_IN ?>styles/application.css"/>
		<link rel="stylesheet" type="text/css" href="<?php echo SITE_IN ?>styles/default.css"/>
		<link rel="stylesheet" type="text/css" href="<?php echo SITE_IN ?>styles/jquery-ui.css"/>
		<link rel="stylesheet" type="text/css" href="<?php echo SITE_IN ?>styles/jquery.ui.timepicker.css"/>
		
		<script type="text/javascript">var BASE_PATH = '<?php echo SITE_IN ?>';</script>
		<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false&language=en"></script>
		<script type="text/javascript" src="<?php echo SITE_IN ?>jscripts/jquery.min.js"></script>
		<script type="text/javascript" src="<?php echo SITE_IN ?>jscripts/jquery-ui.js"></script>
		<script type="text/javascript" src="<?= SITE_IN ?>jscripts/ui.geo_autocomplete.js"></script>
		<script type="text/javascript" src="<?php echo SITE_IN ?>jscripts/jquery.maskMoney.js"></script>
		<script type="text/javascript" src="<?php echo SITE_IN ?>jscripts/functions.js"></script>
		<script type="text/javascript" src="<?php echo SITE_IN ?>jscripts/application.js"></script>
		<script type="text/javascript" src="<?php echo SITE_IN ?>jscripts/jquery.maskedinput-1.3.min.js"></script>
</head>
<body>
<div style="clear: both;margin-top:20px;"></div>
<div class="apl_centering">
<div>@header@</div>
<h3>Create Order</h3>
Complete the form below and click Save when finished. Required fields are marked with a <span style="color:red;">*</span><br/><br/>
@flash_message@
<form action="<?= getLink('orders/create') ?>" method="post">
<div class="order-info" style="float:none;">
	<p class="block-title">Shipper Information</p>
	<div>
		<table cellspacing="0" cellpadding="0" border="0" width="100%" class="form-table order-edit" style="white-space:nowrap;">
			<tr>
				<td colspan="2">&nbsp;</td>
				<td>@shipper_email@</td>
				<td>@shipper_address1@</td>
			</tr>
			<tr>
				<td>@shipper_fname@</td>
				<td>@shipper_phone1@</td>
				<td>@shipper_address2@</td>
			</tr>
			<tr>
				<td>@shipper_lname@</td>
				<td>@shipper_phone2@</td>
				<td>@shipper_city@</td>
			</tr>
			<tr>
				<td>@shipper_company@</td>
				<td>@shipper_mobile@</td>
				<td>@shipper_state@@shipper_state2@@shipper_zip@</td>
			</tr>
			<tr>
				<td>&nbsp;</td><td>&nbsp;</td>
				<td>@shipper_fax@</td>
				<td>@shipper_country@</td>
			</tr>
		</table>
	</div>
</div>
<br/>
<div class="order-info" style="float:none;">
	<p class="block-title">Pickup Contact &amp Location</p>
	<div>
		<table cellspacing="0" cellpadding="0" border="0" width="100%" class="form-table" style="white-space:nowrap;">
			<tr>
				<td>@origin_address1@</td>
				<td>@origin_contact_name@</td>
				<td>@origin_phone1@</td>
			</tr>
			<tr>
				<td>@origin_address2@</td>
				<td>@origin_company_name@</td>
				<td>@origin_phone2@</td>
			</tr>
			<tr>
				<td>@origin_city@</td>
				<td>@origin_buyer_number@</td>
				<td>@origin_phone3@</td>
			</tr>
			<tr>
				<td>@origin_state@@origin_state2@@origin_zip@</td>
				<td colspan="2">&nbsp;</td>
				<td>@origin_mobile@</td>
			</tr>
			<tr>
				<td>@origin_country@</td>
				<td colspan="4">&nbsp;</td>
			</tr>
		</table>
	</div>
</div>
<br/>
<div class="order-info" style="float:none;">
	<p class="block-title">Delivery Contact &amp Location</p>
	<div>
		<table cellspacing="0" cellpadding="0" border="0" width="100%" class="form-table" style="white-space:nowrap;">
			<tr>
				<td>@destination_address1@</td>
				<td>@destination_contact_name@</td>
				<td>@destination_phone1@</td>
			</tr>
			<tr>
				<td>@destination_address2@</td>
				<td>@destination_company_name@</td>
				<td>@destination_phone2@</td>
			</tr>
			<tr>
				<td>@destination_city@</td>
				<td colspan="2">&nbsp;</td>
				<td>@destination_phone3@</td>
			</tr>
			<tr>
				<td>@destination_state@@destination_state2@@destination_zip@</td>
				<td colspan="2">&nbsp;</td>
				<td>@destination_mobile@</td>
			</tr>
			<tr>
				<td>@destination_country@</td>
				<td colspan="4">&nbsp;</td>
			</tr>
		</table>
	</div>
</div>
<br/>
<div class="order-info" style="float:none;">
	<p class="block-title">Shipping Information</p>
	<div>
		<table cellspacing="0" cellpadding="0" border="0" width="100%" class="form-table" style="white-space:nowrap;">
			<tr>
				<td>@avail_pickup_date@</td>
				<td rowspan="3" valign="top">
					<table cellspacing="0" cellpadding="0" border="0">
						<tr>
							<td valign="top">@notes_for_shipper@<br/><i>(Will appear on Shipper Invoice &amp; Shipping Order Form)</i></td>
						</tr>
						<tr>
							<td valign="top">@notes_from_shipper@</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td>@include_shipper_comment@</td>
						</tr>
					</table>
				</td>
			</tr>
<!--			<tr>-->
<!--				<td valign="top">@shipping_vehicles_run@</td>-->
<!--			</tr>-->
			<tr>
				<td valign="top">@shipping_ship_via@</td>
			</tr>
		</table>
	</div>
</div>
<br/>
<div class="order-info" style="float:none;">
	<p class="block-title">Vehicle Information</p>
	<div>
		<table cellpadding="0" cellspacing="0" border="0" width="100%" class="grid" style="white-space:nowrap;" id="vehicles-grid">
			<thead>
			<tr class="grid-head">
				<td class="grid-head-left">Year</td>
				<td>Make</td>
				<td>Model</td>
				<td>Type</td>
				<td>Tariff</td>
				<td>Deposit</td>
				<td class="grid-head-right">Actions</td>
			</tr>
			</thead>
			<tbody>
			<?php if (isset($_POST['year'])) : ?>
				<?php foreach($_POST['year'] as $i => $year) : ?>
				<tr class="grid-body" rel="<?= $i+1 ?>">
					<td class="grid-body-left"><input type="hidden" name="year[]" value="<?= $year ?>"/><?= $year ?></td>
					<td><input type="hidden" name="make[]" value="<?= $_POST['make'][$i] ?>"/><?= $_POST['make'][$i] ?></td>
					<td><input type="hidden" name="model[]" value="<?= $_POST['model'][$i] ?>"/><?= $_POST['model'][$i] ?></td>
					<td><input type="hidden" name="type[]" value="<?= $_POST['type'][$i] ?>"/><?= $_POST['type'][$i] ?></td>
					<td><input type="hidden" name="tariff[]" value="<?= $_POST['tariff'][$i] ?>"/><?= $_POST['tariff'][$i] ?></td>
					<td><input type="hidden" name="deposit[]" value="<?= $_POST['deposit'][$i] ?>"/><?= $_POST['deposit'][$i] ?></td>
					<td align="center" class="grid-body-right">
						<img src="<?= SITE_IN ?>images/icons/edit.png" alt="Edit" title="Edit" onclick="editVehicle(<?= $i+1 ?>)" class="action-icon"/>
						<img src="<?= SITE_IN ?>images/icons/delete.png" alt="Delete" title="Delete" onclick="deleteVehicle(<?= $i+1 ?>)" class="action-icon"/>
					</td>
				</tr>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>
		<br/>
		<div><?= functionButton('Add Vehicle', 'addVehicle()') ?></div>
	</div>
</div>
<br/>
<div class="order-info" style="float:none;">
	<p class="block-title">Pricing Information</p>
	<div>
		<table cellspacing="0" cellpadding="0" border="0" width="100%" class="form-table" style="white-space:nowrap;">
			<tr>
				<td>Total Tariff</td>
				<td>$<span id="total_tariff">@total_tariff@</span>&nbsp;<span class="grey-comment">(Edit tariff under the "Vehicle Information" section)</span></td>
			</tr>
			<tr>
				<td>Required Deposit</td>
				<td>$<span id="total_deposit">@total_deposit@</span>&nbsp;<span class="grey-comment">(Edit deposit under the "Vehicle Information" section)</span></td>
			</tr>
			<tr>
				<td>Carrier Pay</td>
				<td>$<span id="carrier_pay">@carrier_pay@</span>&nbsp;<span class="grey-comment">(Edit tariff and deposit under the "Vehicle Information" section)</span></td>
			</tr>
			<tr>
				<td>@balance_paid_by@</td>
			</tr>
			<tr>
				<td>@pickup_terminal_fee@&nbsp;<span class="grey-comment">(Do not include fees paid directly from shipper to terminal)</span></td>
			</tr>
			<tr>
				<td>@delivery_terminal_fee@&nbsp;<span class="grey-comment">(Do not include fees paid directly from shipper to terminal)</span></td>
			</tr>
		</table>
	</div>
</div>
<br/>
<div class="order-info" style="float:none;">
	<p class="block-title">Additional Information</p>
	<div>
		<table cellspacing="0" cellpadding="0" border="0" width="100%" class="form-table" style="white-space:nowrap;">
			<tr>
				<td>@referred_by@</td>
			</tr>
		</table>
	</div>
</div>
<br />
<div style="float:right">
	<?= submitButtons(SITE_IN."application/orders", "Save") ?>
</div>
</form>
<div style="margin-top: 20px;">
@footer@
</div>
</div>
</body>
</html>