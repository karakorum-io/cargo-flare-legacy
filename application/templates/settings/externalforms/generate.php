<? $r = $this->r; ?>

<link rel="stylesheet" type="text/css"
      href="http<?= (($r['cust_use_ssl'] == "1") ? "s" : "") ?>://<?= $_SERVER['SERVER_NAME'] ?>/styles/styles.css"/>
<link rel="stylesheet" type="text/css"
      href="http<?= (($r['cust_use_ssl'] == "1") ? "s" : "") ?>://<?= $_SERVER['SERVER_NAME'] ?>/styles/application.css"/>
<link rel="stylesheet" type="text/css"
      href="http<?= (($r['cust_use_ssl'] == "1") ? "s" : "") ?>://<?= $_SERVER['SERVER_NAME'] ?>/styles/default.css"/>

<div class="apl_centering">
<br/><br/><br/>

<form action="http<?= (($r['cust_use_ssl'] == "1") ? "s" : "") ?>://<?= $_SERVER['SERVER_NAME'] ?>/quote/hash/@hash@"
      method="post">
<input type="hidden" name="CUSTOM_EXTERNAL_FORM" value="CUSTOM_EXTERNAL_FORM">
<? if ($r["cust_return_url"] != "") { ?>
    <input type="hidden" name="post_back" value="@cust_return_url@"/>
<? }?>
<? if ($r["cust_return_errors"]) { ?>
    <input type="hidden" name="return_errors" value="1"/>
<? }?>

<div class="quote-info" style="float:none;">
    <p class="block-title" style="-webkit-user-select: none;">Contact Information</p>

    <div>

        <table cellspacing="0" cellpadding="0" border="0" width="100%" class="form-table quote-edit"
               style="white-space:nowrap;">
            <tbody>
            <tr>
                <td>
                    <table cellspacing="0" cellpadding="0" border="0" width="100%" class="form-table quote-edit"
                           style="white-space:nowrap;">
                        <tbody>
                        <tr>
                            <td><label for="shipper_fname"><span class="required">*</span>First Name:</label></td>
                            <td><input name="shipper_fname" type="text" maxlength="32" class="form-box-textfield"
                                       id="shipper_fname"></td>
                        </tr>
                        <tr>
                            <td><label for="shipper_lname"><span class="required">*</span>Last Name:</label></td>
                            <td><input name="shipper_lname" type="text" maxlength="32" class="form-box-textfield"
                                       id="shipper_lname"></td>
                        </tr>
                        <? if ($r["cust_company"]) { ?>
                            <tr>
                                <td><label for="shipper_company">Company:</label></td>
                                <td><input name="shipper_company" type="text" maxlength="64" class="form-box-textfield"
                                           id="shipper_company"></td>
                            </tr>
                        <? } ?>
                        </tbody>
                    </table>
                </td>
                <td>
                    <table cellspacing="0" cellpadding="0" border="0" width="100%" class="form-table quote-edit"
                           style="white-space:nowrap;">
                        <tbody>
                        <tr>
                            <td><label for="shipper_email"><span class="required">*</span>Email:</label></td>
                            <td><input class="email form-box-textfield" name="shipper_email" type="text" maxlength="32"
                                       id="shipper_email"></td>
                        </tr>
                        <tr>
                            <td><label for="shipper_phone1"><span class="required">*</span>Phottne:</label></td>
                            <td><input class="phone form-box-textfield" name="shipperDFGSG_phone1" type="text" maxlength="32"
                                       id="shipper_phone1" ></td>
                        </tr>
                        <? if ($r["cust_phone2"]) { ?>
                            <tr>
                                <td><label for="shipper_phone2">Phone 2:</label></td>
                                <td><input class="phone form-box-textfield" name="shipper_phone2" type="text"
                                           maxlength="1" id="shipper_phone2"></td>
                            </tr>
                        <? } ?>
                        <? if ($r["cust_cell"]) { ?>
                            <tr>
                                <td><label for="shipper_mobile">Mobile:</label></td>
                                <td><input class="phone form-box-textfield" name="shipper_mobile" type="text"
                                           maxlength="32" id="shipper_mobile"></td>
                            </tr>
                        <? } ?>
                        <? if ($r["cust_fax"]) { ?>
                            <tr>
                                <td><label for="shipper_fax">Fax:</label></td>
                                <td><input class="phone form-box-textfield" name="shipper_fax" type="text"
                                           maxlength="32" id="shipper_fax"></td>
                            </tr>
                        <? } ?>
                        </tbody>
                    </table>
                </td>
                <td>
                    <table cellspacing="0" cellpadding="0" border="0" width="100%" class="form-table quote-edit"
                           style="white-space:nowrap;">
                        <tbody>
                        <? if ($r["cust_address"]) { ?>
                            <tr>
                                <td><label for="shipper_address1">Address:</label></td>
                                <td><input name="shipper_address1" type="text" maxlength="64" class="form-box-textfield"
                                           id="shipper_address1"></td>
                            </tr>
                        <? } ?>
                        <? if ($r["cust_address2"]) { ?>
                            <tr>
                                <td><label for="shipper_address2">Address 2:</label></td>
                                <td><input name="shipper_address2" type="text" maxlength="64" class="form-box-textfield"
                                           id="shipper_address2"></td>
                            </tr>
                        <? } ?>
                        <? if ($r["cust_city"]) { ?>
                            <tr>
                                <td><label for="shipper_city">City:</label></td>
                                <td><input class="geo-city form-box-textfield" name="shipper_city" type="text"
                                           maxlength="32" id="shipper_city"></td>
                            </tr>
                        <? } ?>
                        <? if ($r["cust_state"]) { ?>
                            <tr>
                                <td><label for="shipper_state">State/Zip:</label></td>
                                <td>@shipper_state@
                                    <? if ($r["cust_zip"]) { ?>
                                        <input style="width:50px;margin-left:7px;" class="zip form-box-textfield"
                                               name="shipper_zip" type="text" maxlength="8" id="shipper_zip">
                                    <? } ?>
                                </td>
                            </tr>
                        <? } ?>
                        <? if ($r["cust_country"]) { ?>
                            <tr>
                                <td><label for="shipper_country">Country:</label></td>
                                <td>@shipper_country@</td>
                            </tr>
                        <? } ?>
                        </tbody>
                    </table>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
