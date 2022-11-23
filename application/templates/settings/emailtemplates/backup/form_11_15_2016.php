<br>
<? include(TPL_PATH . "settings/menu.php"); ?>
<div align="right" style="clear:both; padding-bottom:5px; padding-top:5px;">
    <img style="vertical-align:middle;" src="<?= SITE_IN ?>images/icons/back.png" alt="Back" width="16" height="16" /> <a href="<?= getLink("emailtemplates") ?>">&nbsp;Back to the list</a>
</div>
<form action="<?= getLink("emailtemplates", "edit", "id", get_var("id")) ?>" method="post">
    <?= formBoxStart(((int) get_var("id") > 0 ? "Edit template" : "Add New Template")) ?>
    <table cellpadding="0" cellspacing="10" border="0">
        <tr>
            <td width="150">Name: <?= (!$this->is_system ? "<span class=\"required\">*</span>" : "") ?></td>
            <td>@name@</td>
        </tr>
        <tr>
            <td>Description: </td>
            <td>@description@</td>
        </tr>
        <? if (!$this->is_system) { ?>
            <tr>
                <td>Used for:</td>
                <td>@usedfor@ &nbsp;&nbsp;&nbsp;&nbsp;@is_followup@</td>
            </tr>
        <? } ?>
        <tr>
            <td>@to_address@</td>
        </tr>
        <tr>
            <td>@from_address@</td>
        </tr>
        <tr>
            <td>@from_name@</td>
        </tr>
        <tr>
            <td>@subject@</td>
        </tr>
        <tr>
            <td valign="top">Attachments: <img src="<?= SITE_IN ?>images/icons/attach.png" width="16" height="16" alt="Attachments" style="vertical-align:middle;" /></td>
            <td>
                <div style="max-height:400px; width:490px; overflow: auto; border: 1px solid #ccc; background-color: #f1f1f1; padding: 8px;">
                    <? if (!empty($this->daffny->tpl->attachments)) { ?>
                        <table cellpadding="3" cellspacing="0" border="0">
                            <? foreach ($this->attachments as $key => $f) { ?>
							<?php if (is_array($f)) { ?>
                                <tr>
                                    <td style="display:none;" width="20" align="center" class="att_<?=$f['usedfor']?>"><input class="attid_<?=$f['usedfor']?>" type="checkbox" id="attachments<?= $f['id'] ?>" name="attachments[<?= $f['id'] ?>]" value="<?= $f['id'] ?>" <?= $f['ch'] ?> /></td>
                                    <td style="display:none;" class="att_<?=$f['usedfor']?>"><label for="attachments<?= $f['id'] ?>"><?= $f['name'] ?></label></td>
                                </tr>
							<?php } else { echo $f; } ?>
                            <? } ?>
                        </table>
                    <?
                    } else {
                        echo "No attachments.";
                    }
                    ?>
                </div>
            </td>
        </tr>
        <tr>
            <td>Send Email Using:</td>
            <td>@send_type@</td>
        </tr>
        <tr id="body_text_tr" style="display:none;">
            <td valign="top">@body_text@</td>
        </tr>
        <tr id="body_html_tr" style="display:none;">
            <td valign="top">Body (HTML)</td><td valign="top">@body_html@</td>
        </tr>
        <tr>
            <td valign="top">@bcc_addresses@
                <br />
                <em>These adresses will receive a "blind carbon copy" email without other recipients knowing. Separate multiple addresses with commas, for example: "<strong>user1@domain.com, johndoe@example.com</strong>".</em>
            </td>
        </tr>
    </table>
    <?= formBoxEnd() ?>
    <br />
<?= submitButtons(getLink("emailtemplates"), "OK", "submit_button", "submit", $this->is_system ? getLink("emailtemplates", "revert", "id", (int) get_var("id")) : "") ?>
</form>
<input type="hidden" id="usedfor_txt" name="usedfor_txt" value="@usedfor_txt@" />
<br>
<br>
<div class="lead-info" style="width: 1180px;">
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
                $(".att_quotes").hide();
                $(".att_orders").show();
                
                $(".attid_quotes").removeAttr("checked");
                
            }else{
                $("#codes_orders").hide();
                $("#codes_quotes").show();
                $(".att_orders").hide();
                $(".att_quotes").show();
                
                $(".attid_orders").removeAttr("checked");
            }
        }
        $("#usedfor").change(function(){
            $('#usedfor_txt').val($('#usedfor').val());
            showhideOQ();
        });
        showhideOQ();


        function showhideTH(){
            if ($('#send_type_0').is(':checked')){
                $("#body_html_tr").hide();
                $("#body_text_tr").show();
            }else{
                $("#body_html_tr").show();
                $("#body_text_tr").hide();
            }
        }
        $('#send_type_0').click(function(){
            showhideTH();
        });
        $('#send_type_1').click(function(){
            showhideTH();
        });
        showhideTH();
    });
    //]]></script>