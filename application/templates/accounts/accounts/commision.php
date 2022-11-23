<?
if (isset($_GET['id']) && $_GET['id'] > 0) {
    include(TPL_PATH . "accounts/accounts/menu_details.php");
} else {
    include(TPL_PATH . "accounts/accounts/menu.php");
}

?>
Complete the form below and click "Save Account" when finished.
<div align="right" style="clear:both; padding-bottom:5px; padding-top:5px;">
    <img style="vertical-align:middle;" src="<?= SITE_IN ?>images/icons/back.png" alt="Back" width="16" height="16" /> <a href="<?= getLink("accounts") ?>">&nbsp;Back to the list</a>
</div>

<form action="<?= getLink("accounts", "shippersComm","id",get_var("id"),"shipper",get_var("shipper")) ?>" method="post" id="account_edit_form">
<?= formBoxStart("Create Account Commission Information") ?>

  <div class="row">
    <div class="col-6">
      <div class="form-group">
        <label>Select Shipper</label>

        <? 
        $ID = get_var("id");
        if($_POST['account']!="")
        $ID = $_POST['account'];

        if (count($this->accounts)>0){?>
        <select  name="account" class="form-box-combobox">
                <option value="" >Select One</option>
        <? foreach ($this->accounts as $i => $account) { ?>
         <option value="<?= $account->id ?>"  <?php if($ID == $account->id){print " selected=selected";}?>><?=htmlspecialchars($account->company_name);?> (<?= htmlspecialchars($account->first_name);?> <?= htmlspecialchars($account->last_name);?>)</option>
         <? } ?>
         </select>
        <? }else{?>

            <select  name="account" class="form-box-combobox"
                style="">
                <option value="" > No records found.</option>
            </select>
        <? } ?>


      </div>

      <div class="form-group">
        <label>Select User/Salesman</label>
            <select name="salesman" class="form-box-combobox">
            <option value="" >Select One</option>
            <?php foreach ($this->company_members as $member) : ?>

            <option value="<?= $member->id ?>"
             
              <?php if($_POST['salesman'] == $member->id){print " selected=selected";}?>><?= $member->contactname ?></option>
            <?php endforeach; ?>
            </select>
      </div>

      <div class="form-group">
        @referred_by@
      </div>

        <div class="form-group">
          <div id="referrer_commission"></div>
        </div>
       
    </div>
</div>

<?=submitButtons(getLink("accounts/shippersComm"), "Save")?>

<?= formBoxEnd() ?>
</form>

<br />

<script language="javascript">
function selectReferred()
{
	  var referred_by = $("#referred_by").val();
	  $.ajax({
                url: BASE_PATH + 'application/ajax/entities.php',
                data: {
                    action: "getReferrerCommission",
                    referred_by: referred_by
                },
                type: 'POST',
                dataType: 'json',
                beforeSend: function () {
                },
                success: function (retData) {
                   // $("#commission").val(retData.commission)
				   var data = '<table width="40%" cellspacing="1" cellpadding="1">';
				   data += '<tr><td><b>Intial Percentage:</b>  </td><td>'+retData.commission.intial_percentage+'%</td></tr>';
				   data += '<tr><td><b>Residual Percentage:</b>  </td><td>'+retData.commission.residual_percentage+'%</td></tr>';
				   data += '<tr><td><b>Commission:</b>  </td><td>'+retData.commission.commission+'%</td></tr>';
				   
				   $("#referrer_commission").html(data);
                }
            });
}
</script>
