<? include(TPL_PATH . "myaccount/menu.php"); ?>

<div class="kt-section__info m_btm_10" style="padding-bottom:10px;">These are the ratings you have received and given. To rate another company go to <a href="<?= getLink("ratings", "search") ?>"><img src="<?= SITE_IN ?>images/icons/search.png" width="16" height="16" alt="Search" style="vertical-align:middle" /> Search/Rate Companies</a>.</div>


<div class="row">
	<div class="col-6">
	
		<div class="kt-portlet">
		
			<div class="kt-portlet__head">
				<div class="kt-portlet__head-label">
					<?= formBoxStart("My Rating") ?>
				</div>
			</div>
	
			<div class="kt-portlet__body">
				
				<div class="row">
				
					<div class="col-4">
						<div class="form-group select_opt_new_info">
							<label>Ratings Score:</label>
							<span style="color:black;font-size:16px;">@rating_score@</span>
						</div>
					</div>
					
					<div class="col-4">
						<div class="form-group select_opt_new_info">
							<label>Ratings Received:</label>
							<span style="color:black;font-size:16px;">@rating_received@</span>
						</div>
					</div>
					
					<div class="col-4">
						<div class="form-group select_opt_new_info">
							<label>Member Since:</label>
							<span style="color:black;font-size:16px;">@member_since@</span>
						</div>
					</div>
					
				</div>
				
				<div style="padding:10px; background-color:#fffbd8; border:#000 1px dashed">
					<strong>Ratings Score:</strong> <span style="color:green">Positive</span> ratings receive ONE point. <span style="color:#0052a4">Neutral</span> ratings receive ONE-HALF point. <span style="color:red">Negative</span> ratings receive ZERO points. Points are combined and computed into an overall Ratings Score PERCENTAGE.
				</div>
				
			</div>
			
			<?= formBoxEnd() ?>
			
		</div>
		
	</div>
	
	<div class="col-6">
		<div class="kt-portlet">
		
			<div class="kt-portlet__head">
				<div class="kt-portlet__head-label">
					<?= formBoxStart("Ratings History") ?>
				</div>
			</div>
	
			<div class="kt-portlet__body">
				<table class="table table-bordered">
					<thead>
						<tr>
							<th>&nbsp;</th>
							<th class="text-center">Past Month</th>
							<th class="text-center">Past 6 Months</th>
							<th class="text-center">All Time</th>
						</tr>
						<tr>
							<th class="text-center">Ratings Score</th>
							<th class="text-center">@rating_score1@</th>
							<th class="text-center">@rating_score6@</th>
							<th class="text-center">@rating_score@</th>
						</tr>
					</thead>
					<tr>
						<td class="text-center">
							<img src="<?= SITE_IN ?>images/icons/ratepositive.png" alt="Positive" width="16" height="16" style="vertical-align:middle;" />
							<strong style="color:green;">Positive</strong>
						</td>
						<td align="center">@rating_score_p1@</td>
						<td align="center">@rating_score_p6@</td>
						<td align="center">@rating_score_p@</td>
					</tr>
					<tr>
						<td class="text-center">
							<img src="<?= SITE_IN ?>images/icons/rateneutral.png" alt="Neutral" width="16" height="16" style="vertical-align:middle;" />
							<strong style="color:#0052a4;">Neutral</strong>
						</td>
						<td align="center">@rating_score_t1@</td>
						<td align="center">@rating_score_t6@</td>
						<td align="center">@rating_score_t@</td>
					</tr>
					<tr>
						<td class="text-center">
							<img src="<?= SITE_IN ?>images/icons/ratenegative.png" alt="Negative" width="16" height="16" style="vertical-align:middle;" />
							<strong style="color:red;">Negative</strong>
						</td>
						<td align="center">@rating_score_n1@</td>
						<td align="center">@rating_score_n6@</td>
						<td align="center">@rating_score_n@</td>
					</tr>
				</table>
			</div>
			
			<?= formBoxEnd() ?>
		</div>
	</div>
	
</div>


