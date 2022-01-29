<?php
if (!defined('WPINC')) {
    die('Closed');
}
if(defined('REGMAGIC_ADDON')) include_once(RM_ADDON_ADMIN_DIR . 'views/template_rm_new_form_exerpt.php'); else {
?>
<script>
    function rm_handle_new_form_creation(event) {
        //alert(1);
        var f_name = jQuery('#form_name').val().toString().trim();
        if (f_name === "" || !f_name) {
           jQuery('.rm-create-new-from').animate({
        scrollTop: 0}, 400, 'swing', function(){
            jQuery('#form_name').fadeIn(100).fadeOut(1000, function () {
                jQuery('#form_name').css("border", "");
                jQuery('#form_name').fadeIn(100);
                jQuery('#form_name').val('');
            });
            jQuery('#form_name').css("border", "1px solid #FF6C6C");
        });            
            event.preventDefault();
        }
    }
    
    jQuery(document).ready(function(){
        jQuery('#rm_form_type').click(function(){
            if(jQuery(this).is(":checked")){
                jQuery("#form_type").val('rm_contact_form');
            }else{
                jQuery("#form_type").val('rm_reg_form');
            }
        });
    });
    
</script>

<form action="" id="rm_form_add_new_form" method="post" class="form-horizontal">

    <input type="hidden" name="rm_slug" value="rm_form_add_new_form" id="rm_form_add_new_form-element-1"/>
    <input type="hidden" name="form_type" value="rm_reg_form" id="form_type"/>

    <div class="rm-create-new-from">

        <div class="rmrow">

            <div class="rm-form-name rm-dbfl">

                <div class="rm-form-head rm-difl"><?php _e('Name of your form','custom-registration-form-builder-with-submission-manager'); ?></div> 
                <div class="rm-form-name-input rm-dbfl"><input type="text" value="" name="form_name" id="form_name" /></div>
                <div class="rm-form-name-input rm-dbfl"><input type="checkbox" value="" name="rm_form_type" id="rm_form_type" /><label for="rm_form_type"><?php _e('Turn off user registration for this form','custom-registration-form-builder-with-submission-manager'); ?></label></div>


            </div>

        </div>
        
        <div class="rm-create-new-from-btn-area">    
            <input type="submit" value="<?php _e("Save",'custom-registration-form-builder-with-submission-manager') ?>" name="submit" id="rm_submit_btn" onclick="rm_handle_new_form_creation(event)" class="rm_btn btn btn-primary">
        </div>
        
    </div>


</form>
<?php } ?>