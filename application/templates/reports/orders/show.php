<style type="text/css">
    th {
        font-size: 11px;
    }
</style>

<div class="quote-info accordion_main_info_new">
    <div class="row">
        <div class="col-12">
            <div class="kt-portlet">
                <div class="kt-portlet__head" id="accordion_title">
                    <div class="kt-portlet__head-label">
                        <h3 class="kt-portlet__head-title">
                           Orders Report
                        </h3>
                    </div>
                </div>
                <div class="kt-portlet__body accordion_info_content_new accordion_info_content_open">
                    <?=formBoxStart()?>
                    <form action="<?=getLink("reports", "orders")?>" method="post" />
                        <div class="row">
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <input type="radio" name="ptype" value="1" id="ptype1" @ptype1ch@ />
                                    <label for="ptype1">Time Period:</label>
                                    @time_period@
                                </div>
                            </div>

                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <input type="radio" name="ptype" value="2" id="ptype2" @ptype2ch@ />
                                    <label for="ptype2">Date Range:</label>
                                    <div class="row">
                                    <div class="col-12 col-sm-6">
                                    @start_date@
                                    </div>
                                <div class="col-12 col-sm-6">
                                    @end_date@
                                </div>
                            </div>

                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                @users_ids[]@
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    @referred_by[]@
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    @status_id@
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    @ship_via@
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    @order_id@
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    @email@
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                @phone@
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-sm-6 mb-5" >
                                <div class="row">
                                    <div class="col-12 col-sm-2">
                                        <?=submitButtons("", "Generate")?>
                                    </div>
                                    <div class="col-12 col-sm-3">
                                        <?=exportButton("Export to Excel", 'btn_dark_green btn-sm')?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <?=formBoxEnd()?>
                        <div class="row">
                            <div class="col-12 col-sm-12">
                                <div class="form-group">
                                    @pager@
                                </div>
                            </div>
                        </div>
                        <div class="row">
                        <div class="col-12 col-sm-12">
                            <div class="form-group">
                                <table class="table table-bordered" >
                                    <tr >
                                        <th ><?=$this->order->getTitle("id", "ID");?></th>
                                        <th><?=$this->order->getTitle("created", "Order<br />date");?></th>
                                        <th><?=$this->order->getTitle("assigned_id", "Assigned To");?></th>
                                        <th><?=$this->order->getTitle("status", "Status");?></th>
                                        <th><?=$this->order->getTitle("referred_by", "Referred Name");?></th>
                                        <th><?=$this->order->getTitle("shipper_id", "Shipper Name");?></th>
                                        <th><?=$this->order->getTitle("company", "Company");?></th>
                                        <th><?=$this->order->getTitle("shipper_type", "Shipper Type");?></th>
                                        <th><?=$this->order->getTitle("carrier", "Carrier");?></th>
                                        <th><?=$this->order->getTitle("total", "Total Tarriff");?></th>
                                        <th><?=$this->order->getTitle("carrier_pay", "Carrier Pay");?></th>
                                        <th><?=$this->order->getTitle("Deposit", "Deposit");?></th>
                                    </tr>
                                    <? if (count($this->orders) > 0) { ?>
                                        <? foreach ($this->orders as $i => $o) { ?>
                                            <?php
                                            if ($o->prefix) {
                                                $id = $o->prefix . "-" . $o->number;
                                            } else {
                                                $id = $o->number;
                                            }
                                            $total = $o->total_tariff_stored;
                                            $carrier_pay = $o->carrier_pay_stored;
                                            $deposit = $total - $carrier_pay;
                                        ?>
                                    <tr class="grid-body<?=($i == 0 ? " first-row" : "")?>">
                                        <td style="white-space: nowrap;" class="grid-body-left"><a href="<?=SITE_IN?>application/orders/show/id/<?=$o->id?>"  target="_blank"><?=$id?></a></td>
                                        <td align="center"><?=date("m/d/y h:i a", strtotime($o->created));?></td>
                                        <td><?=htmlspecialchars($o->getAssigned()->contactname);?></td>
                                        <td><?=Entity::$status_name[$o->status]?></td>
                                        <td><?=$o->referred_by?></td>
                                        <td>
                                            <?php echo htmlspecialchars(trim($o->shipper_id) == "" ? "" : $o->getShipper()->fname . " " . $o->getShipper()->lname) ?>
                                            <br/>
                                            <a href="mailto:"><?=htmlspecialchars(trim($o->shipper_id) == "" ? "" : $o->getShipper()->email)?></a>
                                            <br>Source : <?php
                                            $q = "SELECT company_name FROM app_leadsources WHERE `id` =" . $o->source_id;
                                            $r = $this->daffny->DB->query($q);
                                            $rw = $this->daffny->DB->fetch_row($r);
                                            echo $rw['company_name'];
                                            ?>
                                        </td>
                                        <td><?=htmlspecialchars(trim($o->shipper_id) == "" ? "" : $o->getShipper()->company)?></td>
                                        <td><?=$o->getShipper()->shipper_type;?></td>
                                        <td><?=htmlspecialchars(trim($o->carrier_id) > 0 ? $o->getCarrier()->company_name : "")?></td>
                                        <td style="white-space: nowrap;" align="right" class="grid-body-right">
                                            <?php echo $o->total_tariff_stored; ?>
                                        </td>
                                        <td>$<?=$o->carrier_pay_stored;?></td>
                                        <td>$<?=number_format($deposit, 2);?></td>
                                    </tr>
                                            <? } ?>
                                    <? } else { ?>
                                        <tr class="grid-body first-row" id="row-">
                                            <td align="center" colspan="12">
                                                <? if (isset($_POST['submit'])) { ?>
                                                    No records found.
                                                <? } else { ?>
                                                    Generate report.
                                                <? } ?>
                                            </td>
                                        </tr>
                                    <? } ?>
                                </table>
                                <div class="row">
                                    <div class="col-12 col-sm-12">
                                        <div class="form-group">
                                            @pager@
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $.fn.datepicker.defaults.format = "mm/dd/yyyy";
    $('.kt_datepicker_1').datepicker({
        startDate: '-3d'
    });
</script>

<script type="text/javascript">
    $(document).ready(function() {
        $('#users_ids').select2();
        $('#referred_by').select2();
    });
</script>

<script type="text/javascript">//<![CDATA[
    $("#users_ids").multiselect({ // Build multiselect for users
        noneSelectedText: 'Select User',
        selectedText: '# users selected',
        selectedList: 1
    });

    $("#source_name").multiselect({ // Build multiselect for users
        noneSelectedText: 'Select Source',
        selectedText: '# source selected',
        selectedList: 1
    });

    $("#referred_by").multiselect({ // Build multiselect for users
        noneSelectedText: 'Select Referred by',
        selectedText: '# Referred by selected',
        selectedList: 1
    });

    $("#start_date, #end_date").click(function(){
        $("#ptype2").attr("checked", "checked");
    });

    $("#time_period").click(function(){
        $("#ptype1").attr("checked", "checked");
    });
//]]></script>