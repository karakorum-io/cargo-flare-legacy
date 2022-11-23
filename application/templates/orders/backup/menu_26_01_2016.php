<div class="tab-panel-container">
	<ul class="tab-panel">
        <?php /*<li class="tab first<?= (@$_GET['orders'] == 'all')?" active":"" ?>"onclick="location.href = '<?= SITE_IN ?>application/orders/all'">All</li>*/ ?>
		<li class="tab first<?= (@$_GET['orders'] == '')?" active":"" ?>"onclick="location.href = '<?= SITE_IN ?>application/orders/'">My Orders<span>(@active_count@)</span></li>
		<li class="tab<?= (@$_GET['orders'] == 'posted')?" active":"" ?>"onclick="location.href = '<?= SITE_IN ?>application/orders/posted'">Posted to FB<span>(@posted_count@)</span></li>
		<li class="tab<?= (@$_GET['orders'] == 'notsigned')?" active":"" ?>"onclick="location.href = '<?= SITE_IN ?>application/orders/notsigned'">Not Signed<span>(@notsigned_count@)</span></li>
		<li class="tab<?= (@$_GET['orders'] == 'dispatched')?" active":"" ?>"onclick="location.href = '<?= SITE_IN ?>application/orders/dispatched'">Dispatched<span>(@dispatched_count@)</span></li>
		<li class="tab<?= (@$_GET['orders'] == 'pickedup')?" active":"" ?>"onclick="location.href = '<?= SITE_IN ?>application/orders/pickedup'">Picked Up<span>(@pickedup_count@)</span></li>
        <li class="tab<?= (@$_GET['orders'] == 'issues' || @$_GET['orders'] == 'searchIssue')?" active":"" ?>"onclick="location.href = '<?= SITE_IN ?>application/orders/issues'">Issues<span>(@issues_count@)</span></li>
		
		<li class="tab<?= (@$_GET['orders'] == 'delivered')?" active":"" ?>"onclick="location.href = '<?= SITE_IN ?>application/orders/delivered'">Delivered<span>(@delivered_count@)</span></li>
		<li class="tab<?= (@$_GET['orders'] == 'onhold')?" active":"" ?>" onclick="location.href = '<?= SITE_IN ?>application/orders/onhold'">Hold<span>(@onhold_count@)</span></li>
		<li class="last tab<?= (@$_GET['orders'] == 'archived')?" active":"" ?>" onclick="location.href = '<?= SITE_IN ?>application/orders/archived'">Cancelled<span>(@archived_count@)</span></li>
		<?php if (isset($_GET['search_string'])) : ?>
		<li class="last tab active">Search Results<span>(@search_count@)</span></li>
		<?php endif; ?>
	</ul>
	<div style="float:right;">
		<table cellspacing="5" cellpadding="0" border="0">
			<tr>
				<?php $entity_ids = array(); foreach($this->entities as $entity) { $entity_ids[] = $entity->id; }?>
				<td><?=functionButton('Print', 'printOrders(window.open(\'\', \'orders\', \'height=400,width=600\'), \''.implode(",",$entity_ids).'\')');?></td>
			</tr>
		</table>
	</div>
</div>
<div class="tab-panel-line"></div>