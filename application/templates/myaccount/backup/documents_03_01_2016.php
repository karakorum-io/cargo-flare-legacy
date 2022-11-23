<? include(TPL_PATH."myaccount/menu.php");?>
<script type="text/javascript" src="<?=SITE_IN?>jscripts/jquery.ajaxupload.js"></script>
<div align="right" style="clear:both; padding-bottom:5px; padding-top:5px;">
	<img style="vertical-align:middle;" src="<?=SITE_IN?>images/icons/back.png" alt="Back" width="16" height="16" /> <a href="<?=getLink("companyprofile")?>">&nbsp;Company profile</a>
</div>
<form action="<?=getLink("documents")?>" method="post">
	<div style="float:left; width:655px;">
		<?=formBoxStart("My Document Packet");?>
			@is_allowed@<br />
			<br />
			<?php echo submitButtons("", "Update"); ?>
			<br />
			<strong>My Current Document Packet</strong>
			<br />
			<table cellpadding="0" cellspacing="10" border="0">
				<tr>
					<td colspan="2">
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
						<div style="height: 20px;"><img id="upload_process" src="<?=SITE_IN?>images/uploading_file.gif" alt="uploading..." style="display: none;" /></div>
					</td>
				</tr>
				<tr>
					<td>@files_upload@</td>
				</tr>
			</table>
		<?=formBoxEnd();?>
	</div>
</form>
<div style="float:right; width:300px;">
	<?=formBoxStart();?>
		<h3>What is My Company's Document Packet?</h3>
		&bull; U.S. DOT Certification<br />
		&bull; Insurance and/or Bond Certificate<br />
		&bull; A Completed W-9 Form<br />
		&bull; Other Licenses (if any)<br />
		<br />
		<h3>Who Can See My Document Packet?</h3>
		Your document packet is accessible only when you make it either temporarily or permanently available for viewing.<br />
	    <br />
		<h3>How Does It Work?</h3>
		You upload Document Packet.<br />
		We check your Documents.<br />
		You give access to your Packet through this page.
	<?=formBoxEnd();?>
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