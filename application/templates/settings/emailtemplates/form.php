<? include(TPL_PATH . "settings/menu.php"); ?>


<div align="left" style="clear:both; padding-bottom:20px;">
    <img style="vertical-align:middle;" src="<?= SITE_IN ?>images/icons/back.png" alt="Back" width="16" height="16" /> <a href="<?= getLink("emailtemplates") ?>">&nbsp;Back to the list</a>
</div>


<form action="<?= getLink("emailtemplates", "edit", "id", get_var("id")) ?>" method="post">
	
	<div class="kt-portlet">
	
		<div class="kt-portlet__head">
			<div class="kt-portlet__head-label">
				<?= formBoxStart(((int) get_var("id") > 0 ? "Edit template" : "Add New Template")) ?>
			</div>
		</div>
		
		<div class="kt-portlet__body">
			<div class="row">
			
				<div class="col-4">
					<div class="form-group select_opt_new_info">
						<label>Name : <?= (!$this->is_system ? "<span class=\"required\">*</span>" : "") ?></label>
						@name@
					</div>
				</div>
				
				<? if (!$this->is_system) { ?>
				<div class="col-4">
				<? } else{ ?>
				<div class="col-8">
				<? } ?>
					<div class="form-group select_opt_new_info">
						<label>Description :</label>
						@description@
					</div>
				</div>
				
				<div class="col-4">
					<? if (!$this->is_system) { ?>
					<div class="form-group ">
						<label>Used for :</label>
						<br/>
						@usedfor@ &nbsp;&nbsp;&nbsp;&nbsp;@is_followup@
					</div>
					<? } ?>
				</div>
				
			</div>
			
			<div class="row">
				
				<div class="col-6">
					<div class="form-group">					
						@to_address@
					</div>
				</div>
				
				<div class="col-6">
					<div class="form-group">					
						@from_address@
					</div>
				</div>
				
				<div class="col-6">
					<div class="form-group">					
						@from_name@
					</div>
				</div>
				
				<div class="col-6">
					<div class="form-group">					
						@subject@
					</div>
				</div>
			
			</div>
			
			<div class="row">
				<div class="col-2">
				
					<div class="form-group" id="attachStyle">
						<label>Send attachments as :</label><br/>
						<input id="PDF" name="attach_type" value="1" <?= $this->attach_typePDF ?> type="radio"/>
						<label for="PDF" style="margin-right: 2px;">PDF</label>
						<input id="HTML" name="attach_type" value="0"  <?= $this->attach_typeHTML ?> type="radio"/>					
						<label for="HTML">HTML</label>
					</div>
										
				</div>
				
				<div class="col-10">
					<div class="form-group" id="attachStyle">
						<label>Attachments : <img src="<?= SITE_IN ?>images/icons/attach.png" width="16" height="16" alt="Attachments" style="vertical-align:middle;" /></label>
						
						<? if (!empty($this->daffny->tpl->attachments)) { ?>
						<div class="attachments_list_new_info">
							<ul class="row">
								<? foreach ($this->attachments as $key => $f) { ?>
								<?php if (is_array($f)) { ?>
									<li class="col-3">
										<input class="attid_<?=$f['usedfor']?>" type="checkbox" id="attachments<?= $f['id'] ?>" name="attachments[<?= $f['id'] ?>]" value="<?= $f['id'] ?>" <?= $f['ch'] ?> />
										<label for="attachments<?= $f['id'] ?>"><?= $f['name'] ?></label>
									</li>
								<?php } else { echo $f; } ?>
								<? } ?>
							</ul>
						</div>
						<?
						} else {
							echo "No attachments.";
						}
						?>
					</div>
				</div>
				
			</div>
			
			<div class="row">
				<div class="col-2">
					<div class="form-group">
						<label>Send Email Using :</label><br/>
						@send_type@
					</div>					
				</div>
				
				<div class="col-10">
					<div class="form-group">
						@bcc_addresses@
					</div>
				</div>
			</div>
			
			<div class="row m_btm_10">
				<div class="col-12 m_btm_10">
					<div id="body_text_tr" style="display:none;" class="input_wdh_100_per m_btm_10">
						@body_text@
					</div>
					<div id="body_html_tr" style="display:none;" class="input_wdh_100_per m_btm_10">
						Body (HTML) @body_html@
					</div>
				</div>
			</div>
			
			<?= formBoxEnd() ?>
			
			<div class="form-group">
				<?= submitButtons(getLink("emailtemplates"), "OK", "submit_button", "submit", $this->is_system ? getLink("emailtemplates", "revert", "id", (int) get_var("id")) : "") ?>
			</div>

		</div>
		
	</div>
</form>


<input type="hidden" id="usedfor_txt" name="usedfor_txt" value="@usedfor_txt@" />


<div class="kt-portlet">
	
	<div class="kt-portlet__head">
		<div class="kt-portlet__head-label">
			<h4 style="color:#3B67A6">Codes</h4>
		</div>
	</div>
	
	<div class="kt-portlet__body">
		<div class="kt-section__info m_btm_10" style="padding-bottom:10px;">Use the following codes in your form to do further customization. They will be replaced with the order-specific information when the email is sent.</div>
		<?php include (TPL_PATH . "settings/emailtemplates/codes.php"); ?>
	</div>
	
</div>

<script type="text/javascript">//<![CDATA[
    $(function(){
        function showhideOQ(){
            if ($('#usedfor_txt').val() == "orders"){
                $("#codes_quotes").hide();
                $("#codes_orders").show();
                $(".att_quotes").hide();
                $(".att_orders").show();
                
                $(".attid_quotes").removeAttr("checked");
                
            }else{
                $("#codes_orders").hide();
                $("#codes_quotes").show();
                $(".att_orders").hide();
                $(".att_quotes").show();
                
                $(".attid_orders").removeAttr("checked");
            }
        }
        $("#usedfor").change(function(){
            $('#usedfor_txt').val($('#usedfor').val());
            showhideOQ();
        });
        showhideOQ();


        function showhideTH(){
            if ($('#send_type_0').is(':checked')){
                $("#body_html_tr").hide();
                $("#body_text_tr").show();
            }else{
                $("#body_html_tr").show();
                $("#body_text_tr").hide();
            }
        }
        $('#send_type_0').click(function(){
            showhideTH();
        });
        $('#send_type_1').click(function(){
            showhideTH();
        });
        showhideTH();
    });
    //]]></script>
<!-- 
<script>
	$(document).ready(()=>{
		setTimeout(() => {
			$(".ckeditor").ckeditor();
		}, 500);
	});
</script> -->