	<div class="kt-subheader my-3" style="background-color: #ffffff !important;border:1px solid #e5e5e5;padding: 0px !important;">
		<div class="kt-subheader__main">
			
			<?php/* <h3 class="kt-subheader__title">
				<?foreach($this->crumbs as $url => $name):?>
				<?if ($url == ""){?>
					<?echo htmlspecialchars(strip_tags($name))?>
				<?}?>
				<?endforeach;?>
			</h3> 
			
			<span class="kt-subheader__separator kt-hidden"></span>
			<div class="kt-subheader__breadcrumbs">
				<span class="kt-subheader__breadcrumbs-separator"></span>
				<a href="<?echo getLink()?>" class="kt-subheader__breadcrumbs-link"">
					Dashboard </a>
				<?foreach($this->crumbs as $url => $name):?>
					<?if ($url != ""){?>
						<span class="kt-subheader__breadcrumbs-separator"></span>
						<a  class="kt-subheader__breadcrumbs-link" href="<?echo $url?>"><?echo htmlspecialchars(strip_tags($name))?></a>
					<?}?>
				<?endforeach;?>
			</div>*/?>
			
			<ul class="breadcrumb_main">
				<li><a href="<?echo getLink()?>"><i class="fa fa-home"></i></a><i class="right_arrow_info"></i></li>
				
				
				<?foreach($this->crumbs as $url => $name):?>
					<?if ($url != ""){?>
						<li><a href="<?echo $url?>"><?echo htmlspecialchars(strip_tags($name))?></a><i class="right_arrow_info"></i></li>
					<?}?>
				<?endforeach;?>
				
				<li>
					<a href="#">						
						<?foreach($this->crumbs as $url => $name):?>
						<?if ($url == ""){?>
							<?echo htmlspecialchars(strip_tags($name))?>
						<?}?>
						<?endforeach;?>
					</a>
				</li>
			</ul>
			
		</div>
	</div>

	

		
	

