<?if (isset($_GET['id']) && $_GET['id'] > 0){
	include(TPL_PATH."accounts/leadsources/menu_details.php");?>
	<span class="mt-2 mb-2 ">To update the lead source, select whether or not to forward leads below.</span>
<?}else{include(TPL_PATH."accounts/leadsources/menu_details.php");?>
	To add a lead source, enter the data below.
<?}?>
<div class="alert alert-light alert-elevate">
<div class="row" style="width: 100%">

<form action="<?php echo getLink("leadsources", "accessAccount", "id", get_var("id"))?>" method="post">
    
    <?php echo formBoxStart("Access Affiliate Account")?>
        <table cellpadding="0" cellspacing="10" border="0">
            <tr><td>@first_name@</td><td>@last_name@</td></tr>
            <tr>
                <td>
                    @username@ 
                    <span style="font-weight: bold;" class="hint--right hint--rounded hint--bounce" data-hint="Spaces enter in the username will be converted to '_'">
                        <img src="/images/icons/info.png" width="16" height="16">
                    </span>
                </td>
                <td></td>
            </tr>
            <tr><td>@email@</td><td>@mobile@</td></tr>
            <tr><td>@cost@</td><td>@commision@</td>
            <tr><td>@password@</td><td>@c_password@</td></tr>
            <tr><td align="right">&nbsp;</td><td>@weekly_report@</td></tr>            
        </table>
    <?php echo formBoxEnd()?>
    <br>
    <?php echo submitButtons(getLink("leadsources","accessAccount","id",get_var("id")), "Save")?>
</form>
</div>

</div>