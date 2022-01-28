<?php
/** no direct access **/
defined('MECEXEC') or die();

/**
 * Webnus MEC fes (Frontend Event Submission) class.
 * @author Webnus <info@webnus.biz>
 */
class MEC_feature_fes extends MEC_base
{
    public $factory;
    public $main;
    public $db;
    public $settings;
    public $PT;
    public $render;
    public $relative_link = false;

    /**
     * Constructor method
     * @author Webnus <info@webnus.biz>
     */
    public function __construct()
    {
        // Import MEC Factory
        $this->factory = $this->getFactory();

        // Import MEC Main
        $this->main = $this->getMain();

        // Import MEC DB
        $this->db = $this->getDB();

        // MEC Settings
        $this->settings = $this->main->get_settings();

        // Event Post Type
        $this->PT = $this->main->get_main_post_type();
    }

    /**
     * Initialize colors feature
     * @author Webnus <info@webnus.biz>
     */
    public function init()
    {
        // Frontend Event Submission Form
        $this->factory->shortcode('MEC_fes_form', array($this, 'vform'));

        // Event Single Page
        $this->factory->shortcode('MEC_fes_list', array($this, 'vlist'));

        // Process the event form
        $this->factory->action('wp_ajax_mec_fes_form', array($this, 'fes_form'));
        $this->factory->action('wp_ajax_nopriv_mec_fes_form', array($this, 'fes_form'));

        // Upload featured image
        $this->factory->action('wp_ajax_mec_fes_upload_featured_image', array($this, 'fes_upload'));
        $this->factory->action('wp_ajax_nopriv_mec_fes_upload_featured_image', array($this, 'fes_upload'));

        // Export the event
        $this->factory->action('wp_ajax_mec_fes_csv_export', array($this, 'mec_fes_csv_export'));

        // Remove the event
        $this->factory->action('wp_ajax_mec_fes_remove', array($this, 'fes_remove'));

        // Event Published
        $this->factory->action('transition_post_status', array($this, 'status_changed'), 10, 3);
    }

    /**
     * Generate frontend event submission form view
     * @author Webnus <info@webnus.biz>
     * @param array $atts
     * @return string
     */
    public function vform($atts = array())
    {
        // Force to array
        if(!is_array($atts)) $atts = array();

        if(isset($_GET['vlist']) and $_GET['vlist'] == 1)
        {
            return $this->vlist($atts);
        }

        // Force to Relative Link
        $this->relative_link = (isset($atts['relative-link']) and $atts['relative-link']);

        // Show login/register message if user is not logged in and guest submission is not enabled.
        if(!is_user_logged_in() and (!isset($this->settings['fes_guest_status']) or (isset($this->settings['fes_guest_status']) and $this->settings['fes_guest_status'] == '0')))
        {
            // Show message
            $message = sprintf(__('Please %s/%s in order to submit new events.', 'modern-events-calendar-lite'), '<a href="'.wp_login_url($this->main->get_full_url()).'">'.__('Login', 'modern-events-calendar-lite').'</a>', '<a href="'.wp_registration_url().'">'.__('Register', 'modern-events-calendar-lite').'</a>');

            ob_start();
            include MEC::import('app.features.fes.message', true, true);
            return $output = ob_get_clean();
        }

        $post_id = isset($_GET['post_id']) ? sanitize_text_field($_GET['post_id']) : -1;

        // Selected post is not an event
        if($post_id > 0 and get_post_type($post_id) != $this->PT)
        {
            // Show message
            $message = __("Sorry! Selected post is not an event.", 'modern-events-calendar-lite');

            ob_start();
            include MEC::import('app.features.fes.message', true, true);
            return $output = ob_get_clean();
        }

        // Show a warning to current user if modification of post is not possible for him/her
        if($post_id != -1 and !current_user_can('edit_post', $post_id))
        {
            // Show message
            $message = __("Sorry! You don't have access to modify this event.", 'modern-events-calendar-lite');

            ob_start();
            include MEC::import('app.features.fes.message', true, true);
            return $output = ob_get_clean();
        }

        $post = get_post($post_id);

        if($post_id == -1)
        {
            $post = new stdClass();
            $post->ID = -1;
        }

        $path = MEC::import('app.features.fes.form', true, true);

        ob_start();
        include $path;
        return $output = ob_get_clean();
    }

    /**
     * Generate frontend event submission list view
     * @author Webnus <info@webnus.biz>
     * @param array $atts
     * @return string
     */
    public function vlist($atts = array())
    {
        // Force to array
        if(!is_array($atts)) $atts = array();

        $post_id = isset($_GET['post_id']) ? sanitize_text_field($_GET['post_id']) : NULL;

        // Force to Relative Link
        $this->relative_link = (isset($atts['relative-link']) and $atts['relative-link']);

        // Show a warning to current user if modification of post is not possible for him/her
        if($post_id > 0 and !current_user_can('edit_post', $post_id))
        {
            // Show message
            $message = __("Sorry! You don't have access to modify this event.", 'modern-events-calendar-lite');

            ob_start();
            include MEC::import('app.features.fes.message', true, true);
            return $output = ob_get_clean();
        }
        elseif($post_id == -1 or ($post_id > 0 and current_user_can('edit_post', $post_id)))
        {
            return $this->vform($atts);
        }

        // Show login/register message if user is not logged in
        if(!is_user_logged_in())
        {
            // Show message
            $message = sprintf(__('Please %s/%s in order to manage events.', 'modern-events-calendar-lite'), '<a href="'.wp_login_url($this->main->get_full_url()).'">'.__('Login', 'modern-events-calendar-lite').'</a>', '<a href="'.wp_registration_url().'">'.__('Register', 'modern-events-calendar-lite').'</a>');

            ob_start();
            include MEC::import('app.features.fes.message', true, true);
            return $output = ob_get_clean();
        }

        $path = MEC::import('app.features.fes.list', true, true);

        ob_start();
        include $path;
        return $output = ob_get_clean();
    }

    public function fes_remove()
    {
        // Check if our nonce is set.
        if(!isset($_POST['_wpnonce'])) $this->main->response(array('success'=>0, 'code'=>'NONCE_MISSING'));

        // Verify that the nonce is valid.
        if(!wp_verify_nonce(sanitize_text_field($_POST['_wpnonce']), 'mec_fes_remove')) $this->main->response(array('success'=>0, 'code'=>'NONCE_IS_INVALID'));

        $post_id = isset($_POST['post_id']) ? sanitize_text_field($_POST['post_id']) : 0;

        // Verify current user can remove the event
        if(!current_user_can('delete_post', $post_id)) $this->main->response(array('success'=>0, 'code'=>'USER_CANNOT_REMOVE_EVENT'));

        // Trash the event
        wp_delete_post($post_id);

        $this->main->response(array('success'=>1, 'message'=>__('Event removed!', 'modern-events-calendar-lite')));
    }

