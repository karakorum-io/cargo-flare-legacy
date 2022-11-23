@content@
<div style="clear: both;"></div>
<br />
@flash_message@
	<form action="<?php echo getLink("contactus"); ?>" method="post">
		<table cellpadding="0" cellspacing="5" border="0">
			<tr>
				<td>@companyname@</td>
			</tr>
			<tr>
				<td>@activity@</td>
			</tr>
			<tr>
				<td>@contactname@</td>
			</tr>
			<tr>
				<td>@email@</td>
			</tr>
			<tr>
				<td>@phone@</td>
			</tr>
			<tr>
				<td>@url@</td>
			</tr>
			<tr>
				<td colspan="2">
					<span class="required">*</span>Security Code:
					<table cellpadding="0" cellspacing="0" border="0">
						<tr>
							<td style="padding: 2px 5px 0 0;">@captcha@</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="2"><?php echo submitButtons(getLink()); ?></td>
			</tr>
		</table>
	</form>
<br clear="all" />