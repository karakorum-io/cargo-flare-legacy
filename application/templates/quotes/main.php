<script type="text/javascript">	var notes = [];	notes[<?= Note::TYPE_TO ?>] = [];	notes[<?= Note::TYPE_FROM ?>] = [];	notes[<?= Note::TYPE_INTERNAL ?>] = [];	var notesIntervalId = undefined;	var add_entity_id;	var add_notes_type;	var add_busy = false;		function printQuotes(printWindow, entity_ids) {		if (entity_ids.length > 0) {			$.ajax({				type: "POST",				url: "<?= SITE_IN ?>application/ajax/entities.php",				dataType: "json",				data: {					action: 'print',					entity_ids: entity_ids				},				success: function(response) {					if (response.success == true) {						printWindow.document.write('<html><head><title>Quotes</title>');						printWindow.document.write('<link rel="stylesheet" href="<?= SITE_IN ?>styles/application_print.css" type="text/css" />');						printWindow.document.write('</head><body><table cellspacing="0" cellpadding="3" border="1" width="100%">');						printWindow.document.write('<tr><th>ID</th><th>Quoted</th><th>Shipper</th><th>Vehicle</th><th>Origin/Destination</th><th>Tariff</th><th>Est. Ship</th></tr>');						for (i in response.data) {							printWindow.document.write('<tr>');							printWindow.document.write('<td class="nowrap">'+response.data[i].id+'</td>');							printWindow.document.write('<td>'+response.data[i].quoted+'</td>');							printWindow.document.write('<td>'+response.data[i].shipper+'</td>');							printWindow.document.write('<td>'+response.data[i].vehicle+'</td>');							printWindow.document.write('<td>'+response.data[i].origin_dest+'</td>');							printWindow.document.write('<td class="nowrap">'+response.data[i].tariff+'</td>');							printWindow.document.write('<td>'+response.data[i].est_ship+'</td>');							printWindow.document.write('</tr>');						}						printWindow.document.write('</table></body></html>');						printWindow.print();						printWindow.close();					} else {						printWindow.close();					}				}			});		} else {			printWindow.alert('You have no quotes to print');			printWindow.close();		}	}
function reassignQuotes(member) { 
          
        var member_id = 0;		
            member_id = member;		
            if ( member_id == 0 ) 
            {			
            Swal.fire("You must select member to assign");			
            return;		
            }        
            if ($(".entity-checkbox:checked").length == 0) {
            Swal.fire("You have no selected items.");
            } else {
            var entity_ids = [];
            $(".entity-checkbox:checked").each(function(){
            	entity_ids.push($(this).val());
            }); 
			
				 /*Swal.fire('Please wait')
				 Swal.showLoading()*/
		           
            $.ajax({            
            type: 'POST',            
            url: '<?= SITE_IN ?>application/ajax/entities.php',            
            dataType: "json",            
            data: {                
            action: 'reassign',                
            assign_id: member_id,                
            entity_ids: entity_ids.join(',')            
            },            
            success: function(response) 
            {               
            if (response.success) {                    
               window.location.reload();               
            } else {   
                 
            $(".error_reassing").css('display','block');
            $('#reassignCompanyDiv').find('.error_reassing').html('Reassign failed. Try again later, please');                
                   
              }            
            },           
            error: function(response) {  
                
                $(".error_reassing").css('display','block');
                $('#reassignCompanyDiv').find('.error_reassing').html('Reassign failed. Try again later, please');              
                       
            }        
            });	

		}	
}
</script>


<!--  -->

       <div id="notes_add1">
       <div class="modal fade" id="kt_modal_4" tabindex="-1" role="dialog" aria-labelledby="notes_add12" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="notes_add12">
                        <div id="notes_add_title"> </div>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i class="fa fa-times" aria-hidden="true"></i>
                    </button>
                </div>

                <div class="modal-body">
                     
                    <div class="form-group">
                    <div id="notes_container_new" > </div>

                    </div>

                    <div class="form-group">
                        <label for="message-text" class="form-control-label">Add Internal Note:</label>
                        <textarea class="form-control"  class="form-box-textarea" name="add_note_text" ></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                 <label for="message-text" class="form-control-label">Quick Notes:</label>
                                <select name="quick_notes" class="form-control" id="quick_notes" onchange="addQuickNote();">
                                    <option value="">--Select--</option>
                                    <option value="Emailed: Customer.">Emailed: Customer.</option>
                                    <option value="Emailed: Bad e-mail.">Emailed: Bad e-mail.</option>
                                    <option value="Faxed: e-Sign.">Faxed: e-Sign.</option>
                                    <option value="Faxed: B2B.">Faxed: B2B.</option>
                                    <option value="Faxed: Invoice.">Faxed: Invoice.</option>
                                    <option value="Faxed: Recepit.">Faxed: Recepit.</option>
                                    <option value="Phoned: Bad Mobile.">Phoned: Bad Number.</option>
                                    <option value="Phoned: No Voicemail.">Phoned: No Voicemail.</option>
                                    <option value="Phoned: Left Message.">Phoned: Left Message.</option>
                                    <option value="Phoned: No Answer.">Phoned: No Answer.</option>
                                    <option value="Phoned: Spoke to Customer.">Phoned: Spoke to Customer.</option>
                                    <option value="Phoned: Spoke to carrier about pick-up.">Phoned: Spoke to carrier about pick-up.</option>
                                    <option value="Phoned: NSpoke to carrier about drop-off.">Phoned: Spoke to carrier about drop-off.</option>
                                    <option value="Phoned: Customer requested carrier info.">Phoned: Customer requested carrier info.</option>
                                    <option value="Phoned: Customer requested damage.">Phoned: Customer requested damage.</option>
                                    <option value="Phoned: Customer canceled, late pick-up.">Phoned: Customer canceled, late pick-up.</option>
                                    <option value="Phoned: Customer canceled, no reason given.">Phoned: Customer canceled, no reason given.</option>
                                    <option value="Phoned: Customer canceled, through e-Mail.">Phoned: Customer canceled, through e-Mail.</option>
                                    <option value="Phoned: Customer was happy with transport.">Phoned: Customer was happy with transport.</option>
                                    <option value="Phoned: Customer was un-happy with transport.">Phoned: Customer was un-happy with transport.</option>
                                    <option value="Phoned: Customer want a refund.">Phoned: Customer want's a refund.</option>
                                    <option value="Phoned: Not Interested.">Phoned: Not Interested.</option>
                                    <option value="Phoned: Do Not Call.">Phoned: Do Not Call.</option>
                                </select>
                            </div>

                        </div>

                        <div class="col-md-6">
                            <div  class="form-group">
                               <label for="message-text" class="form-control-label">Priority:</label>
                                <select name="priority_notes"  class="form-control" id="priority_notes" >
                                    <option value="0">--Select--</option>
                                    <option value="2">High</option>
                                    <option value="1">Low</option>
                                </select>
                            </div>
                        </div>
                    </div>      

                    <?= functionButton('Add Note', 'addNote()','','btn-sm btn_dark_green') ?>
                    <?= functionButton('Cancel', 'closeAddNotes()','','btn-sm btn-dark') ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!--  -->

