<table cellpadding="0" cellspacing="0" style="border:1px solid #f26e21" class="dispatch_table" width="100%">
	<tr>
		<th colspan="2" align="center" style="background:#fff; border:1px solid #f26e21">
			<h4 align="center" style="color:red;">TRANSPORT DISPATCH SHEET -- NOT TO BE USED AS A BOL</h4>
			<p align="center">SUBJECT TO THE TERMS AND CONDITIONS -- QUESTIONS? 954-256-9132</p>
		</th>
	</tr>
	<tr>
		<th colspan="2" style="background:#f26e21; color:#fff;" class="text-center">COMPANY INFORMATION</th>
	</tr>
	<tr>
		<td class="group" width="50%">

			<table cellpadding="0" cellspacing="0">

				<tr>

					<td class="order_number">Order ID: @order_number@</td>

				</tr>

				<tr>

					<td class="c_companyname">@c_companyname@</td>

				</tr>

				<tr>

					<td class="c_address1 c_address2">@c_address1@ @c_address2@</td>

				</tr>

				<tr>

					<td class="c_city c_state c_zip_code">@c_city@, @c_state@, @c_zip_code@</td>

				</tr>

			</table>

		</td>
		<td class="group" width="50%">

			<table cellpadding="0" cellspacing="0" border="0">

				<tr>

					<td>Company Phone:</td>

					<td class="c_phone">@c_phone@</td>

				</tr>

				<tr>

					<td>Dispatch Contact:</td>

					<td class="c_dispatch_contact">@c_dispatch_contact@</td>

				</tr>

				<tr>

					<td>Dispatch Phone:</td>

					<td class="c_dispatch_phone">@c_dispatch_phone@</td>

				</tr>

				<tr>

					<td>Accounting Fax:</td>

					<td class="c_dispatch_fax">@c_dispatch_accounting_fax@</td>

				</tr>

				<tr>

					<td>Dispatch MC#:</td>

					<td class="c_icc_mc_number">@c_icc_mc_number@</td>

				</tr>

			</table>

		</td>
	</tr>
	<tr>
		<th colspan="2" style="background:#f26e21; color:#fff;" class="text-center">CARRIER INFORMATION</th>
	</tr>

	<tr>

		<td class="group">

			<table cellspacing="0" cellpadding="0" border="0">

				<tr>

					<td>Order ID:</td>

					<td class="order_number">@order_number@</td>

				</tr>

				<tr>

					<td>Carrier:</td>

					<td class="carrier_company_name">@carrier_company_name@</td>

				</tr>

				<tr>

					<td>Address:</td>

					<td class="carrier_address">@carrier_address@</td>

				</tr>

				<tr>

					<td>City:</td>

					<td class="carrier_city carrier_state carrier_zip">

						@carrier_city@,

						@carrier_state@,

						@carrier_zip@

					</td>

				</tr>

				<tr>

					<td>Email:</td>

					<td class="carrier_email">@carrier_email@</td>

				</tr>

				<tr>

					<td>ICC MC Number:</td>

					<td class="carrier_email">@carrier_insurance_iccmcnumber@</td>

				</tr>

				<tr>

					<td>Print on check As:</td>

					<td class="carrier_email">@carrier_print_name@</td>

				</tr>

			</table>

		</td>

		<td class="group">

			<table cellpadding="0" cellspacing="0" border="0">

				<tr>

					<td>Contact:</td>

					<td class="carrier_contact_name">@carrier_contact_name@</td>

				</tr>

				<tr>

					<td>Phone (1):</td>

					<td class="carrier_phone_1">@carrier_phone_1@@carrier_phone_1_ext@</td>

				</tr>

				<tr>

					<td>Phone (2):</td>

					<td class="carrier_phone_2">@carrier_phone_2@@carrier_phone_2_ext@</td>

				</tr>

				<tr>

					<td>Phone (Fax):</td>

					<td class="carrier_fax">@carrier_fax@</td>

				</tr>

				<tr>

					<td>Phone (Cell):</td>

					<td class="carrier_phone_cell">@carrier_phone_cell@</td>

				</tr>

				<tr>

					<td colspan="2" align="center" class="carrier_phone_cell"><?php echo $this->signature ?></td>

				</tr>

			</table>

		</td>

	</tr>

	<tr>
		<th colspan="2" style="background:#f26e21; color:#fff;" class="text-center">ORDER INFORMATION</th>
	</tr>

	<tr>

		<td class="group">

			<table cellpadding="0" cellspacing="0" border="0">

				<tr>

					<td>Dispatch Date:</td>

					<td class="created">@created@</td>

				</tr>

				<tr>

					<td>Pickup @load_date_type@:</td>

					<td class="load_date">@load_date@</td>

				</tr>

				<tr>

					<td>Delivery @delivery_date_type@:</td>

					<td class="delivery_date">@delivery_date@</td>

				</tr>

				<tr>

					<td>Ship Via:</td>

					<td class="ship_via">@ship_via@</td>

				</tr>

			</table>

		</td>

		<td class="group">

			<table cellpadding="0" cellspacing="0" border="0">

				<tr>

					<td>Carrier Pay (total):</td>

					<td class="entity_carrier_pay entity_carrier_pay_c">$@entity_carrier_pay@ @entity_carrier_pay_c@</td>

				</tr>

				<tr>

					<td>On Delivery To Carrier:</td>

					<td class="entity_odtc">$@entity_odtc@</td>

				</tr>

				<tr>

					<td>@company_or_carrier@:</td>

					<td class="entity_coc entity_coc_c">$@entity_coc@ @entity_coc_c@ @paid_by_company@</td>

				</tr>

				<tr>

					<td>Pickup Terminal Fee:</td>

					<td class="entity_coc entity_coc_c">$@entity_pickup_terminal_fee@</td>

				</tr>

				<tr>

					<td>Delivery Terminal Fee:</td>

					<td class="entity_coc entity_coc_c">$@entity_dropoff_terminal_fee@</td>

				</tr>



			</table>

		</td>

	</tr>

	<tr>

		<td colspan="2" align="left">

			<table cellpadding="0" cellspacing="0" border="0">

				<tr>

						<td align="left">Payment Terms:</td>

						<td class="ship_via" align="left">@payments_terms@</td>

				</tr>

			</table>

		</td>

	</tr>

	<tr>

		<th colspan="2" style="background:#f26e21; color:#fff;" class="text-center">Vehicles</th>

	</tr>

	<tr>

		<td colspan="2" class="vehicles">@vehicles@</td>

	</tr>

	<tr>

		<th style="background:#f26e21; color:#fff;" class="text-center">PICKUP FROM</th>

		<th style="background:#f26e21; color:#fff;" class="text-center">DELIVER TO</th>

	</tr>

	<tr>

		<td class="group">

			<table cellpadding="0" cellspacing="0" border="0">

				<tr>

					<td>Name:</td>

					<td class="from_name">@from_name@</td>

				</tr>

				<tr>

					<td>Company:</td>

					<td class="from_company">@from_company@</td>

				</tr>

				<tr>

					<td>Street:</td>

					<td class="from_address">@from_address@</td>

				</tr>

				<tr>

					<td>Street (2):</td>

					<td class="from_address2">@from_address2@</td>

				</tr>

				<tr>

					<td>City, State:</td>

					<td class="from_city from_state">@from_city@, @from_state@</td>

				</tr>

				<tr>

					<td>Zip Code:</td>

					<td class="from_zip">@from_zip@</td>

				</tr>

				<tr>

					<td>Country:</td>

					<td class="from_country">@from_country@</td>

				</tr>

				<tr>

					<td>Phone (1):</td>

					<td class="from_phone_1">@from_phone_1@@from_phone_1_ext@</td>

				</tr>

				<tr>

					<td>Phone (2):</td>

					<td class="from_phone_2">@from_phone_2@@from_phone_2_ext@</td>

				</tr>

				<tr>

					<td>Phone (Cell):</td>

					<td class="from_phone_cell">@from_phone_cell@</td>

				</tr>

				<!--tr>

					<td>Auction Name:</td>

					<td class="origin_auction_name">@origin_auction_name@</td>

				</tr-->

				<tr>

					<td>Booking Number:</td>

					<td class="entity_booking_number">@from_booking_number@</td>

				</tr>

				<tr>

					<td>Buyer Number:</td>

					<td class="entity_buyer_number">@from_buyer_number@</td>

				</tr>
				<tr>

					<td>Hours:</td>

					<td class="from_hours">@from_hours@</td>

				</tr>

			</table>

		</td>

		<td class="group">

			<table cellpadding="0" cellspacing="0" border="0">

				<tr>

					<td>Name:</td>

					<td class="to_name">@to_name@</td>

				</tr>

				<tr>

					<td>Company:</td>

					<td class="to_company">@to_company@</td>

				</tr>

				<tr>

					<td>Street:</td>

					<td class="to_address">@to_address@</td>

				</tr>

				<tr>

					<td>Street (2):</td>

					<td class="to_address2">@to_address2@</td>

				</tr>

				<tr>

					<td>City, State:</td>

					<td class="to_city">@to_city@, @to_state@</td>

				</tr>

				<tr>

					<td>Zip Code:</td>

					<td class="to_zip">@to_zip@</td>

				</tr>

				<tr>

					<td>Country:</td>

					<td class="to_country">@to_country@</td>

				</tr>

				<tr>

					<td>Phone (1):</td>

					<td class="to_phone_1">@to_phone_1@@to_phone_1_ext@</td>

				</tr>

				<tr>

					<td>Phone (2):</td>

					<td class="to_phone_2">@to_phone_2@@to_phone_2_ext@</td>

				</tr>

				<tr>

					<td>Phone (Cell):</td>

					<td class="to_phone_cell">@to_phone_cell@</td>

				</tr>

				<!--tr>

					<td>Auction Name:</td>

					<td class="origin_auction_name">@to_auction_name@</td>

				</tr-->

				<tr>

					<td>Booking Number:</td>

					<td class="entity_booking_number">@to_booking_number@</td>

				</tr>

				<tr>

					<td>Buyer Number:</td>

					<td class="entity_buyer_number">@to_buyer_number@</td>

				</tr>
				<tr>

					<td>Hours:</td>

					<td class="entity_buyer_number">@to_hours@</td>

				</tr>

			</table>

		</td>

	</tr>