    public function mec_fes_csv_export()
    {
        if((!isset($_REQUEST['mec_event_id'])) or (!isset($_REQUEST['fes_nonce'])) or (!wp_verify_nonce($_REQUEST['fes_nonce'], 'mec_fes_nonce'))) die(json_encode(array('ex' => "error")));

        $event_id = intval($_REQUEST['mec_event_id']);
        $timestamp = isset($_REQUEST['timestamp']) ? sanitize_text_field($_REQUEST['timestamp']) : 0;
        $booking_ids = '';
        $type = isset( $_REQUEST['type'] ) ? sanitize_text_field( $_REQUEST['type'] ) : 'csv';

        switch( $type ){

            case 'ms-excel':

                header('Content-Type: application/vnd.ms-excel; charset=utf-8');
                header('Content-Disposition: attachment; filename=attendees-'.md5(time().mt_rand(100, 999)).'.xls');

                break;

            default:
            case 'csv':

                header('Content-Type: text/csv; charset=utf-8');
                header('Content-Disposition: attachment; filename=attendees-'.md5(time().mt_rand(100, 999)).'.csv');

                break;
        }

        if($timestamp)
        {
            $bookings = $this->main->get_bookings($event_id, $timestamp);
            foreach($bookings as $booking)
            {
                $booking_ids .= $booking->ID.',';
            }
        }

        $post_ids = trim($booking_ids) ? explode(',', trim($booking_ids, ', ')) : array();

        if(!count($post_ids) and !$timestamp)
        {
            $books = $this->db->select("SELECT `post_id` FROM `#__postmeta` WHERE `meta_key`='mec_event_id' AND `meta_value`={$event_id}", 'loadAssocList');
            foreach($books as $book) if(isset($book['post_id'])) $post_ids[] = $book['post_id'];
        }

        $event_ids = array();
        foreach($post_ids as $post_id) $event_ids[] = get_post_meta($post_id, 'mec_event_id', true);
        $event_ids = array_unique($event_ids);

        $main_event_id = NULL;
        if(count($event_ids) == 1) $main_event_id = $event_ids[0];

        $columns = array(__('ID', 'modern-events-calendar-lite'), __('Event', 'modern-events-calendar-lite'), __('Date', 'modern-events-calendar-lite'), __('Order Time', 'modern-events-calendar-lite'), $this->main->m('ticket', __('Ticket', 'modern-events-calendar-lite')), __('Transaction ID', 'modern-events-calendar-lite'), __('Total Price', 'modern-events-calendar-lite'), __('Gateway', 'modern-events-calendar-lite'), __('Name', 'modern-events-calendar-lite'), __('Email', 'modern-events-calendar-lite'), __('Ticket Variation', 'modern-events-calendar-lite'), __('Confirmation', 'modern-events-calendar-lite'), __('Verification', 'modern-events-calendar-lite'));
        $columns = apply_filters('mec_csv_export_columns', $columns);

        $reg_fields = $this->main->get_reg_fields($main_event_id);
        foreach($reg_fields as $reg_field_key=>$reg_field)
        {
            // Placeholder Keys
            if(!is_numeric($reg_field_key)) continue;

            $type = isset($reg_field['type']) ? $reg_field['type'] : '';
            $label = isset($reg_field['label']) ? __($reg_field['label'], 'modern-events-calendar-lite') : '';

            if(trim($label) == '' or $type == 'name' or $type == 'mec_email') continue;
            if($type == 'agreement') $label = sprintf($label, get_the_title($reg_field['page']));

            $columns[] = $label;
        }

        $columns[] = 'Attachments';
        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        fputcsv($output, $columns);

        // MEC User
        $u = $this->getUser();

        foreach($post_ids as $post_id)
        {
            $post_id = (int) $post_id;

            $event_id = get_post_meta($post_id, 'mec_event_id', true);
            $transaction_id = get_post_meta($post_id, 'mec_transaction_id', true);
            $order_time = get_post_meta($post_id, 'mec_booking_time', true);
            $tickets = get_post_meta($event_id, 'mec_tickets', true);

            $attendees = get_post_meta($post_id, 'mec_attendees', true);
            if(!is_array($attendees) or (is_array($attendees) and !count($attendees))) $attendees = array(get_post_meta($post_id, 'mec_attendee', true));

            $price = get_post_meta($post_id, 'mec_price', true);
            $gateway_label = get_post_meta($post_id, 'mec_gateway_label', true);
            $booker = $u->booking($post_id);

            $confirmed = $this->main->get_confirmation_label(get_post_meta($post_id, 'mec_confirmed', true));
            $verified = $this->main->get_verification_label(get_post_meta($post_id, 'mec_verified', true));

            $attachments = '';
            if(isset($attendees['attachments']))
            {
                foreach($attendees['attachments'] as $attachment)
                {
                    $attachments .= @$attachment['url'] . "\n";
                }
            }

            $counter = 0;
            foreach($attendees as $key => $attendee)
            {
                if($key === 'attachments') continue;
                if(isset($attendee[0]['MEC_TYPE_OF_DATA'])) continue;

                $ticket_variations_output = '';
                if(isset($attendee['variations']) and is_array($attendee['variations']) and count($attendee['variations']))
                {
                    $ticket_variations = $this->main->ticket_variations($post_id, $attendee['id']);
                    foreach($attendee['variations'] as $a_variation_id => $a_variation_count)
                    {
                        if((int) $a_variation_count > 0) $ticket_variations_output .= $ticket_variations[$a_variation_id]['title'].": (".$a_variation_count.')'.", ";
                    }
                }

                $ticket_id = isset($attendee['id']) ? $attendee['id'] : get_post_meta($post_id, 'mec_ticket_id', true);
                $booking = array(
                    $post_id,
                    html_entity_decode(get_the_title($event_id), ENT_QUOTES | ENT_HTML5),
                    get_the_date('', $post_id),
                    $order_time,
                    (isset($tickets[$ticket_id]['name']) ? $tickets[$ticket_id]['name'] : __('Unknown', 'modern-events-calendar-lite')),
                    $transaction_id,
                    $this->main->render_price(($price ? $price : 0), $post_id),
                    html_entity_decode($gateway_label, ENT_QUOTES | ENT_HTML5),
                    (isset($attendee['name']) ? $attendee['name'] : (isset($booker->first_name) ? trim($booker->first_name.' '.$booker->last_name) : '')),
                    (isset($attendee['email']) ? $attendee['email'] : @$booker->user_email),
                    html_entity_decode(trim($ticket_variations_output, ', '), ENT_QUOTES | ENT_HTML5),
                    $confirmed,
                    $verified
                );

                $booking = apply_filters('mec_csv_export_booking', $booking, $post_id, $event_id);

                $reg_form = isset($attendee['reg']) ? $attendee['reg'] : array();
                foreach($reg_fields as $field_id=>$reg_field)
                {
                    // Placeholder Keys
                    if(!is_numeric($field_id)) continue;

                    $type = isset($reg_field['type']) ? $reg_field['type'] : '';
                    $label = isset($reg_field['label']) ? __($reg_field['label'], 'modern-events-calendar-lite') : '';
                    if(trim($label) == '' or $type == 'name' or $type == 'mec_email') continue;

                    $booking[] = isset($reg_form[$field_id]) ? ((is_string($reg_form[$field_id]) and trim($reg_form[$field_id])) ? $reg_form[$field_id] : (is_array($reg_form[$field_id]) ? implode(' | ', $reg_form[$field_id]) : '---')) : '';
                }

                if($attachments)
                {
                    $booking[]  = $attachments;
                    $attachments = '';
                }

                fputcsv($output, $booking);
                $counter++;
            }
        }

        die();
    }

    public function fes_upload()
    {
        // Check if our nonce is set.
        if(!isset($_POST['_wpnonce'])) $this->main->response(array('success'=>0, 'code'=>'NONCE_MISSING'));

        // Verify that the nonce is valid.
        if(!wp_verify_nonce(sanitize_text_field($_POST['_wpnonce']), 'mec_fes_upload_featured_image')) $this->main->response(array('success'=>0, 'code'=>'NONCE_IS_INVALID'));

        // Include the function
        if(!function_exists('wp_handle_upload')) require_once ABSPATH.'wp-admin/includes/file.php';

        $uploaded_file = isset($_FILES['file']) ? $_FILES['file'] : NULL;

        // No file
        if(!$uploaded_file) $this->main->response(array('success'=>0, 'code'=>'NO_FILE', 'message'=>esc_html__('Please upload an image.', 'modern-events-calendar-lite')));

        $allowed = array('gif', 'jpeg', 'jpg', 'png');

        $ex = explode('.', $uploaded_file['name']);
        $extension = end($ex);

        // Invalid Extension
        if(!in_array($extension, $allowed)) $this->main->response(array('success'=>0, 'code'=>'INVALID_EXTENSION', 'message'=>sprintf(esc_html__('image extension is invalid. You can upload %s images.', 'modern-events-calendar-lite'), implode(', ', $allowed))));

        // Maximum File Size
        $max_file_size = isset($this->settings['fes_max_file_size']) ? (int) ($this->settings['fes_max_file_size'] * 1000) : (5000 * 1000);

        // Invalid Size
        if($uploaded_file['size'] > $max_file_size) $this->main->response(array('success'=>0, 'code'=>'IMAGE_IS_TOO_BIG', 'message'=>sprintf(esc_html__('Image is too big. Maximum size is %s KB.', 'modern-events-calendar-lite'), ($max_file_size / 1000))));

        $movefile = wp_handle_upload($uploaded_file, array('test_form'=>false));

        $success = 0;
        $data = array();

        if($movefile and !isset($movefile['error']))
        {
            $success = 1;
            $message = __('Image uploaded!', 'modern-events-calendar-lite');

            $data['url'] = $movefile['url'];
        }
        else
        {
            $message = $movefile['error'];
        }

        $this->main->response(array('success'=>$success, 'message'=>$message, 'data'=>$data));
    }

