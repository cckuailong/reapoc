<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="pluginops-tabs2" style="width: 107%; background: #fff;">
  <ul class="pluginops-tab2-links">
    <li class="active"><a href="#tsContentTab" class="pluginops-tab2_link">Content</a></li>
    <li><a href="#tsDesignTab" class="pluginops-tab2_link">Design</a></li>
  </ul>
<div class="pluginops-tab2-content" style="box-shadow:none;">
	<div id="tsContentTab" class="pluginops-tab2 active">
		<br>
		<label>Author Name : </label>
		<input type="text" class="tsAuthorName" style="width:300px; ">
		<br>
		<br>
		<br><br><br>
		<label>Job Title : </label>
		<input type="text" class="tsJob" style="width:300px; ">
		<br>
		<br>
		<br><br><br>
		<label>Company Name : </label>
		<input type="text" class="tsCompanyName" style="width:300px; ">
		<br>
		<br>
		<br><br><br>
		<label>Testimonial : </label>
		<textarea class="tsTestimonial" style="width:300px; "></textarea>
		<br>
		<br>
		<br><br><br>
		<label>Link (Author/Company) : </label>
		<input type="text" class="tsCompanyName" style="width:300px; ">
		<br>
		<br>
		<br><br><br>
		<label>Portrait Image :</label>
        <input id="image_location1" type="text" class=" tsUserImg upload_image_button2"  name='lpp_add_img_1' value='' placeholder='Insert Image URL here' style="width: 300px;" />
        <input id="image_location1" type="button" class="upload_bg" data-id="2" value="Upload" />
        <input type="text" value="" class="tsIa altTextField">
        <input type="text" value="" class="tsIt titleTextField">
        <br>
        <br>
      	

	</div>
	<div id="tsDesignTab" class="pluginops-tab2">
		<br>
		<label> Icon Color :</label>
		<input type="text" class="color-picker_btn_two tsIconColor" id="tsIconColor">
		<br>
		<br>
		<label>Icon Size :</label>   
      	<input type="number" name="tsIconSize" class="tsIconSize" id="tsIconSize" value='45'>
      	<br>
      	<br>
      	<hr>
      	<br>
      	<label> Text Color :</label>
		<input type="text" class="color-picker_btn_two tsTextColor" id="tsTextColor">
		<br>
		<br>
		<label>Text Size :</label>   
      	<input type="number" name="tsTextSize" class="tsTextSize" id="tsTextSize" value='15'>
      	<br>
      	<br>
      	<hr>
      	<br>
      	<label> Testimonial Color :</label>
		<input type="text" class="color-picker_btn_two tsTestimonialColor" id="tsTestimonialColor">
		<br>
		<br>
		<label>Testimonial Text Size :</label>   
      	<input type="number" name="tsTestimonialSize" class="tsTestimonialSize" id="tsTestimonialSize" value='16'>
      	<br>
      	<br>
      	<hr>
      	<br>
      	<label>Content Alignment :</label>
      	<select class="tsTextAlignment">
      		<option value="left">Left</option>
      		<option value="center">Center</option>
      		<option value="right">Right</option>
      	</select>
      	<br>
      	<br>
      	<hr>
      	<br>
      	<label>Image Shape :</label>
      	<select class="tsImageShape">
      		<option value="0">Square</option>
      		<option value="100px">Round</option>
      	</select>
      	<br>
      	<br>
	</div>
</div>
</div>