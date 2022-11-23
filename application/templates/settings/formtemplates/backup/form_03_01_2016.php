<? include(TPL_PATH . "settings/menu.php"); ?>
<div align="right" style="clear:both; padding-bottom:5px; padding-top:5px;">
    <img style="vertical-align:middle;" src="<?= SITE_IN ?>images/icons/back.png" alt="Back" width="16" height="16" /> <a href="<?= getLink("formtemplates") ?>">&nbsp;Back to the list</a>
</div>
<form action="<?= getLink("formtemplates", "edit", "id", get_var("id")) ?>" method="post">
    <?= formBoxStart(((int) get_var("id") > 0 ? "Edit template" : "Add New Template")) ?>
    <table cellpadding="0" cellspacing="10" border="0">
        <tr>
            <td width="150">Name: <?= (!$this->is_system ? "<span class=\"required\">*</span>" : "") ?></td>
            <td>@name@</td>
        </tr>
        <tr>
            <td>Description: <?= (!$this->is_system ? "<span class=\"required\">*</span>" : "") ?> </td>
            <td>@description@</td>
        </tr>
        <tr>
            <td>Used for: <?= (!$this->is_system ? "<span class=\"required\">*</span>" : "") ?></td>
            <td <?= ($this->is_system ? "style=\"text-transform:capitalize\"" : "") ?>>@usedfor@</td>
        </tr>
        <tr>
            <td>Attached to:</td>
            <td>
                <img src="<?= SITE_IN ?>images/icons/attach.png" width="16" height="16" alt="Attachments" style="vertical-align:middle;" /> &nbsp;
                <? if (!empty($this->attachments)) { ?>
                    <?= implode("; ", $this->attachments); ?>
                <?
                } else {
                    echo "None";
                }
                ?>
            </td>
        </tr>
        <tr>
            <td valign="top">
                Body:
            </td>
            <td>@body@</td>
        </tr>
    </table>
    <?= formBoxEnd() ?>
    <br />
<?= submitButtons(getLink("formtemplates"), "OK", "submit_button", "submit", $this->is_system ? getLink("formtemplates", "revert", "id", (int) get_var("id")) : "") ?>
</form>
<input type="hidden" id="usedfor_txt" name="usedfor_txt" value="@usedfor_txt@" />
<br />
<div class="lead-info" style="width: 950px;">
    <p class="block-title">Codes</p>
    <div>
        <em>Use the following codes in your form to do further customization. They will be replaced with the order-specific information when the email is sent.</em>
		<div>
			<?php include (TPL_PATH . "settings/emailtemplates/codes.php"); ?>
		</div>
    </div>
</div>
<br /><br />
<script type="text/javascript">//<![CDATA[
    $(function(){
        function showhideOQ(){
            if ($('#usedfor_txt').val() == "orders"){
                $("#codes_quotes").hide();
                $("#codes_orders").show();
            }else{
                $("#codes_orders").hide();
                $("#codes_quotes").show();
            }
        }
        $("#usedfor").change(function(){
            $('#usedfor_txt').val($('#usedfor').val());
            showhideOQ();
        });
        showhideOQ();
    });
    //]]></script>