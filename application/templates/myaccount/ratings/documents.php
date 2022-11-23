	<? include(TPL_PATH."myaccount/menu.php");?>
	<div align="right" style="clear:both; padding-bottom:5px; padding-top:5px;">
		<img style="vertical-align:middle;" src="<?=SITE_IN?>images/icons/back.png" alt="Back" width="16" height="16" /> <a href="<?=getLink("ratings", "company", "id", (int)get_var("id"))?>">&nbsp;Back to the company profile</a>
	</div>
	<h3>@companyname@ - Document Packet</h3>
	<?=formBoxStart()?>
	<table width="100%" cellpadding="0" cellspacing="5" border="0">
		<tr>
			<td>
				<ul class="files-list" id="cat">
					<?php if (isset($this->files) && count($this->files)>0){ ?>
						<? foreach ($this->files as $file) { ?>
							<li id="file-<?=$file['id']?>">
								<?=$file['img']?>
								<a target="_blank" href="<?=getLink("ratings", "getdocs", "id", $file['id'] );?>"><?=$file['name_original']?></a>
								(<?=$file['size_formated']?>)
							</li>
						<?php } ?>
					<?php }else{ ?>
						<li>No documents.</li>
					<?php } ?>
				</ul>
			</td>
		</tr>
	</table>
<?=formBoxEnd()?>
<br />
<?=backButton(getLink("ratings", "company", "id", (int)get_var("id")))?>