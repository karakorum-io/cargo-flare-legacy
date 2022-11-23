<style>
    label{
        cursor:auto;
    }
</style>

<!--  -->
<div class="quote-info accordion_main_info_new">
    <div class="row">           
        <div class="col-12">
            <div class="kt-portlet ">
                <div class="kt-portlet__head" id="accordion_title">
                    <div class="kt-portlet__head-label">
                        <h3 class="kt-portlet__head-title">
                            Print Check Report
                        </h3>
                    </div>
                </div>
                <div class="kt-portlet__body accordion_info_content_new accordion_info_content_open">

             <?php echo formBoxStart() ?>
            <form action="<?php echo getLink("reports", "print_check_report") ?>" method="post"/>
                    <div class="row">
                    <div class="col-12 col-sm-4">
                    <div class="form-group">
                    <label  for="ptype1" class="kt-radio kt-radio--brand" >
                    <input type="radio" name="ptype" value="1" id="ptype1" @ptype1ch@  / >Time Period:
                    <span></span>
                    </label>
                    @time_period@



      

                    </div>
                        </div>
                        <div class="col-12 col-sm-4">
                            <div class="form-group">
                            
                    <label  for="ptype2" class="kt-radio kt-radio--brand" >
                    <input type="radio" name="ptype" value="2" id="ptype2" @ptype2ch@  / >Date Range:
                    <span></span>
                    </label>
                          
                          <div class="row">
                            <div class="col-6">
                                 @start_date@
                            </div>
                            <div class="col-6">
                                 @end_date@
                            </div>
                           
                           
                        </div>
                            </div>
                        </div>
                    </div>


       
                    <div class="row">
                        <div class="col-12 col-sm-4">
                            <div class="form-group">
                                <label>Enter Account #</label>
                                @account_number@
                            </div>
                        </div>
                        <div class="col-12 col-sm-4">
                            <div class="form-group">

                        <div class="row">
                            <div class="col-6">
                                <label  class="kt-radio kt-radio--brand" >
                                    <input type="radio" checked name="exportType" value="1"  / >Excel
                                    <span></span>
                                </label>
                                <?php echo submitButtons("", "Generate",'','','','btn_dark_blue') ?>

                            </div>
                            <div class="col-6">
                                <label  class="kt-radio kt-radio--brand" >
                                    <input type="radio" name="exportType" value="2"  / >CSV
                                    <span></span>
                                </label>  
                                <?php echo exportButton("Export",'btn_dark_green btn-sm') ?>
                            </div>
                        </div>



                            </div>
                            <?php echo formBoxEnd() ?>
                        </div>
                    </div>

        <div class="row">
        <div class="col-12 col-sm-12">
        <div class="form-group">

        </div>
        </div>

        </div>

                    
 <div class="row">

<div class="col-12 col-sm-12">
 <div class="kt-portlet__body">
    <table id="Print_Check_Report" class="table table-bordered  " >
       <thead>
         <tr>
                <th class="grid-head-left"><?php echo $this->order->getTitle("id", "I"); ?></th>
                <th><?php echo $this->order->getTitle("check_number", "Account Number"); ?></th>
                <th><?php echo $this->order->getTitle("created", "Date Issue"); ?></th>
                <th><?php echo $this->order->getTitle("check_number", "Check Number"); ?></th>
                <th><?php echo $this->order->getTitle("amount", "Amount"); ?></th>
                <th><?php echo $this->order->getTitle("print_name", "Print Name"); ?></th>
         </tr>
        </thead>

            <?php if (count($this->orders) > 0) {
                 $paymentManager = new PaymentManager($this->daffny->DB);
            ?>
            <?php
                 foreach ($this->orders as $i => $o) {
            ?>
                <tr>

                    <td  ><a href="<?php echo SITE_IN ?>application/orders/show/id/<?=$o->entity_id?>"  target="_blank"><?=$o->id?></a></td>
                    <td ><?php echo ($this->accuont_number == null ? "" : $this->accuont_number )?></td>
                    <td ><?=$o->created?></td>
                    <td ><?=$o->check_number?></td>
                    <td >$<?=$o->amount_format?></td>
                    <td ><?=$o->print_name?></td>

                </tr>
                <?php 
            }
            ?>
        <?php }?>
    </table>
</div>
</div>
                        
    </div>


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
    
    
    

<!--  -->


<script type="text/javascript">
$.fn.datepicker.defaults.format = "mm/dd/yyyy";
$('#start_date,#end_date').datepicker({
});
$("#start_date,#end_date").attr({'autocomplete': 'off','autocorrect': 'off', 'spellcheck': 'false'})
</script>


<script type="text/javascript">
    $(document).ready(function() {
   $('#Print_Check_Report').DataTable({
       "lengthChange": false,
       "paging": false,
       "bInfo" : false,
       'drawCallback': function (oSettings) {
           $('#Print_Check_Report_wrapper').children('.row:first').children('.col-md-6:first').html('<div class="form-group row"><label class="col-form-label">Show: </label><select class=" records_per_page form-box-combobox form-control " style="width:100px;" onchange="setPagerLimit(this.value)"> <option value="10">10</option> <option value="25">25</option> <option value="50" selected="selected">50</option> <option value="100">100</option> <option value="200">200</option> <option value="500">500</option> </select></div>');
           $('#Print_Check_Report_wrapper').children('.row:last').children('.col-md-5').html($('.pager').clone()).addClass('text-left');
           $('#Print_Check_Report_wrapper').children('.row:last').children('.col-md-7').html($('.table_b ').html()).addClass('text-right');
           $('.pages-div-custom').remove();
           
      }
   });
} );
</script>



