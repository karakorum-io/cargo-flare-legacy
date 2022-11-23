<? include(TPL_PATH . "myaccount/menu.php"); ?>

<div class="row">
	<div class="col-6">
		<div class="kt-portlet">
		
			<div class="kt-portlet__head">
				<div class="kt-portlet__head-label">
					<?= formBoxStart("My current license") ?>
				</div>
			</div>
			
			<div class="kt-portlet__body">
				<table class="table table-bordered">
					<tr>
						<td><strong>Status:</strong></td>
						<td>@is_frozen@</td>
					</tr>
					<tr>
						<td><strong>Current License:</strong></td>
						<td>@license_name@</td>
					</tr>
					<tr>
						<td><strong>Files storage:</strong></td>
						<td>@storage_name@</td>
					</tr>
					<tr>
						<td><strong>Used/Rest space:</strong></td>
						<td><span style="color:#BB0000;">@used_space@</span> / <span style="color:green;">@rest_space@</span></td>
					</tr>
					<tr>
						<td>
							Automate Quoting:
							<? if ($this->buy_addon_aq){ ?>
								<? if (!$this->expired){ ?>
								(<a href="#" onClick="buyaddon_aq(); return false;">Buy addon</a>)
								<? } ?>
								<div id="addon_aq_div">
									<form id="buy_addon_aq_form" action="<?= getLink("billing", "buyaddonaq"); ?>" method="post">
										<table cellspacing="5" cellpadding="5" border="0" width="100%">
											<tr>
												<td colspan="2">
													<strong>@addon_aq_name_for_period@ - $@addon_aq_price_for_period@</strong>
												</td>
											</tr>
											<tr>
												<td><label>Current price until the end of the payment period:</label></td>
												<td style="text-align:right">$@until_price_for_addon_aq@</td>
											</tr>
											<tr class="grid-body">
												<td><label>Total Amount:</label></td>
												<td style="text-align:right">$<span id="buy_addon_aq_total">0.00</span></td>
											</tr>
											<tr>
												<td><label>Current Balance:</label></td>
												<td style="text-align:right">$<span id="currentbalance_addon_aq">@current_balance@</span></td>
											</tr>
											<tr class="add_addon_aq_pay">
												<td><label>Charge Amount:</label></td>
												<td style="text-align:right;">$<span id="add_addon_aq_to_pay">0.00</span></td>
											</tr>
											<tr class="add_addon_aq_pay">
												<td>@addon_aq_billing_cc_id@</td>
											</tr>
										</table>
									</form>
								</div>								
							<? } ?>
						</td>
						<td style="color:#000; font-weight: bold;">@addon_aq_name@</td>
					</tr>
					
					<tr class="grid-body">
						<td>
							Additional Users: <? if (!$this->expired){ ?> (<a href="#" onClick="changeusers(); return false;">Buy additional</a>) <? } ?>
							<div id="usersdiv" style="display:none;">
								<form id="buyform" action="<?= getLink("billing", "buyadditional"); ?>" method="post">
									<table cellspacing="5" cellpadding="5" border="0" width="100%">
										<tr>
											<td colspan="2">
												<strong>@license_name_for_users@ ($@license_price_for_users@ / per user )</strong>
											</td>
										</tr>

										<tr>
											<td><label>Qty users:</label></td>
											<td style="text-align:right">
												<input type="text" maxlength="3" style="width:70px; text-align: right;"class="digit-only form-box-textfield" name="buyusers" id="buyusers" value="0" onkeyup="recalculateTotalForAdditionals();"/>
											</td>
										</tr>
										<tr>
											<td><label>Current price until the end of the payment period:</label></td>
											<td style="text-align:right">$@until_price_for_users@</td>
										</tr>
										<tr class="grid-body">
											<td><label>Total Amount:</label></td>
											<td style="text-align:right">$<span id="buyuserstotal">0.00</span></td>
										</tr>
										<tr>
											<td><label>Current Balance:</label></td>
											<td style="text-align:right">$<span id="currentbalance">@current_balance@</span></td>
										</tr>
										<tr class="add_user_pay">
											<td><label>Charge Amount:</label></td>
											<td style="text-align:right;">$<span id="add_user_to_pay">0.00</span></td>
										</tr>
										<tr class="add_user_pay">
											<td>@billing_cc_id@</td>
										</tr>
									</table>
								</form>
							</div>
						</td>
						<td style="color:#000; font-weight: bold;">@additional_users@</td>
					</tr>
					
					
					<tr class="grid-body">
						<td>Expire:</td>
						<td style="color:#000; font-weight: bold;"> @license_expire@
							<? if ($this->expired) { ?>
								<span style="color:red;">Expired</span>
							<? } ?>
						</td>
					</tr>
					<tr class="grid-body">
						<td>
							Renewal product for next billing period:<br/> (<a href="#" onClick="changerenewals(); return false;">change</a>)
							<div id="renewalsdiv" style="display:none;">
								<form action="<?= getLink("billing", "changelicense") ?>" id="changelicenseform" method="post">
									<input type="hidden" name="changelicense" value="changelicense">
									<strong>Next Billing Period:</strong>
									<table cellspacing="5" cellpadding="5" border="0" width="100%">
										<tr>
											<td nowrap="nowrap" style="width:120px;"><label for="products">License Type:</label>
											</td>
											<td>
												<select name="products" id="products" class="form-box-combobox"
														style="width:350px;">
													<?php foreach ($this->products as $key => $pr) { ?>
														<option
															value="<?= $key ?>" <?=(($key == $this->renewal_product_id) ? "selected=\"selected\"" : "")?>
															data="<?= $pr['period'] ?>"><?= $pr['name'] ?></option>
													<?php } ?>
												</select>
											</td>
										</tr>
										<tr>
											<td nowrap="nowrap"><label for="additional">Storage space:</label></td>
											<td>
												<select name="storages" id="storages" class="form-box-combobox" style="width:350px;">
													<option value="" data="0"><?=License::DEFAULT_STORAGE_NAME?> ($0.00)</option>
													<?php foreach ($this->storages as $key => $st) { ?>
														<option value="<?= $key ?>"
																data="<?= $st['period'] ?>"><?= $st['name'] ?></option>
													<?php } ?>
												</select>
											</td>
										</tr>
										<?php if (count($this->additional) > 0) { ?>
											<tr style="<?= ($this->additional_number > 0 ? "" : "display: none") ?>"
												class="additional-license">
												<td nowrap="nowrap"><label for="additional">Additional License:</label></td>
												<td>
													<select name="additional" id="additional" class="form-box-combobox"
															style="width:350px;">
														<?php foreach ($this->additional as $key => $add) { ?>
															<option value="<?= $key ?>"
																	data="<?= $add['period'] ?>"><?= $add['name'] ?></option>
														<?php } ?>
													</select>
												</td>
											</tr>
										<?php } ?>
										<tr>
											<td>@additional_number@ &nbsp;<img src="<?= SITE_IN ?>images/icons/ajax-loader.gif"
																			   id="loadingimg" style="display:none;"/></td>
										</tr>
										<tr id="reduceusers" style="<?= ($this->shownote === true ? "" : "display:none") ?>">
											<td colspan="2">
												<em class="green">Note: If you will reduce the number of additional licenses - the
													following users will become inactive:</em>

												<div id="next_inactive_users" style="font-weight: bold;">@next_inactive_users@</div>
											</td>
										</tr>
										<tr>
											<td>
												<label for="addon_aq">Automate Quoting:</label>
											</td>
											<td>
												<select name="addon_aq" id="addon_aq" class="form-box-combobox" style="width:350px;">
													<option value="" data="0">--None--</option>
													<?php foreach ($this->addon_aq as $key => $st) { ?>
														<option <?=(($key == $this->renewal_addon_aq_id) ? "selected=\"selected\"" : "")?>  value="<?= $key ?>" data="<?= $st['period'] ?>"><?= $st['name'] ?></option>
													<?php } ?>
												</select>
											</td>
										</tr>
									</table>
								</form>
							</div>
						</td>
						<td style="color:#000; font-style:italic;">
							@renewal_name@<br/>
							@renewal_users@ Additional user(s)<br/>
							@renewal_storage_name@<br/>
							@renewal_addon_aq_name@<br/>
						</td>
					</tr>
					<tr class="grid-body">
						<td>Next @license_payment_type@:</td>
						<td style="color:#000; font-style:italic">$@license_payment@</td>
					</tr>
					<? if ($this->expired) { ?>
						<tr class="grid-body">
							<td colspan="2"><?= simpleButton("Renew", getLink("billing", "renew")); ?></td>
						</tr>
					<? } ?>
					 
					
				</table>
			</div>
			
			<?= formBoxEnd() ?>
		</div>
		<?php // kt portlet END ?>
		
		
	</div> <?php // COL-6-END ?>
	
	<div class="col-6">
	
		<div class="kt-portlet">
		
			<div class="kt-portlet__head">
				<div class="kt-portlet__head-label">
					<?= formBoxStart("Account status") ?>
				</div>
			</div>
			
			<div class="kt-portlet__body">
				<table class="table table-bordered">
					<tr>
						<td>Current Balance:</td>
						<td style="font-size:24px;"><strong style="@bal_style@">$@current_balance@</strong></td>
					</tr>
					<tr>
						<td>Last Payment Received:</td>
						<td>$@last_payment_amount@</td>
					</tr>
					<tr>
						<td>Last Payment Date:</td>
						<td>@last_payment_date@</td>
					</tr>
					<tr>
						<td>Next Billing Date:</td>
						<td><strong>@next_billing_date@</strong></td>
					</tr>
					<tr>
						<td colspan="2"><?= simpleButton("Replenish Balance", getLink("billing", "onetime")); ?></td>
					</tr>
				</table>
			</div>
			
			<?= formBoxEnd() ?>
		</div>
		<?php // kt portlet END ?>
		
		<div class="kt-portlet">
		
			<div class="kt-portlet__head">
				<div class="kt-portlet__head-label" style="width:100%;">
					<div class="row" style="width:calc(100% + 20px);">
						<div class="col-6"><?= formBoxStart("Credit Cards") ?></div>
						<div class="col-6 text-right"><?= functionButton("Add New", "addcreditcard();",'','btn-sm pull-right btn_dark_green'); ?></div>
					</div>
				</div>
			</div>
			
			<div class="kt-portlet__body">
				<div align="right" style="clear:both; padding-bottom:5px;">
					
				</div>
				<div id="cards">
					<? include(TPL_PATH . "myaccount/billing/cards.php"); ?>
				</div>
				<div id="ccdiv" style="display:none;">
					<table cellspacing="2" cellpadding="0" border="0">
						<tr>
							<td>@cc_fname@</td>
						</tr>
						<tr>
							<td>@cc_lname@</td>
						</tr>
						<tr>
							<td>@cc_type@</td>
						</tr>
						<tr>
							<td>@cc_number@</td>
						</tr>
						<tr>
							<td>@cc_cvv2@ <img src="<?= SITE_IN ?>images/icons/cards.gif" alt="Card Types" width="129" height="16"
											   style="vertical-align:middle;"/></td>
						</tr>
						<tr>
							<td>@cc_month@ / @cc_year@</td>
						</tr>
						<tr>
							<td>@cc_address@</td>
						</tr>
						<tr>
							<td>@cc_city@</td>
						</tr>
						<tr>
							<td>@cc_state@</td>
						</tr>
						<tr>
							<td>@cc_zip@</td>
						</tr>
					</table>
				</div>
			</div>
			<?= formBoxEnd() ?>
		</div>
		<?php // kt portlet END ?>
		
	</div> <?php // COL-6-END ?>
	
	
