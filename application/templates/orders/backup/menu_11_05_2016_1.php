 <?php if (isset($_GET['search_string'])) { 
 
     $ruri=rawurldecode($_SERVER['REQUEST_URI']);
	 
	 //$arrStr1 = explode("/op/",$ruri);
	  $arrStr1 = explode("mtype/",$ruri);
	//print_r($arrStr1);
	 if($arrStr1[0]!='')
	   $ruri = $arrStr1[0];
	 if(!isset($_GET['mtype']))  
	    $ruri .= "/";
	   //print $ruri;
	   
	   $searchUrl = $ruri;
 ?>
      
 <div class="tab-panel-container">
	<ul class="tab-panel">
		<li class="tab first <?= (@$_GET['etype'] == 1)?" active":"" ?>" onclick="location.href = '<?= str_replace("/orders/","/leads/",$ruri) ?>mtype/<?= Entity::STATUS_ACTIVE ?>/etype/1/tab/1'">Imported Leads(@imported_lead_count@)<span></span></li>
        
        <?php if($_SESSION['parent_id']==1){?>
        
         <li class="tab <?= (@$_GET['etype'] == 4)?" active":"" ?>" onclick="location.href = '<?= str_replace("/orders/","/leads/",$ruri) ?>mtype/<?= Entity::STATUS_CACTIVE ?>/etype/4/tab/1'">Created Leads(@created_lead_count@)<span></span>
         
         </li>
         <?php } 
		 
		 ?>
         
		<li class="last tab <?= (@$_GET['etype'] == 3  || !ctype_digit((string)$_GET['etype']))?" active":"" ?>" onclick="location.href = '<?= $ruri ?>'">Orders(@order_count@)</li>
		
	</ul>
</div>     
<div class="tab-panel-line"></div>

<div class="tab-panel-container"  style="margin-top:15px;">
	<ul class="tab-panel">
     
        <?php /*<li class="tab first<?= (@$_GET['orders'] == 'all')?" active":"" ?>"onclick="location.href = '<?= SITE_IN ?>application/orders/all'">All</li>*/ ?>
		<li class="tab first<?= (@$_GET['mtype'] == Entity::STATUS_ACTIVE || $this->status == Entity::STATUS_ACTIVE) &&  !isset($_GET['tab'])?" active":"" ?>"onclick="location.href = '<?= $ruri ?>mtype/<?= Entity::STATUS_ACTIVE ?>/etype/3'">My Orders<span>(@active_count@)</span></li>
		<li class="tab<?= (@$_GET['mtype'] == Entity::STATUS_POSTED || $this->status == Entity::STATUS_POSTED)?" active":"" ?>"onclick="location.href = '<?= $ruri ?>mtype/<?= Entity::STATUS_POSTED ?>/etype/3'">Posted to FB<span>(@posted_count@)</span></li>
		<li class="tab<?= (@$_GET['mtype'] == Entity::STATUS_NOTSIGNED  || $this->status == Entity::STATUS_NOTSIGNED)?" active":"" ?>"onclick="location.href = '<?= $ruri ?>mtype/<?= Entity::STATUS_NOTSIGNED ?>/etype/3'">Not Signed<span>(@notsigned_count@)</span></li>
		<li class="tab<?= (@$_GET['mtype'] == Entity::STATUS_DISPATCHED || $this->status == Entity::STATUS_DISPATCHED)?" active":"" ?>"onclick="location.href = '<?= $ruri ?>mtype/<?= Entity::STATUS_DISPATCHED ?>/etype/3'">Dispatched<span>(@dispatched_count@)</span></li>
		<li class="tab<?= (@$_GET['mtype'] == Entity::STATUS_PICKEDUP  || $this->status == Entity::STATUS_PICKEDUP)?" active":"" ?>"onclick="location.href = '<?= $ruri ?>mtype/<?= Entity::STATUS_PICKEDUP ?>/etype/3'">Picked Up<span>(@pickedup_count@)</span></li>
        <li class="tab<?= (@$_GET['mtype'] == Entity::STATUS_ISSUES || @$_GET['orders'] == 'searchIssue'  || $this->status == Entity::STATUS_ISSUES)?" active":"" ?>"onclick="location.href = '<?= $ruri ?>mtype/<?= Entity::STATUS_ISSUES ?>/etype/3'">Issues<span>(@issues_count@)</span></li>
		
		<li class="tab<?= (@$_GET['mtype'] == Entity::STATUS_DELIVERED || $this->status == Entity::STATUS_DELIVERED)?" active":"" ?>"onclick="location.href = '<?= $ruri ?>mtype/<?= Entity::STATUS_DELIVERED ?>/etype/3'">Delivered<span>(@delivered_count@)</span></li>
		<li class="tab<?= (@$_GET['mtype'] == Entity::STATUS_ONHOLD || $this->status == Entity::STATUS_ONHOLD)?" active":"" ?>" onclick="location.href = '<?= $ruri ?>mtype/<?= Entity::STATUS_ONHOLD ?>/etype/3'">Hold<span>(@onhold_count@)</span></li>
		<li class="last tab<?= (@$_GET['mtype'] == Entity::STATUS_ARCHIVED || $this->status == Entity::STATUS_ARCHIVED)?" active":"" ?>" onclick="location.href = '<?= $ruri ?>mtype/<?= Entity::STATUS_ARCHIVED ?>/etype/3'">Cancelled<span>(@archived_count@)</span></li>
        
		<?php if (isset($_GET['search_string'])) : ?>
			 <?php //if(isset($_GET['tab'])){?>
                  <?php if($_GET['etype'] == 3){ ?>
                       <li class="last tab <?= (@$this->status =='')?" active":"" ?>" onclick="location.href = '<?= $ruri ?>'">Search Results<span>(@order_count@)</span></li>
                  <?php }elseif($_GET['etype'] == 4){?>
                       <li class="last tab <?= (@$this->status =='')?" active":"" ?>" onclick="location.href = '<?= str_replace("/orders/","/leads/",$ruri) ?>mtype/<?= Entity::STATUS_CACTIVE ?>/etype/4/tab/1'">Search Results<span>(@created_lead_count@)</span></li>
                  <?php }elseif($_GET['etype'] == 1){?>
                       <li class="last tab <?= (@$this->status =='')?" active":"" ?>" onclick="location.href = '<?= str_replace("/orders/","/leads/",$ruri) ?>mtype/<?= Entity::STATUS_ACTIVE ?>/etype/1/tab/1'">Search Results<span>(@imported_lead_count@)</span></li>
                  <?php //}?>
             <?php }else{?>
                 <li class="last tab <?= (@$this->status =='')?" active":"" ?>">Search Results<span>(@search_count@)</span></li>
            <?php }?>
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


      <?php }else{?>

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
		<li class="last tab<?= (@$_GET['orders'] == 'archived')?" active":"" ?>" onclick="location.href = '<?= SITE_IN ?>application/orders/cancelled'">Cancelled<span>(@archived_count@)</span></li>
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
<?php }?>