    public function fes_form()
    {
        // Check if our nonce is set.
        if(!isset($_POST['_wpnonce'])) $this->main->response(array('success'=>0, 'code'=>'NONCE_MISSING'));

        // Verify that the nonce is valid.
        if(!wp_verify_nonce(sanitize_text_field($_POST['_wpnonce']), 'mec_fes_form')) $this->main->response(array('success'=>0, 'code'=>'NONCE_IS_INVALID'));

        $mec = isset($_POST['mec']) ? $_POST['mec'] : array();

        // Google recaptcha
        if($this->main->get_recaptcha_status('fes'))
        {
            $g_recaptcha_response = isset($_POST['g-recaptcha-response']) ? sanitize_text_field($_POST['g-recaptcha-response']) : NULL;
            if(!$this->main->get_recaptcha_response($g_recaptcha_response)) $this->main->response(array('success'=>0, 'message'=>__('Invalid Captcha! Please try again.', 'modern-events-calendar-lite'), 'code'=>'CAPTCHA_IS_INVALID'));
        }

        $post_id = isset($mec['post_id']) ? sanitize_text_field($mec['post_id']) : -1;

        $start_date = (isset($mec['date']['start']['date']) and trim($mec['date']['start']['date'])) ? $this->main->standardize_format($mec['date']['start']['date']) : date('Y-m-d');
        $end_date = (isset($mec['date']['end']['date']) and trim($mec['date']['end']['date'])) ? $this->main->standardize_format($mec['date']['end']['date']) : date('Y-m-d');

        $post_title = isset($mec['title']) ? sanitize_text_field($mec['title']) : '';
        $post_content = isset($mec['content']) ? $mec['content'] : '';
        $post_excerpt = isset($mec['excerpt']) ? $mec['excerpt'] : '';
        $post_tags = isset($mec['tags']) ? sanitize_text_field($mec['tags']) : '';
        $post_categories = isset($mec['categories']) ? $mec['categories'] : array();
        $post_speakers = isset($mec['speakers']) ? $mec['speakers'] : array();
        $post_labels = isset($mec['labels']) ? $mec['labels'] : array();
        $featured_image = isset($mec['featured_image']) ? sanitize_text_field($mec['featured_image']) : '';

        // Title is Required
        if(!trim($post_title)) $this->main->response(array('success'=>0, 'message'=>__('Please fill event title field!', 'modern-events-calendar-lite'), 'code'=>'TITLE_IS_EMPTY'));

        // Body is Required
        if(isset($this->settings['fes_required_body']) and $this->settings['fes_required_body'] and !trim($post_content)) $this->main->response(array('success'=>0, 'message'=>__('Please fill event body field!', 'modern-events-calendar-lite'), 'code'=>'BODY_IS_EMPTY'));

        // Category is Required
        if(isset($this->settings['fes_section_categories']) and $this->settings['fes_section_categories'] and isset($this->settings['fes_required_category']) and $this->settings['fes_required_category'] and is_array($post_categories) and !count($post_categories)) $this->main->response(array('success'=>0, 'message'=>__('Please select at-least one category!', 'modern-events-calendar-lite'), 'code'=>'CATEGORY_IS_EMPTY'));

        // Label is Required
        if(isset($this->settings['fes_section_labels']) and $this->settings['fes_section_labels'] and isset($this->settings['fes_required_label']) and $this->settings['fes_required_label'] and is_array($post_labels) and !count($post_labels)) $this->main->response(array('success'=>0, 'message'=>__('Please select at-least one label!', 'modern-events-calendar-lite'), 'code'=>'LABEL_IS_EMPTY'));

        // Post Status
        $status = 'pending';
        if(current_user_can('publish_posts')) $status = 'publish';

        $method = 'updated';

        // Create new event
        if($post_id == -1)
        {
            // Force Status
            if(isset($this->settings['fes_new_event_status']) and trim($this->settings['fes_new_event_status'])) $status = $this->settings['fes_new_event_status'];

            $post = array('post_title'=>$post_title, 'post_content'=>$post_content, 'post_excerpt'=>$post_excerpt, 'post_type'=>$this->PT, 'post_status'=>$status);
            $post_id = wp_insert_post($post);

            $method = 'added';
        }

        wp_update_post(array('ID'=>$post_id, 'post_title'=>$post_title, 'post_content'=>$post_content, 'post_excerpt'=>$post_excerpt,));

        // Categories Section
        if(!isset($this->settings['fes_section_categories']) or (isset($this->settings['fes_section_categories']) and $this->settings['fes_section_categories']))
        {
            // Categories
            $categories = array();
            foreach($post_categories as $post_category=>$value) $categories[] = (int) $post_category;

            wp_set_post_terms($post_id, $categories, 'mec_category');
        }

        // Speakers Section
        if(!isset($this->settings['fes_section_speaker']) or (isset($this->settings['fes_section_speaker']) and $this->settings['fes_section_speaker']))
        {
            // Speakers
            if(isset($this->settings['speakers_status']) and $this->settings['speakers_status'])
            {
                $speakers = array();
                foreach($post_speakers as $post_speaker=>$value) $speakers[] = (int) $post_speaker;

                wp_set_post_terms($post_id, $speakers, 'mec_speaker');
            }
        }

        // Labels Section
        if(!isset($this->settings['fes_section_labels']) or (isset($this->settings['fes_section_labels']) and $this->settings['fes_section_labels']))
        {
            // Labels
            $labels = array();
            foreach($post_labels as $post_label=>$value) $labels[] = (int) $post_label;

            wp_set_post_terms($post_id, $labels, 'mec_label');
            do_action('mec_label_change_to_radio', $labels, $post_labels, $post_id);
        }

        // Color Section
        if(!isset($this->settings['fes_section_event_color']) or (isset($this->settings['fes_section_event_color']) and $this->settings['fes_section_event_color']))
        {
            // Color
            $color = isset($mec['color']) ? sanitize_text_field(trim($mec['color'], '# ')) : '';
            update_post_meta($post_id, 'mec_color', $color);
        }

        // Tags Section
        if(!isset($this->settings['fes_section_tags']) or (isset($this->settings['fes_section_tags']) and $this->settings['fes_section_tags']))
        {
            // Tags
            wp_set_post_terms($post_id, $post_tags, apply_filters('mec_taxonomy_tag', ''));
        }

        // Featured Image Section
        if(!isset($this->settings['fes_section_featured_image']) or (isset($this->settings['fes_section_featured_image']) and $this->settings['fes_section_featured_image']))
        {
            // Featured Image
            if(trim($featured_image)) $this->main->set_featured_image($featured_image, $post_id);
            else delete_post_thumbnail($post_id);
        }

        // Links Section
        if(!isset($this->settings['fes_section_event_links']) or (isset($this->settings['fes_section_event_links']) and $this->settings['fes_section_event_links']))
        {
            $read_more = isset($mec['read_more']) ? esc_url($mec['read_more']) : '';
            $more_info = (isset($mec['more_info']) and trim($mec['more_info'])) ? esc_url($mec['more_info']) : '';
            $more_info_title = isset($mec['more_info_title']) ? sanitize_text_field($mec['more_info_title']) : '';
            $more_info_target = isset($mec['more_info_target']) ? sanitize_text_field($mec['more_info_target']) : '';

            update_post_meta($post_id, 'mec_read_more', $read_more);
            update_post_meta($post_id, 'mec_more_info', $more_info);
            update_post_meta($post_id, 'mec_more_info_title', $more_info_title);
            update_post_meta($post_id, 'mec_more_info_target', $more_info_target);
        }

        // Cost Section
        if(!isset($this->settings['fes_section_cost']) or (isset($this->settings['fes_section_cost']) and $this->settings['fes_section_cost']))
        {
            $cost = isset($mec['cost']) ? $mec['cost'] : '';
            $cost = apply_filters(
                'mec_event_cost_sanitize',
                sanitize_text_field($cost),
                $cost
            );

            $currency_options = ((isset($mec['currency']) and is_array($mec['currency'])) ? $mec['currency'] : array());

            update_post_meta($post_id, 'mec_cost', $cost);
            update_post_meta($post_id, 'mec_currency', $currency_options);
        }

        // Guest Name and Email
        $fes_guest_email = isset($mec['fes_guest_email']) ? sanitize_email($mec['fes_guest_email']) : '';
        $fes_guest_name = isset($mec['fes_guest_name']) ? sanitize_text_field($mec['fes_guest_name']) : '';
        $note = isset($mec['note']) ? sanitize_text_field($mec['note']) : '';

        update_post_meta($post_id, 'fes_guest_email', $fes_guest_email);
        update_post_meta($post_id, 'fes_guest_name', $fes_guest_name);
        update_post_meta($post_id, 'mec_note', $note);

        // Location Section
        if(!isset($this->settings['fes_section_location']) or (isset($this->settings['fes_section_location']) and $this->settings['fes_section_location']))
        {
            // Location
            $location_id = isset($mec['location_id']) ? sanitize_text_field($mec['location_id']) : 1;

            // Selected a saved location
            if($location_id)
            {
                // Set term to the post
                wp_set_object_terms($post_id, (int) $location_id, 'mec_location');
            }
            else
            {
                $address = (isset($mec['location']['address']) and trim($mec['location']['address'])) ? sanitize_text_field($mec['location']['address']) : '';
                $name = (isset($mec['location']['name']) and trim($mec['location']['name'])) ? sanitize_text_field($mec['location']['name']) : (trim($address) ? $address : 'Location Name');

                $term = get_term_by('name', $name, 'mec_location');

                // Term already exists
                if(is_object($term) and isset($term->term_id))
                {
                    // Set term to the post
                    wp_set_object_terms($post_id, (int) $term->term_id, 'mec_location');
                }
                else
                {
                    $term = wp_insert_term($name, 'mec_location');

                    $location_id = $term['term_id'];
                    if($location_id)
                    {
                        // Set term to the post
                        wp_set_object_terms($post_id, (int) $location_id, 'mec_location');

                        $latitude = (isset($mec['location']['latitude']) and trim($mec['location']['latitude'])) ? sanitize_text_field($mec['location']['latitude']) : 0;
                        $longitude = (isset($mec['location']['longitude']) and trim($mec['location']['longitude'])) ? sanitize_text_field($mec['location']['longitude']) : 0;
                        $url = (isset($mec['location']['url']) and trim($mec['location']['url'])) ? esc_url($mec['location']['url']) : '';
                        $thumbnail = (isset($mec['location']['thumbnail']) and trim($mec['location']['thumbnail'])) ? sanitize_text_field($mec['location']['thumbnail']) : '';

                        if(!trim($latitude) or !trim($longitude))
                        {
                            $geo_point = $this->main->get_lat_lng($address);

                            $latitude = $geo_point[0];
                            $longitude = $geo_point[1];
                        }

                        update_term_meta($location_id, 'address', $address);
                        update_term_meta($location_id, 'latitude', $latitude);
                        update_term_meta($location_id, 'longitude', $longitude);
                        update_term_meta($location_id, 'url', $url);
                        update_term_meta($location_id, 'thumbnail', $thumbnail);
                    }
                    else $location_id = 1;
                }
            }

            update_post_meta($post_id, 'mec_location_id', $location_id);

            $dont_show_map = isset($mec['dont_show_map']) ? sanitize_text_field($mec['dont_show_map']) : 0;
            update_post_meta($post_id, 'mec_dont_show_map', $dont_show_map);
        }

        // Organizer Section
        if(!isset($this->settings['fes_section_organizer']) or (isset($this->settings['fes_section_organizer']) and $this->settings['fes_section_organizer']))
        {
            // Organizer
            $organizer_id = isset($mec['organizer_id']) ? sanitize_text_field($mec['organizer_id']) : 1;

            // Selected a saved organizer
            if(isset($organizer_id) and $organizer_id)
            {
                // Set term to the post
                wp_set_object_terms($post_id, (int) $organizer_id, 'mec_organizer');
            }
            else
            {
                $name = (isset($mec['organizer']['name']) and trim($mec['organizer']['name'])) ? sanitize_text_field($mec['organizer']['name']) : 'Organizer Name';

                $term = get_term_by('name', $name, 'mec_organizer');

                // Term already exists
                if(is_object($term) and isset($term->term_id))
                {
                    // Set term to the post
                    wp_set_object_terms($post_id, (int) $term->term_id, 'mec_organizer');
                }
                else
                {
                    $term = wp_insert_term($name, 'mec_organizer');

                    $organizer_id = $term['term_id'];
                    if($organizer_id)
                    {
                        // Set term to the post
                        wp_set_object_terms($post_id, (int) $organizer_id, 'mec_organizer');

                        $tel = (isset($mec['organizer']['tel']) and trim($mec['organizer']['tel'])) ? sanitize_text_field($mec['organizer']['tel']) : '';
                        $email = (isset($mec['organizer']['email']) and trim($mec['organizer']['email'])) ? sanitize_text_field($mec['organizer']['email']) : '';
                        $url = (isset($mec['organizer']['url']) and trim($mec['organizer']['url'])) ? (strpos($mec['organizer']['url'], 'http') === false ? 'http://'.sanitize_text_field($mec['organizer']['url']) : sanitize_text_field($mec['organizer']['url'])) : '';
                        $thumbnail = (isset($mec['organizer']['thumbnail']) and trim($mec['organizer']['thumbnail'])) ? sanitize_text_field($mec['organizer']['thumbnail']) : '';

                        update_term_meta($organizer_id, 'tel', $tel);
                        update_term_meta($organizer_id, 'email', $email);
                        update_term_meta($organizer_id, 'url', $url);
                        update_term_meta($organizer_id, 'thumbnail', $thumbnail);
                    }
                    else $organizer_id = 1;
                }
            }

            update_post_meta($post_id, 'mec_organizer_id', $organizer_id);

            // Additional Organizers
            $additional_organizer_ids = isset($mec['additional_organizer_ids']) ? $mec['additional_organizer_ids'] : array();

            foreach($additional_organizer_ids as $additional_organizer_id) wp_set_object_terms($post_id, (int) $additional_organizer_id, 'mec_organizer', true);
            update_post_meta($post_id, 'mec_additional_organizer_ids', $additional_organizer_ids);

            // Additional locations
            $additional_location_ids = isset($mec['additional_location_ids']) ? $mec['additional_location_ids'] : array();

            foreach($additional_location_ids as $additional_location_id) wp_set_object_terms($post_id, (int) $additional_location_id, 'mec_location', true);
            update_post_meta($post_id, 'mec_additional_location_ids', $additional_location_ids);
        }

        // Date Options
        $date = isset($mec['date']) ? $mec['date'] : array();

        $start_date = date('Y-m-d', strtotime($start_date));

        // Set the start date
        $date['start']['date'] = $start_date;

        $start_time_hour = isset($date['start']) ? $date['start']['hour'] : '8';
        $start_time_minutes = isset($date['start']) ? $date['start']['minutes'] : '00';
        $start_time_ampm = (isset($date['start']) and isset($date['start']['ampm'])) ? $date['start']['ampm'] : 'AM';

        $end_date = date('Y-m-d', strtotime($end_date));

        // Fix end_date if it's smaller than start_date
        if(strtotime($end_date) < strtotime($start_date)) $end_date = $start_date;

        // Set the end date
        $date['end']['date'] = $end_date;

        $end_time_hour = isset($date['end']) ? $date['end']['hour'] : '6';
        $end_time_minutes = isset($date['end']) ? $date['end']['minutes'] : '00';
        $end_time_ampm = (isset($date['end']) and isset($date['end']['ampm'])) ? $date['end']['ampm'] : 'PM';

        if(isset($this->settings['time_format']) and $this->settings['time_format'] == 24)
        {
            $day_start_seconds = $this->main->time_to_seconds($this->main->to_24hours($start_time_hour, NULL), $start_time_minutes);
            $day_end_seconds = $this->main->time_to_seconds($this->main->to_24hours($end_time_hour, NULL), $end_time_minutes);
        }
        else
        {
            $day_start_seconds = $this->main->time_to_seconds($this->main->to_24hours($start_time_hour, $start_time_ampm), $start_time_minutes);
            $day_end_seconds = $this->main->time_to_seconds($this->main->to_24hours($end_time_hour, $end_time_ampm), $end_time_minutes);
        }

        if($end_date === $start_date and $day_end_seconds < $day_start_seconds)
        {
            $day_end_seconds = $day_start_seconds;

            $end_time_hour = $start_time_hour;
            $end_time_minutes = $start_time_minutes;
            $end_time_ampm = $start_time_ampm;

            $date['end']['hour'] = $start_time_hour;
            $date['end']['minutes'] = $start_time_minutes;
            $date['end']['ampm'] = $start_time_ampm;
        }

        // If 24 hours format is enabled then convert it back to 12 hours
        if(isset($this->settings['time_format']) and $this->settings['time_format'] == 24)
        {
            if($start_time_hour < 12) $start_time_ampm = 'AM';
            elseif($start_time_hour == 12) $start_time_ampm = 'PM';
            elseif($start_time_hour > 12)
            {
                $start_time_hour -= 12;
                $start_time_ampm = 'PM';
            }
            elseif($start_time_hour == 0)
            {
                $start_time_hour = 12;
                $start_time_ampm = 'AM';
            }

            if($end_time_hour < 12) $end_time_ampm = 'AM';
            elseif($end_time_hour == 12) $end_time_ampm = 'PM';
            elseif($end_time_hour > 12)
            {
                $end_time_hour -= 12;
                $end_time_ampm = 'PM';
            }
            elseif($end_time_hour == 0)
            {
                $end_time_hour = 12;
                $end_time_ampm = 'AM';
            }

            // Set converted values to date array
            $date['start']['hour'] = $start_time_hour;
            $date['start']['ampm'] = $start_time_ampm;

            $date['end']['hour'] = $end_time_hour;
            $date['end']['ampm'] = $end_time_ampm;
        }

        $allday = isset($date['allday']) ? 1 : 0;
        $one_occurrence = isset($date['one_occurrence']) ? 1 : 0;
        $hide_time = isset($date['hide_time']) ? 1 : 0;
        $hide_end_time = isset($date['hide_end_time']) ? 1 : 0;
        $comment = isset($date['comment']) ? sanitize_text_field($date['comment']) : '';
        $timezone = (isset($mec['timezone']) and trim($mec['timezone']) != '') ? sanitize_text_field($mec['timezone']) : 'global';
        $countdown_method = (isset($mec['countdown_method']) and trim($mec['countdown_method']) != '') ? sanitize_text_field($mec['countdown_method']) : 'global';
        $public = (isset($mec['public']) and trim($mec['public']) != '') ? sanitize_text_field($mec['public']) : 1;

        // Set start time and end time if event is all day
        if($allday == 1)
        {
            $start_time_hour = '8';
            $start_time_minutes = '00';
            $start_time_ampm = 'AM';

            $end_time_hour = '6';
            $end_time_minutes = '00';
            $end_time_ampm = 'PM';
        }

        update_post_meta($post_id, 'mec_start_date', $start_date);
        update_post_meta($post_id, 'mec_start_time_hour', $start_time_hour);
        update_post_meta($post_id, 'mec_start_time_minutes', $start_time_minutes);
        update_post_meta($post_id, 'mec_start_time_ampm', $start_time_ampm);
        update_post_meta($post_id, 'mec_start_day_seconds', $day_start_seconds);

        update_post_meta($post_id, 'mec_end_date', $end_date);
        update_post_meta($post_id, 'mec_end_time_hour', $end_time_hour);
        update_post_meta($post_id, 'mec_end_time_minutes', $end_time_minutes);
        update_post_meta($post_id, 'mec_end_time_ampm', $end_time_ampm);
        update_post_meta($post_id, 'mec_end_day_seconds', $day_end_seconds);

        update_post_meta($post_id, 'mec_date', $date);

        // Repeat Options
        $repeat = isset($date['repeat']) ? $date['repeat'] : array();
        $certain_weekdays = isset($repeat['certain_weekdays']) ? $repeat['certain_weekdays'] : array();

        $repeat_status = isset($repeat['status']) ? 1 : 0;
        $repeat_type = ($repeat_status and isset($repeat['type'])) ? $repeat['type'] : '';

        $repeat_interval = ($repeat_status and isset($repeat['interval']) and trim($repeat['interval'])) ? $repeat['interval'] : 1;

        // Advanced Repeat
        $advanced = isset( $repeat['advanced'] ) ? sanitize_text_field($repeat['advanced']) : '';

        if(!is_numeric($repeat_interval)) $repeat_interval = NULL;

        if($repeat_type == 'weekly') $interval_multiply = 7;
        else $interval_multiply = 1;

        // Reset certain weekdays if repeat type is not set to certain weekdays
        if($repeat_type != 'certain_weekdays') $certain_weekdays = array();

        if(!is_null($repeat_interval)) $repeat_interval = $repeat_interval*$interval_multiply;

        // String To Array
		if($repeat_type == 'advanced' and trim($advanced)) $advanced = explode('-', $advanced);
        else $advanced = array();

        $repeat_end = ($repeat_status and isset($repeat['end'])) ? $repeat['end'] : '';
        $repeat_end_at_occurrences = ($repeat_status and isset($repeat['end_at_occurrences'])) ? ($repeat['end_at_occurrences']-1) : '';
        $repeat_end_at_date = ($repeat_status and isset($repeat['end_at_date'])) ? $this->main->standardize_format( $repeat['end_at_date'] ) : '';

        update_post_meta($post_id, 'mec_date', $date);
        update_post_meta($post_id, 'mec_repeat', $repeat);
        update_post_meta($post_id, 'mec_certain_weekdays', $certain_weekdays);
        update_post_meta($post_id, 'mec_allday', $allday);
        update_post_meta($post_id, 'one_occurrence', $one_occurrence);
        update_post_meta($post_id, 'mec_hide_time', $hide_time);
        update_post_meta($post_id, 'mec_hide_end_time', $hide_end_time);
        update_post_meta($post_id, 'mec_comment', $comment);
        update_post_meta($post_id, 'mec_timezone', $timezone);
        update_post_meta($post_id, 'mec_countdown_method', $countdown_method);
        update_post_meta($post_id, 'mec_public', $public);
        update_post_meta($post_id, 'mec_repeat_status', $repeat_status);
        update_post_meta($post_id, 'mec_repeat_type', $repeat_type);
        update_post_meta($post_id, 'mec_repeat_interval', $repeat_interval);
        update_post_meta($post_id, 'mec_repeat_end', $repeat_end);
        update_post_meta($post_id, 'mec_repeat_end_at_occurrences', $repeat_end_at_occurrences);
        update_post_meta($post_id, 'mec_repeat_end_at_date', $repeat_end_at_date);
        update_post_meta($post_id, 'mec_advanced_days', $advanced);

        // Creating $event array for inserting in mec_events table
        $event = array('post_id'=>$post_id, 'start'=>$start_date, 'repeat'=>$repeat_status, 'rinterval'=>(!in_array($repeat_type, array('daily', 'weekly', 'monthly')) ? NULL : $repeat_interval), 'time_start'=>$day_start_seconds, 'time_end'=>$day_end_seconds);

        $year = NULL;
        $month = NULL;
        $day = NULL;
        $week = NULL;
        $weekday = NULL;
        $weekdays = NULL;

        // MEC weekdays
        $mec_weekdays = $this->main->get_weekdays();

        // MEC weekends
        $mec_weekends = $this->main->get_weekends();

        $plus_date = null;
        if($repeat_type == 'daily')
        {
            $plus_date = '+'.$repeat_end_at_occurrences*$repeat_interval.' Days';
        }
        elseif($repeat_type == 'weekly')
        {
            $plus_date = '+'.$repeat_end_at_occurrences*($repeat_interval).' Days';
        }
        elseif($repeat_type == 'weekday')
        {
            $repeat_interval = 1;
            $plus_date = '+'.$repeat_end_at_occurrences*$repeat_interval.' Weekdays';

            $weekdays = ','.implode(',', $mec_weekdays).',';
        }
        elseif($repeat_type == 'weekend')
        {
            $repeat_interval = 1;
            $plus_date = '+'.round($repeat_end_at_occurrences/2)*($repeat_interval*7).' Days';

            $weekdays = ','.implode(',', $mec_weekends).',';
        }
        elseif($repeat_type == 'certain_weekdays')
        {
            $repeat_interval = 1;
            $plus_date = '+' . ceil(($repeat_end_at_occurrences * $repeat_interval) * (7/count($certain_weekdays))) . ' days';

            $weekdays = ','.implode(',', $certain_weekdays).',';
        }
        elseif($repeat_type == 'monthly')
        {
            $plus_date = '+'.$repeat_end_at_occurrences*$repeat_interval.' Months';

            $year = '*';
            $month = '*';

            $s = $start_date;
            $e = $end_date;

            $_days = array();
            while(strtotime($s) <= strtotime($e))
            {
                $_days[] = date('d', strtotime($s));
                $s = date('Y-m-d', strtotime('+1 Day', strtotime($s)));
            }

            $day = ','.implode(',', array_unique($_days)).',';

            $week = '*';
            $weekday = '*';
        }
        elseif($repeat_type == 'yearly')
        {
            $plus_date = '+'.$repeat_end_at_occurrences*$repeat_interval.' Years';

            $year = '*';

            $s = $start_date;
            $e = $end_date;

            $_months = array();
            $_days = array();
            while(strtotime($s) <= strtotime($e))
            {
                $_months[] = date('m', strtotime($s));
                $_days[] = date('d', strtotime($s));

                $s = date('Y-m-d', strtotime('+1 Day', strtotime($s)));
            }

            $month = ','.implode(',', array_unique($_months)).',';
            $day = ','.implode(',', array_unique($_days)).',';

            $week = '*';
            $weekday = '*';
        }
        elseif($repeat_type == "advanced")
        {
            // Render class object
            $this->render = $this->getRender();

            // Get finish date
            $event_info = array('start' => $date['start'], 'end' => $date['end']);
            $dates = $this->render->generate_advanced_days($advanced, $event_info, $repeat_end_at_occurrences +1, date( 'Y-m-d', current_time( 'timestamp', 0 )), 'events');

            $period_date = $this->main->date_diff($start_date, end($dates)['end']['date']);

            $plus_date = '+' . $period_date->days . ' Days';
        }

        // "In Days" and "Not In Days"
        $in_days_arr = (isset($mec['in_days']) and is_array($mec['in_days']) and count($mec['in_days'])) ? array_unique($mec['in_days']) : array();
        $not_in_days_arr = (isset($mec['not_in_days']) and is_array($mec['not_in_days']) and count($mec['not_in_days'])) ? array_unique($mec['not_in_days']) : array();

        $in_days = '';
        if(count($in_days_arr))
        {
            if(isset($in_days_arr[':i:'])) unset($in_days_arr[':i:']);

            $in_days_arr = array_map(function($value)
            {
                $ex = explode(':', $value);

                $in_days_times = '';
                if(isset($ex[2]) and isset($ex[3]))
                {
                    $in_days_start_time = $ex[2];
                    $in_days_end_time = $ex[3];

                    // If 24 hours format is enabled then convert it back to 12 hours
                    if(isset($this->settings['time_format']) and $this->settings['time_format'] == 24)
                    {
                        $ex_start_time = explode('-', $in_days_start_time);
                        $ex_end_time = explode('-', $in_days_end_time);

                        $in_days_start_hour = $ex_start_time[0];
                        $in_days_start_minutes = $ex_start_time[1];
                        $in_days_start_ampm = $ex_start_time[2];

                        $in_days_end_hour = $ex_end_time[0];
                        $in_days_end_minutes = $ex_end_time[1];
                        $in_days_end_ampm = $ex_end_time[2];

                        if(trim($in_days_start_ampm) == '')
                        {
                            if($in_days_start_hour < 12) $in_days_start_ampm = 'AM';
                            elseif($in_days_start_hour == 12) $in_days_start_ampm = 'PM';
                            elseif($in_days_start_hour > 12)
                            {
                                $in_days_start_hour -= 12;
                                $in_days_start_ampm = 'PM';
                            }
                            elseif($in_days_start_hour == 0)
                            {
                                $in_days_start_hour = 12;
                                $in_days_start_ampm = 'AM';
                            }
                        }

                        if(trim($in_days_end_ampm) == '')
                        {
                            if($in_days_end_hour < 12) $in_days_end_ampm = 'AM';
                            elseif($in_days_end_hour == 12) $in_days_end_ampm = 'PM';
                            elseif($in_days_end_hour > 12)
                            {
                                $in_days_end_hour -= 12;
                                $in_days_end_ampm = 'PM';
                            }
                            elseif($in_days_end_hour == 0)
                            {
                                $in_days_end_hour = 12;
                                $in_days_end_ampm = 'AM';
                            }
                        }

                        if(strlen($in_days_start_hour) == 1) $in_days_start_hour = '0'.$in_days_start_hour;
                        if(strlen($in_days_start_minutes) == 1) $in_days_start_minutes = '0'.$in_days_start_minutes;

                        if(strlen($in_days_end_hour) == 1) $in_days_end_hour = '0'.$in_days_end_hour;
                        if(strlen($in_days_end_minutes) == 1) $in_days_end_minutes = '0'.$in_days_end_minutes;

                        $in_days_start_time = $in_days_start_hour.'-'.$in_days_start_minutes.'-'.$in_days_start_ampm;
                        $in_days_end_time = $in_days_end_hour.'-'.$in_days_end_minutes.'-'.$in_days_end_ampm;
                    }

                    $in_days_times = ':'.$in_days_start_time.':'.$in_days_end_time;
                }

                return $this->main->standardize_format($ex[0]) . ':' . $this->main->standardize_format($ex[1]).$in_days_times;
            }, $in_days_arr);

            usort($in_days_arr, function($a, $b)
            {
                $ex_a = explode(':', $a);
                $ex_b = explode(':', $b);

                $date_a = $ex_a[0];
                $date_b = $ex_b[0];

                $in_day_a_time_label = '';
                if(isset($ex_a[2]))
                {
                    $in_day_a_time = $ex_a[2];
                    $pos = strpos($in_day_a_time, '-');
                    if($pos !== false) $in_day_a_time_label = substr_replace($in_day_a_time, ':', $pos, 1);

                    $in_day_a_time_label = str_replace('-', ' ', $in_day_a_time_label);
                }

                $in_day_b_time_label = '';
                if(isset($ex_b[2]))
                {
                    $in_day_b_time = $ex_b[2];
                    $pos = strpos($in_day_b_time, '-');
                    if($pos !== false) $in_day_b_time_label = substr_replace($in_day_b_time, ':', $pos, 1);

                    $in_day_b_time_label = str_replace('-', ' ', $in_day_b_time_label);
                }

                return strtotime(trim($date_a.' '.$in_day_a_time_label)) - strtotime(trim($date_b.' '.$in_day_b_time_label));
            });

            if(!isset($in_days_arr[':i:'])) $in_days_arr[':i:'] = ':val:';
            foreach($in_days_arr as $key => $in_day_arr)
            {
                if(is_numeric($key)) $in_days .= $in_day_arr . ',';
            }
        }

        $not_in_days = '';
        if(count($not_in_days_arr))
        {
            foreach($not_in_days_arr as $key => $not_in_day_arr)
            {
                if(is_numeric($key)) $not_in_days .= $this->main->standardize_format($not_in_day_arr) . ',';
            }
        }

        $in_days = trim($in_days, ', ');
        $not_in_days = trim($not_in_days, ', ');

        update_post_meta($post_id, 'mec_in_days', $in_days);
        update_post_meta($post_id, 'mec_not_in_days', $not_in_days);

        // Repeat End Date
        if($repeat_end == 'never') $repeat_end_date = '0000-00-00';
        elseif($repeat_end == 'date') $repeat_end_date = $repeat_end_at_date;
        elseif($repeat_end == 'occurrences')
        {
            if($plus_date) $repeat_end_date = date('Y-m-d', strtotime($plus_date, strtotime($end_date)));
            else $repeat_end_date = '0000-00-00';
        }
        else $repeat_end_date = '0000-00-00';

        // If event is not repeating then set the end date of event correctly
        if(!$repeat_status or $repeat_type == 'custom_days') $repeat_end_date = $end_date;

        // Add parameters to the $event
        $event['end'] = $repeat_end_date;
        $event['year'] = $year;
        $event['month'] = $month;
        $event['day'] = $day;
        $event['week'] = $week;
        $event['weekday'] = $weekday;
        $event['weekdays'] = $weekdays;
        $event['days'] = $in_days;
        $event['not_in_days'] = $not_in_days;

        // Update MEC Events Table
        $mec_event_id = $this->db->select($this->db->prepare("SELECT `id` FROM `#__mec_events` WHERE `post_id` = %d", $post_id), 'loadResult');

        if(!$mec_event_id)
        {
            $q1 = "";
            $q2 = "";

            foreach($event as $key=>$value)
            {
                $q1 .= "`$key`,";

                if(is_null($value)) $q2 .= "NULL,";
                else $q2 .= "'$value',";
            }

            $this->db->q("INSERT INTO `#__mec_events` (".trim($q1, ', ').") VALUES (".trim($q2, ', ').")", 'INSERT');
        }
        else
        {
            $q = "";

            foreach($event as $key=>$value)
            {
                if(is_null($value)) $q .= "`$key`=NULL,";
                else $q .= "`$key`='$value',";
            }

            $this->db->q("UPDATE `#__mec_events` SET ".trim($q, ', ')." WHERE `id`='$mec_event_id'");
        }

        // Update Schedule
        $schedule = $this->getSchedule();
        $schedule->reschedule($post_id, $schedule->get_reschedule_maximum($repeat_type));

        // Hourly Schedule
        if(!isset($this->settings['fes_section_hourly_schedule']) or (isset($this->settings['fes_section_hourly_schedule']) and $this->settings['fes_section_hourly_schedule']))
        {
            // Hourly Schedule Options
            $raw_hourly_schedules = isset($mec['hourly_schedules']) ? $mec['hourly_schedules'] : array();
            unset($raw_hourly_schedules[':d:']);

            $hourly_schedules = array();
            foreach($raw_hourly_schedules as $raw_hourly_schedule)
            {
                unset($raw_hourly_schedule['schedules'][':i:']);
                $hourly_schedules[] = $raw_hourly_schedule;
            }

            update_post_meta($post_id, 'mec_hourly_schedules', $hourly_schedules);
        }

        // Booking Options
        if(!isset($this->settings['fes_section_booking']) or (isset($this->settings['fes_section_booking']) and $this->settings['fes_section_booking']))
        {
            // Booking and Ticket Options
            $booking = isset($mec['booking']) ? $mec['booking'] : array();
            update_post_meta($post_id, 'mec_booking', $booking);

            // Tickets
            if(!isset($this->settings['fes_section_tickets']) or (isset($this->settings['fes_section_tickets']) and $this->settings['fes_section_tickets']))
            {
                $tickets = isset($mec['tickets']) ? $mec['tickets'] : array();
                unset($tickets[':i:']);

                // Unset Ticket Dats
                if(count($tickets))
                {
                    $new_tickets = array();
                    foreach($tickets as $key => $ticket)
                    {
                        unset($ticket['dates'][':j:']);

                        $ticket_start_time_ampm = ((intval($ticket['ticket_start_time_hour']) > 0 and intval($ticket['ticket_start_time_hour']) < 13) and isset($ticket['ticket_start_time_ampm'])) ? $ticket['ticket_start_time_ampm'] : '';
                        $ticket_render_start_time = date('h:ia', strtotime(sprintf('%02d', $ticket['ticket_start_time_hour']) . ':' . sprintf('%02d', $ticket['ticket_start_time_minute']) . $ticket_start_time_ampm));
                        $ticket_end_time_ampm = ((intval($ticket['ticket_end_time_hour']) > 0 and intval($ticket['ticket_end_time_hour']) < 13) and isset($ticket['ticket_end_time_ampm'])) ? $ticket['ticket_end_time_ampm'] : '';
                        $ticket_render_end_time = date('h:ia', strtotime(sprintf('%02d', $ticket['ticket_end_time_hour']) . ':' . sprintf('%02d', $ticket['ticket_end_time_minute']) . $ticket_end_time_ampm));

                        $ticket['ticket_start_time_hour'] = substr($ticket_render_start_time, 0, 2);
                        $ticket['ticket_start_time_ampm'] = strtoupper(substr($ticket_render_start_time, 5, 6));
                        $ticket['ticket_end_time_hour'] = substr($ticket_render_end_time, 0, 2);
                        $ticket['ticket_end_time_ampm'] = strtoupper(substr($ticket_render_end_time, 5, 6));
                        $ticket['price'] = trim($ticket['price']);
                        $ticket['limit'] = trim($ticket['limit']);
                        $ticket['minimum_ticket'] = trim($ticket['minimum_ticket']);
                        $ticket['stop_selling_value'] = trim($ticket['stop_selling_value']);

                        // Bellow conditional block code is used to change ticket dates format to compatible ticket past dates structure for store in db.
                        if(isset($ticket['dates']))
                        {
                            foreach($ticket['dates'] as $dates_ticket_key => $dates_ticket_values)
                            {
                                if(isset($dates_ticket_values['start']) and trim($dates_ticket_values['start']))
                                {
                                    $ticket['dates'][$dates_ticket_key]['start'] = $this->main->standardize_format($dates_ticket_values['start']);
                                }

                                if(isset($dates_ticket_values['end']) and trim($dates_ticket_values['end']))
                                {
                                    $ticket['dates'][$dates_ticket_key]['end'] = $this->main->standardize_format($dates_ticket_values['end']);
                                }
                            }
                        }

                        $new_tickets[$key] = $ticket;
                    }

                    $tickets = $new_tickets;
                }

                update_post_meta($post_id, 'mec_tickets', $tickets);
            }

            // Fees
            if(!isset($this->settings['fes_section_fees']) or (isset($this->settings['fes_section_fees']) and $this->settings['fes_section_fees']))
            {
                // Fee options
                $fees_global_inheritance = isset($mec['fees_global_inheritance']) ? $mec['fees_global_inheritance'] : 1;
                update_post_meta($post_id, 'mec_fees_global_inheritance', $fees_global_inheritance);

                $fees = isset($mec['fees']) ? $mec['fees'] : array();
                update_post_meta($post_id, 'mec_fees', $fees);
            }

            // Variation
            if(!isset($this->settings['fes_section_ticket_variations']) or (isset($this->settings['fes_section_ticket_variations']) and $this->settings['fes_section_ticket_variations']))
            {
                // Ticket Variation options
                $ticket_variations_global_inheritance = isset($mec['ticket_variations_global_inheritance']) ? $mec['ticket_variations_global_inheritance'] : 1;
                update_post_meta($post_id, 'mec_ticket_variations_global_inheritance', $ticket_variations_global_inheritance);

                $ticket_variations = isset($mec['ticket_variations']) ? $mec['ticket_variations'] : array();
                update_post_meta($post_id, 'mec_ticket_variations', $ticket_variations);
            }

            // Booking Form
            if(!isset($this->settings['fes_section_reg_form']) or (isset($this->settings['fes_section_reg_form']) and $this->settings['fes_section_reg_form']))
            {
                // Registration Fields options
                $reg_fields_global_inheritance = isset($mec['reg_fields_global_inheritance']) ? $mec['reg_fields_global_inheritance'] : 1;
                update_post_meta($post_id, 'mec_reg_fields_global_inheritance', $reg_fields_global_inheritance);

                $reg_fields = isset($mec['reg_fields']) ? $mec['reg_fields'] : array();
                if($reg_fields_global_inheritance) $reg_fields = array();

                update_post_meta($post_id, 'mec_reg_fields', $reg_fields);

                $bfixed_fields = isset($mec['bfixed_fields']) ? $mec['bfixed_fields'] : array();
                if($reg_fields_global_inheritance) $bfixed_fields = array();

                update_post_meta($post_id, 'mec_bfixed_fields', $bfixed_fields);
            }
        }

        // Organizer Payment Options
        $op = isset($mec['op']) ? $mec['op'] : array();
        update_post_meta($post_id, 'mec_op', $op);
        update_user_meta(get_post_field('post_author', $post_id), 'mec_op', $op);

        // MEC Fields
        $fields = (isset($mec['fields']) and is_array($mec['fields'])) ? $mec['fields'] : array();
        update_post_meta($post_id, 'mec_fields', $fields);

        // Downloadable File
        if(isset($mec['downloadable_file']))
        {
            $dl_file = isset($mec['downloadable_file']) ? $mec['downloadable_file'] : '';
            update_post_meta($post_id, 'mec_dl_file', $dl_file);
        }

        do_action('save_fes_meta_action', $post_id, $mec);

        // For Event Notification Badge.
        if(isset($_REQUEST['mec']['post_id']) and trim($_REQUEST['mec']['post_id']) == '-1') update_post_meta($post_id, 'mec_event_date_submit', date('YmdHis', current_time('timestamp', 0)));

        $message = '';
        if($status == 'pending') $message = __('Event submitted. It will publish as soon as possible.', 'modern-events-calendar-lite');
        elseif($status == 'publish') $message = __('The event published.', 'modern-events-calendar-lite');

        // Trigger Event
        if($method == 'updated') do_action('mec_fes_updated', $post_id , 'update');
        else do_action('mec_fes_added', $post_id , '');

        // Save Event Data
        do_action('mec_save_event_data', $post_id, $mec);

        $redirect_to = ((isset($this->settings['fes_thankyou_page']) and trim($this->settings['fes_thankyou_page'])) ? get_permalink(intval($this->settings['fes_thankyou_page'])) : '');
        if(isset($this->settings['fes_thankyou_page_url']) and trim($this->settings['fes_thankyou_page_url'])) $redirect_to = esc_url($this->settings['fes_thankyou_page_url']);

        $this->main->response(array(
            'success' => 1,
            'message' => $message,
            'data'=> array(
                'post_id' => $post_id,
                'redirect_to' => $redirect_to,
            ),
        ));
    }

