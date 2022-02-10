<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="pluginops-tabs2" style="">
  <ul class="pluginops-tab2-links">
    <li class="active"><a href="#testCar_cf1" class="pluginops-tab2_link">Testimonials</a></li>
    <li><a href="#testCar_cf3" class="pluginops-tab2_link">Design</a></li>
    <li><a href="#testCar_cf2" class="pluginops-tab2_link">Carousel</a></li>
  </ul>
<div class="pluginops-tab2-content" style="box-shadow:none;">
	<div id="testCar_cf1" class="pluginops-tab2 active" style="min-width: 380px;">
          <div class="btn btn-blue" id="addNewTestimonialCarouselItem" > <span class="dashicons dashicons-plus-alt"></span> Add Testimonial </div>
          <br>
          <br>
          <ul class="sortableAccordionWidget    testimonialCarSlidesContainer"></ul>
	</div>
	<div id="testCar_cf2" class="pluginops-tab2" style="background: #fff; padding:20px 10px 20px 25px; width: 99%;">
        <label>Carousel Slides</label>
        <select class="tNSlides">
            <option value="1">One</option>
            <option value="2">Two</option>
            <option value="3">Three</option>
            <option value="4">Four</option>
            <option value="5">Five</option>
        </select>
        <br><br><hr><br>
        <label>AutoPlay</label>
        <select class="tCarAutoplay">
            <option value="true">Yes</option>
            <option value="false">No</option>
        </select>
        <br><br><hr><br>
        <label>Slider Delay </label>
		<input type="number" class="tCarDelay" id="tCarDelay" value=''>
        <span> (In seconds) </span>
        <br><br><br><hr><br>
        <label>Loop </label>
        <select class="tCarSlideLoop">
            <option value="true">Yes</option>
            <option value="false">No</option>
        </select>
        <br><br><hr><br>
        <label>Transition </label>
        <select class="tCarSlideTransition">
            <option value="backSlide">Slide</option>
            <option value="fade">Fade</option>
        </select>
        <br><br><hr><br>
        <label>Bullet Navigation </label>
        <select class="tCarPagination">
            <option value="true">Yes</option>
            <option value="false">No</option>
        </select>
        <br><br><hr><br>
        <label>Navigation Buttons </label>
        <select class="tCarNav">
            <option value="true">Yes</option>
            <option value="false">No</option>
        </select>
        <br><br><hr><br>
        <label>Hover Pause </label>
        <select class="tCarPause">
            <option value="true">Yes</option>
            <option value="false">No</option>
        </select>
        <br><br><hr><br>
	</div>
	<div id="testCar_cf3" class="pluginops-tab2" style="background: #fff; padding:20px 10px 20px 25px; width: 99%;">
		<br>
		<label> Icon Color :</label>
		<input type="text" class="color-picker_btn_two tcic" id="tcic">
		<br>
		<br>
      	<div>
            <h4>Icon Size 
            	<span class="responsiveBtn rbt-l " > <i class="fa fa-desktop"></i> </span>   
            	<span class="responsiveBtn rbt-m " > <i class="fa fa-tablet"></i> </span>
            	<span class="responsiveBtn rbt-s " > <i class="fa fa-mobile-phone"></i> </span>
            </h4>
            <div class="responsiveOps responsiveOptionsContainterLarge">
            	<label></label>
            	<input type="number" class="tcis" id="tcis" value=''>
            </div>
            <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
            	<label></label>
            	<input type="number" class="tcist" id="tcist" value=''>
            </div>
            <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
            	<label></label>
            	<input type="number" class="tcism" id="tcism" value=''>
            </div>
        </div>
      	<br>
      	<br>
      	<hr>
      	<br>
      	<label> Info Text Color :</label>
		<input type="text" class="color-picker_btn_two tcntc" id="tcntc">
		<br>
		<br>
      	<div>
            <h4> Info Text Size 
            	<span class="responsiveBtn rbt-l " > <i class="fa fa-desktop"></i> </span>   
            	<span class="responsiveBtn rbt-m " > <i class="fa fa-tablet"></i> </span>
            	<span class="responsiveBtn rbt-s " > <i class="fa fa-mobile-phone"></i> </span>
            </h4>
            <div class="responsiveOps responsiveOptionsContainterLarge">
            	<label></label>
            	<input type="number" class="tcnts" id="tcnts" value=''>
            </div>
            <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
            	<label></label>
            	<input type="number" class="tcntst" id="tcntst" value=''>
            </div>
            <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
            	<label></label>
            	<input type="number" class="tcntsm" id="tcntsm" value=''>
            </div>
        </div>
        <br><br><br>
        <label>Font Family :</label>
        <input class="tcntf gFontSelectorulpb" id="tcntf">
      	<br>
      	<br>
      	<hr>
      	<br>
      	<label> Testimonial Color :</label>
		<input type="text" class="color-picker_btn_two tcttc" id="tcttc">
		<br>
		<br>
      	<div>
            <h4>Testimonial Text Size 
            	<span class="responsiveBtn rbt-l " > <i class="fa fa-desktop"></i> </span>   
            	<span class="responsiveBtn rbt-m " > <i class="fa fa-tablet"></i> </span>
            	<span class="responsiveBtn rbt-s " > <i class="fa fa-mobile-phone"></i> </span>
            </h4>
            <div class="responsiveOps responsiveOptionsContainterLarge">
            	<label></label>
            	<input type="number" class="tctts" id="tctts" value=''>
            </div>
            <div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
            	<label></label>
            	<input type="number" class="tcttst" id="tcttst" value=''>
            </div>
            <div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
            	<label></label>
            	<input type="number" class="tcttsm" id="tcttsm" value=''>
            </div>
        </div>
        <br><br><br>
        <label>Font Family :</label>
        <input class="tcttf gFontSelectorulpb" id="tcttf">
      	<br>
      	<br>
      	<hr>
      	<br>
      	<label>Content Alignment :</label>
      	<select class="tcca">
      		<option value="left">Left</option>
      		<option value="center">Center</option>
      		<option value="right">Right</option>
      	</select>
      	<br>
      	<br>
      	<hr>
      	<br>
      	<label>Image Shape :</label>
      	<select class="tcir">
      		<option value="0">Square</option>
      		<option value="200px">Round</option>
      	</select>
      	<br>
      	<br><br>
      	<label>Image Size :</label>
      	<select class="tcisi">
      		<option value="35">Small</option>
      		<option value="60">Medium</option>
      		<option value="85">Large</option>
      	</select>
      	<br>
      	<br>
	</div>
