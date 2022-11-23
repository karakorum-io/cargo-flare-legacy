<?php

    if (isset($_GET['id']) && $_GET['id'] > 0){
	    include(TPL_PATH."accounts/leadsources/menu_details.php");
?>
    To update the lead source, select whether or not to forward leads below.
<?php
    } else {include(TPL_PATH."accounts/leadsources/menu_details.php");
        // nothing to do
    }
?>

<div align="right" style="clear:both; padding-bottom:5px; padding-top:5px;">
	<img style="vertical-align:middle;" src="<?=SITE_IN?>images/icons/back.png" alt="Back" width="16" height="16" /> <a href="<?=getLink("leadsources")?>">&nbsp;Back to the list</a>
</div>

<form action="<?=getLink("leadsources", "edit", "id", get_var("id"))?>" method="post">
    <div class="alert alert-light alert-elevate " >
        <div  class="row" style="width: 100%" >
            <div class="col-8">
                <?=formBoxStart("Lead Source Information")?>
                    To add a lead source, enter the data below.
                    <div class="row">
                        <div class="col-6">
                            @company_name@
                        </div>
                        <div class="col-6">
                            @domain@
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                        @phone@
                        </div>
                        <div class="col-6">
                        @email_to@
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <br/>
                            @is_send_copy@
                            @email_forward@
                        </div>
                        <div class="col-12">
                            <br/>
                            @exclude_from_auto_quote@
                        </div>
                    </div>
                <?=formBoxEnd()?>
            </div>
            <div class="col-4">
                <?= formBoxStart("Assign Leads To") ?>
                <em>Please choose the algorithm "single user" or "distribute" to assign new leads/quotes to Users.</em>
                <label> Assign to:</label>
                <img src="<?= SITE_IN ?>images/icons/assign.png" width="16" height="16" alt="Assign" style="vertical-align:middle;" />
                <input type="hidden" name="assign_type" value="distribute" />

                <div style="max-height:400px; width:423px; overflow: auto; border: 1px solid #ccc; background-color: #edefff; padding: 8px;">
                    <? if (!empty($this->daffny->tpl->assigns)) { ?>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <tr>
                                <td width="20" align="center">&nbsp;</td>
                                <td align="center">User</td>
                                <td align="center">Batch</td>
                                <td align="center">Order</td>
                            </tr>
                            <? foreach ($this->daffny->tpl->assigns as $key => $a) { ?>
                                <tr>
                                    <td width="20" align="center">
                                        <label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand">
                                            <input type="checkbox" id="assigns<?= $a['id'] ?>" name="assigns[<?= $a['id'] ?>]" value="<?= $a['id'] ?>" <?= $a['ch'] ?>>
                                            <span></span>
                                        </label>
                                    </td>
                                    <td><label for="assigns<?= $a['id'] ?>"><?= htmlspecialchars($a['contactname']); ?></label></td>
                                    <td>&nbsp;<input type="text" class="form-box-textfield batches" name="batches[<?= $a['id'] ?>]" value="<?= $a['batch'] ?>" /></td>
                                    <td>&nbsp;<input type="text" class="form-box-textfield batches" name="ords[<?= $a['id'] ?>]" value="<?= $a['ord'] ?>" /></td>
                                </tr>
                            <? } ?>
                        </table>
                    <?
                    } else {
                        echo "No users.";
                    }
                    ?>
                </div>
            </div>
            <?= formBoxEnd() ?>
            <br />
            <?=submitButtons(getLink("leadsources"), "Save")?>
    </div>
</form>