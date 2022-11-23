<style>
    .error{
        border:1px solid red !important;
    }

    label.error {
        display: none !important;
    }

    #swal2-title{
        font-size: 12px !important;
        line-height: 20px !important;
    }
</style>

<?php include TPL_PATH . "accounts/accounts/menu_details.php";?>

<div align="right" style="clear:both; padding-bottom:5px; padding-top:5px;">
	<img style="vertical-align:middle;" src="<?php echo SITE_IN?>images/icons/back.png" alt="Back" width="16" height="16" />
    <?php
        if ($_SESSION['member']['parent_id'] == $_SESSION['member_id']) {
            ?>
            <a href="<?php echo getLink("accounts")?>">&nbsp;Back to the list</a>
            <?php
        } else {
            ?>
            <a href="<?php echo getLink("accounts", "shippers")?>">&nbsp;Back to the list</a>
            <?php
        }
    ?>
</div>

<div class="row">
    <div class="col-12">
        <input type="button" class="btn btn-sm btn_bright_blue  mb-4 " value="Add Cards" onclick="AddCardData()">
        <table class="table table-bordered mt-4" id="add_cards">
            <thead>
                <tr>
                    <th class="grid-head-left"><?php echo $this->order->getTitle("Number", "Card Number")?></th>
                    <th class="grid-head-left"><?php echo $this->order->getTitle("ExpiryYear", "Expiry")?></th>
                    <th class="grid-head-left"><?php echo $this->order->getTitle("CVV", "CVV")?></th>
                    <th class="grid-head-left"><?php echo $this->order->getTitle("Type", "Type")?></th>
                    <th><?php echo $this->order->getTitle("FirstName", "Firs tName")?></th>
                    <th class="grid-head-right"><?php echo $this->order->getTitle("LastName", "Last Name")?></th>
                    <th class="grid-head-left">Address</th>
                    <th class="grid-head-right"><?php echo $this->order->getTitle("Created", "Added On")?></th>
                    <th class="grid-head-right"><?php echo $this->order->getTitle("Updated", "Updated On")?></th>
                    <th><?php echo $this->order->getTitle("Recent", "Recently Used")?></th>
                    <th class="grid-head-right"><?php echo $this->order->getTitle("Status", "Status")?></th>
                    <th class="grid-head-right">Action</th>
                </tr>
            </thead>
            <tbody id="shipper-cards">
                <? if (count($this->data) > 0){?>
                    <? foreach ($this->data as $i => $data) { ?>
                    <tr class="grid-body<?php echo ($i == 0 ? " " : "")?>" id="row-<?php echo $data['CardId']?>">
                        <td class="grid-body-left"><?php echo $data["Number"]?></td>
                        <td><?php echo $data["ExpiryMonth"]."/".$data["ExpiryYear"]?></td>
                        <td><?php echo $data["CVV"]?></td>
                        <?php
                            if($data["Type"] == 0){
                                $type = "Others";
                            } else if($data["Type"] == 1){
                                $type = "VISA";
                            } else if($data["Type"] == 2){
                                $type = "MasterCard";
                            } else if($data["Type"] == 3){
                                $type = "AMEX";
                            } else {
                                $type = "Discover";
                            }
                        ?>
                        <td><?php echo $type?></td>
                        <td align="left"><?php echo $data["FirstName"]?></td>
                        <td align="left" class="grid-body-right"><?php echo $data["LastName"]?></td>
                        <td align="left" class="grid-body-right"><?php echo $data["Address"].", ".$data["City"].", ".$data["State"]." ".$data["Zipcode"]?></td>
                        <td align="center" class="grid-body-right"><?php echo $data["Created"]?></td>
                        <td align="center" class="grid-body-right"><?php echo ($data["Updated"] != NULL ? $data['Updated'] : "")?></td>
                        <td align="center" class="grid-body-right"><?php echo ($data["Recent"] > 0 ? "Yes" : "" )?></td>
                        <td align="center" class="grid-body-right"><?php echo ($data["Status"] > 0 ? "Active" : "In Active")?></td>
                        <td align="center" class="grid-body-right">
                            <img onclick="EditSavedCards(<?php echo $data['CardId']?>)" src="/images/icons/edit.png" title="Edit" alt="Edit" class="pointer" width="16" height="16">
                            <img onclick="DeleteCards(<?php echo $data['CardId']?>)" src="/images/icons/delete.png" title="Delete" alt="Delete" class="pointer" width="16" height="16">
                        </td>
                    </tr>
                    <? } ?>
                <? }else{ ?>
                    <tr class="grid-body cc-row-empty" id="row-1">
                        <td colspan="12" align="center">Records not found.</td>
                    </tr>
                <? } ?>
            </tbody>
        </table>
    </div>