    public function link_add_event()
    {
        if(!$this->relative_link and isset($this->settings['fes_form_page']) and trim($this->settings['fes_form_page'])) return get_permalink($this->settings['fes_form_page']);
        else return $this->main->add_qs_var('post_id', '-1', $this->main->remove_qs_var('vlist'));
    }

    public function link_edit_event($post_id)
    {
        if(!$this->relative_link and isset($this->settings['fes_form_page']) and trim($this->settings['fes_form_page'])) return $this->main->add_qs_var('post_id', $post_id, get_permalink($this->settings['fes_form_page']));
        else return $this->main->add_qs_var('post_id', $post_id, $this->main->remove_qs_var('vlist'));
    }

    public function link_list_events()
    {
        if(!$this->relative_link and isset($this->settings['fes_list_page']) and trim($this->settings['fes_list_page'])) return get_permalink($this->settings['fes_list_page']);
        else return $this->main->add_qs_var('vlist', 1, $this->main->remove_qs_var('post_id'));
    }

    /**
     * @param string $new_status
     * @param string $old_status
     * @param WP_Post $post
     */
    public function status_changed($new_status, $old_status, $post)
    {
        // User creation is not enabled
        if(!isset($this->settings['fes_guest_user_creation']) or (isset($this->settings['fes_guest_user_creation']) and !$this->settings['fes_guest_user_creation'])) return;

        if(('publish' === $new_status && 'publish' !== $old_status) && $this->PT === $post->post_type)
        {
            $guest_email = get_post_meta($post->ID, 'fes_guest_email', true);
            if(!trim($guest_email) or (trim($guest_email) and !is_email($guest_email))) return;

            $user_id = 0;
            $user_exists = email_exists($guest_email);

            if($user_exists and $user_exists == $post->post_author) return;
            elseif($user_exists) $user_id = $user_exists;
            else
            {
                $registered = register_new_user($guest_email, $guest_email);
                if(!is_wp_error($registered))
                {
                    $user_id = $registered;

                    $guest_name = get_post_meta($post->ID, 'fes_guest_name', true);
                    $ex = explode(' ', $guest_name);

                    $first_name = $ex[0];
                    unset($ex[0]);

                    $last_name = implode(' ', $ex);

                    wp_update_user(array(
                        'ID' => $user_id,
                        'first_name' => $first_name,
                        'last_name' => $last_name,
                    ));

                    $user = new WP_User($user_id);
                    $user->set_role('author');
                }
            }

            if($user_id)
            {
                $db = $this->getDB();
                $db->q("UPDATE `#__posts` SET `post_author`='$user_id' WHERE `ID`='".$post->ID."'");
            }
        }
    }
}

