<? include(TPL_PATH . "myaccount/menu.php"); ?>
<div align="right" style="clear:both; padding-bottom:5px; padding-top:5px;">
    <img style="vertical-align:middle;" src="<?= SITE_IN ?>images/icons/back.png" alt="Back" width="16" height="16" /> <a href="<?= getLink("ratings", "search") ?>">&nbsp;Back to the search</a>
</div>
<div style="text-align: right;">
    <img src="<?= SITE_IN ?>images/icons/print-icon.gif" width="16" height="16" alt="Print" style="vertical-align:middle" />
    [<span class="like-link" id="printbtn">Print</span>]
    &nbsp;&nbsp;&nbsp;&nbsp;
</div>
<div id="printme">
    You rated this company:  <span style="font-size:16px;"><?= colorRate($this->daffny->tpl->rating) ?></span>
    <h2>@companyname@</h2>
    <table width="100%" cellpadding="0" cellspacing="5" border="0">
        <tr>
            <td valign="top" style="padding-right:20px;">
                <strong>@companytype@</strong><br />
                <strong>Contact:</strong> @contactname@<br />
                <strong>Owner/Manager:</strong> @owner@<br />
                <strong>Preferred Contact Method:</strong> @preferred_contact_method@<br />
                @address1@<br />
                @address2@<br />
                @city_state@<br />
                @zip_code@ @country@
            </td>
            <td valign="top">
                <strong>Main Phone:</strong> @phone@<br />
                <strong>Local Phone:</strong> @phone_local@<br />
                <strong>Toll Free:</strong> @phone_tollfree@<br />
                <strong>Fax Number:</strong> @fax@<br />
                <strong>Hours:</strong> @hours_or_operation@<br />
                <strong>Email:</strong> <a href="mailto:@email@">@email@</a><br />
                <strong>Web Site:</strong> @site@<br />
            </td>
        </tr>
    </table>
    <br />
    <h3>Operating Authority and Document Packet</h3>
    <strong>ICC-MC#:</strong> @icc_mc_number@<br />
    <strong>DOT Authority:</strong> <a href="<?= getLink("ratings", "documents", "id", (int) get_var("id")) ?>">View</a><br />
    <strong>W-9:</strong> <a href="<?= getLink("ratings", "documents", "id", (int) get_var("id")) ?>">View</a><br />
    <strong>Insurance Policy:</strong> @insurance_company@ <a href="<?= getLink("ratings", "documents", "id", (int) get_var("id")) ?>">View</a><br />
    <strong>Liability Amount:</strong> $@liability_amount@<br />
    <strong>Insurance Coverage:</strong> $@insurance_coverage@<br />
    <strong>Expiration Date:</strong> @insurance_expdate@<br />
    <strong>Cargo Deductible:</strong> $@cargo_deductible@<br />
    <strong>Broker Bond:</strong> @brocker_bond_name@<br />@brocker_bond_phone@
    <br /><br />
    <h3>Reference Information</h3>
    <strong>Established in:</strong> @established@<br />
    <strong>Member of FD Since:</strong> @member_since@<br />
    <strong>Company Description:</strong> @description@<br />
    <br />
    <strong>Business Reference #1:</strong> @ref1_name@ @ref1_phone@<br />
    <strong>Business Reference #2:</strong> @ref2_name@ @ref2_phone@<br />
    <strong>Business Reference #3:</strong> @ref3_name@ @ref3_phone@<br />
    <br />
    <?= formBoxStart() ?>
    <table cellpadding="0" cellspacing="3" border="0">
        <tr>
            <td valign="top"  width="50%">
                <h3>Company Ratings</h3>
                <table cellpadding="0" cellspacing="3" border="0">
                    <tr>
                        <td width="120"><strong>Ratings Score:</strong></td>
                        <td style="color:black; font-size:16px;">@rating_score@</td>
                    </tr>
                    <tr>
                        <td><strong>Ratings Received:</strong></td>
                        <td style="color:black; font-size:16px;">@rating_received@</td>
                    </tr>
                    <tr>
                        <td><strong>Member Since:</strong></td>
                        <td style="color:black; font-size:16px;">@member_since@</td>
                    </tr>
                </table>
            </td>
            <td valign="top" width="50%">
                <h3>Ratings History</h3>
                <table cellpadding="0" cellspacing="5" border="0">
                    <tr>
                        <td>&nbsp;</td>
                        <td width="100" align="center">Past Month</td>
                        <td width="100" align="center">Past 6 Months</td>
                        <td width="100" align="center">All Time</td>
                    </tr>
                    <tr>
                        <td><strong>Ratings Score</strong></td>
                        <td align="center"><strong>@rating_score1@</strong></td>
                        <td align="center"><strong>@rating_score6@</strong></td>
                        <td align="center"><strong>@rating_score@</strong></td>
                    </tr>
                    <tr>
                        <td>
                            <img src="<?= SITE_IN ?>images/icons/ratepositive.png" alt="Positive" width="16" height="16" style="vertical-align:middle;" />
                            <strong style="color:green;">Positive</strong>
                        </td>
                        <td align="center">@rating_score_p1@</td>
                        <td align="center">@rating_score_p6@</td>
                        <td align="center">@rating_score_p@</td>
                    </tr>
                    <tr>
                        <td>
                            <img src="<?= SITE_IN ?>images/icons/rateneutral.png" alt="Neutral" width="16" height="16" style="vertical-align:middle;" />
                            <strong style="color:#0052a4;">Neutral</strong>
                        </td>
                        <td align="center">@rating_score_t1@</td>
                        <td align="center">@rating_score_t6@</td>
                        <td align="center">@rating_score_t@</td>
                    </tr>
                    <tr>
                        <td>
                            <img src="<?= SITE_IN ?>images/icons/ratenegative.png" alt="Negative" width="16" height="16" style="vertical-align:middle;" />
                            <strong style="color:red;">Negative</strong>
                        </td>
                        <td align="center">@rating_score_n1@</td>
                        <td align="center">@rating_score_n6@</td>
                        <td align="center">@rating_score_n@</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <?= formBoxEnd() ?>
