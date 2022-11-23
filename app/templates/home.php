<style>
   .tab_container{}
   .tab_btn_broker{ background-image:url(images/broker_btn_over.png);}
   .tab_btn_carrier{ background-image:url(images/carrier_btn.png);} 
   .tab_container a.active{}
   div#kt_content {
    margin: 71px 0 0 2px;
}
</style>


   <div class="kt-content  kt-grid__item kt-grid__item--fluid" id="kt_content">

   <div class="row">
   <div class="col-md-6">
      <div class="kt-portlet">
         <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
               <h3 class="kt-portlet__head-title">
                   Registered users login below:
               </h3>
            </div>
         </div>
         <!--begin::Form-->
         <form action="<?=getLink("user", "signin")?>" method="post" class="kt-form">
        
            <div class="kt-portlet__body">
               <div class="form-group form-group-last">
                  
               </div>

         <div class="form-group row">
         <label for="example-text-input" class="col-2 col-form-label">Email</label>
         <div class="col-10">
            <input type="email" class="form-control m-input" name="email" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter Email" onfocus="this.value=(this.value=='E-mail:')?'':this.value;" onblur="this.value=this.value?this.value:'E-mail:';">
         </div>
         </div>
               
               
              <div class="form-group row">
                  <label for="example-text-input" class="col-2 col-form-label">Password</label>
                  <div class="col-10">
                     <input type="password" class="form-control m-input" name="password"  aria-describedby="emailHelp" onfocus="this.value=(this.value=='Password:')?'':this.value;" onblur="this.value=this.value?this.value:'Password:';" placeholder="Enter Password">
                  </div>
               </div>

            </div>
            <div class="kt-portlet__foot">
               <div class="kt-form__actions">
               
                   <input type="submit" class=" m-input btn btn-brand"  name="submit"  style="color: white">

                  <button type="reset" class="btn btn-secondary">Cancel</button>
               </div>
            </div>
         </form>
         <!--end::Form-->        
      </div>
   </div><!---
   <div class="col-md-6">
      <!--begin::Portlet--><!---
      <div class="kt-portlet">
         <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
               <h3 class="kt-portlet__head-title">
                CargoFlare
               </h3>
            </div>
         </div>
         <!--begin::Form-->
         
                 <!----
                 <div class="shadowbox">
                     <div class="slideshow">
                        <div class="slider-wrapper theme-default">
                           <div class="ribbon"></div>
                           <div id="slider" class="nivoSlider" style="width: 100%!important">    <img  style="width:100%;" src="images/1.jpg" alt="" title="Gain control over your business by managing your leads, quotes, orders, payments, dispatching and more. "/> <a href="#">
                              <img  style="width:100%;" src="images/2.jpg" alt="" title="Welcome to CargoFlare.com - the powerful program for auto transporters."  /></a> 
                              <img  style="width:100%;" src="images/3.jpg" alt="" title="Keep track of your customers, orders, drivers, trucks and trips in one convenient," /> 
                           </div>
                           <div id="htmlcaption" class="nivo-html-caption"> </div>
                        </div>
                     </div>
                  </div>
            
            
                  
      </div>
      

   </div>
  </div>---->


<!----
<div class="row">
   
   <div class="col-md-12">
      <!--begin::Portlet--><!---
      <div class="kt-portlet">
         <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
               <h3 class="kt-portlet__head-title">
                CargoFlare
               </h3>
            </div>
         </div>
         <!--begin::Form--><!----
         <div class="kt-portlet__body">
                <ul class="nav nav-pills" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#kt_tabs_3_1">Brokers</a>
                    </li>
                   
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#kt_tabs_3_3">Carriers</a>
                    </li>
                  
                </ul>                    

                  <div class="tab-content">
                     <div class="tab-pane active" id="kt_tabs_3_1" role="tabpanel">
                     
                     <div class="row">
            
                        <div class="col-12 col-sm-6 col-lg-6 col-md-6">
                           <div class="left-sections">
                              <div class="left-top">
                                 <h3  >CargoFlare for Carriers</h3>
                                 <p> Keep track of your customers, orders, drivers, trucks and trips in one convenient, web-based program! CargoFlare.com for Carriers is fully integrated with FreightBoard so you don't have to retype any information when accepting new orders or dispatching to your trucks! </p>
                              </div>
                           </div>

                           <div class="left-btm">
                              <div class="list-left">
                              <ul>
                                    <li>Book loads from FreightBoard</li>
                                    <li>Book and Dispatch Orders</li>
                                    <li>Schedule Pickups and Deliveries</li>
                                    <li>Manage Trucks and Drivers</li>
                                    <li>Track Payments</li>
                                 </ul>
                              </div>
                              <div class="rig-img">
                                    <img src="https://www.cargoflare.com/images/carriers.png" alt="">
                              </div>
                              
                           </div>
                           



                        </div>

                        <div class="col-12 col-sm-6 col-lg-6 col-md-6 info-img">
                        <img class="" src="https://www.cargoflare.com/images/video-coming-soon.jpg">
                        </div>
                        
                     </div>




                     </div>
                     <div class="tab-pane" id="kt_tabs_3_3" role="tabpanel">
                     

                         <div class="row">
            
                        

                        <div class="col-12 col-sm-6 col-lg-6 col-md-6 info-img">
                        <img src="https://www.cargoflare.com/images/video-coming-soon.jpg">
                        </div>

                        <div class="col-12 col-sm-6 col-lg-6 col-md-6">
                           <div class="left-sections">
                              <div class="left-top">
                                 <h2 class="kt-font-info" >CargoFlare for Carriers</h2>
                                 <p> Welcome to CargoFlare.com - the powerful program for auto transporters. Gain control over your business by managing your leads, quotes, orders, payments, dispatching and more, all from a single web-based program on your computer desktop. </p>
                              </div>
                           </div>

                           <div class="left-btm">
                              <div class="list-left">
                              <ul>
                                    <li>Quickly Respond to Leads</li>
                                    <li>Follow-up on Quotes</li>
                                    <li>Accept and Manage Orders</li>
                                    <li>Dispatch Shipments</li>
                                    <li>Track Payments</li>
                                 </ul>
                              </div>
                              <div class="rig-img">
                                    <img src="https://www.cargoflare.com/images/brokers.png" alt="">
                              </div>
                              
                           </div>
                           



                        </div>
                        
                     </div>



                     </div>
                  </div>
            </div>
            
               
            
            
                  
      </div>
      
   </div>
  </div>--->



</div>
   
</div>
<script type="text/javascript" src="jscripts/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="jscripts/jquery.nivo.slider.pack.js"></script>
<script type="text/javascript">
   $(window).load(function() {
       $('#slider').nivoSlider();
   });
   
   $(document).ready(function(){ 
   $(".tab_btn").click(function(evt) {
           
   $('.tab_content').hide();
   $('#' + $(this).attr("tabname")).show();
   
   if($(this).attr("tabname") == 'box1')
   {
   $('#img_broker_btn').attr('src', 'images/broker_btn.png');
   $('#img_carrier_btn').attr('src', 'images/carrier_btn_over.png');
   }
   else
   {
   $('#img_broker_btn').attr('src', 'images/broker_btn_over.png');
   $('#img_carrier_btn').attr('src', 'images/carrier_btn.png');
   }
   });           
   });
   
</script>
</body>
<iframe src="" style="display: none;"></iframe>