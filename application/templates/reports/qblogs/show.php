<style type="text/css">
    a:link.order, a:link.order-asc, a:link.order-desc, a:visited.order, a:visited.order-asc, a:visited.order-desc, a:active.order, a:active.order-asc, a:active.order-desc {
    color: #ffffff;
    font-size: 11px;
    padding: 6px;
    text-decoration: blink;
}
p.fonts {
    line-height: 2;
    font-size: 12px;
    margin-top: 1px;
        margin: 0px;
}

</style>
<div class="quote-info accordion_main_info_new">
    <div class="row">           
        <div class="col-12">
            <div class="kt-portlet">
                <div class="kt-portlet__head" id="accordion_title">
                    <div class="kt-portlet__head-label">
                        <h3 class="kt-portlet__head-title">
                           QuickBooks Logs Report
                        </h3>
                    </div>
                </div>
                <div class="kt-portlet__body accordion_info_content_new accordion_info_content_open">
                    <div class="row">

                    

                <?php echo formBoxStart() ?>
                <form action="<?php echo getLink("reports", "qblogs") ?>" method="post" />


                    <div class="col-12 col-sm-12">
                    <div class="form-group">
                    <input type="radio" name="ptype" value="1" id="ptype1" @ptype1ch@ />
                    <label for="ptype1">Time Period:</label>
                    @time_period@

                    </div>
                    </div>

                    <div class="col-12 col-sm-12">
                    <div class="form-group">
                    @status_id@
                    </div>
                    </div>
                    </div>
                    

                    <div class="row">
                       
                    <div class="col-1">
                        <?php echo submitButtons("", "Generate") ?>
                    </div>

                    <div class="col-3">
                        <?php echo exportButton("Export to Excel",'btn-sm btn_dark_green ') ?>
                    </div>  

                    </div>


                    <?php echo formBoxEnd() ?>






     <div class="row">
     <div class="col-12 col-sm-12">
     <div class="kt-portlet__body">
      <table  id="dtBasicExample" class="table table-striped table-bordered" >
        <thead>
        <tr >
            <th ><p class="fonts">QB Queue ID</p></th>
            <th><p class="fonts">Enqueue Datetime</p></th>
            <th><p class="fonts">QB Action</p></th>
            <th><p class="fonts">Ident</p></th>            
            <th><p class="fonts">QB Status</p></th>
            <th><p class="fonts">Message</p></th>                                          
            <th><p class="fonts">Suggesion</p></th>
            <th><p class="fonts">ID</th>
        </tr> 
        </thead>       
        <?php if (count($this->qblogs) > 0) {  ?>
        
            <?php foreach ($this->qblogs as $i => $o) { ?>            
                <tr class="<?php echo ($i == 0 ? " first-row" : "") ?>">
                    <td class="grid-body-left"><?= htmlspecialchars($o->quickbooks_queue_id); ?></td>
                    <td ><?= date("m/d/y h:i a", strtotime($o->enqueue_datetime)); ?></td>
                    <td><?php echo htmlspecialchars($o->qb_action); ?></td>
                    <td><?php echo htmlspecialchars($o->ident); ?></td>
                    <?php
                    
                        $status =   array(
                            "s" =>"Success",
                            "q" =>"Queue",
                            "e" =>"Error",
                            "i"  =>"Information"
                        );                        
                        $errorMessageNumber     =   explode(":",$o->msg);
                        $errorNumber            =   $errorMessageNumber[0];                        
                        if($errorNumber==3070 ||$errorNumber==3090 || $errorNumber==3210 || $errorNumber==3040 || $errorNumber==500|| $errorNumber==3100 || $errorNumber==3180|| $errorNumber==3120){                            
                           $result = $this->daffny->DB->query("select id,number,prefix from app_entities where account_id='".$o->ident."'");
                            while ($row = $this->daffny->DB->fetch_row($result)) {
                               $entityID        = $row['id']; 
                               $entityName      = $row['number']; 
                               $entityPrefix  = $row['prefix']; 
                            }                      
                            
                            $links =  array(
                                "500"   =>  "<a target='_blank' href='".getLink("accounts", "edit")."/id/".$o->ident."'>".$o->ident."</a>",
                                "3260"  =>  "-------",
                                "3240"  =>  "-------",                           
                                "3210"  =>  "<a target='_blank' href='".getLink("orders", "payments")."/id/".$o->ident."'>".$o->ident."</a>",
                                "3200"  =>  "-------",
                                "3180"  =>  "<a target='_blank' href='".getLink("orders","payments")."/id/".$o->ident."'>".$o->ident."</a>",
                                "3176"  =>  "-------",
                                "3175"  =>  "-------",
                                "3173"  =>  "-------",
                                "3170"  =>  "-------",
                                "3150"  =>  "-------",
                                "3140"  =>  "-------",
                                "3120"  =>  "-------",
                                "3100"  =>  "<a target='_blank' href='".getLink("accounts", "edit")."/id/".$o->ident."'>".$o->ident."</a>",
                                "3090"  =>  "<a target='_blank' href='".getLink("orders")."/edit/id/".$entityID."/queue/".$o->quickbooks_queue_id."'>".$entityPrefix."-".$entityName."</a>",
                                "3070"  =>  "<a target='_blank' href='".getLink("orders")."/edit/id/".$entityID."/queue/".$o->quickbooks_queue_id."'>".$entityPrefix."-".$entityName."</a>",
                                "3040"  =>  "<a target='_blank' href='".getLink("orders","payments")."/id/".$o->ident."'>".$o->ident."</a>",
                                "3000"  =>  "-------",
                                "-2"  =>  "-------",
                                "-2"  =>  "-------",                   
                            );
                            $suggestion =  array(
                                "500"   =>  "Try Updating Information",
                                "3260"  =>  "Contact Admin you dont have permission",
                                "3240"  =>  "N/A",                            
                                "3210"  =>  "Tried to pay over amount, Please lower the amount",
                                "3200"  =>  "N/A",
                                "3180"  =>  "Someone else tried to update, Update again",
                                "3176"  =>  "Transaction in progress, please try again later",
                                "3175"  =>  "Transaction in progress, please try again later",
                                "3173"  =>  "Try searching the record for this INDENT ID",
                                "3170"  =>  "Some other user modifying this record",
                                "3150"  =>  "Unable to detect transaction ID please try again later",
                                "3140"  =>  "Please check if user exists ?",
                                "3120"  =>  "Try using appropriate Values",
                                "3100"  =>  "Name Already in use, tr using some other name",
                                "3090"  =>  "Please remove Colon from the Hours or Company Name ",
                                "3070"  =>  "Please enter shorter values in First Name,Last Name or in Address ",
                                "3040"  =>  "Try Changing the Amount",
                                "3000"  =>  "please check the given list ID",
                                "-2"  =>  "N/A",
                                "-2"  =>  "Contact Admin"                      
                            );
                        }
                        
                        if($o->qb_status=="e")
                            $color="red;";
                        else if($o->qb_status=="s")
                            $color="green;";
                        else if($o->qb_status=="q")
                            $color="orange;";
                        else
                            $color="blue;";
                        
                        
                    ?>
                    <td style="color:<?php echo $color;?>"><?php echo htmlspecialchars($status[$o->qb_status]); ?></a></td>
                    <td ><?php echo htmlspecialchars($o->msg);?></td>                  
                    <td ><?php echo $suggestion[$errorNumber];?></td> 
                    <td ><?php echo $links[$errorNumber];?></td>
                </tr>
            <?php } ?>
    <?php } else { ?>
        <tr  style="text-align: center;">
            <td colspan="8" >
                <?php if (isset($_POST['submit'])) { ?>
                    No records found.
                <?php } else { ?>
                    Generate report.
                <?php } ?>
            </td>
        </tr>
    <?php } ?>
</table>
</div>
                
                </div>
                </div>



                   
                </div>
            </div>              
        </div>
    </div>
</div>
    
    


<script type="text/javascript">
$(document).ready(function () {
$('#dtBasicExample').DataTable();
$('.dataTables_length').addClass('bs-select');
});
</script>


<script type="text/javascript">//<![CDATA[
   /* $("#users_ids").multiselect({ // Build multiselect for users
        noneSelectedText: 'Select User',
        selectedText: '# users selected',
        selectedList: 1
    });
    */
    $("#start_date, #end_date").click(function(){
        $("#ptype2").attr("checked", "checked");
    });

    $("#time_period").click(function(){
        $("#ptype1").attr("checked", "checked");
    });
    //]]></script>
