<br />
<div id="breadCrumb" class="breadCrumb module">
	<ul>
		<li><a href="<?echo getLink()?>">Home</a></li>
		<?foreach($this->crumbs as $url => $name):?>
		<?if ($url != ""){?>
		<li><a href="<?echo $url?>"><?echo htmlspecialchars(strip_tags($name))?></a></li>
		<?}else{?>
		<li><?echo htmlspecialchars(strip_tags($name))?></li>
		<?}?>
		<?endforeach;?>
	</ul>
</div>
<div style="clear:both"></div>