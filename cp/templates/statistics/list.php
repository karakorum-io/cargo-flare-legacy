<?
/***************************************************************************************************
* Statistic template CP Class                                                                     *
* Add letters in queue                                                                             *
*                                                                                                  *
* Client: 	FreightDragon                                                                    *
* Version: 	1.0                                                                                    *
* Date:    	2011-10-03                                                                             *
* Author:  	C.A.W., Inc. dba INTECHCENTER                                                          *
* Address: 	11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076                             *
* E-mail:	techsupport@intechcenter.com                                                           *
* CopyRight 2011 FreightDragon. - All Rights Reserved                                        *
****************************************************************************************************/

?>
@flash_message@
<?=formBoxStart()?>
<table border="0" cellspacing="1" cellpadding="0" bgcolor="#999999">
  <tr bgcolor="#FFFFCC">
    <td align="right" style="padding:5px;">Sent:<font style="color:#0000FF"><strong>&nbsp;@sent@</strong></font></td>
    <td align="right" style="padding:5px;">Failed:<font style="color:#FF0000"><strong>&nbsp;@filed@</strong></font></td>
  </tr>
</table>
<br />
<form action="" method="post" name="filter" style="margin: 0px;">
    <table border="0" cellspacing="5" cellpadding="0">
		<tr>
            <td>@filter_date_from@</td>
            <td>@filter_date_to@</td>
		</tr>
		<tr>
			<td>@template_id@</td>
		</tr>
		<tr>
			<td colspan="4"><?=submitButtons("", "Apply")?></td>
		</tr>
	</table>

</form>
<?=formBoxEnd()?>


<div align="right" style="clear:both; padding-bottom:5px; padding-top:5px;">
	<a href="<?=getLink("statistics", "queue");?>">Emails in queue</a>
</div>
<table width="100%" cellpadding="0" cellspacing="0" border="0" class="grid">
    <tr class="grid-head">
        <td class="grid-head-left"><?=$this->order->getTitle("id", "ID")?></td>
        <td width="200"><?=$this->order->getTitle("email", "Email")?></td>
        <td><?=$this->order->getTitle("template", "Template")?></td>
        <td><?=$this->order->getTitle("action", "Action")?></td>
        <td><?=$this->order->getTitle("process_date", "Date")?></td>
    </tr>
    <? if (isset($this->data) && count($this->data)>0){?>
	    <?php foreach ($this->data as $i => $data): ?>
	    <tr class="grid-body<?php echo ($i == 0 ? " first-row" : ""); ?>" id="row-<?php echo $data['id']; ?>">
	        <td class="grid-body-left"><?php echo htmlspecialchars($data['id']); ?></td>
	        <td><?php echo $data['email']; ?></td>
			<td align="center"><?php echo $data['template']; ?></td>
			<td align="center"><?php echo $data['action']; ?></td>
	        <td class="grid-body-right" align="center"><?php echo $data['process_date']; ?></td>
	    </tr>
	    <?php endforeach; ?>
    <?}?>
</table>
@pager@
<script type="text/javascript">//<![CDATA[
$(function(){
    $('#filter_date_from').datepicker(datepickerSettings);
    $('#filter_date_to').datepicker(datepickerSettings);
});
//]]></script>