</div>
@pager@
<br/>
<?php echo backButton(getLink("accounts"));?>

<!--Edit Saved Card Information Dialogue-->
<div class="modal fade" id="editSavedCards" tabindex="-1" role="dialog" aria-labelledby="editSavedCards_modal" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editSavedCards_modal">Add New Card Information</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i class="fa fa-times" aria-hidden="true"></i>
                </button>
            </div>
            <div class="modal-body">
                <h3>Edit Saved Card</h3>
                <input type="hidden" id="Edit_CardId">
                <div class="pt-3" style="padding-left:20px;padding-right:20px;">
                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <div class="new_form-group">
                                <label for="Edit_e_cc_fname">First Name:</label>
                                <input tabindex="70" type="text" maxlength="50" class="form-box-textfield form-control" value="" id="Edit_e_cc_fname">
                            </div>
                            <div class="new_form-group ">
                                <label for="Edit_e_cc_type">Type:</label>
                                <select tabindex="72" class="form-box-combobox e_cc_type_existing" id="Edit_e_cc_type">
                                    <option value="" selected="selected">--Select--</option>
                                    <option value="1">Visa</option>
                                    <option value="2">MasterCard</option>
                                    <option value="3">Amex</option>
                                    <option value="4">Discover</option>
                                </select>
                                <br/><br><br>
                            </div>
                            <div class="new_form-group">
                                <label for="Edit_e_cc_month">Exp. Date:</label>
                                <select tabindex="75" style="width:84px;" class="form-box-combobox e_cc_month_existing" id="Edit_e_cc_month">
                                    <option value="" selected="selected">--</option>
                                    <option value="01">01</option>
                                    <option value="02">02</option>
                                    <option value="03">03</option>
                                    <option value="04">04</option>
                                    <option value="05">05</option>
                                    <option value="06">06</option>
                                    <option value="07">07</option>
                                    <option value="08">08</option>
                                    <option value="09">09</option>
                                    <option value="10">10</option>
                                    <option value="11">11</option>
                                    <option value="12">12</option>
                                </select>
                                <select tabindex="76" style="width:84px;" class="form-box-combobox e_cc_year_existing" id="Edit_e_cc_year">
                                    <option value="" selected="selected">--</option>
                                    <option value="2022">2022</option>
                                    <option value="2023">2023</option>
                                    <option value="2024">2024</option>
                                    <option value="2025">2025</option>
                                    <option value="2026">2026</option>
                                    <option value="2027">2027</option>
                                    <option value="2028">2028</option>
                                    <option value="2029">2029</option>
                                    <option value="2030">2030</option>
                                    <option value="2031">2031</option>
                                    <option value="2032">2032</option>
                                    <option value="2033">2033</option>
                                    <option value="2034">2034</option>
                                    <option value="2035">2035</option>
                                    <option value="2036">2036</option>
                                    <option value="2037">2037</option>
                                    <option value="2038">2038</option>
                                    <option value="2039">2039</option>
                                    <option value="2040">2040</option>
                                    <option value="2041">2041</option>
                                    <option value="2042">2042</option>
                                </select>
                            </div>
                            <div class="new_form-group">
                                <label for="Edit_e_cc_address">Address:</label>
                                <input tabindex="77" type="text" maxlength="255" class="form-box-textfield form-control" value="" id="Edit_e_cc_address">
                                <div id="edit-suggestions-box-cc" class="suggestions"></div>
                            </div>
                            <div class="new_form-group">
                                <label for="Edit_e_cc_state">State:</label>
                                <select tabindex="79" class="form-box-combobox e_cc_state_existing" id="Edit_e_cc_state">
                                    <option value="" selected="selected">Select State</option>
                                    <option value="AL">Alabama</option>
                                    <option value="AK">Alaska</option>
                                    <option value="AZ">Arizona</option>
                                    <option value="AR">Arkansas</option>
                                    <option value="BS">Bahamas</option>
                                    <option value="CA">California</option>
                                    <option value="CO">Colorado</option>
                                    <option value="CT">Connecticut</option>
                                    <option value="DE">Delaware</option>
                                    <option value="DC">District of Columbia</option>
                                    <option value="FL">Florida</option>
                                    <option value="GA">Georgia</option>
                                    <option value="HI">Hawaii</option>
                                    <option value="ID">Idaho</option>
                                    <option value="IL">Illinois</option>
                                    <option value="IN">Indiana</option>
                                    <option value="IA">Iowa</option>
                                    <option value="KS">Kansas</option>
                                    <option value="KY">Kentucky</option>
                                    <option value="LA">Louisiana</option>
                                    <option value="ME">Maine</option>
                                    <option value="MD">Maryland</option>
                                    <option value="MA">Massachusetts</option>
                                    <option value="MI">Michigan</option>
                                    <option value="MN">Minnesota</option>
                                    <option value="MS">Mississippi</option>
                                    <option value="MO">Missouri</option>
                                    <option value="MT">Montana</option>
                                    <option value="NE">Nebraska</option>
                                    <option value="NV">Nevada</option>
                                    <option value="NH">New Hampshire</option>
                                    <option value="NJ">New Jersey</option>
                                    <option value="NM">New Mexico</option>
                                    <option value="NY">New York</option>
                                    <option value="NC">North Carolina</option>
                                    <option value="ND">North Dakota</option>
                                    <option value="OH">Ohio</option>
                                    <option value="OK">Oklahoma</option>
                                    <option value="OR">Oregon</option>
                                    <option value="PA">Pennsylvania</option>
                                    <option value="PR">Puerto Rico</option>
                                    <option value="RI">Rhode Island</option>
                                    <option value="SC">South Carolina</option>
                                    <option value="SD">South Dakota</option>
                                    <option value="TN">Tennessee</option>
                                    <option value="TX">Texas</option>
                                    <option value="UT">Utah</option>
                                    <option value="VT">Vermont</option>
                                    <option value="VA">Virginia</option>
                                    <option value="WA">Washington</option>
                                    <option value="WV">West Virginia</option>
                                    <option value="WI">Wisconsin</option>
                                    <option value="WY">Wyoming</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="new_form-group">
                                <label for="Edit_e_cc_lname">Last Name:</label>
                                <input tabindex="71" type="text" maxlength="50" class="form-box-textfield form-control" value="" id="Edit_e_cc_lname">
                            </div>
                            <div class="new_form-group">
                                <label for="Edit_e_cc_number">Card Number:</label>
                                <input tabindex="73" class="form-box-textfield form-control e_cc_number" type="text" maxlength="16" value="" id="Edit_e_cc_number">
                                <img src="https://cargoflare.com/images/icons/cards.gif" alt="Card Types" width="129" height="16" style="vertical-align:middle;margin-top:8px;margin-left:10px;">
                                <br/>
                            </div>
                            <div class="new_form-group">
                                <label for="Edit_e_cc_cvv2">CVV:</label>
                                <input tabindex="74" class="form-box-textfield form-control" type="text" maxlength="4" value="" id="Edit_e_cc_cvv2">
                            </div>
                            <div class="new_form-group">
                                <label for="Edit_e_cc_city">City:</label>
                                <input tabindex="78" type="text" maxlength="100" class="form-box-textfield form-control" value="" id="Edit_e_cc_city">
                            </div>
                            <div class="new_form-group">
                                <label for="Edit_e_cc_zip">Zip Code:</label>
                                <input tabindex="80" class="form-box-textfield form-control" type="text" maxlength="11" value="" id="Edit_e_cc_zip">
                            </div>
                        </div>
                    </div>
                </div>
                <!-- <table class="card-details" style="width: 100%">
                    <tr>
                        <td>Card number</td>
                        <td><input type="text" id="Number" class="form-box-textfield"></td>
                    </tr>
                    <tr>
                        <td>First Name</td>
                        <td><input type="text" id="FirstName" class="form-box-textfield"></td>
                    </tr>
                    <tr>
                        <td>Last Name</td>
                        <td><input type="text" id="LastName" class="form-box-textfield"></td>
                    </tr>
                    <tr>
                        <td>Expiry Month</td>
                        <td><input type="text" id="ExpiryMonth" class="form-box-textfield"></td>
                    </tr>
                    <tr>
                        <td>Expiry Year</td>
                        <td><input type="text" id="ExpiryYear" class="form-box-textfield"></td>
                    </tr>
                    <tr>
                        <td>CVV</td>
                        <td><input type="text" id="CVV" class="form-box-textfield"></td>
                    </tr>
                    <tr>
                        <td>Type</td>
                        <td>
                            <select type="text" id="Type" class="form-box-textfield">
                                <option value="1">Visa</option>
                                <option value="2">MasterCard</option>
                                <option value="3">AMEX</option>
                                <option value="4">Discover</option>
                                <option value="0">Others</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Address</td>
                        <td colspan="5">
                            <textarea style="width:100%; height:50px;" type="text" id="Address" class="form-box-textfield"></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td>City</td>
                        <td><input type="text" id="City" class="form-box-textfield"></td>
                    </tr>
                    <tr>
                        <td>State</td>
                        <td><input type="text" id="State" class="form-box-textfield"></td>
                    </tr>
                    <tr>
                        <td>Zip code</td>
                        <td><input type="text" id="Zipcode" class="form-box-textfield"></td>
                    </tr>
                </table> -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-dark" data-dismiss="modal">Close</button>
                <button type="button" class="btn_dark_green btn-sm" onclick="SaveCardData()">Update Cards</button>
            </div>
        </div>
    </div>