<div class="kt-portlet">

	<div class="kt-portlet__body">
		
		<div class="tab-panel-container4">
			<ul class="tab-panel">
				<li class="tab first<?= (!isset($_GET['gave']) ? " active" : ""); ?>"><a href="<?= getLink("ratings"); ?>">Ratings Received</a></li>
				<li class="tab <?= (isset($_GET['gave']) ? " active" : ""); ?>"><a href="<?= getLink("ratings", "gave", "rate"); ?>">Ratings I Gave to Others</a></li>
			</ul>
		</div>

		<? if (isset($_GET['gave'])) { ?>
			<table class="table table-bordered">
				<thead>
					<tr>
						<th><?= $this->order->getTitle("added", "Date") ?></th>
						<th><?= $this->order->getTitle("type", "Rating") ?></th>
						<th><?= $this->order->getTitle("status", "Status") ?></th>
						<th><?= $this->order->getTitle("to_id", "Given to") ?></th>
						<th>Actions</th>
					</tr>
				</thead>
				<? if (count($this->data) > 0) { ?>
					<? foreach ($this->data as $i => $data) { ?>
						<tr id="row-<?= $data['id'] ?>">
							<td align="center" valign="top" class="grid-body-left"><?= $data['added']; ?></td>
							<td align="center">
								<img src="<?= SITE_IN ?>images/icons/<?= $data['type'] ?>.png" width="16" height="16" alt="Rating" />
							</td>
							<td align="center" valign="top"><?= $data['status'] ?></td>
							<td>
								<a href="<?= getLink("ratings", "company", "id", $data['to_id']) ?>"><?= $data['to_name'] ?></a> <em><?= $data['to_address'] ?></em><br />
								Ratings Score: <span style="color:black;"><?= number_format($data['ratings_score'], 2, ".", ",") ?>%</span>, Ratings Received: <span style="color:black;"><?= $data['ratings_received'] ?></span>
							</td>
							<td align="center" class="grid-body-right">&nbsp; <a href="<?= getLink("ratings", "company", "id", $data['to_id']) ?>">Edit</a></td>
						</tr>
					<? } ?>
				<? } else { ?>
					<tr id="row-">
						<td class="grid-body-left">&nbsp;</td>
						<td align="center" colspan="3">No records found.</td>
						<td class="grid-body-right">&nbsp;</td>
					</tr>
				<? } ?>
			</table>
			<? } else { ?>
			<table class="table table-bordered">
				<thead>
					<tr>
						<th><?= $this->order->getTitle("added", "Date") ?></th>
						<th><?= $this->order->getTitle("type", "Rating") ?></th>
						<th><?= $this->order->getTitle("status", "Status") ?></th>
						<th><?= $this->order->getTitle("from_id", "From") ?></th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>
				<? if (count($this->data) > 0) { ?>
					<? foreach ($this->data as $i => $data) { ?>
						<tr id="row-<?= $data['id'] ?>">
							<td align="center" valign="top" class="grid-body-left"><?= $data['added']; ?></td>
							<td align="center"><img src="<?= SITE_IN ?>images/icons/<?= $data['type'] ?>.png" width="16" height="16" alt="Rating" /></td>
							<td align="center" valign="top"><?= $data['status'] ?></td>
							<td>
								<a href="<?= getLink("ratings", "company", "id", $data['from_id']) ?>"><?= $data['from_name'] ?></a> <em><?= $data['from_address'] ?></em><br />
								Ratings Score: <span style="color:black;"><?= number_format($data['ratings_score'], 2, ".", ",") ?>%</span>, Ratings Received: <span style="color:black;"><?= $data['ratings_received'] ?></span>
							</td>
							<td class="grid-body-right">&nbsp;</td>
						</tr>
					<? } ?>
				<? } else { ?>
					<tr>
						<td class="grid-body-left">&nbsp;</td>
						<td align="center" colspan="3">No records found.</td>
						<td class="grid-body-right">&nbsp;</td>
					</tr>
				<? } ?>
				</tbody>
			</table>
		<? } ?>
		
	</div>
	
</div>

<div class="col-12">
	@pager@
</div>