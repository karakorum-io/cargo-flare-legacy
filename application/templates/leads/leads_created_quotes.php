<script src="<?php echo SITE_IN; ?>styles/new/assets/vendors/general/bootstrap/dist/js/bootstrap.min.js" type="text/javascript"></script> 
<link href="<?php echo SITE_IN; ?>styles/new/assets/vendors/custom/datatables/datatables.bundle.css" rel="stylesheet" type="text/css" />
<script src="<?php echo SITE_IN; ?>styles/new/assets/vendors/custom/datatables/datatables.bundle.js" type="text/javascript"></script>

<script type="text/javascript" src="<?php echo SITE_IN; ?>/ckeditor/ckeditor.js"></script>

<style>
.cd-secondary-nav {
  position: static;
 
}
.cd-secondary-nav .is-visible {
  visibility: visible;
  transform: scale(1);
  transition: transform 0.3s, visibility 0s 0s;
}
 .cd-secondary-nav.is-fixed {
    z-index: 9999;
    position: fixed;
    left: auto;
    top: 0;
    width: 1200px;
  background-color:#f4f4f4;
   
  } 
  .ui-dialog-buttonset button {
    color: #fff;
    background-color: #5867dd;
    border-color: #5867dd;
    border : none;
}
button.ui-dialog-titlebar-close {
    display: none;
}
.modal-content .modal-header .close:before {
    display: none;
}
span#ui-id-3 {
    color: #5867dd;
}
</style>


<div id="maildivnew">
    <div style="float: left;">
      <ul style="margin-top: 26px;">  
          <li style="margin-bottom: 14px;">Form Type <input value="1" id="attachPdf" name="attachTpe" type="radio"/><label for="attachPdf" style="margin-right: 2px; cursor:pointer;"> PDF</label><input value="0" id="attachHtml"  name="attachTpe" type="radio"/><label for="attachHtml" style="cursor:pointer"> HTML</label></li>
          <li style="margin-bottom: 11px;">Attachment(s): <span style="color:#24709F;" id="mail_att_new"></span></li>
      </ul>
   </div>
   <div style="text-align: right;">
    <div style="text-align: right;">
                        <img src="<?php echo SITE_IN; ?>/images/icons/add.gif"> <span style="margin-bottom: 3px;cursor:pointer; position: relative;bottom:4px; color:#24709F;" class="add_one_more_field_" >Add a Field</span>
      <ul>
         <li id="extraEmailsingle" style="margin-bottom: 6px;"><span>Email:<span style="color:red">*</span></span> <input type="text" id="mail_to_new" name="mail_to_new" class="form-box-combobox" ></li>
         <li style="margin-bottom: 6px;margin-top: 6px;margin-left: 292px; position:relative; display: none;" id="mailexttra"><input name="optionemailextra" class="form-box-combobox optionemailextra" type="text"><a href="#" style="position: absolute;margin-left: 2px;margin-top: 8px;" class="remove_2sd_field"><img id="singletop" style="width: 12px;height: 12px;" src="?php echo SITE_IN; ?>/images/icons/delete.png"></a></li>
         <li style="margin-bottom: 6px;"><span style="margin-right: 18px;">CC:</span> <input type="text" id="mail_cc_new" name="mail_cc_new" class="form-box-combobox" ></li>
         <li style="margin-bottom: 12px;"><span style="margin-right: 9px;">BCC:</span> <input type="text" id="mail_bcc_new" name="mail_bcc_new" class="form-box-combobox" ></li>
      </ul>
   </div>
   <div class="edit-mail-content" style="margin-bottom: 8px;">
      <div class="edit-mail-row" style="margin-bottom: 8px;">
         <div class="edit-mail-label">Subject:<span>*</span></div>
         <div class="edit-mail-field" style="width: 87%;"><input type="text" id="mail_subject_new" class="form-box-textfield" maxlength="255" name="mail_subject_new" style="width: 100%;"></div>
      </div>
      <div class="edit-mail-row mail_body_section">
         <div class="edit-mail-label">Body:<span>*</span></div>
         <div class="edit-mail-field" style="width: 87%;"><textarea class="form-box-textfield" style="width: 100%;" name="mail_body_new" id="mail_body_new"></textarea></div>
      </div>
   </div>
             <input type="hidden" name="form_id" id="form_id"  value=""/>
            <input type="hidden" name="entity_id" id="entity_id"  value=""/>
            <input type="hidden" name="skillCount" id="skillCount" value="1">
</div>
</div>







<script type="text/javascript">
$('.add_one_more_field_').on('click',function(){ 
   $('#mailexttra').css('display','block');
   return false;
});   
$('#singletop').on('click',function(){
     $('#mailexttra').css('display','none');
     $('.optionemailextra').val('');
});
</script>
<script type="text/javascript"> 
function validateEmail(sEmail) {
var res="",res1="",i;
var filter = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
   for (i = 0; i < sEmail.length; i++){
        if (filter.test(sEmail[i])){
           res += sEmail[i]; 
        }else {
          res1 += sEmail[i];
        }
    }   
    if(res1!==''){
        return false;
    }
}    
</script>

   <!--begin::Modal-->
