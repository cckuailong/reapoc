<?php

namespace MEC\Notifications;

use MEC\Events\Event;
use MEC\Settings\Settings;

class SendEmail{

    public $group_id;
    public $notifications_options;
    public $event_id;

    public function get_attendees(){

        return [];
    }

    public function allowed_check_settings_for_attendees(){

        return [];
    }

    public function get_event_times(){

        return '';
    }

    public function _get_notifications_settings(){

        $options = Settings::getInstance()->get_options('notifications');

        return isset($options[$this->group_id]) && is_array($options[$this->group_id]) ? $options[$this->group_id] : $this->get_default_notification_settings();
    }

    public function get_default_notification_settings(){

        return [];
    }

    public function get_notification_settings( $key = null ){

        if( empty($this->notifications_options) ){

            $global_options = $this->_get_notifications_settings();
            $this->notifications_options = $global_options;

            $event_options = [];
            if($this->event_id){

                $event = new Event( $this->event_id, false );
                $event_options = $event->get_notifications_settings();
                if( isset($event_options['status']) && (bool)$event_options['status'] ){

                    $this->notifications_options['subject'] = $event_options['subject'];
                    $this->notifications_options['content'] = $event_options['content'];
                }
            }

            $this->notifications_options = apply_filters('mec_get_notifications_options', $this->notifications_options, $this->group_id, $global_options, $event_options );
        }

        if(!is_null( $key )){

            return isset( $this->notifications_options[$key] ) ? $this->notifications_options[$key] : null;
        }

        return $this->notifications_options;
    }

    public function get_subject( $default = '' ){

        $subject = $this->get_notification_settings( 'subject' );

        return !is_null($subject) ? __($subject,'modern-events-calendar-lite') : $default;
    }

    public function get_content( $default = '' ){

        $content = $this->get_notification_settings( 'content' );

        return !empty($content) ? $content : $default;
    }

    public function get_enabled_status(){

        $status = $this->get_notification_settings( 'status' );

        return (bool)$status ;
    }

    public function get_send_to_admin_status(){

        $status = $this->get_notification_settings( 'send_to_admin' );

        return (bool)$status ;
    }

    public function get_send_to_organizer_status(){

        $status = $this->get_notification_settings( 'send_to_organizer' );

        return (bool)$status ;
    }

    public function get_send_to_user_status(){

        $status = $this->get_notification_settings( 'send_to_user' );

        return (bool)$status ;
    }

    public function get_recipients_emails(){

        $recipients = $this->get_notification_settings( 'recipients' );
        $recipients = explode(',', trim($recipients));

        return !empty($recipients) && is_array($recipients) ? $recipients : [];
    }

    public function get_receiver_users_ids(){

        $users_ids = $this->get_notification_settings( 'receiver_users' );

        return !empty($users_ids) && is_array($users_ids) ? $users_ids : [];
    }

    public function get_receiver_users_emails(){

        $users_ids = $this->get_receiver_users_ids();

        return (array)\MEC\base::get_main()->get_emails_by_users($users_ids);
    }

    public function get_receiver_roles(){

        $users_ids = $this->get_notification_settings( 'receiver_roles' );

        return !empty($users_ids) && is_array($users_ids) ? $users_ids : [];
    }

    public function get_receiver_roles_emails(){

        $users_roles = $this->get_receiver_roles();

        return (array)\MEC\base::get_main()->get_emails_by_roles( $users_roles );
    }

    public function get_organizer_email(){

        $organizer_id = get_post_meta($this->event_id, 'mec_organizer_id', true);
        $email = get_term_meta($organizer_id, 'email', true);

        return trim($email) ? $email : false;
    }

    public function get_all_recipients_emails(){

        $emails = array_merge(
            $this->get_recipients_emails(),
            $this->get_receiver_users_emails(),
        );

        $emails = array_merge(
            $emails,
            $this->get_receiver_roles_emails()
        );

        $emails = array_map('trim', $emails);
        foreach($emails as $k => $email){

            if(empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)){

                unset($emails[$k]);
            }
        }

