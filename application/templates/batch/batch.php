<!---
/***************************************************************************************************
* Transportation Management Software
*
* Client:			FreightDragon
* Version:			1.0
* Start Date:		2011-10-05
* Author:			Freight Genie LLC
* E-mail:			admin@freightdragon.com
*
* CopyRight 2011 FreightDragon. - All Rights Reserved
****************************************************************************************************/
--->
<form method="post"  action="<?php print SITE_IN."application/batchpayment/batchsubmit";?>">
    <table cellspacing="2" cellpadding="0" border="0">
         <tr><td colspan="2">&nbsp;</td></tr>
         <tr><td colspan="2">&nbsp;</td></tr>
         <tr>
            <td colspan="2">Batch Payment
            </td>
        </tr>
        <tr>
            <td>@batch_order_ids@</td>
        </tr>
        <tr>
            <td colspan="2" align="center">&nbsp;
            <?= submitButtons(SITE_IN."application/batchpayment/batchsubmit", "Start Processing") ?>
            </td>
        </tr>
        
    </table>
    
</form>
