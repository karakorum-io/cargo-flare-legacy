<style type="">
	li.nav-item.active {
		background: #374afb;
	}

	li.btn_right {
		float: right !important;
		float: left;
		padding-left: 129px;
	}

	.nav-tabs {
		margin: 0 0 0px 0;
	}

	.kt-portlet {
		margin-bottom: 0px !important;
		background: no-repeat;
		box-shadow: none;
	}

	.kt-nav {
		position: relative;
	}

	.nav-tabs .nav-item {
		margin-bottom: -3px;
	}
</style>


<?php
	if (isset($_GET['search_string'])) {
		$ruri = rawurldecode($_SERVER['REQUEST_URI']);
		$arrStr1 = explode("mtype/", $ruri);

		if ($arrStr1[0] != '') {
			$ruri = $arrStr1[0];
		}

		if (!isset($_GET['mtype'])) {
			$ruri .= "/";
		}

		$searchUrl = $ruri;
?>

<!-- today-bar -->
<div class="row">
	<div class="col-12">
		<ul class="nav nav-tabs nav-tabs-line nav-tabs-bold nav-tabs-line-3x nav-tabs-line-success" role="tablist" style="margin-bottom:0;">
			
			<li class="nav-item ">
				<a class="nav-link <?=(@$_GET['etype'] == 1) ? " active" : ""?>" href="<?=str_replace("/orders/", "/leads/", $ruri)?>mtype/<?=Entity::STATUS_ACTIVE?>/etype/1/tab/1'">Leads(@imported_lead_count@)</a>
			</li>
			
			<li class="nav-item ">
				<a class="nav-link <?= (@$_GET['etype'] == 4 || ($_GET['lead_search_type'] == Entity::TYPE_CLEAD && !$_GET['etype']))?" active":"" ?>" href="<?= str_replace("/orders/","/leads/",$ruri) ?>/mtype/<?= Entity::STATUS_CACTIVE ?>/etype/4/tab/1">Created Leads(@created_lead_count@)</a>
			</li>
			
			<li class="nav-item ">
				<a class="nav-link <?=(@$_GET['etype'] == 4) ? " active" : ""?>" href="<?=str_replace("/orders/", "/leads/", $ruri)?>mtype/<?=Entity::STATUS_ACTIVE?>/etype/2/tab/1'">Quotes(@quotes_count@)</a>
			</li>

			<li class="nav-item ">
				<a class="nav-link <?=(@$_GET['etype'] == 3 || !ctype_digit((string) $_GET['etype'])) ? " active" : ""?>" href="<?=$ruri?>">Orders(@order_count@)</a>
			</li>			
		</ul>
	</div>
</div>

<div class="alert alert-light alert-elevate">
	<ul class="nav nav-tabs nav-tabs-line nav-tabs-bold nav-tabs-line-3x nav-tabs-line-success mb-2">
	
		<li class="nav-item mr-3" >
			<a href="<?=$ruri?>mtype/<?=Entity::STATUS_ACTIVE?>/etype/3" class="nav-link<?=(@$_GET['mtype'] == Entity::STATUS_ACTIVE || $this->status == Entity::STATUS_ACTIVE) && !isset($_GET['tab']) ? " active" :  ""?>">My Orders(@active_count@)</a>
		</li>
		
		<li class="nav-item mr-3 ">
			<a href="<?=$ruri?>mtype/<?=Entity::STATUS_POSTED?>/etype/3" class="nav-link<?=(@$_GET['mtype'] == Entity::STATUS_POSTED || $this->status == Entity::STATUS_POSTED) ? " active" : ""?>">Posted to FB<span>(@posted_count@)</span></a>
		</li>
		
		<li class="nav-item mr-3 ">
			<a href="<?=$ruri?>mtype/<?=Entity::STATUS_NOTSIGNED?>/etype/3" class="nav-link<?=(@$_GET['mtype'] == Entity::STATUS_NOTSIGNED || $this->status == Entity::STATUS_NOTSIGNED) ? " active" : ""?>">Not Signed<span>(@notsigned_count@)</span></a>
		</li>
		
		<li class="nav-item mr-3 ">
			<a href="<?=$ruri?>mtype/<?=Entity::STATUS_DISPATCHED?>/etype/3" class="nav-link<?=(@$_GET['mtype'] == Entity::STATUS_DISPATCHED || $this->status == Entity::STATUS_DISPATCHED) ? " active" : ""?>">Dispatched<span>(@dispatched_count@)</span></a>
		</li>
		
		<li class="nav-item mr-3 ">
			<a href="<?=$ruri?>mtype/<?=Entity::STATUS_PICKEDUP?>/etype/3" class="nav-link<?=(@$_GET['mtype'] == Entity::STATUS_PICKEDUP || $this->status == Entity::STATUS_PICKEDUP) ? " active" : ""?>">Picked Up<span>(@pickedup_count@)</span></a>
		</li>
		
		<li class="nav-item mr-3 ">
			<a href="<?=$ruri?>mtype/<?=Entity::STATUS_ISSUES?>/etype/3" class="nav-link<?=(@$_GET['mtype'] == Entity::STATUS_ISSUES || @$_GET['orders'] == 'searchIssue' || $this->status == Entity::STATUS_ISSUES) ? " active" : ""?>">Pending Payments&nbsp;<span><font color="red"><strong>(@issues_count@)</strong></font></span></a>
		</li>
		
		<li class="nav-item mr-3 ">
			<a href="<?=$ruri?>mtype/<?=Entity::STATUS_DELIVERED?>/etype/3" class="nav-link<?=(@$_GET['mtype'] == Entity::STATUS_DELIVERED || $this->status == Entity::STATUS_DELIVERED) ? " active" : ""?>">Delivered<span>(@delivered_count@)</span>
		</a>
		</li>
		
		<li class="nav-item mr-3 ">
			<a href="<?=$ruri?>mtype/<?=Entity::STATUS_ONHOLD?>/etype/3" class="nav-link<?=(@$_GET['mtype'] == Entity::STATUS_ONHOLD || $this->status == Entity::STATUS_ONHOLD) ? " active" : ""?>">Hold<span>(@onhold_count@)
			</a>
		</li>
		
		<li class="nav-item mr-3 ">
			<a href="<?=$ruri?>mtype/<?=Entity::STATUS_ARCHIVED?>/etype/3" class="nav-link<?=(@$_GET['mtype'] == Entity::STATUS_ARCHIVED || $this->status == Entity::STATUS_ARCHIVED) ? " active" : ""?>">Cancelled<span>(@archived_count@)</span>
		</a>
		</li>
		
		<?php if (isset($_GET['search_string'])): ?>
		<?php if ($_GET['etype'] == 3) {?>
		<li class="nav-item mr-3 ">
			<a href="<?=$ruri?>" class="nav-link<?=(@$this->status == '') ? " active" : ""?>">Search Results<span>(@order_count@)</span></a>
		</li>
		<?php } elseif ($_GET['etype'] == 4) { ?>
		<li class="nav-item ">
			<a class="nav-link <?= (@$_GET['etype'] == 4 || ($_GET['lead_search_type'] == Entity::TYPE_CLEAD && !$_GET['etype']))?" active":"" ?>" href="<?= str_replace("/orders/","/leads/",$ruri) ?>/mtype/<?= Entity::STATUS_CACTIVE ?>/etype/4/tab/1">Created Leads(@created_lead_count@)</a>
		</li>
		<?php
		} elseif ($_GET['etype'] == 2) { ?>
		
		<li class="nav-item mr-3 ">
			<a href="<?=str_replace("/orders/", "/leads/", $ruri)?>mtype/<?=Entity::STATUS_ACTIVE?>/etype/2/tab/1'" class="nav-link<?=(@$this->status == '') ? " active" : ""?>">Search Results<span>(@quotes_count@)</span></a>
		</li>
		<?php
		} elseif ($_GET['etype'] == 1) {
		?>
		<li class="nav-item mr-3 ">
			<a href="<?=str_replace("/orders/", "/leads/", $ruri)?>mtype/<?=Entity::STATUS_ACTIVE?>/etype/1/tab/1'" class="nav-link<?=(@$this->status == '') ? " active" : ""?>">Search Results<span>(@created_lead_count@)</span></a>
		</li>
		<?php } else { ?>
		<li class="nav-item mr-3 ">
			<a href="<?=str_replace("/orders/", "/leads/", $ruri)?>mtype/<?=Entity::STATUS_ACTIVE?>/etype/1/tab/1'" class="nav-link<?=(@$this->status == '') ? " active" : ""?>">Search Results<span>(@search_count@)</span></a>
		</li>
		<?php } ?>
		<?php endif;?>
	</ul>

	<div class="btn_show">
		<?php
		$entity_ids = array();
		foreach ($this->entities as $entity) {
			$entity_ids[] = $entity->id;
		} ?>
		<?=functionButton('Print', 'printOrders(window.open(\'\', \'orders\', \'height=400,width=600\'), \'' . implode(",", $entity_ids) . '\')');
		?>
	</div>
	
</div>

<?php 
	} else {
?>
<!-- New Tabs  -->
<div class="pull-right" style=" margin-bottom: 15px;"><?php
	$entity_ids = array();
	foreach ($this->entities as $entity) {
		$entity_ids[] = $entity->id;
	} ?>
	<?=functionButton('Print', 'printOrders(window.open(\'\', \'orders\', \'height=400,width=600\'), \'' . implode(",", $entity_ids) . '\')'); ?>
</div>

<div class="clearfix"></div>

<div class="alert alert-light alert-elevate"><!-- today-bar -->
	<div class="row">
		<div class="col-12">
			<ul class="nav nav-tabs nav-tabs-line nav-tabs-line-3x nav-tabs-line-success" role="tablist" style="margin-bottom:0">
			
				<li class="nav-item custom_set">
					<a class="nav-link <?=(@$_GET['orders'] == '') ? " active" : ""?>"  href="<?=SITE_IN?>application/orders/">My Orders&nbsp;<span>(@active_count@)</span></a>
				</li>

				<li class="nav-item">
					<a class="nav-link <?=(@$_GET['orders'] == 'posted') ? " active" : ""?>"  href="<?=SITE_IN?>application/orders/posted">Posted Loads (@posted_count@)</a>
				</li>
				
				<li class="nav-item">
					<a class="nav-link <?=(@$_GET['orders'] == 'notsigned') ? " active" : ""?>"  href="<?=SITE_IN?>application/orders/notsigned">Not Signed (@notsigned_count@)</a>
				</li>
				
				<li class="nav-item">
					<a class="nav-link <?=(@$_GET['orders'] == 'dispatched') ? " active" : ""?>"  href="<?=SITE_IN?>application/orders/dispatched">Dispatched<span>(@dispatched_count@)</span></a>
				</li>

				<li class="nav-item">
					<a class="nav-link <?=(@$_GET['orders'] == 'pickedup') ? " active" : ""?>"  href="<?=SITE_IN?>application/orders/pickedup">Picked Up&nbsp;<span>(@pickedup_count@)</span></a>
				</li>

				<li class="nav-item">
					<a class="nav-link <?=(@$_GET['orders'] == 'issues' || @$_GET['orders'] == ' searchIssue') ? " active" : ""?>"  href="<?=SITE_IN?>application/orders/issues">Pending Payments&nbsp;<span><font color="red"><strong>(@issues_count@)</strong></font></span></a>
				</li>

				<li class="nav-item">
					<a class="nav-link <?=(@$_GET['orders'] == 'delivered') ? " active" : ""?>"  href="<?=SITE_IN?>application/orders/delivered">Delivered&nbsp;<span>(@delivered_count@)</span></a>
				</li>

				<li class="nav-item">
					<a class="nav-link <?=(@$_GET['orders'] == 'onhold') ? " active" : ""?>"  href="<?=SITE_IN?>application/orders/onhold">OnHold&nbsp;<span>(@onhold_count@)</span></a>
				</li>
				
				<li class="nav-item">
					<a class="nav-link <?=(@$_GET['orders'] == 'archived') ? " active" : ""?>"  href="<?=SITE_IN?>application/orders/archived">Cancelled<span>(@archived_count@)</span></a>
				</li>

				<li class="nav-item">
					<a class="nav-link <?=(@$_GET['orders'] == 'pending') ? " active" : ""?>"  href="<?=SITE_IN?>application/orders/pending">Pending<span>(@pending_count@)</span></a>
				</li>

				<?php if (isset($_GET['search_string'])): ?>
					<li class="last tab active">Results<span>(@search_count@)</span></li>
				<?php endif;?>
				
			</ul>
		</div>
	</div>

</div>
<?php }?>