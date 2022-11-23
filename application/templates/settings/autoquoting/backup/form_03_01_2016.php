<? include(TPL_PATH . "settings/menu.php"); ?>
<div align="right" style="clear:both; padding-bottom:5px; padding-top:5px;">
    <img style="vertical-align:middle;" src="<?= SITE_IN ?>images/icons/back.png" alt="Back" width="16" height="16" /> <a href="<?= getLink("autoquoting") ?>">&nbsp;Back to the list</a>
</div>
<form action="<?= getLink("autoquoting", "editseason", "id", get_var("id")) ?>" method="post">
    <?= formBoxStart(((int) get_var("id") > 0 ? "Edit Season" : "Add New Season")) ?>
    <table cellpadding="0" cellspacing="10" border="0">
        <tr>
            <td>@name@</td>
        </tr>
        <tr>
            <td>@start_date@</td>
        </tr>
        <tr>
            <td>@end_date@</td>
        </tr>
        <tr>
            <td>@status@</td>
        </tr>
    </table>
    <?= formBoxEnd() ?>
    <br />
    <?= submitButtons(getLink("autoquoting"), "Save") ?>
</form>
<script type="text/javascript">//<![CDATA[
    $(function(){
        $('#start_date').datepicker(datepickerSettings);
        $('#end_date').datepicker(datepickerSettings);
    });
    //]]></script>