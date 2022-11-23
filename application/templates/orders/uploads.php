<script type="text/javascript" src="<?= SITE_IN ?>jscripts/jquery.ajaxupload.js"></script>
<script type="text/javascript" src="<?= SITE_IN ?>jscripts/dropzone.js"></script>
<link href="<?= SITE_IN ?>application/assets/css/dropzone.css" type="text/css" rel="stylesheet"/>

<style type="text/css">
.shipper_detail
{
	text-align:left;
	font-size:15px;
	color:#222;
	height:40px;
	line-height:40px;
	padding-left:15px;
	background-color:#f7f8fa;
	border-bottom:1px solid #ebedf2;
}
.shipper_detail h4
{
	font-size:15px !important;
	color:#222 !important;
	height:40px;
	line-height:40px;
}
h3.details
{
    padding: 22px 0 0;
    width: 100%;
    font-size: 20px;
}
</style>

<div>
    <?php include('order_menu.php');  ?>
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.11.4/ckeditor.js"></script>

<script>
	CKEDITOR.replace('mail_body');
</script>

<div style="margin-top:-45px;margin-bottom:15px;">
	<h3 class="details">Order #<?= $this->entity->getNumber() ?> Documents</h3>
</div>

<div class="kt-portlet__body">

	<div class="row">
		<div class="col-12 col-sm-6">
			
			<div style="background:#fff;border:1px solid #ebedf2;" class="mb-5">
			
				<div class="shipper_detail hide_show">
					<?= formBoxStart("Document(s) Upload") ?>
				</div>
				
				<div style="padding:20px;">
					<em><strong> Allowed Files: pdf, doc, docx, xls, xlsx, jpg, jpeg, png, tiff, wpd.</strong>
					</em>
					<p>Upload your document(s) by dropping your files into the box below.</p>
					<div action="#" id="dropzdoc" class="dropzone"></div>
						
					<?= formBoxEnd() ?>

					<script>
					Dropzone.autoDiscover = false;
					var myDropzone = new Dropzone("#dropzdoc",{
					url: '<?php echo getLink("orders", "upload_file", "id", (int)$_GET['id'] ); ?>',
					acceptedFiles: ".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.tiff,.wpd",
					createImageThumbnails:false
					});

					myDropzone.on("error", function(file, response) {
						alert('Invalid file extension.\nAllowed file extensions: pdf, doc, docx, xls, xlsx, jpg, jpeg, png, tiff, wpd');
					});

					myDropzone.on("processing", function(file, progress) {
						 $('#upload_process').fadeIn();
						 $('#nodocs').hide();
					});

					myDropzone.on("success", function(file,response) {
						$('#upload_process').fadeOut();
						$(response).appendTo($('#cat'));
					});


					myDropzone.on("addedfile", function(file) {
					if (this.files.length) {
						var _i, _len;
						for (_i = 0, _len = this.files.length; _i < _len - 1; _i++) // -1 to exclude current file
						{
							if(this.files[_i].name === file.name)
							{
								this.removeFile(file); 
							}
						}
					}
					$('.dz-preview').css('display','none');
					$('.dz-default').css('display','block');

					});
					</script>
				</div>
			</div>
			
		</div>
		
		<div class="col-lg-6 col-sm-12 ">
			
			<div style="background:#fff;border:1px solid #ebedf2;" class="mb-5">
				<div class="shipper_detail hide_show">
					<?= formBoxStart("Uploaded Document(s)") ?>
				</div>
				
				<div style="padding:20px;padding-bottom:0;">
					<p>Below you will find all documents that have been uploaded by users or generated by the system. You can email, view and delete documents uploaded to this order from this section.</p>

					<ul class="files-list kt-widget4" id="cat">
						<?php if (isset($this->files) && count($this->files)) { ?>
						<? foreach ($this->files as $file) { ?>
						<li id="file-<?= $file['id'] ?>" class="kt-widget4__item">
						
							<span class="kt-widget4__icon">
								<?=$file['img']?>
							</span>
							
							<a class="kt-widget4__title" href="<?=getLink("orders", "getdocs", "id", $file['id'])?>"><?=$file['name_original']?> (<?=$file['size_formated']?>)</a>
							
							<a class="kt-widget4__icon" href="#" onclick="sendFile('<?=$file['id'];?>', '<?=$file['name_original']?>')">Email</a>
							
							<a class="kt-widget4__icon" <?=strtolower($file['type'])=='pdf'?"target=\"_blank\"":""?> href="<?=getLink("orders", "getdocs	", "id", $file['id'])?>">View</a>
							
							<a class="kt-widget4__icon" href="#" onclick="return deleteFile('<?php echo getLink("orders", "delete-file"); ?>', <?php echo $file['id']; ?>);">
								<img src="<?= SITE_IN ?>images/icons/delete.png" alt="delete" style="vertical-align:middle;" width="16" height="16"/>
							</a>
						</li>
						<?php } ?>
						<?php } else { ?>
							<li id="nodocs" class="kt-widget4__item"><strong>No documents.<strong></li>
						<?php } ?>
					</ul>
					
					<div>
						<img id="upload_process" src="<?= SITE_IN ?>images/uploading_file.gif" alt="uploading..." style="display: none;" />
					</div>

					<?= formBoxEnd() ?>
				</div>
			</div>
		</div>

	</div>
</div>


<script type="text/javascript">//<![CDATA[
    $(function(){
        new AjaxUpload('#files_upload', {
            action: '<?=getLink("orders", "upload_file", "id", (int)$_GET['id'] ) ?>',
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

   

    //]]></script>
<script>
$('.files-list').on('click','img', function(){ 
    myDropzone.removeAllFiles(true);
});

    function  maildiv_send()
    {
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
</script>



<script type="text/javascript">
		 function emailSelectedOrderFormNew() {

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
                            
                        $("#maildiv").modal();
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