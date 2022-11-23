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
<style type="text/css">
	
	.col-6.table.table-bordered.table_b {
		font-size: 14px;
	}
</style>


<div class="row pages_div pages-div-custom" >
	<div class="col-6 "><!--  table table-bordered  -->
		<label>Show: </label>
		<select class=" records_per_page form-box-combobox form-control " style="width:100px;" onchange="setPagerLimit(this.value)">
			<option value="10"<?= ($_SESSION['per_page'] == 10)?' selected="selected"':'' ?>>10</option>
			<option value="25"<?= ($_SESSION['per_page'] == 25)?' selected="selected"':'' ?>>25</option>
			<option value="50"<?= ($_SESSION['per_page'] == 50)?' selected="selected"':'' ?>>50</option>
			<option value="100"<?= ($_SESSION['per_page'] == 100)?' selected="selected"':'' ?>>100</option>
			<option value="200"<?= ($_SESSION['per_page'] == 200)?' selected="selected"':'' ?>>200</option>
			<option value="500"<?= ($_SESSION['per_page'] == 500)?' selected="selected"':'' ?>>500</option>
		</select>
	</div>
	<div class="col-6 table_b "><!--  table table-bordered  -->
		Page @current_page@ of @pages_total@ (<span>@records_total@</span> Records)
	</div>
	<div class="col-6 page_ "><!--  table table-bordered  -->
		@navigation@
	</div>
	

</div>