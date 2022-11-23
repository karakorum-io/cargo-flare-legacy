<?php if (isset($_GET['search_string'])) { 
 
     $ruri=rawurldecode($_SERVER['REQUEST_URI']);
	 
	 //$arrStr1 = explode("/op/",$ruri);
	  $arrStr1 = explode("mtype/",$ruri);
	//print_r($arrStr1);
	 if($arrStr1[0]!='')
	   $ruri = $arrStr1[0];
	   
	   //print $ruri;
 ?>
 <center><font size="4px">We found <strong>@search_count@</strong> Record(s)</font>&nbsp;:&nbsp;<span style="font-weight: bold;" class="hint--right hint--rounded hint--bounce hint--error" data-hint="Clear Search Result"><button type="button" class="searchform-button-small searchform-buttonhover" id="clearsearch" onclick="document.location.href = '<?= SITE_IN ?>application/orders/';">X</button></span></center>        
 <div class="tab-panel-container">
	<ul class="tab-panel">
		<li class="tab first <?= (@$_GET['etype'] == 1 || ($_GET['lead_search_type'] == Entity::TYPE_LEAD && !$_GET['etype']))?" active":"" ?>"onclick="location.href = '<?= str_replace("/orders/","/leads/",$ruri) ?>mtype/<?= Entity::STATUS_ACTIVE ?>/etype/1/tab/1'">Leads(@imported_lead_count@)<span></span></li>
        
        <?php if($_SESSION['parent_id']==1){?>
        
         <li class="tab <?= (@$_GET['etype'] == 4 || ($_GET['lead_search_type'] == Entity::TYPE_CLEAD && !$_GET['etype']))?" active":"" ?>" onclick="location.href = '<?= str_replace("/orders/","/leads/",$ruri) ?>mtype/<?= Entity::STATUS_CACTIVE ?>/etype/4/tab/1'">Created Leads(@created_lead_count@)<span></span>
         
         </li>
         <?php } 
		 if($_GET['etype']==1 || $_GET['etype']==4 || (($_GET['lead_search_type'] == Entity::TYPE_LEAD || $_GET['lead_search_type'] == Entity::TYPE_CLEAD) && !$_GET['etype']))
		    $Oruri = str_replace("/leads/","/orders/",$ruri);
		 ?>
         
		<li class="last tab <?= (@$_GET['etype'] == 3)?" active":"" ?>" onclick="location.href = '<?= $Oruri ?>mtype/<?= Entity::STATUS_ACTIVE ?>/etype/3/tab/1'">Orders(@order_count@)</li>
		
	</ul>
</div>     
<div class="tab-panel-line"></div>

<div class="tab-panel-container" style="margin-top:15px;">
	<ul class="tab-panel">
		
 <?php if($_GET['etype'] == 1 || ($_GET['lead_search_type'] == Entity::TYPE_LEAD && !$_GET['etype'] )
		  ){?>     
          <li class="tab <?= (@$_GET['mtype'] == Entity::STATUS_ACTIVE) && !isset($_GET['tab'])?" active":"" ?>" onclick="location.href = '<?= $ruri ?>mtype/<?= Entity::STATUS_ACTIVE ?>/etype/<?= Entity::TYPE_LEAD ?>'">Quote Requests<span>(@active_count@)</span></li> 
        
        <li class="tab<?= (@$_GET['mtype'] == Entity::STATUS_LQUOTED)?" active":"" ?>" onclick="location.href = '<?= $ruri ?>mtype/<?= Entity::STATUS_LQUOTED ?>/etype/<?= Entity::TYPE_LEAD ?>'">Today's Quotes<span>(@quoted_count@)</span></li>
        <li class="tab<?= (@$_GET['mtype'] == Entity::STATUS_LFOLLOWUP)?" active":"" ?>" onclick="location.href = '<?= $ruri ?>mtype/<?= Entity::STATUS_LFOLLOWUP ?>/etype/<?= Entity::TYPE_LEAD ?>'">Followups<span>(@follow_count@)</span></li>
        <li class="tab<?= (@$_GET['mtype'] == Entity::STATUS_LAPPOINMENT)?" active":"" ?>" onclick="location.href = '<?= $ruri ?>mtype/<?= Entity::STATUS_LAPPOINMENT ?>/etype/<?= Entity::TYPE_LEAD ?>'">Appointments<span>(@appointment_count@)</span></li>
        <li class="tab<?= (@$_GET['mtype'] == Entity::STATUS_LEXPIRED)?" active":"" ?>" onclick="location.href = '<?= $ruri ?>mtype/<?= Entity::STATUS_LEXPIRED ?>/etype/<?= Entity::TYPE_LEAD ?>'">Expired Quotes<span>(@expired_count@)</span></li>
        <li class="tab<?= (@$_GET['mtype'] == Entity::STATUS_LDUPLICATE)?" active":"" ?>" onclick="location.href = '<?= $ruri ?>mtype/<?= Entity::STATUS_LDUPLICATE ?>/etype/<?= Entity::TYPE_LEAD ?>'">Possible Duplicate<span>(@duplicate_count@)</span></li>
        
        
		<li class="tab<?= (@$_GET['mtype'] == Entity::STATUS_ONHOLD)?" active":"" ?>" onclick="location.href = '<?= $ruri ?>mtype/<?= Entity::STATUS_ONHOLD ?>/etype/<?= Entity::TYPE_LEAD ?>'">Hold<span>(@onhold_count@)</span></li>
		<li class="tab<?= (@$_GET['mtype'] == Entity::STATUS_UNREADABLE)?" active":"" ?>" onclick="location.href = '<?= $ruri ?>mtype/<?= Entity::STATUS_UNREADABLE ?>/etype/<?= Entity::TYPE_LEAD ?>'">Unreadable<span>(@unreadable_count@)</span></li>
        <li class="tab<?= (@$_GET['mtype'] == Entity::STATUS_ARCHIVED)?" active":"" ?>" onclick="location.href = '<?= $ruri ?>mtype/<?= Entity::STATUS_ARCHIVED ?>/etype/<?= Entity::TYPE_LEAD ?>'">Cancelled<span>(@archived_count@)</span></li>
      
      
        
        <!--li class="tab<?= (@$_GET['leads'] == '')?" active":"" ?>" onclick="location.href = '<?= SITE_IN ?>application/leads/'">New Leads<span>(@active_count@)</span></li> 
        
        <li class="tab<?= (@$_GET['leads'] == 'quoted')?" active":"" ?>" onclick="location.href = '<?= SITE_IN ?>application/leads/quoted'">Today's Quotes<span>(@quoted_count@)</span></li>
        <li class="tab<?= (@$_GET['leads'] == 'follow')?" active":"" ?>" onclick="location.href = '<?= SITE_IN ?>application/leads/follow'">Followups<span>(@follow_count@)</span></li>
        <li class="tab<?= (@$_GET['leads'] == 'appointment')?" active":"" ?>" onclick="location.href = '<?= SITE_IN ?>application/leads/appointment'">Appointments<span>(@appointment_count@)</span></li>
        <li class="tab<?= (@$_GET['leads'] == 'expired')?" active":"" ?>" onclick="location.href = '<?= SITE_IN ?>application/leads/expired'">Expired Quotes<span>(@expired_count@)</span></li>
        <li class="tab<?= (@$_GET['leads'] == 'duplicate')?" active":"" ?>" onclick="location.href = '<?= SITE_IN ?>application/leads/duplicate'">Duplicate Leads<span>(@duplicate_count@)</span></li>
        
        
		<li class="tab<?= (@$_GET['leads'] == 'onhold')?" active":"" ?>" onclick="location.href = '<?= SITE_IN ?>application/leads/onhold'">Hold<span>(@onhold_count@)</span></li>
		<li class="tab<?= (@$_GET['leads'] == 'unreadable')?" active":"" ?>" onclick="location.href = '<?= SITE_IN ?>application/leads/unreadable'">Unreadable<span>(@unreadable_count@)</span></li>
        <li class="tab<?= (@$_GET['leads'] == 'archived')?" active":"" ?>" onclick="location.href = '<?= SITE_IN ?>application/leads/archived'">Cancelled<span>(@archived_count@)</span></li-->
		
        
   <?php }elseif($_GET['etype'] == 4  || ($_GET['lead_search_type'] == Entity::TYPE_CLEAD && !$_GET['etype'] )
				 ){?>     
   <li class="tab<?= (@$_GET['mtype'] == Entity::STATUS_CACTIVE) && !isset($_GET['tab'])?" active":"" ?>" onclick="location.href = '<?= $ruri ?>mtype/<?= Entity::STATUS_CACTIVE ?>/etype/<?= Entity::TYPE_CLEAD ?>'">Leads<span>(@active_count@)</span></li>
   <li class="tab<?= (@$_GET['mtype'] == Entity::STATUS_CASSIGNED)?" active":"" ?>" onclick="location.href = '<?= $ruri ?>mtype/<?= Entity::STATUS_CASSIGNED ?>/etype/<?= Entity::TYPE_CLEAD ?>'">Assigned Leads<span>(@assigned_count@)</span></li>
    <li class="tab<?= (@$_GET['mtype'] == Entity::STATUS_CPRIORITY)?" active":"" ?>" onclick="location.href = '<?= $ruri ?>mtype/<?= Entity::STATUS_CPRIORITY ?>/etype/<?= Entity::TYPE_CLEAD ?>'">Priority Leads<span>(@priority_count@)</span></li>
        <li class="tab<?= (@$_GET['mtype'] == Entity::STATUS_CQUOTED)?" active":"" ?>" onclick="location.href = '<?= $ruri ?>mtype/<?= Entity::STATUS_CQUOTED ?>/etype/<?= Entity::TYPE_CLEAD ?>'">Today's Quotes<span>(@cquoted_count@)</span></li>
        <li class="tab<?= (@$_GET['mtype'] == Entity::STATUS_CFOLLOWUP)?" active":"" ?>" onclick="location.href = '<?= $ruri ?>mtype/<?= Entity::STATUS_CFOLLOWUP ?>/etype/<?= Entity::TYPE_CLEAD ?>'">Followups<span>(@cfollow_count@)</span></li>
        <li class="tab<?= (@$_GET['mtype'] == Entity::STATUS_CAPPOINMENT)?" active":"" ?>" onclick="location.href = '<?= $ruri ?>mtype/<?= Entity::STATUS_CAPPOINMENT ?>/etype/<?= Entity::TYPE_CLEAD ?>'">Appointments<span>(@cappointment_count@)</span></li>
        <li class="tab<?= (@$_GET['mtype'] == Entity::STATUS_CEXPIRED)?" active":"" ?>" onclick="location.href = '<?= $ruri ?>mtype/<?= Entity::STATUS_CEXPIRED ?>/etype/<?= Entity::TYPE_CLEAD ?>'">Expired Quotes<span>(@cexpired_count@)</span></li>
        
		<li class="tab<?= (@$_GET['mtype'] == Entity::STATUS_CONHOLD)?" active":"" ?>" onclick="location.href = '<?= $ruri ?>mtype/<?= Entity::STATUS_CONHOLD ?>/etype/<?= Entity::TYPE_CLEAD ?>'">Hold<span>(@onhold_count@)</span></li>
		
         <li class="tab<?= (@$_GET['mtype'] == Entity::STATUS_CDEAD)?" active":"" ?>" onclick="location.href = '<?= $ruri ?>mtype/<?= Entity::STATUS_CDEAD ?>/etype/<?= Entity::TYPE_CLEAD ?>'">Do Not Call<span>(@dead_count@)</span></li>
       
        <li class="last tab<?= (@$_GET['mtype'] == Entity::STATUS_CARCHIVED)?" active":"" ?>" onclick="location.href = '<?= $ruri ?>mtype/<?= Entity::STATUS_CARCHIVED ?>/etype/<?= Entity::TYPE_CLEAD ?>'">Cancelled<span>(@archived_count@)</span></li>


<!--li class="tab<?= (@$_GET['leads'] == 'created')?" active":"" ?>" onclick="location.href = '<?= SITE_IN ?>application/leads/created'">New Leads<span>(@active_count@)</span></li>
   <li class="tab<?= (@$_GET['leads'] == 'cassigned')?" active":"" ?>" onclick="location.href = '<?= SITE_IN ?>application/leads/cassigned'">Assigned Leads<span>(@assigned_count@)</span></li>
    <li class="tab<?= (@$_GET['leads'] == 'cpriority')?" active":"" ?>" onclick="location.href = '<?= SITE_IN ?>application/leads/cpriority'">Priority Leads<span>(@priority_count@)</span></li>
        <li class="tab<?= (@$_GET['leads'] == 'cquoted')?" active":"" ?>" onclick="location.href = '<?= SITE_IN ?>application/leads/cquoted'">Today's Quotes<span>(@cquoted_count@)</span></li>
        <li class="tab<?= (@$_GET['leads'] == 'cfollow')?" active":"" ?>" onclick="location.href = '<?= SITE_IN ?>application/leads/cfollow'">Followups<span>(@cfollow_count@)</span></li>
        <li class="tab<?= (@$_GET['leads'] == 'cappointment')?" active":"" ?>" onclick="location.href = '<?= SITE_IN ?>application/leads/cappointment'">Appointments<span>(@cappointment_count@)</span></li>
        <li class="tab<?= (@$_GET['leads'] == 'cexpired')?" active":"" ?>" onclick="location.href = '<?= SITE_IN ?>application/leads/cexpired'">Expired Quotes<span>(@cexpired_count@)</span></li>
        
       
		<li class="tab<?= (@$_GET['leads'] == 'conhold')?" active":"" ?>" onclick="location.href = '<?= SITE_IN ?>application/leads/conhold'">Hold<span>(@onhold_count@)</span></li>
		
        <li class="tab<?= (@$_GET['leads'] == 'cdead')?" active":"" ?>" onclick="location.href = '<?= SITE_IN ?>application/leads/cdead'">Do Not Call<span>(@dead_count@)</span></li>
        <li class="tab<?= (@$_GET['leads'] == 'converted')?" active":"" ?>" onclick="location.href = '<?= SITE_IN ?>application/leads/converted'">Conveted To<span>(@converted_count@)</span></li>
        <li class="last tab<?= (@$_GET['leads'] == 'carchived')?" active":"" ?>" onclick="location.href = '<?= SITE_IN ?>application/leads/carchived'">Cancelled<span>(@archived_count@)</span></li-->		
   <?php }?>
   
   <?php if (isset($_GET['search_string']) ) : ?>
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
    
	
</div>


      <?php }else{?>

<div class="tab-panel-container">
	<ul class="tab-panel">
		<li class="tab first<?= (@$_GET['leads'] == '' || 
		  $_GET['leads'] == 'assigned' ||
		  $_GET['leads'] == 'quoted' ||
		  $_GET['leads'] == 'follow' ||
		  $_GET['leads'] == 'expired' ||
		  $_GET['leads'] == 'duplicate' ||
		  $_GET['leads'] == 'appointment' ||
		  $_GET['leads'] == 'onhold' ||
		  $_GET['leads'] == 'archived' || 
		  $_GET['leads'] == 'unreadable')?" active":"" ?>"onclick="location.href = '<?= SITE_IN ?>application/leads/'">Imported Leads<span></span></li>
        <!--li class="tab<?= (@$_GET['leads'] == 'created')?" active":"" ?>" onclick="location.href = '<?= SITE_IN ?>application/leads/created'">Created Leads<span>(@created_count@)</span></li-->
        <?php if($_SESSION['parent_id']==1){?>
        
         <li class="tab<?= (@$_GET['leads'] == 'created' ||
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
		  $_GET['leads'] == 'quoted' ||
		  $_GET['leads'] == 'follow' ||
		  $_GET['leads'] == 'expired' ||
		  $_GET['leads'] == 'duplicate' ||
		  $_GET['leads'] == 'appointment' ||
		  $_GET['leads'] == 'onhold' ||
		  $_GET['leads'] == 'unreadable' ||
		  $_GET['leads'] == 'archived'
		      // @quoted_count@ @follow_count@ @expired_count@ @duplicate_count@ @appointment_count@
		  ){?>     
         <li class="tab<?= (@$_GET['leads'] == '')?" active":"" ?>" onclick="location.href = '<?= SITE_IN ?>application/leads/'">Quote Requests<span>(@active_count@)</span></li> 
        <!--li class="tab<?= (@$_GET['leads'] == 'assigned')?" active":"" ?>" onclick="location.href = '<?= SITE_IN ?>application/leads/assigned'">Assigned Leads<span>(@assigned_count@)</span></li-->
        <li class="tab<?= (@$_GET['leads'] == 'quoted')?" active":"" ?>" onclick="location.href = '<?= SITE_IN ?>application/leads/quoted'">Today's Quotes<span>(@quoted_count@)</span></li>
        <li class="tab<?= (@$_GET['leads'] == 'follow')?" active":"" ?>" onclick="location.href = '<?= SITE_IN ?>application/leads/follow'">Follow-ups<span>(@follow_count@)</span></li>
        <li class="tab<?= (@$_GET['leads'] == 'appointment')?" active":"" ?>" onclick="location.href = '<?= SITE_IN ?>application/leads/appointment'">Appointments<span>(@appointment_count@)</span></li>
        <li class="tab<?= (@$_GET['leads'] == 'expired')?" active":"" ?>" onclick="location.href = '<?= SITE_IN ?>application/leads/expired'">Expired Quotes<span>(@expired_count@)</span></li>
        <li class="tab<?= (@$_GET['leads'] == 'duplicate')?" active":"" ?>" onclick="location.href = '<?= SITE_IN ?>application/leads/duplicate'">Possible Duplicate<span>(@duplicate_count@)</span></li>
        
		<li class="tab<?= (@$_GET['leads'] == 'onhold')?" active":"" ?>" onclick="location.href = '<?= SITE_IN ?>application/leads/onhold'">Hold<span>(@onhold_count@)</span></li>
		<li class="tab<?= (@$_GET['leads'] == 'unreadable')?" active":"" ?>" onclick="location.href = '<?= SITE_IN ?>application/leads/unreadable'">Unreadable<span>(@unreadable_count@)</span></li>
        <li class="tab<?= (@$_GET['leads'] == 'archived')?" active":"" ?>" onclick="location.href = '<?= SITE_IN ?>application/leads/archived'">Cancelled<span>(@archived_count@)</span></li>
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
				$_GET['leads'] == 'cdead' 
				 ){?>     
   <li class="tab<?= (@$_GET['leads'] == 'created')?" active":"" ?>" onclick="location.href = '<?= SITE_IN ?>application/leads/created'">Leads<span>(@active_count@)</span></li>
   <li class="tab<?= (@$_GET['leads'] == 'cassigned')?" active":"" ?>" onclick="location.href = '<?= SITE_IN ?>application/leads/cassigned'">Assigned to<span>(@assigned_count@)</span></li>
  <li class="tab<?= (@$_GET['leads'] == 'cpriority')?" active":"" ?>" onclick="location.href = '<?= SITE_IN ?>application/leads/cpriority'">Priority Leads<span>(@priority_count@)</span></li>
        <li class="tab<?= (@$_GET['leads'] == 'cquoted')?" active":"" ?>" onclick="location.href = '<?= SITE_IN ?>application/leads/cquoted'">Today's Quotes<span>(@cquoted_count@)</span></li>
        <li class="tab<?= (@$_GET['leads'] == 'cfollow')?" active":"" ?>" onclick="location.href = '<?= SITE_IN ?>application/leads/cfollow'">Follow-ups<span>(@cfollow_count@)</span></li>
        <li class="tab<?= (@$_GET['leads'] == 'cappointment')?" active":"" ?>" onclick="location.href = '<?= SITE_IN ?>application/leads/cappointment'">Appointments<span>(@cappointment_count@)</span></li>
        <li class="tab<?= (@$_GET['leads'] == 'cexpired')?" active":"" ?>" onclick="location.href = '<?= SITE_IN ?>application/leads/cexpired'">Expired Quotes<span>(@cexpired_count@)</span></li>
        <li class="tab<?= (@$_GET['leads'] == 'conhold')?" active":"" ?>" onclick="location.href = '<?= SITE_IN ?>application/leads/conhold'">Hold<span>(@onhold_count@)</span></li>
        <li class="tab<?= (@$_GET['leads'] == 'cdead')?" active":"" ?>" onclick="location.href = '<?= SITE_IN ?>application/leads/cdead'">Do Not Call<span>(@dead_count@)</span></li>
        <li class="tab<?= (@$_GET['leads'] == 'converted')?" active":"" ?>" onclick="location.href = '<?= SITE_IN ?>application/leads/converted'">Converted<span>(@converted_count@)</span></li>
        <li class="last tab<?= (@$_GET['leads'] == 'carchived')?" active":"" ?>" onclick="location.href = '<?= SITE_IN ?>application/leads/carchived'">Cancelled<span>(@archived_count@)</span></li>
		
   <?php }?>
	</ul>
    
	
</div>

<?php endif; ?>
<?php }?>



