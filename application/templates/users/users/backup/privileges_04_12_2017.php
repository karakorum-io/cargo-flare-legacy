<? include(TPL_PATH."users/menu_details.php"); ?>
<?= formBoxStart("Group Privileges") ?>
<form action="<?= getLink("users", "assign_privileges", "id", get_var("id")) ?>" method="post">
    <table cellpadding="0" cellspacing="5" border="0">
        <tr>
            <td colspan="2"><em>Select a group and click "Assign Group Privileges" to assign the group's default privileges to the user.</em></td>
        </tr>
        <tr>
            <td>@group_id@</td>
        </tr>
    </table>
    <br />
    <?= submitButtons("", "Assign", "submit_id", "submit_ap"); ?>
</form>
<?= formBoxEnd() ?>
<br />
<?= formBoxStart("Individual Privileges: <span class=\"lightblue\">@contactname@ (@username@)</span>") ?>
<form action="<?= getLink("users", "privileges", "id", get_var("id")) ?>" method="post" enctype="multipart/form-data">
    <table cellpadding="0" cellspacing="5" border="0">        
        <tr>
            <td colspan="2"><em>Select privileges and click "Assign Individual Privileges" to assign them to the user.</em></td>
        </tr>            
        <tr>
            <td>
                <div style="border:1px solid #CCC; border-radius:5px; padding:5px; margin:5px;">
                    <table cellpadding="0" cellspacing="5" border="0">
                        <tr>
                            <td><h3>Access Information</h3></td>
                        </tr>
                        <tr>
                            <td>Leads:</td><td>@access_leads@</td>
                        </tr>
                        <tr>
                            <td>Quotes:</td>
                            <td>@access_quotes@</td>
                        </tr>
                        <tr>
                            <td>Orders:</td>
                            <td>@access_orders@</td>
                        </tr>
                        <tr>
                            <td>Notes:</td>
                            <td>@access_notes@</td>
                        </tr>
                        <tr>
                            <td>View Sources:</td>
                            <td>@hide_lead_sources@</td>
                        </tr>
                    </table>
                </div>
            </td>
            <td>
                <div style="border:1px solid #CCC; border-radius:5px; padding:16px; margin:5px;">
                    <table cellpadding="0" cellspacing="5" border="0">
                        <td colspan="2">
                            <em style="color:#ff0d0d">The following privileges contain company-sensitive information.<br />
                                They should generally be given to managers or administrators. </em>
                        </td>
						<tr>
                        </tr>
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
                <div style="border:1px solid #CCC; border-radius:5px; padding:16px; margin:5px;">
                <table cellpadding="0" cellspacing="5" border="0">
                    <tr>
                        <td><h3>Manage Orders</h3></td>
                    </tr>
                    <tr>
                        <td>Accounts:</td>
                        <td>@access_accounts@</td>
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
                <div style="border:1px solid #CCC; border-radius:5px; padding:16px; margin:5px;">
                <table cellpadding="0" cellspacing="5" border="0">
                    <tr>
                        <td><h3>Dispatched Privileges</h3></td>
                    </tr>
                    <tr>
                        <td>
                            <table width="100%" cellpadding="1" cellspacing="1">
                             <tr>
                               <td>@access_dispatch@</td>
                                  <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                              <td>@access_dispatch_orders@</td>
                             </tr>
                            </table>
                        </td>
                    </tr>			
                </table>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <div style="border:1px solid #CCC; border-radius:5px; padding:16px; margin:5px;">
                    <table cellpadding="0" cellspacing="5" border="0">
                        <tr>
                            <td><h3>Payment Privileges</h3></td>
                        </tr>
                        <tr>
                            <td>@access_payments@</td>
                        </tr>			
                    </table>
                </div>
            </td>
        </tr> 
    </table>
    <br />
</form>
<?= formBoxEnd() ?>
 <br />
 <br />
<?php if (get_var("id") != $_SESSION['member']['parent_id']) { ?>
        <?= submitButtons(getLink("users"), "Assign"); ?>
    <?php } ?>