<div class="modal fade" id="listmails" tabindex="-1" role="dialog" aria-labelledby="listmails_model" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="listmails_model">Email List</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i class="fa fa-times" aria-hidden="true"></i>
                </button>
            </div>
            <div class="modal-body">
                
    <div class="mail-list-label">
        <div id="adv_option" >Advance Options</div>
        <div style="clear: both"></div>
        <div id="adv_option_toggle" style="display: none; max-height: 122px;"> 
            <div style="float: left;">
                <ul>
                    <li style="margin-bottom: 16px;padding-top: 5px; color: forestgreen;font-weight: bold">Sending Options</li>
                    <li style="margin-bottom: 14px;">Form Type <input id="PDF" name="attachType" value="1" type="radio"/>
                        <label for="PDF" style="margin-right: 2px;">PDF</label>
                        <input id="HTML" name="attachType" value="0" type="radio"/>
                        <label for="HTML">HTML</label>
                    </li>
                    <li style="margin-bottom: 11px;">

                        

                        <label class="kt-checkbox kt-checkbox--bold kt-checkbox--brand">
                            <input name="combine" id="combine" type="checkbox"> Combine to single email
                            <span></span>
                        </label>
                       

                         </li>
                </ul>
            </div>
            <div style="text-align: right;">  
                <img src="/images/icons/add.gif"> <span style="margin-bottom: 3px;cursor:pointer; position: relative;bottom:4px; " class="add_field_button" >Add a Field</span>
                <ul id="adf">
                    <li id="extraEmail" style="margin-bottom: 6px;"><span>Email:<span style="color:red">*</span></span> <input name="optionemail" class="form-box-combobox optionemail" type="text" ></li>


                    <li style="margin-bottom: 6px;margin-top: 6px;margin-left: 292px; position:relative; display: none;" id="mailexttramultiple"><input name="optionemailextramultiple" class="form-box-combobox optionemailextramultiple" type="text"><a href="#" style="position: absolute;margin-left: 2px;margin-top: 8px;" class="remove_2sd_field"><img id="singletopmult" style="width: 12px;height: 12px;" src="/images/icons/delete.png"></a></li>
                    <li style="margin-bottom: 6px;"><span style="margin-right: 18px;">CC:</span> <input name="optioncc" class="form-box-combobox optioncc" type="text"></li>
                    <li style="margin-bottom: 12px;"><span style="margin-right: 9px;">BCC:</span> <input name="optionbcc" class="form-box-combobox optionbcc" type="text"></li>
                </ul>
            </div>  
        </div>



        <script type="text/javascript">
            var atttypem = <?php
            $sql = "SELECT attach_type FROM app_emailtemplates WHERE owner_id =" . getParentId();
            $result = $this->daffny->DB->query($sql);
            $row = $this->daffny->DB->fetch_row($result);
            echo $row['attach_type'];
            ?>;
            if (atttypem > 0) {
                $("#PDF").attr('checked', 'checked');
            } else {
                $("#HTML").attr('checked', 'checked');
            }
        </script>            
        <script type="text/javascript">
            $('.add_field_button').on('click', function () {
                $('#mailexttramultiple').css('display', 'block');
                $('#adf').css('margin-bottom', '25px');
                return false;
            });
            $('#singletopmult').on('click', function () {
                $('#mailexttramultiple').css('display', 'none');
                $('.optionemailextramultiple').val('');
                $('#adf').css('margin-bottom', '4px');
            });
            $("#adv_option").click(function () {

                if ($('#adv_option_toggle').css('display') == 'none') {
                    if ($('.remove_field').length > 0) {
                        $('#adv_option_toggle').css('max-height', '320px').slideDown().finish();
                    } else {
                        $('#adv_option_toggle').css('max-height', '320px').slideDown().finish();
                    }
                } else {
                    $('#adv_option_toggle').slideUp();
                }

            });
        </script>             
        <table  class="table-bordered table" >

            <tbody>

                <tr >

                    <td class="grid-head-left id-column" style="width: 70px;">
                        <?php if (isset($this->order)) : ?>
                            <?php echo $this->order->getTitle("id", "ID") ?>
                        <?php else : ?>ID<?php endif; ?>
                    </td>
                    <td class="shipper-column" style="width: 229px;">
                        <?php
                        if (isset($this->order)):
                            echo $this->order->getTitle("shipper", "Shipper");
                        else :
                            echo "Shipper";

                        endif;
                        ?>

                    </td>
                    <td  style="width: 90px;">
                        Attachment
                    </td>
                    <td class="grid-head-right" style="width: 29px;">
                        Action

                    </td>

                </tr>

            </tbody>

        </table>    

    </div>

    <div class="repeat-column"></div>

    

            </div>
            <div class="modal-footer">

                <div class="editmail"></div>
                
            </div>
        </div>
    </div>
</div>

<!--end::Modal-->
<!--div id="maildivnew">
    <table cellspacing="2" cellpadding="0" border="0">
        <tr>
            <td>@mail_to_new@</td>
        </tr>
        <tr>
            <td>@mail_subject_new@</td>
        </tr>
        <tr>
            <td>@mail_body_new@</td>
        </tr>
        <tr>
            <td colspan="2">&nbsp;<input type="hidden" name="form_id" id="form_id"  value=""/>
            <input type="hidden" name="entity_id" id="entity_id"  value=""/></td>
        </tr>
        
    </table>
</div-->
<!--div id="acc_search_dialog">
  <table cellpadding="0" cellspacing="0" border="0" width="100%" class="form-table">
    <tr>
      <td width="100%"><input type="text" name="app_search_text" id="acc_search_string" style="width:98%" class="form-box-textfield"/></td>
      <td><?=functionButton('Search', "accountSearch()")?></td>
    </tr>
    <tr>
      <td colspan="2">
        <ul id="acc_search_result"></ul>
      </td>
    </tr>
  </table>
</div-->
<!-- <div id="reassignCompanyDiv">
    <table cellspacing="2" cellpadding="0" border="0">
        <tr>
            <td valign="top">
                <select class="form-box-combobox" id="company_members">
                   <option value=""><?php print "Select One"; ?></option>
                    <?php foreach($this->company_members as $member) : ?>
                        <option value="<?= $member->id ?>"><?= $member->contactname ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
 </table>
