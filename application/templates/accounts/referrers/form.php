<? include(TPL_PATH."accounts/referrers/menu.php"); ?>
<div class="alert alert-light alert-elevate " style="margin: 0px;">
    <div class="kt-body">
<div class="row ml-2">
    <div class="8">
        <div class="row">
            <div class="col-6 mb-4 ">
                If you are using referrers on a custom quote request form hosted on your own website, you should re-generate the form to reflect the changes made here.
            </div>
            <div class="col-6 text-right">
                <img style="vertical-align:middle;" src="<?=SITE_IN?>images/icons/back.png" alt="Back" width="16" height="16" /> <a href="<?=getLink("referrers")?>">&nbsp;Back to the list</a>
            </div>
        </div>

        <?php
        $memberid = "";
        if($_POST['salesrep']!="")
            $memberid = $_POST['salesrep'];
        elseif(!is_null($this->salesrep))
            $memberid = $this->salesrep;
        ?>
        <form action="<?=getLink("referrers", "edit", "id", get_var("id"))?>" method="post">
            <?=formBoxStart(((int)get_var("id") > 0 ? "Edit Referrer" : "Add New Referrer"))?>
            <div class="row">
                <div class="col-4">
                    @name@

                    <?php if($_SESSION['parent_id'] ==1){?>
                        <div class="form-group mt-2">
                            <label>Select User/Salesman</label>
                            <select name="salesrep" class="form-box-combobox"
                            id="salesrep">
                            <option value="" >Select One</option>
                            <?php foreach ($this->company_members as $member) : ?>

                                <option value="<?= $member->id ?>"

                                    <?php if($memberid == $member->id){print " selected=selected";}?>><?= $member->contactname ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-4">
                        @description@
                    </div>

                </div>


                <b>Lead Creator Percentage</b>
                <div class="row mt-2">

                    <div class="col-4">
                        @intial_percentage@
                    </div>



                    <div class="col-4">
                        @residual_percentage@
                    </div>
                </div>



                <div class="row mt-4">
                    <div class="col-4">
                        @commission@
                    </div>
                <?php }?> 
                <div class="col-4">
                    @status@
                </div>
            </div>
       <?=formBoxEnd()?>
                  <div class="row mt-4" >
                    <div class="col-4">
                        <?=submitButtons(getLink("referrers"), "Save",' ')?>
                    </div>
            </div>
        </div>
    </div>
</form>


		<!-- <table cellpadding="0" cellspacing="10" border="0">
			<tr>
				<td>@name@</td>
			</tr>
			<tr>
				<td>@description@</td>
			</tr>
            

        
            <tr>
				<td colspan="2" align="left" style="border-bottom:1px solid #666;"><b>Lead Creator Percentage</b></td>
			</tr>
            <tr>
				<td>@intial_percentage@</td>
			</tr>
            <tr>
				<td>@residual_percentage@</td>
			</tr>
            <tr>
				<td colspan="2" align="left" style="border-top:1px solid #666;"></td>
             </tr>   
            <tr>
				<td>@commission@</td>
			</tr>
         
			<tr>
				<td>@status@</td>
			</tr>
		</table> -->
	