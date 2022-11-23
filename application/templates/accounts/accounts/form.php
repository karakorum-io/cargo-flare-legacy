<?
$shipperAccess = 0;
if($_SESSION['member']['access_shippers']==1 || 
	$_SESSION['member']['parent_id']==$_SESSION['member_id'] )
	{
		$shipperAccess = 1;
	}
?>

<script>
function sendToQuickbook(type) {
	
	Processing_show();
	$.ajax({
		type: 'POST',
		url: BASE_PATH+'application/ajax/accounts.php',
		dataType: 'json',
		data: {
			id: <?=get_var("id")?>,
			type:type,
			action: "createCustomerVendorQuickbook"
		},
		success: function(res) {
			console.log(res);
			
			if (res.success) {
				Swal.fire('Account Request Success.');
			} else {
				Swal.fire('Account Request Failed.');
			}
		},
		failed: function(res) {
			Swal.fire('Account Request Failed.');
		},
		complete: function(res) {
			KTApp.unblockPage();
		}
	});
}
</script>


<script type="text/javascript" src="<?= SITE_IN ?>jscripts/jquery.ajaxupload.js"></script>
<script type="text/javascript" src="<?= SITE_IN ?>jscripts/dropzone.js"></script>
<link href="<?= SITE_IN ?>application/assets/css/dropzone.css" type="text/css" rel="stylesheet"/>

<?
if (isset($_GET['id']) && $_GET['id'] > 0) {
    include(TPL_PATH . "accounts/accounts/menu_details.php");
} else {
    include(TPL_PATH . "accounts/accounts/menu.php");
}	
?>

<form action="<?= getLink("accounts", "edit", "id", get_var("id")) ?>" method="post" id="account_edit_form" enctype="multipart/form-data">
	
	<div class="kt-portlet">
		
		<div class="kt-portlet__head">
			<div class="kt-portlet__head-label">
				<?= formBoxStart("Account Status") ?>
			</div>
		</div>
		
		<div class="kt-portlet__body">
		
			<div class="kt-section__info m_btm_10">Complete the form below and click "Save Account" when finished.</div>
			
			<div class="row">
			
				<div class="col-6">
				
					<div class="form-group">
						@status@
					</div>
					
					<div class="row">
						<div class="col-6">
							<div class="form-group">
							@rating@
							</div>
						</div>
						<div class="col-6">
							<div class="form-group">
								<label></label>
								<label></label>
								<br/>
								<br/>
								@donot_dispatch@
							</div>
						</div>
					</div>
				</div>
					
				<div class="col-6">					
					<div class="form-group input_wdh_100_per">
						@notes@
					</div>
				</div>
					
			</div>

			<div class="row">
				<div class="col-4">
					<div class="form-group">
						@is_carrier@
					</div>
				</div>
				<div class="col-4">
					<div class="form-group">
						@is_shipper@
					</div>
				</div>

				<div class="col-4">
					<div class="form-group">
						@is_location@
					</div>
				</div>
			</div>		
			
		</div>

		<?= formBoxEnd() ?>	
	</div>