</div>
 -->





<div id="appointmentDiv">
    <table cellspacing="2" cellpadding="0" border="0">
        <tr>
            <td valign="top">@app_date@</td>
        </tr>
        <tr>
            <td valign="top">@app_time@</td>
        </tr>
        <tr>
            <td valign="top">@app_note@</td>
        </tr>
 </table>
</div>
<?php //if ($this->status == Entity::STATUS_ACTIVE || $_GET['leads']=="search") : ?>
<script type="text/javascript">

function printSelectedQuoteForm() {
    
    if ($(".entity-checkbox:checked").length == 0) {
      Swal.fire('Quote not selected');
       
        return;
      }
    
    if ($(".entity-checkbox:checked").length > 1) {
           Swal.fire('Please select one quote');
       
        return;
      }
     var quote_id = $(".entity-checkbox:checked").val();
    
        form_id = $("#form_templates").val();
        if (form_id == "") {
         
              Swal.fire('Please choose form template');
           
        } else {

            $.ajax({
                url: BASE_PATH + 'application/ajax/entities.php',
                data: {
                    action: "print_quote",
                    form_id: form_id,
                    quote_id: quote_id
                },
                type: 'POST',
                dataType: 'json',
                beforeSend: function () {
                },
                success: function (retData) {
                    printOrder(retData.printform);
                }
            });
        }
    }


    function Processing_show() 
     {

        KTApp.blockPage({
        overlayColor: '#000000',
        type: 'v2',
        state: 'primary',
        message: '.'
        });

     }
  
  function emailSelectedQuoteFormNew() {
  
     if ($(".entity-checkbox:checked").length == 0) {
             Swal.fire('You have no selected items.');
       return;
        } 
    
    if ($(".entity-checkbox:checked").length > 1) {
           Swal.fire('Select only one quote.');
           
       return;
        }
    
    var entity_ids = $(".entity-checkbox:checked").val();
    
        form_id = $("#email_templates").val();
        if (form_id == "") {
             Swal.fire('Please choose email template');
        } else {
               
                $.ajax({
                    type: "POST",
                    url: BASE_PATH + "application/ajax/entities.php",
                    dataType: "json",
                    data: {
                        action: "emailQuoteNew",
                        form_id: form_id,
                        entity_id: entity_ids
                    },
                    success: function (res) {
                        if (res.success) {
              
               $("#form_id").val(form_id);
               $("#mail_to_new").val(res.emailContent.to);
               $("#mail_subject_new").val(res.emailContent.subject);
               $("#mail_body_new").val(res.emailContent.body);
               ckRefresher('new');
                                        
                              $("#entity_id").val(entity_ids);
                //$("#mail_file_name").html(file_name);
               $("#maildivnew").dialog("open");
              
                        } else {
                             Swal.fire("Can't send email. Try again later, please");
                            
                        }
                    },
                    complete: function (res) {
                     
                    }
                });


        }
    }

            $("#maildivnew").dialog({
            modal: true,
            width: 566,
            height: 340,
            title: "Email message",
            hide: 'fade',
            resizable: false,
            draggable: false,
            autoOpen: false,
            buttons: {
            "Submit": function () {
                         var sEmail =[$('#mail_to_new').val(),$('.optionemailextra').val(),$('#mail_cc_new').val(),$('#mail_bcc_new').val()];
                             if (validateEmail(sEmail)== false) {
                                 Swal.fire('Invalid Email Address');
                                 return false;
                            }
                        if($('#attachPdf').is(':checked')){
                             attach_type=$('#attachPdf').val();
                        }else{
                             attach_type=$('#attachHtml').val();
                        };
              $.ajax({
                url: BASE_PATH + 'application/ajax/entities.php',
                data: {
                  action: "emailQuoteNewSend",
                  form_id: $('#form_id').val(),
                  entity_id: $('#entity_id').val(),
                  mail_to: $('#mail_to_new').val(),
                  mail_cc: $('#mail_cc_new').val(),
                                            mail_bcc: $('#mail_bcc_new').val(),          
                  mail_extra: $('.optionemailextra').val(),
                  mail_subject: $('#mail_subject_new').val(),
                  mail_body: $('#mail_body_new').val(),
                                            attach_type:attach_type
                },
                type: 'POST',
                dataType: 'json',
                beforeSend: function () {
                  if ($('#mail_to_new').val() == ""|| $('#mail_subject_new').val() == ""||$('#mail_body_new').val() == "") {
                                            Swal.fire ('Empty Field(s)');
                    return false;
                  } else {
                    // // $("body").nimbleLoader("show");
                  }
                },
                success: function (response) {
                  // $("body").nimbleLoader("hide");
                  if (response.success == true) {
                    $("#maildivnew").dialog("close");
                    clearMailForm();
                    
                         }
                  
                },
                complete: function () {
                  // $("body").nimbleLoader("hide");
                }
              });
            },
            "Cancel": function () {
              $(this).dialog("close");
            }
            }
            });  



    function saveQuotes(email) {
    
        var ajData = [];
        $(".entity-checkbox").each(function(){
            if ($("#lead_tariff_"+$(this).val()).val() > 0) {
                ajData.push('{"entity_id":"'+$(this).val()+'","tariff":"'+$('#lead_tariff_'+$(this).val()).val()+'","deposit":"'+$('#lead_deposit_'+$(this).val()).val()+'"}');
            }
        });
    if (ajData.length == 0) {
      Swal.fire("You have no quote data");
      return;
    }
    $("body").nimbleLoader('show');
        $.ajax({
            type: "POST",
            url: "<?=SITE_IN?>application/ajax/entities.php",
            dataType: 'json',
            data: {
                action: 'saveQuotesNew',
                email: email,
                data: "["+ajData.join(",")+"]"
            },
      success: function(res) {
        if (res.success) {
          document.location.href = document.location.href;
        } else {
          Swal.fire("Can't save Quote(s)");
        }
      },
            complete: function(response) {
        $("body").nimbleLoader('hide');
            }
        });
    }
  
