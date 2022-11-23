<?php
/**
 * @var array $weekData
 * @var array $dashboard
 */
?>
<script type="text/javascript">
	$(document).ready(function() {
		$("#week").change(function() {
			$("#dashboard_submit").click();
		});
	});
</script>
<div style="float: right">
<form name="dashboard_form" id="dashboard_form" method="post" action="<?=getLink()?>">
@week@
<input type="submit" style="display:none" id="dashboard_submit"/>
</form>
</div>
<br style="clear:both;"/><br/>
<table cellpadding="0" cellspacing="0" width="100%" class="grid dashboard-table">
	<tr class="grid-head">
		<th class="grid-head-left">Product Name</th>
		<th colspan="2">Sunday<div class="small-date"><?=$this->weekData['1']['date']?></div></th>
		<th colspan="2">Monday<div class="small-date"><?=$this->weekData['2']['date']?></div></th>
		<th colspan="2">Tuesday<div class="small-date"><?=$this->weekData['3']['date']?></div></th>
		<th colspan="2">Wednesday<div class="small-date"><?=$this->weekData['4']['date']?></div></th>
		<th colspan="2">Thursday<div class="small-date"><?=$this->weekData['5']['date']?></div></th>
		<th colspan="2">Friday<div class="small-date"><?=$this->weekData['6']['date']?></div></th>
		<th colspan="2">Saturday<div class="small-date"><?=$this->weekData['7']['date']?></div></th>
		<th colspan="2">Weekly<br/>Summary</th>
		<th class="grid-head-right" colspan="2">Monthly<br/>Summary</th>
	</tr>
	<?php
	$i = 0;
	$totals = array(
		'2' => array('cnt' => 0, 'amount' => 0),
		'3' => array('cnt' => 0, 'amount' => 0),
		'4' => array('cnt' => 0, 'amount' => 0),
		'5' => array('cnt' => 0, 'amount' => 0),
		'6' => array('cnt' => 0, 'amount' => 0),
		'7' => array('cnt' => 0, 'amount' => 0),
		'1' => array('cnt' => 0, 'amount' => 0),
		'week' => array('cnt' => 0, 'amount' => 0),
		'month' => array('cnt' => 0, 'amount' => 0),
	);
	foreach ($this->dashboard as $pn => $pd) {
	$i++;
	foreach($pd as $key => $val) {
		$totals[$key]['cnt'] += $val['cnt'];
		$totals[$key]['amount'] += $val['amount'];
	}
	?>
	<tr class="grid-body<?=($i==1)?' first-row':''?>">
		<td class="grid-body-left"><?=$pn?></td>
		<td class="grid-body-left even"><?=$pd['1']['cnt']?></td>
		<td class="grid-body-left even"><?='$'.$pd['1']['amount']?></td>
		<td class="grid-body-left"><?=$pd['2']['cnt']?></td>
		<td class="grid-body-left"><?='$'.$pd['2']['amount']?></td>
		<td class="grid-body-left even"><?=$pd['3']['cnt']?></td>
		<td class="grid-body-left even"><?='$'.$pd['3']['amount']?></td>
		<td class="grid-body-left"><?=$pd['4']['cnt']?></td>
		<td class="grid-body-left"><?='$'.$pd['4']['amount']?></td>
		<td class="grid-body-left even"><?=$pd['5']['cnt']?></td>
		<td class="grid-body-left even"><?='$'.$pd['5']['amount']?></td>
		<td class="grid-body-left"><?=$pd['6']['cnt']?></td>
		<td class="grid-body-left"><?='$'.$pd['6']['amount']?></td>
		<td class="grid-body-left even"><?=$pd['7']['cnt']?></td>
		<td class="grid-body-left even"><?='$'.$pd['7']['amount']?></td>
		<td class="grid-body-left"><?=$pd['week']['cnt']?></td>
		<td class="grid-body-left"><?='$'.$pd['week']['amount']?></td>
		<td class="grid-body-left highlight"><?=$pd['month']['cnt']?></td>
		<td class="grid-body-left grid-body-right highlight"><?='$'.$pd['month']['amount']?></td>
	</tr>
	<?php } ?>
	<tr class="grid-body dashboard-total">
		<td class="grid-body-left"><strong>TOTAL</strong></td>
		<td colspan="2" class="grid-body-left even"><?='$'.number_format($totals['1']['amount'], 2)?></td>
		<td colspan="2" class="grid-body-left"><?='$'.number_format($totals['2']['amount'], 2)?></td>
		<td colspan="2" class="grid-body-left even"><?='$'.number_format($totals['3']['amount'], 2)?></td>
		<td colspan="2" class="grid-body-left"><?='$'.number_format($totals['4']['amount'], 2)?></td>
		<td colspan="2" class="grid-body-left even"><?='$'.number_format($totals['5']['amount'], 2)?></td>
		<td colspan="2" class="grid-body-left"><?='$'.number_format($totals['6']['amount'], 2)?></td>
		<td colspan="2" class="grid-body-left even"><?='$'.number_format($totals['7']['amount'], 2)?></td>
		<td colspan="2" class="grid-body-left"><?='$'.number_format($totals['week']['amount'], 2)?></td>
		<td colspan="2" class="grid-body-left grid-body-right highlight"><?='$'.number_format($totals['month']['amount'], 2)?></td>
	</tr>
</table>