<div id="acctableinfo">
	
		<div class="kt-portlet">
			
			<div class="kt-portlet__head">
				<div class="kt-portlet__head-label">
					<?= formBoxStart("Account Information") ?>
				</div>
			</div>
			
			<div class="kt-portlet__body">
			<div class="row">
			
				<div class="col-4">
					<div class="form-group">
						@company_name@
					</div>
				</div>
				
				<div class="col-4" id="first_name_tr" style="display:none;">
					<div class="form-group">
						<label for="cp_login_banner">CP Login Banner:<span class="required">*</span></label>
						<input name="cp_login_banner" type="file" class="form-box-textfield" id="cp_login_banner"/>
						<br/><br/>
						<?php echo $this->login_banner == null ? 'No Image Uploaded' : '<a href="/uploads/CP_Login_Banners/'.$this->login_banner.'" target="_blank">Click Here to View</a>' ?>
					</div>
				</div>
				
				<div class="col-4" id="first_name_tr" style="display:none;">
					<div class="form-group">
						@first_name@
					</div>
				</div>
				
				<div class="col-4" id="last_name_tr" style="display:none;">
					<div class="form-group">
						@last_name@
					</div>
				</div>
				
				<div class="col-4">
					<div class="form-group">
						@insurance_iccmcnumber@
					</div>
				</div>

				<div class="col-4">
					<div class="form-group">
						@us_dot@
					</div>
				</div>
				
				<div class="col-4">
					<div class="form-group">
						@tax_id_num@
					</div>
				</div>
				
				<div class="col-4">
					<div class="form-group">
						@contact_name1@
					</div>
				</div>
				
				<div class="col-4">
					<div class="form-group">
						@contact_name2@
					</div>
				</div>
				
				<div class="col-4">
					<div class="form-group">
						@phone1@
					</div>
				</div>
				
				<div class="col-4">
					<div class="form-group">
						@phone2@
					</div>
				</div>
				
				<div class="col-4">
					<div class="form-group">
						@cell@
					</div>
				</div>
				
				<div class="col-4">
					<div class="form-group">
						@fax@
					</div>
				</div>
				
				<div class="col-4">
					<div class="form-group">
						@email@
					</div>
				</div>
				
				<div class="col-4">
					<div class="form-group">
						<label>Email Option:</label><br/>
						@unsubscribe@
					</div>
				</div>
				
				<div class="col-4" id="location_type_tr">
					<div class="form-group">
						@location_type@
					</div>
				</div>
				
				<div class="col-4" id="carrier_type_tr">
					<div class="form-group select_opt_new_info select_wdh_100_per">
						@carrier_type@
					</div>
				</div>
				
				<div class="col-4" id="shipper_type_tr">
					<div class="form-group select_opt_new_info select_wdh_100_per">
						@shipper_type@
					</div>
				</div>
				
				<div class="col-4" id="hours_of_operation_tr">
					<div class="form-group">
						@hours_of_operation@
					</div>
				</div>
				
				<div class="col-4" id="referred_by_tr">
					<div class="form-group">
						@referred_id@
					</div>
				</div>
				
				<div class="col-4">
					<div class="form-group">
						@address1@
						<div id="suggestions-box"></div>
					</div>
				</div>
				
				<div class="col-4">
					<div class="form-group">
						@address2@
					</div>
				</div>
				
				<div class="col-4">
					<div class="form-group">
						<span id="r1" class="required">*</span> @city@
					</div>
				</div>
				
				<div class="col-4" id="st">
					<div class="form-group">
						<span id="r2" class="required">*</span>@state@
					</div>
				</div>
				
				<div class="col-4" style="display:none;" id="st_other">
					<div class="form-group">
						<span id="r3" class="required">*</span>@state_other@
					</div>
				</div>
				
				<div class="col-4">
					<div class="form-group input_wdh_100_per">
						<span id="r4" class="required">*</span>@zip_code@
					</div>
				</div>
				
				<div class="col-4">
					<div class="form-group">
						<span id="r5" class="required">*</span>@country@
					</div>
				</div>
				
				<div class="col-4">
					<div class="form-group">
						<label> </label>
						<br/>
						<br/>
						<span style="font-weight: bold;" class="hint--top hint--rounded hint--bounce" data-hint="The system will use this address to print all checks to this account">@print_check@</span>
					</div>
				</div>
				
				<div class="col-4">
					<div class="form-group">
						@print_name@
					</div>
				</div>
				
				<div class="col-4">
					<div class="form-group">
						@print_address1@
					</div>
				</div>
				
				<div class="col-4">
					<div class="form-group">
						@print_address2@
					</div>
				</div>
				
				<div class="col-4">
					<div class="form-group">
						<span id="r1" class="required">*</span>@print_city@
					</div>
				</div>
				
				<div class="col-4" id="st">
					<div class="form-group">
						<span id="r2" class="required">*</span>@print_state@
					</div>
				</div>
				
				<div class="col-4" style="display:none;" id="st_other">
					<div class="form-group">
						<span id="r3" class="required">*</span>@print_state_other@
					</div>
				</div>
				
				<div class="col-4">
					<div class="form-group select_opt_new_info input_wdh_100_per">
						<span id="r4" class="required">*</span>@print_zip_code@
					</div>
				</div>
				
				<div class="col-4">
					<div class="form-group">
						<span id="r5" class="required">*</span>@print_country@
					</div>
				</div>
				
			</div>
			
		</div>
		<?= formBoxEnd() ?>
	</div>	