</div>


<div class="kt-portlet">
		
	<div class="kt-portlet__head">
		<div class="kt-portlet__head-label">
			<?= formBoxStart("Recent Transactions") ?>
		</div>
	</div>
	
	<div class="kt-portlet__body">
		<table class="table table-bordered">
			<thead>
				<tr>
					<th>Date</th>
					<th>Description</th>
					<th>Type</th>
					<th>Amount</th>
				</tr>
			</thead>
			<tbody>
			<? if (count($this->transactions) > 0) { ?>
				<? foreach ($this->transactions as $i => $t) { ?>
				<tr id="row-<?= $t->id ?>">
					<td class="grid-body-left"><?= $t->added ?></td>
					<td><?= htmlspecialchars($t->description) ?></td>
					<td><?= colorBillingType(Billing::$type_name[$t->type]) ?></td>
					<td>$<?= ($t->type == 2 ? "-" : "") ?><?= number_format($t->amount, 2, ".", ",") ?></td>
				</tr>
				<? } ?>
			<? } else { ?>
				<tr class="grid-body first-row" id="row-1">
					<td class="grid-body-left">&nbsp;</td>
					<td colspan="2" align="center">Records not found.</td>
					<td class="grid-body-right">&nbsp;</td>
				</tr>
			<? } ?>
			</tbody>
		</table>
		
		<a href="<?= getLink("billing", "history") ?>">View full History</a>
		
	</div>
	
	<?= formBoxEnd() ?>
