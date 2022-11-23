@flash_message@
	<?php foreach ($this->data as $i => $data): ?>
			<h3><a href="<?=getLink("news", "show", "id", $data['id'])?>"> <?=htmlspecialchars($data['title']);?></a></h3>
			<strong><?=$data['news_date_show'];?></strong><br />
			<?=$data['content'];?>
			<br />
			<br /><br />
	<?php endforeach; ?>
	<br />
	@pager@
	TEst
<br clear="all" />