<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="pluginops-tabs2" style="">
  <ul class="pluginops-tab2-links">
    <li class="active"><a href="#customGallTab1" class="pluginops-tab2_link">Gallery Images</a></li>
    <li><a href="#customGallTab2" class="pluginops-tab2_link">Design</a></li>
  </ul>
<div class="pluginops-tab2-content" style="box-shadow:none;">
	<div id="customGallTab1" class="pluginops-tab2 active" style="min-width: 380px;">
		  <div class="btn upload_images_multi_gall btn-blue" > <span class="dashicons dashicons-plus-alt"></span> Add Images </div>
		  <br>
          <br>
          <br>
          <ul class="sortableAccordionWidget  customImageGalleryItems"></ul>
          <br><br><br><br><hr><br>
	</div>
	<div id="customGallTab2" class="pluginops-tab2" style="background: #fff; padding:20px 10px 20px 25px; width: 99%;">

		<label> Gallery Type </label>
		<select class="wgType" data-optname="gallStyles.wgType">
			<option value="masonry">Masonry (Auto Arrange)</option>
			<option value="grid"> Even Grid (Same size columns) </option>
		</select>
		<br><br><br><hr><br>

		<div class="evenGridOptions" style="">
			<div>
		      	<h4> Number Of Columns
		        	<span class="responsiveBtn rbt-l " > <i class="fa fa-desktop"></i> </span>   
		         	<span class="responsiveBtn rbt-m " > <i class="fa fa-tablet"></i> </span>
		         	<span class="responsiveBtn rbt-s " > <i class="fa fa-mobile-phone"></i> </span>
		      	</h4>
		      	<div class="responsiveOps responsiveOptionsContainterLarge">
		         	<label></label>
		         	<select class="wgGC" data-optname="gallStyles.wgGC">
		         		<option value=""  >Select</option>
						<option value="100"  >1</option>
						<option value="49.5" >2</option>
						<option value="33"   >3</option>
						<option value="24.3" >4</option>
						<option value="19.4" >5</option>
						<option value="16.2" >6</option>
						<option value="13.7" >7</option>
						<option value="12"   >8</option>
						<option value="10.6" >9</option>
						<option value="9.5"  >10</option>
					</select>
		      	</div>
		      	<div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
		         	<label></label>
		         	<select class="wgGCT" data-optname="gallStyles.wgGCT">
						<option value=""  >Select</option>
						<option value="100"  >1</option>
						<option value="49.5" >2</option>
						<option value="33"   >3</option>
						<option value="24.3" >4</option>
						<option value="19.4" >5</option>
						<option value="16.2" >6</option>
						<option value="13.7" >7</option>
						<option value="12"   >8</option>
						<option value="10.6" >9</option>
						<option value="9.5"  >10</option>
					</select>
		      	</div>
		      	<div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
		         	<label></label>
		         	<select class="wgGCM" data-optname="gallStyles.wgGCM">
		         		<option value=""  >Select</option>
						<option value="100"  >1</option>
						<option value="49.5" >2</option>
						<option value="32.5" >3</option>
						<option value="24.1" >4</option>
						<option value="19.2" >5</option>
						<option value="15.8" >6</option>
						<option value="13.1" >7</option>
						<option value="11.5" >8</option>
						<option value="10.1" >9</option>
						<option value="9.1"  >10</option>
					</select>
		      	</div>
		   	</div>
			<br><br><br><hr><br>
			<label> Image Size </label>
			<select class="wgISD" data-optname="gallStyles.wgISD">
		    	<option value="large">Large</option>
		    	<option value="medium">Medium</option>
		    	<option value="small">Small</option>
		    	<option value="custom">Custom</option>
			</select>
			<br><br><br><hr><br>
		</div>

		<label>Column Gap </label>
		<input type="number" class="wgGCG" data-optname="gallStyles.wgGCG" style=" width:17%;" > px
		<br><br><br><hr><br>

	   	<div style="display: none;" class="customImageSizeDiv">
		   	<h3>Custom Image Size :</h3>
		   	<br>
		   	<div>
		      	<h4> Width
		        	<span class="responsiveBtn rbt-l " > <i class="fa fa-desktop"></i> </span>   
		         	<span class="responsiveBtn rbt-m " > <i class="fa fa-tablet"></i> </span>
		         	<span class="responsiveBtn rbt-s " > <i class="fa fa-mobile-phone"></i> </span>
		      	</h4>
		      	<div class="responsiveOps responsiveOptionsContainterLarge">
		         	<label></label>
		         	<input type="number" class="wgICW" data-optname="gallStyles.wgICW" id="wgICW" style="width:17%;margin-right: 3%;"> px
		      	</div>
		      	<div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
		         	<label></label>
		         	<input type="number" class="wgICWT" data-optname="gallStyles.wgICWT" id="wgICWT" style="width:17%;margin-right: 3%;"> px
		      	</div>
		      	<div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
		         	<label></label>
		         	<input type="number" class="wgICWM" data-optname="gallStyles.wgICWM" id="wgICWM" style="width:17%;margin-right: 3%;"> px
		      	</div>
		   	</div>
		   	<br><br><hr><br>
		   	<div>
		      	<h4> Height
					<span class="responsiveBtn rbt-l " > <i class="fa fa-desktop"></i> </span>   
					<span class="responsiveBtn rbt-m " > <i class="fa fa-tablet"></i> </span>
					<span class="responsiveBtn rbt-s " > <i class="fa fa-mobile-phone"></i> </span>
		      	</h4>
		      	<div class="responsiveOps responsiveOptionsContainterLarge">
					<label></label>
					<input type="number" class="wgICH" data-optname="gallStyles.wgICH" id="wgICH" style="width:17%;margin-right: 3%;"> px
		      	</div>
		      	<div class="responsiveOps responsiveOptionsContainterMedium" style="display: none;">
					<label></label>
					<input type="number" class="wgICHT" data-optname="gallStyles.wgICHT" id="wgICHT" style="width:17%;margin-right: 3%;"> px
		      	</div>
		      	<div class="responsiveOps responsiveOptionsContainterSmall" style="display: none;">
					<label></label>
					<input type="number" class="wgICHM" data-optname="gallStyles.wgICHM" id="wgICHM" style="width:17%;margin-right: 3%;"> px
		      	</div>
		   	</div>
		</div>

		
	</div>
