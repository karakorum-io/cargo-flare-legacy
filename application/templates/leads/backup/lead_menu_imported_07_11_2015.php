<script type="text/javascript">
	var anim_busy = false;
	function setStatus(entity_id, status) {
		$.ajax({
			type: "POST",
			url: "<?= SITE_IN ?>application/ajax/entities.php",
			dataType: "json",
			data: {
				action: 'setStatus',
				entity_id: entity_id,
				status: status
			},
			success: function(result) {
				if (result.success == true) {
					window.location.reload();
				} else {
					alert("Lead action failed. Try again later, please.");
				}
			},
			error: function(result) {
				alert("Lead action failed. Try again later, please.");
			}
		})
	}
	function reassign(entity_id) {
		$("#reassign_dialog").dialog({
			modal: true,
			resizable: false,
			title: "Reassign Lead",
			width: 300,
			buttons: [{
				text: "Assign",
				click: function() {
					var assign_id = $("#reassign_dialog select").val();
					$.ajax({
						type: "POST",
						url: "<?= SITE_IN ?>application/ajax/entities.php",
						dataType: "json",
						data: {
							action: 'reassign',
							entity_id: entity_id,
							assign_id: assign_id
						},
						success: function(result) {
							if (result.success == true) {
								window.location.reload();
							} else {
								$("#reassign_dialog div.error").html("<p>Can't reassign lead. Try again later, please.</p>");
								$("#reassign_dialog div.error").slideDown().delay(2000).slideUp();
							}
						},
						error: function(result) {
							$("#reassign_dialog div.error").html("<p>Can't reassign lead. Try again later, please.</p>");
							$("#reassign_dialog div.error").slideDown().delay(2000).slideUp();
						}
					})
				}
			},{
				text: "Cancel",
				click: function() {
					$(this).dialog("close");
				}
			}]
		}).dialog("open");
	}
	function convertToQuote(entity_id) {
		$.ajax({
			type: "POST",
			url: "<?= SITE_IN ?>application/ajax/entities.php",
			dataType: "json",
			data: {
				action: 'toQuote',
				entity_id: entity_id
			},
			success: function(result) {
				if (result.success == true) {
					window.location.href = '<?= SITE_IN ?>application/quotes/edit/id/'+entity_id;
				} else {
					if (result.reason != undefined) {
						alert(result.reason);
					} else {
						alert("Can't convert Lead. Try again later, please.");
					}
				}
			},
			error: function(result) {
				alert("Can't convert Lead. Try again later, please.");
			}
		});
	}
	
function convertToOrder(entity_id) {
		var entity_ids = [];
		entity_ids.push(entity_id);
			     
       $.ajax({
            type: "POST",
            url: "<?= SITE_IN ?>application/ajax/entities.php",
            dataType: "json",
            data: {
                action: "LeadtoOrderNew",
                entity_ids: entity_ids.join(',')
            },
            success: function (result) {
                if (result.success == true) {
                  window.location.href = '<?= SITE_IN ?>application/orders/edit/id/'+entity_id;
				  
                } else {
                    alert("Can't convert to Order. Try again later, please");
                }
            },
            error: function (result) {
                alert("Can't convert to Order. Try again later, please");
            }
        });
   }
	
</script>
<div id="reassign_dialog" style="display:none;">
	<div class="error" style="display:none;"></div>
	<strong>Assign to:</strong>
	<select class="form-box-combobox">
	<?php foreach($this->company_members as $member) : ?>
	<option value="<?= $member->id ?>"><?= $member->contactname ?></option>
	<?php endforeach;?>
	</select>
</div>
<div class="tab-panel-container">
	<ul class="tab-panel">
		
        <?php if($_GET['leads'] =="showimported" || $_GET['leads'] =="editimported"){?>
            <li class="tab first<?= (@$_GET['leads'] == 'showimported' )?" active":"" ?>"><span onclick="location.href = '<?= SITE_IN ?>application/leads/showimported/id/<?= $this->entity->id ?>'">Lead Detail</span></li>
        <?php }else{?>
        <li class="tab first<?= (@$_GET['leads'] == 'show' )?" active":"" ?>"><span onclick="location.href = '<?= SITE_IN ?>application/leads/show/id/<?= $this->entity->id ?>'">Lead Detail</span></li>
        <?php }?>
        <?php if($_GET['leads'] =="showimported" || $_GET['leads'] =="editimported"){
			
			?>
		    <li class="tab<?= (@$_GET['leads'] == 'editimported')?" active":"" ?>"><span onclick="location.href = '<?= SITE_IN ?>application/leads/editimported/id/<?= $this->entity->id ?>'">Edit Lead</span></li>
        
         <?php }else{?>
         <?php if (($this->entity->status != Entity::STATUS_ARCHIVED) && !$this->entity->readonly) : ?>
		<li class="tab<?= (@$_GET['leads'] == 'edit')?" active":"" ?>"><span onclick="location.href = '<?= SITE_IN ?>application/leads/edit/id/<?= $this->entity->id ?>'">Edit Lead</span></li>
         <?php endif; ?>
         <?php }?>
         
		
		<li class="tab<?= (@$_GET['leads'] == 'history')?" active":"" ?>"><span onclick="location.href = '<?= SITE_IN ?>application/leads/history/id/<?= $this->entity->id ?>'">Lead History</span></li>
		<?php if (!is_null($this->entity->email_id) && ctype_digit((string)$this->entity->email_id)) : ?>
		<li class="tab last<?= (@$_GET['leads'] == 'email')?" active":"" ?>"><span onclick="location.href = '<?= SITE_IN ?>application/leads/email/id/<?= $this->entity->id ?>'">Original E-mail</span></li>
		<?php endif; ?>
	</ul>
</div>
<div class="tab-panel-line"></div>
<?php if (($this->entity->status != Entity::STATUS_ARCHIVED) && !$this->entity->readonly) : ?>
<div style="float: right;line-height:23px;" class="lead-actions">
	<div class="actions" style="">
		<table cellspacing="5" cellpadding="5" border="0" width="100%">
			<tr>
				<td><?= functionButton('Reassign', 'reassign('.$this->entity->id.')') ?></td>
				<?php if ($this->entity->status == Entity::STATUS_ACTIVE) : ?>
				<td><?= functionButton('Convert to Quote', 'convertToQuote('.$this->entity->id.')') ?></td>
                <td><?= functionButton('Convert to Order', 'convertToOrder('.$this->entity->id.')') ?></td>
				<td><?= functionButton('On Hold', 'setStatus('.$this->entity->id.', '.Entity::STATUS_ONHOLD.')') ?></td>
				<?php elseif ($this->entity->status == Entity::STATUS_ONHOLD) : ?>
				<td><?= functionButton('Remove Hold', 'setStatus('.$this->entity->id.', '.Entity::STATUS_ACTIVE.')') ?></td>
				<?php endif; ?>
				<?php if ($this->entity->status != Entity::STATUS_ARCHIVED) : ?>
				<td><?= functionButton('Cancel', 'setStatus('.$this->entity->id.', '.Entity::STATUS_ARCHIVED.')') ?></td>
				<?php endif; ?>
			</tr>
		</table>
	</div>
</div>
<?php endif; ?>