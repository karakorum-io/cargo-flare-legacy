<form action="<?=BASE_PATH?>quote/hash/@hash@" method="post">
    <input type="hidden" name="CUSTOM_EXTERNAL_FORM" value="CUSTOM_EXTERNAL_FORM">
	<table>
		<tr>
			<th colspan="2" align="left">Contact Information:</th>
		</tr>
		<tr>
			<td><label for="shipper_fname">First Name:</label></td>
			<td><input type="text" name="shipper_fname" id="shipper_fname" value="" size="30" maxlength="50" /></td>
		</tr>
		<tr>
			<td><label for="shipper_lname">Last Name:</label></td>
			<td><input type="text" name="shipper_lname" id="shipper_lname" value="" size="30" maxlength="50" /></td>
		</tr>
		<tr>
			<td><label for="shipper_phone1">Phone:</label></td>
			<td><input type="text" name="shipper_phone1" id="shipper_phone1" value="" size="30" /></td>
		</tr>
		<tr>
			<td><label for="shipper_email">Email:</label></td>
			<td><input type="text" name="shipper_email" id="shipper_email" value="" size="30" /></td>
		</tr>
		<tr>
			<th colspan="2" align="left">Pickup Information:</th>
		</tr>
		<tr>
			<td><label for="origin_city">City:</label></td>
			<td><input type="text" name="origin_city" id="origin_city" value="" size="30" /></td>
		</tr>
		<tr>
			<td><label for="origin_state">State:</label></td>
			<td>@origin_state@</td>
		</tr>
		<tr>
			<td><label for="origin_country">Country:</label></td>
			<td>@origin_country@</td>
		</tr>
		<tr>
			<th colspan="2" align="left">Dropoff Information:</th>
		</tr>
		<tr>
			<td><label for="destination_city">City:</label></td>
			<td><input type="text" name="destination_city" id="destination_city" value="" size="30" /></td>
		</tr>
		<tr>
			<td><label for="destination_state">State:</label></td>
			<td>@destination_state@</td>
		</tr>
		<tr>
			<td><label for="destination_country">Country:</label></td>
			<td>@destination_country@</td>
		</tr>
		<tr>
			<th colspan="2" align="left">Shipping Information:</th>
		</tr>
		<tr>
			<td><label for="shipping_est_date">Estimated Ship Date:</label></td>
			<td><input type="text" name="shipping_est_date" id="shipping_est_date" value="" size="30" /></td>
		</tr>
<!--		<tr>-->
<!--			<td><label for="shipping_vehicles_run">Vehicle(s) Run:</label></td>-->
<!--			<td>-->
<!--				<select name="shipping_vehicles_run" id="shipping_vehicles_run">-->
<!--					<option value="">Select one</option>-->
<!--					<option value="1">Yes</option>-->
<!--					<option value="0">No</option>-->
<!--				</select>-->
<!--			</td>-->
<!--		</tr>-->
		<tr>
			<td><label for="shipping_ship_via">Ship Via:</label></td>
			<td>
				<select name="shipping_ship_via" id="shipping_ship_via">
					<option label="Select one" value="">Select one</option>
					<option label="Open" value="1">Open</option>
					<option label="Enclosed" value="2">Enclosed</option>
					<option label="Driveaway" value="3">Driveaway</option>
				</select>
			</td>
		</tr>
		<tr>
			<th colspan="2" align="left">Vehicle Information:</th>
		</tr>

		<? for ($i=1;$i<=8;$i++){?>
		<tr>
			<td colspan="2">Vehicle #<?=$i?>:</td>
		</tr>
		<tr>
			<td><label for="year<?=$i?>">Year:</label></td>
			<td><input type="text" name="year[]" id="year<?=$i?>" value="" size="10" /></td>
		</tr>
		<tr>
			<td><label for="make<?=$i?>">Make:</label></td>
			<td><input type="text" name="make[]" id="make<?=$i?>" value="" size="30" /></td>
		</tr>
		<tr>
			<td><label for="model<?=$i?>">Model:</label></td>
			<td><input type="text" name="model[]" id="model<?=$i?>" value="" size="30" /></td>
		</tr>
		<tr>
			<td><label for="type<?=$i?>">Vehicle Type:</label></td>
			<td>@type<?=$i?>@</td>
		</tr>
		<tr>
			<td><label for="type<?= $i ?>">Inop:</label></td>
			<td>@inop<?=$i?>@</td>
		</tr>
		<?}?>
		<tr>
			<td><label for="referred_by">How did you hear about us?</label></td>
			<td>
				<select name="referred_by" id="referred_by">
					<option label="Select one" value="">--Select one--</option>
					<? foreach ($this->referrers as $key=>$ref){?>
					<option value="<?=$ref['name']?>"><?=$ref['name']?></option>
					<?}?>
				</select>
			</td>
		</tr>
		<tr>
			<td colspan="2"><input type="submit" value="Submit Quote Request"></td>
		</tr>
	</table>
</form>