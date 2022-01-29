<?php
if (!defined('WPINC')) {
    die('Closed');
}
if(defined('REGMAGIC_ADDON'))
    include_once(RM_ADDON_ADMIN_DIR . 'views/template_rm_field_picker.php');
else {
    if($data->form->form_type==1 && (!in_array('Username', $primary_fields) || !in_array('UserPassword', $primary_fields))): ?>
    <div class="rm-field-tabs-row rm-dbfl">
        <div class="rm-field-tab-cat">Account Fields</div>
        <div id="rm_common_fields_tab">
            <?php if(!in_array('Username', $primary_fields)) : ?>
                <div title="Username" class="rm_button_like_links" onclick="add_user_field_to_page('Username')"><a href="javascript:void(0)"><?php _e("Account Username",'custom-registration-form-builder-with-submission-manager'); ?></a></div> 
            <?php endif; ?>    
            <?php if(!in_array('UserPassword', $primary_fields)) : ?>
                <div title="User Password" class="rm_button_like_links" onclick="add_user_field_to_page('UserPassword')"><a href="javascript:void(0)"><?php _e("Account Password",'custom-registration-form-builder-with-submission-manager'); ?></a></div> 
            <?php endif; ?>
        </div>
    </div> 
<?php endif; ?>
<div class="field-selector-pills">
    
    <div style="display:none;"  id="rm_fields_down"><span><i class="material-icons">arrow_downward</i></span></div>

    <div class="rm-field-tabs-row rm-dbfl">
        <div class="rm-field-tab-cat"><?php echo RM_UI_Strings::get("LABEL_COMMON_FIELDS"); ?></div>
        <div id="rm_common_fields_tab">
            <div title="<?php echo RM_UI_Strings::get("FIELD_HELP_TEXT_Textbox"); ?>" class="rm_button_like_links" onclick="add_new_field_to_page('Textbox')"><a href="javascript:void(0)"><?php echo RM_UI_Strings::get("FIELD_TYPE_TEXT"); ?></a></div>
            <div title="<?php echo RM_UI_Strings::get("FIELD_HELP_TEXT_Select"); ?>" class="rm_button_like_links" onclick="add_new_field_to_page('Select')"><a href="javascript:void(0)"><?php echo RM_UI_Strings::get("FIELD_TYPE_DROPDOWN"); ?></a></div>
            <div title="<?php echo RM_UI_Strings::get("FIELD_HELP_TEXT_Radio"); ?>" class="rm_button_like_links" onclick="add_new_field_to_page('Radio')"><a href="javascript:void(0)"><?php echo RM_UI_Strings::get("FIELD_TYPE_RADIO"); ?></a></div>  
            <div title="<?php echo RM_UI_Strings::get("FIELD_HELP_TEXT_Textarea"); ?>" class="rm_button_like_links" onclick="add_new_field_to_page('Textarea')"><a href="javascript:void(0)"><?php echo RM_UI_Strings::get("FIELD_TYPE_TEXTAREA"); ?></a></div>  
            <div title="<?php echo RM_UI_Strings::get("FIELD_HELP_TEXT_Checkbox"); ?>" class="rm_button_like_links" onclick="add_new_field_to_page('Checkbox')"><a href="javascript:void(0)"><?php echo RM_UI_Strings::get("FIELD_TYPE_CHECKBOX"); ?></a></div>
        </div>
    </div> 
    <div class="rm-field-tabs-row rm-dbfl">
        <div class="rm-field-tab-cat"><?php echo RM_UI_Strings::get("LABEL_SPECIAL_FIELDS"); ?></div>
        <div id="rm_special_fields_tab">
            <div title="<?php echo RM_UI_Strings::get("FIELD_HELP_TEXT_jQueryUIDate"); ?>" class="rm_button_like_links" onclick="add_new_field_to_page('jQueryUIDate')"><a href="javascript:void(0)"><?php echo RM_UI_Strings::get("FIELD_TYPE_DATE"); ?></a></div>
            <div title="<?php echo RM_UI_Strings::get("FIELD_HELP_TEXT_Email"); ?>" class="rm_button_like_links" onclick="add_new_field_to_page('Email')">       <a href="javascript:void(0)"><?php echo RM_UI_Strings::get("FIELD_TYPE_EMAIL"); ?></a></div>                    
            <div title="<?php echo RM_UI_Strings::get("FIELD_HELP_TEXT_Password"); ?>" class="rm_button_like_links" onclick="add_new_field_to_page('Password')">    <a href="javascript:void(0)"><?php echo RM_UI_Strings::get("FIELD_TYPE_PASSWORD"); ?></a></div>
            <div title="<?php echo RM_UI_Strings::get("FIELD_HELP_TEXT_Number"); ?>" class="rm_button_like_links" onclick="add_new_field_to_page('Number')">      <a href="javascript:void(0)"><?php echo RM_UI_Strings::get("FIELD_TYPE_NUMBER"); ?></a></div>
            <div title="<?php echo RM_UI_Strings::get("FIELD_HELP_TEXT_Country"); ?>" class="rm_button_like_links" onclick="add_new_field_to_page('Country')">     <a href="javascript:void(0)"><?php echo RM_UI_Strings::get("FIELD_TYPE_COUNTRY"); ?></a></div>
            <div title="<?php echo RM_UI_Strings::get("FIELD_HELP_TEXT_Timezone"); ?>" class="rm_button_like_links" onclick="add_new_field_to_page('Timezone')">    <a href="javascript:void(0)"><?php echo RM_UI_Strings::get("FIELD_TYPE_TIMEZONE"); ?></a></div>
            <div title="<?php echo RM_UI_Strings::get("FIELD_HELP_TEXT_Terms"); ?>" class="rm_button_like_links" onclick="add_new_field_to_page('Terms')">       <a href="javascript:void(0)"><?php echo RM_UI_Strings::get("FIELD_TYPE_T_AND_C"); ?></a></div>
            <div title="<?php echo RM_UI_Strings::get("FIELD_HELP_TEXT_Price"); ?>" class="rm_button_like_links" onclick="add_new_field_to_page('Price')">       <a href="javascript:void(0)"><?php echo RM_UI_Strings::get("FIELD_TYPE_PRICE"); ?></a></div>
            <div title="<?php echo RM_UI_Strings::get("FIELD_HELP_TEXT_Address"); ?>" class="rm_button_like_links"><a href="javascript:void(0)" onclick="add_new_field_to_page('Address')"><?php echo RM_UI_Strings::get("FIELD_TYPE_ADDRESS"); ?></a></div>
            <div title="<?php echo RM_UI_Strings::get("FIELD_HELP_TEXT_Mobile"); ?>"        class="rm_button_like_links" onclick="add_new_field_to_page('Mobile')"><a href="javascript:void(0)"><?php echo RM_UI_Strings::get("FIELD_TYPE_MOBILE"); ?></a></div>
            <div title="<?php echo RM_UI_Strings::get("FIELD_HELP_TEXT_Hidden"); ?>"        class="rm_button_like_links" onclick="add_new_field_to_page('Hidden')"><a href="javascript:void(0)"><?php echo RM_UI_Strings::get("FIELD_TYPE_HIDDEN"); ?></a></div>
            <div title="<?php echo RM_UI_Strings::get("FIELD_HELP_TEXT_Image"); ?>"         class="rm_button_like_links" onclick="add_new_field_to_page('ESign')"><a href="javascript:void(0)"><?php _e('ESign','custom-registration-form-builder-with-submission-manager'); ?></a></div>
            <?php if($is_privacy_added==0):  ?>
            <div title="<?php _e('Privacy Policy', 'custom-registration-form-builder-with-submission-manager'); ?>"        class="rm_button_like_links" onclick="add_new_field_to_page('Privacy')"><a href="javascript:void(0)"><?php _e('Privacy Policy', 'custom-registration-form-builder-with-submission-manager'); ?></a></div>
            <?php endif; ?>
        </div>
    </div>

    <div class="rm-field-tabs-row rm-dbfl">
        <div class="rm-field-tab-cat"> <?php echo RM_UI_Strings::get("LABEL_PROFILE_FIELDS"); ?></div>
        <div id="rm_profile_fields_tab">
            <div title="<?php echo RM_UI_Strings::get("FIELD_HELP_TEXT_Fname"); ?>" class="rm_button_like_links" onclick="add_new_field_to_page('Fname')"><a href="javascript:void(0)"><?php echo RM_UI_Strings::get("FIELD_TYPE_FNAME"); ?></a></div>
            <div title="<?php echo RM_UI_Strings::get("FIELD_HELP_TEXT_Lname"); ?>" class="rm_button_like_links" onclick="add_new_field_to_page('Lname')"><a href="javascript:void(0)"><?php echo RM_UI_Strings::get("FIELD_TYPE_LNAME"); ?></a></div>
            <div title="<?php echo RM_UI_Strings::get("FIELD_HELP_TEXT_BInfo"); ?>" class="rm_button_like_links" onclick="add_new_field_to_page('BInfo')"><a href="javascript:void(0)"><?php echo RM_UI_Strings::get("FIELD_TYPE_BINFO"); ?></a></div>
            <div title="<?php echo RM_UI_Strings::get("FIELD_HELP_TEXT_Nickname"); ?>" class="rm_button_like_links" onclick="add_new_field_to_page('Nickname')"><a href="javascript:void(0)"><?php echo RM_UI_Strings::get("FIELD_TYPE_NICKNAME"); ?></a></div>
            <div title="<?php echo RM_UI_Strings::get("FIELD_HELP_TEXT_Website"); ?>" class="rm_button_like_links" onclick="add_new_field_to_page('Website')"><a href="javascript:void(0)"><?php echo RM_UI_Strings::get("FIELD_TYPE_WEBSITE"); ?></a></div>
        </div>

    </div>
    
    <div class="rm-field-tabs-row rm-fields-tab-woo rm-dbfl">
        <?php if ( class_exists( 'WooCommerce' ) ): ?>
        <div class="rm-field-tab-cat"> <?php echo __('WooCommerce Fields', 'custom-registration-form-builder-with-submission-manager'); ?></div>
        <div id="rm_wc_fields_tab">
            <div title="<?php echo __('WooCommerce Billing Field', 'custom-registration-form-builder-with-submission-manager'); ?>" class="rm_button_like_links" onclick="add_new_field_to_page('WCBilling')"><a href="javascript:void(0)"><?php echo __('WooCommerce Billing Field', 'custom-registration-form-builder-with-submission-manager'); ?></a></div>
            <div title="<?php echo __('WooCommerce Shipping Field', 'custom-registration-form-builder-with-submission-manager'); ?>" class="rm_button_like_links" onclick="add_new_field_to_page('WCShipping')"><a href="javascript:void(0)"><?php echo __('WooCommerce Shipping Field', 'custom-registration-form-builder-with-submission-manager'); ?></a></div>
            <div title="<?php echo __('Billing Phone Number', 'custom-registration-form-builder-with-submission-manager'); ?>" class="rm_button_like_links" onclick="add_new_field_to_page('WCBillingPhone')"><a href="javascript:void(0)"><?php echo __('Billing Phone Number', 'custom-registration-form-builder-with-submission-manager'); ?></a></div>
        </div>
        <?php else: ?>
        <div class="rm-field-tab-cat"> <?php echo __('WooCommerce Fields (Install and activate WooCommerce to enable these fields)', 'custom-registration-form-builder-with-submission-manager'); ?></div>
        <div id="rm_wc_fields_tab">
            <div title="<?php echo __('WooCommerce Billing Field', 'custom-registration-form-builder-with-submission-manager'); ?>" class="rm_button_like_links"><a href="javascript:void(0)" class="rm_deactivated"><?php echo __('WooCommerce Billing Field', 'custom-registration-form-builder-with-submission-manager'); ?></a></div>
            <div title="<?php echo __('WooCommerce Shipping Field', 'custom-registration-form-builder-with-submission-manager'); ?>" class="rm_button_like_links"><a href="javascript:void(0)" class="rm_deactivated"><?php echo __('WooCommerce Shipping Field', 'custom-registration-form-builder-with-submission-manager'); ?></a></div>
            <div title="<?php echo __('Billing Phone Number', 'custom-registration-form-builder-with-submission-manager'); ?>" class="rm_button_like_links"><a href="javascript:void(0)" class="rm_deactivated"><?php echo __('Billing Phone Number', 'custom-registration-form-builder-with-submission-manager'); ?></a></div>
        </div>
        <?php endif; ?>
    </div>

    <div class="rm-field-tabs-row rm-dbfl">
        <div class="rm-field-tab-cat"> <?php echo RM_UI_Strings::get("LABEL_SOCIAL_FIELDS"); ?></div>
        <div id="rm_social_fields_tab">
            <div title="<?php echo RM_UI_Strings::get("FIELD_HELP_TEXT_Facebook"); ?>" class="rm_button_like_links" onclick="add_new_field_to_page('Facebook')"><a href="javascript:void(0)"><?php echo RM_UI_Strings::get("FIELD_TYPE_FACEBOOK"); ?></a></div>
            <div title="<?php echo RM_UI_Strings::get("FIELD_HELP_TEXT_Twitter"); ?>" class="rm_button_like_links" onclick="add_new_field_to_page('Twitter')"><a href="javascript:void(0)"><?php echo RM_UI_Strings::get("FIELD_TYPE_TWITTER"); ?></a></div>
            <div title="<?php echo RM_UI_Strings::get("FIELD_HELP_TEXT_Google"); ?>" class="rm_button_like_links" onclick="add_new_field_to_page('Google')"><a href="javascript:void(0)"><?php echo RM_UI_Strings::get("FIELD_TYPE_GOOGLE"); ?></a></div>
            <div title="<?php echo RM_UI_Strings::get("FIELD_HELP_TEXT_Instagram"); ?>" class="rm_button_like_links" onclick="add_new_field_to_page('Instagram')"><a href="javascript:void(0)"><?php echo RM_UI_Strings::get("FIELD_TYPE_INSTAGRAM"); ?></a></div>
            <div title="<?php echo RM_UI_Strings::get("FIELD_HELP_TEXT_Linked"); ?>" class="rm_button_like_links" onclick="add_new_field_to_page('Linked')"><a href="javascript:void(0)"><?php echo RM_UI_Strings::get("FIELD_TYPE_LINKED"); ?></a></div>
            <div title="<?php echo RM_UI_Strings::get("FIELD_HELP_TEXT_Youtube"); ?>" class="rm_button_like_links" onclick="add_new_field_to_page('Youtube')"><a href="javascript:void(0)"><?php echo RM_UI_Strings::get("FIELD_TYPE_YOUTUBE"); ?></a></div>
            <div title="<?php echo RM_UI_Strings::get("FIELD_HELP_TEXT_VKontacte"); ?>" class="rm_button_like_links" onclick="add_new_field_to_page('VKontacte')"><a href="javascript:void(0)"><?php echo RM_UI_Strings::get("FIELD_TYPE_VKONTACTE"); ?></a></div>
            <div title="<?php echo RM_UI_Strings::get("FIELD_HELP_TEXT_Skype"); ?>" class="rm_button_like_links" onclick="add_new_field_to_page('Skype')"><a href="javascript:void(0)"><?php echo RM_UI_Strings::get("FIELD_TYPE_SKYPE"); ?></a></div>
            <div title="<?php echo RM_UI_Strings::get("FIELD_HELP_TEXT_SoundCloud"); ?>" class="rm_button_like_links" onclick="add_new_field_to_page('SoundCloud')"><a href="javascript:void(0)"><?php echo RM_UI_Strings::get("FIELD_TYPE_SOUNDCLOUD"); ?></a></div>

        </div>
    </div>
    
    <div class="rm-field-tabs-row rm-dbfl">
        <div class="rm-field-tab-cat"> <?php echo RM_UI_Strings::get("LABEL_DISPLAY_FIELDS"); ?></div>
        <div id="rm_display_fields_tab">

            <div title="<?php echo RM_UI_Strings::get("FIELD_HELP_TEXT_HTMLH"); ?>" class="rm_button_like_links" onclick="add_new_widget_to_page('HTMLH')"><a href="javascript:void(0)"><?php echo RM_UI_Strings::get("WIDGET_TYPE_HEADING"); ?></a></div>
            <div title="<?php echo RM_UI_Strings::get("FIELD_HELP_TEXT_HTMLP"); ?>" class="rm_button_like_links" onclick="add_new_widget_to_page('HTMLP')"><a href="javascript:void(0)"><?php echo RM_UI_Strings::get("WIDGET_TYPE_PARAGRAPH"); ?></a></div>
            <div title="<?php echo RM_UI_Strings::get("FIELD_HELP_TEXT_Divider"); ?>" class="rm_button_like_links" onclick="add_new_widget_to_page('Divider')"><a href="javascript:void(0)"><?php echo RM_UI_Strings::get("WIDGET_TYPE_DIVIDER"); ?></a></div>         
            <div title="<?php echo RM_UI_Strings::get("FIELD_HELP_TEXT_Spacing"); ?>" class="rm_button_like_links" onclick="add_new_widget_to_page('Spacing')"><a href="javascript:void(0)"><?php echo RM_UI_Strings::get("WIDGET_TYPE_SPACING"); ?></a></div>  
            <div title="<?php echo RM_UI_Strings::get("FIELD_HELP_TEXT_RICHTEXT"); ?>" class="rm_button_like_links" onclick="add_new_widget_to_page('RichText')"><a href="javascript:void(0)"><?php echo RM_UI_Strings::get("WIDGET_TYPE_RICHTEXT"); ?></a></div>  
            <div title="<?php echo RM_UI_Strings::get("FIELD_HELP_TEXT_TIMER"); ?>" class="rm_button_like_links" onclick="add_new_widget_to_page('Timer')"><a href="javascript:void(0)"><?php echo RM_UI_Strings::get("WIDGET_TYPE_TIMER"); ?></a></div> 
            <div title="<?php echo RM_UI_Strings::get("FIELD_HELP_TEXT_LINK"); ?>" class="rm_button_like_links" onclick="add_new_widget_to_page('Link')"><a href="javascript:void(0)"><?php echo RM_UI_Strings::get("WIDGET_TYPE_LINK"); ?></a></div>
            <div title="<?php echo RM_UI_Strings::get("FIELD_HELP_TEXT_YOUTUBE"); ?>" class="rm_button_like_links" onclick="add_new_widget_to_page('YouTubeV')"><a href="javascript:void(0)"><?php _e('YouTube Video','custom-registration-form-builder-with-submission-manager') ?></a></div> 
            <div title="<?php echo RM_UI_Strings::get("FIELD_HELP_TEXT_IFRAME"); ?>" class="rm_button_like_links" onclick="add_new_widget_to_page('Iframe')"><a href="javascript:void(0)"><?php _e('IFrame Embed','custom-registration-form-builder-with-submission-manager') ?></a></div> 
            <div title="<?php echo RM_UI_Strings::get("FIELD_HELP_TEXT_IMAGEV"); ?>" class="rm_button_like_links" onclick="add_new_widget_to_page('ImageV')"><a href="javascript:void(0)"><?php _e('Image','custom-registration-form-builder-with-submission-manager') ?></a></div>
            <div title="<?php echo RM_UI_Strings::get("FIELD_HELP_TEXT_PRICEV"); ?>" class="rm_button_like_links" onclick="add_new_widget_to_page('PriceV')"><a href="javascript:void(0)"><?php _e('Total Price','custom-registration-form-builder-with-submission-manager') ?></a></div>
            <div title="<?php _e('If you have set form limits, you can display the limit status using this widget','custom-registration-form-builder-with-submission-manager') ?>" class="rm_button_like_links rm-widget-lg" onclick="add_new_widget_to_page('SubCountV')"><a href="javascript:void(0)"> <?php _e('Submission Countdown','custom-registration-form-builder-with-submission-manager') ?></a></div>
            <div title="<?php _e('Display a location with marker using GoogleMaps','custom-registration-form-builder-with-submission-manager') ?>" class="rm_button_like_links" onclick="add_new_widget_to_page('MapV')"><a href="javascript:void(0)"><?php _e('Map','custom-registration-form-builder-with-submission-manager') ?></a></div>  
            <div title="<?php _e('Display stats about the form using graphs and charts','custom-registration-form-builder-with-submission-manager') ?>" class="rm_button_like_links" onclick="add_new_widget_to_page('Form_Chart')"><a href="javascript:void(0)"><?php _e('Form Data Chart','custom-registration-form-builder-with-submission-manager') ?></a></div> 
            <div title="<?php _e('Display various properties of the form','custom-registration-form-builder-with-submission-manager') ?>" class="rm_button_like_links" onclick="add_new_widget_to_page('FormData')"> <a href="javascript:void(0)"><?php _e('Form Meta-Data','custom-registration-form-builder-with-submission-manager') ?></a></div>
            <div title="<?php _e('Display latest registrations/ submissions data in your form','custom-registration-form-builder-with-submission-manager') ?>" class="rm_button_like_links" onclick="add_new_widget_to_page('Feed')"><a href="javascript:void(0)"><?php echo $data->form->form_type==0 ? __('Submission Feed','custom-registration-form-builder-with-submission-manager') : __('Registration Feed','custom-registration-form-builder-with-submission-manager'); ?></a></div>
        </div>
    </div>
    
    <div class="rm-field-tabs-row rm-dbfl">
        <div class="rm-field-tab-cat"><?php _e('Premium Fields', 'custom-registration-form-builder-with-submission-manager'); ?></div>
        <div id="rm_common_fields_tab">
            <div title="<?php echo RM_UI_Strings::get("FIELD_HELP_TEXT_File"); ?>" class="rm_button_like_links"><a class="rm_deactivated" href="javascript:void(0)"><?php echo RM_UI_Strings::get("FIELD_TYPE_FILE"); ?></a></div>
            <div title="<?php echo RM_UI_Strings::get("FIELD_HELP_TEXT_Repeatable"); ?>" class="rm_button_like_links"><a class="rm_deactivated" href="javascript:void(0)"><?php echo RM_UI_Strings::get("FIELD_TYPE_REPEAT"); ?></a></div>
            <div title="<?php echo RM_UI_Strings::get("FIELD_HELP_TEXT_Map"); ?>" class="rm_button_like_links"><a class="rm_deactivated" href="javascript:void(0)"><?php echo RM_UI_Strings::get("FIELD_TYPE_MAP"); ?></a></div>
            <div title="<?php echo RM_UI_Strings::get("FIELD_HELP_TEXT_Phone"); ?>" class="rm_button_like_links"><a class="rm_deactivated" href="javascript:void(0)"><?php echo RM_UI_Strings::get("FIELD_TYPE_PHONE"); ?></a></div>
            <div title="<?php echo RM_UI_Strings::get("FIELD_HELP_TEXT_Language"); ?>" class="rm_button_like_links"><a class="rm_deactivated" href="javascript:void(0)"><?php echo RM_UI_Strings::get("FIELD_TYPE_LANGUAGE"); ?></a></div>
            <div title="<?php echo RM_UI_Strings::get("FIELD_HELP_TEXT_Bdate"); ?>" class="rm_button_like_links"><a class="rm_deactivated" href="javascript:void(0)"><?php echo RM_UI_Strings::get("FIELD_TYPE_BDATE"); ?></a></div>
            <div title="<?php echo RM_UI_Strings::get("FIELD_HELP_TEXT_Gender"); ?>" class="rm_button_like_links"><a class="rm_deactivated" href="javascript:void(0)"><?php echo RM_UI_Strings::get("FIELD_TYPE_GENDER"); ?></a></div>
            <div title="<?php echo RM_UI_Strings::get("FIELD_HELP_TEXT_Time"); ?>" class="rm_button_like_links"><a class="rm_deactivated" href="javascript:void(0)"><?php echo RM_UI_Strings::get("FIELD_TYPE_TIME"); ?></a></div>
            <div title="<?php echo RM_UI_Strings::get("FIELD_HELP_TEXT_Image"); ?>" class="rm_button_like_links"><a class="rm_deactivated" href="javascript:void(0)"><?php echo RM_UI_Strings::get("FIELD_TYPE_IMAGE"); ?></a></div>
            <div title="<?php echo RM_UI_Strings::get("FIELD_HELP_TEXT_Shortcode"); ?>" class="rm_button_like_links"><a class="rm_deactivated" href="javascript:void(0)"><?php echo RM_UI_Strings::get("FIELD_TYPE_SHORTCODE"); ?></a></div>
            <div title="<?php echo RM_UI_Strings::get("FIELD_HELP_TEXT_Multi-Dropdown"); ?>" class="rm_button_like_links"><a class="rm_deactivated" href="javascript:void(0)"><?php echo RM_UI_Strings::get("FIELD_TYPE_MULTI_DROP_DOWN"); ?></a></div>
            <div title="<?php echo RM_UI_Strings::get("FIELD_HELP_TEXT_Rating"); ?>" class="rm_button_like_links"><a class="rm_deactivated" href="javascript:void(0)"><?php echo RM_UI_Strings::get("FIELD_TYPE_RATING"); ?></a></div>
            <div title="<?php echo RM_UI_Strings::get("FIELD_HELP_TEXT_Custom"); ?>" class="rm_button_like_links"><a class="rm_deactivated" href="javascript:void(0)"><?php echo RM_UI_Strings::get("FIELD_TYPE_CUSTOM"); ?></a></div>
            <div title="<?php echo RM_UI_Strings::get("FIELD_HELP_TEXT_SecEmail"); ?>" class="rm_button_like_links" ><a class="rm_deactivated" href="javascript:void(0)"><?php echo RM_UI_Strings::get("FIELD_TYPE_SEMAIL"); ?></a></div>
        </div>
    </div>

     <div style="display:none;"  id="rm_fields_up"><span><i class="material-icons">arrow_upward</i></span></div>
       
</div>




           
 <script>
   
(function($){

  var colors = ['#71d0b1', '#6e8ecf', '#70afcf', '#717171', '#e9898a', '#fee292', '#c0deda', '#527471', '#cf6e8d', '#fda629', '#fd6d6f', '#8cafac', '#8fd072',]
   , colorsUsed = {}
   , $divsToColor = $('.rm-field-icon'),
   i=0;
   
 $divsToColor.each(function(){
    
   var $div = $(this);

   $div.css('backgroundColor', colors[i]);
     if( colorsUsed[randomColor] ){
         colorsUsed[randomColor]++;
     } else {
         colorsUsed[randomColor] = 1;
     }
     
   if(i >= 12){
       var $div = $(this)
     , randomColor = colors[ Math.floor( Math.random() * colors.length ) ];

   $div.css('backgroundColor', randomColor);
     if( colorsUsed[randomColor] ){
         colorsUsed[randomColor]++;
     } else {
         colorsUsed[randomColor] = 1;
     }
   }  

   i++;
 });



})(jQuery);  

jQuery(function () {
    var $elem = jQuery('.rm-field-selector');
    jQuery('#rm_fields_up').fadeIn('slow');
    jQuery('#rm_fields_down').fadeIn('slow');

    jQuery('#rm_fields_down').click(function (e) {
        jQuery('#rm-field-selector .rm-modal-wrap').animate({
            scrollTop: $elem.height()
        }, 900);
    });
    jQuery('#rm_fields_up').click(function (e) {
        jQuery('#rm-field-selector .rm-modal-wrap').animate({
            scrollTop: '0px'
        }, 900);
    });
});

</script>

<?php } ?>