<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function eme_attributes_form($eme_array) {
   $eme_data = array();
   if (isset($eme_array['event_attributes'])) {
	   $eme_data = $eme_array['event_attributes'];
   } elseif (isset($eme_array['location_attributes'])) {
	   $eme_data = $eme_array['location_attributes'];
   }
   //We also get a list of attribute names and create a ddm list (since placeholders are fixed)
   $formats = 
      get_option('eme_event_list_item_format' ).
      get_option('eme_event_page_title_format' ).
      get_option('eme_full_calendar_event_format' ).
      get_option('eme_location_event_list_item_format' ).
      get_option('eme_ical_title_format' ).
      get_option('eme_ical_description_format' ).
      get_option('eme_rss_description_format' ).
      get_option('eme_rss_title_format' ).
      get_option('eme_single_event_format' ).
      get_option('eme_small_calendar_event_title_format' ).
      get_option('eme_single_location_format' ).
      get_option('eme_contactperson_email_body' ).
      get_option('eme_contactperson_cancelled_email_body' ).
      get_option('eme_contactperson_pending_email_body' ).
      get_option('eme_respondent_email_body' ).
      get_option('eme_registration_pending_email_body' ).
      get_option('eme_registration_denied_email_body' ).
      get_option('eme_registration_cancelled_email_body' ).
      get_option('eme_registration_form_format' ).
      get_option('eme_attendees_list_format' ).
      get_option('eme_payment_form_header_format' ).
      get_option('eme_payment_form_footer_format' ).
      get_option('eme_multipayment_form_header_format' ).
      get_option('eme_multipayment_form_footer_format' ).
      get_option('eme_bookings_list_format' );
      #get_option('eme_location_baloon_format' ).
      #get_option('eme_location_page_title_format' ).

   // include all templates as well
   $templates = eme_get_templates();
   foreach ($templates as $template) {
      $formats .= $template['format'];
   }

   // include all direct strings too
   foreach ($eme_array as $val) {
	   if (is_string($val)) $formats .= $val;
   }
   if (isset($eme_array['event_properties'])) {
      # we know it is an event then
      $event_props=maybe_unserialize($eme_array['event_properties']);
      foreach ($event_props as $key=>$val) {
         if (strstr($key,"_tpl"))
            $formats .= $val;
      }
      
   }
   if (isset($eme_array['location_properties'])) {
      # we know it is a location then
      $location_props=maybe_unserialize($eme_array['location_properties']);
      foreach ($location_props as $key=>$val) {
         if (strstr($key,"_tpl"))
            $formats .= $val;
      }
   }


   //We now have one long string of formats
   preg_match_all("/#(ESC|URL)?_ATT\{.+?\}(\{.+?\})?/", $formats, $placeholders);

   $attributes = array();
   //Now grab all the unique attributes we can use in our event or location.
   foreach($placeholders[0] as $result) {
      $result = str_replace("#ESC","#",$result);
      $result = str_replace("#URL","#",$result);
      $attribute = substr( substr($result, 0, strpos($result, '}')), 6 );
      if( !in_array($attribute, $attributes) ){       
         $attributes[] = $attribute ;
      }
   }
   ?>
   <div class="wrap">
   <?php if( count( $attributes ) > 0 ) { ?> 
      <p><?php _e('Add attributes here','events-made-easy'); ?></p>
      <table class="form-table eme_attributes">
         <thead>
            <tr>
               <th><strong><?php _e('Attribute Name','events-made-easy'); ?></strong></th>
               <th><strong><?php _e('Value','events-made-easy'); ?></strong></th>
            </tr>
         </thead>    
         <tbody id="eme_attr_body">
            <?php
            $count = 1;
            if( is_array($eme_data) and count($eme_data) > 0){
               foreach( $eme_data as $name => $value){
                  ?>
                  <tr id="eme_attr_<?php echo $count ?>">
                     <td>
                        <select name="eme_attr_<?php echo $count ?>_ref">
                           <?php
                           if( !in_array($name, $attributes) ){
                              echo "<option value='$name'>$name (".__('Not defined in templates', 'events-made-easy').")</option>";
                           }
                           foreach( $attributes as $attribute ){
                              if( $attribute == $name ) {
                                 echo "<option selected='selected'>$attribute</option>";
                              }else{
                                 echo "<option>$attribute</option>";
                              }
                           }
                           ?>
                        </select>
                        <a href="#"><?php _e('Remove','events-made-easy'); ?></a>
                     </td>
                     <td>
			<textarea rows="2" cols="40" id="eme_attr_<?php echo $count; ?>_id" name="eme_attr_<?php echo $count; ?>_name"><?php echo eme_esc_html($value); ?></textarea>
                     </td>
                  </tr>
            <?php
                  $count++;
               }
            } else {
            ?>
                  <tr id="eme_attr_<?php echo $count ?>">
                     <td>
                        <select name="eme_attr_<?php echo $count ?>_ref">
                           <?php
                           foreach( $attributes as $attribute ){
                              echo "<option>$attribute</option>";
                           }
                           ?>
                        </select>
                        <a href="#"><?php _e('Remove','events-made-easy'); ?></a>
                     </td>
                     <td>
			<textarea rows="2" cols="40" id="eme_attr_<?php echo $count; ?>_id" name="eme_attr_<?php echo $count; ?>_name"></textarea>
                     </td>
                  </tr>
                  <?php
            }
            ?>
         </tbody>
         <tfoot>
            <tr>
               <td colspan="2"><a href="#" id="eme_attr_add_tag"><?php _e('Add new tag','events-made-easy'); ?></a></td>
            </tr>
         </tfoot>
      </table>
   <?php
   } else {
   ?>
      <p><?php _e('No attributes defined yet. If you want attributes, you first need to define/use some in the Settings page. See the section about custom attributes on the documention site for more info.','events-made-easy'); ?></p>
   <?php
   } //endif count attributes
   ?>
   </div>
<?php
}
?>
