<div align="right" style="clear:both; padding-bottom:5px; padding-top:5px;">
	<img style="vertical-align:middle;" src="<?=SITE_IN?>images/icons/back.png" alt="Back" width="16" height="16" /> <a href="<?=getLink("subscribers")?>">&nbsp;Back to the list</a>
</div>
@flash_message@
<form action="<?=getLink("subscribers", "edit", "id", get_var("id"))?>" method="post">
    <?=formBoxStart()?>
	<table cellpadding="0" cellspacing="5" border="0">
        <tr>
            <td colspan="2">@category_id@</td>
            <td colspan="2"><br />@unsubscribed@</td>
        </tr>
        <tr>
            <td colspan="2">@first_name@</td>
            <td colspan="2">@last_name@</td>
        </tr>
		<tr>
            <td colspan="2">@address@</td>
			<td colspan="2">@email@</td>
        </tr>
		<tr>
            <td colspan="2">@city@</td>
            <td colspan="2">@state@</td>
        </tr>
		<tr>
            <td>@zip@</td>
            <td>@country@</td>
			<td>@phone@</td>
            <td></td>
        </tr>
    </table>
    <?=formBoxEnd()?>
    <br />
    <?=submitButtons(getLink("subscribers"))?>
</form>

<script type="text/javascript">$(function(){$('#first_name').focus();});</script>