        return array_unique($emails);
    }

    public function get_target_users_or_emails(){

        $users_or_emails = array();


        $allowed_check_settings_for_attendees = $this->allowed_check_settings_for_attendees();


        $is_in_allowed = in_array($this->group_id, $allowed_check_settings_for_attendees);

        if(
            !$is_in_allowed
            ||
            ($is_in_allowed && $this->get_send_to_user_status())
            ){

            $attendees = $this->get_attendees();

            if( is_array($attendees) && !empty($attendees) ){

                $users_or_emails = $attendees;
            }
        }

        if($this->get_send_to_admin_status()){

            $users_or_emails[] = get_bloginfo('admin_email');
        }

        if($this->get_send_to_organizer_status()){

            $organizer_email = $this->get_organizer_email();
            if(!empty($organizer_email)){

                $users_or_emails[] = $organizer_email;
            }
        }

        return $users_or_emails;
    }

    public function get_author($object_id){
        return (object)[];
    }

    public function render_author(&$content,$object_id,$attendee){

        $author = $this->get_author($object_id);


        $first_name = (isset($author->first_name) ? $author->first_name : '');
        $last_name = (isset($author->last_name) ? $author->last_name : '');
        $name = (isset($author->first_name) ? trim($author->first_name.' '.(isset($author->last_name) ? $author->last_name : '')) : '');
        $email = (isset($author->user_email) ? $author->user_email : '');

        /**
         * Get the data from Attendee instead of main author user
         */
        if(isset($attendee['name']) and trim($attendee['name'])){
            $name = $attendee['name'];
            $attendee_ex_name = explode(' ', $name);

            $first_name = isset($attendee_ex_name[0]) ? $attendee_ex_name[0] : '';
            $last_name = isset($attendee_ex_name[1]) ? $attendee_ex_name[1] : '';
            $email = isset($attendee['email']) ? $attendee['email'] : $email;
        }

        // author Data
        $content = str_replace('%%first_name%%', $first_name, $content);
        $content = str_replace('%%last_name%%', $last_name, $content);
        $content = str_replace('%%name%%', $name, $content);
        $content = str_replace('%%user_email%%', $email, $content);
        $content = str_replace('%%user_id%%', (isset($author->ID) ? $author->ID : ''), $content);

        return $content;
    }

    public function render_site_data(&$content,$object_id){

        $content = str_replace('%%blog_name%%', get_bloginfo('name'), $content);
        $content = str_replace('%%blog_url%%', get_bloginfo('url'), $content);
        $content = str_replace('%%blog_description%%', get_bloginfo('description'), $content);

        return $content;
    }

    public function render_event_data(&$content,$object_id,$timestamps){

        // Date & Time Format
        $date_format = get_option('date_format');
        $time_format = get_option('time_format');

        if(!trim($timestamps)) {

            $timestamps = $this->get_event_times();
        }
        list($start_timestamp, $end_timestamp) = explode(':', $timestamps);

        // Event Data
        $organizer_id = get_post_meta($this->event_id, 'mec_organizer_id', true);
        $location_id = get_post_meta($this->event_id, 'mec_location_id', true);
        $speaker_id = wp_get_post_terms( $this->event_id, 'mec_speaker', '');

        $organizer = get_term($organizer_id, 'mec_organizer');
        $location = get_term($location_id, 'mec_location');

        // Data Fields
        $event_fields = \MEC\Base::get_main()->get_event_fields();
        $event_fields_data = get_post_meta($this->event_id, 'mec_fields', true);
        if(!is_array($event_fields_data)) $event_fields_data = array();

        foreach($event_fields as $f => $event_field){
            if(!is_numeric($f)) {

                continue;
            }

            $event_field_name = isset($event_field['label']) ? $event_field['label'] : '';
            $field_value = isset($event_fields_data[$f]) ? $event_fields_data[$f] : NULL;
            if((!is_array($field_value) and trim($field_value) === '') or (is_array($field_value) and !count($field_value))){
                $content = str_replace('%%event_field_'.$f.'%%', '', $content);
                $content = str_replace('%%event_field_'.$f.'_with_name%%', '', $content);

                continue;
            }

            if(is_array($field_value)) $field_value = implode(', ', $field_value);

            $content = str_replace('%%event_field_'.$f.'%%', trim(stripslashes($field_value), ', '), $content);
            $content = str_replace('%%event_field_'.$f.'_with_name%%', trim((trim($event_field_name) ? stripslashes($event_field_name).': ' : '').trim(stripslashes($field_value), ', ')), $content);
        }

        $content = str_replace('%%event_title%%', get_the_title($this->event_id), $content);
        $content = str_replace('%%event_description%%', strip_tags(strip_shortcodes(get_post_field('post_content', $this->event_id))), $content);

        $event_tags = get_the_terms($this->event_id, apply_filters('mec_taxonomy_tag', ''));
        $content = str_replace('%%event_tags%%', (is_array($event_tags) ? join(', ', wp_list_pluck($event_tags, 'name')) : ''), $content);

        $event_labels = get_the_terms($this->event_id, 'mec_label');
        $content = str_replace('%%event_labels%%', (is_array($event_labels) ? join(', ', wp_list_pluck($event_labels, 'name')) : ''), $content);

        $event_categories = get_the_terms($this->event_id, 'mec_category');
        $content = str_replace('%%event_categories%%', (is_array($event_categories) ? join(', ', wp_list_pluck($event_categories, 'name')) : ''), $content);

        $mec_cost = get_post_meta($this->event_id, 'mec_cost', true);
        $mec_cost = (isset($params['cost']) and trim($params['cost']) != '') ? preg_replace("/[^0-9.]/", '', $params['cost']) : $mec_cost;

        $read_more = get_post_meta($this->event_id, 'mec_read_more', true);
        $read_more = (isset($params['read_more']) and trim($params['read_more']) != '') ? $params['read_more'] : $read_more;

        $more_info = get_post_meta($this->event_id, 'mec_more_info', true);
        $more_info = (isset($params['more_info']) and trim($params['more_info']) != '') ? $params['more_info'] : $more_info;

        $content = str_replace('%%event_cost%%', (is_numeric($mec_cost) ? \MEC\Base::get_main()->render_price($mec_cost, $this->event_id) : $mec_cost), $content);
        $content = str_replace('%%event_link%%', \MEC\Base::get_main()->get_event_date_permalink(get_permalink($this->event_id), date('Y-m-d', $start_timestamp)), $content);
        $content = str_replace('%%event_more_info%%', esc_url($read_more), $content);
        $content = str_replace('%%event_other_info%%', esc_url($more_info), $content);
        $content = str_replace('%%event_start_date%%', \MEC\Base::get_main()->date_i18n($date_format, $start_timestamp), $content);
        $content = str_replace('%%event_end_date%%', \MEC\Base::get_main()->date_i18n($date_format, $end_timestamp), $content);
        $content = str_replace('%%event_start_time%%', date_i18n($time_format, $start_timestamp), $content);
        $content = str_replace('%%event_end_time%%', date_i18n($time_format, $end_timestamp), $content);
        $content = str_replace('%%event_timezone%%', \MEC\Base::get_main()->get_timezone($this->event_id), $content);

        $online_link = \MEC_feature_occurrences::param($this->event_id, $start_timestamp, 'moved_online_link', get_post_meta($this->event_id, 'mec_moved_online_link', true));
        $content = str_replace('%%online_link%%', esc_url($online_link), $content);

        $featured_image = '';
        $thumbnail_url = \MEC\Base::get_main()->get_post_thumbnail_url($this->event_id, 'medium');
        if(trim($thumbnail_url)) $featured_image = '<img src="'.$thumbnail_url.'">';

        $content = str_replace('%%event_featured_image%%', $featured_image, $content);

        $content = str_replace('%%event_organizer_name%%', (isset($organizer->name) ? $organizer->name : ''), $content);
        $content = str_replace('%%event_organizer_tel%%', get_term_meta($organizer_id, 'tel', true), $content);
        $content = str_replace('%%event_organizer_email%%', get_term_meta($organizer_id, 'email', true), $content);
        $content = str_replace('%%event_organizer_url%%', get_term_meta($organizer_id, 'url', true), $content);

        $additional_organizers_name = '';
        $additional_organizers_tel = '';
        $additional_organizers_email = '';
        $additional_organizers_url = '';

        $additional_organizers_ids = get_post_meta($this->event_id, 'mec_additional_organizer_ids', true);
        if(!is_array($additional_organizers_ids)) $additional_organizers_ids = array();

        foreach($additional_organizers_ids as $additional_organizers_id)
        {
            $additional_organizer = get_term($additional_organizers_id, 'mec_organizer');
            if(isset($additional_organizer->name))
            {
                $additional_organizers_name .= $additional_organizer->name.', ';
                $additional_organizers_tel .= get_term_meta($additional_organizers_id, 'tel', true).'<br>';
                $additional_organizers_email .= get_term_meta($additional_organizers_id, 'email', true).'<br>';
                $additional_organizers_url .= get_term_meta($additional_organizers_id, 'url', true).'<br>';
            }
        }

        $content = str_replace('%%event_other_organizers_name%%', trim($additional_organizers_name, ', '), $content);
        $content = str_replace('%%event_other_organizers_tel%%', trim($additional_organizers_tel, ', '), $content);
        $content = str_replace('%%event_other_organizers_email%%', trim($additional_organizers_email, ', '), $content);
        $content = str_replace('%%event_other_organizers_url%%', trim($additional_organizers_url, ', '), $content);

        $speaker_name = array();
        foreach($speaker_id as $speaker) $speaker_name[] = isset($speaker->name) ? $speaker->name : null;

        $content = str_replace('%%event_speaker_name%%', (isset($speaker_name) ? implode(', ', $speaker_name): ''), $content);
        $content = str_replace('%%event_location_name%%', (isset($location->name) ? $location->name : ''), $content);
        $content = str_replace('%%event_location_address%%', get_term_meta($location_id, 'address', true), $content);

        $additional_locations_name = '';
        $additional_locations_address = '';

        $additional_locations_ids = get_post_meta($this->event_id, 'mec_additional_location_ids', true);
        if(!is_array($additional_locations_ids)) $additional_locations_ids = array();

        foreach($additional_locations_ids as $additional_locations_id){
            $additional_location = get_term($additional_locations_id, 'mec_location');
            if(isset($additional_location->name))
            {
                $additional_locations_name .= $additional_location->name.', ';
                $additional_locations_address .= get_term_meta($additional_locations_id, 'address', true).'<br>';
            }
        }

        $content = str_replace('%%event_other_locations_name%%', trim($additional_locations_name, ', '), $content);
        $content = str_replace('%%event_other_locations_address%%', trim($additional_locations_address, ', '), $content);

        $gmt_offset_seconds = \MEC\Base::get_main()->get_gmt_offset_seconds($start_timestamp, $this->event_id);
        $event_title = get_the_title($this->event_id);
        $event_info = get_post($this->event_id);
        $event_content = trim($event_info->post_content) ? strip_shortcodes(strip_tags($event_info->post_content)) : $event_title;
        $event_content = apply_filters('mec_add_content_to_export_google_calendar_details', $event_content,$this->event_id );

        // Virtual Event
        $content = str_replace('%%virtual_link%%', get_post_meta($this->event_id, 'mec_virtual_link_url', true), $content);
        $content = str_replace('%%virtual_password%%', get_post_meta($this->event_id, 'mec_virtual_password', true), $content);
        $content = str_replace('%%virtual_embed%%', get_post_meta($this->event_id, 'mec_virtual_embed', true), $content);

        $content = str_replace('%%zoom_join%%', get_post_meta($this->event_id, 'mec_zoom_join_url', true), $content);
        $content = str_replace('%%zoom_link%%', get_post_meta($this->event_id, 'mec_zoom_link_url', true), $content);
        $content = str_replace('%%zoom_password%%', get_post_meta($this->event_id, 'mec_zoom_password', true), $content);
        $content = str_replace('%%zoom_embed%%', get_post_meta($this->event_id, 'mec_zoom_embed', true), $content);

        return $content;
    }

    public function add_template($content){

        $style = \MEC\Base::get_main()->get_styling();
        $bgnotifications = isset($style['notification_bg']) ? $style['notification_bg'] : '#f6f6f6';

        return '<table border="0" cellpadding="0" cellspacing="0" class="wn-body" style="background-color: '.$bgnotifications.'; font-family: -apple-system,BlinkMacSystemFont,Segoe UI,Oxygen,Open Sans, sans-serif;border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;">
            <tr>
                <td class="wn-container" style="display: block; margin: 0 auto !important; max-width: 680px; padding: 10px;font-family: sans-serif; font-size: 14px; vertical-align: top;">
                    <div class="wn-wrapper" style="box-sizing: border-box; padding: 38px 9% 50px; width: 100%; height: auto; background: #fff; background-size: contain; margin-bottom: 25px; margin-top: 30px; border-radius: 4px; box-shadow: 0 3px 55px -18px rgba(0,0,0,0.1);">
                        '.$content.'
                    </div>
                </td>
            </tr>
        </table>';
    }

    public function send_mail($args){

        return wp_mail(
            $args['to'],
            html_entity_decode(stripslashes($args['subject']), ENT_HTML5),
            wpautop(stripslashes($args['message'])),
            $args['headers'],
            $args['attachments']
        );
    }
}