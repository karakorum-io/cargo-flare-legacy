<?php
/***************************************************************************************************
* Control Panel HTML Template - Coupons Edit                                                                                 *
*                                                                                                  *
* Client: 	PitBullTax                                                                             *
* Version: 	1.1                                                                                    *
* Date:    	2010-05-31                                                                             *
* Author:  	C.A.W., Inc. dba INTECHCENTER                                                          *
* Address: 	11555 Heron Bay Blvd, Suite 200, Coral Springs, FL - 33076                             *
* E-mail:	techsupport@intechcenter.com                                                           *
* CopyRight 2010-2011 NEGOTIATION TECHNOLOGIES, LLC. - All Rights Reserved                                 *
****************************************************************************************************/
?><br />
@flash_message@
<div align="center">
    <form action="<?=getLink('coupons/save')?>" method="post" enctype="multipart/form-data" name="MainForm" style="margin:0">
        <input name="id" type="hidden" value="@id@" />
        <input name="code" type="hidden" value="@code@" />
        <table cellpadding="3" cellspacing="0" border="0" class="form-table">
            <tr>
                <td align="right"><span class="required">*</span> Time to use:</td>
                <td align="left">
					@time_to_use@
					<input type="checkbox" name="is_per_customer" id="is_per_customer" @is_per_customer@ value="1" style="vertical-align: middle;" />
					<label for="is_per_customer">Per Customer</label>
				</td>
            </tr>
            <tr>
                <td align="right"><label for="expire_date"><span class="required">*</span> Expires:</label></td>
                <td align="left">
                	<input type="text" name="expire_date" id="expire_date" value="@expire_date@" style="width:96px" maxlength="100" /> &nbsp;
                    <input type="checkbox" name="is_never_expire" id="is_never_expire" @is_never_expire@ value="1" style="vertical-align: middle;" />
                    <label for="is_never_expire">Never expires</label>
                </td>
            </tr>
            <tr>
                <td align="right"><label for="company">Campaign:</label></td>
                <td align="left"><input type="text" name="company" id="company" value="@company@" style="width:300px" maxlength="100" /></td>
            </tr>
            <tr>
                <td align="right"><label for="status"><span class="required">*</span> Status:</label></td>
                <td align="left">@status@</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>
                	<div id="details">
                    	<table width="100%" cellpadding="0" cellspacing="0" border="0" class="coupon_details">
                        	<tr>
                            	<th>Code</th>
                            	<th>Product Name</th>
                            	<th>Discount</th>
                            	<th>Is Percent</th>
                            </tr>
                            @products@
                        </table>
                    </div>
                </td>
            </tr>
            <tr>
                <td align="right">&nbsp;</td>
                <td>
					<br /><br />
					<?=submitButtons(getLink("coupons"))?>
				</td>
            </tr>
        </table>
    </form>
</div>
<br />
<br />
<script type="text/javascript">
	$(function() {
		$("#expire_date").datepicker({
			changeMonth: true,
			changeYear: true,
			dateFormat: "dd/mm/yy"
		});
	});
</script>