</div>


<script type="text/javascript">//<![CDATA[
var flds = ["cc_fname", "cc_lname", "cc_address", "cc_city", "cc_state", "cc_zip", "cc_cvv2", "cc_number", "cc_type", "cc_month", "cc_year"];
var ID = 0;
var TOTALBUYUSERS = 0;
var CURRENTBALANCE = '@current_balance@';
CURRENTBALANCE = CURRENTBALANCE * 1;

$("#ccdiv").dialog({
    modal: true,
    width: 400,
    height: 380,
    title: "Add card",
    hide: 'fade',
    resizable: false,
    draggable: false,
    autoOpen: false,
    buttons: {
        "Submit": function () {
            $.ajax({
                url: BASE_PATH + 'application/ajax/cards.php',
                data: {  action: "save", id: ID, cc_fname: $('#cc_fname').val(), cc_lname: $('#cc_lname').val(), cc_address: $('#cc_address').val(), cc_city: $('#cc_city').val(), cc_state: $('#cc_state').val(), cc_zip: $('#cc_zip').val(), cc_number: $('#cc_number').val(), cc_month: $('#cc_month').val(), cc_year: $('#cc_year').val(), cc_type: $('#cc_type').val(), cc_cvv2: $('#cc_cvv2').val()

                },
                type: 'POST',
                dataType: 'json',
                beforeSend: function () {
                    if (!validateCCform()) {
                        return false;
                    } else {
                        // // $("body").nimbleLoader("show");
                    }
                },
                success: function (response) {
                    // $("body").nimbleLoader("hide");
                    if (response.success == true) {
                        if (response.data != '') {
                            $("#cards").html(response.data);
                        }
                        $("#ccdiv").dialog("close");
                        clearCCform();
                        reloadCombo();
                    } else {
                        alert(response.message);
                    }
                },
                complete: function () {
                    // $("body").nimbleLoader("hide");
                }
            });
        },
        "Cancel": function () {
            $(this).dialog("close");
        }
    }
});