function convertToOrder() {
  //Swal.fire('test');
  if ($(".entity-checkbox:checked").length == 0) {

             Swal.fire("You have no selected items.");

      return false;        

        }

    if ($(".entity-checkbox:checked").length > 1) {

             Swal.fire("Error: You may convert one lead at a time.");

      return false;        

        }
    {
      var entity_ids = [];
      $(".entity-checkbox:checked").each(function(){
        entity_ids.push($(this).val());
      });   

             Processing_show();   
  
        $.ajax({
            type: "POST",
            url: "<?= SITE_IN ?>application/ajax/entities.php",
            dataType: "json",
            data: {
                action: "LeadtoOrderCreated",
                entity_ids: entity_ids.join(',')
            },
            success: function (result) {
                if (result.success == true) {
                  // document.location.reload();
          document.location.href = result.url;
          
                } else {
                    Swal.fire("Can't convert Order. Try again later, please");
                    KTApp.unblockPage();
                }
            },
            error: function (result) {
                Swal.fire("Can't convert Order. Try again later, please");
                KTApp.unblockPage();
            }
        });
    }
} 


function reassignOrdersDialog()
{
    if ($(".entity-checkbox:checked").length == 0) 
    {            
         Swal.fire("Leads not selected");            
         return;        
    }else{

           $("#reassignCompanyDiv").modal();
        } 

    //$("#reassignCompanyDiv").dialog("open");
   
}
  
   

function reassignOrders(member) 
{   
        var member_id = 0;    
        member_id = member;   
    if ( member_id == 0 ) 
    {     
      Swal.fire("You must select member to assign");      
      return;   
    }        
    if ($(".entity-checkbox:checked").length == 0) 
    {            
       Swal.fire("Leads not selected");            
         return;        
    }        
    //var entity_id = $(".entity-checkbox:checked").val();        
    var entity_ids = [];       
    //entity_ids.push(entity_id); 
     $(".entity-checkbox:checked").each(function(){
            entity_ids.push($(this).val());
        });
    $("#reassignCompanyDiv").nimbleLoader('show');
    $.ajax({            
         type: 'POST',            
         url: '<?= SITE_IN ?>application/ajax/entities.php',            
         dataType: "json",            
         data: {                
           action: 'reassign',                
         assign_id: member_id,                
         entity_ids: entity_ids.join(',')            
         },            
         success: function(response) 
         {               
            if (response.success) {                    
              window.location.reload();               
            } else {                   
              Swal.fire("Reassign failed. Try again later, please.");   
              $("#reassignCompanyDiv").nimbleLoader('hide');
              }            
          },           
          error: function(response) {                
             Swal.fire("Reassign failed. Try again later, please.");  
             $("#reassignCompanyDiv").nimbleLoader('hide');
             } ,
             complete: function (res) {

                        $("#reassignCompanyDiv").nimbleLoader('hide');

                    }
      }); 
  }
  
  $("#reassignCompanyDiv").dialog({
  modal: true,
  width: 300,
  height: 140,
  title: "Reassign Lead",
  hide: 'fade',
  resizable: false,
  draggable: false,
  autoOpen: false,
  buttons: {
    "Submit": function () {
      var member_id = $("#company_members").val();  
      reassignOrders(member_id);
    },
    "Cancel": function () {
      $(this).dialog("close");
    }
  }
});

function setAppointment()
{
    if ($(".entity-checkbox:checked").length == 0) 
    {            
       Swal.fire("Leads not selected");            
         return;        
    } 
    $("#appointmentDiv").dialog("open");
}
  
   
function setAppointmentData(app_date,app_time,notes) 
{   
          
    if ( app_date == '') 
    {     
      Swal.fire("You select appointment date.");      
      return;   
    }  
    if ( app_time == '') 
    {     
      Swal.fire("You select appointment time.");      
      return;   
    }  
    if ($(".entity-checkbox:checked").length == 0) 
    {            
       Swal.fire("Leads not selected");            
         return;        
    }        
    //var entity_id = $(".entity-checkbox:checked").val();        
    var entity_ids = [];       
    //entity_ids.push(entity_id); 
     $(".entity-checkbox:checked").each(function(){
            entity_ids.push($(this).val());
        });
  $("#appointmentDiv").nimbleLoader('show');
    $.ajax({            
         type: 'POST',            
         url: '<?= SITE_IN ?>application/ajax/entities.php',            
         dataType: "json",            
         data: {                
           action: 'setappointment', 
         app_date:app_date,
         app_time:app_time,
         notes:notes,
         entity_ids: entity_ids.join(',')            
         },            
         success: function(response) 
         {               
            if (response.success) {                    
              window.location.reload();               
            } else {                   
              Swal.fire("Set appointment failed. Try again later, please.");   
              $("#appointmentDiv").nimbleLoader('hide');
              }            
          },           
          error: function(response) {                
             Swal.fire("Set appointment. Try again later, please.");  
             $("#appointmentDiv").nimbleLoader('hide');
             } ,
             complete: function (res) {

                        $("#appointmentDiv").nimbleLoader('hide');

                    }
      }); 
  }
  
