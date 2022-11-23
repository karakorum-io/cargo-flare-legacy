 <style>
    .ui-multiselect {
        padding: 0px 0 2px 4px;
        text-align: left;
        width: 150px !important;
    }
    .customRow{
        padding-left:25px;
    }
    .spacing{
        padding-top: 10px;
    }
</style>
<? include(TPL_PATH."users/menu_details.php"); ?>

  <div class="alert alert-light alert-elevate  ">
    <div class="row w-100">
        <div class="col-12">

        <div class="row">
        <div class="col-8">
        <?= formBoxStart("Group Privileges") ?>
        <form action="<?= getLink("users", "assign_privileges", "id", get_var("id")) ?>" method="post">
        <span class="mb-3">Select a group and click "Assign Group Privileges" to assign the group's default privileges to the user</span> </br>
        @group_id@
        <div class="mt-3">
        <?= submitButtons("", "Assign", "submit_id", "submit_ap"); ?>
        </div>
        </form>
        <?= formBoxEnd() ?>
        </div>
        </div>


<div class="row mt-4">
     <div class="col-12">

        <?= formBoxStart("Individual Privileges: <span class=\"kt-font-boldest\">@contactname@ (@username@)</span>") ?>
<form action="<?= getLink("users", "privileges", "id", get_var("id")) ?>" method="post" enctype="multipart/form-data">
    <table class="w-100 mt-4">        
        <tr>
            <td colspan="2"><em>Select privileges and click "Assign Individual Privileges" to assign them to the user.</em></td>
        </tr>            
        <tr>
            <td>
                <div style="">
                    <table  class="table table-bordered">
                        <tr>
                            <td class="customRow" colspan="3"><h3>Access Information  @specificLeads@ @specificOrders@ @access_leads_custom@ @access_quotes_custom@ @access_orders_custom@ </h3></td>
                            <input type="hidden" id="leadsT" name="leadsT" value="1">
                            <input type="hidden" id="ordersT" name="ordersT" value="1">
                            <script>
                                $(document).ready(function(){
                                    var leads = $("#specificLeads").val();
                                    var orders = $("#specificOrders").val();
                                    var checkboxT = $("#access_leads_custom").val();
                                    var checkboxT2 = $("#access_orders_custom").val();
                                    
                                    var l1 = leads.length;
                                    var l2 = orders.length;
                                    if(checkboxT == 2 && l1 > 0){                                        
                                        $("#custom_user_aceess1").attr("checked","checked");
                                        $("#leadsT").val(2);
                                    } else {
                                        $("#drop1").hide();
                                    }
                                    
                                    if(checkboxT2 == 2 && l2 > 0){                                        
                                        $("#custom_user_aceess2").attr("checked","checked");
                                        $("#ordersT").val(2);
                                    } else {
                                        $("#drop2").hide();
                                    }

                                    
                                    $("#access_leads_0").click(function(){
                                        $("#drop1").hide();
                                        $("#leadsT").val(1);
                                    });
                                    $("#access_leads_1").click(function(){
                                        $("#drop1").hide();
                                        $("#leadsT").val(1);
                                    });
                                    $("#access_leads_2").click(function(){
                                        $("#drop1").hide();
                                        $("#leadsT").val(1);
                                    });
                                    $("#custom_user_aceess1").click(function(){
                                        $("#drop1").show();
                                        $("#leadsT").val(2);
                                    });
                                    
                                    $("#access_orders_0").click(function(){
                                        $("#drop2").hide();
                                        $("#ordersT").val(1);
                                    });
                                    $("#access_orders_1").click(function(){
                                        $("#drop2").hide();
                                        $("#ordersT").val(1);
                                    });
                                    $("#access_orders_2").click(function(){
                                        $("#drop2").hide();
                                        $("#ordersT").val(1);
                                    });
                                    $("#custom_user_aceess2").click(function(){
                                        $("#drop2").show();
                                        $("#ordersT").val(2);
                                    });
                                    
                                    
                                    
                                });
                            </script>
                        </tr>
                        <tr>
                            <td class="customRow" style="">
                                <span class="spacing">@access_leads@</span><br>
                                <?php
                                    $tooltip="Please select the user you wish to have access and edit access to the selected user";
                                ?>
                                <span class="spacing"><input id="custom_user_aceess1" name="access_leads" type="radio" value="2"> &nbsp;&nbsp;&nbsp;Custom User Access <img src="/images/icons/info.png" title="<?php echo $tooltip;?>" alt="Details" width="16" height="16"></span><br>
                                <span class="spacing" id="drop1">@users_ids[]@</span><br>
                            </td>
                            <td class="customRow" style="">
                                <span class="spacing">@access_quotes@</span><br>&nbsp;
                            </td>
                            <td class="customRow" style="">
                                <span class="spacing">@access_orders@</span><br>
                                <span class="spacing"><input id="custom_user_aceess2" name="access_orders" type="radio" value="2"> &nbsp;&nbsp;&nbsp;Custom User Access <img src="/images/icons/info.png" title="<?php echo $tooltip;?>" alt="Details" width="16" height="16"></span><br>
                                <span class="spacing" id="drop2">@users_ids3[]@</span></td>
                        </tr>
                        <tr>
                            <td>&nbsp;<br></td>
                            <td>&nbsp;<br></td>
                            <td>&nbsp;<br></td>
                        </tr>
                        <tr>
                            <td>Notes:</td>
                            <td colspan="2">@access_notes@</td>
                        </tr>
                        <tr>
                            <td>Lead Sources:</td>                            
                            <td colspan="2">@access_lead_sources@</td>
                        </tr>                        
                    </table>
                </div>
            </td>
            <td>
                <div class="member_margin" style="margin-top: -98px; margin-left: 9px">
                    <table class="table-bordered table">
                        <td colspan="2">
                            <em style="color:#ff0d0d">The following privileges contain company-sensitive information.<br />
                                They should generally be given to managers or administrators. </em>
                        </td>
                        <tr>
                            <td>Reports:</td>
                            <td>@access_reports@</td>
                        </tr>
                        <tr>
                            <td>Users:</td>
                            <td>@access_users@</td>
                        </tr>
                        <tr>
                            <td>Preferences:</td>
                            <td>@access_preferences@</td>
                        </tr>
                        <tr>
                            <td>Lead Sources:</td>
                            <td>@access_lead_sources@</td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <div >
                <table  class="table-bordered table">
                    <tr  colspan="2">
                       <h3>Manage Accounts</h3>
                    </tr>
                  
            <!-- chetu added code for account privilege bifurcation  -->
                    <tr>
                        <td>Carriers:</td>
                        <td>@access_carriers@</td>
                    </tr>
                    <tr>
                        <td>Locations:</td>
                        <td>@access_locations@</td>
                    </tr>
                    <tr>
                        <td>Shippers:</td>
                        <td>@access_shippers@</td>
                    </tr>
                </table>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <div style="">
                <table class="table-bordered table">
                    <tr>
                        <h3>Dispatched Privileges</h3>
                    </tr>
                    <tr>
                        <td>Dispatch:</td>
                        <td>
                            <table width="100%" cellpadding="1" cellspacing="1">
                             <tr>
                               <td>@access_dispatch@</td>

                              <td>@access_dispatch_orders@</td>
                             </tr>
                            </table>
                        </td>
                    </tr>           
                </table>
                </div>
            </td>
        </tr>
        <?php 
            // Chetu added this section to fulfill Duplicate carriers & shippers 
            // privileges as per requirement of Stefano
        ?>
        <tr>
            <td>
                <div >
                <table class="table-bordered table">
                    <tr>
                        <h3>Duplicate Accounts </h3>
                    </tr>
                    <tr>
                        <td>Carriers:</td>
                        <td>
                            @access_duplicate_carriers@
                        </td>
                    </tr>
                    <tr>
                        <td>Shippers:</td>
                        <td>
                            @access_duplicate_shippers@
                        </td>
                    </tr>
                </table>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <div >
                    <table class="table-bordered table" ">
                        <tr>
                           <h3>Payment Privileges</h3>
                        </tr>
                        <tr>
                            <td>Payments:</td>
                            <td>@access_payments@</td>
                        </tr>           
                    </table>
                </div>
            </td>
        </tr> 
    </table>
    <br />
    <?php if (get_var("id") != $_SESSION['member']['parent_id']) { ?>
        <?= submitButtons(getLink("users"), "Assign"); ?>
    <?php } ?>
</form>
<?= formBoxEnd() ?>

</div>
</div>
</div>
</div>
</div>







<script type="text/javascript">//<![CDATA[
    $("#users_ids").select2({ // Build multiselect for users
        noneSelectedText: 'Select User',
        selectedText: '# users selected',
        selectedList: 1,
        disabled: true
    });
    $("#users_ids2").select2({ // Build select2 for users
        noneSelectedText: 'Select User',
        selectedText: '# users selected',
        selectedList: 1
    });
    $("#users_ids3").select2({ // Build multiselect for users
        noneSelectedText: 'Select User',
        selectedText: '# users selected',
        selectedList: 1
    });
//]]></script>