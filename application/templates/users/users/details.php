<? include(TPL_PATH."users/menu_details.php"); ?>

   


  <div class="row">

  	<div class="alert alert-light alert-elevate w-100  ">

  	<div class="col-8">
  		 <?=formBoxStart("Details: <span class='kt-font-boldest'>@contactname@</span>")?>
  		<div class="row">
  			<div class="col-6">
				<table class="table-bordered table">
				<tr>
				<td>User ID:</td>
				<td><strong>@id@</strong></td>
				</tr>
				<!--tr>
				<td>Username:</td>
				<td><strong>@username@</strong></td>
				</tr-->
				<tr>
				<td>Name:</td>
				<td><strong class="kt-link kt-link--state kt-link--success">@contactname@</strong></td>
				</tr>
				<tr>
				<td>Phone:</td>
				<td><strong class="kt-link--primary">@phone@</strong></td>
				</tr>
				<tr>
				<td>E-mail:</td>
				<td class="kt-link--primary"> <strong>@email@</strong></td>
				</tr>
				<tr>
				<td>Lead Multiple:</td>
				<td><strong>@lead_multiple@</strong></td>
				</tr>
				<tr>
				<td>Status:</td>
				<td class="kt-link--primary"><strong>@status@</strong></td>
				</tr>
				<tr>
				<td>Reg. date:</td>
				<td class="kt-link--warning"><strong>@reg_date@</strong></td>
				</tr>
			   </table>
				<?=formBoxEnd()?>
				 <?=backButton(getLink("users"))?>

  			</div>

  			<div class="col-6">
			<div>
			<?=formBoxStart("Login restrictions")?>
				<table class="table table-bordered">
				<tr>
				<td nowrap="nowrap" valign="top">Days allowed:</td>
				<td><strong>@days_allowed@</strong></td>
				</tr>
				<tr>
				<td nowrap="nowrap">Time allowed:</td>
				<td><strong>@time_allowed@</strong></td>
				</tr>
				<tr>
				<td colspan="2">&bull; <a class="kt-font-info"  href="<?=getLink("users", "restrictions", "id", (int)get_var("id"))?>">Modify login restrictions</a></td>
				</tr>
				</table>
			<?=formBoxEnd()?>
			</div>

  			</div>
  		</div>
  	</div>
  	<div class="col-4">

  		<div style="">
    <?=formBoxStart("Recent logins")?>
    	<table class="table-bordered table" >
		    <tr >
		        <td class="grid-head-left">Time</td>
		        <td class="grid-head-right">IP</td>
		    </tr>
		    <? if (count($this->logins)>0){?>
			    <? foreach ($this->logins as $i => $logins) { ?>
			    <tr class="grid-body<?=($i == 0 ? " " : "")?>" id="row-<?=$logins['id']?>">
			        <td class="grid-body-left"><?=$logins["logintime"]?></td>
			        <td class="kt-font-info"><?=$logins["ip"]?></td>
			    </tr>
			    <? } ?>
		    <? }else{ ?>
		    	<tr class="grid-body " id="row-1">
			        <td class="grid-body-left">Records not found.</td>
			        <td class="grid-body-right">&nbsp;</td>
			    </tr>
		    <? } ?>
		</table>
		<a class="kt-font-info" href="<?=getLink("users", "loginhistory", "id", (int)get_var("id"))?>">View full history</a>
		
    <?=formBoxEnd()?>
</div>


  	</div>
  </div>
</div>