$("#appointmentDiv").dialog({
  modal: true,
  width: 400,
  height: 240,
  title: "Set Appointment",
  hide: 'fade',
  resizable: false,
  draggable: false,
  autoOpen: false,
  buttons: {
    "Submit": function () {
      var app_date = $("#app_date").val();  
      var app_time = $("#app_time").val();  
      var notes    = $("#app_note").val();  
      setAppointmentData(app_date,app_time,notes)
    },
    "Cancel": function () {
      $(this).dialog("close");
    }
  }
});

$(document).ready(function(){
        //$("#avail_pickup_date").datepicker({dateFormat: 'mm/dd/yy'});
    $("#app_date").datepicker({
      dateFormat: "yy-mm-dd",
            minDate: '+0'
      //setDate: "2012-10-09",
       
    });

  

  });
$("#listmails").dialog({

  modal: true,

  width: 500,

  height: 310,

  title: "Email List",

  hide: 'fade',

  resizable: false,

  draggable: false,

  autoOpen: false

});
</script>
<?php //endif; ?>
<div style="display:none" id="notes">notes</div>
<br/>


<div class="kt-portlet ">
	<div class="kt-portlet__body">
		<div  class="row">
			<div class="col-12 text-right buttion_mar">

				<?php 
				if ($this->status == Entity::STATUS_CARCHIVED || $this->status == Entity::STATUS_CDEAD){?>

				<!-- Check&nbsp;&nbsp;<span class="kt-link" onclick="checkAllEntities()">All</span>
				<span class="kt-link" onclick="uncheckAllEntities()">None</span> -->

				<?= functionButton('Reassign Leads', 'reassignOrdersDialog()',''.'btn-info') ?>
				<?= functionButton('Hold', 'changeStatusLeads('.Entity::STATUS_CONHOLD.')','','btn-warning') ?>
				<?php 
				if ($this->status == Entity::STATUS_CDEAD)
				{?>  
				<?= functionButton('Remove Do Not Call', 'changeStatusLeads('.Entity::STATUS_CACTIVE.')','','btn-danger') ?>
				<?= functionButton('Cancel', 'changeStatusLeads('.Entity::STATUS_CARCHIVED.')','','btn-danger') ?>
				<?php }else{?>
				<?= functionButton('Uncancel', 'changeStatusLeads('.Entity::STATUS_CACTIVE.')','','btn-danger') ?>
				<?php }?>

				<?php
				}
				else 
				{ ?>

				<!-- Check<span class="kt-link" onclick="checkAllEntities()">All</span>
				<span class="kt-link" onclick="uncheckAllEntities()">None</span> -->

				<?= functionButton('Print', 'printSelectedQuoteForm()','','btn_bright_blue btn-sm') ?>
				@form_templates@

				<?php print functionButton('Email', 'emailSelectedQuoteFormNew()','','btn_bright_blue btn-sm'); ?>

				@email_templates@
				<?php  //functionButton('Reassign Leads', 'reassign(\'top\')') ?>
				<?= functionButton('Reassign Leads', 'reassignOrdersDialog()','','btn-info btn-sm btn_bright_blue') ?>

				<?php if ($_GET['leads'] == 'cquoted'  || $_GET['leads'] == 'cfollow'){?>              
				<?php }?>

				<?= functionButton('Convert to Order', 'convertToOrder()','','btn btn-sm btn_bright_blue') ?>
				<?php //}?>

				<?php if($_GET['leads'] == 'cpriority'){?>
				<?= functionButton('Remove Priority', 'changeStatusLeads('.Entity::STATUS_CASSIGNED.')','','btn-danger') ?>
				<?php }else{?>
				<?= functionButton('Make Priority', 'changeStatusLeads('.Entity::STATUS_CPRIORITY.')','',' btn-sm btn_bright_blue') ?>
				<?php }?>

				<?php if($_GET['leads'] == 'conhold'){?>
				<?= functionButton('Remove Hold', 'changeStatusLeads('.Entity::STATUS_CASSIGNED.')','','btn-danger') ?>
				<?php }else{?>
				<?= functionButton('Hold', 'changeStatusLeads('.Entity::STATUS_CONHOLD.')','','btn-sm btn_light_green') ?>
				<?php }?>

				<?= functionButton('Do Not Call', 'changeStatusLeads('.Entity::STATUS_CDEAD.')','','btn-sm btn_bright_blue ') ?>
				<?= functionButton('Cancel', 'changeStatusLeads('.Entity::STATUS_CARCHIVED.')','','btn-dark btn-sm') ?>
				<!--td><?= functionButton('Cancel', 'cancel()') ?></td-->
				<?php }?>
			</div>
		</div>
   


