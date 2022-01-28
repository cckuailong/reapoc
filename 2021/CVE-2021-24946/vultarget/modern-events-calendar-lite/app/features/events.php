<?php
/** no direct access **/
defined('MECEXEC') or die();

/**
 * Webnus MEC events class.
 *
 * @author Webnus <info@webnus.biz>
 */
class MEC_feature_events extends MEC_base
{
    public $factory;
    public $main;
    public $db;
    public $PT;
    public $settings;
    public $render;

    /**
     * Constructor method
     *
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

        // MEC Post Type Name
        $this->PT = $this->main->get_main_post_type();

        // MEC Settings
        $this->settings = $this->main->get_settings();
    }

    /**
     * Initialize events feature
     *
     * @author Webnus <info@webnus.biz>
     */
    public function init()
    {
        $this->factory->action('init', array($this, 'register_post_type'));
        $this->factory->action('mec_category_add_form_fields', array($this, 'add_category_fields'), 10, 2);
        $this->factory->action('mec_category_edit_form_fields', array($this, 'edit_category_fields'), 10, 2);
        $this->factory->action('edited_mec_category', array($this, 'save_metadata'));
        $this->factory->action('created_mec_category', array($this, 'save_metadata'));

        $this->factory->action('init', array($this, 'register_endpoints'));
        $this->factory->action('add_meta_boxes_' . $this->PT, array($this, 'remove_taxonomies_metaboxes'));
        $this->factory->action('save_post', array($this, 'save_event'), 10);
        $this->factory->action('edit_post', array($this, 'quick_edit'), 10);
        $this->factory->action('delete_post', array($this, 'delete_event'), 10);
        $this->factory->action('transition_post_status', array($this, 'event_published'), 10 , 3);

        $this->factory->filter('post_row_actions', array($this, 'action_links'), 10, 2);
        $this->factory->action('init', array($this, 'duplicate_event'));

        $this->factory->action('add_meta_boxes', array($this, 'register_meta_boxes'), 1);
        $this->factory->action('restrict_manage_posts', array($this, 'add_filters'));
        $this->factory->action('manage_posts_extra_tablenav', array($this, 'add_buttons'));
        $this->factory->action('pre_get_posts', array($this, 'filter'));

        $this->factory->action('mec_metabox_details', array($this, 'meta_box_nonce'), 10);
        $this->factory->action('mec_metabox_details', array($this, 'meta_box_dates'), 20);
        $this->factory->action('mec_metabox_details', array($this, 'meta_box_hourly_schedule'), 30);
        $this->factory->action('mec_metabox_details', array($this, 'meta_box_links'), 40);
        $this->factory->action('mec_metabox_details', array($this, 'meta_box_cost'), 50);
        $this->factory->action('mec_metabox_details', array($this, 'meta_box_fields'), 60);

        // Hourly Schedule for FES
        if(!isset($this->settings['fes_section_hourly_schedule']) or (isset($this->settings['fes_section_hourly_schedule']) and $this->settings['fes_section_hourly_schedule']))
        {
            $this->factory->action('mec_fes_metabox_details', array($this, 'meta_box_hourly_schedule'), 30);
        }

        // Data Fields for FES
        if(!isset($this->settings['fes_section_data_fields']) or (isset($this->settings['fes_section_data_fields']) and $this->settings['fes_section_data_fields']))
        {
            $this->factory->action('mec_fes_metabox_details', array($this, 'meta_box_fields'), 20);
        }

        // Show exceptional days if enabled
        if(isset($this->settings['exceptional_days']) and $this->settings['exceptional_days'])
        {
            $this->factory->action('mec_metabox_details', array($this, 'meta_box_exceptional_days'), 25);
            $this->factory->action('mec_fes_metabox_details', array($this, 'meta_box_exceptional_days'), 25);
        }

        // Show Booking meta box only if booking module is enabled
        $booking_status = (isset($this->settings['booking_status']) and $this->settings['booking_status']) ? true : false;
        if($booking_status)
        {
            $this->factory->action('mec_metabox_booking', array($this, 'meta_box_booking_options'), 5);
            $this->factory->action('mec_metabox_booking', array($this, 'meta_box_tickets'), 10);
            $this->factory->action('mec_metabox_booking', array($this, 'meta_box_regform'), 20);
            $this->factory->action('mec_metabox_booking', array($this, 'meta_box_attendees'), 22.5);
            $this->factory->action('wp_ajax_mec_event_bookings', array($this, 'mec_event_bookings'), 22.5);

            if(!isset($this->settings['fes_section_booking']) or (isset($this->settings['fes_section_booking']) and $this->settings['fes_section_booking']))
            {
                // Booking Options for FES
                if(!isset($this->settings['fes_section_booking']) or (isset($this->settings['fes_section_booking']) and $this->settings['fes_section_booking'])) $this->factory->action('mec_fes_metabox_details', array($this, 'meta_box_booking_options'), 35);

                // Ticket Options for FES
                if(!isset($this->settings['fes_section_tickets']) or (isset($this->settings['fes_section_tickets']) and $this->settings['fes_section_tickets'])) $this->factory->action('mec_fes_metabox_details', array($this, 'meta_box_tickets'), 40);

                // Registration Form for FES
                if(!isset($this->settings['fes_section_reg_form']) or (isset($this->settings['fes_section_reg_form']) and $this->settings['fes_section_reg_form'])) $this->factory->action('mec_fes_metabox_details', array($this, 'meta_box_regform'), 45);

                // Attendees for FES
                if(!isset($this->settings['fes_section_booking_att']) or (isset($this->settings['fes_section_booking_att']) and $this->settings['fes_section_booking_att'])) $this->factory->action('mec_fes_metabox_details', array($this, 'meta_box_attendees'), 47.5);
            }
        }

        // Show fees meta box only if fees module is enabled
        if(isset($this->settings['taxes_fees_status']) and $this->settings['taxes_fees_status'])
        {
            $this->factory->action('mec_metabox_booking', array($this, 'meta_box_fees'), 15);

            // Fees for FES
            if(!isset($this->settings['fes_section_booking']) or (isset($this->settings['fes_section_booking']) and $this->settings['fes_section_booking']))
            {
                if($booking_status and (!isset($this->settings['fes_section_fees']) or (isset($this->settings['fes_section_fees']) and $this->settings['fes_section_fees'])))
                {
                    $this->factory->action('mec_fes_metabox_details', array($this, 'meta_box_fees'), 45);
                }
            }
        }

        // Show ticket variations meta box only if the module is enabled
        if($booking_status and isset($this->settings['ticket_variations_status']) and $this->settings['ticket_variations_status'])
        {
            $this->factory->action('mec_metabox_booking', array($this, 'meta_box_ticket_variations'), 16);

            // Ticket Variations for FES
            if(!isset($this->settings['fes_section_booking']) or (isset($this->settings['fes_section_booking']) and $this->settings['fes_section_booking']))
            {
                if($booking_status and (!isset($this->settings['fes_section_ticket_variations']) or (isset($this->settings['fes_section_ticket_variations']) and $this->settings['fes_section_ticket_variations'])))
                {
                    $this->factory->action('mec_fes_metabox_details', array($this, 'meta_box_ticket_variations'), 46);
                }
            }
        }

        $this->factory->filter('manage_' . $this->PT . '_posts_columns', array($this, 'filter_columns'));
        $this->factory->filter('manage_edit-' . $this->PT . '_sortable_columns', array($this, 'filter_sortable_columns'));
        $this->factory->action('manage_' . $this->PT . '_posts_custom_column', array($this, 'filter_columns_content'), 10, 2);

        $this->factory->action('admin_footer-edit.php', array($this, 'add_bulk_actions'));
        $this->factory->action('load-edit.php', array($this, 'do_bulk_actions'));
        $this->factory->action('pre_post_update', array($this, 'bulk_edit'), 10);

        // Event Attendees
        $this->factory->action('wp_ajax_mec_attendees', array($this, 'attendees'));

        // Mass Email
        $this->factory->action('wp_ajax_mec_mass_email', array($this, 'mass_email'));

        // WPML Duplicate
        $this->factory->action('icl_make_duplicate', array($this, 'icl_duplicate'), 10, 4);
        $this->factory->action('icl_pro_translation_saved', array($this, 'wpml_pro_translation_saved'), 10, 3);

        // Image Fallback
        if(isset($this->settings['fallback_featured_image_status']) and $this->settings['fallback_featured_image_status'])
        {
            $this->factory->filter('get_post_metadata', array($this, 'set_fallback_image_id'), 10, 4);
            $this->factory->filter('post_thumbnail_html', array($this, 'show_fallback_image'), 20, 5);
        }
    }

    /**
     * Registers events post type and assign it to some taxonomies
     *
     * @author Webnus <info@webnus.biz>
     */
    public function register_post_type()
    {
        // Get supported features for event post type
        $supports = apply_filters('mec_event_supports', array('editor', 'title', 'excerpt', 'author', 'thumbnail', 'comments'));

        register_post_type(
            $this->PT,
            array(
                'labels' => array(
                    'name' => __('Events', 'modern-events-calendar-lite'),
                    'singular_name' => __('Event', 'modern-events-calendar-lite'),
                    'add_new' => __('Add Event', 'modern-events-calendar-lite'),
                    'add_new_item' => __('Add New Event', 'modern-events-calendar-lite'),
                    'not_found' => __('No events found!', 'modern-events-calendar-lite'),
                    'all_items' => __('All Events', 'modern-events-calendar-lite'),
                    'edit_item' => __('Edit Event', 'modern-events-calendar-lite'),
                    'view_item' => __('View Event', 'modern-events-calendar-lite'),
                    'not_found_in_trash' => __('No events found in Trash!', 'modern-events-calendar-lite'),
                ),
                'public' => true,
                'has_archive' => ($this->main->get_archive_status() ? true : false),
                'menu_icon' => plugin_dir_url(__FILE__ ) . '../../assets/img/mec.svg',
                'menu_position' => 26,
                'show_in_menu' => 'mec-intro',
                'rewrite' => array(
                    'slug' => $this->main->get_main_slug(),
                    'ep_mask' => EP_MEC_EVENTS,
                    'with_front' => false,
                ),
                'supports' => $supports,
                'show_in_rest' => true,

            )
        );

        $singular_label = $this->main->m('taxonomy_category', __('Category', 'modern-events-calendar-lite'));
        $plural_label = $this->main->m('taxonomy_categories', __('Categories', 'modern-events-calendar-lite'));

        register_taxonomy(
            'mec_category',
            $this->PT,
            array(
                'label' => $plural_label,
                'labels' => array(
                    'name' => $plural_label,
                    'singular_name' => $singular_label,
                    'all_items' => sprintf(__('All %s', 'modern-events-calendar-lite'), $plural_label),
                    'edit_item' => sprintf(__('Edit %s', 'modern-events-calendar-lite'), $singular_label),
                    'view_item' => sprintf(__('View %s', 'modern-events-calendar-lite'), $singular_label),
                    'update_item' => sprintf(__('Update %s', 'modern-events-calendar-lite'), $singular_label),
                    'add_new_item' => sprintf(__('Add New %s', 'modern-events-calendar-lite'), $singular_label),
                    'new_item_name' => sprintf(__('New %s Name', 'modern-events-calendar-lite'), $singular_label),
                    'popular_items' => sprintf(__('Popular %s', 'modern-events-calendar-lite'), $plural_label),
                    'search_items' => sprintf(__('Search %s', 'modern-events-calendar-lite'), $plural_label),
                ),
                'public' => true,
                'show_ui' => true,
                'show_in_rest' => true,
                'hierarchical' => true,
                'has_archive' => true,
                'rewrite' => array('slug' => $this->main->get_category_slug()),
            )
        );

        register_taxonomy_for_object_type('mec_category', $this->PT);
    }

    /**
     * Register meta field to taxonomies
     *
     * @author Webnus <info@webnus.biz>
     */
    public function add_category_fields()
    {
        add_thickbox();

        // Fallback Status
        $fallback = (isset($this->settings['fallback_featured_image_status']) and $this->settings['fallback_featured_image_status']);
        ?>
        <div class="form-field">
            <label for="mec_cat_icon"><?php _e('Category Icon', 'modern-events-calendar-lite'); ?></label>
            <input type="hidden" name="mec_cat_icon" id="mec_cat_icon" value=""/>
            <a href="<?php echo $this->main->asset('icon.html'); ?>?&width=680&height=450&inlineId=my-content-id"
               class="thickbox mec_category_icon button"><?php echo __('Select icon', 'modern-events-calendar-lite'); ?></a>
        </div>
        <div class="form-field">
            <label for="mec_cat_color"><?php _e('Color', 'modern-events-calendar-lite'); ?></label>
            <input type="text" name="mec_cat_color" id="mec_cat_color" class="mec-color-picker" />
            <p class="description"><?php _e('Optional category color', 'modern-events-calendar-lite'); ?></p>
        </div>
        <?php if($fallback): ?>
        <div class="form-field">
            <label for="mec_thumbnail_button"><?php _e('Fallback Image', 'modern-events-calendar-lite'); ?></label>
            <div id="mec_thumbnail_img"></div>
            <input type="hidden" name="fallback" id="mec_thumbnail" value="" />
            <button type="button" class="mec_upload_image_button button" id="mec_thumbnail_button"><?php echo __('Upload/Add image', 'modern-events-calendar-lite'); ?></button>
            <button type="button" class="mec_remove_image_button button mec-util-hidden"><?php echo __('Remove image', 'modern-events-calendar-lite'); ?></button>
        </div>
        <?php endif; ?>
        <?php
    }

    /**
     * Edit icon meta for categories
     *
     * @author Webnus <info@webnus.biz>
     */
    public function edit_category_fields($term)
    {
        add_thickbox();

        // Fallback Status
        $fallback = (isset($this->settings['fallback_featured_image_status']) and $this->settings['fallback_featured_image_status']);

        // Fallback Image
        $fallback_image = get_metadata('term', $term->term_id, 'mec_cat_fallback_image', true);

        // Icon
        $icon = get_metadata('term', $term->term_id, 'mec_cat_icon', true);

        // Color
        $color = get_metadata('term', $term->term_id, 'mec_cat_color', true);
        ?>
        <tr class="form-field">
            <th scope="row" >
                <label for="mec_cat_icon"><?php _e('Category Icon', 'modern-events-calendar-lite'); ?></label>
            </th>
            <td>
                <input type="hidden" name="mec_cat_icon" id="mec_cat_icon" value="<?php echo $icon; ?>"/>
                <a href="<?php echo $this->main->asset('icon.html'); ?>?&width=680&height=450&inlineId=my-content-id"
                   class="thickbox mec_category_icon button"><?php echo __('Select icon', 'modern-events-calendar-lite'); ?></a>
                <?php if (isset($icon)) : ?>
                    <div class="mec-webnus-icon"><i class="<?php echo $icon; ?> mec-color"></i></div>
                <?php endif; ?>
            </td>
        </tr>
        <tr class="form-field">
            <th scope="row" >
                <label for="mec_cat_color"><?php _e('Color', 'modern-events-calendar-lite'); ?></label>
            </th>
            <td>
                <input type="text" name="mec_cat_color" id="mec_cat_color" value="<?php echo $color; ?>" data-default-color="<?php echo $color; ?>" class="mec-color-picker" />
                <p class="description"><?php _e('Optional category color', 'modern-events-calendar-lite'); ?></p>
            </td>
        </tr>
        <?php if($fallback): ?>
        <tr class="form-field">
            <th scope="row" >
                <label for="mec_thumbnail_button"><?php _e('Fallback Image', 'modern-events-calendar-lite'); ?></label>
            </th>
            <td>
                <div id="mec_thumbnail_img"><?php if(trim($fallback_image) != '') echo '<img src="'.$fallback_image.'" />'; ?></div>
                <input type="hidden" name="fallback" id="mec_thumbnail" value="<?php echo esc_attr($fallback_image); ?>" />
                <button type="button" class="mec_upload_image_button button" id="mec_thumbnail_button"><?php echo __('Upload/Add image', 'modern-events-calendar-lite'); ?></button>
                <button type="button" class="mec_remove_image_button button <?php echo (!trim($fallback_image) ? 'mec-util-hidden' : ''); ?>"><?php echo __('Remove image', 'modern-events-calendar-lite'); ?></button>
            </td>
        </tr>
        <?php endif; ?>
        <?php
    }

    /**
     * Save meta data for mec categories
     *
     * @author Webnus <info@webnus.biz>
     * @param int $term_id
     */
    public function save_metadata($term_id)
    {
        // Quick Edit
        if(!isset($_POST['mec_cat_icon'])) return;

        $icon = isset($_POST['mec_cat_icon']) ? sanitize_text_field($_POST['mec_cat_icon']) : '';
        update_term_meta($term_id, 'mec_cat_icon', $icon);

        $color = isset($_POST['mec_cat_color']) ? sanitize_text_field($_POST['mec_cat_color']) : '';
        update_term_meta($term_id, 'mec_cat_color', $color);

        $fallback = isset($_POST['fallback']) ? sanitize_text_field($_POST['fallback']) : '';
        update_term_meta($term_id, 'mec_cat_fallback_image', $fallback);
    }

    public function register_endpoints()
    {
        add_rewrite_endpoint('verify', EP_MEC_EVENTS);
        add_rewrite_endpoint('cancel', EP_MEC_EVENTS);
        add_rewrite_endpoint('gateway-cancel', EP_MEC_EVENTS);
        add_rewrite_endpoint('gateway-return', EP_MEC_EVENTS);
    }

    /**
     * Remove normal meta boxes for some taxonomies
     *
     * @author Webnus <info@webnus.biz>
     */
    public function remove_taxonomies_metaboxes()
    {
        remove_meta_box('tagsdiv-mec_location', $this->PT, 'side');
        remove_meta_box('tagsdiv-mec_organizer', $this->PT, 'side');
        remove_meta_box('tagsdiv-mec_label', $this->PT, 'side');
    }

    /**
     * Registers 2 meta boxes for event data
     *
     * @author Webnus <info@webnus.biz>
     */
    public function register_meta_boxes()
    {
        add_meta_box('mec_metabox_details', __('Event Details', 'modern-events-calendar-lite'), array($this, 'meta_box_details'), $this->main->get_main_post_type(), 'normal', 'high');

        // Show Booking meta box onnly if booking module is enabled
        if($this->getPRO() and isset($this->settings['booking_status']) and $this->settings['booking_status'])
        {
            add_meta_box('mec_metabox_booking', __('Booking', 'modern-events-calendar-lite'), array($this, 'meta_box_booking'), $this->main->get_main_post_type(), 'normal', 'high');
        }
    }

    /**
     * Show content of details meta box
     *
     * @author Webnus <info@webnus.biz>
     * @param object $post
     */
    public function meta_box_details($post)
    {
        global $post;
        $note = get_post_meta($post->ID, 'mec_note', true);
        $note_visibility = $this->main->is_note_visible($post->post_status);

        $fes_guest_email = get_post_meta($post->ID, 'fes_guest_email', true);
        $fes_guest_name = get_post_meta($post->ID, 'fes_guest_name', true);

        $event_fields = $this->main->get_event_fields();
    ?>
        <div class="mec-add-event-tabs-wrap">
            <div class="mec-add-event-tabs-left">
                <?php
                $activated = '';
                $tabs = array(
                    __('FES Details', 'modern-events-calendar-lite') => 'mec_meta_box_fes_form',
                    __('Date And Time', 'modern-events-calendar-lite') => 'mec_meta_box_date_form',
                    __('Event Repeating', 'modern-events-calendar-lite') => 'mec_meta_box_repeat_form',
                    __('Event Data', 'modern-events-calendar-lite') => 'mec-event-data',
                    __('Exceptional Days', 'modern-events-calendar-lite') => 'mec-exceptional-days',
                    __('Hourly Schedule', 'modern-events-calendar-lite') => 'mec-hourly-schedule',
                    __('Location/Venue', 'modern-events-calendar-lite') => 'mec-location',
                    __('Links', 'modern-events-calendar-lite') => 'mec-read-more',
                    __('Organizer', 'modern-events-calendar-lite') => 'mec-organizer',
                    __('Cost', 'modern-events-calendar-lite') => 'mec-cost',
                    __('SEO Schema / Event Status', 'modern-events-calendar-lite') => 'mec-schema',
                    __('Notifications', 'modern-events-calendar-lite') => 'mec-notifications',
                );

                $single_event_meta_title = apply_filters('mec-single-event-meta-title', $tabs, $activated, $post);

                foreach($single_event_meta_title as $link_name => $link_address)
                {
                    if($link_address == 'mec_meta_box_fes_form')
                    {
                        if(($note_visibility and trim($note)) || (trim($fes_guest_email) and trim($fes_guest_name)))  echo '<a class="mec-add-event-tabs-link" data-href="'.$link_address.'" href="#">'.$link_name.'</a>';
                    }
                    elseif($link_address == 'mec-exceptional-days')
                    {
                        if(isset($this->settings['exceptional_days']) and $this->settings['exceptional_days']) echo '<a class="mec-add-event-tabs-link" data-href="'.$link_address.'" href="#">'.$link_name.'</a>';
                    }
                    elseif($link_address == 'mec-event-data')
                    {
                        if(count($event_fields) and isset($this->settings['display_event_fields_backend']) and $this->settings['display_event_fields_backend'] == 1) echo '<a class="mec-add-event-tabs-link" data-href="'.$link_address.'" href="#">'.$link_name.'</a>';
                    }
                    elseif($link_address == 'mec-notifications')
                    {
                        if(isset($this->settings['notif_per_event']) and $this->settings['notif_per_event']) echo '<a class="mec-add-event-tabs-link" data-href="'.$link_address.'" href="#">'.$link_name.'</a>';
                    }
                    else
                    {
                        echo '<a class="mec-add-event-tabs-link" data-href="'.$link_address.'" href="#">'.$link_name.'</a>';
                    }
                }
                ?>
            </div>
            <div class="mec-add-event-tabs-right">
                <?php do_action('mec_metabox_details', $post); ?>
            </div>
        </div>
        <script>
            jQuery(".mec-meta-box-fields .mec-event-tab-content:first-of-type,.mec-add-event-tabs-left .mec-add-event-tabs-link:first-of-type").addClass("mec-tab-active");
            jQuery(".mec-add-event-tabs-link").on("click", function (e) {
                e.preventDefault();
                var href = jQuery(this).attr("data-href");
                jQuery(".mec-event-tab-content,.mec-add-event-tabs-link").removeClass("mec-tab-active");
                jQuery(this).addClass("mec-tab-active");
                jQuery("#" + href ).addClass("mec-tab-active");
            });
        </script>

    <?php if(isset($this->settings['display_event_fields_backend']) and $this->settings['display_event_fields_backend'] == 1): ?>
        <script>
            jQuery("#publish").on("click", function()
            {
                var xdf = jQuery("#mec_metabox_details .mec-add-event-tabs-left .mec-add-event-tabs-link[data-href='mec-event-data']");
                jQuery("#mec_metabox_details .mec-add-event-tabs-left .mec-add-event-tabs-link").removeClass("mec-tab-active");
                jQuery("#mec_metabox_details .mec-add-event-tabs-right .mec-event-tab-content").removeClass("mec-tab-active");
                jQuery(xdf).addClass("mec-tab-active");
                jQuery(".mec-add-event-tabs-right #mec-event-data").addClass("mec-tab-active");
            });
        </script>
    <?php
    endif;
    }

    /**
     * Add a security nonce to the Add/Edit events page
     *
     * @author Webnus <info@webnus.biz>
     */
    public function meta_box_nonce()
    {
        // Add a nonce field so we can check for it later.
        wp_nonce_field('mec_event_data', 'mec_event_nonce');
    }

