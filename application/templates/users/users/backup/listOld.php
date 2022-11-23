<? include(TPL_PATH."users/menu.php"); ?>
<table width="100%" cellpadding="0" cellspacing="0" border="0" class="grid">
    <tr class="grid-head">
        <!--td class="grid-head-left"><?=$this->order->getTitle("username", "Username")?></td-->
        <td><?=$this->order->getTitle("contactname", "Name")?></td>
        <!--td><?=$this->order->getTitle("email", "Email")?></td-->
        <td><?=$this->order->getTitle("phone", "Phone")?></td>
        <td><?=$this->order->getTitle("lead_multiple", "Lead Multiple")?></td>
        <td style="width: 150px;"><?=$this->order->getTitle("last_login", "Last Login")?></td>
        <td style="width: 150px;"><?=$this->order->getTitle("reg_date", "Reg. date")?></td>
        <td style="width: 70px;" class="grid-head-left"><?=$this->order->getTitle("status", "Status")?></td>
        <td class="grid-head-right" colspan="4">Actions</td>
    </tr>
    <? 	$countOnline 	= 0;
		$id				= array();
		
		foreach ($this->data as $i => $data) { 
			if ( ($data['id'] != $_SESSION['member_id'])){
				$id['userid'][] = $data['id'];
				if(($data['online_status']=='1'))
					$countOnline++;
			}
			?>
			<tr class="grid-body<?=($i == 0 ? " first-row" : "")?>" id="row-<?=$data['id']?>">
				<td  class="grid-body-left"><?=$data['contactname']?></td>
				
				<td align="center"><?=$data['phone']?></td>
				<td align="center"><?=$data['lead_multiple']?></td>
				<td align="center"><?=$data['last_login']?></td>
				<td align="center"><?=$data['reg_date_show']?></td>
				<? $data['status'] = ($data['status']=='Active'?"<span style=\"color:green\">Active</span>":"<span style=\"color:black\">Inactive</span>"); ?>
				<td align="center"><?=$data['status']?></td>
				<td style="width: 16px;">
					<?php if ($data['id'] != $_SESSION['member_id']) {
					$userid 	= $data['id'];
					if($data['online_status']=='1')
						$userstatus	= 'online';
					else 
						$userstatus	= 'offline';
					$username 	= str_replace(".","-",(str_replace(" ","_",$data['contactname'])));
					?>
					<img src="<?php echo SITE_IN ?>images/icons/chat_16.png" title="Chat" alt="Chat" class="pointer" onclick="chatWith(<?php echo $userid; ?>,'<?php echo $username; ?>','<?php echo $userstatus; ?>');">
					<?php } ?>
				</td>
				<td style="width: 16px;"><?=infoIcon(getLink("users", "show", "id", $data['id']), "Details")?></td>
				<td style="width: 16px;"><?=editIcon(getLink("users", "edit", "id", $data['id']))?></td>
				<td style="width: 16px;" class="grid-body-right">
					<?php if ($data['id'] != $_SESSION['member_id']) { ?>
					<img src="/images/icons/delete.png" title="Delete" alt="Delete" class="pointer" onclick="return deleteUser('<?=$data['id']?>', 'row-<?=$data['id']?>');" width="16" height="16">
					<?php } ?>
				</td>
			</tr>
    <?  }	?>
</table>
@pager@
<div id="delete-user-dialog">
	<p>You must re-assign this user’s Leads, Quotes and Orders first. Please select user(s).</p>
	<table class="delete-user-grid">
		<tr>
			<td align="right"><label for="delete-leads-assign">Leads: </label></td>
			<td><select id="delete-leads-assign"></select></td>
		</tr>
		<tr>
			<td align="right"><label for="delete-quotes-assign">Quotes: </label></td>
			<td><select id="delete-quotes-assign"></select></td>
		</tr>
		<tr>
			<td align="right"><label for="delete-orders-assign">Orders: </label></td>
			<td><select id="delete-orders-assign"></select></td>
		</tr>
	</table>
</div>

<script>
	var userId, userRow;
	$(document).ready(function() {
		/* getuserdetails(<?php echo $countOnline; ?>,<?php echo JSON_encode($id); ?>);
		setTimeout(function() {
			offlinePulse(<?php echo JSON_encode($id); ?>);
		}, 5000); */
		
		$('#delete-user-dialog').dialog({
			title: 'Delete User',
			modal: true,
			autoOpen: false,
			resizable: false,
			draggable: true,
			width: 250,
			buttons: [{
				text: 'Cancel',
				click: function() {
					$(this).dialog('close');
				}
			},{
				text: 'OK',
				click: function() {
					$.ajax({
						url: '<?php echo SITE_PATH ?>users/delete',
						type: 'POST',
						dataType: 'json',
						data: {
							id: userId,
							leads_id: $('#delete-leads-assign').val(),
							quotes_id: $('#delete-quotes-assign').val(),
							orders_id: $('#delete-orders-assign').val()
						},
						success: function(res) {
							$('#delete-user-dialog').dialog('close');
							if (!res.success) {
								alert('Failed to delete user. Try again later');
								return;
							}
							afterDelete($('#'+userRow));
						},
						error: function() {
							alert('Failed to delete user. Try again later');
						}
					});
				}
			}]
		});
	});
	function deleteUser(id, row) {
		userId = id;
		userRow = row;
		$.ajax({
			type: 'POST',
			url: '<?php echo SITE_PATH ?>users/prepareDelete',
			data: {
				id: id
			},
			dataType: 'json',
			success: function(res) {
				if (!res.success) {
					alert('Failed to delete user. Try again later');
					return;
				}
				var options = '';
				for (i in res.members) {
					options += '<option value="'+res.members[i].id+'">'+res.members[i].name+'</option>';
				}
				$('#delete-leads-assign').html(options);
				$('#delete-quotes-assign').html(options);
				$('#delete-orders-assign').html(options);
				$('#delete-user-dialog').dialog('open');
			},
			error: function() {
				alert('Failed to delete user. Try again later');
			}
		});
	}
		
</script>