<div id="nimble_dialog_button" >


	<table  id="cfollowtable" class="table table-bordered">

		<th class="grid-head-left" >
			<label class="kt-checkbox kt-checkbox--bold kt-checkbox--success kt-checkbox--all ">
			<input type="checkbox" onchange="if($(this).is(':checked')){ checkAllEntities() }else{ uncheckAllEntities() }"><span style="margin-left: 10px"></span></label>
			<?php if (isset($this->order)) : ?>
			<?=$this->order->getTitle("id", "ID")?>
			<?php else : ?>ID<?php endif; ?>
		</th>


        <th>
			<?php if (isset($this->order)) : ?>
			<?=$this->order->getTitle("quoted", "Quoted")?>
			<?php else : ?>Quoted<?php endif; ?>
        </th>
        <th>Notes</th>
        <th>
			<?php if (isset($this->order)) : ?>
			<?=$this->order->getTitle("shipperfname", "Shipper Information")?>
			<?php else : ?>Shipper<?php endif; ?>
        </th>
        <th>Vehicle Information</th>
        <th>
			<?php if (isset($this->order)) : ?>
			<?=$this->order->getTitle("Origincity", "Origin")?>
			<?php else : ?>Origin<?php endif; ?>/
			<?php if (isset($this->order)) : ?>
			<?=$this->order->getTitle("Destinationcity", "Destination")?>
			<?php else : ?>Destination<?php endif; ?>
        </th>
        <th class="grid-head-right">
			<?php if (isset($this->order)) : ?>
			<?=$this->order->getTitle("est_ship_date", "Est. Ship")?>
			<?php else : ?>Est. Ship<?php endif; ?>
        </th>
        <th>
           <?php if (isset($this->order)) : ?>
			<?=$this->order->getTitle("tariff", "Transport Cost")?>
			<?php else : ?>Tariff<?php endif; ?>
        </th>
		<?php if (count($this->entities) == 0): ?>
		<tr class="grid-body">
			<td colspan="8" align="center" class="grid-body-left grid-body-right"><i>No records</i></td>
		</tr>
		<?php endif; ?>
		<?php 
		$i=0;
		/*print "<pre>";
		print_r($this->entities);
		print "</pre>"; */
		$date_type_string = array(
			1 => "Estimated",
			2=> "Exactly",
			3 => "Not Earlier Than",
			4 => "Not Later Than"
		);

		$ship_via_string = array(
			1 => "Open",
			2 => "Enclosed",
			3 => "Driveaway"
		);
	$words = array("+", "-", " ","(",")");
	$wordsReplace   = array("", "", "", "", "");
  
	$searchData = array();

	foreach($this->entities as $i => $entity) :
		flush();
		$i++;
		$searchData[] = $entity['entityid'];
		$bgcolor = "#ffffff";
		if($i%2==0)
        $bgcolor = "";
      
		$number = "";
        if (trim($entity['prefix']) != "") {
            $number .= $entity['prefix'] . "-";
        }
        $number .= $entity['number'];
	?>
    <tr id="quote_tr_<?= $entity['entityid'] ?>" class="<?=($i == 0 ? " first-row" : "")?>">
		<td align="center" >
			<label class="kt-checkbox kt-checkbox--success" style="padding-left:20px;width:18px;height:18px;">
				<input type="checkbox" value="<?= $entity['entityid'] ?>" class="entity-checkbox">
				<span></span>
			</label>
			<?php if (!$entity['readonly']) : ?>
			</br>
			<?php endif; ?>
			<a href="<?= SITE_IN ?>application/leads/showcreated/id/<?= $entity['entityid'] ?>"><div class=" kt-badge  kt-badge--info kt-badge--inline kt-badge--pill order_id" style="margin-bottom: 2px;"><?= $number ?></div></a><br/>       
			<a href="<?= SITE_IN ?>application/quotes/history/id/<?= $entity['entityid'] ?>" class="kt-badge  kt-badge--success kt-badge--inline kt-badge--pill" >History</a><br/><br/>
			<?php /*if ($entity['status'] == Entity::STATUS_ARCHIVED) : ?>
                <a href="<?= SITE_IN ?>application/quotes/unarchived/id/<?= $entity['id'] ?>">UnArchive</a>
               <?php endif;*/ ?>
		</td>
		<?php  //$assigned = $entity->getAssigned(); ?>
         <td valign="top" width="15%">
			<div class="kt-font-warning"><?= date("m/d/y h:i a", strtotime($entity['quoted'])) ?> </div>
			<br>Assigned to:<br/> <strong class="kt-font-success"> <?= $entity['AssignedName'] ?></strong><br />
		</td>
		<td bgcolor="<?= $bgcolor ?>" width="5%">
		<?php
			$notes = new NoteManager($this->daffny->DB);
			$notesData = $notes->getNotesArrData($entity['entityid']);
			$countNewNotes = count($notesData[Note::TYPE_INTERNALNEW]);
			$countInternalNotes = count($notesData[Note::TYPE_INTERNAL]) + $countNewNotes;
            $NotesCount1 = 0;
            if(!is_null($entity['NotesCount1']))
               $NotesCount1 = $entity['NotesCount1'];            
			$NotesCount2 = 0;
			if(!is_null($entity['NotesCount2']))
				$NotesCount2 = $entity['NotesCount2'];
				$NotesCount3 = 0;
            if(!is_null($entity['NotesCount3']))
				$NotesCount3 = $entity['NotesCount3'];
				$countNewNotes =  $entity['NotesFlagCount3']; 
            ?>
			<?= notesIcon($entity['entityid'], $NotesCount1, Note::TYPE_FROM, $entity['status'] == Entity::STATUS_ARCHIVED) ?>
			<?= notesIcon($entity['entityid'], $NotesCount2, Note::TYPE_TO, $entity['status'] == Entity::STATUS_ARCHIVED) ?>
			<?= notesIcon($entity['entityid'], $NotesCount3, Note::TYPE_INTERNAL, $entity['status'] == Entity::STATUS_ARCHIVED,$countNewNotes) ?>
		</td>
		<?php
			if(trim($entity['shipperphone1'])!="")
            {                   
                $code = substr($entity['shipperphone1'], 0, 3);
                $areaCodeStr="";
                $areaCodeRows = $this->daffny->DB->selectRow("*", "app_area_code", "WHERE  AreaCode='".$code."'");
				if (!empty($areaCodeRows)) {
					$areaCodeStr = "<b>".$areaCodeRows['StdTimeZone']."-".$areaCodeRows['statecode']."</b>";
				}
            }
            if(trim($entity['shipperphone2'])!="")
            {
                $code = substr($entity['shipperphone2'], 0, 3);
                $areaCodeStr2="";                
                $areaCodeRows2 = $this->daffny->DB->selectRow("*", "app_area_code", "WHERE  AreaCode='".$code."'");
				if (!empty($areaCodeRows2)) {
					$areaCodeStr2 = "<b>".$areaCodeRows2['StdTimeZone']."-".$areaCodeRows2['statecode']."</b>";
				}
            }
            if($entity['shipperphone1_ext']!='') $phone1_ext = " <b>X</b> ".$entity['shipperphone1_ext'];
            if($entity['shipperphone2_ext']!='') $phone2_ext = " <b>X</b> ".$entity['shipperphone2_ext'];     
		?>
		<td>
			<span class="kt-font-bold kt-font-primary"><?= $entity['shipperfname'] ?> <?= $entity['shipperlname'] ?></span><br/>
			<?php if($entity['shippercompany']!=""){?>
				<b class="kt-font-bold kt-font-primary"><?= $entity['shippercompany']?></b><br />
			<?php } ?>
			<?php if($entity['shipperphone1']!=""){ $phone1 = str_replace($words, $wordsReplace, $entity['shipperphone1']); ?><div class="shipper_number"><a href="javascript:void(0);" onclick="showSMSDialog('<?php print $entity['entityid'];?>','<?= $phone1; ?>','Shipper');"><?= formatPhone($entity['shipperphone1']) ?></a> <?php }?>\
			<?= $phone1_ext;?>
			<?= $areaCodeStr;?><br/></div>
			<?php if($entity['shipperphone2']!=""){  $phone2 = str_replace($words, $wordsReplace, $entity['shipperphone2']);  ?>
			<div class="shipper_number">
				<a href="javascript:void(0);" onclick="showSMSDialog('<?php print $entity['entityid'];?>','<?= $phone2; ?>','Shipper');"><?= formatPhone($entity['shipperphone2']) ?></a>
				<?php } ?>
				<?= $phone2_ext;?>
				<?= $areaCodeStr2;?>
			</div>
			<?php if($entity['shipperemail']!=""){?>
				<?php if(strlen($entity['shipperemail']) < 25 ){?>
				<a href="mailto:<?= $entity['shipperemail'] ?>" TITLE="<?= $entity['shipperemail'] ?>"><div class="kt-font-bold kt-font-danger shipper_email">
					<?= $entity['shipperemail'] ?><br/></div>
				</a>
				<?php } else { ?>
					<a href="mailto:<?= $entity['shipperemail'] ?>"  TITLE="<?= $entity['shipperemail'] ?>"><div class="kt-font-bold kt-font-danger shipper_email" ><?= substr($entity['shipperemail'], 0, 25)  ?><br/></div></a>
				<?php  } ?>
			<?php } ?>
			<?php if($entity['referred_by'] != ""){?>
				Source <b><?= $entity['referred_by'] ?></b><br>
			<?php } ?>
		</td>
		<td>
			<?php
				$vehicleManager = new VehicleManager($this->daffny->DB);
				$vehicles = $vehicleManager->getVehiclesArrData($entity['entityid'], $entity['type']);
			?>
			<?php if (count($vehicles) == 0) { ?>
                <?php }elseif (count($vehicles) == 1) { ?>
                    <?php $vehicle = $vehicles[0]; ?>
                    <?= $vehicle['make']; ?> <?= $vehicle['model']; ?><br/>
                    <?= $vehicle['year']; ?> <?= $vehicle['type']; ?>&nbsp;<?= imageLink($vehicle['year'] . " " . $vehicle['make'] . " " . $vehicle['model'] . " " . $vehicle['type']) ?>
                    <br/>
                <?php } else { ?>
                    <span class="kt-link multi-vehicles">Multiple Vehicles<b><span style="color:#000000;">(<?php print count($vehicles);?>)</span></b></span>
                    
                    <div class="vehicles-info">
              <table width="100%"   cellpadding="0" cellspacing="1">
                         <tr>
                             <td  style="padding:3px;"><b><p>Year</p></b></td>
                             <td  style="padding:3px;"><b><p><?= Make ?></p></b></td>
               <td  style="padding:3px;"><b><p><?= Model ?></p></b></td>
                             <td  style="padding:3px;"><b><p><?= Type ?></p></b></td> 
               <td  style="padding:3px;"><b><p><?= Vin# ?></p></b></td>
                             <td  style="padding:3px;"><b><p><?= Inop ?></p></b></td>
              </tr>
                        <?php foreach ($vehicles as $key => $vehicle) : ?>
                            <tr>
                             <td bgcolor="#ffffff" style="padding:3px;"><?= $vehicle['year'] ?></td>
                             <td bgcolor="#ffffff" style="padding:3px;"><?= $vehicle['make'] ?></td>
               <td bgcolor="#ffffff" style="padding:3px;"><?= $vehicle['model'] ?></td> 
                             <td bgcolor="#ffffff" style="padding:3px;"><?= $vehicle['type'] ?></td>
                             <td bgcolor="#ffffff" style="padding:3px;"> <?php  print $vehicle['vin'] ?></td>
                             <td bgcolor="#ffffff" style="padding-left:5px;"> <?php  print $vehicle['inop']==0?"No":"Yes"; ?></td>
                           </tr>
                        <?php endforeach; ?>
            </table>
                    </div>
                    <br/>
                <?php  } ?>
                <br><span style="color:black;weight:bold;">Ship Via:  </span><span style="color:red;weight:bold;"><?php print ($entity['ship_via'] != 0) ? $ship_via_string[$entity['ship_via']] : ""; ?></span><br/>
                <!--<strong>Source: </strong><?php //print $source->company_name; ?>--->
            </td>
            
      <?php
      $o_link = "http://maps.google.com/maps?q=" . urlencode($entity['Orgincity'] . ",+" . $entity['Originstate']);
      $o_formatted = trim($entity['Orgincity'].', '.$entity['Originstate'].' '.$entity['Originzip'], ", ");
      
      $d_link = "http://maps.google.com/maps?q=" . urlencode($entity['Destinationcity'] . ",+" . $entity['Destinationstate']);
      $d_formatted = trim($entity['Destinationcity'].', '.$entity['Destinationstate'].' '.$entity['Destinationzip'], ", ");
      ?>
            <td  width="14%">
               <span class="kt-link"
                      onclick="window.open('<?= $o_link ?>', '_blank')"><?= $o_formatted ?></span> /<br/>
                <span class="kt-link"
                      onclick="window.open('<?= $d_link ?>')"><?= $d_formatted ?></span><br/>
                
                <?php if (is_numeric($entity['distance']) && ($entity['distance'] > 0)) { ?>
                    <?= number_format($entity['distance'], 0, "", "") ?> mi
                    <?php $cost = $entity['carrier_pay'] + $entity['pickup_terminal_fee'] + $entity['dropoff_terminal_fee'];
                          
                    ?>
                        ($ <?= number_format(($cost / $entity['distance']), 2, ".", ",") ?>/mi)
                <?php } ?>
                <span class="kt-link" onclick="mapIt(<?= $entity['entityid'] ?>);">Map it</span>
            </td>
            <td  width="12%">
              <span class="kt-badge  kt-badge--warning kt-badge--inline kt-badge--pill"><? print date("m/d/y", strtotime($entity['est_ship_date'])); ?></span></td>
      <td width="10%"  >

        <div  class="row">
          <div  class="col-md-12">
           <img src="<?= SITE_IN ?>images/icons/dollar.png" alt="Total Tariff" title="Total Tariff" width="16" height="16"/>
           :-<?= ("$ " . number_format((float)$entity['total_tariff_stored'], 2, ".", ",")) ?>
          </div>
        </div>


         <div  class="row">
          <div  class="col-md-12">
           <img src="<?= SITE_IN ?>images/icons/truck.png" alt="Carrier Pay" title="Carrier Pay" width="16" height="16"/>
           :-<?= ("$ " . number_format((float)$entity['carrier_pay_stored'], 2, ".", ",")) ?>
          </div>
        </div>



         <div  class="row">
          <div  class="col-md-12">
           <img src="<?= SITE_IN ?>images/icons/person.png" alt="Deposit" title="Deposit" width="16" height="16"/>
           :-<?= ("$ " . number_format((float)($entity['total_tariff_stored'] - $entity['carrier_pay_stored']), 2, ".", ",")) ?>
          </div>
        </div>


        
      </td>
    </tr>
