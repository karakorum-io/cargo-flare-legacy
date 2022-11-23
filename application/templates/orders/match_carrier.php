<style type="text/css">
.header_
{
    padding-top: 21px;
}
h3.details
{
    padding: 22px 0 0;
    width: 100%;
    font-size: 20px;
}
</style>


<!--  -->


 <!--begin::Modal-->
    <div class="modal fade" id="maildivnew" tabindex="-1" role="dialog" aria-labelledby="maildivnewmodel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="maildivnewmodel">Send Email</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i class="fa fa-times" aria-hidden="true"></i>
                    </button>
                </div>
                <div class="modal-body">

                            
                    <div style="float: left;">
                    <ul style="margin-top: 26px;">  
                    <li style="margin-bottom: 14px;">Form Type <input value="1" id="attachPdf" name="attachTpe" type="radio"/><label for="attachPdf" style="margin-right: 2px; cursor:pointer;"> PDF</label><input value="0" id="attachHtml"  name="attachTpe" type="radio"/><label for="attachHtml" style="cursor:pointer"> HTML</label></li>
                    <li style="margin-bottom: 11px;">Attachment(s): <span style="color:#24709F;" id="mail_att_new"></span></li>
                    </ul>
                    </div>
                    <div style="text-align: right;">
                    <div style="text-align: right;">
                    <img src="/images/icons/add.gif"> <span style="margin-bottom: 3px;cursor:pointer; position: relative;bottom:4px; color:#24709F;" class="add_one_more_field_" >Add a Field</span>
                    <ul>
                    <li id="extraEmailsingle" style="margin-bottom: 6px;"><span>Email:<span style="color:red">*</span></span> <input type="text" id="mail_to_new" name="mail_to_new" class="form-box-combobox" ></li>
                    <li style="margin-bottom: 6px;margin-top: 6px;margin-left: 292px; position:relative; display: none;" id="mailexttra"><input name="optionemailextra" class="form-box-combobox optionemailextra" type="text"><a href="#" style="position: absolute;margin-left: 2px;margin-top: 8px;" class="remove_2sd_field"><img id="singletop" style="width: 12px;height: 12px;" src="/images/icons/delete.png"></a></li>
                    <li style="margin-bottom: 6px;"><span style="margin-right: 18px;">CC:</span> <input type="text" id="mail_cc_new" name="mail_cc_new" class="form-box-combobox" ></li>
                    <li style="margin-bottom: 12px;"><span style="margin-right: 9px;">BCC:</span> <input type="text" id="mail_bcc_new" name="mail_bcc_new" class="form-box-combobox" ></li>
                    </ul>
                    </div>
                    <div class="edit-mail-content" style="margin-bottom: 8px;">
                    <div class="form-group">
                    <label>Subject</label>
                   
                        <input type="text" id="mail_subject_new" class="form-box-textfield form-control" maxlength="255" name="mail_subject_new" style="width: 100%;">
                    </div>
                    <div class="form-group">
                    <div class="edit-mail-label">Body:<span>*</span></div>
                    <div class="edit-mail-field" style="width: 100%;"><textarea class="form-box-textfield" style="width: 100%;" name="mail_body_new" id="mail_body_new"></textarea></div>
                    </div>
                    </div>
                    <input type="hidden" name="form_id" id="form_id"  value=""/>
                    <input type="hidden" name="entity_id" id="entity_id"  value=""/>
                    <input type="hidden" name="skillCount" id="skillCount" value="1">
                    </div>
               
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-dark btn-sm" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn_dark_blue btn-sm" onclick="emailSelectedLeadFormNewsend()">Submit</button>
                </div>
            </div>
        </div>
    </div>

<!--  -->

<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor">
	<div class="kt-subheader kt-grid__item" id="kt_subheader">
		<div class="kt-subheader__main">
			<h3 class="kt-subheader__title">Carrier Match</h3>
			<span class="kt-subheader__separator kt-hidden"></span>
			<div class="kt-subheader__breadcrumbs">
				<span class="kt-subheader__breadcrumbs-separator"></span>
				<a href="<?php print SITE_IN;?>application/" class="kt-subheader__breadcrumbs-link">Home</a>
				<span class="kt-subheader__breadcrumbs-separator"></span>
				<a class="kt-subheader__breadcrumbs-link" href="<?php print SITE_IN;?>application/orders">Orders</a>
				<span class="kt-subheader__breadcrumbs-separator"></span>
				<a class="kt-subheader__breadcrumbs-link" href="<?php print SITE_IN;?>application/orders/show/id/<? echo $this->entity->id ?>">Order #<?= $this->entity->getNumber() ?></a>
			</div>
		</div>
	</div>
