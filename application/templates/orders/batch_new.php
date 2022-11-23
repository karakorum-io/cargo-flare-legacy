<script language="javascript" type="text/javascript">



	$(document).ready(function(){

$('#add_order').keypress(function(e) {
								  //alert(e.which);
    if(e.which == 13) {
        //alert('You pressed enter!');
		var textOrder = $("#add_order").val();
        if(textOrder!="")
		  addSearchOrder(); 

	
		return false;
    }
});	

		

		var createForm = $('#create_form');

		createForm.find("input.shipper_company-model").autocomplete({

			source: function(request, response) {

				$.ajax({

					url: '<?= SITE_IN ?>application/ajax/autocomplete.php',

					type: 'GET',

					dataType: 'json',

					data: {

						term: request.term,

						action: 'getCompanyDataBatchPayment'

					},

					success: function(data) {

						response(data);

					}

				})

			},

			minLength: 0,

			autoFocus: true,

			select: function( event, ui ) {

				$( "#shipper_company" ).val( ui.item.company_name);

				$( "#shipper_company_id" ).val( ui.item.value );

				

				         

				return false;

			 },

			change: function() {

			

			}

		});

		

	});

function redirect_to_func()
{
	document.location.href = "/application/orders/batch";
}	

function addSearchOrder() {

	var textOrder = $("#add_order").val();

	var textOld = $("#batch_order_ids").val();

	

	if(textOrder !=''){

	  if(textOld == ''){

	     textOld = textOrder;

		 

	  }

	  else{

	     textOld += ','+textOrder;

		 

	  }

	  

	  $('#orders_list').append('<option value="'+textOrder+'" selected>'+textOrder+'</option>');

	$("#batch_order_ids").val(textOld);

	$("#add_order").val('');

	}

	   

} 	

function clear_batch_value()

{

		  

	$("#shipper_company").val('');

	$("#shipper_company_id").val('');

	$("#orders_list option:selected").remove();

	$("#batch_order_ids").val('');

}

		

</script>
<style>
div.form-box-buttons1 input{
  width: 180px;
  height: 23px;
  color: #fff;
  background-color:#06F;
  border: 0;
  font-size: 11px;
  font-family: Arial, Helvetica, sans-serif;
  cursor: pointer;
  font-weight: normal;
}
</style>

<br />

<br />

<table width="100%" cellspacing="0" cellpadding="0" border="0">
<tr>
    <td>
                 <table width="80%" cellspacing="0" cellpadding="0" border="0">
                
                    <tbody><tr>
                       <td align="right">
                                  <div class="form-box-buttons1" style="text-align:right !important;">
                <span id="submit1_button-submit-btn" style="-webkit-user-select: none;text-align:right !important;"><input type="button" id="redirect_to" value="Go To Shipper Payment" onclick="redirect_to_func();" style="-webkit-user-select: none;"></span>
                </div>
                      </td>
                    </tr>
                    <tr><td>&nbsp;</td></tr>
                   </tbody>
                   </table> 
 </td>
</tr>
<tr>

 <td align="center">

    <table width="60%" cellspacing="0" cellpadding="0" border="0">

    <tbody><tr>

    <td class="form-box-white-top-left"></td>

    <td class="form-box-white-top">&nbsp;</td>

    <td class="form-box-white-top-right"></td>

    </tr>

    <tr>

    <td class="form-box-white-content-left">&nbsp;</td>

    <td valign="top" class="form-box-white-content">

    <h4 style="color:#3B67A6">Carrier Batch Payment Processing</h4>

    

                <form method="post"  action="<?php print SITE_IN."application/orders/batchsubmitnew";?>" name="create_form" id="create_form">

                <table width="100%" cellspacing="1" cellpadding="1">

                

                <tr>

                 <td width="40%">

                    <table cellspacing="2" cellpadding="0" border="0">

                        <tr>

                            <td><span class="required">*</span>Order ID:</td>

                            <td valign="top">

                            <input type="text" id="add_order" maxlength="10" name="add_order" tabindex="41" class="zip form-box-textfield" style="width:170px;margin-left:5px;" kl_virtual_keyboard_secure_input="on">

                            

                            <!--input type="text" name="add_order" value="" id="add_order" /-->&nbsp;&nbsp;<img src="<?= SITE_IN ?>images/icons/add.gif"  width="20" height="20" onclick="addSearchOrder();"/></td>

                        </tr>

                        <tr><td colspan="2">&nbsp;</td></tr>

                        <tr>

                        <td>&nbsp;</td>

                        <td >

                          <select name='orders_list[]' id="orders_list" size=7 multiple style="width:200px;">



                          </select> 

                          <input type="hidden" name="batch_order_ids" id="batch_order_ids" value=""/>

                        </td></tr>

                        

                         <!--tr><td colspan="2">&nbsp;</td></tr>

                        

                        <tr>

                            <td>@batch_order_ids@</td>

                        </tr-->

                       

                        

                    </table>

                  </td>

                  <td width="10%" align="center">

                    <b>OR</b>

                  </td>

                  <td width="45%" align="left" valign="top">

                    <table cellspacing="2" cellpadding="0" border="0">

                         <tr><td colspan="2">&nbsp;</td></tr>

                         <tr><td colspan="2">&nbsp;</td></tr>

                        <tr>

                            <td>@shipper_company@

                                <input type="hidden" name="shipper_company_id" id="shipper_company_id" /></td>

                        </tr>

                        

                        <tr><td colspan="2">&nbsp;</td></tr>

                        <tr>

                            <td colspan="2" align="center">&nbsp;

                            <table width="100%" cellpadding="1" cellspacing="1">

                              <tr>

                                <td width="80%">

                            <?php print submitButtons(SITE_IN."application/orders/batch", "Locate Orders") ?>

                            <!--input type="submit" name="submit" value="Start Processing" /-->

                            <input type="hidden" name="submit" value="Start Processing" />

                              </td>

                              <td align="left">

                                <div class="form-box-buttons">

<span id="submit_button-submit-btn" style="-webkit-user-select: none;"><input type="button" id="clear_batch" value="Clear" onclick="clear_batch_value();" style="-webkit-user-select: none;"></span>

</div>

                              </td>

                              </tr>

                              </table>

                            </td>

                        </tr>

                    </table>

                  </td> 

                 </tr>

                

                  <tr><td colspan="2">&nbsp;</td></tr>

                        

                 </table>         

                </form>

    

    </td>

    <td class="form-box-white-content-right">&nbsp;</td>

    </tr>

    <tr>

    <td class="form-box-white-bottom-left"></td>

    <td class="form-box-white-bottom">&nbsp;</td>

    <td class="form-box-white-bottom-right"></td>

    </tr>

    </tbody></table>

 </td>

</tr>

</table>    