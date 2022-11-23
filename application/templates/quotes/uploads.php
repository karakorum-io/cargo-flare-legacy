<script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.11.4/ckeditor.js"></script>
<script>
	CKEDITOR.replace('mail_body_new');
</script>

<style>
h3.details
{
    padding:22px 0 0;
    width:100%;
    font-size:20px;
}
</style>


<script type="text/javascript" src="<?= SITE_IN ?>jscripts/jquery.ajaxupload.js"></script>
    
	<?php include('quote_menu.php');  ?>
	
	<div class="col-sm-4" style="margin-top:-55px;margin-bottom:15px;">
		<h3 class="details">Quote #<?= $this->entity->getNumber() ?> Documents</h3>
	</div>

</div>

<div class="modal fade" id="maildiv" tabindex="-1" role="dialog" aria-labelledby="maildiv_modal" aria-hidden="true">
	<div class="modal-dialog" role="document">
        <div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="maildiv_modal">Email message with that document attached</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<i class="fa fa-times" aria-hidden="true"></i>
				</button>
			</div>
			<div class="modal-body">
			
				<div class="row">
					<div class="col-12">
						<div class="form-group">
							@mail_to@
						</div>
					</div>  
				</div>

				<div class="row">
					<div class="col-12">
						<div class="form-group">
						  @mail_subject@
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-12">
						<div class="form-group">
							@mail_body@
						</div>
					</div>
				</div>
			 
				<div id="mail_file_name" style="font-weight: bold; color: #0052a4; "></div>
				
			</div>
			
			<div class="modal-footer">
				<button type="button" id="maildivnew_send_close"  class="btn btn-secondary" data-dismiss="modal">Close</button>
				<button type="button" id="maildivnew_send" class="btn btn-primary" onclick="maildiv_send()">Submit</button>
			</div>
			
		</div>
	</div>
</div>
	

	
	
	<div style="background:#fff;border:1px solid #ebedf2;" class="mt-3">
	
		<div class="hide_show">
			<h3 class="order_heading shipper_detail text-left" style="padding-left:15px;">Attachment</h3>
		</div>
		
		<div id="shipper_information_info_1" class="pt-3" style="padding-left:20px;padding-right:20px;">
		
			<div class="quote_doc_upload_new">
				@files_upload@
			</div>
			
			<?= formBoxStart() ?>
			<div>
				<ul class="files-list" id="cat">
					<?php if (isset($this->files) && count($this->files)) { ?>
					<? foreach ($this->files as $file) { ?>
					<li id="file-<?= $file['id'] ?>">
						<?=$file['img']?>
						<a href="<?=getLink("quotes", "getdocs", "id", $file['id'])?>"><?=$file['name_original']?>	</a> 
						(<?=$file['size_formated']?>)&nbsp;&nbsp;<a href="#" onclick="sendFile('<?=$file['id'];?>', '<?=$file['name_original']?>')">Email</a>
						&nbsp;&nbsp;<a <?=strtolower($file['type'])=='pdf'?"target=\"_blank\"":""?> href="<?=getLink("quotes", "getdocs", "id", $file['id'])?>">View</a>
						&nbsp;&nbsp;&nbsp;
						<a href="#" onclick="return deleteFile('<?php echo getLink("quotes", "delete-file"); ?>', <?php echo $file['id']; ?>);">
							<img src="<?= SITE_IN ?>images/icons/delete.png" alt="delete" style="vertical-align:middle;" width="16" height="16"/>
						</a>
					</li>
					<?php } ?>
					<?php } else { ?>
					<li id="nodocs">No documents.</li>
					<?php } ?>
				</ul>
				<div class="upload_loading_process_new" id="upload_process" style="display:none;">
					<img src="<?= SITE_IN ?>images/uploading_file.gif" alt="uploading..."/>
				</div>
			</div>
			<?= formBoxEnd() ?>
			
		</div>
		
	</div>
	
</div>
</div>
	
	
<div style="clear:both;">&nbsp;</div>
<script type="text/javascript">//<![CDATA[
    $(function(){
        new AjaxUpload('#files_upload', {
            action: '<?=getLink("quotes", "upload_file", "id", (int)$_GET['id'] ) ?>',
            name: 'file',
            onChange: function(file, extension){
                this.setData({});
            },
            onSubmit: function(file , ext){
                if (!(ext && /^(pdf|doc|docx|xls|xlsx|jpg|jpeg|png|tiff|wpd)$/.test(ext))){
                    alert('Invalid file extension.\nAllowed file extensions: pdf, doc, docx, xls, xlsx, jpg, jpeg, png, tiff, wpd');
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

    function  maildiv_send()
    {

        console.log(mail_file_id);

    $.ajax({
        url: BASE_PATH + 'application/ajax/send_document.php',
        data: {
            action: "entity",
            file_id: mail_file_id,
            mail_to: $('#mail_to').val(),
            mail_subject: $('#mail_subject').val(),
            mail_body: $('#mail_body').val()
        },
        type: 'POST',
        dataType: 'json',
        beforeSend: function () {
            if (!validateMailForm()) {
                return false;
            } else {
                console.log("Ddd")
               Processing_show();
            }
        },
        success: function (response) {
            // $("body").nimbleLoader("hide");
            if (response.success == true) {
                console.log('success');
                $("#maildiv").modal('hide');
                clearMailForm();
            }
            swal.fire(response.message);
        },
        complete: function () {
             KTApp.unblockPage();
        }
    });

    }

    function Processing_show() 
     {

        KTApp.blockPage({
        overlayColor: '#000000',
        type: 'v2',
        state: 'primary',
        message: '.'
        });

     }


    //]]></script>