</div>
<br />
<div>
    <form action="<?= getLink("ratings", "updaterating", "id", get_var("id")) ?>" method="post">
        <h3>Did this company meet the terms stated in the contract?</h3>
        @type@
        <br />
        <strong>You Previously Rated This Company:  <span style="font-size:16px;"><?= colorRate($this->daffny->tpl->rating) ?></span></strong>
        <br /><br />
        <em>For neutral or negative rating, you have the option of including one or more comments from those below.</em>
        <br /><br />
        <? if (!empty($this->daffny->tpl->comments)) {
            $i = 0; ?>
            <div style="float:left; width:250px;">
                <? foreach ($this->daffny->tpl->comments as $key => $f) {
                    $i++; ?>
                    <? if ($i == 6) { ?>
                        <br />
                        <?= submitButtons("", "Update Rating") ?>
                    </div>
                    <div style="float:left; width:250px;">
                    <? } ?>
                    <input class="comtype" disabled="disabled" type="checkbox" id="comments<?= $f['id'] ?>" name="comments[<?= $f['id'] ?>]" value="<?= $f['id'] ?>" <?= $f['ch'] ?> />&nbsp;&nbsp;<label for="comments<?= $f['id'] ?>"><?= $f['name'] ?></label><br />
            <? } ?>
            </div>
<? } ?>
        <div style="float:right; width:300px; background-color:#fffbd8; border:#000 1px dashed; padding:10px">
            <strong>Ratings Score:</strong> <?= colorRate("Positive") ?> ratings receive ONE point. <?= colorRate("Neutral") ?> ratings receive ONE-HALF point. <?= colorRate("Negative") ?> ratings receive ZERO points. Points are combined and computed into an overall Ratings Score PERCENTAGE.
        </div>
        <div style="clear:both;">&nbsp;</div>
    </form>
</div>
<!-- rating recieved-->
<div class="tab-panel-container">
    <ul class="tab-panel">
        <li class="tab first<?= (!isset($_GET['gave']) ? " active" : ""); ?>"><a href="<?= getLink("ratings", "company", "id", (int) get_var("id")); ?>">Ratings Received</a></li>
        <li class="tab <?= (isset($_GET['gave']) ? " active" : ""); ?>"><a href="<?= getLink("ratings", "company", "id", (int) get_var("id"), "gave", "rate"); ?>">Ratings Given to Others</a></li>
    </ul>
