<? include(TPL_PATH."accounts/leadsources/menu.php"); ?>


<div class="alert alert-light alert-elevate " >
<div class="row" style="width: 100%">
    <div class="col-12">

<h3>Manage Lead Sources</h3>
Below is a list of your lead sources. Click the Company Name of any lead source to view or edit details.
<div class="mb-3">
	<img style="vertical-align:middle;" src="<?=SITE_IN?>images/icons/add.gif" alt="Add" width="16" height="16" /> &nbsp;<a href="<?=getLink("leadsources", "edit")?>">Add New</a>
</div>



<table id="mange_leads" class="table table-bordered">
    <thead>
    <tr class="">
        <th class="grid-head-left"><?=$this->order->getTitle("id", "ID")?></th>
        <th><?=$this->order->getTitle("company_name", "Company")?></th>
        <th><?=$this->order->getTitle("domain", "Domain")?></th>
        <th><?=$this->order->getTitle("phone", "Phone")?></th>
        <th><?=$this->order->getTitle("email_to", "To Address")?></th>
        <th><?=$this->order->getTitle("email_forward", "Forward Email")?></th>
        <th><?=$this->order->getTitle("status", "Status")?></th>
        <th >Actions</th>
    </tr>
    </thead>
    <? if (count($this->leadsources)>0){?>
	    <? foreach ($this->leadsources as $i => $leadsource) { ?>
	    <tr class="<?=($i == 0 ? " " : "")?>" id="row-<?=$leadsource->id?>">
	        <td class="grid-body-left"><a href="<?=getLink("leadsources", "details", "id", $leadsource->id);?>"><?=$leadsource->id;?></a></td>
	        <td><?=htmlspecialchars($leadsource->company_name);?><br /></td>
	        <td><a href="http://<?=htmlspecialchars($leadsource->domain);?>" target="_blank"><?=htmlspecialchars($leadsource->domain);?></a></td>
	        <td><?=htmlspecialchars($leadsource->phone);?></td>
	        <td><?=$leadsource->email_forward!=""?"<a href=\"mailto:".$leadsource->email_to."\">".$leadsource->email_to."</a>":"";?></td>
	        <td>
                    <?=$leadsource->cron_email!=""?"<a href=\"mailto:".$leadsource->cron_email."_".$leadsource->id."_".$leadsource->owner_id."@".$_SERVER['SERVER_NAME']."\">".$leadsource->cron_email."_".$leadsource->id."_".$leadsource->owner_id."@".$this->daffny->cfg['MAILDOMAIN']."</a>":"";?>
                </td>
	        <td align="center">
                    <?=leadsource::$status_name[$leadsource->status];?>
                </td>
            <td>
            <div class="row">
            <div class="col-4">
                 <?=infoIcon(getLink("leadsources", "details", "id", $leadsource->id))?>
            </div>
            <div class="col-4">
                <?=editIcon(getLink("leadsources", "edit", "id", $leadsource->id))?>
            </div>
            <div class="col-4">
                 <?=deleteIcon(getLink("leadsources", "delete", "id", $leadsource->id), "row-".$leadsource->id)?>
            </div>
            </div>

                </td>
	       
	    <? } ?>
	<?}else{?>
		<tr class="grid-body " id="row-">
	        <td align="center" colspan="10">No records found.</td>
	    </tr>
	<? } ?>
</table>
</div>
</div>
</div>
@pager@

<script type="text/javascript">
        $(document).ready(function() {
        $('#mange_leads').DataTable({
        "lengthChange": false,
        "paging": false,
        "bInfo" : false,
        'drawCallback': function (oSettings) {

        $("#mange_leads_wrapper").children('.row:first').children('.col-sm-12:first').html('<div class="form-group row"><label class="col-form-label">Show: </label><select class=" records_per_page form-box-combobox form-control " style="width:100px;" onchange="setPagerLimit(this.value)"> <option value="10">10</option> <option value="25">25</option> <option value="50" selected="selected">50</option> <option value="100">100</option> <option value="200">200</option> <option value="500">500</option> </select></div>');
        $("#mange_leads_wrapper").children('.row:nth-child(3)').children('.col-sm-12:first').html($('.pager').clone());
        $('.pages_div').remove();


        }
        });
        } );
        </script>