<?php
$data = $this->daffny->tpl;
$ipaddress = $_SERVER['REMOTE_ADDR'];
?>
<!-- importing used external dependencies -->
<link rel="stylesheet" href="../../../styles/B2BContractUpdatePage.css">
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAZm1duQElICTt_u39BR-OA0zAwyFPuZjU&callback=initMap"></script>
<!-- importing used external dependencies ends here -->

<!-- loader & validation messages User Interface -->
<div class='loader' id='loaderOverlay'></div>
<div class='loader' id='loader'>
    <div>
        <h3>Error Messages</h3>
    </div>
    <?php
    if (count($data->errors) > 0) {
        for ($i = 0; $i < count($data->errors); $i++) {
            ?>
            <p style='line-height:0.8em;'><?php echo $data->errors[$i]; ?></p>
            <?php
        }
    }
    ?>
</div>
<!-- loader & validation messages User Interface ends here -->

<form id="contractForm" action="<?php echo $_GET['id']; ?>" method="post">
    <div class="wrapper">
        <ul>
            <center><img src="<?php echo SITE_IN . 'uploads/company/' . $data->parent_id . '.jpg'; ?>" width="200"></center>
            <div class="form-header">Commercial Account Contract</div>
            <div class="print-section">
                <table class="form-fields">
                    <colgroup>
                        <col span="2" width="50%" />
                    </colgroup>
                    <tbody>
                        <tr valign="top">
                            <td width="50%">
                                <div class="company_address_info">
                                    <span class="company_name"><?php echo $data->account->company_name; ?></span><br />
                                    <?php echo $data->account->address1; ?><br />
                                    <?php echo $data->account->city; ?>, <?php echo $data->account->state; ?> <?php echo $data->account->zip_code; ?></div>
                            </td>
                            <td width="50%" align="right">
                                <b id="ip">IP: <?php echo $ipaddress; ?></b>
                                <div id="locations">Location</div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <br>
            <dl class="form-list">       
                <li>
                    <a  id="tab1" class="toggle" href="javascript:void(0);">1. Shipper Information.</a>
                    <ul class="inner" style="display: block;">
                        <table class="form-fields" style="border:1px solid #ccc; border-radius:5px; margin-bottom:10px; padding:10px;">
                            <tbody>

                                <tr>
                                    <td class="label" >First Name: <span class="required">*</span></td>
                                    <td class="">
                                        <input class="elementname form-box-textfield" elementname="input" id="sfname" name="sfname" tabindex="1" type="text" value="<?php echo $data->account->first_name; ?>">
                                    </td>
                                    <td class="label" >Last Name: <span class="required">*</span></td>
                                    <td class=""  >
                                        <input class="elementname form-box-textfield" id="slname" name="slname" tabindex="2" type="text" value="<?php echo $data->account->last_name; ?>">
                                    </td>
                                </tr>
                                <tr>
                                    <td class="label" >Company: <span class="required">*</span></td>
                                    <td class="" >
                                        <input class="elementname form-box-textfield" elementname="input" id="scompany" name="scompany" tabindex="3" type="text" value="<?php echo $data->account->company_name; ?>">
                                    </td>
                                    <td class="label" >Shipper Type:</td>
                                    <td class="" >
                                        Commercial
                                    </td>
                                </tr>
                                <tr>
                                    <td class="label" >Hours: <span class="required">*</span></td>
                                    <td class="" >
                                        <input class="elementname form-box-textfield" elementname="input" id="shours" name="shours" tabindex="4" type="text" value="<?php echo $data->account->hours_of_operation; ?>">
                                    </td>
                                    <td class="label" >Email: <span class="required">*</span></td>
                                    <td class="" >
                                        <input class="elementname form-boxsssss-textfield" elementname="input" id="sEmail" name="sEmail" tabindex="5" type="text" value="<?php echo $data->account->email; ?>">
                                    </td>
                                </tr>
                                <tr>
                                    <td class="label" >Phone: <span class="required">*</span></td>
                                    <td class="" >
                                        <input class="elementname form-box-textfield" elementname="input" id="sphone" name="sphone" tabindex="6" type="text" value="<?php echo $data->account->phone1; ?>">
                                    </td>
                                    <td class="label" >Phone2:</td>
                                    <td class="" >
                                        <input class="elementname  form-box-textfield" elementname="input" id="sphone2" name="sphone2" tabindex="7" type="text" value="<?php echo $data->account->phone2; ?>">
                                    </td>
                                </tr>
                                <tr>
                                    <td class="label" >Mobile: </td>
                                    <td class="" >
                                        <input class="elementname form-box-textfield" elementname="input" id="sMobile" name="sMobile" tabindex="8" type="text" value="<?php echo $data->account->cell; ?>">
                                    </td>
                                    <td class="label" >Fax: </td>
                                    <td class="" >
                                        <input class="elementname form-box-textfield" elementname="input" id="sFax" name="sFax" tabindex="9" type="text" value="<?php echo $data->account->fax; ?>">
                                    </td>
                                </tr>
                                <tr>
                                    <td class="label" >Address: </td>
                                    <td class="" >
                                        <input class="elementname form-box-textfield" elementname="input" id="sAddress" name="sAddress" tabindex="10" type="text" value="<?php echo $data->account->address1; ?>">
                                    </td>
                                    <td class="label" >Address2: </td>
                                    <td class="" >
                                        <input class="elementname form-box-textfield" elementname="input" id="sAddress2" name="sAddress2" tabindex="11" type="text" value="<?php echo $data->account->address2; ?>">
                                    </td>
                                </tr>
                                <tr>
                                    <td class="label" >City: </td>
                                    <td class="" >
                                        <input class="elementname form-box-textfield" elementname="input" id="sCity" name="sCity" tabindex="12" type="text" value="<?php echo $data->account->city; ?>">
                                    </td>
                                    <td class="label" >State: </td>
                                    <td class="" >
                                        <input class="elementname form-box-textfield" elementname="input" id="sState" name="sState" tabindex="13" type="text" value="<?php echo $data->account->state; ?>">
                                    </td>
                                </tr>
                                <tr>
                                    <td class="label" >Zip:</td>
                                    <td class="" >
                                        <input class="elementname form-box-textfield" elementname="input" id="sZip" name="sZip" tabindex="14" type="text" value="<?php echo $data->account->zip_code; ?>">
                                    </td>
                                    <td class="label" >Country: </td>
                                    <td class="" >
                                        <input class="elementname form-box-textfield" elementname="input" id="sCountry" name="sCountry" tabindex="15" type="text" value="<?php echo $data->account->country; ?>">
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4" >
                                        <a class="next-btn" id="next1" onclick="openSecondSection()"> Next</a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </ul>
                </li>                
                <li> <a id="tab2" class="toggle" href="javascript:void(0);">2. Additional Company Information.</a>
                    <ul class="inner">
                        <table class="form-fields" style="border:1px solid #ccc; border-radius:5px; margin-bottom:10px; padding:10px;">
                            <tbody>
                                <tr>
                                    <td class="label" >1-First Name: <span class="required">*</span></td>
                                    <td class="">
                                        <input class="elementname form-box-textfield" elementname="input" id="fname1" maxlength="32" name="fname1" tabindex="1" type="text" >
                                    </td>
                                    <td class="label" >1-Last Name: <span class="required">*</span></td>
                                    <td class=""  >
                                        <input class="form-box-textfield" id="lname1" maxlength="255" name="lname1" tabindex="2" type="text" >
                                    </td>
                                </tr>
                                <tr>
                                    <td class="label" >1-Title: <span class="required">*</span></td>
                                    <td class="" >
                                        <input class="elementname form-box-textfield" elementname="input" id="title1" maxlength="32" name="title1" tabindex="3" type="text" >
                                    </td>
                                    <td class="label" >1-Email: <span class="required">*</span></td>
                                    <td class="" >
                                        <input class="form-box-textfield" id="email1" maxlength="255" name="email1" tabindex="4" type="text" >
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <table class="form-fields" style="border:1px solid #ccc; border-radius:5px; margin-bottom:10px; padding:10px;">
                            <tbody>
                                <tr>
                                    <td class="label" >2-First Name:</td>
                                    <td class="">
                                        <input class="elementname form-box-textfield" elementname="input" id="fname2" maxlength="32" name="fname2" tabindex="5" type="text" >
                                    </td>
                                    <td class="label" >2-Last Name:</td>
                                    <td class=""  >
                                        <input class="form-box-textfield" id="lname2" maxlength="255" name="lname2" tabindex="6" type="text" >
                                    </td>
                                </tr>
                                <tr>
                                    <td class="label" >2-Title:</td>
                                    <td class="" >
                                        <input class="elementname form-box-textfield" elementname="input" id="title2" maxlength="32" name="title2" tabindex="7" type="text" >
                                    </td>
                                    <td class="label" >2-Email:</td>
                                    <td class="" >
                                        <input class="form-box-textfield" id="email2" maxlength="255" name="email2" tabindex="8" type="text" >
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <table class="form-fields" style="border:1px solid #ccc; border-radius:5px; margin-bottom:10px; padding:10px;">
                            <tbody>
                                <tr>
                                    <td class="label" >3-First Name:</td>
                                    <td class="">
                                        <input class="elementname form-box-textfield" elementname="input" id="fname3" maxlength="32" name="fname3" tabindex="9" type="text" >
                                    </td>
                                    <td class="label" >3-Last Name:</td>
                                    <td class=""  >
                                        <input class="form-box-textfield" id="lname3" maxlength="255" name="lname3" tabindex="10" type="text" >
                                    </td>
                                </tr>
                                <tr>
                                    <td class="label" >3-Title:</td>
                                    <td class="" >
                                        <input class="elementname form-box-textfield" elementname="input" id="title3" maxlength="32" name="title3" tabindex="11" type="text" >
                                    </td>
                                    <td class="label" >3-Email:</td>
                                    <td class="" >
                                        <input class="form-box-textfield" id="email3" maxlength="255" name="email3" tabindex="12" type="text" >
                                    </td>
                                </tr>
                            </tbody>
                            <tr>
                                <td colspan="4" >
                                    <a id='next2' class="next-btn" onclick="openThirdSection()"> Next</a>
                                    <a id='prev1' class="next-btn prev-btn" onclick="openPrevious(1);"> Prev</a>
                                </td>
                            </tr>
                        </table>
                    </ul>
                </li>
                <li> <a id="tab3" class="toggle" href="javascript:void(0);">3. Payment Method.</a>
                    <ul class="inner">
                        <table class="form-fields" style="border:1px solid #ccc; border-radius:5px; margin-bottom:10px; padding:10px;">
                            <tbody>
                                <tr>
                                    <td class="label">Payment Method: <span class="required">*</span></td>
                                    <td  class="">
                                        <select class="form-box-combobox" id="payment_method" name="payment_method" tabindex="29">
                                            <option selected="selected" value="">Select One</option>
                                            <option value="1">Company Check</option>
                                            <option value="2">Wire-Check</option>
                                            <option value="3">Credit Card</option>
                                            <option value="4">ACH</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4" >
                                        <a id='next3' class="next-btn" onclick="openFourthSection()"> Next</a>
                                        <a id='prev2' class="next-btn prev-btn" onclick="openPrevious(2);"> Prev</a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <br>         
                        <dd class="ach">                    
                            <table class="form-fields" style="border:1px solid #ccc; border-radius:5px; padding:10px;">
                                <tbody>
                                    <tr>
                                        <td colspan="4" align="center"><h3>ACH Information</h3></td>
                                    </tr>
                                    <tr>
                                        <td class="label">Banking Name: <span class="required">*</span></td></td>
                                        <td  class=""><input class="form-box-textfield" id="bName" maxlength="255" name="bName" tabindex="19" type="text" ></td>
                                        <td class="label">Bank Account Number: <span class="required">*</span></td></td>
                                        <td  class=""><input class="form-box-textfield" id="bAccountNumber" maxlength="255" name="bAccountNumber" tabindex="19" type="text" ></td>
                                    </tr>
                                    <tr>
                                        <td class="label">Bank Routing Number:<span class="required">*</span></td>
                                        <td  class=""><input class="form-box-textfield" id="bRouting" maxlength="255" name="bRouting" tabindex="19" type="text" ></td>
                                        <td class="label">Bank Address:</td>
                                        <td  class=""><input class="form-box-textfield" id="bAddress" maxlength="255" name="bAddress" tabindex="19" type="text" ></td>
                                    </tr>                   
                                </tbody>
                            </table>
                        </dd>
                        <dd class="creditCardInfo">                    
                            <table class="form-fields" style="border:1px solid #ccc; border-radius:5px; padding:10px;">
                                <tbody>
                                    <tr>
                                        <td colspan="4" align="center"><h3>Credit Card Information</h3></td>
                                    </tr>
                                    <tr>
                                        <td class="label">First Name: <span class="required">*</span></td></td>
                                        <td  class=""><input class="form-box-textfield" id="ccFname" maxlength="255" name="ccFname" tabindex="19" type="text" ></td>
                                        <td class="label">Last Name: <span class="required">*</span></td></td>
                                        <td  class=""><input class="form-box-textfield" id="ccLname" maxlength="255" name="ccLname" tabindex="19" type="text" ></td>
                                    </tr>
                                    <tr>
                                        <td class="label">Type: <span class="required">*</span></td></td>
                                        <td  class="">
                                            <select class="form-box-combobox" id="ccType" name="ccType" style="width:150px;" tabindex="32">
                                                <option selected="selected" value="">--Select--</option>
                                                <option value="1">Visa</option>
                                                <option value="2">MasterCard</option>
                                                <option value="3">Amex</option>
                                                <option value="4">Discover</option>
                                            </select>
                                        </td>
                                        <td class="label">Card Number: <span class="required">*</span></td></td>
                                        <td  class=""><input class="form-box-textfield" id="ccNumber" maxlength="255" name="ccNumber" tabindex="19" type="text" ></td>
                                    </tr>
                                    <tr>
                                        <td class="label">CVV: <span class="required">*</span></td></td></td>
                                        <td  class="">
                                            <input class="cvv form-box-textfield" id="ccCvv" maxlength="4" name="ccCvv" style="width:75px;" tabindex="34" type="text" value="">
                                            <img alt="Card Types" height="16" src="/images/icons/cards.gif" style="vertical-align:middle;" width="129">
                                        </td>
                                        <td class="label">Exp. Date: <span class="required">*</span></td></td></td>
                                        <td  class="">
                                            <select class="form-box-combobox" id="ccMonth" name="ccMonth" style="width:50px;" tabindex="35">
                                                <option selected="selected" value="">--</option>
                                                <?php for ($i = 1; $i <= 12; $i++) { ?>
                                                    <option value= "<?php echo $i; ?>"><?php echo $i; ?></option>
                                                <?php } ?>
                                            </select>
                                            <select class="form-box-combobox" id="ccYear" name="ccYear" style="width:75px;" tabindex="36">
                                                <option selected="selected" value="">--</option>
                                                <?php for ($i = 2018; $i <= 2040; $i++) { ?>
                                                    <option  value= "<?php echo $i; ?>"><?php echo $i; ?></option>
                                                <?php } ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="label">Address:</td>
                                        <td  class=""><input class="form-box-textfield" id="ccAddress" maxlength="255" name="ccAddress" tabindex="19" type="text" ></td>
                                        <td class="label">City:</td>
                                        <td  class=""><input class="form-box-textfield" id="ccCity" maxlength="255" name="ccCity" tabindex="19" type="text" ></td>
                                    </tr>
                                    <tr>
                                        <td class="label">State:</td>
                                        <td  class=""><input class="form-box-textfield" id="ccState" maxlength="255" name="ccState" tabindex="19" type="text" ></td>
                                        <td class="label">Zip Code:</td>
                                        <td  class=""><input class="form-box-textfield" id="ccZip" maxlength="255" name="ccZip" tabindex="19" type="text" ></td>
                                    </tr>                                    
                                </tbody>
                            </table>
                        </dd>
                    </ul>
                </li>
                <li> <a id='tab4' class="toggle" href="javascript:void(0);">4. Additional Information</a>
                    <ul class="inner">
                        <table class="form-fields" style="border:1px solid #ccc; border-radius:5px; padding:10px;">
                            <tbody>
                                <tr>
                                    <td class="label">EIN #: <span class="required">*</span></td>
                                    <td  class=""><input class="form-box-textfield" id="ein" maxlength="255" name="ein" tabindex="19" type="text" ></td>
                                    <td class="label">DUNS: </td>
                                    <td  class=""><input class="form-box-textfield" id="duns" maxlength="255" name="duns" tabindex="19" type="text" ></td>
                                </tr>                                
                                <tr>
                                    <td colspan="4" >
                                        <a id='next4' class="next-btn"  onclick="openFiveSection()"> Next</a>
                                        <a id='prev3' class="next-btn prev-btn" onclick="openPrevious(3);"> Prev</a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </ul>
                </li>
                <li>
                    <a id='tab5' class="toggle" href="javascript:void(0);">5. Updated Commercial Contract</a>
                    <ul class="inner">
                        <div id='border:1px solid #ccc; padding:10px;'>
                            <dd style="border:1px solid #ccc; border-radius:5px; margin-bottom:10px; padding:10px; height:200px; overflow: auto;">
                                <?php echo $data->contract; ?>            
                            </dd>
                            <br>                       
                            <dd>
                                <a id='next5' class="next-btn" onclick="openSixSection()"> Next</a>
                                <a id='prev4' class="next-btn prev-btn" onclick="openPrevious(4);"> Prev</a>
                            </dd>
                        </div>
                    </ul>
                </li>
                <li>
                    <a id='tab6' class="toggle" href="javascript:void(0);">6. Agreed &amp; Accepted</a>
                    <ul class="inner">                        
                        <div style="text-align: center; border:1px solid #ccc; padding:10px; border-radius: 5px;">                
                            <div style="text-align: left;">
                                <span style="margin-bottom: 10px; ">
                                    <input class="field checkbox" id="terms" name="terms" required="required" type="checkbox" value="1" tabindex="41">
                                    (box for customer to click)<br>
                                    <b> IMPORTANT I have read, and understand, the above Terms and Conditions and I intend, and agree, to be bound by them.</b>
                                </span>
                                <br><br>
                                <span style="margin-bottom: 10px; ">
                                    <input class="field checkbox" id="signature_terms" name="signature_terms" required="required" tabindex="42" type="checkbox" value="1">
                                    (box for customer to click)<br>
                                    <b> IMPORTANT It is my intent and understanding, that my electronic signature has the same force and effect as my original signature.</b>
                                </span>
                            </div>
                            <br>
                            <div>
                                <table style=" border-radius:5px; padding:10px; margin: 0 auto;">                                    
                                    <tr>
                                        <td align='left'>Enter Your Name <span class="required">*</span> </td>
                                        <td><input style="margin-left:30px;" class="form-box-textfield latin" id="sign_name" maxlength="64" name="sign_name" size="50" type="text" required></td>
                                        <td><input class="submit-btn" name="submit" type="submit" value="eSign Now"></td>                                         
                                    </tr>
                                </table>
                            </div>
                            <br>
                        </div>
                    </ul>
                </li>
        </ul>
    </div>
</form>
<script>    
<?php
if (count($data->errors) > 0) {
    ?>
$(".loader").show();
    <?php
}
?>
</script>
<script src="../../../jscripts/B2BContractUpdate.js"></script>