</div>


<?php 
if(is_array($_SESSION['searchData']) && $_SESSION['searchCount']>0) {
	//$_SESSION['searchShowCount'] = $_SESSION['searchShowCount'] + 1;
	
	$eid = $_GET['id'];
	$indexSearchData = array_search($eid,$_SESSION['searchData']);
	
	$nextSearch = $indexSearchData+1;
	$_SESSION['searchShowCount'] = $indexSearchData;
	$prevSearch = $indexSearchData-1;
	
	$entityPrev = $_SESSION['searchData'][$prevSearch];
	$entityNext = $_SESSION['searchData'][$nextSearch];
}
?>

<?php include('order_menu.php');  ?>

<div class="col-3" style="margin-top:-50px;margin-bottom:15px;">
	<h3 class="details">Carriers Matched for Order #<?= $this->entity->getNumber() ?></h3>
</div>

<div class="col-12">
<table class="table table-bordered">
	<tr>
		<td></td>
	</tr>
	<?php if (count($this->MatchCarrier) == 0) : ?>
	<tr>
		<td style="text-align: center;">You have no matching carrier</td>
	</tr>
	<?php endif; ?>
	<?php
		$i=1;
		$dateCreatedTemp = '';
		$account = new Account($this->daffny->DB);
		foreach ($this->MatchCarrier as $mcarrier) {
		try{
			if($mcarrier->account_id !=0 && $mcarrier->account_id!='')
			$account->load($mcarrier->account_id);				
		} catch (FDException $e) {
			continue;
		}			
		if($account instanceof Account)
		{
			$createdDate = date("m/d/Y", strtotime($mcarrier->created));
			if( $createdDate != $dateCreatedTemp ){
				if($dateCreatedTemp !=''){
					print "</table></td></tr><tr><td>&nbsp;</td></tr>";
				$i=1;
				}
	?>
	
    <h3>This order was matched on: <?= $createdDate?></h3>
    
    <table class="table table-bordered" id="carrier_match" >
    	<thead>
	<tr>
		<th>Num #</th>
        <th>Created</th>
        <th>Carrier</th>
        <th>Phone #</th>
		<th>Email</th>
        <th>Carrier Pay</th>
		<th>Mail Sent</th>
		
	</tr>
	</thead>
    <?php 
	    $dateCreatedTemp = $createdDate;
	   }
	?>
	<tr  id="row-<?=$mcarrier->id?>">
       <td class="grid-body-left"><?= $i?></td>
		<td>
			<?php print date("m/d/y h:i a", strtotime($mcarrier->created));?>
		</td>
        <td><? print $account->company_name ?></td>
         <td><? print $account->phone1 ?></td>
		<td><?= $mcarrier->email ?></td>
        <td><?= ("$ " . number_format((float)$mcarrier->carrier_pay_stored, 2, ".", ","))  ?></td>
        <td><?php ($mcarrier->mail_status==0)?print "<span style='color:red;'>".MatchCarrier::$attributeSentStatus[$mcarrier->mail_status]."</span>":print "<span style='color:green;'>".MatchCarrier::$attributeSentStatus[$mcarrier->mail_status]."</span>";?></td>
             
	</tr>
    
	<?php $i++; }} ?>
    </table>
   
</table>

<?php if (count($this->MatchCarrier) != 0) : ?>
@pager@
<?php endif; ?>

</div>
</div>


<script type="text/javascript">
	$(document).ready(function() {
    $('#carrier_match').DataTable({
    	"lengthChange": false,
    	"paging": false,
    	"bInfo" : false,
    	'drawCallback': function (oSettings) {
    		$('#carrier_match_wrapper').children('.row:first').children('.col-md-6:first').html('<div class="form-group row" style="margin-left:0;"><label class="col-form-label">Show: </label><select class=" records_per_page form-box-combobox form-control " style="width:100px; margin-left:5px" onchange="setPagerLimit(this.value)"> <option value="10">10</option> <option value="25">25</option> <option value="50" selected="selected">50</option> <option value="100">100</option> <option value="200">200</option> <option value="500">500</option> </select></div>');
    		$('#carrier_match_wrapper').children('.row:last').children('.col-md-5').html($('.pager').clone()).addClass('text-left');
    		$('#carrier_match_wrapper').children('.row:last').children('.col-md-7').html($('.table_b ').html()).addClass('text-right');
    		$('.pages-div-custom').remove();
    		// $("#datatableCampaigns_length").addClass("col-6 pd-0 float-left");
           // $("#datatableCampaigns_filter").addClass("p-0 col-6 float-left");
           // $('.dataTables_paginate > .pagination').addClass('pagination-rounded');
           // $(document).find('.popoverOption').tooltip({trigger: 'hover'});
       }
    });
} );
</script>