</div>	
<br>
<?php if($this->accountType == "Shipper" && $_SESSION['parent_id'] ==1){?>
	<br />
<div id="user_com_div">	
    <?=formBoxStart("User/Salesman Commission Information")?>  

	<div class="row ">
	<div class="col-12 text-right mb-3 ">
	<?php if($this->accountType == "Shipper"  && $_SESSION['member_id'] ==1){?>
	<?= functionButton("Reassign Account", "manageSalesrep()",'','btn-sm btn_bright_blue ') ?>
	<?php } ?>
	</div>
	</div>
			
	
    <table class="table table-bordered" >
		<tr >
			<td class="grid-head-left">Num</td>
			<td class="grid-head-left">Shipper</td>
			<td>Salesman</td>
			<td>Reffered By</td>
			<td>Commission</td>
			<td>Primary</td>
		   <td width="60" class="grid-head-right" colspan="3">Actions</td>
		</tr>
   
		<? if (count($this->commissionData)>0){?>
	    <? foreach ($this->commissionData as $i => $commission) { ?>        
		<tr class="grid-body<?=($i == 0 ? " " : "")?>" id="row-<?=$commission['id']?>">
	        <td>
	        	<?= $i+1;?>
	        </td>
	        <td>
	        	<?=htmlspecialchars($commission['company_name']);?>
	        </td>
            <td>
	        	<?=htmlspecialchars($commission['contactname']);?>	       
	        </td>
	        <td><?=htmlspecialchars($commission['reffered_by']);?></td>
	        <td><?=htmlspecialchars($commission['commision']);?></td>
            <td>
				<?php if($commission['primary']==1){ ?>		  
				<b style="color:red;font-size:20px;">&radic;</b>
				<?php }else{ ?>
					<b>X</b>
				<?php }?>
            </td>	        
	        <td style="width:16px;"><?=editIcon(getLink("accounts", "shippersCommEdit", "id", $commission['id'],"shipper",get_var("id")))?></td>
			<td style="width:16px;" class="grid-body-right"><?=deleteIcon(getLink("accounts", "shippersCommDelete", "id", $commission['id']), "row-".$commission['id'])?></td>
	    </tr>
	    <? } ?>
		<?} else {?>
		<tr class="grid-body " id="row-">
	        <td align="center" colspan="9">No records found.</td>
	    </tr>
		<? } ?>
	</table>

<?=formBoxEnd()?>
</div>
<?php }?>

	<div id="ins_div" style="display:none;">
		
		<div class="kt-portlet">
		
			<div class="kt-portlet__head">
				<div class="kt-portlet__head-label">
					<?=formBoxStart("Insurance Information")?>
				</div>
			</div>
			
			<div class="kt-portlet__body">
				<div class="row">
				
					<div class="col-4">
						<div class="form-group">
							@insurance_companyname@
						</div>
					</div>
					<div class="col-4">
						<div class="form-group">
							@insurance_address@
						</div>
					</div>
					<div class="col-4">
						<div class="form-group">
							@insurance_phone@
						</div>
					</div>
					
					<div class="col-4" id="ins_holder_tr">
						<div class="form-group select_wdh_100_per">
							@insurance_holder@
						</div>
					</div>
					
					<div class="col-4" id="ins_insured_tr">
						<div class="form-group select_wdh_100_per">
							@insurance_insured@
						</div>
					</div>
					
					<div class="col-4" id="ins_contract_tr">
						<div class="form-group select_wdh_100_per">
							@insurance_contract@
						</div>
					</div>
				</div>
					
				<div class="row">
					
					<div class="col-4">
						<div class="form-group select_wdh_100_per">
							@insurance_agentname@
						</div>
					</div>
					
					<div class="col-4">
						<div class="form-group select_wdh_100_per">
							@insurance_agentphone@
						</div>
					</div>
					
					<div class="col-4">
						<div class="form-group select_wdh_100_per">
							@insurance_policynumber@
						</div>
					</div>
					
					<div class="col-4" id="ins_contract_tr">
						<div class="form-group select_wdh_100_per">
							@insurance_liability_amount@
						</div>
					</div>
					
					<div class="col-4" id="ins_contract_tr">
						<div class="form-group select_wdh_100_per">
							@insurance_coverage@
						</div>
					</div>
					
					<div class="col-4" id="ins_contract_tr">
						<div class="form-group select_wdh_100_per">
							@insurance_cargo_deductible@
						</div>
					</div>
				</div>
				
				<div class="row">
					
					<div class="col-5">
						<div class="form-group select_wdh_100_per">
							@insurance_type@
						</div>
						
						<div class="form-group input_wdh_100_per">
							@insurance_expirationdate@
						</div>
					</div>
					
					<div class="col-7">
						<div>
							<ul class="files-list" id="cat">
								<?php if (isset($this->files) && count($this->files)) { ?>
									<? foreach ($this->files as $file) { ?>
									<li id="file-<?= $file['id'] ?>">
									   <?php print Account::$ins_tupe_name[$file['insurance_type']].": ";?>
										<?=$file['img']?>
										<a href="<?=getLink("accounts", "getdocs", "id", $file['id'],"type",1)?>"><b>View</b> <?=date("m/d/y", strtotime($file['insurance_expirationdate']))?></a>
										&nbsp;&nbsp;&nbsp;
										<a href="#" onclick="return deleteFile('<?php echo getLink("accounts", "delete-file"); ?>', <?php echo $file['id']; ?>);">
											<img src="<?= SITE_IN ?>images/icons/delete.png" alt="delete" style="vertical-align:middle;" width="16" height="16"/>
										</a>													
									</li>
									<?php } ?>
								<?php } else { ?>
									<li id="nodocs">No insurance documents.</li>
								<?php } ?>
							</ul>
						</div>
						<div>
							<img id="upload_process" src="<?= SITE_IN ?>images/uploading_file.gif" alt="uploading..." style="display: none;"/>
						</div>
						<div action="#" id="dropzdoc" class="dropzone" style="width:100%;line-height:16px;min-height:50px !important;height:120px !important;padding-top:0;padding-bottom:0;"></div>
					</div>
					
				</div>
			</div>
		<?=formBoxEnd()?>
		</div> 
	</div> 

	<div style="width:100%;">
		<div style="width:40%;float:left;">
		  <?=submitButtons(getLink("accounts"), "Save")?>
		</div> 
       
    <?php if($_SESSION['member']['parent_id'] == 1){?>
		<div id="customer_quickbook" class="text-right" <?php if($this->accountType == "Shipper"){ }else{?>style="display:none;" <?php }?> >
			<?=  functionButton('Fix Customer to Create in Quickbook', 'sendToQuickbook(\'4\')','','btn btn-sm btn-dark');?>
			<?=  functionButton('Sync Customer in Quickbook', 'sendToQuickbook(\'5\')','','btn btn-sm btn_dark_green');?>
			<?=  functionButton('Create Customer in Quickbook', 'sendToQuickbook(\'1\')','','btn btn-sm btn_bright_blue ');?>
		</div>
        <div id="vendor_quickbook" class="text-right" <?php if($this->accountType == "Carrier"){ }else{?>style="display:none;" <?php }?> >			
			<?=  functionButton('Fix Vendor to Create in Quickbook', 'sendToQuickbook(\'3\')','','btn btn-sm btn-dark');?>
			<?=  functionButton('Sync Vendor in Quickbook', 'sendToQuickbook(\'6\')','','btn btn-sm btn_dark_green');?>
			<?=  functionButton('Create Vendor in Quickbook', 'sendToQuickbook(\'2\')','','btn btn-sm btn_bright_blue ');?>
        </div>
    <?php }?>                 
	       <?php /*if($this->accountType == "Shipper"){?> 
                  <div style="width:25%;float:right;">
                     <?=  functionButton('Create Customer in Quickbook', 'sendToQuickbook(\'1\')');?>
                   </div>
                   <div style="width:15%;float:right;" class="createQB">
						    <?=  functionButton('Fix to Create in Quickbook', 'sendToQuickbook(\'4\')');?>
                           </div>
                    <?php }
					     else
						 {  ?>
                         <div style="width:25%;float:right;" class="createQB">
                            <?=  functionButton('Fix to Create in Quickbook', 'sendToQuickbook(\'3\')');?>
                            </div>
                         <div style="width:15%;float:right;" class="createQB">
						    <?=  functionButton('Create in Quickbook', 'sendToQuickbook(\'2\')');?>
                           </div>
                           
                           <?php
						}*/
					?>
                    
       </div>  
