<?php
/***************************************************************************************************
* Control Panel HTML Template - Coupons List                                                                                 *
*                                                                                                  *
* Client: 	PitBullTax                                                                             *
* Version: 	1.1                                                                                    *
* Date:    	2010-05-31                                                                             *
* Author:  	C.A.W., Inc. dba INTECHCENTER                                                          *
* Address: 	11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076                             *
* E-mail:	techsupport@intechcenter.com                                                           *
* CopyRight 2010-2011 NEGOTIATION TECHNOLOGIES, LLC. - All Rights Reserved                                 *
****************************************************************************************************/
?><script type="text/javascript">
function toggleSearch(sh) {
    $.cookie('couponsSearch', sh);
    if (sh == 1) {
        $('#search').show();
    }
    else {
        $('#search').hide();
    }
    return false;
}

function selectAll() {
	$('.is_check_class').attr('checked', $('#select_all').is(':checked'));
}

function search() {
	$('#action1').val('');
	$('#Search').submit();
	return false;
}

function exportSelected() {
	if ($('.is_check_class:checked').size() == 0) return false;
	$('#action2').val('export_selected');
	$('#MainForm').submit();
	return false;
}

function exportAllFound() {
	$('#action1').val('export_all_found');
	$('#Search').submit();
	return false;
}

function changePeriod() {
   	if ($('#period').val() == 3) {
   		$('#date_from_label').show();
   		$('#date_to_label').show();
   		$('#date_from').show();
   		$('#date_to').show();
   	} else {
   		$('#date_from_label').hide();
   		$('#date_to_label').hide();
   		$('#date_from').hide();
   		$('#date_to').hide();
   	}
}

$(function(){
	$('#date_from,#date_to').datepicker({
		changeMonth: true,
		changeYear: true,
		duration: ''
	});

	$('#period').change(changePeriod);
	changePeriod();
});
</script>
<br />
<ul class="cp-top-actions">
	<li style="background: url(<?=SITE_IN?>images/icons/search.gif) 0 0 no-repeat;"><a href="#" onclick="return toggleSearch(1);">Search coupons</a></li>
	<li></li>
    <li style="background: url(<?=SITE_IN?>images/icons/add.gif) 0 0 no-repeat;"><a href="<?=getLink('coupons/edit/id/new')?>">Add new coupon</a></li>
</ul>
@flash_message@
<br />
<div id="search" <?=(@$_COOKIE["couponsSearch"] != 1 ? "style='display:none;'" : "")?>>
    <table cellpadding="0" cellspacing="1" border="0" class="form-table" style="border: 1px solid #CCC;">
        <tr>
            <th style="background-image: url(<?=SITE_IN?>images/bg_menu_g.gif);">
                <table width="100%" cellpadding="0" cellspacing="0" border="0" class="nogrid">
                    <tr>
                        <td>Search coupons</td>
                        <td align="right"><img src="<?=SITE_IN?>images/icons/close.png" onclick="toggleSearch(0);" style="cursor:pointer;" alt="" width="16" height="16" /></td>
                    </tr>
                </table>
            </th>
        </tr>
        <tr>
            <td bgcolor="#ffffff">
                <form action="<?=getLink('coupons')?>" method="get" name="Search" id="Search" style="margin:0px;">
                    <input type="hidden" name="coupons" />
                    <input type="hidden" name="order" id="order" value="<?=get_var("order")?>" />
                    <input type="hidden" name="arrow" id="arrow" value="<?=get_var("arrow")?>" />
                    <input type="hidden" name="page" id="page" value="<?=get_var("page")?>" />
                    <input type="hidden" name="action" id="action1" value="" />
                    <table width="100%" cellpadding="0" cellspacing="2" border="0" class="nogrid">
                        <tr>
                            <td>Period:</td>
                            <td>&nbsp;</td>
                            <td><span id="date_from_label" style="display:none">Date From:</span></td>
                            <td>&nbsp;</td>
                            <td><span id="date_to_label" style="display:none">Date To:</span></td>
                            <td>&nbsp;</td>
                            <td>Status:</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr height="22px">
                            <td>@s_period@</td>
                            <td>&nbsp;</td>
                            <td><input type="text" name="date_from" id="date_from" value="@s_date_from@" maxlength="10" style="display:none" /></td>
                            <td>&nbsp;</td>
                            <td><input type="text" name="date_to" id="date_to" value="@s_date_to@" maxlength="10" style="display:none" /></td>
                            <td>&nbsp;</td>
                            <td>@s_status@</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>Code:</td>
                            <td colspan="5">&nbsp;</td>
                            <td>Company:</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td><input type="text" name="code" id="code" value="@s_code@" /></td>
                            <td>&nbsp;</td>
                            <td colspan="4">&nbsp;</td>
                            <td><input type="text" name="company" id="company" value="@s_company@" /></td>
                            <td>&nbsp;</td>
                            <td align="right"><input type="button" value="Search" style="width:100px;" onclick="search()"/></td>
                        </tr>
                    </table>
                </form>
            </td>
        </tr>
    </table>
    <br />
</div>
<? if ($this->coupons) { ?>
<form action="" method="post" name="MainForm" id="MainForm" style="margin:0px;">
<input type="hidden" name="action" id="action2" value="" />
<table width="100%" cellpadding="0" cellspacing="0" border="0" class="grid">
    <tr class="grid-head">
    	<th class="grid-head-left" width="20" align="center"><input type="checkbox" id="select_all" onclick="selectAll()"></th>
        <th width="40" align="center">@id@</th>
        <th align="center">@code@</th>
        <th align="center">@time_to_use@</th>
        <th align="center">@expires@</th>
        <th align="center">@company@</th>
        <th align="center">@status@</th>
        <th class="grid-head-right" nowrap="nowrap" align="center" colspan="2">Actions</th>
    </tr>
	<? foreach ($this->coupons as $i => $m) {
	/* @var Coupon $m */
	?>
	<tr class="grid-body <? if ($i == 0) { echo ' first-row'; } if ($m->isExpired()) { echo " disabled"; }  ?>">
    	<td class="grid-body-left" align="center"><input type="checkbox" class="is_check_class" name="is_check[]" value="<?=$m->id?>"></td>
        <td align="center"><?=$m->id?></td>
        <td align="center"><?=$m->code?></td>
        <td align="center"><?=($m->time_to_use==0)?'unlimited':$m->time_to_use?></td>
        <td align="center"><?=$m->expire_date?></td>
        <td><?=$m->company?></td>
        <td align="center"><?=($m->isExpired())?'Expired':$m->status?></td>
        <td width="21" align="center"><a href="<?=getLink('coupons/edit/id/'.$m->id)?>"><img src="<?=SITE_IN?>images/icons/edit.png" border="0" alt="" title="Edit" width="16" height="16" /></a></td>
        <td class="grid-body-right" width="21" align="center"><a href="<?=getLink('coupons/delete/id/'.$m->id)?>" onclick="return are_you_sure();"><img src="<?=SITE_IN?>images/icons/delete.png" border="0" alt="" title="Delete" width="16" height="16" /></a></td>
    </tr>
    <? } ?>
</table>
</form>
<br />
@pager@
<table cellpadding="10" cellspacing="0" border="0">
	<tr>
		<table cellpadding="10" cellspacing="0" border="0">
	<tr>
		<td><?=functionButton("Export selected","exportSelected()"); ?></td>
		<td>&nbsp;</td>
		<td><?=functionButton("Export all found","exportAllFound()"); ?></td>
	</tr>
</table>
	</tr>
</table>
<? } else { ?>
<p>Records not found.</p>
<? } ?>