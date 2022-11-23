<script type="text/javascript" src="<?=SITE_IN?>jscripts/jquery.ajaxupload.js"></script>
<form action="<?=getLink("news", "edit", "id", (int)get_var("id"))?>" method="post">
                @flash_message@
                <div align="right" style="clear:both; padding-bottom:5px; padding-top:5px;">
					<img style="vertical-align:middle;" src="<?=SITE_IN?>images/icons/back.png" alt="Back" width="16" height="16" /> <a href="<?=getLink("news")?>">&nbsp;Back to the list</a>
				</div>
				<?=formBoxStart()?>
				<table cellpadding="0" cellspacing="5" border="0">
					<tr>
						<td valign="top">
			                <table cellpadding="0" cellspacing="5" border="0" style="padding-bottom: 5px;">
			                	<tr>
			                		<td colspan="2">@is_featured@&nbsp;&nbsp;&nbsp;&nbsp;@is_hidden@</td>
			                	</tr>
			                    <tr>
			                        <td>@title@</td>
			                    </tr>
			                    <tr>
			                        <td>@news_date@</td>
			                    </tr>
			                    <tr>
			                        <td>@image@</td>
			                    </tr>
							</table>
		                </td>
		                <td valign="top">
							<div id="cat">
								@image_file@
					        </div>
					        <div style="height: 20px;"><img id="upload_process" src="<?=SITE_IN?>images/uploading_file.gif" alt="uploading..." style="display: none;" /></div>
	    	            </td>
    	            </tr>
                </table>
                <?=formBoxEnd()?>
                <br />
				<?=formBoxStart("Content")?>
                @content@
                <?=formBoxEnd()?>
                <br />
                <?=submitButtons(getLink("news"))?>
</form>
<script type="text/javascript">//<![CDATA[
$(function(){
    $('#news_date').datepicker(datepickerSettings);
});
//]]></script>
<script type="text/javascript">//<![CDATA[
$(function(){
	new AjaxUpload('#image', {
        action: '<?=getLink("news", "upload-file")?>',
        name: 'image',
        onChange: function(file, extension){
            this.setData({'news_id': <?php echo (int)get_var("id"); ?>});
        },
        onSubmit: function(file , ext){

            if (!(ext && /^(jpg|gif|png|jpeg)$/.test(ext))){
                alert('Allowed: *.jpg, gif, png, jpeg');
                return false;
            }

            $('#upload_process').fadeIn();

        },
        onComplete : function(file, response){
            $('#upload_process').fadeOut();
            if (response.indexOf('ERROR:') != -1) {
                alert('Can not upload file.\n'+response);
                return false;
            }
            $('#cat').html(response);
        }
    });
});
//]]></script>