<p>@content@</p>
<br/>
@flash_message@
<script type="text/javascript">


    function showStep(step) {
        $('#registration_errors ul').empty();
        var data = {
            page: step
        };
        switch (step) {
            case 1:
                break;
            case 2:
                data['products'] = $('#products').val();
                data['storages'] = $('#storages').val();
                data['addon_aq'] = $('#addon_aq').val();
                data['additional'] = $('#additional').val();
                data['additional_number'] = $.trim($('#additional_number').val());
                data['contactname'] = $.trim($('#contactname').val());
                data['companyname'] = $.trim($('#companyname').val());
                data['email'] = $.trim($('#email').val());
                data['phone'] = $.trim($('#phone').val());
                data['address'] = $.trim($('#address').val());
                data['city'] = $.trim($('#city').val());
                data['state'] = $.trim($('#state').val());
                data['zip'] = $.trim($('#zip').val());
                data['username'] = $.trim($('#username').val());
                data['password'] = $.trim($('#password').val());
                data['password_confirm'] = $.trim($('#password_confirm').val());
                data['password_hint'] = $.trim($('#password_hint').val());
                break;
            default:
                data['products'] = $.trim($('#products').val());
                data['storages'] = $.trim($('#storages').val());
                data['addon_aq'] = $.trim($('#addon_aq').val());
                data['additional'] = $.trim($('#additional').val());
                data['additional_number'] = $.trim($('#additional_number').val());
                data['card_first_name'] = $.trim($('#card_first_name').val());
                data['card_last_name'] = $.trim($('#card_last_name').val());
                data['card_number'] = $.trim($('#card_number').val());
                data['card_type'] = $.trim($('#card_type').val());
                data['card_cvv2'] = $.trim($('#card_cvv2').val());
                data['card_expire_month'] = $.trim($('#card_expire_month').val());
                data['card_expire_year'] = $.trim($('#card_expire_year').val());
                data['billing_address'] = $.trim($('#billing_address').val());
                data['billing_city'] = $.trim($('#billing_city').val());
                data['billing_state'] = $.trim($('#billing_state').val());
                data['billing_zip'] = $.trim($('#billing_zip').val());
                data['ref_code'] = $.trim($('#ref_code').val());
                data['did_any_help'] = $.trim($('input[name=did_any_help]:checked').val());
                data['who_help'] = $.trim($('#who_help').val());
                data['coupon_code'] = $.trim($('#coupon_code').val());
                break;
        }
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: '<?= SITE_IN ?>cp/registration/ajax',
            data: data,
            success: function (res) {
                if (res.success) {
                    if (step == 3) {
                        $('#order').html(res.data);
                        if (res.coupon_success != "") {
                            alert(res.coupon_success);
                        }
                    }
                    $('#registration_errors').hide();
                    $("#registration_form").accordion('activate', step - 1);
                } else {
                    if (res.errors != undefined) {
                        for (i in res.errors) {
                            $('#registration_errors ul').append('<li>' + res.errors[i] + '</li>');
                            $('#' + i).addClass('error');
                        }
                        $('#registration_errors').show();
                        $(document).scrollTo('#registration_errors', 300);
                    }
                }
            },
            error: function (res) {
                alert('Request failed. Try again later, please');
            }
        });
    }
    $(document).ready(function () {
        $('#additional_number').keyup(function () {
            if ($(this).val() != 0 && $.trim($(this).val()) != '') {
                $('.additional-license').show();
            } else {
                $('.additional-license').hide();
            }
        });
        $('#products').change(function () {
            var period = $(this).find('option:selected').attr('data');
            $('#additional option').attr('disabled', 'disabled');
            $('#additional option[data="' + period + '"]').attr('disabled', null).attr('selected', 'selected');

            $('#storages option').attr('disabled', 'disabled');
            $('#storages option[data="' + period + '"]').attr('disabled', null).attr('selected', 'selected');
            $('#storages option[data="0"]').attr('disabled', null);

            $('#addon_aq option').attr('disabled', 'disabled');
            if ($('#addon_aq').val() > 0 ){
                $('#addon_aq option[data="' + period + '"]').attr('disabled', null).attr('selected', 'selected');
                $('#addon_aq option[data="0"]').attr('disabled', null);
            }else{
                $('#addon_aq option[data="' + period + '"]').attr('disabled', null);
                $('#addon_aq option[data="0"]').attr('disabled', null).attr('selected', 'selected');
            }
        });

        $('#products').change();
        $("#registration_form").accordion({
            autoHeight: false
        });
        $("#billing_same").click(function () {
            if ($(this).is(':checked')) {
                $('#billing_address').val($('#address').val());
                $('#billing_address2').val($('#address2').val());
                $('#billing_city').val($('#city').val());
                $('#billing_state').val($('#state').val());
                $('#billing_zip').val($('#zip').val());
            } else {
                $('#billing_address').val('');
                $('#billing_address2').val('');
                $('#billing_city').val('');
                $('#billing_state').val('');
                $('#billing_zip').val('');
            }
        });
        $('#billing_table input:text, #billing_table select').change(function () {
            $('#billing_same').attr('checked', null);
        });
        $('#information_table input:text, #information_table select').change(function () {
            $('#billing_same').attr('checked', null);
        });
        $(".ui-accordion-header").unbind('click');
        $('.cvv-hint').click(function () {
            $(this).next().toggle();
        });
        $('#card_number').mask('999999999999?9999');
        $('#card_cvv2').mask('999?9');
        <?php
        if (count($_POST) > 0) {
                ?>
        $("#registration_form").accordion('activate', 2);
        <?php
}
?>
        $('#did_any_help_yes,#did_any_help_no').click(showhidehelp);
        function showhidehelp() {
            if ($('#did_any_help_yes').attr('checked')) {
                $('#did_any_help_block').show();
            }
            else {
                $('#did_any_help_block').hide();
            }
        }


    });