    /**
     * Show date options of event into the Add/Edit event page
     *
     * @author Webnus <info@webnus.biz>
     * @param object $post
     */
    public function meta_box_dates($post)
    {
        global $post;

        $allday = get_post_meta($post->ID, 'mec_allday', true);
        $one_occurrence = get_post_meta($post->ID, 'one_occurrence', true);
        $comment = get_post_meta($post->ID, 'mec_comment', true);
        $hide_time = get_post_meta($post->ID, 'mec_hide_time', true);
        $hide_end_time = get_post_meta($post->ID, 'mec_hide_end_time', true);
        $start_date = get_post_meta($post->ID, 'mec_start_date', true);

        // This date format used for datepicker
        $datepicker_format = (isset($this->settings['datepicker_format']) and trim($this->settings['datepicker_format'])) ? $this->settings['datepicker_format'] : 'Y-m-d';

        // Advanced Repeating Day
        $advanced_days = get_post_meta($post->ID, 'mec_advanced_days', true);
        $advanced_days = is_array($advanced_days) ? $advanced_days : array();
        $advanced_str = count($advanced_days) ? implode('-', $advanced_days) : '';

        $start_time_hour = get_post_meta($post->ID, 'mec_start_time_hour', true);
        if(trim($start_time_hour) == '') $start_time_hour = 8;

        $start_time_minutes = get_post_meta($post->ID, 'mec_start_time_minutes', true);
        if(trim($start_time_minutes) == '') $start_time_minutes = 0;

        $start_time_ampm = get_post_meta($post->ID, 'mec_start_time_ampm', true);
        if(trim($start_time_ampm) == '') $start_time_ampm = 'AM';

        $end_date = get_post_meta($post->ID, 'mec_end_date', true);

        $end_time_hour = get_post_meta($post->ID, 'mec_end_time_hour', true);
        if(trim($end_time_hour) == '') $end_time_hour = 6;

        $end_time_minutes = get_post_meta($post->ID, 'mec_end_time_minutes', true);
        if(trim($end_time_minutes) == '') $end_time_minutes = 0;

        $end_time_ampm = get_post_meta($post->ID, 'mec_end_time_ampm', true);
        if(trim($end_time_ampm) == '') $end_time_ampm = 'PM';

        $repeat_status = get_post_meta($post->ID, 'mec_repeat_status', true);
        $repeat_type = get_post_meta($post->ID, 'mec_repeat_type', true);
        if(trim($repeat_type) == '') $repeat_type = 'daily';

        $repeat_interval = get_post_meta($post->ID, 'mec_repeat_interval', true);
        if(trim($repeat_interval) == '' and in_array($repeat_type, array('daily', 'weekly'))) $repeat_interval = 1;

        $certain_weekdays = get_post_meta($post->ID, 'mec_certain_weekdays', true);
        if($repeat_type != 'certain_weekdays') $certain_weekdays = array();

        $in_days_str = get_post_meta($post->ID, 'mec_in_days', true);
        $in_days = trim($in_days_str) ? explode(',', $in_days_str) : array();

        $mec_repeat_end = get_post_meta($post->ID, 'mec_repeat_end', true);
        if(trim($mec_repeat_end) == '') $mec_repeat_end = 'never';

        $repeat_end_at_occurrences = get_post_meta($post->ID, 'mec_repeat_end_at_occurrences', true);
        if(trim($repeat_end_at_occurrences) == '') $repeat_end_at_occurrences = 9;

        $repeat_end_at_date = get_post_meta($post->ID, 'mec_repeat_end_at_date', true);

        $note = get_post_meta($post->ID, 'mec_note', true);
        $note_visibility = $this->main->is_note_visible($post->post_status);

        $fes_guest_email = get_post_meta($post->ID, 'fes_guest_email', true);
        $fes_guest_name = get_post_meta($post->ID, 'fes_guest_name', true);
        $imported_from_google = get_post_meta($post->ID, 'mec_imported_from_google', true);

        $event_timezone = get_post_meta($post->ID, 'mec_timezone', true);
        if(trim($event_timezone) == '') $event_timezone = 'global';

        $countdown_method = get_post_meta($post->ID, 'mec_countdown_method', true);
        if(trim($countdown_method) == '') $countdown_method = 'global';

        // Public Event
        $public = get_post_meta($post->ID, 'mec_public', true);
        if(trim($public) === '') $public = 1;
        ?>
        <div class="mec-meta-box-fields" id="mec-date-time">
            <?php if(($note_visibility and trim($note)) || (trim($fes_guest_email) and trim($fes_guest_name))): ?>
                <div id="mec_meta_box_fes_form" class="mec-event-tab-content">
            <?php endif; ?>
            <?php if($note_visibility and trim($note)): ?>
                <div class="mec-event-note">
                    <h4><?php _e('Note for reviewer', 'modern-events-calendar-lite'); ?></h4>
                    <p><?php echo $note; ?></p>
                </div>
            <?php endif; ?>
            <?php if(trim($fes_guest_email) and trim($fes_guest_name)): ?>
                <div class="mec-guest-data">
                    <h4><?php _e('Guest Data', 'modern-events-calendar-lite'); ?></h4>
                    <p><strong><?php _e('Name', 'modern-events-calendar-lite'); ?>:</strong> <?php echo $fes_guest_name; ?></p>
                    <p><strong><?php _e('Email', 'modern-events-calendar-lite'); ?>:</strong> <?php echo $fes_guest_email; ?></p>
                </div>
            <?php endif; ?>
            <?php if(($note_visibility and trim($note)) || (trim($fes_guest_email) and trim($fes_guest_name))): ?>
                </div>
            <?php endif; ?>
            <?php do_action('start_mec_custom_fields', $post); ?>

            <?php if($imported_from_google): ?>
            <p class="info-msg"><?php esc_html_e("This event is imported from Google calendar so if you modify it, it would overwrite in the next import from Google.", 'modern-events-calendar-lite'); ?></p>
            <?php endif; ?>

            <div id="mec_meta_box_date_form" class="mec-event-tab-content">
                <h4><?php _e('Date and Time', 'modern-events-calendar-lite'); ?></h4>
                <div class="mec-title">
                    <span class="mec-dashicons dashicons dashicons-calendar-alt"></span>
                    <label for="mec_start_date"><?php _e('Start Date', 'modern-events-calendar-lite'); ?></label>
                </div>
                <div class="mec-form-row">
                    <div class="mec-col-4">
                        <input type="text" name="mec[date][start][date]" id="mec_start_date" value="<?php echo esc_attr($this->main->standardize_format($start_date, $datepicker_format)); ?>" placeholder="<?php _e('Start Date', 'modern-events-calendar-lite'); ?>" autocomplete="off"/>
                    </div>
                    <div class="mec-col-6 mec-time-picker <?php echo ($allday == 1) ? 'mec-util-hidden' : ''; ?>">
                        <?php $this->main->timepicker(array(
                            'method' => (isset($this->settings['time_format']) ? $this->settings['time_format'] : 12),
                            'time_hour' => $start_time_hour,
                            'time_minutes' => $start_time_minutes,
                            'time_ampm' => $start_time_ampm,
                            'name' => 'mec[date][start]',
                            'id_key' => 'start_',
                        )); ?>
                    </div>
                </div>
                <div class="mec-title">
                    <span class="mec-dashicons dashicons dashicons-calendar-alt"></span>
                    <label for="mec_end_date"><?php _e('End Date', 'modern-events-calendar-lite'); ?></label>
                </div>
                <div class="mec-form-row">
                    <div class="mec-col-4">
                        <input type="text" name="mec[date][end][date]" id="mec_end_date" value="<?php echo esc_attr($this->main->standardize_format($end_date, $datepicker_format)); ?>" placeholder="<?php _e('End Date', 'modern-events-calendar-lite'); ?>" autocomplete="off"/>
                    </div>
                    <div class="mec-col-6 mec-time-picker <?php echo ($allday == 1) ? 'mec-util-hidden' : ''; ?>">
                        <?php $this->main->timepicker(array(
                            'method' => (isset($this->settings['time_format']) ? $this->settings['time_format'] : 12),
                            'time_hour' => $end_time_hour,
                            'time_minutes' => $end_time_minutes,
                            'time_ampm' => $end_time_ampm,
                            'name' => 'mec[date][end]',
                            'id_key' => 'end_',
                        )); ?>
                    </div>
                </div>
                <?php do_action('add_event_after_time_and_date', $post->ID); ?>
                <div class="mec-form-row mec-all-day-event">
                    <input
                        <?php
                        if ($allday == '1') {
                            echo 'checked="checked"';
                        }
                        ?>
                            type="checkbox" name="mec[date][allday]" id="mec_allday" value="1"
                            onchange="jQuery('.mec-time-picker, .mec-time-picker-label').toggle(); jQuery('#mec_add_in_days').data('allday', (jQuery(this).is(':checked') ? 1 : 0));"/><label
                            for="mec_allday"><?php _e('All-day Event', 'modern-events-calendar-lite'); ?></label>
                </div>
                <div class="mec-form-row">
                    <input
                        <?php
                        if ($hide_time == '1') {
                            echo 'checked="checked"';
                        }
                        ?>
                            type="checkbox" name="mec[date][hide_time]" id="mec_hide_time" value="1"/><label
                            for="mec_hide_time"><?php _e('Hide Event Time', 'modern-events-calendar-lite'); ?></label>
                </div>
                <div class="mec-form-row">
                    <input
                        <?php
                        if ($hide_end_time == '1') {
                            echo 'checked="checked"';
                        }
                        ?>
                            type="checkbox" name="mec[date][hide_end_time]" id="mec_hide_end_time" value="1"/><label
                            for="mec_hide_end_time"><?php _e('Hide Event End Time', 'modern-events-calendar-lite'); ?></label>
                </div>
                <div class="mec-form-row">
                    <div class="mec-col-4">
                        <input type="text" class="" name="mec[date][comment]" id="mec_comment"
                               placeholder="<?php _e('Notes on the time', 'modern-events-calendar-lite'); ?>"
                               value="<?php echo esc_attr($comment); ?>"/>
                        <span class="mec-tooltip">
							<div class="box top">
								<h5 class="title"><?php _e('Notes on the time', 'modern-events-calendar-lite'); ?></h5>
								<div class="content"><p><?php esc_attr_e('It shows next to event time on the Single Event Page. You can enter notes such as timezone in this field.', 'modern-events-calendar-lite'); ?>
                                        <a href="https://webnus.net/dox/modern-events-calendar/add-event/"
                                           target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>
							</div>
							<i title="" class="dashicons-before dashicons-editor-help"></i>
						</span>
                    </div>
                </div>

                <?php if(isset($this->settings['tz_per_event']) and $this->settings['tz_per_event']): ?>
                <div class="mec-form-row mec-timezone-event">
                    <div class="mec-title">
                        <label for="mec_event_timezone"><?php esc_html_e('Timezone', 'modern-events-calendar-lite'); ?></label>
                    </div>
                    <div class="mec-form-row">
                        <div class="mec-col-4">
                            <select name="mec[timezone]" id="mec_event_timezone">
                                <option value="global"><?php esc_html_e('Inherit from global options'); ?></option>
                                <?php echo $this->main->timezones($event_timezone); ?>
                            </select>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <?php if(isset($this->settings['countdown_status']) and $this->settings['countdown_status']): ?>
                <h4><?php _e('Countdown Method', 'modern-events-calendar-lite'); ?></h4>
                <div class="mec-form-row">
                    <div class="mec-col-4">
                        <select name="mec[countdown_method]" id="mec_countdown_method" title="<?php esc_attr_e('Countdown Method', 'modern-events-calendar-lite'); ?>">
                            <option value="global" <?php if('global' == $countdown_method) echo 'selected="selected"'; ?>><?php _e('Inherit from global options', 'modern-events-calendar-lite'); ?></option>
                            <option value="start" <?php if('start' == $countdown_method) echo 'selected="selected"'; ?>><?php _e('Count to Event Start', 'modern-events-calendar-lite'); ?></option>
                            <option value="end" <?php if('end' == $countdown_method) echo 'selected="selected"'; ?>><?php _e('Count to Event End', 'modern-events-calendar-lite'); ?></option>
                        </select>
                    </div>
                </div>
                <?php endif; ?>

                <h4><?php _e('Visibility', 'modern-events-calendar-lite'); ?></h4>
                <div class="mec-form-row">
                    <div class="mec-col-4">
                        <select name="mec[public]" id="mec_public" title="<?php esc_attr_e('Event Visibility', 'modern-events-calendar-lite'); ?>">
                            <option value="1" <?php if('1' == $public) echo 'selected="selected"'; ?>><?php _e('Show on Shortcodes', 'modern-events-calendar-lite'); ?></option>
                            <option value="0" <?php if('0' == $public) echo 'selected="selected"'; ?>><?php _e('Hide on Shortcodes', 'modern-events-calendar-lite'); ?></option>
                        </select>
                    </div>
                </div>

            </div>
            <div id="mec_meta_box_repeat_form" class="mec-event-tab-content">
                <h4><?php _e('Repeating', 'modern-events-calendar-lite'); ?></h4>
                <div class="mec-form-row">
                    <input
                        <?php
                        if ($repeat_status == '1') {
                            echo 'checked="checked"';
                        }
                        ?>
                            type="checkbox" name="mec[date][repeat][status]" id="mec_repeat" value="1"/><label
                            for="mec_repeat"><?php _e('Event Repeating (Recurring events)', 'modern-events-calendar-lite'); ?></label>
                </div>
                <div class="mec-form-repeating-event-row">
                    <div class="mec-form-row">
                        <label class="mec-col-3" for="mec_repeat_type"><?php _e('Repeats', 'modern-events-calendar-lite'); ?></label>
                        <select class="mec-col-2" name="mec[date][repeat][type]" id="mec_repeat_type">
                            <option
                                <?php
                                if ($repeat_type == 'daily') {
                                    echo 'selected="selected"';
                                }
                                ?>
                                    value="daily"><?php _e('Daily', 'modern-events-calendar-lite'); ?></option>
                            <option
                                <?php
                                if ($repeat_type == 'weekday') {
                                    echo 'selected="selected"';
                                }
                                ?>
                                    value="weekday"><?php _e('Every Weekday', 'modern-events-calendar-lite'); ?></option>
                            <option
                                <?php
                                if ($repeat_type == 'weekend') {
                                    echo 'selected="selected"';
                                }
                                ?>
                                    value="weekend"><?php _e('Every Weekend', 'modern-events-calendar-lite'); ?></option>
                            <option
                                <?php
                                if ($repeat_type == 'certain_weekdays') {
                                    echo 'selected="selected"';
                                }
                                ?>
                                    value="certain_weekdays"><?php _e('Certain Weekdays', 'modern-events-calendar-lite'); ?></option>
                            <option
                                <?php
                                if ($repeat_type == 'weekly') {
                                    echo 'selected="selected"';
                                }
                                ?>
                                    value="weekly"><?php _e('Weekly', 'modern-events-calendar-lite'); ?></option>
                            <option
                                <?php
                                if ($repeat_type == 'monthly') {
                                    echo 'selected="selected"';
                                }
                                ?>
                                    value="monthly"><?php _e('Monthly', 'modern-events-calendar-lite'); ?></option>
                            <option
                                <?php
                                if ($repeat_type == 'yearly') {
                                    echo 'selected="selected"';
                                }
                                ?>
                                    value="yearly"><?php _e('Yearly', 'modern-events-calendar-lite'); ?></option>
                            <option
                                <?php
                                if ($repeat_type == 'custom_days') {
                                    echo 'selected="selected"';
                                }
                                ?>
                                    value="custom_days"><?php _e('Custom Days', 'modern-events-calendar-lite'); ?></option>
                            <option
                                <?php
                                if ($repeat_type == 'advanced') {
                                    echo 'selected="selected"';
                                }
                                ?>
                                    value="advanced"><?php _e('Advanced', 'modern-events-calendar-lite'); ?></option>
                        </select>
                    </div>
                    <div class="mec-form-row" id="mec_repeat_interval_container">
                        <label class="mec-col-3"
                               for="mec_repeat_interval"><?php _e('Repeat Interval', 'modern-events-calendar-lite'); ?></label>
                        <input class="mec-col-2" type="text" name="mec[date][repeat][interval]" id="mec_repeat_interval"
                               placeholder="<?php _e('Repeat interval', 'modern-events-calendar-lite'); ?>"
                               value="<?php echo($repeat_type == 'weekly' ? ($repeat_interval / 7) : $repeat_interval); ?>"/>
                    </div>
                    <div class="mec-form-row" id="mec_repeat_certain_weekdays_container">
                        <label class="mec-col-3"><?php _e('Week Days', 'modern-events-calendar-lite'); ?></label>
                        <?php
                            $weekdays = $this->main->get_weekday_i18n_labels();
                            foreach($weekdays as $weekday):
                        ?>
                        <label>
                            <input type="checkbox" name="mec[date][repeat][certain_weekdays][]"
                                value="<?php echo intval($weekday[0]); ?>" <?php echo(in_array($weekday[0], $certain_weekdays) ? 'checked="checked"' : ''); ?> /><?php echo $weekday[1]; ?>
                        </label>
                        <?php endforeach; ?>
                    </div>
                    <div class="mec-form-row" id="mec_exceptions_in_days_container">
                        <div class="mec-form-row">
                            <div class="mec-col-12">
                                <?php if(!$this->getPRO()): ?>
                                <div class="mec-form-row">
                                    <div class="mec-col-12">
                                        <p class="description"><?php esc_html_e("To add multiple occurrences per day you need Pro version of Modern Events Calendar.", 'modern-events-calendar-lite'); ?></p>
                                    </div>
                                </div>
                                <?php endif; ?>
                                <div class="mec-form-row">
                                    <div class="mec-col-4">
                                        <input type="text" id="mec_exceptions_in_days_start_date" value="" placeholder="<?php _e('Start', 'modern-events-calendar-lite'); ?>" title="<?php _e('Start', 'modern-events-calendar-lite'); ?>" class="mec_date_picker_dynamic_format widefat" autocomplete="off"/>
                                    </div>
                                    <div class="mec-col-3 mec-time-picker <?php echo ($allday == 1) ? 'mec-util-hidden' : ''; ?>">
                                        <?php $this->main->timepicker(array(
                                            'method' => (isset($this->settings['time_format']) ? $this->settings['time_format'] : 12),
                                            'time_hour' => $start_time_hour,
                                            'time_minutes' => $start_time_minutes,
                                            'time_ampm' => $start_time_ampm,
                                            'name' => 'mec[exceptionsdays][start]',
                                            'id_key' => 'exceptions_in_days_start_',
                                        )); ?>
                                    </div>
                                    <div class="mec-col-5">
                                        <button class="button" type="button" id="mec_add_in_days" data-allday="<?php echo $allday; ?>"><?php _e('Add', 'modern-events-calendar-lite'); ?></button>
                                        <span class="mec-tooltip">
                                            <div class="box top">
                                                <h5 class="title"><?php _e('Custom Days Repeating', 'modern-events-calendar-lite'); ?></h5>
                                                <div class="content">
                                                    <p>
                                                        <?php esc_attr_e('Add certain days to event occurrence dates. If you have a single day event, start and end dates should be the same, If you have a multiple day event, the start and end dates must be commensurate with the initial date.', 'modern-events-calendar-lite'); ?>
                                                        <a href="https://webnus.net/dox/modern-events-calendar/date-and-time/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a>
                                                    </p>
                                                </div>
                                            </div>
                                            <i title="" class="dashicons-before dashicons-editor-help"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="mec-form-row">
                                    <div class="mec-col-4">
                                        <input type="text" id="mec_exceptions_in_days_end_date" value="" placeholder="<?php _e('End', 'modern-events-calendar-lite'); ?>" title="<?php _e('End', 'modern-events-calendar-lite'); ?>" class="mec_date_picker_dynamic_format" autocomplete="off"/>
                                    </div>
                                    <div class="mec-col-8 mec-time-picker <?php echo ($allday == 1) ? 'mec-util-hidden' : ''; ?>">
                                        <?php $this->main->timepicker(array(
                                            'method' => (isset($this->settings['time_format']) ? $this->settings['time_format'] : 12),
                                            'time_hour' => $end_time_hour,
                                            'time_minutes' => $end_time_minutes,
                                            'time_ampm' => $end_time_ampm,
                                            'name' => 'mec[exceptionsdays][end]',
                                            'id_key' => 'exceptions_in_days_end_',
                                        )); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mec-form-row mec-certain-day" id="mec_in_days">
                            <?php $i = 1; foreach($in_days as $in_day): ?>
                                <?php
                                    $in_day = explode(':', $in_day);
                                    $first_date = $this->main->standardize_format($in_day[0], $datepicker_format);
                                    $second_date = $this->main->standardize_format($in_day[1], $datepicker_format);

                                    $in_day_start_time = '';
                                    $in_day_start_time_label = '';
                                    $in_day_end_time = '';
                                    $in_day_end_time_label = '';

                                    if(isset($in_day[2]) and isset($in_day[3]))
                                    {
                                        $in_day_start_time = $in_day[2];
                                        $in_day_end_time = $in_day[3];

                                        // If 24 hours format is enabled then convert it back to 12 hours
                                        if(isset($this->settings['time_format']) and $this->settings['time_format'] == 24)
                                        {
                                            $in_day_ex_start = explode('-', $in_day_start_time);
                                            $in_day_ex_end = explode('-', $in_day_end_time);

                                            $in_day_start_time_label = $this->main->to_24hours($in_day_ex_start[0], $in_day_ex_start[2]).':'.$in_day_ex_start[1];
                                            $in_day_end_time_label = $this->main->to_24hours($in_day_ex_end[0], $in_day_ex_end[2]).':'.$in_day_ex_end[1];
                                        }
                                        else
                                        {
                                            $pos = strpos($in_day_start_time, '-');
                                            if($pos !== false) $in_day_start_time_label = substr_replace($in_day_start_time, ':', $pos, 1);

                                            $pos = strpos($in_day_end_time, '-');
                                            if($pos !== false) $in_day_end_time_label = substr_replace($in_day_end_time, ':', $pos, 1);

                                            $in_day_start_time_label = str_replace('-', ' ', $in_day_start_time_label);
                                            $in_day_end_time_label = str_replace('-', ' ', $in_day_end_time_label);
                                        }
                                    }

                                    $in_day = $first_date . ':' . $second_date.(trim($in_day_start_time) ? ':'.$in_day_start_time : '').(trim($in_day_end_time) ? ':'.$in_day_end_time : '');
                                    $in_day_label = $first_date. (trim($in_day_start_time_label) ? ' <span class="mec-time-picker-label '.($allday ? 'mec-util-hidden' : '').'">'.$in_day_start_time_label.'</span>' : '') . ' - ' . $second_date. (trim($in_day_end_time_label) ? ' <span class="mec-time-picker-label '.($allday ? 'mec-util-hidden' : '').'">'.$in_day_end_time_label.'</span>' : '');
                                ?>
                                <div class="mec-form-row" id="mec_in_days_row<?php echo $i; ?>">
                                    <input type="hidden" name="mec[in_days][<?php echo $i; ?>]" value="<?php echo $in_day; ?>"/>
                                    <span class="mec-in-days-day"><?php echo $in_day_label; ?></span>
                                    <span class="mec-not-in-days-remove" onclick="mec_in_days_remove(<?php echo $i; ?>);">x</span>
                                </div>
                            <?php $i++; endforeach; ?>
                        </div>
                        <input type="hidden" id="mec_new_in_days_key" value="<?php echo $i + 1; ?>"/>
                        <div class="mec-util-hidden" id="mec_new_in_days_raw">
                            <div class="mec-form-row" id="mec_in_days_row:i:">
                                <input type="hidden" name="mec[in_days][:i:]" value=":val:"/>
                                <span class="mec-in-days-day">:label:</span>
                                <span class="mec-not-in-days-remove" onclick="mec_in_days_remove(:i:);">x</span>
                            </div>
                        </div>
                    </div>
                    <div id="mec-advanced-wraper">
                        <div class="mec-form-row">
                            <ul>
								<li>
									<?php _e('First', 'modern-events-calendar-lite'); ?>
								</li>
								<ul>
									<?php $day_1th = $this->main->advanced_repeating_sort_day($this->main->get_first_day_of_week(), 1); ?>
									<li class="<?php $this->main->mec_active($advanced_days, "{$day_1th}.1"); ?>">
										<?php _e($day_1th, 'modern-events-calendar-lite'); ?>
										<span class="key"><?php echo $day_1th ?>.1-</span>
									</li>
									<?php $day_2th = $this->main->advanced_repeating_sort_day($this->main->get_first_day_of_week(), 2); ?>
									<li class="<?php $this->main->mec_active($advanced_days, "{$day_2th}.1"); ?>">
										<?php _e($day_2th, 'modern-events-calendar-lite'); ?>
										<span class="key"><?php echo $day_2th ?>.1-</span>
									</li>
									<?php $day_3th = $this->main->advanced_repeating_sort_day($this->main->get_first_day_of_week(), 3); ?>
									<li class="<?php $this->main->mec_active($advanced_days, "{$day_3th}.1"); ?>">
										<?php _e($day_3th, 'modern-events-calendar-lite'); ?>
										<span class="key"><?php echo $day_3th ?>.1-</span>
									</li>
									<?php $day_4th = $this->main->advanced_repeating_sort_day($this->main->get_first_day_of_week(), 4); ?>
									<li class="<?php $this->main->mec_active($advanced_days, "{$day_4th}.1"); ?>">
										<?php _e($day_4th, 'modern-events-calendar-lite'); ?>
										<span class="key"><?php echo $day_4th ?>.1-</span>
									</li>
									<?php $day_5th = $this->main->advanced_repeating_sort_day($this->main->get_first_day_of_week(), 5); ?>
									<li class="<?php $this->main->mec_active($advanced_days, "{$day_5th}.1"); ?>">
										<?php _e($day_5th, 'modern-events-calendar-lite'); ?>
										<span class="key"><?php echo $day_5th ?>.1-</span>
									</li>
									<?php $day_6th = $this->main->advanced_repeating_sort_day($this->main->get_first_day_of_week(), 6); ?>
									<li class="<?php $this->main->mec_active($advanced_days, "{$day_6th}.1"); ?>">
										<?php _e($day_6th, 'modern-events-calendar-lite'); ?>
										<span class="key"><?php echo $day_6th ?>.1-</span>
									</li>
									<?php $day_7th = $this->main->advanced_repeating_sort_day($this->main->get_first_day_of_week(), 7); ?>
									<li class="<?php $this->main->mec_active($advanced_days, "{$day_7th}.1"); ?>">
										<?php _e($day_7th, 'modern-events-calendar-lite'); ?>
										<span class="key"><?php echo $day_7th ?>.1-</span>
									</li>
								</ul>
							</ul>
                            <ul>
								<li>
									<?php _e('Second', 'modern-events-calendar-lite'); ?>
								</li>
								<ul>
									<?php $day_1th = $this->main->advanced_repeating_sort_day($this->main->get_first_day_of_week(), 1); ?>
									<li class="<?php $this->main->mec_active($advanced_days, "{$day_1th}.2"); ?>">
										<?php _e($day_1th, 'modern-events-calendar-lite'); ?>
										<span class="key"><?php echo $day_1th ?>.2-</span>
									</li>
									<?php $day_2th = $this->main->advanced_repeating_sort_day($this->main->get_first_day_of_week(), 2); ?>
									<li class="<?php $this->main->mec_active($advanced_days, "{$day_2th}.2"); ?>">
										<?php _e($day_2th, 'modern-events-calendar-lite'); ?>
										<span class="key"><?php echo $day_2th ?>.2-</span>
									</li>
									<?php $day_3th = $this->main->advanced_repeating_sort_day($this->main->get_first_day_of_week(), 3); ?>
									<li class="<?php $this->main->mec_active($advanced_days, "{$day_3th}.2"); ?>">
										<?php _e($day_3th, 'modern-events-calendar-lite'); ?>
										<span class="key"><?php echo $day_3th ?>.2-</span>
									</li>
									<?php $day_4th = $this->main->advanced_repeating_sort_day($this->main->get_first_day_of_week(), 4); ?>
									<li class="<?php $this->main->mec_active($advanced_days, "{$day_4th}.2"); ?>">
										<?php _e($day_4th, 'modern-events-calendar-lite'); ?>
										<span class="key"><?php echo $day_4th ?>.2-</span>
									</li>
									<?php $day_5th = $this->main->advanced_repeating_sort_day($this->main->get_first_day_of_week(), 5); ?>
									<li class="<?php $this->main->mec_active($advanced_days, "{$day_5th}.2"); ?>">
										<?php _e($day_5th, 'modern-events-calendar-lite'); ?>
										<span class="key"><?php echo $day_5th ?>.2-</span>
									</li>
									<?php $day_6th = $this->main->advanced_repeating_sort_day($this->main->get_first_day_of_week(), 6); ?>
									<li class="<?php $this->main->mec_active($advanced_days, "{$day_6th}.2"); ?>">
										<?php _e($day_6th, 'modern-events-calendar-lite'); ?>
										<span class="key"><?php echo $day_6th ?>.2-</span>
									</li>
									<?php $day_7th = $this->main->advanced_repeating_sort_day($this->main->get_first_day_of_week(), 7); ?>
									<li class="<?php $this->main->mec_active($advanced_days, "{$day_7th}.2"); ?>">
										<?php _e($day_7th, 'modern-events-calendar-lite'); ?>
										<span class="key"><?php echo $day_7th ?>.2-</span>
									</li>
								</ul>
							</ul>
                            <ul>
								<li>
									<?php _e('Third', 'modern-events-calendar-lite'); ?>
								</li>
								<ul>
									<?php $day_1th = $this->main->advanced_repeating_sort_day($this->main->get_first_day_of_week(), 1); ?>
									<li class="<?php $this->main->mec_active($advanced_days, "{$day_1th}.3"); ?>">
										<?php _e($day_1th, 'modern-events-calendar-lite'); ?>
										<span class="key"><?php echo $day_1th ?>.3-</span>
									</li>
									<?php $day_2th = $this->main->advanced_repeating_sort_day($this->main->get_first_day_of_week(), 2); ?>
									<li class="<?php $this->main->mec_active($advanced_days, "{$day_2th}.3"); ?>">
										<?php _e($day_2th, 'modern-events-calendar-lite'); ?>
										<span class="key"><?php echo $day_2th ?>.3-</span>
									</li>
									<?php $day_3th = $this->main->advanced_repeating_sort_day($this->main->get_first_day_of_week(), 3); ?>
									<li class="<?php $this->main->mec_active($advanced_days, "{$day_3th}.3"); ?>">
										<?php _e($day_3th, 'modern-events-calendar-lite'); ?>
										<span class="key"><?php echo $day_3th ?>.3-</span>
									</li>
									<?php $day_4th = $this->main->advanced_repeating_sort_day($this->main->get_first_day_of_week(), 4); ?>
									<li class="<?php $this->main->mec_active($advanced_days, "{$day_4th}.3"); ?>">
										<?php _e($day_4th, 'modern-events-calendar-lite'); ?>
										<span class="key"><?php echo $day_4th ?>.3-</span>
									</li>
									<?php $day_5th = $this->main->advanced_repeating_sort_day($this->main->get_first_day_of_week(), 5); ?>
									<li class="<?php $this->main->mec_active($advanced_days, "{$day_5th}.3"); ?>">
										<?php _e($day_5th, 'modern-events-calendar-lite'); ?>
										<span class="key"><?php echo $day_5th ?>.3-</span>
									</li>
									<?php $day_6th = $this->main->advanced_repeating_sort_day($this->main->get_first_day_of_week(), 6); ?>
									<li class="<?php $this->main->mec_active($advanced_days, "{$day_6th}.3"); ?>">
										<?php _e($day_6th, 'modern-events-calendar-lite'); ?>
										<span class="key"><?php echo $day_6th ?>.3-</span>
									</li>
									<?php $day_7th = $this->main->advanced_repeating_sort_day($this->main->get_first_day_of_week(), 7); ?>
									<li class="<?php $this->main->mec_active($advanced_days, "{$day_7th}.3"); ?>">
										<?php _e($day_7th, 'modern-events-calendar-lite'); ?>
										<span class="key"><?php echo $day_7th ?>.3-</span>
									</li>
								</ul>
							</ul>
                            <ul>
								<li>
									<?php _e('Fourth', 'modern-events-calendar-lite'); ?>
								</li>
								<ul>
									<?php $day_1th = $this->main->advanced_repeating_sort_day($this->main->get_first_day_of_week(), 1); ?>
									<li class="<?php $this->main->mec_active($advanced_days, "{$day_1th}.4"); ?>">
										<?php _e($day_1th, 'modern-events-calendar-lite'); ?>
										<span class="key"><?php echo $day_1th ?>.4-</span>
									</li>
									<?php $day_2th = $this->main->advanced_repeating_sort_day($this->main->get_first_day_of_week(), 2); ?>
									<li class="<?php $this->main->mec_active($advanced_days, "{$day_2th}.4"); ?>">
										<?php _e($day_2th, 'modern-events-calendar-lite'); ?>
										<span class="key"><?php echo $day_2th ?>.4-</span>
									</li>
									<?php $day_3th = $this->main->advanced_repeating_sort_day($this->main->get_first_day_of_week(), 3); ?>
									<li class="<?php $this->main->mec_active($advanced_days, "{$day_3th}.4"); ?>">
										<?php _e($day_3th, 'modern-events-calendar-lite'); ?>
										<span class="key"><?php echo $day_3th ?>.4-</span>
									</li>
									<?php $day_4th = $this->main->advanced_repeating_sort_day($this->main->get_first_day_of_week(), 4); ?>
									<li class="<?php $this->main->mec_active($advanced_days, "{$day_4th}.4"); ?>">
										<?php _e($day_4th, 'modern-events-calendar-lite'); ?>
										<span class="key"><?php echo $day_4th ?>.4-</span>
									</li>
									<?php $day_5th = $this->main->advanced_repeating_sort_day($this->main->get_first_day_of_week(), 5); ?>
									<li class="<?php $this->main->mec_active($advanced_days, "{$day_5th}.4"); ?>">
										<?php _e($day_5th, 'modern-events-calendar-lite'); ?>
										<span class="key"><?php echo $day_5th ?>.4-</span>
									</li>
									<?php $day_6th = $this->main->advanced_repeating_sort_day($this->main->get_first_day_of_week(), 6); ?>
									<li class="<?php $this->main->mec_active($advanced_days, "{$day_6th}.4"); ?>">
										<?php _e($day_6th, 'modern-events-calendar-lite'); ?>
										<span class="key"><?php echo $day_6th ?>.4-</span>
									</li>
									<?php $day_7th = $this->main->advanced_repeating_sort_day($this->main->get_first_day_of_week(), 7); ?>
									<li class="<?php $this->main->mec_active($advanced_days, "{$day_7th}.4"); ?>">
										<?php _e($day_7th, 'modern-events-calendar-lite'); ?>
										<span class="key"><?php echo $day_7th ?>.4-</span>
									</li>
								</ul>
							</ul>
                			<ul>
								<li>
									<?php _e('Last', 'modern-events-calendar-lite'); ?>
								</li>
								<ul>
									<?php $day_1th = $this->main->advanced_repeating_sort_day($this->main->get_first_day_of_week(), 1); ?>
									<li class="<?php $this->main->mec_active($advanced_days, "{$day_1th}.l"); ?>">
										<?php _e($day_1th, 'modern-events-calendar-lite'); ?>
										<span class="key"><?php echo $day_1th ?>.l-</span>
									</li>
									<?php $day_2th = $this->main->advanced_repeating_sort_day($this->main->get_first_day_of_week(), 2); ?>
									<li class="<?php $this->main->mec_active($advanced_days, "{$day_2th}.l"); ?>">
										<?php _e($day_2th, 'modern-events-calendar-lite'); ?>
										<span class="key"><?php echo $day_2th ?>.l-</span>
									</li>
									<?php $day_3th = $this->main->advanced_repeating_sort_day($this->main->get_first_day_of_week(), 3); ?>
									<li class="<?php $this->main->mec_active($advanced_days, "{$day_3th}.l"); ?>">
										<?php _e($day_3th, 'modern-events-calendar-lite'); ?>
										<span class="key"><?php echo $day_3th ?>.l-</span>
									</li>
									<?php $day_4th = $this->main->advanced_repeating_sort_day($this->main->get_first_day_of_week(), 4); ?>
									<li class="<?php $this->main->mec_active($advanced_days, "{$day_4th}.l"); ?>">
										<?php _e($day_4th, 'modern-events-calendar-lite'); ?>
										<span class="key"><?php echo $day_4th ?>.l-</span>
									</li>
									<?php $day_5th = $this->main->advanced_repeating_sort_day($this->main->get_first_day_of_week(), 5); ?>
									<li class="<?php $this->main->mec_active($advanced_days, "{$day_5th}.l"); ?>">
										<?php _e($day_5th, 'modern-events-calendar-lite'); ?>
										<span class="key"><?php echo $day_5th ?>.l-</span>
									</li>
									<?php $day_6th = $this->main->advanced_repeating_sort_day($this->main->get_first_day_of_week(), 6); ?>
									<li class="<?php $this->main->mec_active($advanced_days, "{$day_6th}.l"); ?>">
										<?php _e($day_6th, 'modern-events-calendar-lite'); ?>
										<span class="key"><?php echo $day_6th ?>.l-</span>
									</li>
									<?php $day_7th = $this->main->advanced_repeating_sort_day($this->main->get_first_day_of_week(), 7); ?>
									<li class="<?php $this->main->mec_active($advanced_days, "{$day_7th}.l"); ?>">
										<?php _e($day_7th, 'modern-events-calendar-lite'); ?>
										<span class="key"><?php echo $day_7th ?>.l-</span>
									</li>
								</ul>
							</ul>
                            <input class="mec-col-2" type="hidden" name="mec[date][repeat][advanced]"
                                   id="mec_date_repeat_advanced" value="<?php echo esc_attr($advanced_str); ?>"/>
                        </div>
                    </div>
                    <div id="mec_end_wrapper">
                        <div class="mec-form-row">
                            <label for="mec_repeat_ends_never">
                                <h4 class="mec-title"><?php _e('Ends Repeat', 'modern-events-calendar-lite'); ?></h4>
                            </label>
                        </div>
                        <div class="mec-form-row">
                            <input
                                <?php
                                if ($mec_repeat_end == 'never') {
                                    echo 'checked="checked"';
                                }
                                ?>
                                    type="radio" value="never" name="mec[date][repeat][end]"
                                    id="mec_repeat_ends_never"/>
                            <label for="mec_repeat_ends_never"><?php _e('Never', 'modern-events-calendar-lite'); ?></label>
                        </div>
                        <div class="mec-form-row">
                            <div class="mec-col-3">
                                <input
                                    <?php
                                    if ($mec_repeat_end == 'date') {
                                        echo 'checked="checked"';
                                    }
                                    ?>
                                        type="radio" value="date" name="mec[date][repeat][end]"
                                        id="mec_repeat_ends_date"/>
                                <label for="mec_repeat_ends_date"><?php _e('On', 'modern-events-calendar-lite'); ?></label>
                            </div>
                            <input class="mec-col-2" type="text" name="mec[date][repeat][end_at_date]"
                                   id="mec_date_repeat_end_at_date" autocomplete="off"
                                   value="<?php echo esc_attr( $this->main->standardize_format( $repeat_end_at_date, $datepicker_format ) ); ?>"/>
                        </div>
                        <div class="mec-form-row">
                            <div class="mec-col-3">
                                <input
                                    <?php
                                    if ($mec_repeat_end == 'occurrences') {
                                        echo 'checked="checked"';
                                    }
                                    ?>
                                        type="radio" value="occurrences" name="mec[date][repeat][end]"
                                        id="mec_repeat_ends_occurrences"/>
                                <label for="mec_repeat_ends_occurrences"><?php _e('After', 'modern-events-calendar-lite'); ?></label>
                            </div>
                            <input class="mec-col-2" type="text" name="mec[date][repeat][end_at_occurrences]"
                                   id="mec_date_repeat_end_at_occurrences" autocomplete="off"
                                   placeholder="<?php _e('Occurrences times', 'modern-events-calendar-lite'); ?>"
                                   value="<?php echo esc_attr(($repeat_end_at_occurrences + 1)); ?>"/>
                            <span class="mec-tooltip">
								<div class="box top">
									<h5 class="title"><?php _e('Occurrences times', 'modern-events-calendar-lite'); ?></h5>
									<div class="content"><p><?php esc_attr_e('The event will finish after certain repeats. For example if you set it to 10, the event will finish after 10 occurrences.', 'modern-events-calendar-lite'); ?>
                                            <a href="https://webnus.net/dox/modern-events-calendar/date-and-time/"
                                               target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>
								</div>
								<i title="" class="dashicons-before dashicons-editor-help"></i>
							</span>
                        </div>
                    </div>
                    <div class="mec-form-row">
                        <input
                            <?php
                            if ($one_occurrence == '1') {
                                echo 'checked="checked"';
                            }
                            ?>
                                type="checkbox" name="mec[date][one_occurrence]" id="mec-one-occurrence" value="1"/><label
                                for="mec-one-occurrence"><?php _e('Show only one occurrence of this event', 'modern-events-calendar-lite'); ?></label>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Show cost option of event into the Add/Edit event page
     *
     * @author Webnus <info@webnus.biz>
     * @param object $post
     */
    public function meta_box_cost($post)
    {
        $cost = get_post_meta($post->ID, 'mec_cost', true);

        $currency = get_post_meta($post->ID, 'mec_currency', true);
        if(!is_array($currency)) $currency = array();

        $type = ((isset($this->settings['single_cost_type']) and trim($this->settings['single_cost_type'])) ? $this->settings['single_cost_type'] : 'numeric');
        $currency_per_event = ((isset($this->settings['currency_per_event']) and trim($this->settings['currency_per_event'])) ? $this->settings['currency_per_event'] : 0);

        $currencies = $this->main->get_currencies();
        $current_currency = (isset($currency['currency']) ? $currency['currency'] : (isset($this->settings['currency']) ? $this->settings['currency'] : 'USD'));
        ?>
        <div class="mec-meta-box-fields mec-event-tab-content" id="mec-cost">
            <h4><?php echo $this->main->m('event_cost', __('Event Cost', 'modern-events-calendar-lite')); ?></h4>
            <div id="mec_meta_box_cost_form">
                <div class="mec-form-row">
                    <?php if( apply_filters( 'mec_event_cost_custom_field_status', false ) ): ?>
                        <?php do_action( 'mec_event_cost_custom_field', $cost, $type, 'mec[cost]' ); ?>
                    <?php else: ?>
                        <input type="<?php echo ($type === 'alphabetic' ? 'text' : 'number'); ?>" <?php echo ($type === 'numeric' ? 'min="0" step="any"' : ''); ?> class="mec-col-3" name="mec[cost]" id="mec_cost"
                            value="<?php echo esc_attr($cost); ?>" title="<?php _e('Cost', 'modern-events-calendar-lite'); ?>" placeholder="<?php _e('Cost', 'modern-events-calendar-lite'); ?>"/>
                    <?php endif; ?>
                </div>
            </div>

            <?php if($currency_per_event): ?>
            <h4><?php echo __('Currency Options', 'modern-events-calendar-lite'); ?></h4>
            <div class="mec-form-row">
                <label class="mec-col-2" for="mec_currency_currency"><?php _e('Currency', 'modern-events-calendar-lite'); ?></label>
                <div class="mec-col-4">
                    <select name="mec[currency][currency]" id="mec_currency_currency">
                        <?php foreach($currencies as $c=>$currency_name): ?>
                        <option value="<?php echo $c; ?>" <?php echo (($current_currency == $c) ? 'selected="selected"' : ''); ?>><?php echo $currency_name; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="mec-form-row">
                <label class="mec-col-2" for="mec_currency_currency_symptom"><?php _e('Currency Sign', 'modern-events-calendar-lite'); ?></label>
                <div class="mec-col-4">
                    <input type="text" name="mec[currency][currency_symptom]" id="mec_currency_currency_symptom" value="<?php echo (isset($currency['currency_symptom']) ? $currency['currency_symptom'] : ''); ?>" />
                    <span class="mec-tooltip">
                        <div class="box left">
                            <h5 class="title"><?php _e('Currency Sign', 'modern-events-calendar-lite'); ?></h5>
                            <div class="content"><p><?php esc_attr_e("Default value will be \"currency\" if you leave it empty.", 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/currency-options/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>
                        </div>
                        <i title="" class="dashicons-before dashicons-editor-help"></i>
                    </span>
                </div>
            </div>
            <div class="mec-form-row">
                <label class="mec-col-2" for="mec_currency_currency_sign"><?php _e('Currency Position', 'modern-events-calendar-lite'); ?></label>
                <div class="mec-col-4">
                    <select name="mec[currency][currency_sign]" id="mec_currency_currency_sign">
                        <option value="before" <?php echo ((isset($currency['currency_sign']) and $currency['currency_sign'] == 'before') ? 'selected="selected"' : ''); ?>><?php _e('$10 (Before)', 'modern-events-calendar-lite'); ?></option>
                        <option value="before_space" <?php echo ((isset($currency['currency_sign']) and $currency['currency_sign'] == 'before_space') ? 'selected="selected"' : ''); ?>><?php _e('$ 10 (Before with Space)', 'modern-events-calendar-lite'); ?></option>
                        <option value="after" <?php echo ((isset($currency['currency_sign']) and $currency['currency_sign'] == 'after') ? 'selected="selected"' : ''); ?>><?php _e('10$ (After)', 'modern-events-calendar-lite'); ?></option>
                        <option value="after_space" <?php echo ((isset($currency['currency_sign']) and $currency['currency_sign'] == 'after_space') ? 'selected="selected"' : ''); ?>><?php _e('10 $ (After with Space)', 'modern-events-calendar-lite'); ?></option>
                    </select>
                </div>
            </div>
            <div class="mec-form-row">
                <label class="mec-col-2" for="mec_currency_thousand_separator"><?php _e('Thousand Separator', 'modern-events-calendar-lite'); ?></label>
                <div class="mec-col-4">
                    <input type="text" name="mec[currency][thousand_separator]" id="mec_currency_thousand_separator" value="<?php echo (isset($currency['thousand_separator']) ? $currency['thousand_separator'] : ','); ?>" />
                </div>
            </div>
            <div class="mec-form-row">
                <label class="mec-col-2" for="mec_currency_decimal_separator"><?php _e('Decimal Separator', 'modern-events-calendar-lite'); ?></label>
                <div class="mec-col-4">
                    <input type="text" name="mec[currency][decimal_separator]" id="mec_currency_decimal_separator" value="<?php echo (isset($currency['decimal_separator']) ? $currency['decimal_separator'] : '.'); ?>" />
                </div>
            </div>
            <div class="mec-form-row">
                <div class="mec-col-12">
                    <label for="mec_currency_decimal_separator_status">
                        <input type="hidden" name="mec[currency][decimal_separator_status]" value="1" />
                        <input type="checkbox" name="mec[currency][decimal_separator_status]" id="mec_currency_decimal_separator_status" <?php echo ((isset($currency['decimal_separator_status']) and $currency['decimal_separator_status'] == '0') ? 'checked="checked"' : ''); ?> value="0" />
                        <?php _e('No decimal', 'modern-events-calendar-lite'); ?>
                    </label>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <?php
    }

    public function meta_box_fields($post)
    {
        $fields = $this->getEventFields();
        $fields->form(array(
            'id' => 'mec-event-data',
            'class' => 'mec-meta-box-fields mec-event-tab-content',
            'post' => $post,
            'data' => get_post_meta($post->ID, 'mec_fields', true),
            'name_prefix' => 'mec',
            'id_prefix' => 'mec_event_fields_',
            'mandatory_status' => true,
        ));
    }

    /**
     * Show exceptions options of event into the Add/Edit event page
     *
     * @author Webnus <info@webnus.biz>
     * @param object $post
     */
    public function meta_box_exceptional_days($post)
    {
        $not_in_days_str = get_post_meta($post->ID, 'mec_not_in_days', true);
        $not_in_days = trim($not_in_days_str) ? explode(',', $not_in_days_str) : array();
        ?>
        <div class="mec-meta-box-fields mec-event-tab-content" id="mec-exceptional-days">
            <h4><?php _e('Exceptional Days (Exclude Dates)', 'modern-events-calendar-lite'); ?></h4>
            <div id="mec_meta_box_exceptions_form">

                <div id="mec_exceptions_not_in_days_container">
                    <div class="mec-title">
                        <span class="mec-dashicons dashicons dashicons-calendar-alt"></span>
                        <label for="mec_exceptions_not_in_days_date"><?php _e('Exclude certain days', 'modern-events-calendar-lite'); ?></label>
                    </div>
                    <div class="mec-form-row">
                        <div class="mec-col-12">
                            <input type="text" id="mec_exceptions_not_in_days_date" value=""
                                   placeholder="<?php _e('Date', 'modern-events-calendar-lite'); ?>" class="mec_date_picker_dynamic_format" autocomplete="off"/>
                            <button class="button" type="button"
                                    id="mec_add_not_in_days"><?php _e('Add', 'modern-events-calendar-lite'); ?></button>
                            <span class="mec-tooltip">
								<div class="box top">
									<h5 class="title"><?php _e('Exclude certain days', 'modern-events-calendar-lite'); ?></h5>
									<div class="content"><p><?php esc_attr_e('Exclude certain days from event occurrence dates. Please note that you can exclude only single day occurrences and you cannot exclude one day from multiple day occurrences.', 'modern-events-calendar-lite'); ?>
                                            <a href="https://webnus.net/dox/modern-events-calendar/exceptional-days/"
                                               target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>
								</div>
								<i title="" class="dashicons-before dashicons-editor-help"></i>
							</span>
                        </div>
                    </div>
                    <div class="mec-form-row mec-certain-day" id="mec_not_in_days">
                        <?php
                        // This date format used for datepicker
                        $datepicker_format = (isset($this->settings['datepicker_format']) and trim($this->settings['datepicker_format'])) ? $this->settings['datepicker_format'] : 'Y-m-d';
                        $i = 1;
                        foreach ($not_in_days as $not_in_day) : ?>
                            <div class="mec-form-row" id="mec_not_in_days_row<?php echo $i; ?>">
                                <input type="hidden" name="mec[not_in_days][<?php echo $i; ?>]"
                                       value="<?php echo $this->main->standardize_format( $not_in_day, $datepicker_format ); ?>"/>
                                <span class="mec-not-in-days-day"><?php echo $this->main->standardize_format( $not_in_day, $datepicker_format ); ?></span>
                                <span class="mec-not-in-days-remove"
                                      onclick="mec_not_in_days_remove(<?php echo $i; ?>);">x</span>
                            </div>
                            <?php
                            $i++;
                        endforeach;
                        ?>
                    </div>
                    <input type="hidden" id="mec_new_not_in_days_key" value="<?php echo $i + 1; ?>"/>
                    <div class="mec-util-hidden" id="mec_new_not_in_days_raw">
                        <div class="mec-form-row" id="mec_not_in_days_row:i:">
                            <input type="hidden" name="mec[not_in_days][:i:]" value=":val:"/>
                            <span class="mec-not-in-days-day">:val:</span>
                            <span class="mec-not-in-days-remove" onclick="mec_not_in_days_remove(:i:);">x</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <?php
    }

    /**
     * Show hourly schedule options of event into the Add/Edit event page
     *
     * @author Webnus <info@webnus.biz>
     * @param object $post
     */
    public function meta_box_hourly_schedule($post)
    {
        $meta_hourly_schedules = get_post_meta($post->ID, 'mec_hourly_schedules', true);
        if(is_array($meta_hourly_schedules) and count($meta_hourly_schedules))
        {
            $first_key = key($meta_hourly_schedules);

            $hourly_schedules = array();
            if(!isset($meta_hourly_schedules[$first_key]['schedules']))
            {
                $hourly_schedules[] = array(
                    'title' => __('Day 1', 'modern-events-calendar-lite'),
                    'schedules' => $meta_hourly_schedules,
                );
            }
            else
            {
                $hourly_schedules = $meta_hourly_schedules;
            }
        }
        else
        {
            $hourly_schedules = array();
        }

        // Status of Speakers Feature
        $speakers_status = (!isset($this->settings['speakers_status']) or (isset($this->settings['speakers_status']) and !$this->settings['speakers_status'])) ? false : true;
        $speakers = get_terms('mec_speaker', array(
            'orderby' => 'name',
            'order' => 'ASC',
            'hide_empty' => '0',
        ));

        $hourly_schedule = $this->getHourlySchedule();
        $hourly_schedule->form(array(
            'hourly_schedules' => $hourly_schedules,
            'speakers_status' => $speakers_status,
            'speakers' => $speakers,
        ));
    }

    /**
     * Show read more option of event into the Add/Edit event page
     *
     * @author Webnus <info@webnus.biz>
     * @param object $post
     */
    public function meta_box_links($post)
    {
        $read_more = get_post_meta($post->ID, 'mec_read_more', true);
        $more_info = get_post_meta($post->ID, 'mec_more_info', true);
        $more_info_title = get_post_meta($post->ID, 'mec_more_info_title', true);
        $more_info_target = get_post_meta($post->ID, 'mec_more_info_target', true);
        ?>
        <div class="mec-meta-box-fields mec-event-tab-content" id="mec-read-more">
            <h4><?php _e('Event Links', 'modern-events-calendar-lite'); ?></h4>
            <div class="mec-form-row">
                <label class="mec-col-2"
                       for="mec_read_more_link"><?php echo $this->main->m('read_more_link', __('Event Link', 'modern-events-calendar-lite')); ?></label>
                <input class="mec-col-7" type="text" name="mec[read_more]" id="mec_read_more_link"
                       value="<?php echo esc_attr($read_more); ?>"
                       placeholder="<?php _e('eg. http://yoursite.com/your-event', 'modern-events-calendar-lite'); ?>"/>
                                       <?php do_action('extra_event_link', $post->ID); ?>

                <span class="mec-tooltip">
					<div class="box top">
						<h5 class="title"><?php _e('Event Link', 'modern-events-calendar-lite'); ?></h5>
						<div class="content"><p><?php esc_attr_e('If you fill it, it will replace the default event page link. Insert full link including http(s):// - Also, if you use advertising URL, can use URL Shortener', 'modern-events-calendar-lite'); ?>
                                <a href="https://bit.ly/"
                                   target="_blank"><?php _e('URL Shortener', 'modern-events-calendar-lite'); ?></a></p></div>
					</div>
					<i title="" class="dashicons-before dashicons-editor-help"></i>
				</span>
            </div>
            <div class="mec-form-row">
                <label class="mec-col-2"
                       for="mec_more_info_link"><?php echo $this->main->m('more_info_link', __('More Info', 'modern-events-calendar-lite')); ?></label>
                <input class="mec-col-3" type="text" name="mec[more_info]" id="mec_more_info_link"
                       value="<?php echo esc_attr($more_info); ?>"
                       placeholder="<?php _e('eg. http://yoursite.com/your-event', 'modern-events-calendar-lite'); ?>"/>
                <input class="mec-col-2" type="text" name="mec[more_info_title]" id="mec_more_info_title"
                       value="<?php echo esc_attr($more_info_title); ?>"
                       placeholder="<?php _e('More Information', 'modern-events-calendar-lite'); ?>"/>
                <select class="mec-col-2" name="mec[more_info_target]" id="mec_more_info_target">
                    <option value="_self" <?php echo($more_info_target == '_self' ? 'selected="selected"' : ''); ?>><?php _e('Current Window', 'modern-events-calendar-lite'); ?></option>
                    <option value="_blank" <?php echo($more_info_target == '_blank' ? 'selected="selected"' : ''); ?>><?php _e('New Window', 'modern-events-calendar-lite'); ?></option>
                </select>
                <span class="mec-tooltip">
					<div class="box top">
						<h5 class="title"><?php _e('More Info', 'modern-events-calendar-lite'); ?></h5>
						<div class="content"><p><?php esc_attr_e('If you fill it, it will be shown in event details page as an optional link. Insert full link including http(s)://', 'modern-events-calendar-lite'); ?>
                                <a href="https://webnus.net/dox/modern-events-calendar/add-event/"
                                   target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>
					</div>
					<i title="" class="dashicons-before dashicons-editor-help"></i>
				</span>
            </div>
        </div>
        <?php
    }

    /**
     * Show booking meta box contents
     *
     * @author Webnus <info@webnus.biz>
     * @param object $post
     */
    public function meta_box_booking($post)
    {
        $gateway_settings = $this->main->get_gateways_options();
    ?>
        <div class="mec-add-booking-tabs-wrap">
            <div class="mec-add-booking-tabs-left">
                <a class="mec-add-booking-tabs-link mec-tab-active" data-href="mec_meta_box_booking_options_form_1" href="#"><?php echo esc_html__('Booking Options','modern-events-calendar-lite'); ?></a>
                <a class="mec-add-booking-tabs-link" data-href="mec_meta_box_booking_options_form_2" href="#"><?php echo esc_html__('Total User Booking Limits','modern-events-calendar-lite'); ?></a>
                <a class="mec-add-booking-tabs-link" data-href="mec-tickets" href="#"><?php echo esc_html__('Tickets','modern-events-calendar-lite'); ?></a>
                <?php if(isset($this->settings['taxes_fees_status']) and $this->settings['taxes_fees_status']): ?>
                <a class="mec-add-booking-tabs-link" data-href="mec-fees" href="#"><?php echo esc_html__('Fees','modern-events-calendar-lite'); ?></a>
                <?php endif; ?>
                <?php if(isset($this->settings['ticket_variations_status']) and $this->settings['ticket_variations_status']): ?>
                <a class="mec-add-booking-tabs-link" data-href="mec-ticket-variations" href="#"><?php echo esc_html__('Ticket Variations / Options','modern-events-calendar-lite'); ?></a>
                <?php endif; ?>
                <a class="mec-add-booking-tabs-link" data-href="mec-reg-fields" href="#"><?php echo esc_html__('Booking Form','modern-events-calendar-lite'); ?></a>
                <?php if(isset($gateway_settings['op_status']) && $gateway_settings['op_status'] == 1): ?>
                <a class="mec-add-booking-tabs-link" data-href="mec_meta_box_op_form" href="#"><?php echo esc_html__('Organizer Payment','modern-events-calendar-lite'); ?></a>
                <?php endif; ?>
                <?php if(isset($this->settings['downloadable_file_status']) and $this->settings['downloadable_file_status']): ?>
                <a class="mec-add-booking-tabs-link" data-href="mec-downloadable-file" href="#"><?php echo esc_html__('Downloadable File','modern-events-calendar-lite'); ?></a>
                <?php endif; ?>
                <?php if(isset($gateway_settings['gateways_per_event']) and $gateway_settings['gateways_per_event']): ?>
                <a class="mec-add-booking-tabs-link" data-href="mec_meta_box_booking_options_form_gateways_per_event" href="#"><?php echo esc_html__('Payment Gateways','modern-events-calendar-lite'); ?></a>
                <?php endif; ?>
                <a class="mec-add-booking-tabs-link" data-href="mec_meta_box_booking_options_form_attendees" href="#"><?php echo esc_html__('Bookings','modern-events-calendar-lite'); ?></a>
                <?php do_action('add_event_booking_sections_left_menu'); ?>
            </div>
            <div class="mec-add-booking-tabs-right">
                <?php do_action('mec_metabox_booking', $post); ?>
            </div>
        </div>
        <script>
        jQuery(".mec-add-booking-tabs-link").on("click", function(e)
        {
            e.preventDefault();
            var href = jQuery(this).attr("data-href");
            jQuery(".mec-booking-tab-content,.mec-add-booking-tabs-link").removeClass("mec-tab-active");
            jQuery(this).addClass("mec-tab-active");
            jQuery("#" + href ).addClass("mec-tab-active");
        });
        </script>
    <?php
    }

    /**
     * Show booking options of event into the Add/Edit event page
     *
     * @author Webnus <info@webnus.biz>
     * @param object $post
     */
    public function meta_box_booking_options($post)
    {
        $FES = (boolean) !is_admin();

        $booking_options = get_post_meta($post->ID, 'mec_booking', true);
        if(!is_array($booking_options)) $booking_options = array();

        $bookings_limit = isset($booking_options['bookings_limit']) ? $booking_options['bookings_limit'] : '';
        $bookings_limit_unlimited = isset($booking_options['bookings_limit_unlimited']) ? $booking_options['bookings_limit_unlimited'] : 0;
        $bookings_user_limit = isset($booking_options['bookings_user_limit']) ? $booking_options['bookings_user_limit'] : '';
        $bookings_user_limit_unlimited = isset($booking_options['bookings_user_limit_unlimited']) ? $booking_options['bookings_user_limit_unlimited'] : true;
        $bookings_all_occurrences = isset($booking_options['bookings_all_occurrences']) ? $booking_options['bookings_all_occurrences'] : 0;
        $bookings_all_occurrences_multiple = isset($booking_options['bookings_all_occurrences_multiple']) ? $booking_options['bookings_all_occurrences_multiple'] : 0;
        $bookings_stop_selling_after_first_occurrence = isset($booking_options['stop_selling_after_first_occurrence']) ? $booking_options['stop_selling_after_first_occurrence'] : 0;
        $bookings_last_few_tickets_percentage_inherite = isset($booking_options['last_few_tickets_percentage_inherit']) ? $booking_options['last_few_tickets_percentage_inherit'] : 1;
        $bookings_last_few_tickets_percentage = ((isset($booking_options['last_few_tickets_percentage']) and trim($booking_options['last_few_tickets_percentage']) != '') ? max(1, $booking_options['last_few_tickets_percentage']) : (isset($this->settings['booking_last_few_tickets_percentage']) ? max(1, $this->settings['booking_last_few_tickets_percentage']) : 15));

        $bookings_thankyou_page_inherit = isset($booking_options['thankyou_page_inherit']) ? $booking_options['thankyou_page_inherit'] : 1;

        $loggedin_discount = isset($booking_options['loggedin_discount']) ? $booking_options['loggedin_discount'] : '';

        global $wp_roles;
        $roles = $wp_roles->get_names();

        $gateway_settings = $this->main->get_gateways_options();
        $gateways = $this->main->get_gateways();

        $enableds_gateways = array();
        foreach($gateways as $gateway)
        {
            if(!$gateway->enabled()) continue;
            $enableds_gateways[] = $gateway;
        }

        // WordPress Pages
        $pages = get_pages();
        ?>
        <div id="mec-booking">
            <div class="mec-meta-box-fields mec-booking-tab-content mec-tab-active" id="mec_meta_box_booking_options_form_1">

                <?php if(!$FES or ($FES and (!isset($this->settings['fes_section_booking_tbl']) or (isset($this->settings['fes_section_booking_tbl']) and $this->settings['fes_section_booking_tbl'])))): ?>
                <h4 class="mec-title"><label for="mec_bookings_limit"><?php _e('Total booking limit', 'modern-events-calendar-lite'); ?></label></h4>
                <div class="mec-form-row">
                    <label class="mec-col-4" for="mec_bookings_limit_unlimited" id="mec_bookings_limit_unlimited_label">
                        <input type="hidden" name="mec[booking][bookings_limit_unlimited]" value="0"/>
                        <input id="mec_bookings_limit_unlimited"
                            <?php
                            if ($bookings_limit_unlimited == 1) {
                                echo 'checked="checked"';
                            }
                            ?>
                               type="checkbox" value="1" name="mec[booking][bookings_limit_unlimited]"/>
                        <?php _e('Unlimited', 'modern-events-calendar-lite'); ?>
                        <span class="mec-tooltip">
                            <div class="box">
                                <h5 class="title"><?php _e('Total booking limit', 'modern-events-calendar-lite'); ?></h5>
                                <div class="content">
                                    <p>
                                        <?php esc_attr_e('If you want to set a limit to all tickets, uncheck this checkbox and put a limitation number.', 'modern-events-calendar-lite'); ?>
                                        <a href="https://webnus.net/dox/modern-events-calendar/total-booking-limits/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a>
                                        <a href="https://webnus.net/dox/modern-events-calendar/add-a-booking-system/" target="_blank"><?php _e('Read About A Booking System', 'modern-events-calendar-lite'); ?></a>
                                    </p>
                                </div>
                            </div>
                            <i title="" class="dashicons-before dashicons-editor-help"></i>
                        </span>
                    </label>
                    <input class="mec-col-4 <?php echo ($bookings_limit_unlimited == 1) ? 'mec-util-hidden' : ''; ?>" type="text" name="mec[booking][bookings_limit]" id="mec_bookings_limit"
                           value="<?php echo esc_attr($bookings_limit); ?>" placeholder="<?php _e('100', 'modern-events-calendar-lite'); ?>"/>
                </div>
                <?php endif; ?>

                <?php if(!$FES or ($FES and (!isset($this->settings['fes_section_booking_dpur']) or (isset($this->settings['fes_section_booking_dpur']) and $this->settings['fes_section_booking_dpur'])))): ?>
                <h4 class="mec-title"><?php _e('Discount per user roles', 'modern-events-calendar-lite'); ?></h4>
                <?php foreach($roles as $role_key => $role_name): $role_discount = isset($booking_options['roles_discount_'.$role_key]) ? $booking_options['roles_discount_'.$role_key] : $loggedin_discount; ?>
                <div class="mec-form-row">
                    <div class="mec-col-2">
                        <label for="mec_bookings_roles_discount_<?php echo $role_key; ?>"><?php echo $role_name; ?></label>
                    </div>
                    <input class="mec-col-4" type="text" name="mec[booking][roles_discount_<?php echo $role_key; ?>]" id="mec_bookings_roles_discount_<?php echo $role_key; ?>" value="<?php echo esc_attr($role_discount); ?>" placeholder="<?php _e('5', 'modern-events-calendar-lite'); ?>">
                </div>
                <?php endforeach; ?>
                <?php endif; ?>

                <?php if(!$FES or ($FES and (!isset($this->settings['fes_section_booking_bao']) or (isset($this->settings['fes_section_booking_bao']) and $this->settings['fes_section_booking_bao'])))): ?>
                <h4 class="mec-title"><?php _e('Book All Occurrences', 'modern-events-calendar-lite'); ?></h4>
                <div class="mec-form-row">
                    <label class="mec-col-4" for="mec_bookings_all_occurrences">
                        <input type="hidden" name="mec[booking][bookings_all_occurrences]" value="0"/>
                        <input id="mec_bookings_all_occurrences"
                            <?php
                            if ($bookings_all_occurrences == 1) {
                                echo 'checked="checked"';
                            }
                            ?>
                               type="checkbox" value="1" name="mec[booking][bookings_all_occurrences]" onchange="jQuery('#mec_bookings_all_occurrences_options').toggle();"/>
                        <?php _e('Sell all occurrences by one booking', 'modern-events-calendar-lite'); ?>
                        <span class="mec-tooltip">
                            <div class="box">
                                <h5 class="title"><?php _e('Book All Occurrences', 'modern-events-calendar-lite'); ?></h5>
                                <div class="content">
                                    <p>
                                        <?php esc_attr_e("If you have a series of events and you want to sell all of them at once, this option is for you! For example a weekly yoga course or something similar.", 'modern-events-calendar-lite'); ?>
                                    </p>
                                </div>
                            </div>
                            <i title="" class="dashicons-before dashicons-editor-help"></i>
                        </span>
                    </label>
                </div>
                <div class="mec-form-row <?php echo (!$bookings_all_occurrences ? 'mec-util-hidden' : ''); ?>" id="mec_bookings_all_occurrences_options">
                    <label class="mec-col-8" for="mec_bookings_all_occurrences_multiple">
                        <input type="hidden" name="mec[booking][bookings_all_occurrences_multiple]" value="0"/>
                        <input id="mec_bookings_all_occurrences_multiple"
                            <?php
                            if ($bookings_all_occurrences_multiple == 1) {
                                echo 'checked="checked"';
                            }
                            ?>
                               type="checkbox" value="1" name="mec[booking][bookings_all_occurrences_multiple]"/>
                        <?php _e('Allow multiple bookings by same email on different dates.', 'modern-events-calendar-lite'); ?>
                    </label>
                </div>
                <?php endif; ?>

                <?php if(!$FES or ($FES and (!isset($this->settings['fes_section_booking_io']) or (isset($this->settings['fes_section_booking_io']) and $this->settings['fes_section_booking_io'])))): ?>
                <h4 class="mec-title"><?php _e('Interval Options', 'modern-events-calendar-lite'); ?></h4>
                <div class="mec-form-row">
                    <label class="mec-col-4" for="mec_booking_show_booking_form_interval"><?php _e('Show Booking Form Interval', 'modern-events-calendar-lite'); ?></label>
                    <div class="mec-col-4">
                        <input type="number" id="mec_booking_show_booking_form_interval" name="mec[booking][show_booking_form_interval]" value="<?php echo ((isset($booking_options['show_booking_form_interval']) and trim($booking_options['show_booking_form_interval']) != '') ? $booking_options['show_booking_form_interval'] : ''); ?>" placeholder="<?php esc_attr_e('Minutes (e.g 5)', 'modern-events-calendar-lite'); ?>" />
                        <span class="mec-tooltip">
                            <div class="box">
                                <h5 class="title"><?php _e('Show Booking Form Interval', 'modern-events-calendar-lite'); ?></h5>
                                <div class="content"><p><?php esc_attr_e("You can show booking form only at certain times before event start. If you set this option to 30 then booking form will open only 30 minutes before starting the event! One day is 1440 minutes.", 'modern-events-calendar-lite'); ?></p></div>
                            </div>
                            <i title="" class="dashicons-before dashicons-editor-help"></i>
                        </span>
                    </div>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-8" for="mec_booking_stop_selling_after_first_occurrence">
                        <input type="hidden" name="mec[booking][stop_selling_after_first_occurrence]" value="0"/>
                        <input id="mec_booking_stop_selling_after_first_occurrence"
                            <?php
                            if ($bookings_stop_selling_after_first_occurrence == 1) {
                                echo 'checked="checked"';
                            }
                            ?>
                               type="checkbox" value="1" name="mec[booking][stop_selling_after_first_occurrence]"/>
                        <?php _e('Stop selling tickets after first occurrence.', 'modern-events-calendar-lite'); ?>
                    </label>
                </div>
                <?php endif; ?>

                <?php if(!$FES or ($FES and (!isset($this->settings['fes_section_booking_aa']) or (isset($this->settings['fes_section_booking_aa']) and $this->settings['fes_section_booking_aa'])))): ?>
                <h4><?php _e('Automatic Approval', 'modern-events-calendar-lite'); ?></h4>
                <div class="mec-form-row">
                    <label class="mec-col-4" for="mec_booking_auto_verify"><?php _e('Email Verification', 'modern-events-calendar-lite'); ?></label>
                    <div class="mec-col-4">
                        <select name="mec[booking][auto_verify]" id="mec_booking_auto_verify">
                            <option value="global" <?php if(isset($booking_options['auto_verify']) and 'global' == $booking_options['auto_verify']) echo 'selected="selected"'; ?>><?php _e('Inherit from global options', 'modern-events-calendar-lite'); ?></option>
                            <option value="0" <?php if(isset($booking_options['auto_verify']) and '0' == $booking_options['auto_verify']) echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                            <option value="1" <?php if(isset($booking_options['auto_verify']) and '1' == $booking_options['auto_verify']) echo 'selected="selected"'; ?>><?php _e('Enabled', 'modern-events-calendar-lite'); ?></option>
                        </select>
                    </div>
                </div>
                <div class="mec-form-row">
                    <label class="mec-col-4" for="mec_booking_auto_confirm"><?php _e('Booking Confirmation', 'modern-events-calendar-lite'); ?></label>
                    <div class="mec-col-4">
                        <select name="mec[booking][auto_confirm]" id="mec_booking_auto_confirm">
                            <option value="global" <?php if(isset($booking_options['auto_confirm']) and 'global' == $booking_options['auto_confirm']) echo 'selected="selected"'; ?>><?php _e('Inherit from global options', 'modern-events-calendar-lite'); ?></option>
                            <option value="0" <?php if(isset($booking_options['auto_confirm']) and '0' == $booking_options['auto_confirm']) echo 'selected="selected"'; ?>><?php _e('Disabled', 'modern-events-calendar-lite'); ?></option>
                            <option value="1" <?php if(isset($booking_options['auto_confirm']) and '1' == $booking_options['auto_confirm']) echo 'selected="selected"'; ?>><?php _e('Enabled', 'modern-events-calendar-lite'); ?></option>
                        </select>
                    </div>
                </div>
                <?php endif; ?>

                <?php if(!$FES or ($FES and (!isset($this->settings['fes_section_booking_lftp']) or (isset($this->settings['fes_section_booking_lftp']) and $this->settings['fes_section_booking_lftp'])))): ?>
                <div class="mec-form-row">
                    <h4 class="mec-title"><?php _e('Last Few Tickets Percentage', 'modern-events-calendar-lite'); ?></h4>
                    <div class="mec-form-row">
                        <label class="mec-col-4" for="mec_bookings_last_few_tickets_percentage_inherit">
                            <input type="hidden" name="mec[booking][last_few_tickets_percentage_inherit]" value="0"/>
                            <input id="mec_bookings_last_few_tickets_percentage_inherit"
                                <?php
                                if ($bookings_last_few_tickets_percentage_inherite == 1) {
                                    echo 'checked="checked"';
                                }
                                ?>
                                   type="checkbox" value="1" name="mec[booking][last_few_tickets_percentage_inherit]" onchange="jQuery(this).parent().parent().find('input[type=number]').toggle();"/>
                            <?php _e('Inherit from global options', 'modern-events-calendar-lite'); ?>
                        </label>
                        <input class="mec-col-4" <?php echo ($bookings_last_few_tickets_percentage_inherite == 1) ? 'style="display: none;"' : ''; ?> type="number" min="1" max="100" step="1" name="mec[booking][last_few_tickets_percentage]" value="<?php echo esc_attr($bookings_last_few_tickets_percentage); ?>" placeholder="<?php _e('15', 'modern-events-calendar-lite'); ?>"/>
                    </div>
                </div>
                <?php endif; ?>

                <?php if(!$FES or ($FES and (!isset($this->settings['fes_section_booking_typ']) or (isset($this->settings['fes_section_booking_typ']) and $this->settings['fes_section_booking_typ'])))): ?>
                <div class="mec-form-row">
                    <h4 class="mec-title"><?php _e('Thank You Page', 'modern-events-calendar-lite'); ?></h4>
                    <div class="mec-form-row">
                        <label class="mec-col-4" for="mec_bookings_thankyou_page_inherit">
                            <input type="hidden" name="mec[booking][thankyou_page_inherit]" value="0"/>
                            <input id="mec_bookings_thankyou_page_inherit"
                                <?php
                                if ($bookings_thankyou_page_inherit == 1) {
                                    echo 'checked="checked"';
                                }
                                ?>
                                   type="checkbox" value="1" name="mec[booking][thankyou_page_inherit]" onchange="jQuery('#mec_booking_thankyou_page_options').toggle();"/>
                            <?php _e('Inherit from global options', 'modern-events-calendar-lite'); ?>
                        </label>
                    </div>
                    <div id="mec_booking_thankyou_page_options" <?php echo ($bookings_thankyou_page_inherit == 1) ? 'style="display: none;"' : ''; ?>>
                        <br>
                        <div class="mec-form-row">
                            <label class="mec-col-3" for="mec_bookings_booking_thankyou_page"><?php _e('Thank You Page', 'modern-events-calendar-lite'); ?></label>
                            <div class="mec-col-9">
                                <select id="mec_bookings_booking_thankyou_page" name="mec[booking][booking_thankyou_page]">
                                    <option value="">----</option>
                                    <?php foreach($pages as $page): ?>
                                        <option <?php echo ((isset($booking_options['booking_thankyou_page']) and $booking_options['booking_thankyou_page'] == $page->ID) ? 'selected="selected"' : ''); ?> value="<?php echo $page->ID; ?>"><?php echo $page->post_title; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <span class="mec-tooltip">
                                    <div class="box left">
                                        <h5 class="title"><?php _e('Thank You Page', 'modern-events-calendar-lite'); ?></h5>
                                        <div class="content"><p><?php esc_attr_e("User redirects to this page after booking. Leave it empty if you want to disable it.", 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/booking/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>
                                    </div>
                                    <i title="" class="dashicons-before dashicons-editor-help"></i>
                                </span>
                            </div>
                        </div>
                        <div class="mec-form-row">
                            <label class="mec-col-3" for="mec_bookings_booking_thankyou_page_time"><?php _e('Thank You Page Time Interval', 'modern-events-calendar-lite'); ?></label>
                            <div class="mec-col-9">
                                <input type="number" id="mec_bookings_booking_thankyou_page_time" name="mec[booking][booking_thankyou_page_time]" value="<?php echo ((isset($booking_options['booking_thankyou_page_time']) and trim($booking_options['booking_thankyou_page_time']) != '0') ? $booking_options['booking_thankyou_page_time'] : '2000'); ?>" placeholder="<?php esc_attr_e('2000 mean 2 seconds', 'modern-events-calendar-lite'); ?>" />
                                <span class="mec-tooltip">
                                    <div class="box left">
                                        <h5 class="title"><?php _e('Thank You Page Time Interval', 'modern-events-calendar-lite'); ?></h5>
                                        <div class="content"><p><?php esc_attr_e("Waiting time before redirecting to thank you page. It's in miliseconds so 2000 means 2 seconds.", 'modern-events-calendar-lite'); ?><a href="https://webnus.net/dox/modern-events-calendar/booking/" target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>
                                    </div>
                                    <i title="" class="dashicons-before dashicons-editor-help"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

            </div>

            <?php if(!$FES or ($FES and (!isset($this->settings['fes_section_booking_tubl']) or (isset($this->settings['fes_section_booking_tubl']) and $this->settings['fes_section_booking_tubl'])))): ?>
            <div class="mec-meta-box-fields mec-booking-tab-content" id="mec_meta_box_booking_options_form_2">
                <h4 class="mec-title"><label for="mec_bookings_user_limit"><?php _e('Total User Booking Limits', 'modern-events-calendar-lite'); ?></label></h4>
                <div class="mec-form-row">
                    <label class="mec-col-4" for="mec_bookings_user_limit_unlimited" id="mec_bookings_user_limit_unlimited_label">
                        <input type="hidden" name="mec[booking][bookings_user_limit_unlimited]" value="0"/>
                        <input id="mec_bookings_user_limit_unlimited"
                            <?php
                            if ($bookings_user_limit_unlimited == 1) {
                                echo 'checked="checked"';
                            }
                            ?>
                                type="checkbox" value="1" name="mec[booking][bookings_user_limit_unlimited]" onchange="jQuery(this).parent().parent().find('input[type=text]').toggle().val('');"/>
                        <?php _e('Inherit from global options', 'modern-events-calendar-lite'); ?>
                    </label>
                    <input class="mec-col-4" <?php echo ($bookings_user_limit_unlimited == 1) ? 'style="display: none;"' : ''; ?> type="text" name="mec[booking][bookings_user_limit]" id="mec_bookings_user_limit"
                            value="<?php echo esc_attr($bookings_user_limit); ?>" placeholder="<?php _e('12', 'modern-events-calendar-lite'); ?>"/>
                </div>
            </div>
            <?php endif; ?>

            <?php if(isset($gateway_settings['gateways_per_event']) and $gateway_settings['gateways_per_event']): ?>
            <div class="mec-meta-box-fields mec-booking-tab-content" id="mec_meta_box_booking_options_form_gateways_per_event">
                <h4 class="mec-title"><?php _e('Disabled Gateways', 'modern-events-calendar-lite'); ?></h4>
                <p class="description"><?php esc_html_e("You can disable some of the following payment gateways by checking them otherwise they will be enabled.", 'modern-events-calendar-lite'); ?></p>

                <?php foreach($enableds_gateways as $g): ?>
                <div class="mec-form-row" style="margin-bottom: 0;">
                    <label class="mec-col-4">
                        <input type="hidden" name="mec[booking][gateways_<?php echo $g->id(); ?>_disabled]" value="0"/>
                        <input type="checkbox" value="1" name="mec[booking][gateways_<?php echo $g->id(); ?>_disabled]" <?php echo (isset($booking_options['gateways_'.$g->id().'_disabled']) and $booking_options['gateways_'.$g->id().'_disabled']) ? 'checked="checked"' : ''; ?> />
                        <?php echo $g->title(); ?>
                    </label>
                </div>
                <?php endforeach; ?>

            </div>
            <?php endif; ?>
        </div>
        <?php
    }

    /**
     * Show tickets options of event into the Add/Edit event page
     *
     * @author Webnus <info@webnus.biz>
     * @param object $post
     */
    public function meta_box_tickets($post)
    {
        $tickets = get_post_meta($post->ID, 'mec_tickets', true);

        // This date format used for datepicker
        $datepicker_format = (isset($this->settings['datepicker_format']) and trim($this->settings['datepicker_format'])) ? $this->settings['datepicker_format'] : 'Y-m-d';

        // Private Description
        $private_description_status = (!isset($this->settings['booking_private_description']) or (isset($this->settings['booking_private_description']) and $this->settings['booking_private_description'])) ? true : false;
        if(is_admin()) $private_description_status = true;

        // Variations Per Ticket
        $variations_per_ticket_status = (isset($this->settings['ticket_variations_per_ticket']) and $this->settings['ticket_variations_per_ticket']) ? true : false;
        if(isset($this->settings['ticket_variations_status']) and !$this->settings['ticket_variations_status']) $variations_per_ticket_status = false;

        // Ticket Times Status
        $ticket_times_status = (isset($this->settings['disable_ticket_times']) and $this->settings['disable_ticket_times']) ? false : true;

        if(!is_array($tickets)) $tickets = array();
        ?>
        <div class="mec-meta-box-fields mec-booking-tab-content" id="mec-tickets">
            <h4 class="mec-meta-box-header"><?php echo $this->main->m('tickets', __('Tickets', 'modern-events-calendar-lite')); ?></h4>

            <?php if($post->ID != $this->main->get_original_event($post->ID)): ?>
            <p class="warning-msg"><?php _e("You're translating an event so MEC will use the original event for tickets and booking. You can only translate the ticket name and description. Please define exact tickets that you defined in the original event here.", 'modern-events-calendar-lite'); ?></p>
            <?php endif; ?>

            <div id="mec_meta_box_tickets_form">
                <div class="mec-form-row">
                    <button class="button" type="button" id="mec_add_ticket_button"><?php _e('Add Ticket', 'modern-events-calendar-lite'); ?></button>
                </div>
                <div id="mec_tickets">
                    <?php
                    $i = 0;
                    $tvi = 100;
                    foreach($tickets as $key => $ticket)
                    {
                        if(!is_numeric($key)) continue;
                        $i = max($i, $key);
                        ?>
                        <div class="mec-box" id="mec_ticket_row<?php echo $key; ?>">
                            <div class="mec-ticket-id" title="<?php esc_attr_e('Ticket ID', 'modern-events-calendar-lite'); ?>"><span class="mec-ticket-id-title"><?php esc_attr_e('ID', 'modern-events-calendar-lite'); ?>: </span><?php echo $key; ?></div>
                            <div class="mec-form-row">
                                <input type="text" class="mec-col-12" name="mec[tickets][<?php echo $key; ?>][name]"
                                       placeholder="<?php esc_attr_e('Ticket Name', 'modern-events-calendar-lite'); ?>"
                                       value="<?php echo(isset($ticket['name']) ? esc_attr($ticket['name']) : ''); ?>"/>
                            </div>

                            <?php do_action( 'mec_ticket_properties', $key, $ticket, $post->ID ); ?>

                            <?php if($ticket_times_status): ?>
                            <div class="mec-form-row wn-ticket-time">
                                <div class="mec-ticket-start-time mec-col-12">
                                    <span class="mec-ticket-time"><?php esc_html_e('Start Time', 'modern-events-calendar-lite'); ?></span>
                                    <?php $this->main->timepicker(array(
                                        'method' => (isset($this->settings['time_format']) ? $this->settings['time_format'] : 12),
                                        'time_hour' => (isset($ticket['ticket_start_time_hour']) ? $ticket['ticket_start_time_hour'] : 8),
                                        'time_minutes' => (isset($ticket['ticket_start_time_minute']) ? $ticket['ticket_start_time_minute'] : 0),
                                        'time_ampm' => (isset($ticket['ticket_start_time_ampm']) ? $ticket['ticket_start_time_ampm'] : 'AM'),
                                        'name' => 'mec[tickets]['.$key.']',
                                        'hour_key' => 'ticket_start_time_hour',
                                        'minutes_key' => 'ticket_start_time_minute',
                                        'ampm_key' => 'ticket_start_time_ampm',
                                    )); ?>
                                </div>
                                <div class="mec-ticket-end-time mec-ticket-start-time mec-col-12">
                                    <span class="mec-ticket-time"><?php esc_html_e('End Time', 'modern-events-calendar-lite'); ?></span>
                                    <?php $this->main->timepicker(array(
                                        'method' => (isset($this->settings['time_format']) ? $this->settings['time_format'] : 12),
                                        'time_hour' => (isset($ticket['ticket_end_time_hour']) ? $ticket['ticket_end_time_hour'] : 6),
                                        'time_minutes' => (isset($ticket['ticket_end_time_minute']) ? $ticket['ticket_end_time_minute'] : 0),
                                        'time_ampm' => (isset($ticket['ticket_end_time_ampm']) ? $ticket['ticket_end_time_ampm'] : 'PM'),
                                        'name' => 'mec[tickets]['.$key.']',
                                        'hour_key' => 'ticket_end_time_hour',
                                        'minutes_key' => 'ticket_end_time_minute',
                                        'ampm_key' => 'ticket_end_time_ampm',
                                    )); ?>
                                </div>
                            </div>
                            <?php endif; ?>

                            <div class="mec-form-row">
                                <textarea type="text" class="mec-col-12"
                                       name="mec[tickets][<?php echo $key; ?>][description]"
                                       placeholder="<?php esc_attr_e('Description', 'modern-events-calendar-lite'); ?>"><?php echo(isset($ticket['description']) ? esc_textarea($ticket['description']) : ''); ?></textarea>
                            </div>
                            <?php if($private_description_status): ?>
                            <div class="mec-form-row">
                                <textarea type="text" class="mec-col-12"
                                          name="mec[tickets][<?php echo $key; ?>][private_description]"
                                          placeholder="<?php esc_attr_e('Private Description', 'modern-events-calendar-lite'); ?>"><?php echo(isset($ticket['private_description']) ? esc_textarea($ticket['private_description']) : ''); ?></textarea>
                                <span class="mec-tooltip">
                                    <div class="box top">
                                        <h5 class="title"><?php _e('Private Description', 'modern-events-calendar-lite'); ?></h5>
                                        <div class="content"><p><?php esc_attr_e("You can show it on the email notifications by placing %%ticket_private_description%% into the email template.", 'modern-events-calendar-lite'); ?></p></div>
                                    </div>
                                    <i title="" class="dashicons-before dashicons-editor-help"></i>
                                </span>
                            </div>
                            <?php endif; ?>
                            <div class="mec-form-row">
							<span class="mec-col-4">
								<input type="number" min="0" step="0.01" name="mec[tickets][<?php echo $key; ?>][price]"
                                       placeholder="<?php esc_attr_e('Price', 'modern-events-calendar-lite'); ?>"
                                       value="<?php echo(isset($ticket['price']) ? esc_attr($ticket['price']) : ''); ?>"/>
								<span class="mec-tooltip">
									<div class="box top">
										<h5 class="title"><?php _e('Price', 'modern-events-calendar-lite'); ?></h5>
										<div class="content"><p><?php esc_attr_e('Insert 0 for free ticket. Only numbers please — Enter only the price without any symbols or characters.', 'modern-events-calendar-lite'); ?>
                                                <a href="https://webnus.net/dox/modern-events-calendar/add-a-booking-system/"
                                                   target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>
									</div>
									<i title="" class="dashicons-before dashicons-editor-help"></i>
								</span>
							</span>
                                <span class="mec-col-8">
								<input type="text" name="mec[tickets][<?php echo $key; ?>][price_label]"
                                       placeholder="<?php esc_attr_e('Price Label', 'modern-events-calendar-lite'); ?>"
                                       value="<?php echo(isset($ticket['price_label']) ? esc_attr($ticket['price_label']) : ''); ?>"
                                       class="mec-col-12"/>
								<span class="mec-tooltip">
									<div class="box top">
										<h5 class="title"><?php _e('Price Label', 'modern-events-calendar-lite'); ?></h5>
										<div class="content"><p><?php esc_attr_e('For showing on website. e.g. $15', 'modern-events-calendar-lite'); ?>
                                                <a href="https://webnus.net/dox/modern-events-calendar/add-a-booking-system/"
                                                   target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>
									</div>
									<i title="" class="dashicons-before dashicons-editor-help"></i>
								</span>
							</span>
                            </div>
                            <div class="mec-form-row">
                                <div class="mec-col-10">
                                    <input class="mec-col-4 mec-available-tickets" type="text" name="mec[tickets][<?php echo $key; ?>][limit]"
                                        placeholder="<?php esc_attr_e('Available Tickets', 'modern-events-calendar-lite'); ?>"
                                        value="<?php echo(isset($ticket['limit']) ? esc_attr($ticket['limit']) : '100'); ?>"/>
                                    <label class="mec-col-4" for="mec_tickets_unlimited_<?php echo $key; ?>"
                                        id="mec_bookings_limit_unlimited_label<?php echo $key; ?>">
                                        <input type="hidden" name="mec[tickets][<?php echo $key; ?>][unlimited]" value="0"/>
                                        <input id="mec_tickets_unlimited_<?php echo $key; ?>" type="checkbox" value="1"
                                            name="mec[tickets][<?php echo $key; ?>][unlimited]"
                                            <?php
                                            if (isset($ticket['unlimited']) and $ticket['unlimited']) {
                                                echo 'checked="checked"';
                                            }
                                            ?>
                                        />
                                        <?php _e('Unlimited', 'modern-events-calendar-lite'); ?>
                                    </label>
                                </div>
                            </div>
                            <div class="mec-form-row">
                                <div class="mec-col-4">
                                    <input type="text" name="mec[tickets][<?php echo $key; ?>][minimum_ticket]" value="<?php echo(isset($ticket['minimum_ticket']) ? esc_attr($ticket['minimum_ticket']) : '0'); ?>" placeholder="<?php _e('Minimum Ticket e.g. 3', 'modern-events-calendar-lite'); ?>">
                                    <span class="mec-tooltip">
                                        <div class="box top">
                                            <h5 class="title"><?php _e('MinimumTicket', 'modern-events-calendar-lite'); ?></h5>
                                            <div class="content">
                                                <p><?php esc_attr_e('Set a number for the minimum ticket reservation possible', 'modern-events-calendar-lite'); ?></p>
                                            </div>
                                        </div>
                                        <i title="" class="dashicons-before dashicons-editor-help"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="mec-form-row">
                                <?php ob_start(); ?>
                                <input type="number" class="mec-stop-selling-tickets" name="mec[tickets][<?php echo $key; ?>][stop_selling_value]" value="<?php echo((isset($ticket['stop_selling_value']) and trim($ticket['stop_selling_value'])) ? esc_attr($ticket['stop_selling_value']) : '0'); ?>" placeholder="<?php _e('e.g. 0', 'modern-events-calendar-lite'); ?>">
                                <select name="mec[tickets][<?php echo $key; ?>][stop_selling_type]">
                                    <option value="day" <?php echo(isset($ticket['stop_selling_type']) and trim($ticket['stop_selling_type']) == 'day') ? 'selected="selected"' : ''; ?>><?php _e("Day", "limitmec"); ?></option>
                                    <option value="hour" <?php echo(isset($ticket['stop_selling_type']) and trim($ticket['stop_selling_type']) == 'hour') ? 'selected="selected"' : ''; ?>><?php _e("Hour", "mec"); ?></option>
                                </select>
                                <?php echo sprintf(__('Stop selling ticket %s before event start.', 'modern-events-calendar-lite'), ob_get_clean()); ?>
                                <button class="button remove" type="button"
                                        onclick="mec_ticket_remove(<?php echo $key; ?>);"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" focusable="false" width="1em" height="1em" style="-ms-transform: rotate(360deg); -webkit-transform: rotate(360deg); transform: rotate(360deg);" preserveAspectRatio="xMidYMid meet" viewBox="0 0 20 20"><path d="M14.95 6.46L11.41 10l3.54 3.54l-1.41 1.41L10 11.42l-3.53 3.53l-1.42-1.42L8.58 10L5.05 6.47l1.42-1.42L10 8.58l3.54-3.53z"/></svg></button>
                            </div>
                            <?php do_action('custom_field_ticket', $ticket, $key); ?>
                            <div id="mec_price_per_dates_container">
                                <div class="mec-form-row">
                                    <h4><?php _e('Price per Date', 'modern-events-calendar-lite'); ?></h4>
                                    <button class="button mec_add_price_date_button" type="button"
                                            data-key="<?php echo $key; ?>"><?php _e('Add', 'modern-events-calendar-lite'); ?></button>
                                </div>
                                <div id="mec-ticket-price-dates-<?php echo $key; ?>">
                                    <?php $j = 0; if(isset($ticket['dates']) and count($ticket['dates'])) : ?>
                                        <?php
                                        foreach ($ticket['dates'] as $p => $price_date) :
                                            if (!is_numeric($p)) {
                                                continue;
                                            }
                                            $j = max($j, $p);
                                            ?>
                                            <div id="mec_ticket_price_raw_<?php echo $key; ?>_<?php echo $p; ?>">
                                                <div class="mec-form-row">
                                                    <input class="mec-col-3 mec_date_picker_dynamic_format" type="text"
                                                           name="mec[tickets][<?php echo $key; ?>][dates][<?php echo $p; ?>][start]"
                                                           value="<?php echo isset($price_date['start']) ? $this->main->standardize_format($price_date['start'], $datepicker_format) : $this->main->standardize_format(date('Y-m-d'), $datepicker_format); ?>"
                                                           placeholder="<?php esc_attr_e('Start', 'modern-events-calendar-lite'); ?>"/>
                                                    <input class="mec-col-3 mec_date_picker_dynamic_format" type="text"
                                                           name="mec[tickets][<?php echo $key; ?>][dates][<?php echo $p; ?>][end]"
                                                           value="<?php echo isset($price_date['end']) ? $this->main->standardize_format($price_date['end'], $datepicker_format) : $this->main->standardize_format(date('Y-m-d', strtotime( '+10 days')), $datepicker_format); ?>"
                                                           placeholder="<?php esc_attr_e('End', 'modern-events-calendar-lite'); ?>"/>
                                                    <input class="mec-col-3" type="number"
                                                           name="mec[tickets][<?php echo $key; ?>][dates][<?php echo $p; ?>][price]"
                                                           value="<?php echo isset($price_date['price']) ? $price_date['price'] : ''; ?>"
                                                           placeholder="<?php esc_attr_e('Price', 'modern-events-calendar-lite'); ?>" min="0" step="0.01"/>
                                                    <input class="mec-col-2" type="text"
                                                           name="mec[tickets][<?php echo $key; ?>][dates][<?php echo $p; ?>][label]"
                                                           value="<?php echo isset($price_date['label']) ? $price_date['label'] : ''; ?>"
                                                           placeholder="<?php esc_attr_e('Label', 'modern-events-calendar-lite'); ?>"/>
                                                    <button class="button mec-col-1" type="button"
                                                            onclick="mec_ticket_price_remove(<?php echo $key; ?>, <?php echo $p; ?>)"><?php _e('Remove', 'modern-events-calendar-lite'); ?></button>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                                <input type="hidden" id="mec_new_ticket_price_key_<?php echo $key; ?>"
                                       value="<?php echo $j + 1; ?>"/>
                                <div class="mec-util-hidden" id="mec_new_ticket_price_raw_<?php echo $key; ?>">
                                    <div id="mec_ticket_price_raw_<?php echo $key; ?>_:j:">
                                        <div class="mec-form-row">
                                            <input class="mec-col-3 new_added" type="text"
                                                   name="mec[tickets][<?php echo $key; ?>][dates][:j:][start]"
                                                   value="<?php echo $this->main->standardize_format( date( 'Y-m-d' ), $datepicker_format ); ?>"
                                                   placeholder="<?php esc_attr_e('Start', 'modern-events-calendar-lite'); ?>"/>
                                            <input class="mec-col-3 new_added" type="text"
                                                   name="mec[tickets][<?php echo $key; ?>][dates][:j:][end]"
                                                   value="<?php echo $this->main->standardize_format( date( 'Y-m-d', strtotime( '+10 days' ) ), $datepicker_format ); ?>"
                                                   placeholder="<?php esc_attr_e('End', 'modern-events-calendar-lite'); ?>"/>
                                            <input class="mec-col-3" type="number"
                                                   name="mec[tickets][<?php echo $key; ?>][dates][:j:][price]"
                                                   placeholder="<?php esc_attr_e('Price', 'modern-events-calendar-lite'); ?>" min="0" step="0.01"/>
                                            <input class="mec-col-2" type="text"
                                                   name="mec[tickets][<?php echo $key; ?>][dates][:j:][label]"
                                                   placeholder="<?php esc_attr_e('Label', 'modern-events-calendar-lite'); ?>"/>
                                            <button class="button mec-col-1" type="button"
                                                    onclick="mec_ticket_price_remove(<?php echo $key; ?>, :j:)"><?php _e('Remove', 'modern-events-calendar-lite'); ?></button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <?php if($variations_per_ticket_status): ?>
                            <?php
                                $event_inheritance = (isset($ticket['variations_event_inheritance']) ? $ticket['variations_event_inheritance'] : 1);
                                if(trim($event_inheritance) == '') $event_inheritance = 1;

                                // Ticket Variations Object
                                $TicketVariations = $this->getTicketVariations();
                            ?>
                            <div id="mec_variations_per_ticket_container">
                                <div class="mec-form-row">
                                    <h4><?php _e('Variations Per Ticket', 'modern-events-calendar-lite'); ?></h4>
                                    <div id="mec_variations_per_ticket_form<?php echo $key; ?>">
                                        <div class="mec-form-row">
                                            <label>
                                                <input type="hidden" name="mec[tickets][<?php echo $key; ?>][variations_event_inheritance]" value="0"/>
                                                <input onchange="jQuery('#mec_variations_per_ticket_container_toggle<?php echo $key; ?>').toggle();" value="1" type="checkbox" name="mec[tickets][<?php echo $key; ?>][variations_event_inheritance]" <?php echo ($event_inheritance ? 'checked="checked"' : ''); ?>> <?php _e('Inherit from event options', 'modern-events-calendar-lite'); ?>
                                            </label>
                                        </div>
                                        <div id="mec_variations_per_ticket_container_toggle<?php echo $key; ?>" class="<?php echo ($event_inheritance ? 'mec-util-hidden' : ''); ?>">
                                            <div class="mec-form-row">
                                                <button class="button" type="button" id="mec_add_variation_per_ticket_button<?php echo $key; ?>" onclick="add_variation_per_ticket(<?php echo $key; ?>);"><?php _e('Add', 'modern-events-calendar-lite'); ?></button>
                                            </div>
                                            <div id="mec_ticket_variations_list<?php echo $key; ?>">
                                                <?php
                                                $ticket_variations = ((isset($ticket['variations']) and is_array($ticket['variations'])) ? $ticket['variations'] : array());
                                                foreach($ticket_variations as $tvk => $ticket_variation)
                                                {
                                                    if(!is_numeric($tvk)) continue;

                                                    $tvi = max($tvi, $tvk);
                                                    $TicketVariations->item(array(
                                                        'name_prefix' => 'mec[tickets]['.$key.'][variations]',
                                                        'id_prefix' => 'variation_per_ticket'.$key,
                                                        'i' => $tvi,
                                                        'value' => $ticket_variation,
                                                    ));
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mec-util-hidden" id="mec_new_variation_per_ticket_raw<?php echo $key; ?>">
                                        <?php
                                        $TicketVariations->item(array(
                                            'name_prefix' => 'mec[tickets]['.$key.'][variations]',
                                            'id_prefix' => 'variation_per_ticket'.$key,
                                            'i' => ':v:',
                                            'value' => array(),
                                        ));
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    <?php
                    }
                    ?>
                </div>
                <input type="hidden" id="mec_new_variation_per_ticket_key" value="<?php echo $tvi + 1; ?>"/>
            </div>
            <input type="hidden" id="mec_new_ticket_key" value="<?php echo $i + 1; ?>"/>
            <div class="mec-util-hidden" id="mec_new_ticket_raw">
                <div class="mec-box" id="mec_ticket_row:i:">
                    <div class="mec-ticket-id" title="<?php esc_attr_e('Ticket ID', 'modern-events-calendar-lite'); ?>"><span class="mec-ticket-id-title"><?php esc_attr_e('ID', 'modern-events-calendar-lite'); ?>: </span>:i:</div>
                    <div class="mec-form-row">
                        <input class="mec-col-12" type="text" name="mec[tickets][:i:][name]"
                               placeholder="<?php esc_attr_e('Ticket Name', 'modern-events-calendar-lite'); ?>"/>
                    </div>
                    <?php do_action('mec_ticket_properties', ':i:', [], $post->ID ); ?>
                    <div class="mec-form-row wn-ticket-time">
                        <div class="mec-ticket-start-time mec-col-12">
                            <span class="mec-ticket-time"><?php esc_html_e('Start Time', 'modern-events-calendar-lite'); ?></span>
                            <?php $this->main->timepicker(array(
                                'method' => (isset($this->settings['time_format']) ? $this->settings['time_format'] : 12),
                                'time_hour' => 8,
                                'time_minutes' => 0,
                                'time_ampm' => 'AM',
                                'name' => 'mec[tickets][:i:]',
                                'hour_key' => 'ticket_start_time_hour',
                                'minutes_key' => 'ticket_start_time_minute',
                                'ampm_key' => 'ticket_start_time_ampm',
                            )); ?>
                        </div>
                        <div class="mec-ticket-end-time mec-ticket-start-time mec-col-12">
                            <span class="mec-ticket-time"><?php esc_html_e('End Time', 'modern-events-calendar-lite'); ?></span>
                            <?php $this->main->timepicker(array(
                                'method' => (isset($this->settings['time_format']) ? $this->settings['time_format'] : 12),
                                'time_hour' => 6,
                                'time_minutes' => 0,
                                'time_ampm' => 'PM',
                                'name' => 'mec[tickets][:i:]',
                                'hour_key' => 'ticket_end_time_hour',
                                'minutes_key' => 'ticket_end_time_minute',
                                'ampm_key' => 'ticket_end_time_ampm',
                            )); ?>
                        </div>
                    </div>
                    <div class="mec-form-row">
                        <textarea class="mec-col-12" type="text" name="mec[tickets][:i:][description]"
                               placeholder="<?php esc_attr_e('Description', 'modern-events-calendar-lite'); ?>"></textarea>
                    </div>
                    <?php if($private_description_status): ?>
                    <div class="mec-form-row">
                        <textarea type="text" class="mec-col-12" name="mec[tickets][:i:][private_description]"
                                  placeholder="<?php esc_attr_e('Private Description', 'modern-events-calendar-lite'); ?>"></textarea>
                        <span class="mec-tooltip">
                            <div class="box top">
                                <h5 class="title"><?php _e('Private Description', 'modern-events-calendar-lite'); ?></h5>
                                <div class="content"><p><?php esc_attr_e("You can show it on the email notifications by placing %%ticket_private_description%% into the email template.", 'modern-events-calendar-lite'); ?></p></div>
                            </div>
                            <i title="" class="dashicons-before dashicons-editor-help"></i>
                        </span>
                    </div>
                    <?php endif; ?>
                    <div class="mec-form-row">
						<span class="mec-col-4">
							<input type="number" min="0" step="0.01" name="mec[tickets][:i:][price]"
                                   placeholder="<?php esc_attr_e('Price', 'modern-events-calendar-lite'); ?>"/>
							<span class="mec-tooltip">
								<div class="box top">
									<h5 class="title"><?php _e('Price', 'modern-events-calendar-lite'); ?></h5>
									<div class="content"><p><?php esc_attr_e('Insert 0 for free ticket. Only numbers please — Enter only the price without any symbols or characters.', 'modern-events-calendar-lite'); ?>
                                            <a href="https://webnus.net/dox/modern-events-calendar/add-a-booking-system/"
                                               target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>
								</div>
								<i title="" class="dashicons-before dashicons-editor-help"></i>
							</span>
						</span>
                        <span class="mec-col-8">
							<input type="text" name="mec[tickets][:i:][price_label]"
                                   placeholder="<?php esc_attr_e('Price Label', 'modern-events-calendar-lite'); ?>" class="mec-col-12"/>
							<span class="mec-tooltip">
								<div class="box top">
									<h5 class="title"><?php _e('Price Label', 'modern-events-calendar-lite'); ?></h5>
									<div class="content"><p><?php esc_attr_e('For showing on website. e.g. $15', 'modern-events-calendar-lite'); ?>
                                            <a href="https://webnus.net/dox/modern-events-calendar/add-a-booking-system/"
                                               target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>
								</div>
								<i title="" class="dashicons-before dashicons-editor-help"></i>
							</span>
						</span>
                    </div>
                    <div class="mec-form-row">
                        <div class="mec-col-10">
                            <input class="mec-col-4 mec-available-tickets" type="text" name="mec[tickets][:i:][limit]"
                                placeholder="<?php esc_attr_e('Available Tickets', 'modern-events-calendar-lite'); ?>"/>
                            <label class="mec-col-4" for="mec_tickets_unlimited_:i:"
                                id="mec_bookings_limit_unlimited_label">
                                <input type="hidden" name="mec[tickets][:i:][unlimited]" value="0"/>
                                <input id="mec_tickets_unlimited_:i:" type="checkbox" value="1"
                                    name="mec[tickets][:i:][unlimited]"/>
                                <?php _e('Unlimited', 'modern-events-calendar-lite'); ?>
                            </label>
                        </div>
                    </div>
                    <div class="mec-form-row">
                        <div class="mec-col-4">
                            <input type="text" name="mec[tickets][:i:][minimum_ticket]" value="1" placeholder="<?php _e('Minimum Ticket e.g. 3', 'modern-events-calendar-lite'); ?>">
                            <span class="mec-tooltip">
                                <div class="box top">
                                    <h5 class="title"><?php _e('MinimumTicket', 'modern-events-calendar-lite'); ?></h5>
                                    <div class="content">
                                        <p><?php esc_attr_e('Set a number for the minimum ticket reservation possible', 'modern-events-calendar-lite'); ?></p>
                                    </div>
                                </div>
                                <i title="" class="dashicons-before dashicons-editor-help"></i>
                            </span>
                        </div>
                    </div>
                    <div class="mec-form-row">
                        <?php ob_start(); ?>
                        <input type="number" class="mec-stop-selling-tickets" name="mec[tickets][:i:][stop_selling_value]" value="0" placeholder="<?php _e('e.g. 0', 'modern-events-calendar-lite'); ?>">
                        <select name="mec[tickets][:i:][stop_selling_type]">
                            <option value="day"><?php _e("Day", "mec"); ?></option>
                            <option value="hour"><?php _e("Hour", "mec"); ?></option>
                        </select>
                        <?php echo sprintf(__('Stop selling ticket %s before event start.', 'modern-events-calendar-lite'), ob_get_clean()); ?>
                        <button class="button remove" type="button"
                                onclick="mec_ticket_remove(:i:)"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" focusable="false" width="1em" height="1em" style="-ms-transform: rotate(360deg); -webkit-transform: rotate(360deg); transform: rotate(360deg);" preserveAspectRatio="xMidYMid meet" viewBox="0 0 20 20"><path d="M14.95 6.46L11.41 10l3.54 3.54l-1.41 1.41L10 11.42l-3.53 3.53l-1.42-1.42L8.58 10L5.05 6.47l1.42-1.42L10 8.58l3.54-3.53z"/></svg></button>
                    </div>
                    <?php do_action('custom_field_dynamic_ticket'); ?>
                    <div id="mec_price_per_dates_container_:i:">
                        <div class="mec-form-row">
                            <h4><?php _e('Price per Date', 'modern-events-calendar-lite'); ?></h4>
                            <button class="button mec_add_price_date_button" type="button"
                                    data-key=":i:"><?php _e('Add', 'modern-events-calendar-lite'); ?></button>
                        </div>
                        <div id="mec-ticket-price-dates-:i:">
                        </div>
                        <input type="hidden" id="mec_new_ticket_price_key_:i:" value="1"/>
                        <div class="mec-util-hidden" id="mec_new_ticket_price_raw_:i:">
                            <div id="mec_ticket_price_raw_:i:_:j:">
                                <div class="mec-form-row">
                                    <input class="mec-col-3 new_added" type="text"
                                           name="mec[tickets][:i:][dates][:j:][start]"
                                           value="<?php echo $this->main->standardize_format( date( 'Y-m-d' ), $datepicker_format ); ?>"
                                           placeholder="<?php esc_attr_e('Start', 'modern-events-calendar-lite'); ?>"/>
                                    <input class="mec-col-3 new_added" type="text"
                                           name="mec[tickets][:i:][dates][:j:][end]"
                                           value="<?php echo $this->main->standardize_format( date( 'Y-m-d', strtotime( '+10 days' ) ), $datepicker_format ); ?>"
                                           placeholder="<?php esc_attr_e('End', 'modern-events-calendar-lite'); ?>"/>
                                    <input class="mec-col-3" type="number" name="mec[tickets][:i:][dates][:j:][price]"
                                           placeholder="<?php esc_attr_e('Price', 'modern-events-calendar-lite'); ?>" min="0" step="0.01"/>
                                    <input class="mec-col-2" type="text" name="mec[tickets][:i:][dates][:j:][label]"
                                           placeholder="<?php esc_attr_e('Label', 'modern-events-calendar-lite'); ?>"/>
                                    <button class="button mec-col-1" type="button"
                                            onclick="mec_ticket_price_remove(:i:, :j:)"><?php _e('Remove', 'modern-events-calendar-lite'); ?></button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if($variations_per_ticket_status): ?>
                    <?php
                        // Ticket Variations Object
                        $TicketVariations = $this->getTicketVariations();
                    ?>
                    <div id="mec_variations_per_ticket_container">
                        <div class="mec-form-row">
                            <h4><?php _e('Variations Per Ticket', 'modern-events-calendar-lite'); ?></h4>
                            <div id="mec_variations_per_ticket_form:i:">
                                <div class="mec-form-row">
                                    <label>
                                        <input type="hidden" name="mec[tickets][:i:][variations_event_inheritance]" value="0"/>
                                        <input onchange="jQuery('#mec_variations_per_ticket_container_toggle:i:').toggle();" value="1" type="checkbox" name="mec[tickets][:i:][variations_event_inheritance]" checked="checked"> <?php _e('Inherit from event options', 'modern-events-calendar-lite'); ?>
                                    </label>
                                </div>
                                <div id="mec_variations_per_ticket_container_toggle:i:" class="mec-util-hidden">
                                    <div class="mec-form-row">
                                        <button class="button" type="button" id="mec_add_variation_per_ticket_button:i:" onclick="add_variation_per_ticket(:i:);"><?php _e('Add', 'modern-events-calendar-lite'); ?></button>
                                    </div>
                                    <div id="mec_ticket_variations_list:i:"></div>
                                </div>
                            </div>
                            <input type="hidden" id="mec_new_variation_per_ticket_key:i:" value="1"/>
                            <div class="mec-util-hidden" id="mec_new_variation_per_ticket_raw:i:">
                                <?php
                                $TicketVariations->item(array(
                                    'name_prefix' => 'mec[tickets][:i:][variations]',
                                    'id_prefix' => 'variation_per_ticket:i:',
                                    'i' => ':v:',
                                    'value' => array(),
                                ));
                                ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Show fees of event into the Add/Edit event page
     *
     * @author Webnus <info@webnus.biz>
     * @param object $post
     */
    public function meta_box_fees($post)
    {
        $global_inheritance = get_post_meta($post->ID, 'mec_fees_global_inheritance', true);
        if (trim($global_inheritance) == '') {
            $global_inheritance = 1;
        }

        $fees = get_post_meta($post->ID, 'mec_fees', true);

        $global_fees = isset($this->settings['fees']) ? $this->settings['fees'] : array();
        if (!is_array($fees) and trim($fees) == '') {
            $fees = $global_fees;
        }

        if (!is_array($fees)) {
            $fees = array();
        }
        ?>
        <div class="mec-meta-box-fields mec-booking-tab-content" id="mec-fees">
            <h4 class="mec-meta-box-header"><?php _e('Fees', 'modern-events-calendar-lite'); ?></h4>
            <div id="mec_meta_box_fees_form">
                <div class="mec-form-row">
                    <label>
                        <input type="hidden" name="mec[fees_global_inheritance]" value="0"/>
                        <input onchange="jQuery('#mec_taxes_fees_container_toggle').toggle();" value="1" type="checkbox"
                               name="mec[fees_global_inheritance]"
                            <?php
                            if ($global_inheritance) {
                                echo 'checked="checked"';
                            }
                            ?>
                        /> <?php _e('Inherit from global options', 'modern-events-calendar-lite'); ?>
                    </label>
                </div>
                <div id="mec_taxes_fees_container_toggle" class="
				<?php
                if ($global_inheritance) {
                    echo 'mec-util-hidden';
                }
                ?>
				">
                    <div class="mec-form-row">
                        <button class="button" type="button" id="mec_add_fee_button"><?php _e('Add', 'modern-events-calendar-lite'); ?></button>
                    </div>
                    <div id="mec_fees_list">
                        <?php
                        $i = 0;
                        foreach ($fees as $key => $fee) :
                            if (!is_numeric($key)) {
                                continue;
                            }
                            $i = max($i, $key);
                            ?>
                            <div class="mec-box" id="mec_fee_row<?php echo $i; ?>">
                                <div class="mec-form-row">
                                    <input class="mec-col-12" type="text" name="mec[fees][<?php echo $i; ?>][title]"
                                           placeholder="<?php esc_attr_e('Fee Title', 'modern-events-calendar-lite'); ?>"
                                           value="<?php echo(isset($fee['title']) ? esc_attr($fee['title']) : ''); ?>"/>
                                </div>
                                <div class="mec-form-row">
								<span class="mec-col-4">
									<input type="text" name="mec[fees][<?php echo $i; ?>][amount]"
                                           placeholder="<?php esc_attr_e('Amount', 'modern-events-calendar-lite'); ?>"
                                           value="<?php echo(isset($fee['amount']) ? esc_attr($fee['amount']) : ''); ?>"/>
									<span class="mec-tooltip">
										<div class="box top">
											<h5 class="title"><?php _e('Amount', 'modern-events-calendar-lite'); ?></h5>
											<div class="content"><p><?php esc_attr_e('Fee amount, considered as fixed amount if you set the type to amount otherwise considered as percentage', 'modern-events-calendar-lite'); ?>
                                                    <a href="https://webnus.net/dox/modern-events-calendar/tickets-and-taxes-fees/"
                                                       target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>
										</div>
										<i title="" class="dashicons-before dashicons-editor-help"></i>
									</span>
								</span>
                                    <span class="mec-col-4">
									<select name="mec[fees][<?php echo $i; ?>][type]">
										<option value="percent" <?php echo((isset($fee['type']) and $fee['type'] == 'percent') ? 'selected="selected"' : ''); ?>><?php _e('Percent', 'modern-events-calendar-lite'); ?></option>
										<option value="amount" <?php echo((isset($fee['type']) and $fee['type'] == 'amount') ? 'selected="selected"' : ''); ?>><?php _e('Amount (Per Ticket)', 'modern-events-calendar-lite'); ?></option>
										<option value="amount_per_booking" <?php echo((isset($fee['type']) and $fee['type'] == 'amount_per_booking') ? 'selected="selected"' : ''); ?>><?php _e('Amount (Per Booking)', 'modern-events-calendar-lite'); ?></option>
									</select>
								</span>
                                    <button class="button" type="button" id="mec_remove_fee_button<?php echo $i; ?>"
                                            onclick="mec_remove_fee(<?php echo $i; ?>);"><?php _e('Remove', 'modern-events-calendar-lite'); ?></button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <input type="hidden" id="mec_new_fee_key" value="<?php echo $i + 1; ?>"/>
            <div class="mec-util-hidden" id="mec_new_fee_raw">
                <div class="mec-box" id="mec_fee_row:i:">
                    <div class="mec-form-row">
                        <input class="mec-col-12" type="text" name="mec[fees][:i:][title]"
                               placeholder="<?php esc_attr_e('Fee Title', 'modern-events-calendar-lite'); ?>"/>
                    </div>
                    <div class="mec-form-row">
						<span class="mec-col-4">
							<input type="text" name="mec[fees][:i:][amount]"
                                   placeholder="<?php esc_attr_e('Amount', 'modern-events-calendar-lite'); ?>"/>
							<span class="mec-tooltip">
								<div class="box top">
									<h5 class="title"><?php _e('Amount', 'modern-events-calendar-lite'); ?></h5>
									<div class="content"><p><?php esc_attr_e('Fee amount, considered as fixed amount if you set the type to amount otherwise considered as percentage', 'modern-events-calendar-lite'); ?>
                                            <a href="https://webnus.net/dox/modern-events-calendar/tickets-and-taxes-fees/"
                                               target="_blank"><?php _e('Read More', 'modern-events-calendar-lite'); ?></a></p></div>
								</div>
								<i title="" class="dashicons-before dashicons-editor-help"></i>
							</span>
						</span>
                        <span class="mec-col-4">
							<select name="mec[fees][:i:][type]">
								<option value="percent"><?php _e('Percent', 'modern-events-calendar-lite'); ?></option>
								<option value="amount"><?php _e('Amount (Per Ticket)', 'modern-events-calendar-lite'); ?></option>
								<option value="amount_per_booking"><?php _e('Amount (Per Booking)', 'modern-events-calendar-lite'); ?></option>
							</select>
						</span>
                        <button class="button" type="button" id="mec_remove_fee_button:i:"
                                onclick="mec_remove_fee(:i:);"><?php _e('Remove', 'modern-events-calendar-lite'); ?></button>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Show ticket variations into the Add/Edit event page
     *
     * @author Webnus <info@webnus.biz>
     * @param object $post
     */
    public function meta_box_ticket_variations($post)
    {
        $global_inheritance = get_post_meta($post->ID, 'mec_ticket_variations_global_inheritance', true);
        if(trim($global_inheritance) == '') $global_inheritance = 1;

        $ticket_variations = get_post_meta($post->ID, 'mec_ticket_variations', true);
        $global_variations = isset($this->settings['ticket_variations']) ? $this->settings['ticket_variations'] : array();

        if(!is_array($ticket_variations) and trim($ticket_variations) == '') $ticket_variations = $global_variations;
        if(!is_array($ticket_variations)) $ticket_variations = array();

        // Ticket Variations Object
        $TicketVariations = $this->getTicketVariations();
        ?>
        <div class="mec-meta-box-fields mec-booking-tab-content" id="mec-ticket-variations">
            <h4 class="mec-meta-box-header"><?php _e('Ticket Variations / Options', 'modern-events-calendar-lite'); ?></h4>
            <div id="mec_meta_box_ticket_variations_form">
                <div class="mec-form-row">
                    <label>
                        <input type="hidden" name="mec[ticket_variations_global_inheritance]" value="0"/>
                        <input onchange="jQuery('#mec_taxes_ticket_variations_container_toggle').toggle();" value="1" type="checkbox" name="mec[ticket_variations_global_inheritance]" <?php echo ($global_inheritance ? 'checked="checked"' : ''); ?>> <?php _e('Inherit from global options', 'modern-events-calendar-lite'); ?>
                    </label>
                </div>
                <div id="mec_taxes_ticket_variations_container_toggle" class="<?php echo ($global_inheritance ? 'mec-util-hidden' : ''); ?>">
                    <div class="mec-form-row">
                        <button class="button" type="button" id="mec_add_ticket_variation_button"><?php _e('Add', 'modern-events-calendar-lite'); ?></button>
                    </div>
                    <div id="mec_ticket_variations_list">
                        <?php
                        $i = 0;
                        foreach($ticket_variations as $key => $ticket_variation)
                        {
                            if(!is_numeric($key)) continue;

                            $i = max($i, $key);
                            $TicketVariations->item(array(
                                'i' => $i,
                                'value' => $ticket_variation,
                            ));
                        }
                        ?>
                    </div>
                </div>
            </div>
            <input type="hidden" id="mec_new_ticket_variation_key" value="<?php echo $i + 1; ?>"/>
            <div class="mec-util-hidden" id="mec_new_ticket_variation_raw">
                <?php
                    $TicketVariations->item(array(
                        'i' => ':i:',
                        'value' => array(),
                    ));
                ?>
            </div>
        </div>
        <?php
    }

    /**
     * Show registration form of event into the Add/Edit event page
     *
     * @author Webnus <info@webnus.biz>
     * @param object $post
     */
    public function meta_box_regform($post)
    {
        do_action('mec_events_meta_box_regform_start', $post);

        $global_inheritance = get_post_meta($post->ID, 'mec_reg_fields_global_inheritance', true);
        if(trim($global_inheritance) == '') $global_inheritance = 1;

        $reg_fields = get_post_meta($post->ID, 'mec_reg_fields', true);
        $global_reg_fields = $this->main->get_reg_fields();

        if((is_array($reg_fields) and !count($reg_fields)) or (!is_array($reg_fields) and trim($reg_fields) == '')) $reg_fields = $global_reg_fields;
        if(!is_array($reg_fields)) $reg_fields = array();

        $bfixed_fields = get_post_meta($post->ID, 'mec_bfixed_fields', true);
        $global_bfixed_fields = $this->main->get_bfixed_fields();

        if((is_array($bfixed_fields) and !count($bfixed_fields)) or (!is_array($bfixed_fields) and trim($bfixed_fields) == '')) $bfixed_fields = $global_bfixed_fields;
        if(!is_array($bfixed_fields)) $bfixed_fields = array();

        $mec_name = false;
        $mec_email = false;

        foreach($reg_fields as $field)
        {
            if(isset($field['type']))
            {
                if($field['type'] == 'mec_email') $mec_email = true;
                if($field['type'] == 'name') $mec_name = true;
            }
            else break;
        }

        if(!$mec_name)
        {
            array_unshift($reg_fields, array(
                'mandatory' => '0',
                'type' => 'name',
                'label' => esc_html__('Name', 'modern-events-calendar-lite'),
            ));
        }

        if(!$mec_email)
        {
            array_unshift($reg_fields, array(
                'mandatory' => '0',
                'type' => 'mec_email',
                'label' => esc_html__('Email', 'modern-events-calendar-lite'),
            ));
        }
        ?>
        <div class="mec-meta-box-fields mec-booking-tab-content" id="mec-reg-fields">
            <h4 class="mec-meta-box-header"><?php _e('Booking Form', 'modern-events-calendar-lite'); ?></h4>

            <?php if($post->ID != $this->main->get_original_event($post->ID)) : ?>
            <p class="warning-msg"><?php _e("You're translating an event so MEC will use the original event for booking form. You can only translate the field name and options. Please define exact fields that you defined in the original event here.", 'modern-events-calendar-lite'); ?></p>
            <?php endif; ?>

            <div id="mec_meta_box_reg_fields_form">
                <div class="mec-form-row">
                    <label>
                        <input type="hidden" name="mec[reg_fields_global_inheritance]" value="0"/>
                        <input onchange="jQuery('#mec_regform_container_toggle').toggle();" value="1" type="checkbox"
                               name="mec[reg_fields_global_inheritance]"
                            <?php
                            if ($global_inheritance) {
                                echo 'checked="checked"';
                            }
                            ?>
                        /> <?php _e('Inherit from global options', 'modern-events-calendar-lite'); ?>
                    </label>
                </div>
                <?php do_action('mec_meta_box_reg_fields_form', $post->ID); ?>
                <div id="mec_regform_container_toggle" class="
				<?php
                if ($global_inheritance) {
                    echo 'mec-util-hidden';
                }
                ?>">

                    <div class="mec-booking-per-attendee-fields">
                        <h5 class="mec-form-subtitle"><?php _e('Per Attendee Fields', 'modern-events-calendar-lite'); ?></h5>
                        <?php /** Don't remove this hidden field **/ ?>
                        <input type="hidden" name="mec[reg_fields]" value=""/>

                        <ul id="mec_reg_form_fields">
                            <?php
                            $i = 0;
                            foreach($reg_fields as $key => $reg_field)
                            {
                                if(!is_numeric($key)) continue;

                                $i = max($i, $key);

                                if($reg_field['type'] == 'text') echo $this->main->field_text($key, $reg_field);
                                elseif($reg_field['type'] == 'mec_email') echo $this->main->field_mec_email($key, $reg_field);
                                elseif($reg_field['type'] == 'name') echo $this->main->field_name($key, $reg_field);
                                elseif($reg_field['type'] == 'email') echo $this->main->field_email($key, $reg_field);
                                elseif($reg_field['type'] == 'date') echo $this->main->field_date($key, $reg_field);
                                elseif($reg_field['type'] == 'file') echo $this->main->field_file($key, $reg_field);
                                elseif($reg_field['type'] == 'tel') echo $this->main->field_tel($key, $reg_field);
                                elseif($reg_field['type'] == 'textarea') echo $this->main->field_textarea($key, $reg_field);
                                elseif($reg_field['type'] == 'p') echo $this->main->field_p($key, $reg_field);
                                elseif($reg_field['type'] == 'checkbox') echo $this->main->field_checkbox($key, $reg_field);
                                elseif($reg_field['type'] == 'radio') echo $this->main->field_radio($key, $reg_field);
                                elseif($reg_field['type'] == 'select') echo $this->main->field_select($key, $reg_field);
                                elseif($reg_field['type'] == 'agreement') echo $this->main->field_agreement($key, $reg_field);
                            }
                            ?>
                        </ul>
                        <div id="mec_reg_form_field_types">
                            <button type="button" class="button red" data-type="name"><?php _e('MEC Name', 'modern-events-calendar-lite'); ?></button>
                            <button type="button" class="button red" data-type="mec_email"><?php _e('MEC Email', 'modern-events-calendar-lite'); ?></button>
                            <button type="button" class="button" data-type="text"><?php _e('Text', 'modern-events-calendar-lite'); ?></button>
                            <button type="button" class="button" data-type="email"><?php _e('Email', 'modern-events-calendar-lite'); ?></button>
                            <button type="button" class="button" data-type="date"><?php _e('Date', 'modern-events-calendar-lite'); ?></button>
                            <button type="button" class="button" data-type="tel"><?php _e('Tel', 'modern-events-calendar-lite'); ?></button>
                            <button type="button" class="button" data-type="file"><?php _e('File', 'modern-events-calendar-lite'); ?></button>
                            <button type="button" class="button" data-type="textarea"><?php _e('Textarea', 'modern-events-calendar-lite'); ?></button>
                            <button type="button" class="button" data-type="checkbox"><?php _e('Checkboxes', 'modern-events-calendar-lite'); ?></button>
                            <button type="button" class="button" data-type="radio"><?php _e('Radio Buttons', 'modern-events-calendar-lite'); ?></button>
                            <button type="button" class="button" data-type="select"><?php _e('Dropdown', 'modern-events-calendar-lite'); ?></button>
                            <button type="button" class="button" data-type="agreement"><?php _e('Agreement', 'modern-events-calendar-lite'); ?></button>
                            <button type="button" class="button" data-type="p"><?php _e('Paragraph', 'modern-events-calendar-lite'); ?></button>
                        </div>
                        <input type="hidden" id="mec_new_reg_field_key" value="<?php echo $i + 1; ?>"/>
                        <div class="mec-util-hidden">
                            <div id="mec_reg_field_text">
                                <?php echo $this->main->field_text(':i:'); ?>
                            </div>
                            <div id="mec_reg_field_email">
                                <?php echo $this->main->field_email(':i:'); ?>
                            </div>
                            <div id="mec_reg_field_mec_email">
                                <?php echo $this->main->field_mec_email(':i:'); ?>
                            </div>
                            <div id="mec_reg_field_name">
                                <?php echo $this->main->field_name(':i:'); ?>
                            </div>
                            <div id="mec_reg_field_tel">
                                <?php echo $this->main->field_tel(':i:'); ?>
                            </div>
                            <div id="mec_reg_field_date">
                                <?php echo $this->main->field_date(':i:'); ?>
                            </div>
                            <div id="mec_reg_field_file">
                                <?php echo $this->main->field_file(':i:'); ?>
                            </div>
                            <div id="mec_reg_field_textarea">
                                <?php echo $this->main->field_textarea(':i:'); ?>
                            </div>
                            <div id="mec_reg_field_checkbox">
                                <?php echo $this->main->field_checkbox(':i:'); ?>
                            </div>
                            <div id="mec_reg_field_radio">
                                <?php echo $this->main->field_radio(':i:'); ?>
                            </div>
                            <div id="mec_reg_field_select">
                                <?php echo $this->main->field_select(':i:'); ?>
                            </div>
                            <div id="mec_reg_field_agreement">
                                <?php echo $this->main->field_agreement(':i:'); ?>
                            </div>
                            <div id="mec_reg_field_p">
                                <?php echo $this->main->field_p(':i:'); ?>
                            </div>
                            <div id="mec_reg_field_option">
                                <?php echo $this->main->field_option(':fi:', ':i:'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="mec-booking-fixed-fields">
                        <h5 class="mec-form-subtitle"><?php _e('Fixed Fields', 'modern-events-calendar-lite'); ?></h5>
                        <div class="mec-form-row" id="mec_bfixed_form_container">
                            <?php /** Don't remove this hidden field **/ ?>
                            <input type="hidden" name="mec[bfixed_fields]" value="" />

                            <ul id="mec_bfixed_form_fields">
                                <?php
                                $b = 0;
                                foreach($bfixed_fields as $key => $bfixed_field)
                                {
                                    if(!is_numeric($key)) continue;
                                    $b = max($b, $key);

                                    if($bfixed_field['type'] == 'text') echo $this->main->field_text( $key, $bfixed_field, 'bfixed' );
                                    elseif($bfixed_field['type'] == 'name') echo $this->main->field_name( $key, $bfixed_field, 'bfixed' );
                                    elseif($bfixed_field['type'] == 'mec_email') echo $this->main->field_mec_email( $key, $bfixed_field, 'bfixed' );
                                    elseif($bfixed_field['type'] == 'email') echo $this->main->field_email( $key, $bfixed_field, 'bfixed' );
                                    elseif($bfixed_field['type'] == 'date') echo $this->main->field_date( $key, $bfixed_field, 'bfixed' );
                                    elseif($bfixed_field['type'] == 'file') echo $this->main->field_file( $key, $bfixed_field, 'bfixed' );
                                    elseif($bfixed_field['type'] == 'tel') echo $this->main->field_tel( $key, $bfixed_field, 'bfixed' );
                                    elseif($bfixed_field['type'] == 'textarea') echo $this->main->field_textarea( $key, $bfixed_field, 'bfixed' );
                                    elseif($bfixed_field['type'] == 'p') echo $this->main->field_p( $key, $bfixed_field, 'bfixed' );
                                    elseif($bfixed_field['type'] == 'checkbox') echo $this->main->field_checkbox( $key, $bfixed_field, 'bfixed' );
                                    elseif($bfixed_field['type'] == 'radio') echo $this->main->field_radio( $key, $bfixed_field, 'bfixed' );
                                    elseif($bfixed_field['type'] == 'select') echo $this->main->field_select( $key, $bfixed_field, 'bfixed' );
                                    elseif($bfixed_field['type'] == 'agreement') echo $this->main->field_agreement( $key, $bfixed_field, 'bfixed' );
                                }
                                ?>
                            </ul>
                            <div id="mec_bfixed_form_field_types">
                                <button type="button" class="button" data-type="text"><?php _e( 'Text', 'modern-events-calendar-lite' ); ?></button>
                                <button type="button" class="button" data-type="email"><?php _e( 'Email', 'modern-events-calendar-lite' ); ?></button>
                                <button type="button" class="button" data-type="date"><?php _e( 'Date', 'modern-events-calendar-lite' ); ?></button>
                                <button type="button" class="button" data-type="tel"><?php _e( 'Tel', 'modern-events-calendar-lite' ); ?></button>
                                <button type="button" class="button" data-type="textarea"><?php _e( 'Textarea', 'modern-events-calendar-lite' ); ?></button>
                                <button type="button" class="button" data-type="checkbox"><?php _e( 'Checkboxes', 'modern-events-calendar-lite' ); ?></button>
                                <button type="button" class="button" data-type="radio"><?php _e( 'Radio Buttons', 'modern-events-calendar-lite' ); ?></button>
                                <button type="button" class="button" data-type="select"><?php _e( 'Dropdown', 'modern-events-calendar-lite' ); ?></button>
                                <button type="button" class="button" data-type="agreement"><?php _e( 'Agreement', 'modern-events-calendar-lite' ); ?></button>
                                <button type="button" class="button" data-type="p"><?php _e( 'Paragraph', 'modern-events-calendar-lite' ); ?></button>
                            </div>
                        </div>
                        <input type="hidden" id="mec_new_bfixed_field_key" value="<?php echo $b + 1; ?>" />
                        <div class="mec-util-hidden">
                            <div id="mec_bfixed_field_text">
                                <?php echo $this->main->field_text(':i:', array(), 'bfixed'); ?>
                            </div>
                            <div id="mec_bfixed_field_email">
                                <?php echo $this->main->field_email(':i:', array(), 'bfixed'); ?>
                            </div>
                            <div id="mec_bfixed_field_tel">
                                <?php echo $this->main->field_tel(':i:', array(), 'bfixed'); ?>
                            </div>
                            <div id="mec_bfixed_field_date">
                                <?php echo $this->main->field_date(':i:', array(), 'bfixed'); ?>
                            </div>
                            <div id="mec_bfixed_field_textarea">
                                <?php echo $this->main->field_textarea(':i:', array(), 'bfixed'); ?>
                            </div>
                            <div id="mec_bfixed_field_checkbox">
                                <?php echo $this->main->field_checkbox(':i:', array(), 'bfixed'); ?>
                            </div>
                            <div id="mec_bfixed_field_radio">
                                <?php echo $this->main->field_radio(':i:', array(), 'bfixed'); ?>
                            </div>
                            <div id="mec_bfixed_field_select">
                                <?php echo $this->main->field_select(':i:', array(), 'bfixed'); ?>
                            </div>
                            <div id="mec_bfixed_field_agreement">
                                <?php echo $this->main->field_agreement(':i:', array(), 'bfixed'); ?>
                            </div>
                            <div id="mec_bfixed_field_p">
                                <?php echo $this->main->field_p(':i:', array(), 'bfixed'); ?>
                            </div>
                            <div id="mec_bfixed_field_option">
                                <?php echo $this->main->field_option(':fi:', ':i:', array(), 'bfixed'); ?>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <?php
        do_action('mec_events_meta_box_regform_end', $post->ID);
    }

    /**
     * Show attendees of event into the Add/Edit event page
     *
     * @author Webnus <info@webnus.biz>
     * @param object $post
     */
    public function meta_box_attendees($post)
    {
        $draft = (isset($post->post_status) and $post->post_status != 'auto-draft') ? false : true;
        if($draft) return;

        $limit = 100;
        $now = current_time('timestamp', 0);
        $_6months_ago = strtotime('-6 Months', $now);

        $occ = new MEC_feature_occurrences();
        $occurrences = $occ->get_dates($post->ID, $now, $limit);

        $date_format = get_option('date_format');
        $time_format = get_option('time_format');
        $datetime_format = $date_format.' '.$time_format;

        do_action('mec_events_meta_box_attendees_start', $post);
        ?>
        <div class="mec-meta-box-fields mec-booking-tab-content" id="mec_meta_box_booking_options_form_attendees">
            <h4 class="mec-meta-box-header"><?php _e('Attendees', 'modern-events-calendar-lite'); ?></h4>
            <div class="mec-attendees-wrapper mec-booking-attendees-wrapper">
                <div>
                    <select id="mec_att_occurrences_dropdown" title="<?php esc_attr_e('Occurrence', 'modern-events-calendar-lite'); ?>">
                        <option class="mec-load-occurrences" value="<?php echo $_6months_ago.':'.$_6months_ago; ?>"><?php esc_html_e('Previous Occurrences', 'modern-events-calendar-lite'); ?></option>
                        <?php $i = 1; foreach($occurrences as $occurrence): ?>
                        <option value="<?php echo $occurrence->tstart.':'.$occurrence->tend; ?>" <?php echo ($i === 1 ? 'selected="selected"' : ''); ?>><?php echo (date_i18n($datetime_format, $occurrence->tstart)); ?></option>
                        <?php $i++; endforeach; ?>
                        <?php if(count($occurrences) >= $limit and isset($occurrence)): ?>
                        <option class="mec-load-occurrences" value="<?php echo $occurrence->tstart.':'.$occurrence->tend; ?>"><?php esc_html_e('Next Occurrences', 'modern-events-calendar-lite'); ?></option>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="mec-attendees-list">
                </div>
            </div>
        </div>
        <script>
        jQuery(document).ready(function()
        {
            mec_attendees_trigger_load_dates();
            setTimeout(function()
            {
                jQuery('#mec_att_occurrences_dropdown').trigger('change');
            }, 500);
        });

        function mec_attendees_trigger_load_dates()
        {
            jQuery('#mec_att_occurrences_dropdown').off('change').on('change', function()
            {
                var $dropdown = jQuery(this);
                var value = $dropdown.val();
                var $attendees = jQuery('.mec-booking-attendees-wrapper .mec-attendees-list');

                // Load Dates
                if($dropdown.find(jQuery('option[value="'+value+'"]')).hasClass('mec-load-occurrences'))
                {
                    // Disable the Form
                    $dropdown.attr('disabled', 'disabled');

                    jQuery.ajax(
                    {
                        url: "<?php echo admin_url('admin-ajax.php', NULL); ?>",
                        type: "POST",
                        data: "action=mec_occurrences_dropdown&id=<?php echo $post->ID; ?>&_wpnonce=<?php echo wp_create_nonce('mec_occurrences_dropdown'); ?>&date="+value,
                        dataType: "json"
                    })
                    .done(function(response)
                    {
                        if(response.success) $dropdown.html(response.html);

                        // New Trigger
                        mec_attendees_trigger_load_dates();

                        setTimeout(function()
                        {
                            jQuery('#mec_att_occurrences_dropdown').trigger('change');
                        }, 500);

                        // Enable the Form
                        $dropdown.removeAttr('disabled');
                    });
                }
                // Load Attendees
                else
                {
                    // Disable the Form
                    $dropdown.attr('disabled', 'disabled');

                    jQuery.ajax(
                    {
                        url: "<?php echo admin_url('admin-ajax.php', NULL); ?>",
                        type: "POST",
                        data: "action=mec_event_bookings&id=<?php echo $post->ID; ?>&occurrence="+value+"&backend=<?php echo (is_admin() ? 1 : 0); ?>",
                        dataType: "json"
                    })
                    .done(function(response)
                    {
                        if(response.html) $attendees.html(response.html);

                        // Enable the Form
                        $dropdown.removeAttr('disabled');
                    });
                }
            });
        }
        </script>
        <?php
        do_action('mec_events_meta_box_attendees_end', $post);
    }

    /**
     * Save event data
     *
     * @author Webnus <info@webnus.biz>
     * @param int $post_id
     * @return void
     */
    public function save_event($post_id)
    {
        // Check if our nonce is set.
        if(!isset($_POST['mec_event_nonce'])) return;

        // It's from FES
        if(isset($_POST['action']) and $_POST['action'] === 'mec_fes_form') return;

        // Verify that the nonce is valid.
        if(!wp_verify_nonce($_POST['mec_event_nonce'], 'mec_event_data')) return;

        // If this is an autosave, our form has not been submitted, so we don't want to do anything.
        if(defined('DOING_AUTOSAVE') and DOING_AUTOSAVE) return;

        // Get Modern Events Calendar Data
        $_mec = isset($_POST['mec']) ? $_POST['mec'] : array();

        $start_date = (isset($_mec['date']['start']['date']) and trim($_mec['date']['start']['date'])) ? $this->main->standardize_format($_mec['date']['start']['date']) : date('Y-m-d');
        $end_date = (isset($_mec['date']['end']['date']) and trim($_mec['date']['end']['date'])) ? $this->main->standardize_format($_mec['date']['end']['date']) : date('Y-m-d');

        // Remove Cached Data
        wp_cache_delete($post_id, 'mec-events-data');

        $location_id = isset($_mec['location_id']) ? sanitize_text_field($_mec['location_id']) : 0;
        $dont_show_map = isset($_mec['dont_show_map']) ? sanitize_text_field($_mec['dont_show_map']) : 0;
        $organizer_id = isset($_mec['organizer_id']) ? sanitize_text_field($_mec['organizer_id']) : 0;
        $read_more = isset($_mec['read_more']) ? esc_url($_mec['read_more']) : '';
        $more_info = (isset($_mec['more_info']) and trim($_mec['more_info'])) ? esc_url($_mec['more_info']) : '';
        $more_info_title = isset($_mec['more_info_title']) ? sanitize_text_field($_mec['more_info_title']) : '';
        $more_info_target = isset($_mec['more_info_target']) ? sanitize_text_field($_mec['more_info_target']) : '';

        $cost = isset($_mec['cost']) ? $_mec['cost'] : '';
        $cost = apply_filters(
            'mec_event_cost_sanitize',
            sanitize_text_field($cost),
            $cost
        );

        $currency_options = ((isset($_mec['currency']) and is_array($_mec['currency'])) ? $_mec['currency'] : array());

        update_post_meta($post_id, 'mec_location_id', $location_id);
        update_post_meta($post_id, 'mec_dont_show_map', $dont_show_map);
        update_post_meta($post_id, 'mec_organizer_id', $organizer_id);
        update_post_meta($post_id, 'mec_read_more', $read_more);
        update_post_meta($post_id, 'mec_more_info', $more_info);
        update_post_meta($post_id, 'mec_more_info_title', $more_info_title);
        update_post_meta($post_id, 'mec_more_info_target', $more_info_target);
        update_post_meta($post_id, 'mec_cost', $cost);
        update_post_meta($post_id, 'mec_currency', $currency_options);

        do_action('update_custom_dev_post_meta', $_mec, $post_id);

        // Additional Organizers
        $additional_organizer_ids = isset($_mec['additional_organizer_ids']) ? $_mec['additional_organizer_ids'] : array();

        foreach($additional_organizer_ids as $additional_organizer_id) wp_set_object_terms($post_id, (int) $additional_organizer_id, 'mec_organizer', true);
        update_post_meta($post_id, 'mec_additional_organizer_ids', $additional_organizer_ids);

        // Additional locations
        $additional_location_ids = isset($_mec['additional_location_ids']) ? $_mec['additional_location_ids'] : array();

        foreach($additional_location_ids as $additional_location_id) wp_set_object_terms($post_id, (int) $additional_location_id, 'mec_location', true);
        update_post_meta($post_id, 'mec_additional_location_ids', $additional_location_ids);

        // Date Options
        $date = isset($_mec['date']) ? $_mec['date'] : array();

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
        $timezone = (isset($_mec['timezone']) and trim($_mec['timezone']) != '') ? sanitize_text_field($_mec['timezone']) : 'global';
        $countdown_method = (isset($_mec['countdown_method']) and trim($_mec['countdown_method']) != '') ? sanitize_text_field($_mec['countdown_method']) : 'global';
        $public = (isset($_mec['public']) and trim($_mec['public']) != '') ? sanitize_text_field($_mec['public']) : 1;

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

        // Repeat Options
        $repeat = isset($date['repeat']) ? $date['repeat'] : array();
        $certain_weekdays = isset($repeat['certain_weekdays']) ? $repeat['certain_weekdays'] : array();

        $repeat_status = isset($repeat['status']) ? 1 : 0;
        $repeat_type = ($repeat_status and isset($repeat['type'])) ? $repeat['type'] : '';

        // Unset Repeat if no days are selected
        if($repeat_type == 'certain_weekdays' and (!is_array($certain_weekdays) or (is_array($certain_weekdays) and !count($certain_weekdays))))
        {
            $repeat_status = 0;
            $repeat['status'] = 0;
            $repeat['type'] = '';
        }

        $repeat_interval = ($repeat_status and isset($repeat['interval']) and trim($repeat['interval'])) ? $repeat['interval'] : 1;

        // Advanced Repeat
        $advanced = isset($repeat['advanced']) ? sanitize_text_field($repeat['advanced']) : '';

        if(!is_numeric($repeat_interval)) $repeat_interval = null;

        if($repeat_type == 'weekly') $interval_multiply = 7;
        else $interval_multiply = 1;

        // Reset certain weekdays if repeat type is not set to certain weekdays
        if($repeat_type != 'certain_weekdays') $certain_weekdays = array();

        if(!is_null($repeat_interval)) $repeat_interval = $repeat_interval * $interval_multiply;

        // String To Array
        if($repeat_type == 'advanced' and trim($advanced)) $advanced = explode('-', $advanced);
        else $advanced = array();

        $repeat_end = ($repeat_status and isset($repeat['end'])) ? $repeat['end'] : '';
        $repeat_end_at_occurrences = ($repeat_status and isset($repeat['end_at_occurrences'])) ? ($repeat['end_at_occurrences'] - 1) : '';
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

        do_action('update_custom_post_meta', $date, $post_id);

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

        update_post_meta($post_id, 'mec_repeat_status', $repeat_status);
        update_post_meta($post_id, 'mec_repeat_type', $repeat_type);
        update_post_meta($post_id, 'mec_repeat_interval', $repeat_interval);
        update_post_meta($post_id, 'mec_repeat_end', $repeat_end);
        update_post_meta($post_id, 'mec_repeat_end_at_occurrences', $repeat_end_at_occurrences);
        update_post_meta($post_id, 'mec_repeat_end_at_date', $repeat_end_at_date);
        update_post_meta($post_id, 'mec_advanced_days', $advanced);

        // For Event Notification Badge.
        if(!current_user_can('administrator')) update_post_meta($post_id, 'mec_event_date_submit', date('YmdHis', current_time('timestamp', 0)));

        // Creating $event array for inserting in mec_events table
        $event = array(
            'post_id' => $post_id,
            'start' => $start_date,
            'repeat' => $repeat_status,
            'rinterval' => (!in_array($repeat_type, array('daily', 'weekly', 'monthly')) ? null : $repeat_interval),
            'time_start' => $day_start_seconds,
            'time_end' => $day_end_seconds,
        );

        $year = null;
        $month = null;
        $day = null;
        $week = null;
        $weekday = null;
        $weekdays = null;

        // MEC weekdays
        $mec_weekdays = $this->main->get_weekdays();

        // MEC weekends
        $mec_weekends = $this->main->get_weekends();

        $plus_date = '';
        if($repeat_type == 'daily')
        {
            $plus_date = '+' . $repeat_end_at_occurrences * $repeat_interval . ' Days';
        }
        elseif($repeat_type == 'weekly')
        {
            $plus_date = '+' . $repeat_end_at_occurrences * ($repeat_interval) . ' Days';
        }
        elseif($repeat_type == 'weekday')
        {
            $repeat_interval = 1;
            $plus_date = '+' . $repeat_end_at_occurrences * $repeat_interval . ' Weekdays';

            $weekdays = ',' . implode(',', $mec_weekdays) . ',';
        }
        elseif($repeat_type == 'weekend')
        {
            $repeat_interval = 1;
            $plus_date = '+' . round($repeat_end_at_occurrences / 2) * ($repeat_interval * 7) . ' Days';

            $weekdays = ',' . implode(',', $mec_weekends) . ',';
        }
        elseif($repeat_type == 'certain_weekdays')
        {
            $repeat_interval = 1;
            $plus_date = '+' . ceil(($repeat_end_at_occurrences * $repeat_interval) * (7 / count($certain_weekdays))) . ' days';

            $weekdays = ',' . implode(',', $certain_weekdays) . ',';
        }
        elseif($repeat_type == 'monthly')
        {
            $plus_date = '+' . $repeat_end_at_occurrences * $repeat_interval . ' Months';

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

            $day = ',' . implode(',', array_unique($_days)) . ',';

            $week = '*';
            $weekday = '*';
        }
        elseif($repeat_type == 'yearly')
        {
            $plus_date = '+' . $repeat_end_at_occurrences * $repeat_interval . ' Years';

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

            $_months = array_unique($_months);

            $month = ',' . implode(',', array($_months[0])) . ',';
            $day = ',' . implode(',', array_unique($_days)) . ',';

            $week = '*';
            $weekday = '*';
        }
        elseif($repeat_type == "advanced")
        {
            // Render class object
            $this->render = $this->getRender();

            // Get finish date
            $event_info = array('start' => $date['start'], 'end' => $date['end']);
            $dates = $this->render->generate_advanced_days($advanced, $event_info, $repeat_end_at_occurrences +1, $start_date, 'events');

            $period_date = $this->main->date_diff($start_date, end($dates)['end']['date']);
            $plus_date = '+' . $period_date->days . ' Days';
        }

        $in_days_arr = (isset($_mec['in_days']) and is_array($_mec['in_days']) and count($_mec['in_days'])) ? array_unique($_mec['in_days']) : array();
        $not_in_days_arr = (isset($_mec['not_in_days']) and is_array($_mec['not_in_days']) and count($_mec['not_in_days'])) ? array_unique($_mec['not_in_days']) : array();

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
                if(is_numeric($key)) $not_in_days .= $this->main->standardize_format( $not_in_day_arr ) . ',';
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
        $mec_event_id = $this->db->select("SELECT `id` FROM `#__mec_events` WHERE `post_id`='$post_id'", 'loadResult');

        if(!$mec_event_id)
        {
            $q1 = '';
            $q2 = '';

            foreach($event as $key => $value)
            {
                $q1 .= "`$key`,";

                if(is_null($value)) $q2 .= 'NULL,';
                else $q2 .= "'$value',";
            }

            $this->db->q('INSERT INTO `#__mec_events` (' . trim($q1, ', ') . ') VALUES (' . trim($q2, ', ') . ')', 'INSERT');
        }
        else
        {
            $q = '';

            foreach($event as $key => $value)
            {
                if(is_null($value)) $q .= "`$key`=NULL,";
                else $q .= "`$key`='$value',";
            }

            $this->db->q('UPDATE `#__mec_events` SET ' . trim($q, ', ') . " WHERE `id`='$mec_event_id'");
        }

        // Update Schedule
        $schedule = $this->getSchedule();
        $schedule->reschedule($post_id, $schedule->get_reschedule_maximum($repeat_type));

        // Hourly Schedule Options
        $raw_hourly_schedules = isset($_mec['hourly_schedules']) ? $_mec['hourly_schedules'] : array();
        unset($raw_hourly_schedules[':d:']);

        $hourly_schedules = array();
        foreach($raw_hourly_schedules as $raw_hourly_schedule)
        {
            if(isset($raw_hourly_schedule['schedules'][':i:'])) unset($raw_hourly_schedule['schedules'][':i:']);
            $hourly_schedules[] = $raw_hourly_schedule;
        }

        update_post_meta($post_id, 'mec_hourly_schedules', $hourly_schedules);

        // Booking and Ticket Options
        $booking = isset($_mec['booking']) ? $_mec['booking'] : array();
        update_post_meta($post_id, 'mec_booking', $booking);

        $tickets = isset($_mec['tickets']) ? $_mec['tickets'] : array();
        if(isset($tickets[':i:'])) unset($tickets[':i:']);

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

        // Fee options
        $fees_global_inheritance = isset($_mec['fees_global_inheritance']) ? $_mec['fees_global_inheritance'] : 1;
        update_post_meta($post_id, 'mec_fees_global_inheritance', $fees_global_inheritance);

        $fees = isset($_mec['fees']) ? $_mec['fees'] : array();
        if(isset($fees[':i:'])) unset($fees[':i:']);

        update_post_meta($post_id, 'mec_fees', $fees);

        // Ticket Variations options
        $ticket_variations_global_inheritance = isset($_mec['ticket_variations_global_inheritance']) ? $_mec['ticket_variations_global_inheritance'] : 1;
        update_post_meta($post_id, 'mec_ticket_variations_global_inheritance', $ticket_variations_global_inheritance);

        $ticket_variations = isset($_mec['ticket_variations']) ? $_mec['ticket_variations'] : array();
        if(isset($ticket_variations[':i:'])) unset($ticket_variations[':i:']);

        update_post_meta($post_id, 'mec_ticket_variations', $ticket_variations);

        // Registration Fields options
        $reg_fields_global_inheritance = isset($_mec['reg_fields_global_inheritance']) ? $_mec['reg_fields_global_inheritance'] : 1;
        update_post_meta($post_id, 'mec_reg_fields_global_inheritance', $reg_fields_global_inheritance);

        $reg_fields = isset($_mec['reg_fields']) ? $_mec['reg_fields'] : array();
        if($reg_fields_global_inheritance) $reg_fields = array();

        do_action('mec_save_reg_fields', $post_id, $reg_fields);
        update_post_meta($post_id, 'mec_reg_fields', $reg_fields);

        $bfixed_fields = isset($_mec['bfixed_fields']) ? $_mec['bfixed_fields'] : array();
        if($reg_fields_global_inheritance) $bfixed_fields = array();

        do_action('mec_save_bfixed_fields', $post_id, $bfixed_fields);
        update_post_meta($post_id, 'mec_bfixed_fields', $bfixed_fields);

        // Organizer Payment Options
        $op = isset($_mec['op']) ? $_mec['op'] : array();
        update_post_meta($post_id, 'mec_op', $op);
        update_user_meta(get_post_field('post_author', $post_id), 'mec_op', $op);

        // MEC Fields
        $fields = (isset($_mec['fields']) and is_array($_mec['fields'])) ? $_mec['fields'] : array();
        update_post_meta($post_id, 'mec_fields', $fields);

        // Downloadable File
        if(isset($_mec['downloadable_file']))
        {
            $dl_file = isset($_mec['downloadable_file']) ? $_mec['downloadable_file'] : '';
            update_post_meta($post_id, 'mec_dl_file', $dl_file);
        }

        // Notifications
        if(isset($_mec['notifications']))
        {
            $notifications = (isset($_mec['notifications']) and is_array($_mec['notifications'])) ? $_mec['notifications'] : array();
            update_post_meta($post_id, 'mec_notifications', $notifications);
        }

        $mec_update = (isset($_REQUEST['original_publish']) and strtolower(trim($_REQUEST['original_publish'])) == 'publish') ? false : true;
        do_action('mec_after_publish_admin_event', $post_id, $mec_update);

        // Save Event Data
        do_action('mec_save_event_data', $post_id, $_mec);
    }

    public function quick_edit($post_id)
    {
        // Validating And Verifying
        if((!isset($_POST['screen']) || trim($_POST['screen']) != 'edit-mec-events') and !check_ajax_referer('inlineeditnonce', '_inline_edit', false)) return;

        $mec_locations = (isset($_POST['tax_input']['mec_location']) and trim($_POST['tax_input']['mec_location'])) ? array_filter(explode(',', sanitize_text_field($_POST['tax_input']['mec_location']))) : NULL;
        $mec_organizers = (isset($_POST['tax_input']['mec_organizer']) and trim($_POST['tax_input']['mec_organizer'])) ? array_filter(explode(',', sanitize_text_field($_POST['tax_input']['mec_organizer']))) : NULL;

        // MEC Locations Quick Edit
        $this->mec_locations_edit($post_id, $mec_locations, 'quick_edit');

        // MEC Organizers Quick Edit
        $this->mec_organizers_edit($post_id, $mec_organizers, 'quick_edit');
    }

     /**
     * Publish a event
     * @author Webnus <info@webnus.biz>
     * @param string $new
     * @param string $old
     * @param object $post
     * @return void
     */
    public function event_published($new, $old, $post)
    {
        // Fires after publish a event to send notifications etc.
        do_action('mec_event_published', $new, $old, $post);
    }

    /**
     * Remove MEC event data after deleting a post permanently
     *
     * @author Webnus <info@webnus.biz>
     * @param int $post_id
     * @return boolean
     */
    public function delete_event($post_id)
    {
        $this->db->q("DELETE FROM `#__mec_events` WHERE `post_id`='$post_id'");
        $this->db->q("DELETE FROM `#__mec_dates` WHERE `post_id`='$post_id'");
        $this->db->q("DELETE FROM `#__mec_occurrences` WHERE `post_id`='$post_id'");

        return true;
    }

    public function add_buttons($which)
    {
        $screen = get_current_screen();
        if($which === 'top' and $screen->post_type === $this->PT)
        {
            echo '<a href="'.admin_url('edit.php?post_type='.$this->PT.'&mec-expired=1').'" class="button">'.esc_html__('Expired Events', 'modern-events-calendar-lite').'</a>';
            echo '&nbsp;<a href="'.admin_url('edit.php?post_type='.$this->PT.'&mec-upcoming=1').'" class="button">'.esc_html__('Upcoming Events', 'modern-events-calendar-lite').'</a>';
        }
    }

    /**
     * Add filter options in manage events page
     *
     * @author Webnus <info@webnus.biz>
     * @param string $post_type
     * @return void
     */
    public function add_filters($post_type)
    {
        if($post_type != $this->PT) return;

        $taxonomy = 'mec_label';
        if(wp_count_terms($taxonomy))
        {
            wp_dropdown_categories(
                array(
                    'show_option_all' => sprintf(__('Show all %s', 'modern-events-calendar-lite'), $this->main->m('taxonomy_labels', __('labels', 'modern-events-calendar-lite'))),
                    'taxonomy' => $taxonomy,
                    'name' => $taxonomy,
                    'value_field' => 'slug',
                    'orderby' => 'name',
                    'order' => 'ASC',
                    'selected' => (isset($_GET[$taxonomy]) ? sanitize_text_field($_GET[$taxonomy]) : ''),
                    'show_count' => false,
                    'hide_empty' => false,
                )
            );
        }

        $taxonomy = 'mec_location';
        if(wp_count_terms($taxonomy))
        {
            wp_dropdown_categories(
                array(
                    'show_option_all' => sprintf(__('Show all %s', 'modern-events-calendar-lite'), $this->main->m('taxonomy_locations', __('locations', 'modern-events-calendar-lite'))),
                    'taxonomy' => $taxonomy,
                    'name' => $taxonomy,
                    'value_field' => 'slug',
                    'orderby' => 'name',
                    'order' => 'ASC',
                    'selected' => (isset($_GET[$taxonomy]) ? sanitize_text_field($_GET[$taxonomy]) : ''),
                    'show_count' => false,
                    'hide_empty' => false,
                )
            );
        }

        $taxonomy = 'mec_organizer';
        if(wp_count_terms($taxonomy))
        {
            wp_dropdown_categories(
                array(
                    'show_option_all' => sprintf(__('Show all %s', 'modern-events-calendar-lite'), $this->main->m('taxonomy_organizers', __('organizers', 'modern-events-calendar-lite'))),
                    'taxonomy' => $taxonomy,
                    'name' => $taxonomy,
                    'value_field' => 'slug',
                    'orderby' => 'name',
                    'order' => 'ASC',
                    'selected' => (isset($_GET[$taxonomy]) ? sanitize_text_field($_GET[$taxonomy]) : ''),
                    'show_count' => false,
                    'hide_empty' => false,
                )
            );
        }

        $taxonomy = 'mec_category';
        if(wp_count_terms($taxonomy))
        {
            wp_dropdown_categories(
                array(
                    'show_option_all' => sprintf(__('Show all %s', 'modern-events-calendar-lite'), $this->main->m('taxonomy_categorys', __('Categories', 'modern-events-calendar-lite'))),
                    'taxonomy' => $taxonomy,
                    'name' => $taxonomy,
                    'value_field' => 'slug',
                    'orderby' => 'name',
                    'order' => 'ASC',
                    'selected' => (isset($_GET[$taxonomy]) ? sanitize_text_field($_GET[$taxonomy]) : ''),
                    'show_count' => false,
                    'hide_empty' => false,
                )
            );
        }

        // Lightbox
        echo '
            <div id="mec_manage_events_lightbox" class="lity-hide">
                <div class="mec-attendees-list-head">'. esc_html__('Attendees List', 'modern-events-calendar-lite') .'</div>
                <div class="mec-attendees-list-wrap">
                    <div class="mec-attendees-list-left">
                        <div class="mec-attendees-list-left-menu mec-owl-carousel mec-owl-theme">

                        </div>
                    </div>
                    <div class="mec-attendees-list-right">

                    </div>
                </div>
            </div>';
    }

    /**
     * Filters columns of events feature
     *
     * @author Webnus <info@webnus.biz>
     * @param array $columns
     * @return array
     */
    public function filter_columns($columns)
    {
        unset($columns['comments']);
        unset($columns['date']);
        unset($columns['tags']);

        $columns['title'] = __('Title', 'modern-events-calendar-lite');
        $columns['category'] = __('Category', 'modern-events-calendar-lite');
        $columns['location'] = $this->main->m('taxonomy_location', __('Location', 'modern-events-calendar-lite'));
        $columns['organizer'] = $this->main->m('taxonomy_organizer', __('Organizer', 'modern-events-calendar-lite'));
        $columns['start_date'] = __('Start Date', 'modern-events-calendar-lite');
        $columns['end_date'] = __('End Date', 'modern-events-calendar-lite');

        // Sold Tickets
        if($this->getPRO() and (isset($this->settings['booking_status']) and $this->settings['booking_status'])) $columns['sold_tickets'] = __('Sold Tickets', 'modern-events-calendar-lite');

        $columns['repeat'] = __('Repeat', 'modern-events-calendar-lite');
        return $columns;
    }

    /**
     * Filters sortable columns of events feature
     *
     * @author Webnus <info@webnus.biz>
     * @param array $columns
     * @return array
     */
    public function filter_sortable_columns($columns)
    {
        $columns['start_date'] = 'start_date';
        $columns['end_date'] = 'end_date';

        return $columns;
    }

    /**
     * Filters columns content of events feature
     *
     * @author Webnus <info@webnus.biz>
     * @param string $column_name
     * @param int $post_id
     * @return string
     */
    public function filter_columns_content($column_name, $post_id)
    {
        if($column_name == 'location')
        {
            $location = get_term(get_post_meta($post_id, 'mec_location_id', true));
            echo(isset($location->name) ? $location->name : '----');
        }
        elseif($column_name == 'organizer')
        {
            $organizer = get_term(get_post_meta($post_id, 'mec_organizer_id', true));
            echo(isset($organizer->name) ? $organizer->name : '----');
        }
        elseif($column_name == 'start_date')
        {
            $datetime_format = get_option('date_format', 'Y-n-d').' '.get_option('time_format', 'H:i');
            $date = get_post_meta($post_id, 'mec_start_date', true);

            echo $this->main->date_i18n($datetime_format, (strtotime($date) + ((int) get_post_meta($post_id, 'mec_start_day_seconds', true))), $post_id);
        }
        elseif($column_name == 'end_date')
        {
            $datetime_format = get_option('date_format', 'Y-n-d').' '.get_option('time_format', 'H:i');
            $date = get_post_meta($post_id, 'mec_end_date', true);

            echo $this->main->date_i18n($datetime_format, (strtotime($date) + ((int) get_post_meta($post_id, 'mec_end_day_seconds', true))), $post_id);
        }
        elseif($column_name == 'sold_tickets')
        {
            echo $this->getBook()->get_all_sold_tickets($post_id);
        }
        elseif($column_name == 'repeat')
        {
            $repeat_type = get_post_meta($post_id, 'mec_repeat_type', true);
            echo ucwords(str_replace('_', ' ', $repeat_type));
        }
        elseif($column_name == 'category')
        {
            $post_categories = get_the_terms($post_id, 'mec_category');
            if($post_categories) foreach($post_categories as $post_category) $categories[] = $post_category->name;
            if(!empty($categories))
            {
                $category_name = implode(",", $categories);
                echo $category_name;
            }
        }
    }

    /**
     * Sort events if sorted by custom columns
     *
     * @author Webnus <info@webnus.biz>
     * @param object $query
     * @return void
     */
    public function filter($query)
    {
        if(!is_admin() or $query->get('post_type') != $this->PT) return;

        $meta_query = array();
        $order_query = array();

        $orderby = $query->get('orderby');
        $order = $query->get('order');

        $expired = (isset($_REQUEST['mec-expired']) ? $_REQUEST['mec-expired'] : 0);
        if($expired)
        {
            $meta_query[] = array(
                'key' => 'mec_repeat_status',
                'value' => '0',
            );

            $meta_query[] = array(
                'key' => 'mec_end_date',
                'value' => current_time('Y-m-d'),
                'compare' => '<',
                'type' => 'DATE',
            );

            if(!trim($orderby)) $orderby = 'end_date';
            if(!trim($order)) $order = 'asc';
        }

        $upcoming = (isset($_REQUEST['mec-upcoming']) ? $_REQUEST['mec-upcoming'] : 0);
        if($upcoming)
        {
            $now = current_time('Y-m-d H:i:s');

            $post_id_rows = $this->db->select("SELECT `post_id` FROM `#__mec_dates` WHERE `tstart` >= '".strtotime($now)."' GROUP BY `post_id`", 'loadObjectList');

            $post_ids = array();
            foreach($post_id_rows as $post_id_row) $post_ids[] = $post_id_row->post_id;

            $post_ids = array_unique($post_ids);
            $query->set('post__in', $post_ids);

            if(!trim($orderby)) $orderby = 'start_date';
        }

        if($orderby == 'start_date')
        {
            $meta_query['mec_start_date'] = array(
                'key' => 'mec_start_date',
            );

            $meta_query['mec_start_day_seconds'] = array(
                'key' => 'mec_start_day_seconds',
            );

            $order_query = array(
                'mec_start_date' => $query->get('order'),
                'mec_start_day_seconds' => $query->get('order'),
            );
        }
        elseif($orderby == 'end_date')
        {
            $meta_query['mec_end_date'] = array(
                'key' => 'mec_end_date',
            );

            $meta_query['mec_end_day_seconds'] = array(
                'key' => 'mec_end_day_seconds',
            );

            $order_query = array(
                'mec_end_date' => $order,
                'mec_end_day_seconds' => $order,
            );
        }

        if(count($meta_query)) $query->set('meta_query', $meta_query);
        if(count($order_query)) $query->set('orderby', $order_query);
    }

    public function add_bulk_actions()
    {
        global $post_type;

        if ($post_type == $this->PT) {
            ?>
            <script type="text/javascript">
                jQuery(document).ready(function () {
                    jQuery('<option>').val('ical-export').text('<?php echo __('iCal / Outlook Export', 'modern-events-calendar-lite'); ?>').appendTo("select[name='action']");
                    jQuery('<option>').val('ical-export').text('<?php echo __('iCal / Outlook Export', 'modern-events-calendar-lite'); ?>').appendTo("select[name='action2']");

                    jQuery('<option>').val('csv-export').text('<?php echo __('CSV Export', 'modern-events-calendar-lite'); ?>').appendTo("select[name='action']");
                    jQuery('<option>').val('csv-export').text('<?php echo __('CSV Export', 'modern-events-calendar-lite'); ?>').appendTo("select[name='action2']");

                    jQuery('<option>').val('ms-excel-export').text('<?php echo __('MS Excel Export', 'modern-events-calendar-lite'); ?>').appendTo("select[name='action']");
                    jQuery('<option>').val('ms-excel-export').text('<?php echo __('MS Excel Export', 'modern-events-calendar-lite'); ?>').appendTo("select[name='action2']");

                    jQuery('<option>').val('xml-export').text('<?php echo __('XML Export', 'modern-events-calendar-lite'); ?>').appendTo("select[name='action']");
                    jQuery('<option>').val('xml-export').text('<?php echo __('XML Export', 'modern-events-calendar-lite'); ?>').appendTo("select[name='action2']");

                    jQuery('<option>').val('json-export').text('<?php echo __('JSON Export', 'modern-events-calendar-lite'); ?>').appendTo("select[name='action']");
                    jQuery('<option>').val('json-export').text('<?php echo __('JSON Export', 'modern-events-calendar-lite'); ?>').appendTo("select[name='action2']");

                    jQuery('<option>').val('duplicate').text('<?php echo __('Duplicate', 'modern-events-calendar-lite'); ?>').appendTo("select[name='action']");
                    jQuery('<option>').val('duplicate').text('<?php echo __('Duplicate', 'modern-events-calendar-lite'); ?>').appendTo("select[name='action2']");
                });
            </script>
            <?php
        }
    }

    public function do_bulk_actions()
    {
        $wp_list_table = _get_list_table('WP_Posts_List_Table');

        $action = $wp_list_table->current_action();
        if(!$action) return false;

        $post_type = isset($_GET['post_type']) ? sanitize_text_field($_GET['post_type']) : 'post';
        if($post_type != $this->PT) return false;

        check_admin_referer('bulk-posts');

        switch($action)
        {
            case 'ical-export':

                $post_ids = $_GET['post'];
                $events = '';

                foreach($post_ids as $post_id) $events .= $this->main->ical_single((int) $post_id);
                $ical_calendar = $this->main->ical_calendar($events);

                header('Content-type: application/force-download; charset=utf-8');
                header('Content-Disposition: attachment; filename="mec-events-' . date('YmdTHi') . '.ics"');

                echo $ical_calendar;

                exit;
                break;

            case 'ms-excel-export':

                header('Content-Type: application/vnd.ms-excel; charset=utf-8');
                header('Content-Disposition: attachment; filename=mec-events-' . md5(time() . mt_rand(100, 999)) . '.xls');

                $this->csvexcel();

                exit;
                break;

            case 'csv-export':

                header('Content-Type: text/csv; charset=utf-8');
                header('Content-Disposition: attachment; filename=mec-events-' . md5(time() . mt_rand(100, 999)) . '.csv');

                $this->csvexcel();

                exit;
                break;

            case 'xml-export':

                $post_ids = $_GET['post'];

                $events = array();
                foreach($post_ids as $post_id) $events[] = $this->main->export_single((int) $post_id);

                $xml_feed = $this->main->xml_convert(array('events' => $events));

                header('Content-type: application/force-download; charset=utf-8');
                header('Content-Disposition: attachment; filename="mec-events-' . date('YmdTHi') . '.xml"');

                echo $xml_feed;

                exit;
                break;

            case 'json-export':

                $post_ids = $_GET['post'];

                $events = array();
                foreach ($post_ids as $post_id) $events[] = $this->main->export_single((int) $post_id);

                header('Content-type: application/force-download; charset=utf-8');
                header('Content-Disposition: attachment; filename="mec-events-' . date('YmdTHi') . '.json"');

                echo json_encode($events);

                exit;
                break;

            case 'duplicate':

                $post_ids = $_GET['post'];
                foreach($post_ids as $post_id) $this->main->duplicate((int) $post_id);

                break;

            default:
                return false;
        }

        wp_redirect('edit.php?post_type=' . $this->main->get_main_post_type());
        exit;
    }

    public function csvexcel($export_all = false, $excel = false)
    {
        // MEC Render Library
        $render = $this->getRender();

        if($export_all) $post_ids = get_posts('post_type=mec-events&fields=ids&posts_per_page=-1');
        else $post_ids = isset($_GET['post']) ? (array) $_GET['post'] : array();

        $columns = array(
            __('ID', 'modern-events-calendar-lite'),
            __('Title', 'modern-events-calendar-lite'),
            __('Description', 'modern-events-calendar-lite'),
            __('Start Date', 'modern-events-calendar-lite'),
            __('Start Time', 'modern-events-calendar-lite'),
            __('End Date', 'modern-events-calendar-lite'),
            __('End Time', 'modern-events-calendar-lite'),
            __('Link', 'modern-events-calendar-lite'),
            $this->main->m('taxonomy_location', __('Location', 'modern-events-calendar-lite')),
            __('Address', 'modern-events-calendar-lite'),
            $this->main->m('taxonomy_organizer', __('Organizer', 'modern-events-calendar-lite')),
            sprintf(__('%s Tel', 'modern-events-calendar-lite'), $this->main->m('taxonomy_organizer', __('Organizer', 'modern-events-calendar-lite'))),
            sprintf(__('%s Email', 'modern-events-calendar-lite'), $this->main->m('taxonomy_organizer', __('Organizer', 'modern-events-calendar-lite'))),
            $this->main->m('event_cost', __('Event Cost', 'modern-events-calendar-lite')),
            __('Featured Image', 'modern-events-calendar-lite'),
            __('Labels', 'modern-events-calendar-lite'),
            __('Categories', 'modern-events-calendar-lite'),
            __('Tags', 'modern-events-calendar-lite'),
        );

        // Speakers
        if(isset($this->settings['speakers_status']) and $this->settings['speakers_status']) $columns[] = __('Speakers', 'modern-events-calendar-lite');

        // Event Fields
        $fields = $this->main->get_event_fields();
        if(!is_array($fields)) $fields = array();

        foreach($fields as $f => $field)
        {
            if(!is_numeric($f)) continue;
            if(!isset($field['label']) or (isset($field['label']) and trim($field['label']) == '')) continue;

            $columns[] = stripslashes($field['label']);
        }

        $delimiter = ($excel ? "\t" : ',');
        $output = fopen('php://output', 'w');

        if($excel) fwrite($output, "sep=\t".PHP_EOL);
        else fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        fputcsv($output, $columns, $delimiter);

        foreach($post_ids as $post_id)
        {
            $post_id = (int)$post_id;

            $data = $render->data($post_id);
            $dates = $render->dates($post_id, $data);
            $date = $dates[0];

            $location = isset($data->locations[$data->meta['mec_location_id']]) ? $data->locations[$data->meta['mec_location_id']] : array();
            $organizer = isset($data->organizers[$data->meta['mec_organizer_id']]) ? $data->organizers[$data->meta['mec_organizer_id']] : array();
            $cost = isset($data->meta['mec_cost']) ? $data->meta['mec_cost'] : null;

            $taxonomies = array('mec_label', 'mec_category', apply_filters('mec_taxonomy_tag', ''));
            if(isset($this->settings['speakers_status']) and $this->settings['speakers_status']) $taxonomies[] = 'mec_speaker';

            $labels = array();
            $categories = array();
            $tags = array();
            $speakers = array();

            $terms = wp_get_post_terms($post_id, $taxonomies, array('fields'=>'all'));
            foreach($terms as $term)
            {
                // First Validation
                if(!isset($term->taxonomy)) continue;

                if($term->taxonomy == 'mec_label') $labels[] = $term->name;
                elseif($term->taxonomy == 'mec_category') $categories[] = $term->name;
                elseif($term->taxonomy == apply_filters('mec_taxonomy_tag', '')) $tags[] = $term->name;
                elseif($term->taxonomy == 'mec_speaker') $speakers[] = $term->name;
            }

            $event = array(
                $post_id,
                html_entity_decode($data->title, ENT_QUOTES | ENT_HTML5),
                html_entity_decode(strip_tags($data->content), ENT_QUOTES | ENT_HTML5),
                $date['start']['date'],
                $data->time['start'],
                $date['end']['date'],
                $data->time['end'],
                $data->permalink,
                (isset($location['name']) ? $location['name'] : ''),
                (isset($location['address']) ? $location['address'] : ''),
                (isset($organizer['name']) ? $organizer['name'] : ''),
                (isset($organizer['tel']) ? $organizer['tel'] : ''),
                (isset($organizer['email']) ? $organizer['email'] : ''),
                (is_numeric($cost) ? $this->main->render_price($cost, $post_id) : $cost),
                $this->main->get_post_thumbnail_url($post_id),
                implode(', ', $labels),
                implode(', ', $categories),
                implode(', ', $tags),
            );

            // Speakers
            if(isset($this->settings['speakers_status']) and $this->settings['speakers_status']) $event[] = implode(', ', $speakers);

            // Event Fields
            if(isset($data->fields) and is_array($data->fields) and count($data->fields))
            {
                foreach($data->fields as $field) $event[] = $field['value'];
            }

            fputcsv($output, $event, $delimiter);
        }
    }

    public function action_links($actions, $post)
    {
        if($post->post_type != $this->PT) return $actions;

        $actions['mec-duplicate'] = '<a href="'.$this->main->add_qs_vars(array('mec-action'=>'duplicate-event', 'id'=>$post->ID)).'">'.__('Duplicate', 'modern-events-calendar-lite').'</a>';

        return $actions;
    }

    public function duplicate_event()
    {
        // It's not a duplicate request
        if(!isset($_GET['mec-action']) or (isset($_GET['mec-action']) and $_GET['mec-action'] != 'duplicate-event')) return false;

        // Event ID to duplicate
        $id = isset($_GET['id']) ? $_GET['id'] : 0;
        if(!$id) return false;

        // Duplicate
        $new_post_id = $this->main->duplicate((int) $id);

        wp_redirect('post.php?post=' . $new_post_id . '&action=edit');
        exit;
    }

     /**
     * Do bulk edit Action
     *
     * @author Webnus <info@webnus.biz>
     * @return void
     */
    public function bulk_edit()
    {
        $post_ids = (isset($_GET['post']) and is_array($_GET['post']) and count($_GET['post'])) ? array_map('sanitize_text_field', wp_unslash($_GET['post'])) : array();
        if(!is_array($post_ids) or !count($post_ids)) return;

        $mec_locations = (isset($_GET['tax_input']['mec_location']) and trim($_GET['tax_input']['mec_location'])) ? array_filter(explode(',', sanitize_text_field($_GET['tax_input']['mec_location']))) : NULL;
        $mec_organizers = (isset($_GET['tax_input']['mec_organizer']) and trim($_GET['tax_input']['mec_organizer'])) ? array_filter(explode(',', sanitize_text_field($_GET['tax_input']['mec_organizer']))) : NULL;
        $terms = get_terms(array(
            'taxonomy' => array('mec_location', 'mec_organizer'),
        ));

        foreach($post_ids as $post_id)
        {
            foreach($terms as $term)
            {
                $term_objects = get_objects_in_term($term->term_id, $term->taxonomy);
                if(in_array($post_id, $term_objects)) wp_remove_object_terms($post_id, $term->term_id, $term->taxonomy);
            }

            // MEC Locations Bulk Edit
            $this->mec_locations_edit($post_id, $mec_locations);

            // MEC Organizers Bulk Edit
            $this->mec_organizers_edit($post_id, $mec_organizers);
        }
    }

    // MEC Locations Edit.
    public function mec_locations_edit($post_id, $mec_locations, $action = 'bulk_edit')
    {
        if(!is_null($mec_locations))
        {
            $term_location = current($mec_locations);
            if(!term_exists($term_location, 'mec_location')) wp_insert_term($term_location, 'mec_location', array());

            $location_id =  get_term_by('name', $term_location, 'mec_location')->term_id;
            wp_set_object_terms($post_id, (int) $location_id, 'mec_location');
            update_post_meta($post_id, 'mec_location_id', $location_id);

            if(count($mec_locations) > 1)
            {
                // Additional locations
                $additional_location_ids = array();

                for($i = 1; $i < count($mec_locations); $i++)
                {
                    if(!term_exists($mec_locations[$i], 'mec_location')) wp_insert_term($mec_locations[$i], 'mec_location', array());

                    $additional_location_id =  get_term_by('name', $mec_locations[$i], 'mec_location')->term_id;
                    wp_set_object_terms($post_id, (int)$additional_location_id, 'mec_location', true);
                    $additional_location_ids[] = (int)$additional_location_id;
                }

                update_post_meta($post_id, 'mec_additional_location_ids', $additional_location_ids);
            }
        }
        elseif($action == 'quick_edit')
        {
            update_post_meta($post_id, 'mec_location_id', 0);
            update_post_meta($post_id, 'mec_additional_location_ids', array());
        }
    }

    // MEC Organizers Edit.
    public function mec_organizers_edit($post_id, $mec_organizers, $action = 'bulk_edit')
    {
        if(!is_null($mec_organizers))
        {
            $term_organizer = current($mec_organizers);
            if(!term_exists($term_organizer, 'mec_organizer')) wp_insert_term($term_organizer, 'mec_organizer', array());

            $organizer_id =  get_term_by('name', current($mec_organizers), 'mec_organizer')->term_id;
            wp_set_object_terms($post_id, (int)$organizer_id, 'mec_organizer');
            update_post_meta($post_id, 'mec_organizer_id', $organizer_id);

            if(count($mec_organizers) > 1)
            {
                // Additional organizers
                $additional_organizer_ids = array();

                for($i = 1; $i < count($mec_organizers); $i++)
                {
                    if(!term_exists($mec_organizers[$i], 'mec_organizer')) wp_insert_term($mec_organizers[$i], 'mec_organizer', array());

                    $additional_organizer_id =  get_term_by('name', $mec_organizers[$i], 'mec_organizer')->term_id;
                    wp_set_object_terms($post_id, (int)$additional_organizer_id, 'mec_organizer', true);
                    $additional_organizer_ids[] = (int)$additional_organizer_id;
                }

                update_post_meta($post_id, 'mec_additional_organizer_ids', $additional_organizer_ids);
            }
        }
        elseif($action == 'quick_edit')
        {
            update_post_meta($post_id, 'mec_organizer_id', 0);
            update_post_meta($post_id, 'mec_additional_organizer_ids', array());
        }
    }

    public function attendees()
    {
        $id = isset($_POST['id']) ? sanitize_text_field($_POST['id']) : 0;

        $occurrence = isset($_POST['occurrence']) ? sanitize_text_field($_POST['occurrence']) : NULL;
        $occurrence = explode(':', $occurrence)[0];
        if($occurrence == 'all') $occurrence = strtotime('+100 years');

        $tickets = get_post_meta($id, 'mec_tickets', true);
        $attendees = $this->main->get_event_attendees($id, $occurrence);

        $html = '';
        if(count($attendees))
        {
            $html .= '<div class="w-clearfix mec-attendees-head">
                <div class="w-col-xs-1">
                    <span><input type="checkbox" id="mec-send-email-check-all" onchange="mec_send_email_check_all(this);" /></span>
                </div>
                <div class="w-col-xs-3 name">
                    <span>'.__('Name', 'modern-events-calendar-lite').'</span>
                </div>
                <div class="w-col-xs-3 email">
                    <span>'.__('Email', 'modern-events-calendar-lite').'</span>
                </div>
                <div class="w-col-xs-3 ticket">
                    <span>'.$this->main->m('ticket', __('Ticket', 'modern-events-calendar-lite')).'</span>
                </div>
                <div class="w-col-xs-2">
                    <span>'.__('Variations', 'modern-events-calendar-lite').'</span>
                </div>';

            $html = apply_filters('mec_attendees_list_header_html', $html, $id, $occurrence);
            $html .= '</div>';
            $index = $key = 0;

            foreach($attendees as $attendee)
            {
                $key++;

                $html .= '<div class="w-clearfix mec-attendees-content">';
                $html .= '<div class="w-col-xs-1"><input type="checkbox" onchange="mec_send_email_check(this);" /><span class="mec-util-hidden mec-send-email-attendee-info">'.$attendee['name'].':.:'.$attendee['email'].',</span></div>';
                $html .= '<div class="w-col-xs-3 name">' . get_avatar($attendee['email']) .$attendee['name'].'</div>';
                $html .= '<div class="w-col-xs-3 email">'.$attendee['email'] .'</div>';
                $html .= '<div class="w-col-xs-3 ticket">'.((isset($attendee['id']) and isset($tickets[$attendee['id']]['name'])) ? $tickets[$attendee['id']]['name'] : __('Unknown', 'modern-events-calendar-lite')).'</div>';

                $variations = '<div class="w-col-xs-2">';
                if(isset($attendee['variations']) and is_array($attendee['variations']) and count($attendee['variations']))
                {
                    $ticket_variations = $this->main->ticket_variations($id, $attendee['id']);

                    foreach($attendee['variations'] as $variation_id=>$variation_count)
                    {
                        if(!$variation_count or ($variation_count and $variation_count < 0)) continue;

                        $variation_title = (isset($ticket_variations[$variation_id]) and isset($ticket_variations[$variation_id]['title'])) ? $ticket_variations[$variation_id]['title'] : '';
                        if(!trim($variation_title)) continue;

                        $variations .= '<span>+ '.$variation_title.'</span>
                        <span>('.$variation_count.')</span>';
                    }
                }

                $variations .= '</div>';

                $html .= $variations;
                $html = apply_filters('mec_attendees_list_html', $html, $attendee, $attendee['key'], $attendee['book_id']);
                $html .= '</div>';

                $index++;
            }

            $email_button = '<p>'.esc_html__('If you want to send an email, first select your attendees and then click in the button below, please.', 'modern-events-calendar-lite').'</p><button data-id="'.$id.'" onclick="mec_submit_event_email('.$id.');">'.esc_html__('Send Email', 'modern-events-calendar-lite').'</button>';
        }
        else
        {
            $html .= '<p>'.__("No Attendees Found!", 'modern-events-calendar-lite').'</p>';
            $email_button = '';
        }

        echo json_encode(array('html' => $html , 'email_button' => $email_button ));
        exit;
    }

    public function mass_email()
    {
        if(!wp_verify_nonce($_REQUEST['nonce'], 'mec_settings_nonce')) exit();

        // Current User is not Permitted
        if(!current_user_can('mec_report')) $this->main->response(array('success'=>0, 'code'=>'NO_ACCESS'));

        $mail_recipients_info = isset($_POST['mail_recipients_info']) ? trim(sanitize_text_field($_POST['mail_recipients_info']), ', ') : '';
        $mail_subject = isset($_POST['mail_subject']) ? sanitize_text_field($_POST['mail_subject']) : '';
        $mail_content = isset($_POST['mail_content']) ? $_POST['mail_content'] : '';
        $mail_copy = isset($_POST['mail_copy']) ? $_POST['mail_copy'] : 0;

        $render_recipients = array_unique(explode(',', $mail_recipients_info));
        $headers = array('Content-Type: text/html; charset=UTF-8');

        // Changing some sender email info.
        $notifications = $this->getNotifications();
        $notifications->mec_sender_email_notification_filter();

        // Send to Admin
        if($mail_copy) $render_recipients[] = 'Admin:.:'.get_option('admin_email');

        // Set Email Type to HTML
        add_filter('wp_mail_content_type', array($this->main, 'html_email_type'));

        foreach($render_recipients as $recipient)
        {
            $render_recipient = explode(':.:', $recipient);

            $to = isset($render_recipient[1]) ? trim($render_recipient[1]) : '';
            if(!trim($to)) continue;

            $message = $mail_content;
            $message = str_replace('%%name%%', (isset($render_recipient[0]) ? trim($render_recipient[0]) : ''), $message);

            $mail_arg = array(
                'to' => $to,
                'subject' => $mail_subject,
                'message' => $message,
                'headers' => $headers,
                'attachments' => array(),
            );

            $mail_arg = apply_filters('mec_before_send_mass_email', $mail_arg, 'mass_email');

            // Send the mail
            wp_mail($mail_arg['to'], html_entity_decode(stripslashes($mail_arg['subject']), ENT_HTML5), wpautop(stripslashes($mail_arg['message'])), $mail_arg['headers'], $mail_arg['attachments']);
        }

        // Remove the HTML Email filter
        remove_filter('wp_mail_content_type', array($this->main, 'html_email_type'));

        wp_die(true);
    }

    public function icl_duplicate($master_post_id, $lang, $post, $id)
    {
        $master = get_post($master_post_id);
        $target = get_post($id);

        if($master->post_type != $this->PT) return;
        if($target->post_type != $this->PT) return;

        $already_duplicated = get_post_meta($id, 'mec_icl_duplicated', true);
        if($already_duplicated) return;

        $master_location_id = get_post_meta($master_post_id, 'mec_location_id', true);
        $target_location_id = apply_filters('wpml_object_id', $master_location_id, 'mec_location', true, $lang);

        update_post_meta($id, 'mec_location_id', $target_location_id);

        $master_additional_location_ids = get_post_meta($master_post_id, 'mec_additional_location_ids', true);
        if(!is_array($master_additional_location_ids)) $master_additional_location_ids = array();

        $target_additional_location_ids = array();
        foreach($master_additional_location_ids as $master_additional_location_id)
        {
            $target_additional_location_ids[] = apply_filters('wpml_object_id', $master_additional_location_id, 'mec_location', true, $lang);
        }

        update_post_meta($id, 'mec_additional_location_ids', $target_additional_location_ids);

        $master_organizer_id = get_post_meta($master_post_id, 'mec_organizer_id', true);
        $target_organizer_id = apply_filters('wpml_object_id', $master_organizer_id, 'mec_organizer', true, $lang);

        update_post_meta($id, 'mec_organizer_id', $target_organizer_id);

        $master_additional_organizer_ids = get_post_meta($master_post_id, 'mec_additional_organizer_ids', true);
        if(!is_array($master_additional_organizer_ids)) $master_additional_organizer_ids = array();

        $target_additional_organizer_ids = array();
        foreach($master_additional_organizer_ids as $master_additional_organizer_id)
        {
            $target_additional_organizer_ids[] = apply_filters('wpml_object_id', $master_additional_organizer_id, 'mec_location', true, $lang);
        }

        update_post_meta($id, 'mec_additional_organizer_ids', $target_additional_organizer_ids);

        // MEC Tables
        $this->db->q("INSERT INTO `#__mec_events` (`post_id`, `start`, `end`, `repeat`, `rinterval`, `year`, `month`, `day`, `week`, `weekday`, `weekdays`, `days`, `not_in_days`, `time_start`, `time_end`) SELECT '".$id."', `start`, `end`, `repeat`, `rinterval`, `year`, `month`, `day`, `week`, `weekday`, `weekdays`, `days`, `not_in_days`, `time_start`, `time_end` FROM `#__mec_events` WHERE `post_id`='".$master_post_id."'");

        update_post_meta($id, 'mec_icl_duplicated', 1);

        // Update Schedule
        $schedule = $this->getSchedule();
        $schedule->reschedule($id);
    }

    public function wpml_pro_translation_saved($new_post_id, $fields, $job)
    {
        global $iclTranslationManagement;

        $master_post_id = NULL;
        if(is_object($job) and $iclTranslationManagement)
        {
            $element_type_prefix = $iclTranslationManagement->get_element_type_prefix_from_job($job);
            $original_post = $iclTranslationManagement->get_post($job->original_doc_id, $element_type_prefix);

            if($original_post) $master_post_id = $original_post->ID;
        }

        // Target Language
        $lang_options = apply_filters('wpml_post_language_details', NULL, $new_post_id);
        $lang = (is_array($lang_options) and isset($lang_options['language_code'])) ? $lang_options['language_code'] : '';

        // Duplicate Content
        if($master_post_id) $this->icl_duplicate($master_post_id, $lang, (new stdClass()), $new_post_id);
    }

    public function set_fallback_image_id($value, $post_id, $meta_key, $single)
    {
        // Only on frontend
        if(is_admin() and (!defined('DOING_AJAX') or (defined('DOING_AJAX') and !DOING_AJAX))) return $value;

        // Only for empty _thumbnail_id keys
        if(!empty($meta_key) && '_thumbnail_id' !== $meta_key) return $value;

        // Only For Events
        if(get_post_type($post_id) != $this->PT) return $value;

        // Get current Cache
        $meta_cache = wp_cache_get($post_id, 'post_meta');
        if(!$meta_cache)
        {
            $meta_cache = update_meta_cache('post', array($post_id));

            if(isset($meta_cache[$post_id])) $meta_cache = $meta_cache[$post_id];
            else $meta_cache = array();
        }

        // Is the _thumbnail_id present in cache?
        if(!empty($meta_cache['_thumbnail_id'][0])) return $value;

        $fallback_image_id = $this->get_fallback_image_id($post_id);
        if(!$fallback_image_id) return $value;

        // Set the Fallback Image in cache
        $meta_cache['_thumbnail_id'][0] = $fallback_image_id;
        wp_cache_set($post_id, $meta_cache, 'post_meta');

        return $value;
    }

    public function show_fallback_image($html, $post_id, $post_thumbnail_id, $size, $attr)
    {
        // Only on frontend
        if((is_admin() && (!defined('DOING_AJAX') || !DOING_AJAX))) return $html;

        // Only For Events
        if(get_post_type($post_id) != $this->PT) return $html;

        $fallback_image_id = $this->get_fallback_image_id($post_id);

        // if an image is set return that image.
        if((int) $fallback_image_id !== (int) $post_thumbnail_id) return $html;

        if(isset($attr['class'])) $attr['class'] .= ' mec-fallback-img';
        else
        {
            $size_class = $size;
            if(is_array($size_class)) $size_class = 'size-'.implode('x', $size_class);

            $attr = array('class' => 'attachment-'.$size_class.' default-featured-img');
        }

        return wp_get_attachment_image($fallback_image_id, $size, false, $attr);
    }

    public function get_fallback_image_id($event_id)
    {
        // Categories
        $categories = get_the_terms($event_id, 'mec_category');
        if(!is_array($categories) or (is_array($categories) and !count($categories))) return NULL;

        // Fallback Image ID
        $fallback_image_id = NULL;
        foreach($categories as $category)
        {
            $fallback_image = get_term_meta($category->term_id, 'mec_cat_fallback_image', true);
            if(trim($fallback_image))
            {
                $fallback_image_id = attachment_url_to_postid($fallback_image);
                if($fallback_image_id) break;
            }
        }

        return $fallback_image_id;
    }

    public function mec_event_bookings()
    {
        $id = isset($_POST['id']) ? sanitize_text_field($_POST['id']) : 0;
        $backend = isset($_POST['backend']) ? sanitize_text_field($_POST['backend']) : 0;

        $occurrence = isset($_POST['occurrence']) ? sanitize_text_field($_POST['occurrence']) : NULL;
        $occurrence = explode(':', $occurrence)[0];
        if($occurrence == 'all') $occurrence = strtotime('+100 years');

        $bookings = $this->main->get_bookings($id, $occurrence);
        $book = $this->getBook();

        $html = '';
        if(count($bookings))
        {
            $html .= '<div class="w-clearfix">
                <div class="w-col-xs-3 name">
                    <span>'.__('Title', 'modern-events-calendar-lite').'</span>
                </div>
                <div class="w-col-xs-3 email">
                    <span>'.__('Attendees', 'modern-events-calendar-lite').'</span>
                </div>
                <div class="w-col-xs-3 ticket">
                    <span>'.__('Transaction ID', 'modern-events-calendar-lite').'</span>
                </div>
                <div class="w-col-xs-3">
                    <span>'.__('Price', 'modern-events-calendar-lite').'</span>
                </div>
            </div>';

            /** @var WP_Post $booking */

            $index = $key = 0;
            foreach($bookings as $booking)
            {
                $key++;

                $attendees = $book->get_attendees($booking->ID);

                $unique_attendees = array();
                foreach($attendees as $attendee)
                {
                    if(!isset($unique_attendees[$attendee['email']])) $unique_attendees[$attendee['email']] = $attendee;
                    else $unique_attendees[$attendee['email']]['count'] += 1;
                }

                $attendees_html = '<strong>'.count($attendees).'</strong>';
                $attendees_html .= '<div class="mec-booking-attendees-tooltip">';
                $attendees_html .= '<ul>';

                foreach($unique_attendees as $unique_attendee)
                {
                    $attendees_html .= '<li>';
                    $attendees_html .= '<div class="mec-booking-attendees-tooltip-name">'.$unique_attendee['name'].((isset($unique_attendee['count']) and $unique_attendee['count'] > 1) ? ' ('.$unique_attendee['count'].')' : '').'</div>';
                    $attendees_html .= '<div class="mec-booking-attendees-tooltip-email"><a href="mailto:'.$unique_attendee['email'].'">'.$unique_attendee['email'].'</a></div>';
                    $attendees_html .= '</li>';
                }

                $attendees_html .= '</ul>';
                $attendees_html .= '</div>';

                $transaction_id = get_post_meta($booking->ID, 'mec_transaction_id', true);

                $price = get_post_meta($booking->ID, 'mec_price', true);
                $event_id = get_post_meta($booking->ID, 'mec_event_id', true);

                $price_html = $this->main->render_price(($price ? $price : 0), $event_id);
                $price_html .= ' '.get_post_meta($booking->ID, 'mec_gateway_label', true);

                $html .= '<div class="w-clearfix">';
                $html .= '<div class="w-col-xs-3">'.($backend ? '<a href="'.get_edit_post_link($booking->ID).'" target="_blank">'.$booking->post_title.'</a>' : $booking->post_title).'</div>';
                $html .= '<div class="w-col-xs-3">'.$attendees_html.'</div>';
                $html .= '<div class="w-col-xs-3">'.$transaction_id.'</div>';
                $html .= '<div class="w-col-xs-3">'.$price_html.'</div>';
                $html .= '</div>';

                $index++;
            }
        }
        else
        {
            $html .= '<p>'.__("No Bookings Found!", 'modern-events-calendar-lite').'</p>';
        }

        echo json_encode(array('html' => $html));
        exit;
    }
}