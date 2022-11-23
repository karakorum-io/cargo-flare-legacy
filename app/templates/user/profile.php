<? include_once("menu.php"); ?>
<style type="text/css">
  
</style>
@flash_message@

<form action="<?=getLink("user", "profile")?>" method="post" class="kt-form">
    <div class="form-group row">
        <div class="form-group profile_user col-12 col-sm-6 col-12 col-sm-6">
         <div class="">
          @username@
         </div>
       </div>

       <div class="form-group profile_user col-12 col-sm-6">
         <div class="">
         @contactname@
         </div>
        </div>

      <div class="form-group profile_user col-12 col-sm-6">
         <div class="">
          @phone@
         </div>
     </div>
    
     <div class="form-group profile_user col-12 col-sm-6">
         <div class="">
         @email@
         </div>
     </div>

            


       <div class="form-group profile_user col-12 col-sm-6">
       
        <div class="">
 
           @password@
         </div>
          Leave <strong style="color: red"> 'password' </strong> empty if you do not want to change it.
     </div>

            <div class="form-group profile_user col-12 col-sm-6">
             <div class="">
        @password_confirm@
         </div>
     </div>


</div>
    <?php echo submitButtons(getLink()); ?>
    
</form>
<script type="text/javascript">
//<![CDATA[
$(function(){
    $('#username').focus();
});
//]]>
</script>