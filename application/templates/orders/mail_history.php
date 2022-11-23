<?php
    $shipper = $this->entity->getShipper();
?>
<?php include('order_menu.php');  ?>
<div class="kt-portlet" style="background:#fff;border:1px solid #ebedf2; ">
    <div class="hide_show">
        <div class="shipper_detail">
            <h4 style="color:#3B67A6; padding-top:10px; font-size:16px;">Email History</h4>
        </div>
    </div>
    <div class="kt-portlet__body" style="padding-left:20px;padding-right:20px;">
        <div class="row">
            <div class="col-12">
                <div class="form-group">
                    <label>
                        <strong>Shipper Details</strong>
                    </label>
                    <table style="width:40%;">
                        <thead>
                            <tr>
                                <th>Name</th><td><?= $shipper->fname ?> <?= $shipper->lname ?></td>
                            </tr>
                            <tr>
                                <th>Email</th><td><?= $shipper->email ?></td>
                            </tr>
                            <tr>
                                <th>Phone</th><td><?= formatPhone($shipper->phone1); ?></td>
                            </tr>
                            <tr>
                                <th>Company</th><td><?= $shipper->company; ?></td>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <hr/>
            <div class="col-12">
                <div class="form-group">
                    <table id="email_history" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Date</th>
                                <th>Sent By</th>
                                <th>Sent To</th>
                                <th>Sent CC</th>
                                <th>Sent BCC</th>
                                <th>Email Name</th>
                                <th>Opened</th>
                                <th>Opened At</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                foreach ($this->history as $key => $value) {
                            ?>
                            <tr>
                                <td><?php echo $value['id']?></td>
                                <td><?php echo date('m/d/Y h:i a',strtotime($value['created_at']))?></td>
                                <td>
                                    <?php 
                                        $member_name = $this->daffny->DB->selectRow("contactname", "members", "WHERE  id='".$value['sent_by']."'");
                                        print_r($member_name['contactname']);
                                    ?>
                                </td>
                                <td><?php echo $value['to_email']?></td>
                                <td><?php echo $value['cc_email']?></td>
                                <td><?php echo $value['bcc_email']?></td>
                                <td><?php echo $value['email_name']?></td>
                                <td><?php echo $value['status'] == 0 ? "Not Opened" : "Opened"?></td>
                                <td><?php echo $value['updated_at'] ? date('m/d/Y h:i a',strtotime($value['updated_at'])) : ""?></td>
                            </tr>
                            <?php
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>	
</div>
<script>
    $(document).ready(function() {
		$('#email_history').DataTable({
			"lengthChange": false,
			"paging": false,
			"bInfo": false
		});
	});
</script>