</table>



<div class="fake-th text-center" style="background:#f26e21; color:#fff;">DISPATCH INSTRUCTIONS</div>

<div class="fake-td" style="border-left:1px solid #f26e21;border-right:1px solid #f26e21;">

    @instructions@

    <br/>

    @information@

    <br/>

    <strong>Pickup @load_date_type@:</strong> @load_date@<br/>

    <strong>Delivery @delivery_date_type@:</strong> @delivery_date@

</div>

<div class="fake-td" style="border-left:1px solid #f26e21;border-right:1px solid #f26e21;">

    <hr/>

    <strong>

        PLEASE GIVE THE SHIPPER AT LEAST A 24 HOUR NOTICE FOR PICKUP AND DELIVERY.<br/>

        PLEASE DO A THOROUGH INSPECTION OF THE VEHICLE ON PICKUP.<br/>

    </strong>

    <br/>

    Authority to transport this vehicle is hereby assigned to <strong>@carrier_company_name@</strong>.

    By accepting this agreement <strong>@carrier_company_name@</strong> certifies that they have the proper legal authority and insurance to carry the above described vehicle, only on trucks owned by <strong>@carrier_company_name@</strong>.

    All invoices must be accompanied by a signed delivery receipt and faxed to <strong>@c_companyname@</strong>.

    The above agreed upon price includes any and all surcharges.

    <br/><br/>

    Notwithstanding anything to the contrary, the agreement between <strong>@carrier_company_name@</strong> and <strong>@c_companyname@</strong>, as described in this dispatch sheet, is solely between <strong>@carrier_company_name@</strong> and <strong>@c_companyname@</strong>. <strong>CargoFlare.com</strong> is not a party to such agreement, has no obligation under such agreement and expressly disclaims all liability whatsoever arising out of, or in connection with such agreement.

</div>

<div class="fake-th text-center" style="background:#f26e21; color:#fff;">ADDITIONAL TERMS</div>

<div class="fake-td" style="border-left:1px solid #f26e21;border-right:1px solid #f26e21; border-bottom:1px solid #f26e21">@dispatch_terms@</div>

<?php echo $this->signature ?>