$("#usersdiv").dialog({
    modal: true,
    width: 400,
    title: "Buy additional user(s)",
    hide: 'fade',
    resizable: false,
    draggable: false,
    autoOpen: false,
    buttons: {
        "Process Order": function () {
            if (parseInt($("#buyusers").val()) > 0) {
                if (isNaN(parseInt($('#buyform select[name="billing_cc_id"]').val()))) {
                    alert("You should select Credit Card");
                } else {
                    $("#buyform").submit();
                }
            } else {
                alert("Please check additional users quantity.");
            }
        },
        "Cancel": function () {
            $(this).dialog("close");
        }
    }
});

$("#addon_aq_div").dialog({
    modal: true,
    width: 400,
    title: "Buy Automate Quoting Addon",
    hide: 'fade',
    resizable: false,
    draggable: false,
    autoOpen: false,
    buttons: {
        "Process Order": function () {
            if ($('.add_user_pay').is(":visible")){
                if (isNaN(parseInt($('#buy_addon_aq_form select[name="addon_aq_billing_cc_id"]').val()))) {
                    alert("You should select Credit Card");
                } else {
                    $("#buy_addon_aq_form").submit();
                }
            }else{
                $("#buy_addon_aq_form").submit();
            }
        },
        "Cancel": function () {
            $(this).dialog("close");
        }
    }
});

$("#renewalsdiv").dialog({
    modal: true,
    width: 530,
    height: 300,
    title: "Change Lisense Type",
    hide: 'fade',
    resizable: false,
    draggable: false,
    autoOpen: false,
    buttons: {
        "Submit": function () {
            $("#changelicenseform").submit();
        },
        "Cancel": function () {
            $(this).dialog("close");
        }
    }
});

