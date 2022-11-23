<style type="text/css">
.modal-content .modal-header .close:before {
		display: none;
	}	
	span.history {
		position: absolute;
		top: 351px;
		font-size: 20px;
		margin-bottom: -2px;
	}
</style>

<?php if (isset($_GET['search_string'])) { 
	$ruri=rawurldecode($_SERVER['REQUEST_URI']);
	$arrStr1 = explode("/mtype/",$ruri);
	if($arrStr1[0]!='')
		$ruri = $arrStr1[0];
?>

<div class="kt-nav">
	<ul class="nav nav-tabs nav-tabs-line nav-tabs-bold nav-tabs-line-3x nav-tabs-line-success" role="tablist">
		<li class="nav-item ">
			<a class="nav-link <?= (@$_GET['etype'] == 1 || ($_GET['lead_search_type'] == Entity::TYPE_LEAD && !$_GET['etype']))?" active":"" ?>" href="<?=str_replace("/orders/", "/leads/", $ruri)?>mtype/<?=Entity::STATUS_ACTIVE?>/etype/1/tab/1'">Leads(@imported_lead_count@)</a>
		</li>
		<li class="nav-item ">
			<a class="nav-link <?= (@$_GET['etype'] == 4 || ($_GET['lead_search_type'] == Entity::TYPE_CLEAD && !$_GET['etype']))?" active":"" ?>" href="<?= str_replace("/orders/","/leads/",$ruri) ?>/mtype/<?= Entity::STATUS_CACTIVE ?>/etype/4/tab/1">Created Leads(@created_lead_count@)</a>
		</li>
        <li class="nav-item ">
			<a class="nav-link <?= (@$_GET['etype'] == 2 || ($_GET['lead_search_type'] == Entity::TYPE_QUOTE && !$_GET['etype']))?" active":"" ?>" href="<?= str_replace("/orders/","/leads/",$ruri) ?>/mtype/<?= Entity::STATUS_ACTIVE ?>/etype/2/tab/1">Quotes(@quotes_count@)</a>
		</li>
         <?php
		    $Oruri = str_replace("/leads/","/orders/",$ruri);
		 ?>
		<li class="nav-item ">
			<a class="nav-link <?= (@$_GET['etype'] == 3)?" active":"" ?>" href="<?= $Oruri ?>/mtype/<?= Entity::STATUS_ACTIVE ?>/etype/3/tab/1">Orders(@order_count@)</a>
		</li>
	</ul>
</div>     

<div class="alert alert-light alert-elevate">
	<ul class="nav nav-tabs nav-tabs-line nav-tabs-bold nav-tabs-line-3x nav-tabs-line-success">
		<?php 
			if($_GET['etype'] == 1 || ($_GET['lead_search_type'] == Entity::TYPE_LEAD && !$_GET['etype'] )){
		?>
		<li class="nav-item " >
			<a href="<?= $ruri ?>/mtype/<?= Entity::STATUS_ACTIVE ?>/etype/<?= Entity::TYPE_LEAD ?>'" class="nav-link<?= (@$_GET['mtype'] == Entity::STATUS_ACTIVE) && !isset($_GET['tab'])?" active":"" ?>">Quote Requests<span>(@active_count@)</a>
		</li>
		<!-- <li class="nav-item " >
			<a href="<?= $ruri ?>/mtype/<?= Entity::STATUS_LQUOTED ?>/etype/<?= Entity::TYPE_LEAD ?>" class="nav-link<?= (@$_GET['mtype'] == Entity::STATUS_LQUOTED)?" active":"" ?>">Today's Quotes<span>(@quoted_count@)</span></a>
		</li>
		<li class="nav-item " >
			<a href="<?= $ruri ?>/mtype/<?= Entity::STATUS_LFOLLOWUP ?>/etype/<?= Entity::TYPE_LEAD ?>" class="nav-link<?= (@$_GET['mtype'] == Entity::STATUS_LFOLLOWUP)?" active":"" ?>">Followups<span>(@follow_count@)</span></a>
		</li> -->
        <?php if($_SESSION['parent_id']==1){?>
        <?php }?>
		<!-- <li class="nav-item " >
			<a href="<?= $ruri ?>/mtype/<?= Entity::STATUS_LEXPIRED ?>/etype/<?= Entity::TYPE_LEAD ?>" class="nav-link<?= (@$_GET['mtype'] == Entity::STATUS_LEXPIRED)?" active":"" ?>">Expired Quotes<span>(@expired_count@)</span></a>
		</li> -->
		<li class="nav-item " >
			<a href="<?= $ruri ?>/mtype/<?= Entity::STATUS_LDUPLICATE ?>/etype/<?= Entity::TYPE_LEAD ?>" class="nav-link<?= (@$_GET['mtype'] == Entity::STATUS_LDUPLICATE)?" active":"" ?>"> Duplicate<span>(@duplicate_count@)</span></a>
		</li>
		<li class="nav-item " >
			<a href="<?= $ruri ?>/mtype/<?= Entity::STATUS_ONHOLD ?>/etype/<?= Entity::TYPE_LEAD ?>" class="nav-link<?= (@$_GET['mtype'] == Entity::STATUS_ONHOLD)?" active":"" ?>">Hold<span>(@onhold_count@)</span></a>
		</li>
		<li class="nav-item " >
			<a href="<?= $ruri ?>/mtype/<?= Entity::STATUS_UNREADABLE ?>/etype/<?= Entity::TYPE_LEAD ?>" class="nav-link<?= (@$_GET['mtype'] == Entity::STATUS_UNREADABLE)?" active":"" ?>">Unreadable<span>(@unreadable_count@)</span></a>
		</li>
		<li class="nav-item " >
			<a href="<?= $ruri ?>/mtype/<?= Entity::STATUS_ARCHIVED ?>/etype/<?= Entity::TYPE_LEAD ?>" class="nav-link<?= (@$_GET['mtype'] == Entity::STATUS_ARCHIVED)?" active":"" ?>">Cancelled<span>(@archived_count@)</span></a>
		</li>
		<?php 
			} elseif($_GET['etype'] == 4  || ($_GET['lead_search_type'] == Entity::TYPE_CLEAD && !$_GET['etype'] )) {
		?>  
		<li class="nav-item " >
			<a href="<?= $ruri ?>/mtype/<?= Entity::STATUS_CACTIVE ?>/etype/<?= Entity::TYPE_CLEAD ?>" class="nav-link<?= (@$_GET['mtype'] == Entity::STATUS_CACTIVE) && !isset($_GET['tab'])?" active":"" ?>">Leads<span>(@active_count@)</a>
		</li>
		<li class="nav-item " >
			<a href="<?= $ruri ?>/mtype/<?= Entity::STATUS_CASSIGNED ?>/etype/<?= Entity::TYPE_CLEAD ?>" class="nav-link<?= (@$_GET['mtype'] == Entity::STATUS_CASSIGNED)?" active":"" ?>">Assigned Leads<span>(@assigned_count@)</span></a>
		</li>
		<li class="nav-item " >
			<a href="<?= $ruri ?>/mtype/<?= Entity::STATUS_CPRIORITY ?>/etype/<?= Entity::TYPE_CLEAD ?>" class="nav-link<?= (@$_GET['mtype'] == Entity::STATUS_CPRIORITY)?" active":"" ?>">Priority Leads<span>(@priority_count@)</span></a>
		</li>
		<!-- <li class="nav-item " >
			<a href="<?= $ruri ?>/mtype/<?= Entity::STATUS_CQUOTED ?>/etype/<?= Entity::TYPE_CLEAD ?>" class="nav-link<?= (@$_GET['mtype'] == Entity::STATUS_CQUOTED)?" active":"" ?>">Today's Quotes<span>(@cquoted_count@)</span></a>
		</li>
		<li class="nav-item " >
			<a href="<?= $ruri ?>/mtype/<?= Entity::STATUS_CQUOTED ?>/etype/<?= Entity::TYPE_CLEAD ?>" class="nav-link<?= (@$_GET['mtype'] == Entity::STATUS_CFOLLOWUP)?" active":"" ?>">Followups<span>(@cfollow_count@)</span></a>
		</li>
		<li class="nav-item " >
			<a href="<?= $ruri ?>/mtype/<?= Entity::STATUS_CEXPIRED ?>/etype/<?= Entity::TYPE_CLEAD ?>" class="nav-link<?= (@$_GET['mtype'] == Entity::STATUS_CEXPIRED)?" active":"" ?>">Expired Quotes<span>(@cexpired_count@)</span></a>
		</li> -->
		<li class="nav-item " >
			<a href="<?= $ruri ?>/mtype/<?= Entity::STATUS_CONHOLD ?>/etype/<?= Entity::TYPE_CLEAD ?>" class="nav-link<?= (@$_GET['mtype'] == Entity::STATUS_CONHOLD)?" active":"" ?>">Hold<span>(@onhold_count@)</span></a>
		</li>
		<li class="nav-item " >
			<a href="<?= $ruri ?>/mtype/<?= Entity::STATUS_CDEAD ?>/etype/<?= Entity::TYPE_CLEAD ?>" class="nav-link<?= (@$_GET['mtype'] == Entity::STATUS_CDEAD)?" active":"" ?>">Do Not Call<span>(@dead_count@)</span></a>
		</li>
		<li class="nav-item " >
			<a href="<?= $ruri ?>/mtype/<?= Entity::STATUS_CARCHIVED ?>/etype/<?= Entity::TYPE_CLEAD ?>" class="nav-link<?= (@$_GET['mtype'] == Entity::STATUS_CARCHIVED)?" active":"" ?>">Cancelled<span>(@archived_count@)</a>
		</li>
	<?php }?>
   
	<?php if (isset($_GET['search_string']) ) : ?>
		<?php if($_GET['etype'] == 3){ ?>
		<li class="nav-item " >
			<a href="<?= $ruri ?>" class="nav-link<?= (@$this->status =='')?" active":"" ?>">Search Results<span>(@order_count@)</span></a>
		</li>
		<?php }elseif($_GET['etype'] == 4){?>
		<li class="nav-item " >
			<a href="<?= str_replace("/orders/","/leads/",$ruri) ?>/mtype/<?= Entity::STATUS_CACTIVE ?>/etype/4/tab/1" class="nav-link<?= (@$this-> status =='')?" active":"" ?>">Search Results<span>(@created_lead_count@)</span></a>
		</li>
		<?php }elseif($_GET['etype'] == 1){?>
		<li class="nav-item " >
			<a href="<?= str_replace("/orders/","/leads/",$ruri) ?>/mtype/<?= Entity::STATUS_ACTIVE ?>/etype/1/tab/1'" class="nav-link<?= (@$this-> status =='')?" active":"" ?>">Search  Results<span>(@imported_lead_count@)</span></a>
		</li>
		<?php }else{?>
		<li class="nav-item " >
			<a href="<?= $ruri ?>/mtype/<?= Entity::STATUS_ARCHIVED ?>/etype/<?= Entity::TYPE_LEAD ?>" class="nav-link<?= (@$this->status =='')?" active":"" ?>">Search Results<span>(@search_count@)</span></a>
		</li>
		<?php }?>
		<?php endif; ?>
	</ul>
</div>
	
<?php } else {?>
	  
<div class="row">
	<div class="col-md-4">
		<ul class="nav nav-pills nav-fill" >
			<li class="custom_col nav-item<?= (@$_GET['leads'] == '' ||
				$_GET['leads'] == 'assigned' ||
				$_GET['leads'] == 'quoted' ||
				$_GET['leads'] == 'follow' ||
				$_GET['leads'] == 'expired' ||
				$_GET['leads'] == 'duplicate' ||
				$_GET['leads'] == 'appointment' ||
				$_GET['leads'] == 'onhold' ||
				$_GET['leads'] == 'archived' || 
				$_GET['leads'] == 'unreadable')?" active":"" ?>" >
				<a class="nav-link"  href="<?= SITE_IN ?>application/leads">Imported Leads</a>
			</li>
		   
			<?php if($_SESSION['parent_id']==1){?>
			<li class=" custom_col nav-item<?= (@$_GET['leads'] == 'created' ||
				$_GET['leads'] == 'cquoted' ||
				$_GET['leads'] == 'cfollow' ||
				$_GET['leads'] == 'cexpired' ||
				$_GET['leads'] == 'cduplicate' ||
				$_GET['leads'] == 'cappointment' ||
				$_GET['leads'] == 'cpriority' ||
				$_GET['leads'] == 'cassigned' ||
				$_GET['leads'] == 'conhold' ||
				$_GET['leads'] == 'cunreadable' ||
				$_GET['leads'] == 'carchived'  ||
				$_GET['leads'] == 'converted'  ||
				$_GET['leads'] == 'cdead' )?" active":"" ?>">
				<a class="nav-link"  href="<?= SITE_IN ?>application/leads/created">Created Leads</a>
			</li>
			 <?php } ?>
			 <?php if (isset($_GET['search_string'])) : ?>
			<li class="last nav-item">Search Results<span>(@search_count@)</span></li>
			<?php endif; ?>
		</ul>
	</div>
	<div class="col-md-8 prt-btn">
		<div style="float:right;">
			<?php $entity_ids = array(); foreach($this->entities as $entity) { $entity_ids[] = $entity->id; }?>
			<?=functionButton('Print', 'printLeads(window.open(\'\', \'Leads\', \'height=400,width=600\'), \''.implode(",",$entity_ids).'\')');?>
		</div>
	</div>
