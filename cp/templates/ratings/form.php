<div align="right" style="clear:both; padding-bottom:5px; padding-top:5px;">
    <img style="vertical-align:middle;" src="<?= SITE_IN ?>images/icons/back.png" alt="Back" width="16" height="16" /> <a href="<?= getLink("ratings") ?>">&nbsp;Back to the list</a>
</div>
@flash_message@
<form action="<?= getLink("ratings", "editrating", "id", get_var("id")) ?>" method="post">
    <?= formBoxStart("Edit Rating") ?>
    <table cellpadding="0" cellspacing="10" border="0">
        <tr>
            <td>From:</td>
            <td>@from@</td>
        </tr>
        <tr>
            <td>To:</td>
            <td>@to@</td>
        </tr>
        <tr>
            <td>@type@</td>
        </tr>
        <tr>
            <td>@status@</td>
        </tr>
        <tr>
            <td colspan="2">
                <?
                if (!empty($this->daffny->tpl->comments)) {
                    $i = 0;
                    ?>
                    <div style="float:left; width:250px;">
                        <? foreach ($this->daffny->tpl->comments as $f) {$i++; ?>
                                <? if ($i == 6) { ?>
                                        <br />
                                    </div>
                                    <div style="float:left; width:250px;">
                                <? } ?>
                            <input class="comtype" disabled="disabled" type="checkbox" id="comments<?= $f['id'] ?>" name="comments[<?= $f['id'] ?>]" value="<?= $f['id'] ?>" <?= $f['ch'] ?> />&nbsp;&nbsp;<label for="comments<?= $f['id'] ?>"><?= $f['name'] ?></label><br />
                            <? } ?>
                    </div>
                </td>
            </tr>
    <? } ?>
    </table>
    <?= formBoxEnd() ?>
    <br />
<?= submitButtons(getLink("ratings"), "Save") ?>
</form>
<script type="text/javascript">//<![CDATA[
    $(function(){
        function changeType(){
            if ($('#type').val() == '<?= Rating::TYPE_NEUTRAL ?>' || $('#type').val() == '<?= Rating::TYPE_NEGATIVE ?>'){
                $(".comtype").removeAttr("disabled");
            }else{
                $(".comtype").attr("disabled","disabled");
            }
        }

        $("#type").change(function(){
            changeType()
        });

        changeType();
    });
    //]]></script>