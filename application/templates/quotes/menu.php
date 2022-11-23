<?php /* ?>
<div class="tab-panel-container">
	<ul class="tab-panel">
        
		<li class="last tab<?= (@$_GET['quotes'] == 'followup')?" active":"" ?>" onclick="location.href = '<?= SITE_IN ?>application/quotes/followup'">Follow-up<span>(@followup_count@)</span></li>
		<li class="tab first<?= (@$_GET['quotes'] == '')?" active":"" ?>"onclick="location.href = '<?= SITE_IN ?>application/quotes/'">Quotes<span>(@active_count@)</span></li>
		
		
		<li class="tab<?= (@$_GET['quotes'] == 'onhold')?" active":"" ?>" onclick="location.href = '<?= SITE_IN ?>application/quotes/onhold'">Hold<span>(@onhold_count@)</span></li>
		
		
		
		<li class="last tab<?= (@$_GET['quotes'] == 'archived')?" active":"" ?>" onclick="location.href = '<?= SITE_IN ?>application/quotes/archived'">Cancelled<span>(@archived_count@)</span></li>
		
		
		<?php if (isset($_GET['search_string'])) : ?>
		<li class="last tab active">Search Results<span>(@search_count@)</span></li>
		<?php endif; ?>
		
	</ul>
	<div style="float:right;">
		<table cellspacing="5" cellpadding="0" border="0">
			<tr>
				<?php $entity_ids = array(); foreach($this->entities as $entity) { $entity_ids[] = $entity->id; }?>
				<td><?=functionButton('Print', 'printQuotes(window.open(\'\', \'quotes\', \'height=400,width=600\'), \''.implode(",",$entity_ids).'\')');?></td>
			</tr>
		</table>
	</div>
</div> <?php */ ?>



<div class="alert alert-light alert-elevate" role="alert">
		<div class="col-12 col-sm-9" style="padding-left:0;">
			<ul class="nav nav-tabs nav-tabs-line nav-tabs-line-3x nav-tabs-line-success pull-left" role="tablist" style="margin-bottom: 0px">
				<li class="nav-item custom_set">
					<a class="nav-link <?= (@$_GET['quotes'] == 'followup')?" active":"" ?>" href="<?= SITE_IN ?>application/quotes/followup">Follow-up (@followup_count@)</a>
				</li>
				
				<li class="nav-item">
					<a class="nav-link <?= (@$_GET['quotes'] == '')?" active":"" ?>" href="<?= SITE_IN ?>application/quotes/">Today's Quotes (@active_count@)</a>
				</li>

				<li class="nav-item">
					<a class="nav-link <?= (@$_GET['quotes'] == 'expired')?" active":"" ?>" href="<?= SITE_IN ?>application/quotes/expired">Expired Quotes (@expired_count@)</a>
				</li>

				<li class="nav-item">
					<a class="nav-link <?= (@$_GET['quotes'] == 'duplicates')?" active":"" ?>" href="<?= SITE_IN ?>application/quotes/duplicates">Possible Duplicates (@cduplicate_count@)</a>
				</li>

				<li class="nav-item">
					<a class="nav-link <?= (@$_GET['quotes'] == 'unreadables')?" active":"" ?>" href="<?= SITE_IN ?>application/quotes/unreadables">Unreadables (@unreadables@)</a>
				</li>
				
				<li class="nav-item">
					<a class="nav-link <?= (@$_GET['quotes'] == 'onhold')?" active":"" ?>" href="<?= SITE_IN ?>application/quotes/onhold">Hold (@onhold_count@) </a>
				</li>
				
				<li class="nav-item">
					<a class="nav-link <?= (@$_GET['quotes'] == 'archived')?" active":"" ?>" href="<?= SITE_IN ?>application/quotes/archived">Cancelled (@archived_count@)</a>
				</li>
	
				<?php if (isset($_GET['search_string'])) : ?>
					<li class="nav-item active">Search Results<span>(@search_count@)</span></li>
				<?php endif; ?>										
			</ul>
		</div>
		<div class="col-12 col-sm-3 text-right" style="padding-right:0;">
			<div style="float:right;">
				<table cellspacing="5" cellpadding="0" border="0">
					<tr>
						<?php $entity_ids = array(); foreach($this->entities as $entity) { $entity_ids[] = $entity->id; }?>
						<td><?=functionButton('Print', 'printQuotes(window.open(\'\', \'quotes\', \'height=400,width=600\'), \''.implode(",",$entity_ids).'\')');?></td>
					</tr>
				</table>
			</div>
		</div>
	</div>