</div>

<?php if (!isset($_GET['search_string'])) : ?>
	<div class="alert alert-light alert-elevate" role="alert">
		<div class="row">
			<div class="col-12">
				<ul class="nav nav-tabs  nav-tabs-line nav-tabs-line-3x nav-tabs-line-success" role="tablist" style="margin-bottom: 0px">
					<?php if($_GET['leads'] == '' || 
					$_GET['leads'] == 'quoted' ||
					$_GET['leads'] == 'follow' ||
					$_GET['leads'] == 'expired' ||
					$_GET['leads'] == 'duplicate' ||
					$_GET['leads'] == 'appointment' ||
					$_GET['leads'] == 'onhold' ||
					$_GET['leads'] == 'unreadable' ||
					$_GET['leads'] == 'archived'
					){?>     
					<li class="nav-item custom_set">
						<a class="nav-link <?= (@$_GET['leads'] == '')?" active":"" ?>"  href="<?= SITE_IN ?>application/leads/">Quote Requests (@active_count@)</a>
					</li> 
					<!-- <li class="nav-item" >
						<a class="nav-link<?= (@$_GET['leads'] == 'quoted')?" active":"" ?>"  href="<?= SITE_IN ?>application/leads/quoted">Today's Quotes (@quoted_count@)</a>
					</li>
					<li class="nav-item">
						<a class="nav-link<?= (@$_GET['leads'] == 'follow')?" active":"" ?>" href="<?= SITE_IN ?>application/leads/follow">Follow-ups (@follow_count@)</a>
					</li>
					<li class="nav-item">
						<a class="nav-link<?= (@$_GET['leads'] == 'expired')?" active":"" ?>"  href="<?= SITE_IN ?>application/leads/expired">Expired Quotes(@expired_count@)</a>
					</li> -->
					<li class="nav-item">
						<a class="nav-link<?= (@$_GET['leads'] == 'duplicate')?" active":"" ?>"  href="<?= SITE_IN ?>application/leads/duplicate">Possible Duplicate(@duplicate_count@)</a>
					</li>
					<li class="nav-item" >
						<a class="nav-link<?= (@$_GET['leads'] == 'onhold')?" active":"" ?>"  href="<?= SITE_IN ?>application/leads/onhold">Hold (@onhold_count@)</a>
					</li>
					<li class="nav-item" >
						<a class="nav-link<?= (@$_GET['leads'] == 'unreadable')?" active":"" ?>"  href="<?= SITE_IN ?>application/leads/unreadable">Unreadable(@unreadable_count@)</a>
					</li>
					<li class="nav-item" >
						<a class="nav-link<?= (@$_GET['leads'] == 'archived')?" active":"" ?>"  href="<?= SITE_IN ?>application/leads/archived">Cancelled(@archived_count@)</a>
					</li>
					<?php if (isset($_GET['search_string'])) : ?>
						<li class="last tab active">Search Results<span>(@search_count@)</span></li>
					<?php endif; ?>
					<?php }elseif($_GET['leads'] == 'created' ||
					$_GET['leads'] == 'cquoted' ||
					$_GET['leads'] == 'cfollow' ||
					$_GET['leads'] == 'cexpired' ||
					$_GET['leads'] == 'cduplicate' ||
					$_GET['leads'] == 'cappointment' ||
					$_GET['leads'] == 'cpriority' ||
					$_GET['leads'] == 'cassigned' ||
					$_GET['leads'] == 'conhold' ||
					$_GET['leads'] == 'cunreadable' ||
					$_GET['leads'] == 'carchived'  ||
					$_GET['leads'] == 'converted'  ||
					$_GET['leads'] == 'cdead' ){?>     
					<li class="nav-item">
						<a class="nav-link<?= (@$_GET['leads'] == 'created')?" active":"" ?>"  href="<?= SITE_IN ?>application/leads/created">Leads(@active_count@)</a>
					</li>
					<li class="nav-item" >
						<a class="nav-link<?= (@$_GET['leads'] == 'cassigned')?" active":"" ?>"  href="<?= SITE_IN ?>application/leads/cassigned">Assigned to<span>(@assigned_count@)</span> </a>
					</li>

					<li class="nav-item" >
						<a class="nav-link<?= (@$_GET['leads'] == 'cpriority')?" active":"" ?>"  href="<?= SITE_IN ?>application/leads/cpriority">Priority Leads(@priority_count@) </a>
					</li>
					<!-- <li class="nav-item" >
						<a class="nav-link<?= (@$_GET['leads'] == 'cquoted')?" active":"" ?>"  href="<?= SITE_IN ?>application/leads/cquoted">Today's Quotes(@cquoted_count@) </a>
					</li>
					<li class="nav-item">
						<a class="nav-link<?= (@$_GET['leads'] == 'follow')?" active":"" ?>"  href="<?= SITE_IN ?>application/leads/follow">Follow-ups(@cfollow_count@) </a>
					</li>
					<li class="nav-item" >
						<a class="nav-link<?= (@$_GET['leads'] == 'cexpired')?" active":"" ?>"  href="<?= SITE_IN ?>application/leads/cexpired">Expired Quotes(@cexpired_count@) </a>
					</li> -->
					<li class="nav-item">
						<a class="nav-link<?= (@$_GET['leads'] == 'conhold')?" active":"" ?>"  href="<?= SITE_IN ?>application/leads/conhold">Hold(@onhold_count@) </a>
					</li>
					<li class="nav-item" >
						<a class="nav-link<?= (@$_GET['leads'] == 'cdead')?" active":"" ?>"  href="<?= SITE_IN ?>application/leads/cdead">Do Not Call(@dead_count@) </a>
					</li>
					<li class="nav-item" >
						<a class="nav-link<?= (@$_GET['leads'] == 'converted')?" active":"" ?>"  href="<?= SITE_IN ?>application/leads/converted">Converted(@converted_count@) </a>
					</li>
					<li class=" nav-item" >
						<a class="nav-link<?= (@$_GET['leads'] == 'carchived')?" active":"" ?>"  href="<?= SITE_IN ?>application/leads/carchived">Cancelled<span>(@archived_count@) </a>
					</li>
				   <?php }?>
				</ul>
			</div>
		</div>
	</div>
<?php endif; ?>
<?php }?>