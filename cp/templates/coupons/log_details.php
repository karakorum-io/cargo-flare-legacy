<?php
/***************************************************************************************************
* Control Panel HTML Template - Coupons Details                                                                                 *
*                                                                                                  *
* Client: 	PitBullTax                                                                             *
* Version: 	1.1                                                                                    *
* Date:    	2010-05-31                                                                             *
* Author:  	C.A.W., Inc. dba INTECHCENTER                                                          *
* Address: 	11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076                             *
* E-mail:	techsupport@intechcenter.com                                                           *
* CopyRight 2010-2011 NEGOTIATION TECHNOLOGIES, LLC. - All Rights Reserved                                 *
****************************************************************************************************/
?><?php if (!empty($this->details)): ?>
<table width="100%" cellpadding="0" cellspacing="1" border="0" class="grid">
	<tr>
		<th>Code</th>
		<th>Product Name</th>
		<th>Discount</th>
	</tr>
	<?php foreach ($this->details as $detail): ?>
	<tr valign="top">
		<td><?php echo $detail['code']; ?></td>
		<td><?php echo $detail['name']; ?></td>
		<td align="right"><?php echo $detail['discount']; ?></td>
	</tr>
	<?php endforeach; ?>
</table>
<?php endif; ?>