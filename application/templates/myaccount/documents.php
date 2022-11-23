<? include(TPL_PATH."myaccount/menu.php");?>

<script type="text/javascript" src="<?=SITE_IN?>jscripts/jquery.ajaxupload.js"></script>

<div style="clear:both; padding-bottom:20px;" align="left">
	<img style="vertical-align:middle;" src="<?=SITE_IN?>images/icons/back.png" alt="Back" width="16" height="16" /> <a href="<?=getLink("companyprofile")?>">&nbsp;Company profile</a>
</div>

<div class="row">
	
	<div class="col-4">		
	
		<form action="<?=getLink("documents")?>" method="post">
			<div class="kt-portlet">
				
				<div class="kt-portlet__head">
					<div class="kt-portlet__head-label">
						<?=formBoxStart("My Document Packet");?>
					</div>
				</div>
				
				<div class="kt-portlet__body">
					<div class="form-group">
						<label>My Current Document Packet</label>
						<div style="border:#eee 1px solid; padding:10px; background-color:#e1e1e1">
							<ul class="files-list" id="cat">
								<?php if (isset($this->files)){ ?>
									<? foreach ($this->files as $file) { ?>
									<li id="file-<?=$file['id']?>">
										<?=$file['img']?>
										<?=$file['name_original']?>
										<?=$file['type']?>
										(<?=$file['size_formated']?>)
										<?=colorRate($file['status'])?>
										<a href="#" onclick="return deleteFile('<?php echo getLink("documents", "delete-file"); ?>', <?php echo $file['id']; ?>);"><img src="<?=SITE_IN?>images/icons/delete.png" alt="delete" style="vertical-align:middle;" width="16" height="16" /></a>
									</li>
									<?php } ?>
								<?php }else{ ?>
									<li id="nodocs">No documents.</li>
								<?php } ?>
							</ul>
						</div>
						
						<div class="form-group">
							@files_upload@
						</div>
						
					</div>
				</div>
				
				<?=formBoxEnd();?>
			</div>
			
		</form>
		
	</div>
	
	<div class="col-8">
		<div class="kt-portlet">
			<div class="kt-portlet__body">
				<?=formBoxStart();?>
					<h4>What is My Company's Document Packet?</h4>
					<ul>
						<li>U.S. DOT Certification</li>
						<li>Insurance and/or Bond Certificate</li>
						<li>A Completed W-9 Form</li>
						<li>Other Licenses (if any)</li>
					</ul>
					<br/>
					<br/>
					<br/>
					<h4>Who Can See My Document Packet?</h4>
					<p>Your document packet is accessible only when you make it either temporarily or permanently available for viewing.</p>
					<br/>
					<br/>
					<br/>
					<h4>How Does It Work?</h4>
					<ul>
						<li>You upload Document Packet.</li>
						<li>We check your Documents.</li>
						<li>You give access to your Packet through this page.</li>
					</ul>
				<?=formBoxEnd();?>
			</div>
		</div>
	</div>
</div>

<div style="float:right; width:300px;">
	
</div>
<div style="clear:both;">&nbsp;</div>

<script type="text/javascript">//<![CDATA[
$(function(){
	new AjaxUpload('#files_upload', {
        	action: '<?=getLink("documents", "upload-file")?>',
	        name: 'file',
        	onChange: function(file, extension){
            	this.setData({});
	        },
        	onSubmit: function(file , ext){
            	if (!(ext && /^(pdf|doc|docx)$/.test(ext))){
	                alert('Invalid file extension.\nAllowed file extensions: *.pdf, *.doc, *.docx');
                	return false;
            	}
            	$('#upload_process').fadeIn();
            	$('#nodocs').hide();
	        },
        	onComplete : function(file, response){
	            $('#upload_process').fadeOut();
            	if (response.indexOf('ERROR:') != -1) {
                	alert('Cant\' upload file.\n'+response);
                	return false;
            	}
	            $(response).appendTo($('#cat'));
        	}
    	});
});
//]]></script>