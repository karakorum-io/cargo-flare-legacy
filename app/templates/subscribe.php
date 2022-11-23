@content@
<div style="clear: both;"></div>
<br />
@flash_message@
<form action="<?php echo getLink("subscribe"); ?>" method="post">
    <table cellpadding="0" cellspacing="5" border="0">
        <tr>
            <td>@first_name@</td>
        </tr>
        <tr>
            <td>@last_name@</td>
        </tr>
		<tr>
            <td>@address@</td>
        </tr>
        <tr>
			<td>@email@</td>
        </tr>
		<tr>
            <td>@city@</td>
        </tr>
        <tr id="state_tr1">
            <td>@state@</td>
        </tr>
        <tr id="state_tr2" style="display:none;">
            <td>@state2@</td>
        </tr>
		<tr>
            <td>@zip@</td>
        </tr>
        <tr>
            <td>@country@</td>
        </tr>
        <tr>
			<td>@phone@</td>
        </tr>
        <tr>
            <td colspan="2">
                <span class="required">*</span>Security code:
                <table cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td style="padding: 2px 5px 0 0;">@captcha@</td>
                        <td style="padding: 0;"></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="2"><br /><?php echo submitButtons(getLink()); ?></td>
        </tr>
    </table>
</form>
<script type="text/javascript">//<![CDATA[
	function checkCountry(){
		var val = $('#country').attr('value');
		if (val !="US"){
			$('#state_tr1').hide();
			$('#state_tr2').show();
		}else{
			$('#state_tr1').show();
			$('#state_tr2').hide();
		}
	}
	checkCountry();
//]]></script>