</div>

<!--begin::Modal-->
<div class="modal fade" id="addCardInformation" tabindex="-1" role="dialog" aria-labelledby="addCardInformation_modal" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Add New Card</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i class="fa fa-times" aria-hidden="true"></i>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="AccountId" value="<?php echo $_GET['id']?>">
                <div class="pt-3" style="padding-left:20px;padding-right:20px;">
                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <div class="new_form-group">
                                <label for="e_cc_fname">First Name:</label>
                                <input tabindex="70" type="text" maxlength="50" class="form-box-textfield form-control" value="" id="e_cc_fname">
                            </div>
                            <div class="new_form-group ">
                                <label for="e_cc_type">Type:</label>
                                <select tabindex="72" class="form-box-combobox e_cc_type_existing" id="e_cc_type">
                                    <option value="" selected="selected">--Select--</option>
                                    <option value="1">Visa</option>
                                    <option value="2">MasterCard</option>
                                    <option value="3">Amex</option>
                                    <option value="4">Discover</option>
                                </select>
                                <br/><br><br>
                            </div>
                            <div class="new_form-group">
                                <label for="e_cc_month">Exp. Date:</label>
                                <select tabindex="75" style="width:84px;" class="form-box-combobox e_cc_month_existing" id="e_cc_month">
                                    <option value="" selected="selected">--</option>
                                    <option value="01">01</option>
                                    <option value="02">02</option>
                                    <option value="03">03</option>
                                    <option value="04">04</option>
                                    <option value="05">05</option>
                                    <option value="06">06</option>
                                    <option value="07">07</option>
                                    <option value="08">08</option>
                                    <option value="09">09</option>
                                    <option value="10">10</option>
                                    <option value="11">11</option>
                                    <option value="12">12</option>
                                </select>
                                <select tabindex="76" style="width:84px;" class="form-box-combobox e_cc_year_existing" id="e_cc_year">
                                    <option value="" selected="selected">--</option>
                                    <option value="2022">2022</option>
                                    <option value="2023">2023</option>
                                    <option value="2024">2024</option>
                                    <option value="2025">2025</option>
                                    <option value="2026">2026</option>
                                    <option value="2027">2027</option>
                                    <option value="2028">2028</option>
                                    <option value="2029">2029</option>
                                    <option value="2030">2030</option>
                                    <option value="2031">2031</option>
                                    <option value="2032">2032</option>
                                    <option value="2033">2033</option>
                                    <option value="2034">2034</option>
                                    <option value="2035">2035</option>
                                    <option value="2036">2036</option>
                                    <option value="2037">2037</option>
                                    <option value="2038">2038</option>
                                    <option value="2039">2039</option>
                                    <option value="2040">2040</option>
                                    <option value="2041">2041</option>
                                    <option value="2042">2042</option>
                                </select>
                            </div>
                            <div class="new_form-group">
                                <label for="e_cc_address">Address:</label>
                                <input tabindex="77" type="text" maxlength="255" class="form-box-textfield form-control" value="" id="e_cc_address">
                                <div id="suggestions-box-cc" class="suggestions"></div>
                            </div>
                            <div class="new_form-group">
                                <label for="e_cc_state">State:</label>
                                <select tabindex="79" class="form-box-combobox e_cc_state_existing" id="e_cc_state">
                                    <option value="" selected="selected">Select State</option>
                                    <option value="AL">Alabama</option>
                                    <option value="AK">Alaska</option>
                                    <option value="AZ">Arizona</option>
                                    <option value="AR">Arkansas</option>
                                    <option value="BS">Bahamas</option>
                                    <option value="CA">California</option>
                                    <option value="CO">Colorado</option>
                                    <option value="CT">Connecticut</option>
                                    <option value="DE">Delaware</option>
                                    <option value="DC">District of Columbia</option>
                                    <option value="FL">Florida</option>
                                    <option value="GA">Georgia</option>
                                    <option value="HI">Hawaii</option>
                                    <option value="ID">Idaho</option>
                                    <option value="IL">Illinois</option>
                                    <option value="IN">Indiana</option>
                                    <option value="IA">Iowa</option>
                                    <option value="KS">Kansas</option>
                                    <option value="KY">Kentucky</option>
                                    <option value="LA">Louisiana</option>
                                    <option value="ME">Maine</option>
                                    <option value="MD">Maryland</option>
                                    <option value="MA">Massachusetts</option>
                                    <option value="MI">Michigan</option>
                                    <option value="MN">Minnesota</option>
                                    <option value="MS">Mississippi</option>
                                    <option value="MO">Missouri</option>
                                    <option value="MT">Montana</option>
                                    <option value="NE">Nebraska</option>
                                    <option value="NV">Nevada</option>
                                    <option value="NH">New Hampshire</option>
                                    <option value="NJ">New Jersey</option>
                                    <option value="NM">New Mexico</option>
                                    <option value="NY">New York</option>
                                    <option value="NC">North Carolina</option>
                                    <option value="ND">North Dakota</option>
                                    <option value="OH">Ohio</option>
                                    <option value="OK">Oklahoma</option>
                                    <option value="OR">Oregon</option>
                                    <option value="PA">Pennsylvania</option>
                                    <option value="PR">Puerto Rico</option>
                                    <option value="RI">Rhode Island</option>
                                    <option value="SC">South Carolina</option>
                                    <option value="SD">South Dakota</option>
                                    <option value="TN">Tennessee</option>
                                    <option value="TX">Texas</option>
                                    <option value="UT">Utah</option>
                                    <option value="VT">Vermont</option>
                                    <option value="VA">Virginia</option>
                                    <option value="WA">Washington</option>
                                    <option value="WV">West Virginia</option>
                                    <option value="WI">Wisconsin</option>
                                    <option value="WY">Wyoming</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="new_form-group">
                                <label for="e_cc_lname">Last Name:</label>
                                <input tabindex="71" type="text" maxlength="50" class="form-box-textfield form-control" value="" id="e_cc_lname">
                            </div>
                            <div class="new_form-group">
                                <label for="e_cc_number">Card Number:</label>
                                <input tabindex="73" class="form-box-textfield form-control e_cc_number" type="text" maxlength="16" value="" id="e_cc_number">
                                <img src="https://cargoflare.dev/images/icons/cards.gif" alt="Card Types" width="129" height="16" style="vertical-align:middle;margin-top:8px;margin-left:10px;">
                                <br/>
                            </div>
                            <div class="new_form-group">
                                <label for="e_cc_cvv2">CVV:</label>
                                <input tabindex="74" class="form-box-textfield form-control" type="text" maxlength="4" value="" id="e_cc_cvv2">
                            </div>
                            <div class="new_form-group">
                                <label for="e_cc_city">City:</label>
                                <input tabindex="78" type="text" maxlength="100" class="form-box-textfield form-control" value="" id="e_cc_city">
                            </div>
                            <div class="new_form-group">
                                <label for="e_cc_zip">Zip Code:</label>
                                <input tabindex="80" class="form-box-textfield form-control" type="text" maxlength="11" value="" id="e_cc_zip">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-dark btn-sm" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn_dark_green btn-sm" onclick="AddCardData_send()">Add Cards</button>
            </div>
        </div>
    </div>