<?php endforeach; ?>

 <?php
          $searchCount = count($searchData);
      if($searchCount>0){
         $_SESSION['searchData'] = $searchData;
         $_SESSION['searchCount'] = $searchCount;
         $_SESSION['searchShowCount'] = 0;
      }
  ?>
    
  
</table>    




<?php if ($this->status != Entity::STATUS_CARCHIVED) { ?>

<?php }else{?>
<table cellspacing="0" cellpadding="0" width="100%" class="control-bar">
  <tr>
    <td align="left">Check&nbsp;&nbsp;<span class="kt-link" onclick="checkAllEntities()">All</span>&nbsp;&nbsp;|&nbsp;&nbsp;<span class="kt-link" onclick="uncheckAllEntities()">None</span></td>
    <td width="100%">&nbsp;</td>
    <td><?php  //functionButton('Reassign Leads', 'reassign(\'top\')') ?>
             <?= functionButton('Reassign Leads', 'reassignOrdersDialog()','','btn-info btn-sm btn_bright_blue') ?>
        </td>
       <td><?= functionButton('Hold', 'changeStatusLeads('.Entity::STATUS_CONHOLD.')','','btn-warning') ?></td>
   <td valign="top"><?= functionButton('Uncancel', 'changeStatusLeads('.Entity::STATUS_CACTIVE.')','','btn-danger') ?></td>
         
  </tr>
</table>
<?php }?>
@pager@
</div>


