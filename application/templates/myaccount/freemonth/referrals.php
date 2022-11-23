<? include(TPL_PATH."myaccount/menu.php");?>

<div align="left" style="clear:both; padding-bottom:20px;">
	<img style="vertical-align:middle;" src="<?=SITE_IN?>images/icons/back.png" alt="Back" width="16" height="16" /> <a href="<?=getLink("freemonth")?>">&nbsp;Refer a Friend.</a>
</div>


<div class="kt-portlet">
	
	<div class="kt-portlet__head">
		<div class="kt-portlet__head-label">
			<h4 style="color:#3B67A6">Signed-Up Referrals</h4>
		</div>
	</div>
	
	<div class="kt-portlet__body">
		<div class="kt-section__info m_btm_10">Below is a list of Signed-Up Referrals. Click the ID of any referral to view details.</div>
		
		<table class="table table-bordered">
			<thead>
				<tr>
					<th><?=$this->order->getTitle("companyname", "Name")?></th>
					<th><?=$this->order->getTitle("reg_date", "Registered")?></th>
					<th>Payments</th>
					<th>Actions</th>
				</tr>
			</thead>
			<tbody>
			<? if (count($this->referrals)>0){?>
				<? foreach ($this->referrals as $i => $referral) { ?>
				<tr id="row-<?=$referral->id?>">
					<td><?=htmlspecialchars($referral->getCompanyProfile()->companyname);?></td>
					<td><?=htmlspecialchars($referral->getRegDate());?></td>
					<td><?=($referral->is_freemonth_payed == 1?"Referral payment has been received":"Referral Terms is not satisfied");?></td>
					<td><?=infoIcon(getLink("ratings", "company", "id", $referral->id))?></td>
				</tr>
				<? } ?>
			<?}else{?>
				<tr id="row-">
					<td align="center" colspan="4">No records found.</td>
				</tr>
			<? } ?>
			</tbody>
		</table>
		
	</div>
	
</div>

<br /><br />

@pager@