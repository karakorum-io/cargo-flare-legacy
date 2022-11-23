<script type="text/javascript">
$(document).ready(function() {
	$('.show-answer').click(function() {
		var answer = $(this).parent().find('.answer');

		if (!answer.hasClass('rolled-down'))
			$('.rolled-down').removeClass('rolled-down').slideUp(100);
		answer.toggleClass('rolled-down').slideToggle(300);
		return false;
	});
});
</script>
<div class=" col-md-12">
	<?php foreach ($this->data as $i => $data): ?>
	    <div class="question-block">
				<p class="question"><?=htmlspecialchars($data['question']);?></p>
				<div class="answer-block">
					<a href="#" class="show-answer">See answer</a>
					<div class="answer"><p><?=$data['answer'];?></p></div>
				</div>
			</div>
	<?php endforeach; ?>
	<br />
</div>
<div style="clear:both;"></div>