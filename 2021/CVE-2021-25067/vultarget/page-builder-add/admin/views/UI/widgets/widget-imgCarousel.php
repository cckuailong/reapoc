<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="pluginops-tabs2" style="">
  <ul class="pluginops-tab2-links">
    <li class="active"><a href="#imgCar_cf1" class="pluginops-tab2_link">Images</a></li>
    <li><a href="#imgCar_cf2" class="pluginops-tab2_link">Slider Settings</a></li>
  </ul>
<div class="pluginops-tab2-content" style="box-shadow:none;">
	<div id="imgCar_cf1" class="pluginops-tab2 active" style="min-width: 380px;">
          <div class="btn btn-blue" id="addNewCarouselItem" > <span class="dashicons dashicons-plus-alt"></span> Add Slide </div>
          <br>
          <br>
          <ul class="sortableAccordionWidget    carouselSlidesContainer"></ul>
	</div>
	<div id="imgCar_cf2" class="pluginops-tab2" style="background: #fff; padding:20px 10px; width: 99%;">
        <label>Slides In View</label>
        <input type="number" min="1" max="25" class="pbImgCarouselSlides">
        <br><br><hr><br>
        <label>AutoPlay</label>
        <select class="pbImgCarouselAutoplay">
            <option value="true">Yes</option>
            <option value="false">No</option>
        </select>
        <br><br><hr><br>
        <label>Slider Delay </label>
		<input type="number" class="pbImgCarouselDelay" id="pbImgCarouselDelay" value=''>
        <span> (In seconds) </span>
        <br><br><br><hr><br>
        <label>Loop </label>
        <select class="imgCarouselSlideLoop">
            <option value="true">Yes</option>
            <option value="false">No</option>
        </select>
        <br><br><hr><br>
        <label>Transition </label>
        <select class="imgCarouselSlideTransition">
            <option value="backSlide">Slide</option>
            <option value="fade">Fade</option>
        </select>
        <br><br><hr><br>
        <label>Bullet Navigation </label>
        <select class="imgCarouselPagination">
            <option value="true">Yes</option>
            <option value="false">No</option>
        </select>
        <br><br><hr><br>
        <label>Navigation Buttons </label>
        <select class="pbImgCarouselNav">
            <option value="true">Yes</option>
            <option value="false">No</option>
        </select>
        <br><br><hr><br>
        <label>Hover Pause </label>
        <select class="pbImgCarouselPause">
            <option value="true">Yes</option>
            <option value="false">No</option>
        </select>
        <br><br><hr><br>
	</div>
</div>
</div>
<script type="text/javascript">
    (function($){

        var slideCountA = 380;
        jQuery('#addNewCarouselItem').on('click',function(){
        jQuery('.carouselSlidesContainer').append(
            '<li>'+
                '<h3 class="handleHeader">Slide <span class="dashicons dashicons-trash slideRemoveButton" style="float: right;"></span> </h3>'+
                '<div  class="accordContentHolder">'+
                    '<label>Slide Image :</label>'+
                    '<input id="image_location'+slideCountA+'" type="text" class="carouselImgURL upload_image_button'+slideCountA+'"  name="lpp_add_img_'+slideCountA+'" value=""  placeholder="Insert Video URL here" style="width:100%;" />'+
                    '<input id="image_location'+slideCountA+'" type="button" class="upload_bg_btn_imageSlider" data-id="'+slideCountA+'" value="Upload" style="width:100%;" />'+
                    '<br><br><br><br><br><br><hr><br>'+
                    '<label> Slide URL </label>'+
                    '<input type="url" class="carouselImgLink">'+
                '</div>'+
            '</li>'
        );

        jQuery( '.carouselSlidesContainer' ).accordion( "refresh" );

        slideCountA++;
        jQuery('.closeWidgetPopup').trigger('click');

    }); // CLICK function ends here.


    })(jQuery);
</script>