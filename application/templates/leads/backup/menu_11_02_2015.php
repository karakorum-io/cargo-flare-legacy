<?php //print "-----".$_GET['leads']."----"; ?>
<div class="tab-panel-container">
	<ul class="tab-panel">
		<li class="tab first<?= (@$_GET['leads'] == '' || 
		  $_GET['leads'] == 'assigned' ||
		  $_GET['leads'] == 'onhold' ||
		  $_GET['leads'] == 'archived' || 
		  $_GET['leads'] == 'unreadable')?" active":"" ?>"onclick="location.href = '<?= SITE_IN ?>application/leads/'">Imported Leads<span></span></li>
        <!--li class="tab<?= (@$_GET['leads'] == 'created')?" active":"" ?>" onclick="location.href = '<?= SITE_IN ?>application/leads/created'">Created Leads<span>(@created_count@)</span></li-->
        <?php if($_SESSION['member_id']==1){?>
         <li class="tab<?= (@$_GET['leads'] == 'created' ||
				$_GET['leads'] == 'cpriority' ||
				$_GET['leads'] == 'cassigned' ||
				$_GET['leads'] == 'conhold' ||
				$_GET['leads'] == 'cunreadable' ||
				$_GET['leads'] == 'carchived'  ||
				$_GET['leads'] == 'converted'  ||
				$_GET['leads'] == 'cdead' )?" active":"" ?>" onclick="location.href = '<?= SITE_IN ?>application/leads/created'">Created Leads<span></span>
         
         </li>
         <?php } ?>
         <?php if (isset($_GET['search_string'])) : ?>
		<li class="last tab active">Search Results<span>(@search_count@)</span></li>
		<?php endif; ?>
	</ul>
    
	<div style="float:right;">
		<table cellspacing="5" cellpadding="0" border="0">
			<tr>
				<!--<td><?=functionButton('GetLeads', 'window.location.href=\''.SITE_IN.'application/leads\'');?></td>-->
				<?php $entity_ids = array(); foreach($this->entities as $entity) { $entity_ids[] = $entity->id; }?>
				<td><?=functionButton('Print', 'printLeads(window.open(\'\', \'Leads\', \'height=400,width=600\'), \''.implode(",",$entity_ids).'\')');?></td>
			</tr>
		</table>
	</div>
</div>
<div class="tab-panel-line"></div>
<?php if (!isset($_GET['search_string'])) : ?>
<div class="tab-panel-container" style="margin-top:15px;">
	<ul class="tab-panel">
		
 <?php if($_GET['leads'] == '' || 
		  $_GET['leads'] == 'assigned' ||
		  $_GET['leads'] == 'onhold' ||
		  $_GET['leads'] == 'unreadable' ||
		  $_GET['leads'] == 'archived'
		  ){?>     
          <li class="tab<?= (@$_GET['leads'] == '')?" active":"" ?>" onclick="location.href = '<?= SITE_IN ?>application/leads/'">Active Leads<span>(@active_count@)</span></li> 
        <!--li class="tab<?= (@$_GET['leads'] == 'assigned')?" active":"" ?>" onclick="location.href = '<?= SITE_IN ?>application/leads/assigned'">Assigned Leads<span>(@assigned_count@)</span></li-->
        
		<li class="tab<?= (@$_GET['leads'] == 'onhold')?" active":"" ?>" onclick="location.href = '<?= SITE_IN ?>application/leads/onhold'">Hold<span>(@onhold_count@)</span></li>
		<li class="tab<?= (@$_GET['leads'] == 'unreadable')?" active":"" ?>" onclick="location.href = '<?= SITE_IN ?>application/leads/unreadable'">Unreadable<span>(@unreadable_count@)</span></li>
        <li class="tab<?= (@$_GET['leads'] == 'archived')?" active":"" ?>" onclick="location.href = '<?= SITE_IN ?>application/leads/archived'">Cancelled<span>(@archived_count@)</span></li>
		<?php if (isset($_GET['search_string'])) : ?>
		<li class="last tab active">Search Results<span>(@search_count@)</span></li>
		<?php endif; ?>
        
   <?php }elseif($_GET['leads'] == 'created' ||
				$_GET['leads'] == 'cpriority' ||
				$_GET['leads'] == 'cassigned' ||
				$_GET['leads'] == 'conhold' ||
				$_GET['leads'] == 'cunreadable' ||
				$_GET['leads'] == 'carchived'  ||
				$_GET['leads'] == 'converted'  ||
				$_GET['leads'] == 'cdead' 
				 ){?>     
   <li class="tab<?= (@$_GET['leads'] == 'created')?" active":"" ?>" onclick="location.href = '<?= SITE_IN ?>application/leads/created'">New Leads<span>(@active_count@)</span></li>
   <li class="tab<?= (@$_GET['leads'] == 'cassigned')?" active":"" ?>" onclick="location.href = '<?= SITE_IN ?>application/leads/cassigned'">Assigned Leads<span>(@assigned_count@)</span></li>
        <li class="tab<?= (@$_GET['leads'] == 'cpriority')?" active":"" ?>" onclick="location.href = '<?= SITE_IN ?>application/leads/cpriority'">Priority Leads<span>(@priority_count@)</span></li>
		<li class="tab<?= (@$_GET['leads'] == 'conhold')?" active":"" ?>" onclick="location.href = '<?= SITE_IN ?>application/leads/conhold'">Hold<span>(@onhold_count@)</span></li>
		<!--li class="tab<?= (@$_GET['leads'] == 'cunreadable')?" active":"" ?>" onclick="location.href = '<?= SITE_IN ?>application/leads/cunreadable'">Unreadable<span>(@unreadable_count@)</span></li-->
        <li class="tab<?= (@$_GET['leads'] == 'cdead')?" active":"" ?>" onclick="location.href = '<?= SITE_IN ?>application/leads/cdead'">Do Not Call<span>(@dead_count@)</span></li>
        <li class="tab<?= (@$_GET['leads'] == 'converted')?" active":"" ?>" onclick="location.href = '<?= SITE_IN ?>application/leads/converted'">Converted To<span>(@converted_count@)</span></li>
        <li class="last tab<?= (@$_GET['leads'] == 'carchived')?" active":"" ?>" onclick="location.href = '<?= SITE_IN ?>application/leads/carchived'">Cancelled<span>(@archived_count@)</span></li>
		<?php if (isset($_GET['search_string'])) : ?>
		<li class="last tab active">Search Results<span>(@search_count@)</span></li>
		<?php endif; ?>
   <?php }?>
	</ul>
    
	
</div>
<?php endif; ?>