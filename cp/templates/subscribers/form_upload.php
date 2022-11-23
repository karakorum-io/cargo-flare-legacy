@flash_message@
<div align="right" style="clear:both; padding-bottom:5px; padding-top:5px;">
	<img style="vertical-align:middle;" src="<?=SITE_IN?>images/icons/back.png" alt="Back" width="16" height="16" /> <a href="<?=getLink("subscribers")?>">&nbsp;Back to the list</a>
</div>
<?=formBoxStart()?>
<form method="post" action="<?=getLink("subscribers", "import")?>" enctype="multipart/form-data">
  <div align="center">
    <table cellpadding="0" cellspacing="10" border="0">
      <tr>
        <td align="left">@category_id@</td>
      </tr>
      <tr>
        <td align="left">@fupload@</td>
      </tr>
      <tr>
      	<td colspan="2"><br /><?=submitButtons(getLink("subscribers"))?></td>
      </tr>
    </table>
  </div>
</form>
<?=formBoxEnd()?>