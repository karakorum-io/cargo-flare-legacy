@flash_message@
<div align="right" style="clear:both; padding-bottom:5px; padding-top:5px;">
    <img style="vertical-align:middle;" src="<?= SITE_IN ?>images/icons/back.png" alt="Back" width="16" height="16" /> <a href="<?= getLink("members") ?>">&nbsp;Back to the list</a>
</div>
<form action="<?= getLink("members", "edit", "id", get_var("id")) ?>" method="post">
    <?= formBoxStart("Personal Information") ?>
    <table cellpadding="0" cellspacing="5" border="0">
        <tr>
            <td>@username@</td>
        </tr>
        <tr>
            <td>@contactname@</td>
        </tr>
        <tr>
            <td>@phone@</td>
        </tr>
    </table>
    <?= formBoxEnd() ?>
    <br />
    <?= formBoxStart("Company Information") ?>
    <table cellpadding="0" cellspacing="5" border="0">
        <tr>
            <td>@companyname@</td>
        </tr>
        <tr>
            <td>@is_carrier@ &nbsp;&nbsp;&nbsp;&nbsp;@is_broker@</td>
        </tr>
        <tr>
            <td>@is_frozen@</td>
        </tr>
    </table>
    <?= formBoxEnd() ?>
    <br />
    <?= formBoxStart("Login") ?>
    <table cellpadding="0" cellspacing="5" border="0">
        <tr>
            <td>@email@</td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>@password@</td>
            <td>@password_confirm@</td>
        </tr>
    </table>
    <?= formBoxEnd() ?>
    <br />
    <?= submitButtons(getLink("members")) ?>
</form>