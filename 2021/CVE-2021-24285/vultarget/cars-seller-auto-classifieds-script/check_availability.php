<?php


add_action( 'wp_ajax_nopriv_check_availability_carseller', 'check_availability_callback_carseller' );
add_action( 'wp_ajax_check_availability_carseller', 'check_availability_callback_carseller' );
function check_availability_callback_carseller() {
	$post_id = $_POST['post_id'] ;
	?>

	<div id="check_availabitlity_form" class="lead-form-container1" style="display:none">
       
         
<div class="lead-title">
  <h3>
  Request More Information</h3>
  <a href="javascript:void(0);" onclick="hideform();" class="btn btn-link tablet-hide-btn">x
  <span class="glyphicon glyphicon-remove-circle"></span></a>
</div>
<form id="lead-form" class="lead-form" role="form">
  <input hidden="" id="postid" name="postid" value="<?php echo $post_id;?>">
  <div class="form-group-double clearfix">
    <div class="form-group col-md-6">
      <input type="text" class="form-control empty-field" id="leadFirstName" name="FirstName" value="" placeholder="First name*" required >
    </div>
    <div class="form-group col-md-6">
      <input type="text" class="form-control empty-field" id="leadLastName"  name="LastName" value="" placeholder="Last name*" required>
    </div>
  </div>
  <div class="form-group">
    <label for="leadEmailAddress">Email address</label>
    <input type="email" class="form-control empty-field" id="leadEmailAddress" name="EmailAddress" value="" placeholder="Email address*" required>
  </div>
  <div class="form-group-double form-group-date-phone clearfix">
  <div class="form-group">
      <label for="leadPhoneNumber">Phone number</label>
      <input type="tel" class="form-control empty-field" id="leadPhoneNumber"  name="PhoneNumber" value="" placeholder="Phone number" >
    </div>
    
    

  </div>
  <div class="form-group">
    
    <textarea class="edit-off" id="leadMessage" name="message" required>Hi,
Could you please contact me for discuss?
Thanks
    </textarea>
    
  </div>
  
  <input type="submit" class="btn btn-cta-main btn-lg lead-form-submit" value="Send Message" />
</form><!-- END #lead-form -->


       
       </div>
       <?php 
	exit();
}