</div>
<!--end::Modal-->

<!--Add Card Information Dialogue-->
<script>
    function DeleteCards(CardId){

        $engine.confirm("Are you Sure? You want to delete this card?", action => {
            if (action === "confirmed") {
                $.ajax({
                    type: 'POST',
                    url: BASE_PATH + 'application/ajax/accounts.php',
                    dataType: 'json',
                    data: {
                        action: "DeleteCard",
                        CardId: CardId
                    },
                    success: function (response) {
                        location.reload();
                    }
                });
            }
        });
    }

    function EditSavedCards(CardID){
        $.ajax({
            type: 'POST',
            url: BASE_PATH + 'application/ajax/accounts.php',
            dataType: 'json',
            data: {
                action: "GetSavedCards",
                CardId: CardID
            },
            success: function (response) {
                $("#Edit_CardId").val(response.data.CardId);
                $("#Edit_e_cc_number").val(response.data.Number);
                $("#Edit_e_cc_fname").val(response.data.FirstName);
                $("#Edit_e_cc_lname").val(response.data.LastName);
                $("#Edit_e_cc_month").val(response.data.ExpiryMonth);
                $("#Edit_e_cc_year").val(response.data.ExpiryYear);
                $("#Edit_e_cc_cvv2").val(response.data.CVV);
                $("#Edit_e_cc_type").val(response.data.Type);
                $("#Edit_e_cc_address").val(response.data.Address);
                $("#Edit_e_cc_state").val(response.data.State);
                $("#Edit_e_cc_city").val(response.data.City);
                $("#Edit_e_cc_zip").val(response.data.Zipcode);
                $("#editSavedCards").modal();
            }   
        });
    }

    function SaveCardData(){

        let error = false;
        let errorMessage = "";
        
        $(".form-control").removeClass('error');
        $(".form-box-combobox").removeClass('error');

        if($("#Edit_e_cc_fname").val() == ""){
            errorMessage += "Credit Card first name is required<br>";
            error = true;
            $("#Edit_e_cc_fname").addClass("error");
        }

        if($("#Edit_e_cc_lname").val() == ""){
            errorMessage += "Credit Card last name is required<br>";
            error = true;
            $("#Edit_e_cc_lname").addClass("error");
        }

        if($("#Edit_e_cc_number").val() == ""){
            errorMessage += "Credit Card Number is required<br>";
            error = true;
            $("#Edit_e_cc_number").addClass("error");
        } else {
            if(($("#Edit_e_cc_number").val().length != 16) && Number($("#Edit_e_cc_number").val()) ){
                errorMessage += "Credit Card Number is Invalid<br>";
                error = true;
                $("#Edit_e_cc_number").addClass("error");
            }
        }

        if($("#Edit_e_cc_type").val() == ""){
            errorMessage += "Credit Card Type is required<br>";
            error = true;
            $("#Edit_e_cc_type").addClass("error");
        }

        if($("#Edit_e_cc_month").val() == ""){
            errorMessage += "Credit Card Month is required<br>";
            error = true;
            $("#Edit_e_cc_month").addClass("error");
        }

        if($("#Edit_e_cc_year").val() == ""){
            errorMessage += "Credit Card Year is required<br>";
            error = true;
            $("#Edit_e_cc_year").addClass("error");
        }

        if($("#Edit_e_cc_cvv2").val() == ""){
            errorMessage += "Credit Card CVV is required<br>";
            error = true;
            $("#Edit_e_cc_cvv2").addClass("error");
        }

        if(error == true){
            $engine.notify(errorMessage);
            return false;
        }
        
        $.ajax({
            type: 'POST',
            url: BASE_PATH + 'application/ajax/accounts.php',
            dataType: 'json',
            data: {
                action: "UpdateCardData",
                CardId: $("#Edit_CardId").val(),
                Number: $("#Edit_e_cc_number").val(),
                FirstName: $("#Edit_e_cc_fname").val(),
                LastName: $("#Edit_e_cc_lname").val(),
                ExpiryMonth: $("#Edit_e_cc_month").val(),
                ExpiryYear: $("#Edit_e_cc_year").val(),
                CVV: $("#Edit_e_cc_cvv2").val(),
                Type: $("#Edit_e_cc_type").val(),
                Address: $("#Edit_e_cc_address").val(),
                State: $("#Edit_e_cc_state").val(),
                City: $("#Edit_e_cc_city").val(),
                Zipcode: $("#Edit_e_cc_zip").val()
            },
            success: function (response) {
                $("#editSavedCards").modal('hide');
                swal.fire("Card Details Updated");
                location.reload();
            }
        });
    }

    function AddCardData() {
        $("#addCardInformation").modal();
    }

    function AddCardData_send() {

        let error = false;
        let errorMessage = "";
        
        $(".form-control").removeClass('error');
        $(".form-box-combobox").removeClass('error');

        if($("#e_cc_fname").val() == ""){
            errorMessage += "Credit Card first name is required<br>";
            error = true;
            $("#e_cc_fname").addClass("error");
        }

        if($("#e_cc_lname").val() == ""){
            errorMessage += "Credit Card last name is required<br>";
            error = true;
            $("#e_cc_lname").addClass("error");
        }

        if($("#e_cc_number").val() == ""){
            errorMessage += "Credit Card Number is required<br>";
            error = true;
            $("#e_cc_number").addClass("error");
        } else {
            if(($("#e_cc_number").val().length != 16) && Number($("#e_cc_number").val()) ){
                errorMessage += "Credit Card Number is Invalid<br>";
                error = true;
                $("#e_cc_number").addClass("error");
            }
        }

        if($("#e_cc_type").val() == ""){
            errorMessage += "Credit Card Type is required<br>";
            error = true;
            $("#e_cc_type").addClass("error");
        }

        if($("#e_cc_month").val() == ""){
            errorMessage += "Credit Card Month is required<br>";
            error = true;
            $("#e_cc_month").addClass("error");
        }

        if($("#e_cc_year").val() == ""){
            errorMessage += "Credit Card Year is required<br>";
            error = true;
            $("#e_cc_year").addClass("error");
        }

        if($("#e_cc_cvv2").val() == ""){
            errorMessage += "Credit Card CVV is required<br>";
            error = true;
            $("#e_cc_cvv2").addClass("error");
        }

        if(error == true){
            $engine.notify(errorMessage);
            return false;
        }

        $.ajax({
            type: 'POST',
            url: BASE_PATH + 'application/ajax/accounts.php',
            dataType: 'json',
            data: {
                action: "AddCards",
                AccountId: $("#AccountId").val(),
                Number : $("#e_cc_number").val(),
                FirstName : $("#e_cc_fname").val(),
                LastName : $("#e_cc_lname").val(),
                ExpiryMonth : $("#e_cc_month").val(),
                ExpiryYear : $("#e_cc_year").val(),
                CVV : $("#e_cc_cvv2").val(),
                Type : $("#e_cc_type").val(),
                Address : $("#e_cc_address").val(),
                City : $("#e_cc_city").val(),
                State : $("#e_cc_state").val(),
                Zipcode : $("#e_cc_zip").val()                
            },
            success: function (res) {
                if(res.success){
                    
                    $("#addCardInformation").modal('hide');
                    swal.fire("Card Details Added");

                    let cardType = ['other','visa','master','amex','discover'];

                    let html = `
                        <tr class="cc-row">
                            <td>${res.data.Number}</td>
                            <td>${res.data.ExpiryMonth}/${res.data.ExpiryYear}</td>
                            <td>${res.data.CVV}</td>
                            <td>${cardType[res.data.Type].toUpperCase()}</td>
                            <td>${res.data.FirstName}</td>
                            <td>${res.data.LastName}</td>
                            <td>${res.data.Address}, ${res.data.City}, ${res.data.State}<br/>${res.data.Zipcode}</td>
                            <td align="center">${res.data.Created}</td>
                            <td align="center">${res.data.Updated == null ? "" : res.data.Updated}</td>
                            <td align="center">${res.data.Recent == 0 ? "" : "Yes"}</td>
                            <td align="center">${res.data.Status == 1 ? "Active" : "In Active"}</td>
                            <td align="center" class="grid-body-right">
                                <img onclick="EditSavedCards(${res.data.CardId})" src="/images/icons/edit.png" title="Edit" alt="Edit" class="pointer" width="16" height="16">
                                <img onclick="DeleteCards(${res.data.CardId})" src="/images/icons/delete.png" title="Delete" alt="Delete" class="pointer" width="16" height="16">
                            </td>
                        </tr>
                    `;

                    $(".cc-row-empty").remove();
                    $("#shipper-cards").append(html);
                    $("#addCardInformation").modal('hide');
                }
            }
        });
    }

    function autoComplete(address, type) {
        if(address.trim() != ""){
            $.ajax({
                type: 'POST',
                url: BASE_PATH + 'application/ajax/auto_complete.php',
                dataType: 'json',
                data: {
                    action: 'suggestions',
                    address: address
                },
                success: function (response) {
                    let result = response.result;
                    let html = ``;
                    let h = null;
                    let functionName = null;
                    
                    if(type == 'cc'){
                        h = document.getElementById("suggestions-box-cc");
                        h.innerHTML = "";
                        functionName = 'applyAddressCC';
                        html += '<ul class="typeahead dropdown-menu" role="listbox" style="top: 36px; width:350px; left: 133px; display: block;">';
                        html += '<li><a href="javascript:void(0)" style="height:25px !important; padding-top:0px !important; font-size:10px;">Suggestions</a></li>';
                    }

                    if(type == 'Edit_cc'){
                        h = document.getElementById("edit-suggestions-box-cc");
                        h.innerHTML = "";
                        functionName = 'applyAddressEditCC';
                        html += '<ul class="typeahead dropdown-menu" role="listbox" style="top: 36px; width:350px; left: 133px; display: block;">';
                        html += '<li><a href="javascript:void(0)" style="height:25px !important; padding-top:0px !important; font-size:10px;">Suggestions</a></li>';
                    }

                    result.forEach( (element, index) => {

                        let address = `<strong>${element.street}</strong>,<br>${element.city}, ${element.state} ${element.zip}`;
                        
                        html += `<li>
                                    <a class="dropdown-item" href="javascript:void(0)" onclick="${functionName}('${element.street}','${element.city}','${element.state}','${element.zip}')" role="option">
                                        <p>${address}</p>
                                    </a>
                                </li>`;
                    });

                    html += `<li>
                                <a href="javascript:void(0)" style="height: 29px !important;font-size:10px;padding: 0px !important;padding-left: 10px !important; padding-top:10px !important;">Powered by
                                    &nbsp;&nbsp;&nbsp;<img alt="Cargo Flare" src="https://cargoflare.com/styles/cargo_flare/logo.png" style="width:auto;">
                                </a>
                            </li>`;
                    html += `</ul>`;
                    h.innerHTML = html;
                }
            });
        }
    }

    function applyAddressCC (address, city, state, zip) {
        $("#suggestions-box").html("");
        $("#e_cc_address").val(address);
        $("#e_cc_city").val(city);
        $("#e_cc_state").val(state);
        $("#e_cc_zip").val(zip);
        document.getElementById("suggestions-box-cc").innerHTML = "";
    }

    function applyAddressEditCC (address, city, state, zip) {
        $("#suggestions-box").html("");
        $("#Edit_e_cc_address").val(address);
        $("#Edit_e_cc_city").val(city);
        $("#Edit_e_cc_state").val(state);
        $("#Edit_e_cc_zip").val(zip);
        document.getElementById("edit-suggestions-box-cc").innerHTML = "";
    }

    $(document).ready(function() {

        $(".e_cc_number").attr("placeholder", "xxxx-xxxx-xxxx-xxxx");
        $(".e_cc_number").keyup(function(){
            function phoneFormat(card) {
                card = card.replace(/[^0-9]/g, '');
                card = card.replace(/(\d{4})(\d{4})(\d{4})(\d{4})/, "$1-$2-$3-$4");
                return card;
            }

            var card = $(this).val();
            card = phoneFormat(card);

            $(this).val(card);
        });

        // address search API key
        let timer;
        const waitTime = 1000;

        document.querySelector('#e_cc_address').addEventListener('keyup', (e) => {
            const text = e.currentTarget.value;
            clearTimeout(timer);
            timer = setTimeout(() => {
                autoComplete($("#e_cc_address").val().trim(), 'cc');
            }, waitTime);
        });

        document.querySelector('#Edit_e_cc_address').addEventListener('keyup', (e) => {
            const text = e.currentTarget.value;
            clearTimeout(timer);
            timer = setTimeout(() => {
                autoComplete($("#Edit_e_cc_address").val().trim(), 'Edit_cc');
            }, waitTime);
        });
    });
</script>

