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
<div align="right" style="clear:both; padding-bottom:5px; padding-top:5px;">
	<img style="vertical-align:middle;" src="<?=SITE_IN?>images/icons/back.png" alt="Back" width="16" height="16" /> <a href="<?=getLink("statistics", "queue")?>">&nbsp;Back to the list</a>
</div>
<table width="100%" cellpadding="0" cellspacing="0" border="0" class="grid">
    <tr class="grid-head">
        <td class="grid-head-left"><?=$this->order->getTitle("id", "ID")?></td>
        <td><?=$this->order->getTitle("email", "Email")?></td>
        <td><?=$this->order->getTitle("template", "Template")?></td>
        <td><?=$this->order->getTitle("from_name", "From")?></td>
        <td><?=$this->order->getTitle("subject", "Subject")?></td>
        <td class="grid-head-right"><?=$this->order->getTitle("co", "Attempts")?></td>
    </tr>
    <?php foreach ($this->data as $i => $data): ?>
    <tr class="grid-body<?php echo ($i == 0 ? " first-row" : ""); ?>" id="row-<?php echo $data['id']; ?>">
        <td align="center" class="grid-body-left"><?php echo $data['id']; ?></td>
        <td align="center"><?php echo $data['email']; ?></td>
        <td align="center"><?php echo $data['title']; ?></td>
        <td align="center"><?php echo $data['from_name']; ?></td>
        <td align="center"><?php echo $data['subject']; ?></td>
        <td align="center" class="grid-body-right" align="center"><?php echo $data['co']; ?></td>
    </tr>
    <?php endforeach; ?>
</table>
@pager@
<br />
<?=backButton(getLink("statistics"));?>