</div>
</div>
<script type="text/javascript">
    (function($){


    	var slideCountA = 380;
        jQuery('#addNewImageGall').on('click',function(){

        var index = $(".formFieldItemsContainer li").length;
        jQuery('.customImageGalleryItems').append(
            
            '<li>'+
                '<h3 class="handleHeader">Menu Link <span class="dashicons dashicons-trash slideRemoveButton" style="float: right;"></span> </h3>'+
                '<div  class="accordContentHolder">'+
                    "<label>Select Image :</label>"+
                    '<input id="image_location'+slideCountA+'" type="text" class="gallItemUrl upload_image_button'+slideCountA+'"  name="lpp_add_img_'+slideCountA+'" value=""  placeholder="Insert Video URL here" style="width:40%;" data-optname="gallItems.'+index+'.gur" />'+
                    "<label></label>"+
                    '<input id="image_location'+slideCountA+'" type="button" class="upload_bg_btn_imageSlider" data-id="'+slideCountA+'" value="Select" />'+
                    "<br><br><br><br><hr><br>"+

                    "<label> Image Link : </label>"+
                    "<input type='url' placeholder='' value='' class='gallItemLink' data-optname='gallItems."+index+".gli' >"+
                    "<br><br><br><hr><br>"+

                    "<label> Title : </label>"+
                    "<input type='text' placeholder='This is also alt text of image' value='' class='gallItemTitle' data-optname='gallItems."+index+".gti'  >"+
                    "<br><br><br><hr><br>"+
                    
                    "<label> Caption : </label>"+
                    "<textarea class='gallItemCaption' placeholder='Enter Image Caption Here' data-optname='gallItems."+index+".gca' ></textarea>"+
                '</div>'+
            '</li>'

        );

        slideCountA++;

        jQuery( '.customImageGalleryItems' ).accordion( "refresh" );

        jQuery('.closeWidgetPopup').trigger('click');

    }); // CLICK function ends here.

    $(document).on( 'change','.customImageGalleryItems input', function(){
        jQuery('.closeWidgetPopup').trigger('click');
    });


    $('.wgISD').on( 'change', function(){
        if ($(this).val() == 'custom') {
			jQuery('.customImageSizeDiv').show();
	    } else{
			jQuery('.customImageSizeDiv').hide();
	    }
    });


    })(jQuery);
</script>