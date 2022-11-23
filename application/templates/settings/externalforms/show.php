<? include(TPL_PATH."settings/menu.php"); ?>
<h3>External Forms</h3>
Below you can view and edit your external forms.
<br /><br />
<form action="<?=getLink("externalforms")?>" method="post">
	<?=formBoxStart("Configure External Pages")?>
	    <table cellspacing="5" cellpadding="5" border="0">
			<tr>
				<td width="200">@redirect_url@</td>
			</tr>
			<tr>
				<td colspan="2">
					<em>The page that users will see after submitting the Quote Request. If left blank, a generic thank-you page will be displayed. Must start with <strong>'http://'</strong> or <strong>'https://'</strong>.</em>
					<br /><br />
				</td>
			</tr>
	        <tr>
	        	<td>Quote Request Form:</td>
	        	<td><img style="vertical-align:middle;" src="<?=SITE_IN?>images/icons/external.png" alt="External" width="16" height="16" /> &nbsp;<a target="_blank" href="http://<?= $_SERVER['SERVER_NAME'] ?><?=SITE_IN?>quote/hash/@hash@">http://<?= $_SERVER['SERVER_NAME'] ?><?=SITE_IN?>quote/hash/@hash@</a></td>
	        </tr>
	        <tr>
	        	<td>&nbsp;</td>
	        	<td><em>You may also use the link above on your website.</em>
	        	<br /><br />
	        	</td>
	        </tr>
	        <tr>
	        	<td valign="top">Custom Quote Request Form:</td>
	        	<td>
	        		<img src="<?=SITE_IN?>images/icons/build.png" alt="Build" width="16" height="16" style="vertical-align:middle;" />&nbsp;<a href="<?=getLink("externalforms", "build")?>">Build your own quote request form</a> to be hosted on your website, or
					<img src="<?=SITE_IN?>images/icons/download.png" alt="download" width="16" height="16" style="vertical-align:middle;" />&nbsp;<a href="<?=getLink("externalforms", "download","hash", '@hash@')?>">Download</a> a sample form ready to use on your website right away.
	        	</td>
	        </tr>
	    </table>
	<?=formBoxEnd()?>
	<br />
	<?=formBoxStart("Configure Header and Footer")?>
	<table cellspacing="5" cellpadding="5" border="0">
		<tr>
			<td valign="top">
				<table cellspacing="5" cellpadding="5" border="0">
					<tr>
						<td>@header@</td>
			        </tr>
			        <tr>
				        <td>@footer@</td>
			        </tr>
			    </table>
			</td>
			<td valign="top" style="padding-left:30px;">
				<table cellspacing="5" cellpadding="5" border="0">
					<tr>
						<td>
							<img style="vertical-align:middle;" src="<?=SITE_IN?>images/icons/note.png" alt="Note" width="16" height="16" /> &nbsp;<em>You may use the dynamic fields in your html header and footer.<br /> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;These will be replaced by the actual data for your company.</em>
						</td>
					</tr>
					<tr>
						<td><? include(TPL_PATH."settings/codes_externalforms.php")?></td>
					</tr>
        		</table>
			</td>
		</tr>
	</table>
	<?=formBoxEnd()?>
	<br />
	<?php echo submitButtons(getLink("externalforms"), "Save"); ?>
</form>