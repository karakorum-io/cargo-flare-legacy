
<div id="acc_search_dialog">
	<table cellpadding="0" cellspacing="0" border="0" width="100%" class="form-table">
		<tr>
			<td width="100%"><input type="text" name="app_search_text" id="acc_search_string" style="width:98%" class="form-box-textfield" /></td>
			<td><?=functionButton('Search', "accountSearch()")?>
              
			<td>&nbsp;<span class="like-link multi-vehicles"><b>[?]</b></span>
                               <div class="search_help">
                               <p> 
                                 Company<br /> 
                                 Phone Number1 <br />
                                 Phone Number2<br /> 
                                 Contact Name1<br /> 
                                 Contact Name2<br /> 
                                 </p>
                               </div></td>
		</tr>
        
		<tr>
			<td colspan="3">
              <div style="overflow:scroll;  max-height:280px;">
				<ul id="acc_search_result"></ul>
              </div>  
			</td>
		</tr>
	</table>
</div>

<div id="acc_search_dialog_new_dispatch">
	<table cellpadding="0" cellspacing="0" border="0" width="100%" class="form-table">
		<tr>
			<td width="100%"><input type="text" name="app_search_text_new_dispatch" id="acc_search_string_new_dispatch" style="width:98%" class="form-box-textfield"/></td>
			<td><?=functionButton('Search', "accountSearchNewDispatch()")?></td>
			<td>&nbsp;<span class="like-link multi-vehicles"><b>[?]</b></span>
                               <div class="search_help">
                               <p> 
                                 Company<br /> 
                                 Phone Number1 <br />
                                 Phone Number2<br /> 
                                 Contact Name1<br /> 
                                 Contact Name2<br /> 
                                 </p>
                               </div></td>
		</tr>
		<tr>
			<td colspan="3">
				<ul id="acc_search_result_new_dispatch"></ul>
			</td>
		</tr>
        <tr id="colorCod" style="display:none;">
			<td colspan="3">
				<table width="100%">
                  <tr><td bgcolor="#F0FF1A" width="10%" ></td> <td>Insurance is about to expire.</td></tr>
                  <tr><td bgcolor="#FF1A24" width="10%"></td> <td>Insurance expired.</td></tr>
                </table>
			</td>
		</tr>
	</table>
</div>

<div id="acc_entity_dialog" style="display: none">
    You previously posted this order to Central Dispatch. Would you like to re-post it with updated information?
</div>
<script type="text/javascript">
	$(document).ready(function () {
								
		$('#acc_search_string_new_dispatch').keypress(function(e) {
							  
			if(e.which == 13) {
				//alert('You pressed enter!');
				var textOrder = $("#add_order").val();
				if(textOrder!="")
				  accountSearchNewDispatch();
		
			
				return false;
			}
	});	
								
		$("#acc_entity_dialog").dialog({
			autoOpen: false,
			modal: true,
			width: 400,
			resizable: false,
			draggable: true,
			buttons: [
				{
					text: 'Yes',
					click: function () {
						$("#post_to_cd").val('1');
						$("#save_order_form").submit();
						$(this).dialog('close');
					}
				},
				{
					text: 'No',
					click: function () {
						$("#post_to_cd").val('0');
						//alert($("#post_to_cd").val());
						$("#save_order_form").submit();
						$(this).dialog('close');
					}
				}
			]
		});
	});
</script>