// FES Categories Custom Walker
class FES_Custom_Walker extends Walker_Category
{
    /**
     * This class is a custom walker for front end event submission hierarchical categories customizing
     */
    private $post_id;

    function __construct($post_id)
    {
        $this->post_id = $post_id;
    }

    function start_lvl(&$output, $depth = 0, $args = array())
    {
        $indent  = str_repeat("\t", $depth);
        $output .= "$indent<div class='mec-fes-category-children'>";
    }

    function end_lvl(&$output, $depth = 0, $args = array())
    {
        $indent  = str_repeat("\t", $depth);
        $output .= "$indent</div>";
    }

    function start_el(&$output, $category, $depth = 0, $args = array(), $id = 0)
    {
        $post_categories = get_the_terms($this->post_id, 'mec_category');

        $categories = array();
        if($post_categories) foreach($post_categories as $post_category) $categories[] = $post_category->term_id;

        $output .= '<label for="mec_fes_categories' . $category->term_id . '">
        <input type="checkbox" name="mec[categories][' . $category->term_id . ']"
        id="mec_fes_categories' . $category->term_id .'" value="1"' . (in_array($category->term_id, $categories) ? 'checked="checked"' : '') . '/>' . $category->name;
    }

    function end_el(&$output, $page, $depth = 0, $args = array())
    {
        $output .= '</label>';
    }
}