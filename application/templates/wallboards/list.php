<?php
/**
 * UI created to display all the available wall boards in the web application in
 * a list
 * 
 * @author Chetu Inc.
 * @version 1.0
 */

/**
 * loading depending view files
 */
include(TPL_PATH . "wallboards/menu.php");

$accesible_users = explode(",", $_SESSION['member']['specific_user_access']);
?>

<div class="row alert alert-light alert-elevate mt-4 ">

    <div class="col-12 mt-2">
       
		<div class="img_add mb-4 text-right">
			<img style="vertical-align:middle;" src="<?= SITE_IN ?>images/icons/add.gif" alt="Add" width="16" height="16" />
			<a class="mb-2" href="<?= getLink("wallboards", "add_edit") ?>">
				Add Wallboard
			</a >
		</div>
      

		<table id="wallboards" class="table table-bordered">
			<thead>
				<tr>        
					<th><?php echo $this->order->getTitle("id", "ID") ?></th>        
					<th><?php echo $this->order->getTitle("title", "Wallboard Title") ?></th>
					<th><?php echo $this->order->getTitle("domain", "Domain") ?></th>
					<th ><?php echo $this->order->getTitle("agents", "Agents") ?></th>
					<th style="width: 150px;"><?php echo $this->order->getTitle("forward_email", "Forward Email") ?></th>
					<th style="width: 70px;" class="grid-head-left"><?php echo $this->order->getTitle("status", "Status") ?></th>
					<th >Actions</th>
				</tr>
			</thead>
			<?php

			foreach ($this->data as $i => $data) {        
				?>
				<tr class="grid-body<?php echo ($i == 0 ? " " : "") ?>" id="row-<?php echo $data['id'] ?>">
					<td align="center" class="grid-body-left">
						<?php echo $data['id'] ?>
					</td>
					<td align="left">
						<?php echo $data['title'] ?>
					</td>
					<td align="left">
						<?php
						if(isset($_SERVER['HTTPS'])){
							$url = "https://".$_SERVER['SERVER_NAME'];
						} else {
							$url = "http://".$_SERVER['SERVER_NAME'];
						}
						?>
						<a href="<?php echo SITE_PATH."../wallboards/view/hash/".$data['hash'];?>" target="_blank">
							<?php echo $url; ?>/<?php echo "wallboards/view/hash/".$data['hash'] ?>
						</a>
					</td>
					<td align="center">
						<?php echo $data['agents'] ?>
					</td>
					<td align="center">
						<?php echo $data['forward_email'] ?>
					</td>

					<?php
					$data['status'] = ($data['status'] == 1 ? "<span style=\"color:green\">Active</span>" : "<span style=\"color:black\">Inactive</span>");
					?>

					<td align="center">
						<?php echo $data['status'] ?>
					</td>            
					<td>  

						<div class="row">
							<div class="col-4">
								<a href="<?php echo getLink("wallboards", "export", $data['id']);?>">
								<img style="cursor: pointer;" src="<?php echo SITE_IN ?>images/additionals/excel.png" width="23" height="23">
							</a>
							</div>
							<div class="col-4 align-items-center d-flex">
								<?php
								echo editIcon(getLink("wallboards", "add_edit", $data['id']));
								?>
							</div>
							<div class="col-4 align-items-center d-flex">
								<a href="<?php echo getLink("wallboards", "delete", $data['id'])?>">
								<img src="<?php echo SITE_IN ?>images/icons/delete.png" title="Delete" alt="Delete" class="pointer" width="16" height="16">
								</a>
							</div>
						</div>

					</td>
				</tr>
			<?php } ?>
		</table>
	</div>
</div>


@pager@



<script type="text/javascript">
$(document).ready(function() {
$('#wallboards').DataTable({
   "lengthChange": false,
   "paging": false,
   "bInfo" : false,
   'drawCallback': function (oSettings) {
       $('#wallboards_wrapper').children('.row:first').children('.col-md-6:first').html('<div class="form-group row"><label class="col-form-label">Show: </label><select class=" records_per_page form-box-combobox form-control " style="width:100px;" onchange="setPagerLimit(this.value)"> <option value="10">10</option> <option value="25">25</option> <option value="50" selected="selected">50</option> <option value="100">100</option> <option value="200">200</option> <option value="500">500</option> </select></div>');
       $('#wallboards_wrapper').children('.row:last').children('.col-md-5').html($('.pager').clone()).addClass('text-left');
       $('#wallboards_wrapper').children('.row:last').children('.col-md-7').html($('.table_b ').html()).addClass('text-right');
       $('.pages-div-custom').remove();
       $("#wallboards_wrapper").find('.row:first').css('margin-left','5px');
       
  }
});
} );
</script>


