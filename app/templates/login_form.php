	
	<style type="text/css">
	.staic_cantants {
    padding-top: 0px;
}	


	</style>		    

			    	<?php if (isGuest()): ?>
			    <div id="loginbox">
			    	 <div class="kt-portlet">
                <div class="kt-portlet__body">
			    	<h3>Welcome!</h3>

				    	Registered users login below:
				    	<form action="<?php echo getLink("user", "signin"); ?>" method="post">
                               <div class="form-group">
									<input type="email" class="form-control m-input" name="email" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter Email" onfocus="this.value=(this.value=='E-mail:')?'':this.value;" id="email" onblur="this.value=this.value?this.value:'E-mail:';">
								</div>
								<div class="form-group">
					    		

					    			<input type="password" class="form-control m-input" name="password"  aria-describedby="emailHelp" onfocus="this.value=(this.value=='Password:')?'':this.value;" onblur="this.value=this.value?this.value:'Password:';" placeholder="Enter Password">
					        	</div>
					        	
					        	
					        		<a href="<?php echo getLink("user", "forgot-password"); ?>" class="forgot" style="color: #008ec2; font-size: 12px">Forgot password?</a> &nbsp;&nbsp;&nbsp;<a class="forgot" href="#" onclick="passwordHint($('#email').val()); return false;">Hint</a>

					        		<input type="submit" class="form-control m-input btn-primary btn-block"  id="exampleInputEmail1" name="submit" placeholder="Enter Email" style="color: white">
					       
				        </form>
				    </div>
				    </div>





		    	<div class="loginbox-bottom">&nbsp;</div>
		        </div>


				    <?php else: ?>
				    	<div class="kt-portlet">
				    		<div class="kt-portlet__body">
				    <?=formboxStart("", "254" ,"wg")?>
						<strong><?=$_SESSION['member']['email']?></strong><br />
						<strong><a href="<?=getLink("application")?>" style="color: #3b67a6;">Application</a></strong><br />
						<strong><a style="color: #3b67a6;"  href="<?=getLink(getUserDir(), "profile")?>">My Profile</a></strong><br />
						<a href="<?=getLink("user", "signout")?>">Logout</a><br />

					<?=formboxEnd("wg")?>
				</div>
				</div>	
					<?php endif; ?>
