<div align="right" style="clear:both; padding-bottom:5px; padding-top:5px;">
	<img style="vertical-align:middle;" src="<?=SITE_IN?>images/icons/back.png" alt="Back" width="16" height="16" /> <a href="<?=getLink("newsletters")?>">&nbsp;Back to the list</a>
</div>
<form action="<?=getLink("newsletters", "edit", "id", (int)get_var("id"))?>" method="post">
    <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td valign="top">
                @flash_message@
                <table cellpadding="3" cellspacing="0" border="0" style="padding-bottom: 5px;">
                    <tr>
                        <td>@title@</td>
                    </tr>
                </table>
                @content@
            </td>
        </tr>
        <tr>
            <td><br /><?=submitButtons(getLink("newsletters"))?></td>
        </tr>
    </table>
</form>
<script type="text/javascript">//<![CDATA[
$(function(){
    $('#newsletter_date').datepicker(datepickerSettings);
});
//]]></script>