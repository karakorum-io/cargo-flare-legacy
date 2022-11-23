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
	<img style="vertical-align:middle;" src="<?=SITE_IN?>images/icons/back.png" alt="Back" width="16" height="16" /> <a href="<?=getLink("statistics")?>">&nbsp;Back to the list</a>
</div>
<table width="100%" cellpadding="0" cellspacing="0" border="0" class="grid">
    <tr class="grid-head">
        <td class="grid-head-left">Attempts</td>
        <td width="200">E-mails quantity</td>
        <td class="grid-head-right">Details</td>
    </tr>
    <?php foreach ($this->data as $i => $data): ?>
    <tr class="grid-body<?php echo ($i == 0 ? " first-row" : ""); ?>">
        <td align="center" class="grid-body-left"><?php echo htmlspecialchars($data['counter']); ?></td>
        <td align="center"><?php echo $data['cnt']; ?></td>
        <td align="center" class="grid-body-right" align="center"><?php echo $data['details']; ?></td>
    </tr>
    <?php endforeach; ?>
</table>
@pager@
<br />
<?=backButton(getLink("statistics"));?>