<script type="text/javascript">
		 function emailSelectedOrderFormNew() {

		 	 console.log("onthe work");

        form_id = $("#email_templates").val();
        if (form_id == "") {
           Swal.fire("Please choose email template");
        } else {

              Processing_show();
                $.ajax({
                    type: "POST",
                    url: BASE_PATH + "application/ajax/entities.php",
                    dataType: "json",
                    data: {
                        action: "emailOrderNew",
                        form_id: form_id,
                        entity_id: <?=$this->entity->id?>
                    },
                    success: function (res) {
                        if (res.success) {
                            
                        $("#maildivnew").modal();
                        $/*("#maildivnew").empty();*/
                        
                        $('.add_one_more_field_').on('click',function(){
                            $('#mailexttra').css('display','block');
                            return false;
                        });
                        $('#singletop').on('click',function(){
                            $('#mailexttra').css('display','none');
                            $('.optionemailextra').val('');
                        });

                        $("#form_id").val(form_id);
                        $("#mail_to_new").val(res.emailContent.to);
                        $("#mail_subject_new").val(res.emailContent.subject);
                        $("#mail_body_new").val(res.emailContent.body);

                         /* CKEDITOR.instances['mail_body_new'].setData(res.emailContent.body)
                          //Calling CKEDITOR instance #Chetu
                          ckRefresher('new');*/

                          $("#mail_att_new").html(res.emailContent.att);

                            if(res.emailContent.atttype > 0){
                                $("#attachPdf").attr('checked', 'checked');
                            }else{
                                $("#attachHtml").attr('checked', 'checked');
                            }

                        } else {
                            Swal.fire("Can't send email. Try again later, please");
                        }
                    },
                    complete: function (res) {
                        KTApp.unblockPage();
                    }
                });


        }
    }


 function emailSelectedLeadFormNewsend()
		{
		var sEmail = [$('#mail_to_new').val(), $('.optionemailextra').val(), $('#mail_cc_new').val(), $('#mail_bcc_new').val()];
		if (validateEmail(sEmail) == false) {
		    swal.fire('Invalid Email Address');
		    return false;
		}
		if ($('#attachPdf').is(':checked')) {
		    attach_type = $('#attachPdf').val();
		} else {
		    attach_type = $('#attachHtml').val();
		}
		;
		$.ajax({
		    url: BASE_PATH + 'application/ajax/entities.php',
		    data: {
		        action: "emailQuoteNewSend",
		        form_id: $('#form_id').val(),
		        entity_id: $('#entity_id').val(),
		        mail_to: $('#mail_to_new').val(),
		        mail_cc: $('#mail_cc_new').val(),
		        mail_bcc: $('#mail_bcc_new').val(),
		        mail_extra: $('.optionemailextra').val(),
		        mail_subject: $('#mail_subject_new').val(),
		        mail_body: $('#mail_body_new').val(),
		        attach_type: attach_type
		    },
		    type: 'POST',
		    dataType: 'json',
		    beforeSend: function () {
		          
		           $("#maildivnew").find('.modal-body').addClass('kt-spinner kt-spinner--lg kt-spinner--dark');
		        if ($('#mail_to_new').val() == "" || $('#mail_subject_new').val() == "" || $('#mail_body_new').val() == "") {
		            swal.fire('Empty Field(s)');
		            return false;
		        }
		        ;
		    },
		    success: function (response) {
		        // $("body").nimbleLoader("hide");
		        if (response.success == true) {

		            $("#maildivnew").modal('hide');
		               clearMailForm();
		        }

		    },
		    complete: function () {
		         $("#maildivnew").find('.modal-body').removeClass('kt-spinner kt-spinner--lg kt-spinner--dark');
		         $("#maildivnew").modal('hide');
		    }
		});
		}

		function validateEmail(sEmail) {
        var res = "", res1 = "", i;
        var filter = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
        for (i = 0; i < sEmail.length; i++) {
            if (filter.test(sEmail[i])) {
                res += sEmail[i];
            } else {
                res1 += sEmail[i];
            }
        }
        if (res1 !== '') {
            return false;
        }
    }

	</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.11.4/ckeditor.js">
</script>
<script>
	CKEDITOR.replace('mail_body_new');
</script>	