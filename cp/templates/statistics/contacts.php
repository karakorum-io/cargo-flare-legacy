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
@message@
<div align="right" style="clear:both; padding-bottom:5px; padding-top:5px;">
	<img style="vertical-align:middle;" src="<?=SITE_IN?>images/icons/back.png" alt="Back" width="16" height="16" /> <a href="<?=getLink("statistics")?>">&nbsp;Back to the list</a>
</div>
<? if (count($this->daffny->tpl->contdata) > 0) { ?>
<div style="height: 8px;"></div>
<table width="100%" cellspacing="1" cellpadding="3" border="0" bgcolor="#bebebe">
  <tr class="table_hat">
    <td width="50">@id@</td>
    <td>@name@</td>
    <td>@phone@</td>
    <td>@email@</td>
    <td>@business_type@</td>
    <td>@url@</td>
  </tr>
  <? foreach ($this->daffny->tpl->contdata as $d): ?>
  <tr bgcolor="#<?=$d['bg']?>">
    <td><?=$d['id']?></td>
    <td><?=$d['name']?></td>
    <td><?=$d['phone']?></td>
    <td><?=$d['email']?></td>
    <td><?=$d['business_type']?></td>
    <td><?=$d['url']?></td>
  </tr>
  <? endforeach; ?>
</table>
<div style="height: 8px;"></div>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
  <tr>
    <td align="left">@pages@</td>
    <td align="right" nowrap="nowrap">Records on page:&nbsp;</td>
    <td width="47" align="right" nowrap="nowrap"><form action="index.php?statistics=contacts" method="post" name="pager" style="margin: 0px;">@on_page@</form></td>
  </tr>
</table>
<? } else { ?>
<br /><br /><br /><br /><br /><br />
<div align="center">No one record found.</div>
<br /><br /><br /><br /><br /><br />
<? } ?>