<script type="text/javascript">
    $(document).ready(function() {
   $('#cfollow').DataTable({
       "lengthChange": false,
       "paging": false,
       "bInfo" : false,
       'drawCallback': function (oSettings) {
           $('#cfollow_wrapper').children('.row:first').children('.col-md-6:first').html('<div class="form-group row"><label class="col-form-label">Show: </label><select class=" records_per_page form-box-combobox form-control " style="width:100px;" onchange="setPagerLimit(this.value)"> <option value="10">10</option> <option value="25">25</option> <option value="50" selected="selected">50</option> <option value="100">100</option> <option value="200">200</option> <option value="500">500</option> </select></div>');
           $('#cfollow_wrapper').children('.row:last').children('.col-md-5').html($('.pager').clone()).addClass('text-left');
           $('#cfollow_wrapper').children('.row:last').children('.col-md-7').html($('.table_b ').html()).addClass('text-right');
           $('.pages-div-custom').remove();
           
      }
   });
} );
</script>

</div>
</div>

<!--  -->
    <div class="modal fade" id="reassignCompanyDiv" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Reassign Lead</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <i class="fa fa-times" aria-hidden="true"></i>
            </button>
        </div>
        <div class="modal-body">
    <select class="form-box-combobox" id="company_members">
    <option value=""><?php print "Select One"; ?></option>
    <?php foreach($this->company_members as $member) : ?>
    <?php if($member->status == "Active"){
    $activemember .="<option value= '".$member->id."'>" .$member->contactname ."</option>";
    }
   
    ?>
    <?php endforeach; ?>
    </select>
        </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="button" onclick="reassignOrders()" class="btn btn-primary">Save</button>
        </div> 
    </div>
    </div>
    </div>




<!--  -->