function addcreditcard() {
    clearCCform();
    ID = 0;
    $("#ccdiv").dialog("open");
}
function editcreditcard(id) {
    clearCCform();
    ID = id;
    loadCCform();
}
function validateCCform() {
    ret = true;
    for (x in flds) {
        if ($('#' + flds[x]).val() == "") {
            $('#' + flds[x]).addClass("ui-state-error");
            ret = false;
        } else {
            $('#' + flds[x]).removeClass("ui-state-error");
        }
    }
    return ret;
}
function clearCCform() {
    for (x in flds) {
        $('#' + flds[x]).val('');
        $('#' + flds[x]).removeClass("ui-state-error");
    }
}
function loadCCform() {
    // // $("body").nimbleLoader("show");
    $.ajax({
        url: BASE_PATH + 'application/ajax/cards.php',
        data: {  action: "load", id: ID
        },
        type: 'POST',
        dataType: 'json',
        beforeSend: function () {
        },
        success: function (response) {
            // $("body").nimbleLoader("hide");
            if (response.success == true) {
                $('#cc_fname').val(response.cc_fname);
                $('#cc_lname').val(response.cc_lname);
                $('#cc_address').val(response.cc_address);
                $('#cc_city').val(response.cc_city);
                $('#cc_state').val(response.cc_state);
                $('#cc_zip').val(response.cc_zip);
                $('#cc_number').val(response.cc_number);
                $('#cc_month').val(response.cc_month);
                $('#cc_year').val(response.cc_year);
                $('#cc_type').val(response.cc_type);
                $('#cc_cvv2').val(response.cc_cvv2);
                $("#ccdiv").dialog("open");
            } else {
                alert(response.message);
            }
        },
        complete: function () {
            // $("body").nimbleLoader("hide");
        }
    });
}
function reloadCombo() {
    // // $("body").nimbleLoader("show");
    $.ajax({
        url: BASE_PATH + 'application/ajax/cards.php',
        data: {  action: "getcombo"},
        type: 'POST',
        dataType: 'json',
        success: function (response) {
            if (response.success == true) {
                // $("body").nimbleLoader("hide");
                if (response.data != '') {
                    $("#billing_cc_id").html(response.data);
                }
            }
        },
        complete: function () {
            // $("body").nimbleLoader("hide");
        }
    });
}
function changeusers() {
    $("#usersdiv").dialog("open");
}

function buyaddon_aq() {
    $("#addon_aq_div").dialog("open");
    recalculateTotalForAddonAq();
}

function changerenewals() {
    $("#renewalsdiv").dialog("open");
}

function recalculateTotalForAdditionals() {
    var qty = $("#buyusers").val();
    var price = parseFloat(@until_price_for_users@);
    TOTALBUYUSERS = parseInt(qty) * price;
    $("#buyuserstotal").html(number_format(TOTALBUYUSERS, 2));
    if (CURRENTBALANCE < TOTALBUYUSERS) {
        $("#add_user_to_pay").html(number_format((TOTALBUYUSERS - CURRENTBALANCE), 2));
        $('.add_user_pay').show();
    } else {
        $('.add_user_pay').hide();
    }
}

function recalculateTotalForAddonAq() {
    var price = parseFloat(@until_price_for_addon_aq@);
    TOTALBUYADDONAQ = price*1;
    $("#buy_addon_aq_total").html(number_format(TOTALBUYADDONAQ, 2));
    if (CURRENTBALANCE < TOTALBUYADDONAQ) {
        $("#add_addon_aq_to_pay").html(number_format((TOTALBUYADDONAQ - CURRENTBALANCE), 2));
        $('.add_addon_aq_pay').show();
    } else {
        $('.add_addon_aq_pay').hide();
    }
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

    $('#additional_number').change(function () {
        var additional_users = '@additional_users@';
        additional_users = additional_users * 1;
        newval = $(this).val() * 1;

        if (newval < additional_users) {
            $.ajax({
                url: BASE_PATH + 'application/ajax/reduceusers.php',
                data: {  action: "getlist", users: additional_users, renewal_users: newval
                },
                type: 'POST',
                dataType: 'json',
                beforeSend: function () {
                    $("#loadingimg").show();
                },
                success: function (response) {
                    $("#loadingimg").hide();
                    if (response.success == true) {
                        if (response.data != '') {
                            $("#next_inactive_users").html(response.data);
                            $("#reduceusers").show();
                        }
                    } else {
                        alert(response.message);
                    }
                },
                complete: function () {
                    $("#loadingimg").hide();
                }
            });
        } else {
            $("#reduceusers").hide();
        }
    });
});

//]]></script>