<script type="text/javascript">
	function setPagerLimit(val) {
		$.ajax({
			type: "POST",
			url: "<?= SITE_IN ?>application/ajax/member.php?action=setLimit",
			dataType: "json",
			data: "limit="+val,
			success: function(result) {
				if (result.success == true) {
					window.location.reload();
				}
			}
		});
	}
</script>
<table style="width: 100%;" cellpadding="0" cellspacing="0" border="0" class="grid-pager">
    <tr>
		<td class="grid-pager-left" style="width: 100px;">
			Show:
			<select class="records_per_page form-box-combobox" style="width:60px;" onchange="setPagerLimit(this.value)">
                <option value="10"<?= ($_SESSION['per_page'] == 10)?' selected="selected"':'' ?>>10</option>
				<option value="25"<?= ($_SESSION['per_page'] == 25)?' selected="selected"':'' ?>>25</option>
				<option value="50"<?= ($_SESSION['per_page'] == 50)?' selected="selected"':'' ?>>50</option>
				<option value="100"<?= ($_SESSION['per_page'] == 100)?' selected="selected"':'' ?>>100</option>
				<option value="200"<?= ($_SESSION['per_page'] == 200)?' selected="selected"':'' ?>>200</option>
				<option value="500"<?= ($_SESSION['per_page'] == 500)?' selected="selected"':'' ?>>500</option>
			</select>
		</td>
        <td>@navigation@</td>
        <td class="grid-pager-right">Page <strong>@current_page@</strong> of @pages_total@ (<span>@records_total@</span> Records)</td>
    </tr>
</table>