</script>
<div id="registration_errors" class="msg-error" style="display: none;">
    <ul class="msg-list"></ul>
</div>
<form action="<?= getLink("registration") ?>" method="post">
<div id="registration_form">
<h3>Step 1: Registration</h3>

<div>
    <?= formBoxStart("License") ?>
    <table cellpadding="0" cellspacing="3" border="0">
        <tr>
            <td><label for="products">License Type:</label></td>
            <td>
                <select name="products" id="products" class="form-box-combobox" style="width:350px;">
                    <?php foreach ($this->products as $key => $pr) { ?>
                        <option value="<?= $key ?>" data="<?= $pr['period'] ?>"><?= $pr['name'] ?></option>
                    <?php } ?>
                </select>
            </td>
        </tr>
        <tr>
            <td><label for="storages">Storage Space:</label></td>
            <td>
                <select name="storages" id="storages" class="form-box-combobox" style="width:350px;">
                    <option value="" data="0"><?=License::DEFAULT_STORAGE_NAME?> ($0.00)</option>
                    <?php foreach ($this->storages as $key => $st) { ?>
                        <option value="<?= $key ?>" data="<?= $st['period'] ?>"><?= $st['name'] ?></option>
                    <?php } ?>
                </select>
            </td>
        </tr>
        <?php if (count($this->additional) > 0) { ?>
            <tr style="display: none" class="additional-license">
                <td><label for="additional">Additional License:</label></td>
                <td>
                    <select name="additional" id="additional" class="form-box-combobox" style="width:350px;">
                        <?php foreach ($this->additional as $key => $add) { ?>
                            <option value="<?= $key ?>" data="<?= $add['period'] ?>"><?= $add['name'] ?></option>
                        <?php } ?>
                    </select>
                </td>
            </tr>
        <?php } ?>

        <tr>
            <td>@additional_number@</td>
        </tr>
        <tr>
            <td colspan="2">
                <br />
                <h4 style="color:#3B67A6">Addons</h4>
            </td>
        </tr>
        <tr>
            <td><label for="addon_aq">Automate Quoting:</label></td>
            <td>
                <select name="addon_aq" id="addon_aq" class="form-box-combobox" style="width:350px;">
                    <option value="" data="0">--None--</option>
                    <?php foreach ($this->addon_aq as $key => $st) { ?>
                        <option value="<?= $key ?>" data="<?= $st['period'] ?>"><?= $st['name'] ?></option>
                    <?php } ?>
                </select>
            </td>
        </tr>


    </table>
    <?= formBoxEnd() ?>
    <br />
    <?= formBoxStart("Contact information") ?>
    <table cellpadding="0" cellspacing="3" border="0" id="information_table">
        <tr>
           <td><b>Plan:</b></td><td><b><?= $this->PlanInfo ?></b></td>
        </tr>
        <tr>
            <td>@contactname@</td>
        </tr>
        <tr>
            <td>@companyname@</td>
        </tr>
        <tr>
            <td>@type@</td>
        </tr>
        <tr>
            <td>@address@</td>
        </tr>
        <tr>
            <td>@address2@</td>
        </tr>
        <tr>
            <td>@city@</td>
        </tr>
        <tr>
            <td>@state@</td>
        </tr>
        <tr>
            <td>@zip@</td>
        </tr>
        <tr>
            <td>@phone@</td>
        </tr>
        <tr>
            <td>@fax@</td>
        </tr>
    </table>
    <?= formBoxEnd() ?>
    <br/>
    <?= formBoxStart("Login information") ?>
    <table cellpadding="0" cellspacing="3" border="0">
        <tr>
            <td>@username@</td>
        </tr>
        <tr>
            <td>@email@</td>
        </tr>
        <tr>
            <td valign="top">@password@<br/><em>(6-10 characters containing at least 1 alpha, 1 numeric and 1 special
                    character) – list special characters \%$^&*( )<>?/</em></td>
        </tr>
        <tr>
            <td nowrap="nowrap" style="width:120px;">@password_confirm@</td>
        </tr>
        <tr>
            <td valign="top">@password_hint@ <br/><em>In case you forget your password your hint can be helpful to
                    recall it.</em></td>
        </tr>
        <tr>
            <td colspan="2"></td>
        </tr>

    </table>
    <?php echo formBoxEnd(); ?>
    <br/>
    <?= formBoxStart("Referral Code") ?>
    <table cellpadding="0" cellspacing="3" border="0">
        <tr>
            <td>@ref_code@</td>
        </tr>
    </table>
    <?php echo formBoxEnd(); ?>
    <div class="button-center">
        <?= functionButton('Continue to Payment Information', 'showStep(2)'); ?>
    </div>
