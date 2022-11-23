<script type="text/javascript">
	function SelectAll(){
	    $('#custom_form').focus().select();
	}
</script>
<? include(TPL_PATH."settings/menu.php"); ?>
Copy the following HTML source code and paste it into your web page. You may style it, but do not remove any fields. Make sure to test the form.
<div align="right" style="clear:both; padding-bottom:5px; padding-top:5px;">
	<img style="vertical-align:middle;" src="<?=SITE_IN?>images/icons/back.png" alt="Back" width="16" height="16" /> <a href="<?=getLink("externalforms", "build")?>">&nbsp;Back to the generator</a>
</div>
	<?=formBoxStart("Your form")?>
	    <table cellspacing="5" cellpadding="5" border="0">
			<tr>
				<td width="600">@custom_form@</td>
			</tr>
	    </table>
	<?=formBoxEnd()?>
	<br />
	<?php echo backButton(getLink("externalforms","build")); ?>
</form>