<br />
<br />
<div class="quote-info" style="float:none;">
    <p class="block-title" style="-webkit-user-select: none;">Origin and Destination</p>
    <div>
        <table cellspacing="0" cellpadding="0" border="0" width="100%" class="form-table" style="white-space:nowrap;">
            <tbody><tr>
                <td><strong>From:</strong></td>
                <td>&nbsp;</td>
                <td><strong>To:</strong></td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td><label for="origin_city"><span class="required">*</span>City:</label></td><td><input class="geo-city form-box-textfield" name="origin_city" type="text" maxlength="32" id="origin_city"></td>
                <td><label for="destination_city"><span class="required">*</span>City:</label></td><td><input class="geo-city form-box-textfield" name="destination_city" type="text" maxlength="32" id="destination_city"></td>
            </tr>
            <tr>
                <td><label for="origin_state"><span class="required">*</span>State/Zip:</label></td><td>@origin_state@ <? if ($r["cust_pickup_zip"]) { ?><input style="width:50px;margin-left:5px;" class="zip form-box-textfield" name="origin_zip" type="text" maxlength="64" id="origin_zip"><? } ?></td>
                <td><label for="destination_state"><span class="required">*</span>State/Zip:</label></td><td>@destination_state@ <? if ($r["cust_dropoff_zip"]) { ?><input style="width:50px;margin-left:5px;" class="zip form-box-textfield" name="destination_zip" type="text" maxlength="64" id="destination_zip"><? } ?></td>
            </tr>
            <tr>
                <td><label for="origin_country">Country:</label></td><td>@origin_country@</td>
                <td><label for="destination_country">Country:</label></td><td>@destination_country@</td>
            </tr>
            </tbody></table>
    </div>
