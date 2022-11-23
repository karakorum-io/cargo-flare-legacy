<!DOCTYPE html>
<html lang="en">
    <head>
        <title>CargoFlare :: Convert to Order</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css" integrity="sha512-1sCRPdkRXhBV2PBLUdRb4tMg1w2YPf37qatUFeS7zlBy7jJI8Lf4VHwWfZZfpXtYSLy85pkm9GaYVYMfw5BC1A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <link rel="stylesheet" href="https://cargoflare.com/styles/revolution/style.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    </head>
    <body>
        <div class="content">
            <div class="row header">
                <img alt="Cargo Flare" src="https://cargoflare.com/styles/cargo_flare/logo.png" width="230">
            </div>
            <div class="container">
                <div class="row">
                    <ul class="nav nav-tabs">
                        <li class="active"><a data-toggle="tab" href="#home">Pickup</a></li>
                        <li><a data-toggle="tab" href="#menu1" id="dropoffMenu">DropOff</a></li>
                        <li><a data-toggle="tab" href="#menu2">Preview</a></li>
                        <li><a data-toggle="tab" href="#menu3">Payments</a></li>
                    </ul>

                    <div class="tab-content">
                        <div id="home" class="tab-pane fade in active">
                            <br/>
                            <h3>Pickup Information</h3>
                            <p class="text-danger">Modify pickup information if needed</p>
                            <?php
                                $origin = $this->entity->getOrigin();
                            ?>
                            <div>
                                <div class="row">
                                    <div class="col-xs-12 col-sm-3">
                                        <div class="form-group" style="margin: 0px">
                                            <label for="origin_address1">Address:</label>
                                            <input name="origin_address1" type="text"  class="form-control" id="origin_address1" value="<?php echo $origin->address?>">
                                            <div id="suggestions-box" class="suggestions">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-3">
                                        <div class="form-group ">
                                            <label for="origin_contact_name">Contact Name:</label>
                                            <input name="origin_contact_name" type="text" class="form-control" id="origin_contact_name" value="<?php echo $origin->comtact_name?>">
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-3">
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-xs-6 col-sm-8 zero-side-padding">
                                                    <label for="origin_phone1">Phone 1:</label>
                                                    <input class="form-control" tabindex="32" name="origin_phone1" type="text" maxlength="10" id="origin_phone1" placeholder="xxx-xxx-xxxx">
                                                </div>
                                                <div style="padding-left:3px; padding-right:0px;" class="col-xs-6 col-sm-4">
                                                    <label for="origin_phone1">&nbsp;</label>
                                                    <input placeholder="Ext." tabindex="32" class="form-control" name="origin_phone1_ext" type="text" maxlength="10" id="origin_phone1_ext">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-3">
                                        <div class="form-group ">
                                            <label for="origin_mobile">Mobile:</label>
                                            <input class="form-control" name="origin_mobile" type="text" maxlength="10" id="origin_mobile" placeholder="xxx-xxx-xxxx" value="<?php echo $origin->mobile?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12 col-sm-3">
                                        <div class="form-group" style="margin: 0px">
                                            <label for="origin_address2">Address 2:</label>
                                            <input name="origin_address2" type="text" class="form-control" id="origin_address2" value="<?php echo $origin->address2?>">
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-3">
                                        <div class="form-group ">
                                            <label for="origin_contact_name">Contact Name 2:</label>
                                            <input tabindex="26" name="origin_contact_name2" type="text" maxlength="255" class="form-control" id="origin_contact_name2" value="<?php echo $origin->contact_name2?>">
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-3">
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-xs-6 col-sm-8 zero-side-padding">
                                                    <label for="origin_phone1">Phone 2:</label>
                                                    <input class="form-control" tabindex="32" name="origin_phone2" type="text" maxlength="10" id="origin_phone2" placeholder="xxx-xxx-xxxx">
                                                </div>
                                                <div style="padding-left:3px; padding-right:0px;" class="col-xs-6 col-sm-4">
                                                    <label for="origin_phone1">&nbsp;</label>
                                                    <input placeholder="Ext." tabindex="32" class="form-control" name="origin_phone2_ext" type="text" maxlength="10" id="origin_phone2_ext">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-3">
                                        <div class="form-group ">
                                            <label for="origin_mobile2">Mobile 2:</label>
                                            <input class="form-control" tabindex="35" name="origin_mobile2" type="text" maxlength="10" id="origin_mobile2" placeholder="xxx-xxx-xxxx">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12 col-sm-3">
                                        <div class="form-group">
                                            <label for="origin_city"><span class="text-danger">*</span>City:</label>
                                            <input class="form-control" tabindex="20" name="origin_city" type="text" maxlength="255" id="origin_city">
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-3">
                                        <div class="form-group ">
                                            <label for="origin_company_name">Company Name:</label><span class="text-danger" id="origin_company-span" style="display:none;">*</span><input tabindex="28" name="origin_company_name" type="text" maxlength="255" class="form-control" id="origin_company_name">
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-3">
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-xs-6 col-sm-8 zero-side-padding">
                                                    <label for="origin_phone3">Phone 3:</label>
                                                    <input class="form-control" tabindex="32" name="origin_phone1" type="text" maxlength="10" id="origin_phone3" placeholder="xxx-xxx-xxxx">
                                                </div>
                                                <div style="padding-left:3px; padding-right:0px;" class="col-xs-6 col-sm-4">
                                                    <label for="origin_phone3">&nbsp;</label>
                                                    <input placeholder="Ext." tabindex="32" class="form-control" name="origin_phone1_ext" type="text" maxlength="10" id="origin_phone3_ext">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-3">
                                        <div class="form-group ">
                                            <label for="origin_fax">Fax:</label>
                                            <input class="form-control" tabindex="36" name="origin_fax" type="text" maxlength="10" id="origin_fax" placeholder="xxx-xxx-xxxx">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12 col-sm-3">
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-xs-6 col-sm-8 zero-side-padding">
                                                    <label for="origin_state"><span class="text-danger">*</span>State/Zip:</label>
                                                    <select class="form-control" name="origin_state" id="origin_state">
                                                        <option value="" selected="selected">Select One</option>
                                                        <optgroup label="United States">
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
                                                        </optgroup>
                                                        <optgroup label="Canada">
                                                            <option value="AB">Alberta</option>
                                                            <option value="BC">British Columbia</option>
                                                            <option value="MB">Manitoba</option>
                                                            <option value="NB">New Brunswick</option>
                                                            <option value="NL">Newfoundland</option>
                                                            <option value="NT">Northwest Territories</option>
                                                            <option value="NS">Nova Scotia</option>
                                                            <option value="NU">Nunavut</option>
                                                            <option value="ON">Ontario</option>
                                                            <option value="PE">Prince Edward Island</option>
                                                            <option value="QC">Quebec</option>
                                                            <option value="SK">Saskatchewan</option>
                                                            <option value="YT">Yukon</option>
                                                        </optgroup>
                                                    </select>
                                                </div>
                                                <div style="padding-left:3px; padding-right:0px;" class="col-xs-6 col-sm-4">
                                                    <label for="origin_phone1">&nbsp;</label>
                                                    <input class="form-control" tabindex="22" name="origin_zip" type="text" maxlength="10" id="origin_zip" placeholder="Zipcode" value="<?php echo $origin->zip?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-3">
                                        <div class="form-group ">
                                            <label for="origin_auction_name">Auction Name:</label><span class="text-danger" id="origin_auction-span" style="display:none;">*</span>
                                            <input tabindex="29" name="origin_auction_name" type="text" maxlength="255" class="form-control" id="origin_auction_name">
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-3">
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-xs-6 col-sm-8 zero-side-padding">
                                                    <label for="origin_phone4">Phone 4:</label>
                                                    <input class="form-control" tabindex="32" name="origin_phone4" type="text" maxlength="10" id="origin_phone4" placeholder="xxx-xxx-xxxx">
                                                </div>
                                                <div style="padding-left:3px; padding-right:0px;" class="col-xs-6 col-sm-4">
                                                    <label for="origin_phone3">&nbsp;</label>
                                                    <input placeholder="Ext." tabindex="32" class="form-control" name="origin_phone4_ext" type="text" maxlength="10" id="origin_phone4_ext">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-3">
                                        <div class="form-group ">
                                            <label for="origin_fax2">Fax2:</label>
                                            <input class="form-control" tabindex="37" name="origin_fax2" type="text" maxlength="10" id="origin_fax2" placeholder="xxx-xxx-xxxx">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12 col-sm-3">
                                        <div class="form-group ">
                                            <label for="origin_country">Country:</label>
                                            <select tabindex="23" name="origin_country" class="form-control" id="origin_country"><option value="US">United States</option><option value="CA">Canada</option></select>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-3">
                                        <div class="form-group ">
                                            <label for="origin_booking_number">Booking Number:</label>
                                            <input tabindex="30" name="origin_booking_number" type="text" maxlength="100" class="form-control" id="origin_booking_number">
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-3">
                                        <div class="form-group  ">
                                            <label for="origin_type"><span class="text-danger">*</span>Location Type  :</label><select tabindex="24" elementname="select" class="elementname form-control" onchange="origintypeselected();" name="origin_type" id="origin_type"><option value="" selected="selected">Select One</option><option value="Residential">Residential </option><option value="Commercial">Commercial</option></select>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-3">
                                        <div class="form-group ">
                                            <label for="origin_buyer_number">Buyer Number:</label>
                                            <input tabindex="31" name="origin_buyer_number" type="text" maxlength="100" class="form-control" id="origin_buyer_number">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12 col-sm-3">
                                        <div class="form-group ">
                                            <label for="origin_hours">Hours:</label><span class="text-danger" id="origin_hour" style="display:none;">*</span><input tabindex="25" name="origin_hours" type="text" maxlength="200" class="form-control" id="origin_hours">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12 col-sm-12 text-center">
                                        <button onclick="$('#dropoffMenu').trigger('click')" class="btn btn-primary">Review Drop Off</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="menu1" class="tab-pane fade">
                            <br/>
                            <h3>Drop-Off Information</h3>
                            <p class="text-danger">Modify drop-off information if needed</p>
                            <div>
                                <div class="row">
                                    <div class="col-xs-12 col-sm-3">
                                        <div class="form-group" style="margin: 0px">
                                            <label for="drop_address1">Address:</label>
                                            <input tabindex="18" name="drop_address1" type="text" maxlength="255" class="form-control" id="drop_address1">
                                            <div id="suggestions-box" class="suggestions">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-3">
                                        <div class="form-group ">
                                            <label for="drop_contact_name">Contact Name:</label>
                                            <input tabindex="26" name="drop_contact_name" type="text" maxlength="255" class="form-control" id="drop_contact_name">
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-3">
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-xs-6 col-sm-8 zero-side-padding">
                                                    <label for="drop_phone1">Phone 1:</label>
                                                    <input class="form-control" tabindex="32" name="drop_phone1" type="text" maxlength="10" id="drop_phone1" placeholder="xxx-xxx-xxxx">
                                                </div>
                                                <div style="padding-left:3px; padding-right:0px;" class="col-xs-6 col-sm-4">
                                                    <label for="drop_phone1">&nbsp;</label>
                                                    <input placeholder="Ext." tabindex="32" class="form-control" name="drop_phone1_ext" type="text" maxlength="10" id="drop_phone1_ext">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-3">
                                        <div class="form-group ">
                                            <label for="drop_mobile">Mobile:</label>
                                            <input class="form-control" tabindex="35" name="drop_mobile" type="text" maxlength="10" id="drop_mobile" placeholder="xxx-xxx-xxxx">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12 col-sm-3">
                                        <div class="form-group" style="margin: 0px">
                                            <label for="drop_address2">Address 2:</label>
                                            <input tabindex="18" name="drop_address2" type="text" maxlength="255" class="form-control" id="drop_address2">
                                            <div id="suggestions-box" class="suggestions">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-3">
                                        <div class="form-group ">
                                            <label for="drop_contact_name">Contact Name 2:</label>
                                            <input tabindex="26" name="drop_contact_name2" type="text" maxlength="255" class="form-control" id="drop_contact_name2">
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-3">
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-xs-6 col-sm-8 zero-side-padding">
                                                    <label for="drop_phone1">Phone 2:</label>
                                                    <input class="form-control" tabindex="32" name="drop_phone2" type="text" maxlength="10" id="drop_phone2" placeholder="xxx-xxx-xxxx">
                                                </div>
                                                <div style="padding-left:3px; padding-right:0px;" class="col-xs-6 col-sm-4">
                                                    <label for="drop_phone1">&nbsp;</label>
                                                    <input placeholder="Ext." tabindex="32" class="form-control" name="drop_phone2_ext" type="text" maxlength="10" id="drop_phone2_ext">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-3">
                                        <div class="form-group ">
                                            <label for="drop_mobile2">Mobile 2:</label>
                                            <input class="form-control" tabindex="35" name="drop_mobile2" type="text" maxlength="10" id="drop_mobile2" placeholder="xxx-xxx-xxxx">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12 col-sm-3">
                                        <div class="form-group">
                                            <label for="drop_city"><span class="text-danger">*</span>City:</label>
                                            <input class="form-control" tabindex="20" name="drop_city" type="text" maxlength="255" id="drop_city">
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-3">
                                        <div class="form-group ">
                                            <label for="drop_company_name">Company Name:</label><span class="text-danger" id="drop_company-span" style="display:none;">*</span><input tabindex="28" name="drop_company_name" type="text" maxlength="255" class="form-control" id="drop_company_name">
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-3">
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-xs-6 col-sm-8 zero-side-padding">
                                                    <label for="drop_phone3">Phone 3:</label>
                                                    <input class="form-control" tabindex="32" name="drop_phone1" type="text" maxlength="10" id="drop_phone3" placeholder="xxx-xxx-xxxx">
                                                </div>
                                                <div style="padding-left:3px; padding-right:0px;" class="col-xs-6 col-sm-4">
                                                    <label for="drop_phone3">&nbsp;</label>
                                                    <input placeholder="Ext." tabindex="32" class="form-control" name="drop_phone1_ext" type="text" maxlength="10" id="drop_phone3_ext">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-3">
                                        <div class="form-group ">
                                            <label for="drop_fax">Fax:</label>
                                            <input class="form-control" tabindex="36" name="drop_fax" type="text" maxlength="10" id="drop_fax" placeholder="xxx-xxx-xxxx">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12 col-sm-3">
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-xs-6 col-sm-8 zero-side-padding">
                                                    <label for="drop_state"><span class="text-danger">*</span>State/Zip:</label>
                                                    <select tabindex="21" elementname="select" class="form-control" name="drop_state" id="drop_state"><option value="" selected="selected">Select One</option><optgroup label="United States"><option value="AL">Alabama</option><option value="AK">Alaska</option><option value="AZ">Arizona</option><option value="AR">Arkansas</option><option value="BS">Bahamas</option><option value="CA">California</option><option value="CO">Colorado</option><option value="CT">Connecticut</option><option value="DE">Delaware</option><option value="DC">District of Columbia</option><option value="FL">Florida</option><option value="GA">Georgia</option><option value="HI">Hawaii</option><option value="ID">Idaho</option><option value="IL">Illinois</option><option value="IN">Indiana</option><option value="IA">Iowa</option><option value="KS">Kansas</option><option value="KY">Kentucky</option><option value="LA">Louisiana</option><option value="ME">Maine</option><option value="MD">Maryland</option><option value="MA">Massachusetts</option><option value="MI">Michigan</option><option value="MN">Minnesota</option><option value="MS">Mississippi</option><option value="MO">Missouri</option><option value="MT">Montana</option><option value="NE">Nebraska</option><option value="NV">Nevada</option><option value="NH">New Hampshire</option><option value="NJ">New Jersey</option><option value="NM">New Mexico</option><option value="NY">New York</option><option value="NC">North Carolina</option><option value="ND">North Dakota</option><option value="OH">Ohio</option><option value="OK">Oklahoma</option><option value="OR">Oregon</option><option value="PA">Pennsylvania</option><option value="PR">Puerto Rico</option><option value="RI">Rhode Island</option><option value="SC">South Carolina</option><option value="SD">South Dakota</option><option value="TN">Tennessee</option><option value="TX">Texas</option><option value="UT">Utah</option><option value="VT">Vermont</option><option value="VA">Virginia</option><option value="WA">Washington</option><option value="WV">West Virginia</option><option value="WI">Wisconsin</option><option value="WY">Wyoming</option></optgroup><optgroup label="Canada"><option value="AB">Alberta</option><option value="BC">British Columbia</option><option value="MB">Manitoba</option><option value="NB">New Brunswick</option><option value="NL">Newfoundland</option><option value="NT">Northwest Territories</option><option value="NS">Nova Scotia</option><option value="NU">Nunavut</option><option value="ON">Ontario</option><option value="PE">Prince Edward Island</option><option value="QC">Quebec</option><option value="SK">Saskatchewan</option><option value="YT">Yukon</option></optgroup></select>
                                                </div>
                                                <div style="padding-left:3px; padding-right:0px;" class="col-xs-6 col-sm-4">
                                                    <label for="drop_phone1">&nbsp;</label>
                                                    <input class="form-control" tabindex="22" name="drop_zip" type="text" maxlength="10" id="drop_zip" placeholder="Zipcode">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-3">
                                        <div class="form-group ">
                                            <label for="drop_auction_name">Auction Name:</label><span class="text-danger" id="drop_auction-span" style="display:none;">*</span>
                                            <input tabindex="29" name="drop_auction_name" type="text" maxlength="255" class="form-control" id="drop_auction_name">
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-3">
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-xs-6 col-sm-8 zero-side-padding">
                                                    <label for="drop_phone4">Phone 4:</label>
                                                    <input class="form-control" tabindex="32" name="drop_phone4" type="text" maxlength="10" id="drop_phone4" placeholder="xxx-xxx-xxxx">
                                                </div>
                                                <div style="padding-left:3px; padding-right:0px;" class="col-xs-6 col-sm-4">
                                                    <label for="drop_phone3">&nbsp;</label>
                                                    <input placeholder="Ext." tabindex="32" class="form-control" name="drop_phone4_ext" type="text" maxlength="10" id="drop_phone4_ext">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-3">
                                        <div class="form-group ">
                                            <label for="drop_fax2">Fax2:</label>
                                            <input class="form-control" tabindex="37" name="drop_fax2" type="text" maxlength="10" id="drop_fax2" placeholder="xxx-xxx-xxxx">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12 col-sm-3">
                                        <div class="form-group ">
                                            <label for="drop_country">Country:</label>
                                            <select tabindex="23" name="drop_country" class="form-control" id="drop_country"><option value="US">United States</option><option value="CA">Canada</option></select>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-3">
                                        <div class="form-group ">
                                            <label for="drop_booking_number">Booking Number:</label>
                                            <input tabindex="30" name="drop_booking_number" type="text" maxlength="100" class="form-control" id="drop_booking_number">
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-3">
                                        <div class="form-group  ">
                                            <label for="drop_type"><span class="text-danger">*</span>Location Type  :</label><select tabindex="24" elementname="select" class="elementname form-control" onchange="origintypeselected();" name="drop_type" id="drop_type"><option value="" selected="selected">Select One</option><option value="Residential">Residential </option><option value="Commercial">Commercial</option></select>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-3">
                                        <div class="form-group ">
                                            <label for="drop_buyer_number">Buyer Number:</label>
                                            <input tabindex="31" name="drop_buyer_number" type="text" maxlength="100" class="form-control" id="drop_buyer_number">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12 col-sm-3">
                                        <div class="form-group ">
                                            <label for="drop_hours">Hours:</label><span class="text-danger" id="drop_hour" style="display:none;">*</span><input tabindex="25" name="drop_hours" type="text" maxlength="200" class="form-control" id="drop_hours">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12 col-sm-12 text-center">
                                        <button onclick="$('#dropoffMenu').trigger('click')" class="btn btn-primary">Review Drop Off</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="menu2" class="tab-pane fade">
                            <br/>
                            <h3>Vehicle(s) Information</h3>
                            <p class="text-danger">Choosen vehicle(s)</p>
                            <?php
                                $vehicles = $this->entity->getVehicles();
                            ?>
                            <table class="table table-bordered table-responsive">
                                <thead>
                                    <tr>
                                        <th>S.No.</th>
                                        <th>Year</th>
                                        <th>Make</th>
                                        <th>Model</th>
                                        <th>Inop</th>
                                        <th>Type</th> 
                                        <th>Vin</th>
                                        <th>Lot</th>
                                        <th>Carrier Fee</th>
                                        <th>Loading Fee</th>
                                        <th>Total Cost</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $i = 1;
                                        foreach ($vehicles as $key => $vehicle) {
                                    ?>
                                    <tr>
                                        <td><?php echo $i;?></td>
                                        <td><?php echo $vehicle->year;?></td>
                                        <td><?php echo $vehicle->make;?></td>
                                        <td><?php echo $vehicle->model;?></td>
                                        <td><?php echo $vehicle->inop == 0 ? "No" : "Yes";?></td>
                                        <td><?php echo $vehicle->type;?></td>
                                        <td><?php echo $vehicle->vin;?></td>
                                        <td><?php echo $vehicle->lot;?></td>
                                        <td><?php echo $vehicle->carrier_pay;?></td>
                                        <td><?php echo $vehicle->deposit;?></td>
                                        <td><?php echo $vehicle->tariff;?></td>
                                    </tr>
                                    <?php
                                            $i++;
                                        }
                                    ?>
                                </tbody>
                            </table>
                            <h3>Shipping & Credit Card</h3>
                            <p class="text-danger">When will be the shipment picked up</p>
                            <div class="row">
                                <div class="col-xs-12 col-sm-3">
                                    <div class="form-group" style="margin: 0px">
                                        <label for="shipping_on">Shipping Date:</label>
                                        <input name="shipping_on" type="date" maxlength="255" class="form-control" id="shipping_on">
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-3">
                                    <div class="form-group" style="margin: 0px">
                                        <label for="cc_fname">First Name:</label>
                                        <input name="cc_fname" type="text" maxlength="255" class="form-control" id="cc_fname">
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-3">
                                    <div class="form-group" style="margin: 0px">
                                        <label for="cc_lname">Last Name:</label>
                                        <input name="cc_lname" type="text" maxlength="255" class="form-control" id="cc_lname">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12 col-sm-3">
                                    <div class="form-group" style="margin: 0px">
                                        <label for="cc_type">Type:</label>
                                        <select name="cc_type" class="form-control" id="cc_type">
                                            <option value="" selected="selected">--Select--</option>
                                            <option value="1">Visa</option>
                                            <option value="2">MasterCard</option>
                                            <option value="3">Amex</option>
                                            <option value="4">Discover</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-3">
                                    <div class="form-group" style="margin: 0px">
                                        <label for="cc_number">Card Number:</label>
                                        <input name="cc_number" type="number" maxlength="16" class="form-control" id="cc_number">
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-3">
                                    <div class="form-group" style="margin: 0px">
                                        <div class="col-xs-6 col-sm-8 zero-side-padding">
                                            <label for="cc_expiry_month">Expiry Date:</label>
                                            <select class="form-control" name="cc_expiry_month" tmaxlength="10" id="cc_expiry_month">
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
                                        </div>
                                        <div style="padding-left:3px; padding-right:0px;" class="col-xs-6 col-sm-4">
                                            <label for="cc_expiry_year">&nbsp;</label>
                                            <select class="form-control" name="cc_expiry_year" id="cc_expiry_year">
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
                                    </div>
                                </div>    
                                <div class="col-xs-12 col-sm-3">
                                    <div class="form-group" style="margin: 0px">
                                        <label for="cc_cvv">CVV:</label>
                                        <input name="cc_cvv" type="number" maxlength="5" class="form-control" id="cc_cvv">
                                    </div>
                                </div>
                            </div>
                            <br/>
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 text-center">
                                    <button class="btn btn-primary">See Offers</button>
                                </div>
                            </div>
                        </div>
                        <div id="menu3" class="tab-pane fade">
                            <div class="row">
                                <div class="box">
                                    <div class="error" onclick="editDetailsScreen()">
                                        Select new <b style="cursor: pointer" id="fsd-scroll-to-btn">First Available Pick-up Date</b> because your current one is in the past.
                                    </div>
                                    <div class="success">
                                        <b>$<b style="font-size:20px;">0</b> upfront payment required to book your shipment!</b>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 discounted-div">
                                            <div class="plans">
                                                <?php 
                                                    $amount = $this->entity->total_tariff_stored - $this->entity->carrier_pay_stored;
                                                ?>
                                                <input type="radio" name="price" value="<?php echo $amount; ?>" id="discounted-radio" onclick="setPriceType('discounted')"/>
                                                <label class="amount">$<amount><?php echo $amount; ?></amount></label>
                                                <p class="price-label">DISCOUNTED PRICE</p>
                                                <div class="inner-div">
                                                    <div class="method">
                                                        PAYMENT METHOD &nbsp;&nbsp;&nbsp;
                                                        <i class="fa fa-info-circle" aria-hidden="true" data-toggle="tooltip" title="Some information over here!"></i>
                                                    </div>
                                                    <p class="price-icons"><i class="fa fa-credit-card"></i> + <i class="fa fa-usd" aria-hidden="true"></i></p>
                                                    <p class="price-icons-labels"><b>Card and Cash</b></p>
                                                </div>
                                                <div class="last-wrapper">
                                                    <p><i class="fa fa-check" aria-hidden="true"></i> Free Cancellation</p>
                                                    <p><i class="fa fa-check" aria-hidden="true"></i> Full Insurance Coverage</p>
                                                    <p><i class="fa fa-check" aria-hidden="true"></i> Door to Door Transport</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 regular-div">
                                            <div class="plans">
                                                <?php 
                                                    $cod = $this->entity->total_tariff_stored - $this->entity->carrier_pay_stored;
                                                    $amount = $cod + $cod * (4/100); 
                                                ?>
                                                <input type="radio" name="price" value="<?php echo $amount; ?>" id="regular-radio" onclick="setPriceType('regular')"/>
                                                <label class="amount">
                                                    $<amount><?php echo $amount; ?></amount>
                                                </label>
                                                <p class="price-label">REGULAR PRICE</p>
                                                <div class="inner-div">
                                                    <div class="method">
                                                        PAYMENT METHOD &nbsp;&nbsp;&nbsp;<i class="fa fa-info-circle" aria-hidden="true" data-toggle="tooltip" title="Some information over here!"></i>
                                                    </div>
                                                    <p class="price-icons"><i class="fa fa-credit-card"></i></p>
                                                    <p class="price-icons-labels"><b>Card only</b></p>
                                                </div>
                                                <div class="last-wrapper">
                                                    <p><i class="fa fa-check" aria-hidden="true"></i> Free Cancellation</p>
                                                    <p><i class="fa fa-check" aria-hidden="true"></i> Full Insurance Coverage</p>
                                                    <p><i class="fa fa-check" aria-hidden="true"></i> Door to Door Transport</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12 col-sm-12 text-center">
                                    <button class="btn btn-primary">Book & ESign Now</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="content instructions">
            <p class="inst">… or book via: <span>(864) 546-5038</span></p>
            <p class="inst-secondary">You can change your First Available Pick-up Date at any time. <i class="fa fa-info-circle" data-toggle="tooltip" title="Restrictions may apply!"></i></p>
        </div>
        <div class="content footer">
            <div class="row">
                <p><span><i class="fa fa-hashtag" aria-hidden="true"></i>34,816</span><br/>Total satisfied customers!</p>
            </div>
        </div>
    </body>
    <script>
        const editDetailsScreen = () => {
            $("#edit-form").show();
            $("#preview-form").hide();
        }

        const openPreview =() => {
            $("#preview-form").show();
            $("#edit-form").hide();
        }

        const save = () => {
            if($("#first_avail").val() == ""){
                $(".error").show();

                setTimeout(()=>{
                    $(".error").hide();
                },2000)
            } else {
                $(".summary-price span.red").html($("#first_avail").val());
            }

            openPreview();
        }

        const setPriceType = (priceType) => {
            if(priceType == 'discounted'){
                $(".regular-div .plans").removeClass('div-active')
                $(".discounted-div .plans").addClass('div-active')
            } else {
                $(".discounted-div .plans").removeClass('div-active')
                $(".regular-div .plans").addClass('div-active')
            }
        }

        const convertToOrder = () => {
            if($("#first_avail").val() == ""){
                $(".error").show();

                setTimeout(()=>{
                    $(".error").hide();
                },2000)
            } else {
                let price = $("input[name=price]:checked").val();

                $.ajax({
                    type: "POST",
                    url: "https://cargoflare.com/quote/make_order",
                    dataType: "json",
                    data: {
                        entity_id: <?php echo $this->entity->id?>,
                        price: price,
                        avail_pickup_date: $("#first_avail").val()
                    },
                    success: function (result) {
                        if(result.success){
                            location.href = "https://cargoflare.com/quote/convert_to_order_thanks";
                        } else {
                            alert(result.message);    
                        }
                    },
                    error: function (result) {
                        alert("Something went wrong cannot convert to order!");
                    }
                });
            }
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
                        if(type == 'pickup'){
                            h = document.getElementById("suggestions-box");
                            h.innerHTML = "";
                            functionName = 'applyAddressOrigin';
                            html += '<ul class="typeahead dropdown-menu" role="listbox" style="top: 36px; width:350px; left: 133px; display: block;">';
                            html += '<li><a href="javascript:void(0)" style="height:25px !important; padding-top:0px !important; font-size:10px;">Suggestions</a></li>';
                        }
                        if(type == 'shipper'){
                            h = document.getElementById("suggestions-box-shipper");
                            h.innerHTML = "";
                            functionName = 'applyAddressShipper';
                            html += '<ul class="typeahead dropdown-menu" role="listbox" style="top: 36px; width:350px; left: 133px; display: block;">';
                            html += '<li><a href="javascript:void(0)" style="height:25px !important; padding-top:0px!important; font-size:10px;">Suggestions</a></li>';
                        }
                        if(type == 'destination'){
                            h = document.getElementById("suggestions-box-destination");
                            h.innerHTML = "";
                            functionName = 'applyAddressDestination';
                            html += '<ul class="typeahead dropdown-menu" role="listbox" style="top: 36px; width:350px; left: 133px; display: block;">';
                            html += '<li><a href="javascript:void(0)" style="height:25px !important; padding-top:0px !important; font-size:10px;">Suggestions</a></li>';
                        }
                        if(type == 'cc'){
                            h = document.getElementById("suggestions-box-cc");
                            h.innerHTML = "";
                            functionName = 'applyAddressCC';
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
                                        &nbsp;&nbsp;&nbsp;<img alt="Cargo Flare" src="https://cargoflare.com/styles/cargo_flare/logo.png" style="width:75px;">
                                    </a>
                                </li>`;
                        html += `</ul>`;
                        h.innerHTML = html;
                    }
                });
            }
        }

        function applyAddressOrigin(address, city, state, zip){
            $("#suggestions-box").html("");
            $("#origin_address1").val(address);
            $("#origin_city").val(city);
            $("#origin_state").val(state);
            $("#origin_zip").val(zip);
            document.getElementById("suggestions-box").innerHTML = "";
        }

        let BASE_PATH = "https://cargoflare.com/";
        $(document).ready(()=>{
            $("#discounted-radio").trigger('click');
            $(".error").hide();

            let timer;
            const waitTime = 1000;

            document.querySelector('#origin_address1').addEventListener('keyup', (e) => {
                const text = e.currentTarget.value;
                clearTimeout(timer);
                timer = setTimeout(() => {
                    autoComplete($("#origin_address1").val().trim(), 'pickup');
                }, waitTime);
            });
        });

        $("#origin_phone1,#origin_phone2,#origin_phone3,#origin_phone4,#origin_mobile,#origin_mobile2,#origin_fax,#origin_fax2,#shipper_phone1,#shipper_phone2,#shipper_mobile,#shipper_fax,#destination_phone1,#destination_phone2,#destination_phone3,#destination_phone4,#destination_mobile,#destination_phone4,#destination_mobile2,#destination_fax,#destination_fax2, #payable_phone").attr("placeholder", "xxx-xxx-xxxx");
        $('#origin_phone1,#origin_phone2,#origin_phone3,#origin_phone4,#origin_mobile,#origin_mobile2,#origin_fax,#origin_fax2,#shipper_phone1,#shipper_phone2,#shipper_mobile,#shipper_fax,#destination_phone1,#destination_phone2,#destination_phone3,#destination_phone4,#destination_mobile,#destination_phone4,#destination_mobile2,#destination_fax,#destination_fax2, #payable_phone').keyup(function() {

            function phoneFormat(phone) {

                phone = phone.replace(/[^0-9]/g, '');
                phone = phone.replace(/(\d{3})(\d{3})(\d{4})/, "$1-$2-$3");

                if(phone.length > 12){
                    return phone.substring(0,12);
                }

                return phone;
            }

            var phone = $(this).val();
            phone = phoneFormat(phone);

            $(this).val(phone);
        });
    </script>
</html>