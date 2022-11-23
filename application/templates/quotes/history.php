<style type="text/css">
select#email_templates
{
    display: none;
}
button#Email
{
    display: none;
}
h3.details
{
    padding:22px 0 0;
    width:100%;
    font-size:20px;
}
</style>


    <?php include('quote_menu.php');  ?>

	<div class="col-3" style="margin-top:-55px;margin-bottom:15px;">
		<h3 class="details">Quote #<?= $this->entity->getNumber() ?> History</h3>
	</div>
	
</div>

<div class="clearfix"></div>

<table class="table table-bordered table_a_link_color">
    <thead>
        <tr>
            <th><?= $this->order->getTitle("field_name", "Field Name") ?></th>
            <th>Old Value</th>
            <th>New Value</th>
            <th><?= $this->order->getTitle("change_date", "Date") ?></th>
            <th><?= $this->order->getTitle("changed_by", "Changed By") ?></th>
        </tr>
    </thead>
	
    <tbody>
        <?php if (count($this->history) == 0) : ?>
            <tr class="grid-body">
                <td class="grid-body-left">&nbsp;</td>
                <td colspan="3" align="center">No records</td>
                <td class="grid-body-right">&nbsp;</td>
            </tr>
            <?php else : ?>
			<?php foreach ($this->history as $record) : ?>
			<tr class="grid-body">
				<td>
					<?= $record->field_name ?>
				</td>
				<td>
					<?= $record->old_value ?>
				</td>
				<td>
					<?= $record->new_value ?>
				</td>
				<td>
					<?= $record->getDate() ?>
				</td>
				<td>
					<?= $record->getChangedBy() ?>
				</td>
			</tr>
		<?php endforeach; ?>
		<?php endif; ?>
    </tbody>
</table>

<?php /* ?> <div class="col-12">
	@pager@
</div> <?php */ ?>