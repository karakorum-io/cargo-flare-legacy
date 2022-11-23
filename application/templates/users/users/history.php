<? include(TPL_PATH."users/menu.php"); ?>
<div class="row">
    <div class="alert alert-light alert-elevate mt-4 " style="width: 100%">
        <div class="col-12">
            <div class="row mt-3 mb-3">
				<table id="users" class="table table-bordered"></table>
				<table class="table table-bordered">
					<thead>
						<tr>
							<th><?=$this->order->getTitle("update_date", "Date")?></th>
							<th><?=$this->order->getTitle("member_id", "Username")?></th>
							<th><?=$this->order->getTitle("action", "Action")?></th>
							<th>Active Users</th>
							<th>Paid Users</th>
							<th><?=$this->order->getTitle("changed_by", "Changed By")?></th>
						</tr>
					</thead>
					<tbody>
						<? if (count($this->data)>0){?>
							<? foreach ($this->data as $i => $data) { ?>
							<tr id="row-<?=$data['id']?>">
								<td><?=$data["update_date"]?></td>
								<td><?=htmlspecialchars($data["username"])?></td>
								<td><?=$data["action"]?></td>
								<td align="center"><?=$data["active_users"]?></td>
								<td align="center"><?=$data["paid_users"]?></td>
								<td align="center" class="grid-body-right"><?=htmlspecialchars($data["changed_by"])?></td>
							</tr>
							<? } ?>
						<? } else { ?>
							<tr id="row-1">
								<td colspan="5" class="grid-body-left">Records not found.</td>
								<td class="grid-body-right">&nbsp;</td>
							</tr>
						<? } ?>
					</tbody>
				</table>
				@pager@
			</div>
		</div>
	</div>
</div>