</div>
<div style="clear:both; height:1px; fon-size:0; line-height:0;">&nbsp;</div>
<? if (isset($_GET['gave'])) { ?>
    <table width="100%" cellpadding="0" cellspacing="0" border="0" class="grid">
        <tr class="grid-head">
            <td class="grid-head-left"><?= $this->order->getTitle("added", "Date") ?></td>
            <td><?= $this->order->getTitle("type", "Rating") ?></td>
            <td><?= $this->order->getTitle("status", "Status") ?></td>
            <td><?= $this->order->getTitle("to_id", "Given to") ?></td>
            <td class="grid-head-right">Actions</td>
        </tr>
    <? if (count($this->data) > 0) { ?>
        <? foreach ($this->data as $i => $data) { ?>
                <tr class="grid-body<?= ($i == 0 ? " first-row" : "") ?>" id="row-<?= $data['id'] ?>">
                    <td align="center" valign="top" class="grid-body-left"><?= $data['added']; ?></td>
                    <td align="center">
                        <img src="<?= SITE_IN ?>images/icons/<?= $data['type'] ?>.png" width="16" height="16" alt="Rating" />
                    </td>
                    <td align="center" valign="top"><?= $data['status'] ?></td>
                    <td>
                        <a href="<?= getLink("ratings", "company", "id", $data['to_id']) ?>"><?= $data['to_name'] ?></a> <em><?= $data['to_address'] ?></em><br />
                        Ratings Score: <span style="color:black;"><?= number_format($data['ratings_score'], 2, ".", ",") ?>%</span>, Ratings Received: <span style="color:black;"><?= $data['ratings_received'] ?></span>
                    </td>
                    <td class="grid-body-right">&nbsp;</td>
                </tr>
        <? } ?>
    <? } else { ?>
            <tr class="grid-body first-row" id="row-">
                <td class="grid-body-left">&nbsp;</td>
                <td align="center" colspan="3">No records found.</td>
                <td class="grid-body-right">&nbsp;</td>
            </tr>
    <? } ?>
    </table>
<? } else { ?>
    <table width="100%" cellpadding="0" cellspacing="0" border="0" class="grid">
        <tr class="grid-head">
            <td class="grid-head-left"><?= $this->order->getTitle("added", "Date") ?></td>
            <td><?= $this->order->getTitle("type", "Rating") ?></td>
            <td><?= $this->order->getTitle("status", "Status") ?></td>
            <td><?= $this->order->getTitle("from_id", "From") ?></td>
            <td class="grid-head-right">Actions</td>
        </tr>
    <? if (count($this->data) > 0) { ?>
        <? foreach ($this->data as $i => $data) { ?>
                <tr class="grid-body<?= ($i == 0 ? " first-row" : "") ?>" id="row-<?= $data['id'] ?>">
                    <td align="center" valign="top" class="grid-body-left"><?= $data['added']; ?></td>
                    <td align="center">
                        <img src="<?= SITE_IN ?>images/icons/<?= $data['type'] ?>.png" width="16" height="16" alt="Rating" />
                    </td>
                    <td align="center" valign="top"><?= $data['status'] ?></td>
                    <td>
                        <a href="<?= getLink("ratings", "company", "id", $data['from_id']) ?>"><?= $data['from_name'] ?></a> <em><?= $data['from_address'] ?></em><br />
                        Ratings Score: <span style="color:black;"><?= number_format($data['ratings_score'], 2, ".", ",") ?>%</span>, Ratings Received: <span style="color:black;"><?= $data['ratings_received'] ?></span>
                    </td>
                    <td align="center" class="grid-body-right">
                        <? if ($data['from_id'] == getParentId()) { ?>
                            <a href="<?= getLink("ratings", "edit", "id", $data['from_id']) ?>">Edit</a>
                <? } ?>&nbsp;
                    </td>
                </tr>
        <? } ?>
    <? } else { ?>
            <tr class="grid-body first-row" id="row-">
                <td class="grid-body-left">&nbsp;</td>
                <td align="center" colspan="3">No records found.</td>
                <td class="grid-body-right">&nbsp;</td>
            </tr>
    <? } ?>
    </table>
<? } ?>
@pager@

<script type="text/javascript">//<![CDATA[
    $(function(){
        $("#printbtn").click(function(){
            $("#printme").printArea();
        });

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