</form>
<script type="text/javascript">
    $(function(){
        function checkCountry(){
            if ($("#country").val() == "US"){
                $("#st_other").hide();
                $("#st").show();
            }else{
                $("#st").hide();
                $("#st_other").show();
            }
        }

        function checkType(){
            if ($('#is_location').is(':checked')){
                $("#r1,#r2,#r3,#r4,#r5").show();
            }else{
                $("#r1,#r2,#r3,#r4,#r5").hide();
            }
        }

        function checkFLNames(){
            if ($('#is_shipper').is(':checked')){
                $("#first_name_tr,#last_name_tr,#tax_id_num_tr").show();
                $("#shipper_rt").hide();
            }else{
                $("#first_name_tr,#last_name_tr,#tax_id_num_tr").hide();
                $("#shipper_rt").show();
            }
        }

        function checkInsFields(){
            if ($('#is_carrier').is(':checked')){
                $("#ins_holder_tr").show();
                $("#ins_insured_tr").show();
                $("#ins_contract_tr").show();
                $("#ins_iccmc_tr").show();
            }else{
                $("#ins_holder_tr").hide();
                $("#ins_insured_tr").hide();
                $("#ins_contract_tr").hide();
                $("#ins_iccmc_tr").hide();
            }
        }

		function checkInsurance(){
			$("#ins_div").show();
		}

		function notselected(){
			if(!($('#is_carrier').is(':checked'))&& !($('#is_location').is(':checked'))&& !($('#is_shipper').is(':checked'))){
				$('#acctableinfo').hide();
				$("#ins_div").hide();
			}
			if(!($('#is_location').is(':checked'))){
				$("#ins_div").hide();
			}
			if(!($('#is_shipper').is(':checked'))){
				$("#ins_div").hide();
			}
			if($('#is_carrier').is(':checked')){
				$("#ins_div").show();
			}
		}

        function typesNames(){
            if ($('#is_location').is(':checked')){
                $("#location_type_tr").show();
                $("#first_name_tr").hide();
                $("#last_name_tr").hide();
                $("#tax_id_num_tr").hide();
                $("#tax_id_num_tr").hide();
                $("#ins_iccmc_tr").hide();
                //$("#col-3,.createQB").hide();
				$("#customer_quickbook").hide();
				$("#vendor_quickbook").hide();
            }else{
                $("#location_type_tr").hide();
            }
            if ($('#is_carrier').is(':checked')){
                $("#carrier_type_tr").show();
                $("#first_name_tr").hide();
                $("#last_name_tr").hide();
                $("#tax_id_num_tr").hide();
                //$("#col-3,.createQB").show();
				$("#customer_quickbook").hide();
				$("#vendor_quickbook").show();
            }else{
                $("#carrier_type_tr").hide();
            }
            if ($('#is_shipper').is(':checked')){
                $("#shipper_type_tr").show();
                $("#ins_iccmc_tr").hide();
               // $("#col-3,.createQB").hide();
			   $("#vendor_quickbook").hide();
			   $("#customer_quickbook").show();
			
            }else{
                $("#shipper_type_tr").hide();
            }

			if ($('#is_shipper').is(':checked')){
                $("#referred_by_tr").show();
            }else{
                $("#referred_by_tr").hide();
            }
			
        }

        $("#country").change(function(){
            checkCountry();
        });

        $("#is_location").click(function(){
			if($(this).is(':checked')){
				$('#is_carrier,#is_shipper').attr('checked', false);
				$('#acctableinfo').show();
				$("#ins_div").hide(); 
				$("#user_com_div").hide();
				checkType();
				typesNames();
			}else{
				$('#acctableinfo').hide();
				$("#user_com_div").hide();
				
			}
        });

        $("#is_carrier").click(function(){
			 if(!($('#shipper_type_tr,#location_type_tr').is(':visible'))&& !($(this).is(':checked'))){
				$('#acctableinfo').hide();
				$("#ins_div").hide();
				$("#user_com_div").hide();
				$('#is_location,#is_shipper').attr('checked', false);
			 }else{
				$('#is_location,#is_shipper').attr('checked', false);
                                $("#user_com_div").hide();
				$('#acctableinfo').show();
				checkInsFields();
				typesNames();
				url=($(location).attr('href'));
				value = url.substring(url.lastIndexOf('/') + 1);
				if($.isNumeric(value)){
					$("#ins_div").show();
				}else{
					$("#ins_div").hide(); 
				}
				}
        });
        
        $("#is_shipper").click(function(){
			if($(this).is(':checked')){
				$('#is_carrier,#is_location').attr('checked', false);
				$('#acctableinfo').show();
				$("#ins_div").hide(); 
				if($('#ShppierComs').length > 0){
					$("#user_com_div").show();
				}else{
					$("#user_com_div").hide();
				}		
				checkFLNames();
				typesNames();
			}else{
				$('#acctableinfo').hide();
				$("#ins_div").hide();
				$("#user_com_div").hide();
			}
        });

        checkCountry();
        checkType();
        checkFLNames();
		checkInsurance();
        checkInsFields();
        typesNames();
		notselected();

        $('#insurance_expirationdate').datepicker(datepickerSettings);
		<? if (isset($this->data->member_id)) { ?>
		$("#account_edit_form").find("input, textarea").attr("readonly", "readonly");
		$("#account_edit_form").find("select, input[type='checkbox']").attr("disabled", "disabled");
		$("#account_edit_form").find("#donot_dispatch, #status, #notes, input[type='button'], input[type='submit']").attr("readonly", null);
		$("#account_edit_form").find("#donot_dispatch, #status, #is_carrier, #notes, input[type='button'], input[type='submit']").attr("disabled", null);
		$("#is_carrier").unbind("click");
		$("#is_carrier").click(function(){
			return false;
		});
		<? } ?>
    });
	
	function manageSalesrep()
	{
		"<a href='<?= getLink("accounts", "shippersComm", "id", get_var("id")) ?>'>  </a>"    
	}
    
  	$(function(){	
		new AjaxUpload('#insurance_doc', {
				action: '<?=getLink("accounts", "upload_insurance", "id", get_var("id") ) ?>>',
				name: 'insurance_doc',
				
			onChange: function(file, extension){
				this.setData({insurance_type: $('#insurance_type').val(),insurance_expirationdate: $('#insurance_expirationdate').val()});
			},
			onSubmit: function(file , ext){

				if (!(ext && /^(pdf)$/.test(ext))){
					alert('Invalid file extension.\nAllowed file extensions: *.pdf');
					return false;
				}
				
				$('#upload_process').fadeIn();
			},
			onComplete : function(file, response){
				$('#upload_process').fadeOut();
				$('#upload_process').hide();
				if (response.indexOf('ERROR:') != -1) {
					alert('Cant\' upload file.\n'+response);
					return false;
				}
				$('#cat').html(response);
			}
		});
	});

	Dropzone.autoDiscover = false;

	var valid_extensions=  /(\.pdf|\.doc|\.docx|\.xls|\.xlsx|\.jpg|\.jpeg|\.png|\.tiff|\.wpd)$/i;
	var ins_Type='';
	var ins_Date='';

	ins_Type = $('#insurance_type option:selected').val();
	ins_Date= $('#insurance_expirationdate').val();
	
	$('#insurance_type').on('change',function(){
		ins_Type=$(this).val();          
	});

	$('#insurance_expirationdate').on('change',function(){
		ins_Date=$(this).val();          
	});

	myDropzone = new Dropzone("#dropzdoc",{
		url: '<?php echo getLink("accounts", "upload_insurance", "id", get_var("id") ); ?>',
		sending: function(file, xhr, formData) {
			formData.append("insurance_type", ins_Type);
			formData.append("insurance_expirationdate", ins_Date);
		},
		acceptedFiles:".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.tiff,.wpd",
		paramName: "insurance_doc",
		maxFilesize: 3,
		createImageThumbnails:false,
		dictDefaultMessage: '<div style="text-align:left;">- Select your Insurance Type.<br>\n\
							- Select the Expiration Date.<br>\n\
							- Drop your file here.<br><div>\n\
							Only <span style="color:red;">1</span> file is acccepted.',
		autoProcessQueue:false,
		maxFiles: 1
	});

	myDropzone.on("processing", function(file, progress) {
		$('#upload_process').fadeIn();
		$('#nodocs').hide();
	});

	myDropzone.on("success", function(file,response) {
		$('#upload_process').fadeOut();
		$(response).appendTo($('#cat'));
		$('#dropzdoc').removeClass('dz-clickable');
		$('#dropzdoc')[0].removeEventListener('click', this.listeners[1].events.click);
	});
	
	myDropzone.on("addedfile", function(file) {
		if (this.files.length) {
			var _i, _len;
			for (_i = 0, _len = this.files.length; _i < _len; _i++) {
				if(valid_extensions.test(this.files[_i].name)==false) {
					alert('Invalid file extension.\nAllowed file extensions: pdf, doc, docx, xls, xlsx, jpg, jpeg, png, tiff, wpd');
					this.removeFile(file); 
					return;
				}

				if(this.files[_i].size > 3*1024*1024) {
					alert('File too big');
					this.removeFile(file); 
					return;
				}
			}
		}

		$('.dz-preview').css('display','none');
		$('.dz-default').css('display','block'); 
	
		if(ins_Type  > 0 && ins_Date !==''){
				this.options.autoProcessQueue = true;
				this.processQueue();         
		} else {
			alert('Insurance Type or Expiration Date are empty');
			this.removeAllFiles(file); 
		}
	});

	$('.files-list').on('click','img', function(){ 
		setTimeout(function(){ 
			if($('.files-list li img').length == 0){
				myDropzone.removeAllFiles(true);
				$('#dropzdoc').addClass('dz-clickable');
				myDropzone.setupEventListeners();  
			}
		}, 1000);
	});
					
	if($('.files-list li').children('img').length > 0){
		$('#dropzdoc').removeClass('dz-clickable');
		myDropzone.removeEventListeners();
	}

	$(document).ready(()=>{
		$("#insurance_iccmcnumber").blur(()=>{
			let mcNumber = $("#insurance_iccmcnumber").val();

			if(mcNumber != ""){
				$.ajax({
					type: "GET",
					url: "https://saferwebapi.com/v2/mcmx/snapshot/"+mcNumber,
					dataType: "JSON",
					beforeSend: function(xhr){
						xhr.setRequestHeader('x-api-key', '169ebdecadf6464f9aa24b49638877d4');
					},
					success: function(result) {
						if(result.message){

						} else {
							renderCarrier(result);
						}
					}
				});
			}
		});

		$("#us_dot").blur(()=>{
			let usDot = $("#us_dot").val();

			if(usDot != ""){
				$.ajax({
					type: "GET",
					url: "https://saferwebapi.com/v2/usdot/snapshot/"+ usDot,
					dataType: "JSON",
					beforeSend: function(xhr){
						xhr.setRequestHeader('x-api-key', '169ebdecadf6464f9aa24b49638877d4');
					},
					success: function(result) {
						if(result.message){

						} else {
							renderCarrier(result);
						}
					}
				});
			}
		});
	});

	let renderCarrier = result => {
		console.log(result)
		let exploded = [];
		exploded = result.mailing_address.split(" ");
		let fullZip = exploded[exploded.length -1];
		let state = exploded[exploded.length -3];
		let zip = fullZip.split("-")[0];
		let address = result.mailing_address.split(",")[0];

		console.log(fullZip);
		console.log(state);
		console.log(zip);
		console.log(address);

		$("#company_name").val(result.legal_name);
		$("#insurance_iccmcnumber").val(result.mc_mx_ff_numbers ? result.mc_mx_ff_numbers.split("-")[1] : "");
		$("#us_dot").val(result.usdot);
		$("#phone1").val(result.phone);
		$("#address1").val(address);
		$("#state").val(state);
		$("#zip_code").val(zip);
		$("#print_state").val(state);
		$("#print_zip_code").val(zip);
	}

	$(document).ready(()=>{
		$("#address1").keyup(()=>{
            autoComplete($("#address1").val(), 'address')
        });
	});

	function autoComplete(address, type) {

		if(address.trim() != ""){
			$.ajax({
				type: 'POST',
				url: BASE_PATH + 'application/ajax/auto_complete.php',
				dataType: 'json',
				data: {
					action: 'suggestions',
					address: address
				},
				success: function (response) {
					let result = response.result;
					let html = ``;
					let h = null;
					let functionName = null;

					if(type == 'address'){
						h = document.getElementById("suggestions-box");
						h.innerHTML = "";
						functionName = 'applyAddress';
						html += '<ul class="typeahead dropdown-menu" role="listbox" style="top: 36px; width:350px; left: 133px; display: block;">';
					}

					result.forEach( (element, index) => {
						html += `<li>
									<a class="dropdown-item" href="javascript:void(0)" onclick="${functionName}('${element.street}','${element.city}','${element.state}','${element.zip}')" role="option">
										<strong>${element.street}</strong>, ${element.city}, ${element.state} ${element.zip}
									</a>
								</li>`;
					});

					html += `</ul>`;
					h.innerHTML = html;
				}
			});
		}
	}

	function applyAddress(address, city, state, zip){
		$("#suggestions-box").html("");
		$("#address1").val(address);
		$("#city").val(city);
		$("#state").val(state);
		$("#zip_code").val(zip);
		document.getElementById("suggestions-box").innerHTML = "";
	}
</script>