</div>
</div>
<script type="text/javascript">
    (function($){

        var slideCountA = 480;
        jQuery('#addNewTestimonialCarouselItem').on('click',function(){
        jQuery('.testimonialCarSlidesContainer').append('<li><h3 class="handleHeader">Testimonial <span class="dashicons dashicons-trash slideRemoveButton" style="float: right;"></span> </h3><div  class="accordContentHolder wdt-fields">'+
        	'<label> Name : </label> <input type="text" value="" class="tcut tcn"> <br><br>'+
        	'<label> Job : </label> <input type="text" value="" class="tcut tcj"> <br><br>'+
        	'<label> Testimonial : </label> <br> <textarea type="text" value="" class="tcut tct" rows="5" cols="40"> Enter the testimonial comment here.</textarea> <br><br>'+
        	'<label>Testimonial Image :</label> <input id="image_location'+slideCountA+'" type="text" class="tcut tci upload_image_button'+slideCountA+'"  name="lpp_add_img_'+slideCountA+'" value=""  placeholder="Image URL" style="width:40%;" /> <label></label> <input id="image_location'+slideCountA+'" type="button" class="tcut upload_bg_btn_imageSlider" data-id="'+slideCountA+'" value="Upload" /> <br><br><br><br>'+
        	'<label> Link : </label> <input type="url" value="" class="tcut tcl"> <br><br>'+
          '<input type="hidden" value="" class="tcut tcia altTextField">       <input type="hidden" value="" class="tcut tcit titleTextField"> '+
        	'   </div></li>');

        jQuery( '.testimonialCarSlidesContainer' ).accordion( "refresh" );

        slideCountA++;
        jQuery('.closeWidgetPopup').trigger('click');
        
    }); // CLICK function ends here.

    jQuery(document).on('change','.tcut',function(){
    	jQuery('.closeWidgetPopup').trigger('click');
    });

    })(jQuery);
</script>