</div>
<h3>Step 2: Payment Information</h3>

<div>
    <div class="button-center">
        <?= functionButton('Back to Registration', 'showStep(1)'); ?>
    </div>
    <?= formBoxStart("Credit Card") ?>
    <table cellspacing="3" cellpadding="0" border="0">
        <tr>
            <td>@card_first_name@</td>
        </tr>
        <tr>
            <td>@card_last_name@</td>
        </tr>
        <tr>
            <td>@card_number@</td>
        </tr>
        <tr>
            <td>@card_type@</td>
        </tr>
        <tr>
            <td>@card_expire_month@@card_expire_year@&nbsp;&nbsp;&nbsp;&nbsp;@card_cvv2@</td>
        </tr>
        <tr>
            <td colspan="2">
                <span class="like-link cvv-hint">What is CVV?</span>

                <div class="hint" style="width: 400px;">
                    <strong>The Card Verification Value (CVV*)</strong> is an extra code printed on your debit or credit
                    card.<br/>
                    <br/><br/>
                    CVV for Visa, MasterCard and Diners is the final three digits of the number printed on the signature
                    strip on the back of your card.<br/>
                    <img src="<?= SITE_IN ?>images/cvv-visa.gif" alt="CVV Visa"/><br/><br/>
                    CVV for American Express appears as a separate 4-digit code printed on the front of your card.<br/>
                    <img src="<?= SITE_IN ?>images/cvv-amex.gif" alt="CVV American Express"/>
                </div>
            </td>
        </tr>
    </table>
    <?= formBoxEnd() ?>
    <br/>
    <?= formBoxStart("Billing Address") ?>
    <table cellpadding="0" cellspacing="3" border="0" id="billing_table">
        <tr>
            <td colspan="2">@billing_same@</td>
        </tr>
        <tr>
            <td>@billing_address@</td>
        </tr>
        <tr>
            <td>@billing_address2@</td>
        </tr>
        <tr>
            <td>@billing_city@</td>
        </tr>
        <tr>
            <td>@billing_state@</td>
        </tr>
        <tr>
            <td>@billing_zip@</td>
        </tr>
    </table>
    <?= formBoxEnd() ?>
    * Your credit card will not be processed until you click "Place Order" on the next page.<br/>
    ** Coupon code can be entered on the next page.
    <div class="button-center">
        <?= functionButton('Review Order', 'showStep(3)', 'width:100px;margin:0 auto;'); ?>
    </div>
</div>
<h3>Step 3: Checkout</h3>

<div>
    <div class="button-center">
        <?= functionButton('Back to Payment Information', 'showStep(2)'); ?>
    </div>
    <div id="order"></div>
    <br/><br/>
    <table cellpadding="0" cellspacing="3" border="0" id="billing_table">
        <tr>
            <td><input type="text" class="form-box-textfield" id="coupon_code" name="coupon_code"
                       value="<?= post_var("coupon_code") ?>" style="width:60px" maxlength="6"/></td>
            <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
            <td><?=functionButton("Apply Coupon", "showStep(3)");?></td>
        </tr>
    </table>
    <br/><br/>
    Did any Customer Service Representative help you during decision-making process?
    <input type="radio" name="did_any_help" id="did_any_help_yes"
           value="1"<?php echo (post_var("did_any_help") == 1 ? ' checked="checked"' : ''); ?> /><label
        for="did_any_help_yes">Yes</label>
    <input type="radio" name="did_any_help" id="did_any_help_no"
           value="0"<?php echo (post_var("did_any_help") == "0" ? ' checked="checked"' : ''); ?> /><label
        for="did_any_help_no">No</label>
    <br/>

    <div id="did_any_help_block" style="padding: 5px; display: none;">@who_help@</div>
    <br/><br/>
    Upon license expiration credit card used to place this order will be automatically charged for another billing
    cycle. You can update/change credit card information at any time.
    <br/><br/>
    @i_agree@ <a style="text-decoration: underline;" href="<?= getLink("terms") ?>" target="_blank">Terms of
        Service.</a>
    <br/><br/>
    <?= submitButtons(getLink(), 'Place Order'); ?>
</div>
<br/>
</div>
</form>