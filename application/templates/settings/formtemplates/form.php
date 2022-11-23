<? include(TPL_PATH . "settings/menu.php"); ?>

<div style="clear:both;padding-bottom:20px;" align="left">
    <img style="vertical-align:middle;" src="<?= SITE_IN ?>images/icons/back.png" alt="Back" width="16" height="16" /> <a href="<?= getLink("formtemplates") ?>">&nbsp;Back to the list</a>
</div>

<form action="<?= getLink("formtemplates", "edit", "id", get_var("id")) ?>" method="post">
	
	<div class="kt-portlet">
	
		<div class="kt-portlet__head">
			<div class="kt-portlet__head-label">
				<?= formBoxStart(((int) get_var("id") > 0 ? "Edit template" : "Add New Template")) ?>
			</div>
		</div>

		<div class="kt-portlet__body">
			<div class="row">
				<div class="col-3">
					<div class="form-group select_opt_new_info">
						<label>Name : <?= (!$this->is_system ? "<span class=\"required\">*</span>" : "") ?></label>
						@name@
					</div>
				</div>
				
				<div class="col-3">
					<div class="form-group select_opt_new_info">
						<label>Description : <?= (!$this->is_system ? "<span class=\"required\">*</span>" : "") ?></label>
						@description@
					</div>
				</div>
				
				<div class="col-3">
					<div class="form-group select_opt_new_info">
						<label>Used for : <?= (!$this->is_system ? "<span class=\"required\">*</span>" : "") ?></label>
						@usedfor@
					</div>
				</div>
				
				<div class="col-3">
					<div class="form-group select_opt_new_info">
						<label>Attached to :</label>
						<img src="<?= SITE_IN ?>images/icons/attach.png" width="16" height="16" alt="Attachments" style="vertical-align:middle;" /> <? if (!empty($this->attachments)) { ?>
							<?= implode("; ", $this->attachments); ?>
						<?
						} else {
							echo "None";
						}
						?>
					</div>
				</div>
				
			</div>
			
			<div class="row">
				<div class="col-12">
					<div class="form-group input_wdh_100_per">
						<label>Body : </label>				
						@body@
					</div>
				</div>
			</div>
			
			<div class="text-right">
				<?= submitButtons(getLink("formtemplates"), "OK", "submit_button", "submit", $this->is_system ? getLink("formtemplates", "revert", "id", (int) get_var("id")) : "") ?>
			</div>
			
		</div>
		<?= formBoxEnd() ?>		
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
            }else{
                $("#codes_orders").hide();
                $("#codes_quotes").show();
            }
        }
        $("#usedfor").change(function(){
            $('#usedfor_txt').val($('#usedfor').val());
            showhideOQ();
        });
        showhideOQ();
    });
    //]]></script>

	<script>
		$(document).ready(()=>{
			CKEDITOR.replace( 'body', {
				allowedContent: true
			});
		});
	</script>