<!--  <div id="print_container" style="display:none"></div>
<div id="notes_container" ></div>

<div id="notes_add">
	<div id="notes_add_title"></div>
	<div id="notes_container_new" style="overflow-y:scroll;  max-height:280px; background-color:#ffffff; margin:5px; padding:5px;font-size:12px;"></div>	<br /><p></p>	
	<textarea class="form-box-textarea" name="add_note_text" style="padding-left:3px;font-size:11px;line-height:14px;color:#555;"></textarea>
		<div style="float:right;">	<table cellspacing="0" cellpadding="0" border="0">			<tr>				<td style="color:#00000;">Quick Notes&nbsp;</td><td>
			<select name="quick_notes" id="quick_notes" onchange="addQuickNote();">
<option value="">--Select--</value>
<option value="Emailed: Customer.">Emailed: Customer.</value>
<option value="Emailed: Bad e-mail.">Emailed: Bad e-mail.</value>
<option value="Faxed: e-Sign.">Faxed: e-Sign.</value>
<option value="Faxed: B2B.">Faxed: B2B.</value>
<option value="Faxed: Invoice.">Faxed: Invoice.</value>
<option value="Faxed: Recepit.">Faxed: Recepit.</value>
<option value="Phoned: Bad Mobile.">Phoned: Bad Number.</value>
<option value="Phoned: No Voicemail.">Phoned: No Voicemail.</value>
<option value="Phoned: Left Message.">Phoned: Left Message.</value>
<option value="Phoned: No Answer.">Phoned: No Answer.</value>
<option value="Phoned: Spoke to Customer.">Phoned: Spoke to Customer.</value>
<option value="Phoned: Spoke to carrier about pick-up.">Phoned: Spoke to carrier about pick-up.</value>
<option value="Phoned: NSpoke to carrier about drop-off.">Phoned: Spoke to carrier about drop-off.</value>
<option value="Phoned: Customer requested carrier info.">Phoned: Customer requested carrier info.</value>
<option value="Phoned: Customer requested damage.">Phoned: Customer requested damage.</value>
<option value="Phoned: Customer canceled, late pick-up.">Phoned: Customer canceled, late pick-up.</value>
<option value="Phoned: Customer canceled, no reason given.">Phoned: Customer canceled, no reason given.</value>
<option value="Phoned: Customer canceled, through e-Mail.">Phoned: Customer canceled, through e-Mail.</value>
<option value="Phoned: Customer was happy with transport.">Phoned: Customer was happy with transport.</value>
<option value="Phoned: Customer was un-happy with transport.">Phoned: Customer was un-happy with transport.</value>
<option value="Phoned: Customer want a refund.">Phoned: Customer want's a refund.</value>
<option value="Phoned: Not Interested.">Phoned: Not Interested.</value>
<option value="Phoned: Do Not Call.">Phoned: Do Not Call.</value>
</select></td><td><div style="float:left; padding:2px;">
&nbsp;&nbsp;&nbsp;Priority&nbsp;
</div>
<div style="float:left; padding:2px;"><select name="priority_notes" id="priority_notes" >
<option value="0">--Select--</option>
<option value="2">High</option>
<option value="1">Low</option>

</select>
</div>
</td><td><?= functionButton('Add Note', 'addNote()','','btn-sm btn_dark_green') ?></td>	
<td><?= functionButton('Cancel', 'closeAddNotes()','','btn-sm btn-dark') ?></td>			
</tr>		
</table>	
</div>
</div>  -->

<div style="padding-top: 10px;">
<? include_once("menu.php"); ?>
</div>	@content@</div>
<div style="clear: both">