<script type="text/javascript" src="<?=SITE_IN?>jscripts/jquery.ajaxupload.js"></script>
<div style="padding-top:10px;">
<div style="clear: both"></div>
<h3><?= (isset($_GET['id']))?'Edit Truck':'Add a New Truck' ?></h3>
<form method="post" action="<?= SITE_IN ?>application/trucks/edit">
	<?= (isset($_GET['id']))?'<input type="hidden" name="id" value="'.$_GET['id'].'"/>':'' ?>
	<br/><br/>
	<div class="row">
		<div class="col-12 col-sm-4">
			<h5>Truck Information</h5>
			<div class="col-12 col-sm-12">
				@name@
				(Used internally by your company)
			</div>
			<div class="col-12 col-sm-12">
				@trailer@
			</div>
			<div class="col-12 col-sm-12">
				@inops@
			</div>
			<div class="col-12 col-sm-12">
				@phone@
				(Phone for companies to call)
			</div>
		</div>
		<div class="col-12 col-sm-4">
			<h5>Insurance and DOT documents</h5>
			<div class="col-12 col-sm-12">
				<div style="border:#eee 1px solid; padding:10px; background-color:#fff">
					<ul class="files-list" id="cat">
						<?php if (isset($this->files) && count($this->files) > 0) { ?>
							<?php foreach ($this->files as $file) { ?>
								<li id="file-<?=$file['id']?>">
									<?=$file['img']?>
									<?=$file['name_original']?>
									(<?=$file['size_formated']?>)
									<a href="#" onclick="return deleteFile('<?= getLink("trucks", "delete-file") ?>', <?= $file['id'] ?>);"><img src="<?=SITE_IN?>images/icons/delete.png" alt="delete" style="vertical-align:middle;" width="16" height="16" /></a>
								</li>
							<?php } ?>
						<?php } else { ?>
							<li id="nodocs">No documents.</li>
						<?php } ?>
					</ul>
					<div style="height: 20px;"><img id="upload_process" src="<?=SITE_IN?>images/uploading_file.gif" alt="uploading..." style="display: none;" /></div>
					<br />
				</div>
			</div>
			<div class="col-12 col-sm-12">
				@files_upload@
			</div>
			<div class="col-12 col-sm-12 text-right">
				<br/>
				<br/>
				<?=submitButtons(getLink("trucks"), "Save")?>
			</div>
		</div>
	</div>
</form>

<script type="text/javascript">//<![CDATA[
$(function(){
	new AjaxUpload('#files_upload', {
		action: '<?=getLink("trucks", "upload-file")?>',
		name: 'file',
		onChange: function(file, extension){
			this.setData({track_id:<?=(int)get_var("id")?>});
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