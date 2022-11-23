<? include(TPL_PATH . "settings/menu.php"); ?>
<h3>AQ Settings</h3>
<div align="right" style="clear:both; padding-bottom:5px; padding-top:5px;">
    <img style="vertical-align:middle;" src="<?= SITE_IN ?>images/icons/back.png" alt="Back" width="16" height="16" /> <a href="<?= getLink("autoquoting") ?>">&nbsp;Back to the list</a>
</div>
<form action="<?= getLink("autoquoting", "settings") ?>" method="post">
    <div style="float:left; width:480px; padding-right:10px;">
        <?= formBoxStart("Automated Quoting Settings") ?>
        <table cellspacing="5" cellpadding="5" border="0">
            <tr><td>@is_enabled@</td></tr>
            <!--<tr><td><strong>Send auto-quoted initial quote emails:</strong></td></tr>
            <tr><td>@email_type@</td></tr>
            <tr><td><strong>Set enclosed surcharge type to:</strong></td></tr>
            <tr><td>@surcharge_type@</td></tr>
            <tr><td>@is_autoquote_unknown@</td></tr>-->
        </table>
        <?= formBoxEnd() ?>
    </div>
    <div style="float:left; width:480px;">
        <?= formBoxStart("Vehicles Quoted") ?>
        <table cellspacing="5" cellpadding="5" border="0">
            <tr>
                <td>Today:</td>
                <td class="totalv">@today@</td>
            </tr>
            <tr>
                <td>This month:</td>
                <td class="totalv">@this_month@</td>
            </tr>
            <tr>
                <td>Last month:</td>
                <td class="totalv">@last_month@</td>
            </tr>
        </table>
        <?= formBoxEnd() ?>
    </div>
    <div style="clear:both;">&nbsp;</div>
    <br />
    <?php echo submitButtons(getLink("autoquoting"), "Save"); ?>
</form>