</div>
<br />
<br />
<div class="quote-info" style="float:none;">
    <p class="block-title" style="-webkit-user-select: none;">Shipping Information</p>
    <div>
        <table cellspacing="0" cellpadding="0" border="0" width="100%" class="form-table" style="white-space:nowrap;">
            <tbody><tr>
                <td><label for="shipping_est_date"><span class="required">*</span>Estimated Ship Date:</label></td><td><input class="datepicker form-box-textfield hasDdffatepicker" name="shipping_est_date" type="text" maxlength="8" id="shipping_est_date" placeholder="MM/DD/YY"></td>
                <td rowspan="3" valign="top">
                    <? if ($r["cust_shipper_comments"]) { ?>
                        <label for="shipping_notes">Notes:</label></td><td rowspan="3"><textarea style="height:80px;" name="shipping_notes" cols="4" rows="10" class="form-box-textarea" id="shipping_notes"></textarea>
                    <? }else{ ?>
                        &nbsp;
                    <? } ?>
                </td>
            </tr>
<!--            <tr>-->
<!--                <td><label for="shipping_vehicles_run"><span class="required">*</span>Vehicle(s) Run:</label></td><td><select name="shipping_vehicles_run" class="form-box-combobox" id="shipping_vehicles_run"><option value="" selected="selected">Select One</option><option value="1">No</option><option value="2">Yes</option></select></td>-->
<!--            </tr>-->
            <tr>
                <td><label for="shipping_ship_via"><span class="required">*</span>Ship Via:</label></td><td><select name="shipping_ship_via" class="form-box-combobox" id="shipping_ship_via"><option value="" selected="selected">Select One</option><option value="1">Open</option><option value="2">Enclosed</option><option value="3">Driveaway</option></select></td>
            </tr>
            </tbody></table>
    </div>
</div>

<? for ($i = 1; $i <= (int)$r['cust_vehicles_qty']; $i++) { ?>
<br />
<br />
<div class="quote-info" style="float:none;">
    <p class="block-title" style="-webkit-user-select: none;">Vehicle Information #<?=$i?></p>
    <div>
        <table cellspacing="0" cellpadding="0" border="0" width="100%" class="form-table" style="white-space:nowrap;">
            <tbody>
                <tr>
                    <td><label for="year<?= $i ?>">Year:</label></td>
                    <td>
                        <input class="form-box-textfield" type="text" name="year[]" id="year<?= $i ?>" value="" size="10"/>
                    </td>
                </tr>
                <tr>
                    <td><label for="make<?= $i ?>">Make:</label></td>
                    <td><input class="form-box-textfield" type="text" name="make[]" id="make<?= $i ?>" value="" size="30"/></td>
                </tr>
                <tr>
                    <td><label for="model<?= $i ?>">Model:</label></td>
                    <td><input class="form-box-textfield" type="text" name="model[]" id="model<?= $i ?>" value="" size="30"/></td>
                </tr>
                <tr>
                    <td><label for="type<?= $i ?>">Vehicle Type:</label></td>
                    <td>@type<?=$i?>@</td>
                </tr>
                <tr>
	                <td><label for="type<?= $i ?>">Inop:</label></td>
	                <td>@inop<?=$i?>@</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<? }?>
<? if (count($this->referrers) > 0) { ?>
    <br />
    <br />
    <div class="quote-info" style="float:none;">
        <p class="block-title" style="-webkit-user-select: none;">Referrers<?=$i?></p>
        <div>
            <table cellspacing="0" cellpadding="0" border="0" width="100%" class="form-table" style="white-space:nowrap;">
                <tbody>
                    <tr>
                        <td width="200"><label for="referred_by">How did you hear about us?</label></td>
                        <td>
                            <select name="referred_by" id="referred_by">
                                <option label="Select one" value="">--Select one--</option>
                                <? foreach ($this->referrers as $key => $ref) { ?>
                                    <option value="<?= $ref['name'] ?>"><?=$ref['name']?></option>
                                <? }?>
                            </select>
                        </td>
                      </tr>
                </tbody>
            </table>
      </div>
</div>
<? }?>
<br /><br />
<div style="text-align: center">
    <?= submitButtons("", "Submit") ?>
</div>
</form>

</div>