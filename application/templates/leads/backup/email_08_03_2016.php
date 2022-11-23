<div style="padding-top:15px;">
<?php include('lead_menu.php');  ?>
</div>
<?php $email = $this->entity->getEmail() ?>
<br/>
<h3>Lead #<?= $this->entity->getNumber() ?> Original E-Mail</h3>
<strong>Date received: </strong><?= $email->getReceived() ?><br/>
<strong>To address(es): </strong><?= htmlspecialchars($email->to_address) ?><br/>
<strong>From address(es): </strong><?= htmlspecialchars($email->from_address) ?><br/>
<strong>Subject: </strong><?= htmlspecialchars($email->subject) ?><br/>
<strong>Body: </strong><br/>
<div class="email-body-raw">
<pre><?= nl2br(htmlspecialchars($email->body)) ?></pre>
</div>