<br/>
<div style="margin: 20px auto;width:430px">
	<?=formBoxStart()?>
	<form method="post" enctype="multipart/form-data">
		<table style="padding: 0 10px;width:400px;">
			<tr>
				<td colspan="2">
					<span class="green" style="font-size: 1.2em;font-weight: bold">Import Trucks</span>
					<div style="float:right">
						<a href="<?php echo SITE_IN ?>data/trucks_import.xlsx">Download Sample</a>
					</div>
				</td>
			</tr>
			<tr>
				<td colspan="2" style="text-align: center">
					<span class="small">Allowed formats: XLS, XLSX, CSV (double quoted enclosure)</span><br/><br/>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<input type="file" name="import" id="import" accept="application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,text/csv"/>
					<div class="form-box-buttons" style="text-align: right;float:right;">
					<span id="submit_button-submit-btn">
						<input type="submit" id="submit_button" value="Import" onclick="return confirm('Are you sure you want to upload data to the system?');">
					</span>
					</div>
				</td>
			</tr>
			<tr>
				<td colspan="2"><br/></td>
			</tr>
			<?php if (count($_FILES)) { ?>
				<tr>
					<br/>
					<td colspan="2">
						<strong>Import results:</strong><br/>
						Success: @success@<br/>
						Failed: @failed@<br/>
					</td>
				</tr>
			<?php } ?>
		</table>
	</form>
	<div class="clear"><br/></div>
	<div class="attention-box">
		<span style="color: #f00">ATTENTION:</span> Please ensure the data you are uploading is correct before importing into your database.
		Please Follow the template provided in the "Download Sample" link.
	</div>
	<?=formBoxEnd()?>
</div>