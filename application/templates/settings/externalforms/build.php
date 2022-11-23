<? include(TPL_PATH . "settings/menu.php"); ?>
<h3>Build your own quote request form</h3>
Here you can generate a custom HTML quote request form to use on your own web page. Quote requests will be submitted directly into your FreightDragon account as leads.
Check the fields you want to include and click the "Generate Form" button. Fields marked with an asterisk (<span style="color:red;">*</span>) are required and cannot be unchecked.
<div align="right" style="clear:both; padding-bottom:5px; padding-top:5px;">
    <img style="vertical-align:middle;" src="<?= SITE_IN ?>images/icons/back.png" alt="Back" width="16" height="16" /> <a href="<?= getLink("externalforms") ?>">&nbsp;Back to the External Forms</a>
</div>
<form action="<?= getLink("externalforms", "build") ?>" method="post">
    <div style="float:left; width:480px; padding-right:10px;">
        <?= formBoxStart("Shipper Information") ?>
        <table cellspacing="5" cellpadding="5" border="0">
            <tr>
                <td>@cust_first_name@</td>
                <td>@cust_company@</td>
                <td>@cust_address@</td>
                <td>@cust_zip@</td>
            </tr>
            <tr>
                <td>@cust_last_name@</td>
                <td>@cust_cell@</td>
                <td>@cust_address2@</td>
                <td>@cust_country@</td>
            </tr>
            <tr>
                <td>@cust_phone@</td>
                <td>@cust_phone2@</td>
                <td>@cust_city@</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td>@cust_email@</td>
                <td>@cust_fax@</td>
                <td>@cust_state@</td>
                <td>&nbsp;</td>
            </tr>

        </table>
        <?= formBoxEnd() ?>
        <br />
        <?= formBoxStart("Vehicle Information") ?>
        <table cellspacing="5" cellpadding="5" border="0">
            <tr>
                <td colspan="2">Allow entering up to @cust_vehicles_qty@  vehicle(s). Each vehicle section will include:</td>
            </tr>
            <tr>
                <td>@cust_year@</td>
            </tr>
            <tr>
                <td>@cust_make@</td>
            </tr>
            <tr>
                <td>@cust_model@</td>
            </tr>
            <tr>
                <td>@cust_vehicle_type@</td>
            </tr>
	        <tr>
		        <td>@cust_vehicle_inop@</td>
	        </tr>
        </table>
        <?= formBoxEnd() ?>
        <br />
        <? if (count($this->referrers) > 0) { ?>
            <?= formBoxStart("Referrers") ?>
            <table cellspacing="5" cellpadding="5" border="0">
                <tr>
                    <td><em>Select which referrers you want to give the user the ability to choose. Select "Referrers" from the "Manage" dropdown to customize your list.</em><br /><br /></td>
                </tr>
                <tr>
                    <td>
                        <? foreach ($this->referrers as $key => $ref) { ?>
                            <input type="checkbox" name="ref[]" id="ref_<?= $ref['id'] ?>" value="<?= $ref['id'] ?>" <?= $ref['ch'] ?> />
                            <label for="ref_<?= $ref['id'] ?>"><?= htmlspecialchars($ref['name']) ?></label>
                            <br />
                        <? } ?>
                    </td>
                </tr>
            </table>
            <?= formBoxEnd() ?>
        <? } ?>
        <br />
        <?php echo submitButtons(getLink("defaultsettings"), "Generate"); ?>
    </div>
    <div style="float:left; width:480px;">
        <?= formBoxStart("Shipping Information") ?>
        <table cellspacing="5" cellpadding="5" border="0">
            <tr><td>@cust_estimated_ship_date@</td></tr>
<!--            <tr><td>@cust_vehicles_run@</td></tr>-->
            <tr><td>@cust_ship_via@</td></tr>
            <tr><td>@cust_shipper_comments@</td></tr>
        </table>
        <?= formBoxEnd() ?>
        <br />
        <?= formBoxStart("Pickup/Dropoff Information") ?>
        <table cellspacing="5" cellpadding="5" border="0">
            <tr>
                <td width="150">Pickup:</td>
                <td>Dropoff:</td>
            </tr>
            <tr>
                <td>@cust_pickup_city@</td>
                <td>@cust_dropoff_city@</td>
            </tr>
            <tr>
                <td>@cust_pickup_state@</td>
                <td>@cust_dropoff_state@</td>
            </tr>
            <tr>
                <td>@cust_pickup_country@</td>
                <td>@cust_dropoff_country@</td>
            </tr>
            <tr>
                <td>@cust_pickup_zip@</td>
                <td>@cust_dropoff_zip@</td>
            </tr>
        </table>
        <?= formBoxEnd() ?>
        <br />
        <?= formBoxStart("Form Processing") ?>
        <table cellspacing="5" cellpadding="5" border="0">
            <tr><td colspan="2">These are hidden fields that let us know how to process your form.</td></tr>
            <tr><td>@cust_return_url@</td></tr>
            <tr><td colspan="2"><em>A URL to return to after processing the form. If you leave this field empty, the user will be taken to our generic thank-you page.</em></td></tr>
            <tr><td colspan="2">@cust_return_errors@</td></tr>
            <tr><td colspan="2"><em>Check this field to have any processing errors returned to the Return URL. If unchecked, we will display the errors to the user with a "Back" button.</em></td></tr>
            <tr><td colspan="2">@cust_use_ssl@</td></tr>
            <tr><td colspan="2"><em>Check this field if you submit the form over a secure website, i.e. if your website URL starts with "https://", to prevent security warning popups.</em></td></tr>
        </table>
        <?= formBoxEnd() ?>
    </div>
    <div style="clear:both;">&nbsp;</div>
</form>