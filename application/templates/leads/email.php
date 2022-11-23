<style type="text/css">
div#accordionExample1
{
	border: 1px solid #959cbc14;
}
h3.details
{
    padding:22px 0 0;
    width:100%;
    font-size:20px;
}
</style>	
<?php include('lead_menu_imported.php');  ?>
</div>
  
<script src="<?php echo SITE_IN; ?>styles/new/assets/vendors/general/bootstrap/dist/js/bootstrap.min.js" type="text/javascript"></script>

<?php $email = $this->entity->getEmail() ?>

<div style="margin-top:-55px;margin-bottom:15px;">
	<h3 class="details">Lead #<?= $this->entity->getNumber() ?> Original E-Mail</h3>
</div>

<div class="row">

	<div class="col-12">
		<div class="card1">
			<div class="card-1body">
				<strong>Date received :-</strong>
				<span>
					<?= $email->getReceived() ?>							
				</span><br/>
				<strong>To address(es):</strong>
				<span><?= htmlspecialchars($email->to_address) ?></span><br/>
				<strong>From address(es): </strong>
				<span><?= htmlspecialchars($email->from_address) ?></span></br>
				<strong>Subject: </strong>
				<span><?= htmlspecialchars($email->subject) ?></span><br/>
				<strong>Body: </strong>
			</div>
		</div>
	</div>

	<div class="col-12">
		<div class="card">
			<div id="Date_received" class="collapse show " aria-labelledby="headingOne" data-parent="#accordionExample1">
				<div class="card-body">
				   <?= nl2br(htmlspecialchars($email->body)) ?>
				</div>
			</div>
		</div>
	</div>
	
</div>