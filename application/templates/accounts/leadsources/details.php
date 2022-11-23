<? include(TPL_PATH."accounts/leadsources/menu_details.php"); ?>
<div align="right" style="clear:both; padding-bottom:5px; padding-top:5px;">
	<img style="vertical-align:middle;" src="<?=SITE_IN?>images/icons/back.png" alt="Back" width="16" height="16" /> <a href="<?=getLink("leadsources")?>">&nbsp;Back to the list</a>
</div>


<div class="alert alert-light alert-elevate " >
<div class="row">
     <div class="col-12">
        <div class="row">
                <div class="col-6 mt-2">
                    <?=formBoxStart("Lead Source Information")?>
                <table cellpadding="0" cellspacing="10" border="0">
                <tr>
                    <td>Status:</td>
                    <td class="kt-link kt-link--state kt-link--success" >@status_name@</td>
                </tr>
                <tr>
                    <td>Company name:</td>
                    <td class="kt-link kt-link--state kt-link--warning">@company_name@</td>
                </tr>
                <tr>
                    <td>Domain:</td>
                    <td><a class="kt-link kt-link--state kt-link--primary"  href="http://@domain@" target="_blank">@domain@</a>&nbsp;&nbsp;&nbsp;<b>Rule [ Firstname+Lastname.Domainname@freightdragon.com ]</b></td>
                </tr>
                <tr>
                    <td>Phone:</td>
                    <td class="kt-link kt-link--state kt-link--info">@phone@</td>
                </tr>
                <tr>
                    <td>To Address:</td>
                    <td class="kt-link kt-link--state kt-link--primary">@email_to@</td>
                </tr>
                <tr>
                    <td>Forward Email:</td>
                    <td class="kt-link kt-link--state kt-link--success">@email_forward@</td>
                </tr>
                </table>
                <?=formBoxEnd()?>

                 </div>
             <div class="col-6 mt-2">

                 <?= formBoxStart("Assign Leads To") ?>
        <table cellspacing="5" cellpadding="5" border="0">
        
            <tr>
                <label>Assigned to:</label>
                <td>
                   <input type="hidden" name="assign_type" value="distribute" />
                    <div style="max-height:400px; width:300px; overflow: auto; border: 1px solid #ccc; background-color: #f1f1f1; padding: 8px;">
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
                                        <td width="20" align="center"><input type="checkbox" id="assigns<?= $a['id'] ?>" name="assigns[<?= $a['id'] ?>]" value="<?= $a['id'] ?>" <?= $a['ch'] ?> disabled="disabled"/></td>
                                        <td><label for="assigns<?= $a['id'] ?>"><?= htmlspecialchars($a['contactname']); ?></label></td>
                                        <td>&nbsp;<input type="text" class="form-box-textfield batches" name="batches[<?= $a['id'] ?>]" value="<?= $a['batch'] ?>" disabled="disabled"/></td>
                                        <td>&nbsp;<input type="text" class="form-box-textfield batches" name="ords[<?= $a['id'] ?>]" value="<?= $a['ord'] ?>" disabled="disabled"/></td>
                                    </tr>
                                <? } ?>
                            </table>
                        <?
                        } else {
                            echo "No users.";
                        }
                        ?>
                    </div>
                </td>
            </tr>
        </table>
        <?= formBoxEnd() ?>
    <br />
    <?=backButton(getLink("leadsources"))?>

            </div>
        </div>
     </div>
</div>
</div>


    
	