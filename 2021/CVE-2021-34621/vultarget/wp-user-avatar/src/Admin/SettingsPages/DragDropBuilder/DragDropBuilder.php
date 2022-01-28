<?php

namespace ProfilePress\Core\Admin\SettingsPages\DragDropBuilder;

use ProfilePress\Core\Admin\SettingsPages\FormList;
use ProfilePress\Core\Classes\ExtensionManager as EM;
use ProfilePress\Core\Classes\FormRepository as FR;
use ProfilePress\Core\Classes\PROFILEPRESS_sql;
use ProfilePress\Core\Themes\DragDrop\AbstractTheme;

class DragDropBuilder
{
    public $saved_data = [];

    public $form_type;

    public $form_class;

    public $form_id;

    public $meta_box_settings;

    /** @var AbstractTheme */
    public $theme_class_instance;

    public function __construct()
    {
        if ( ! $this->is_drag_drop_page()) return;

        Fields\Init::init();

        $this->form_id   = absint($_GET['id']);
        $this->form_type = sanitize_text_field($_GET['form-type']);

        $this->form_class = FR::get_form_meta($this->form_id, $this->form_type, FR::FORM_CLASS);

        $this->saved_data = apply_filters('ppress_form_builder_saved_data', []);

        add_action('admin_footer', [$this, 'form_fields_json']);
        add_action('admin_footer', [$this, 'print_template']);
        add_action('admin_footer', [$this, 'icon_picker_template']);

        add_action('admin_init', [$this, 'save_form']);

        add_action('admin_enqueue_scripts', [$this, 'js_wp_editor_enqueue']);
        add_action('admin_footer', [$this, 'js_wp_editor']);
    }

    public function standard_fields()
    {
        return apply_filters('ppress_form_builder_standard_fields', []);
    }

    public function extra_fields()
    {
        return apply_filters('ppress_form_builder_extra_fields', []);
    }

    public function defined_fields($woocommerce_field = false)
    {
        if ( ! in_array($this->form_type, [FR::REGISTRATION_TYPE, FR::EDIT_PROFILE_TYPE])) return [];

        if ($woocommerce_field !== false && ( ! EM::is_enabled(EM::WOOCOMMERCE) || ! EM::is_enabled(EM::CUSTOM_FIELDS))) {
            return [];
        }

        $custom_fields = PROFILEPRESS_sql::get_profile_custom_fields();

        $contact_infos = PROFILEPRESS_sql::get_contact_info_fields();

        $fields = [];

        if ($woocommerce_field === false) {
            foreach ($contact_infos as $field_key => $label) {

                // field key and type are being added to make the key unique for each defined fields array.
                $tag_name         = $this->form_type == FR::REGISTRATION_TYPE ? 'reg' : 'edit-profile';
                $key              = $tag_name . '-cpf-' . $field_key . 'contactinfo';
                $definedFieldType = 'input';

                $fields[$key] = [
                    'definedFieldKey'  => $field_key,
                    'definedFieldType' => $definedFieldType,
                    'fieldTitle'       => $label,
                    'label'            => $label,
                    'placeholder'      => $label,
                    'fieldIcon'        => '<span class="dashicons dashicons-portfolio"></span>',
                ];
            }
        }

        foreach ($custom_fields as $custom_field) {

            // field key and type are being added to make the key unique for each defined fields array.
            $tag_name         = $this->form_type == FR::REGISTRATION_TYPE ? 'reg' : 'edit-profile';
            $key              = $tag_name . '-cpf-' . $custom_field['field_key'] . $custom_field['type'];
            $definedFieldType = $custom_field['type'];

            if ($definedFieldType == 'password') {
                $definedFieldType = 'password';
            }

            if (in_array($definedFieldType, ['tel', 'text', 'number', 'email', 'hidden', 'file', 'textarea'])) {
                $definedFieldType = 'input';
            }

            if (in_array($definedFieldType, ['country', 'select'])) {
                $definedFieldType = 'select';
            }

            $field_key = $custom_field['field_key'];

            if (false == $woocommerce_field && in_array($field_key, ppress_woocommerce_billing_shipping_fields())) continue;

            if ($woocommerce_field !== false) {

                $bucket = ('billing' == $woocommerce_field ? ppress_woocommerce_billing_fields() : ppress_woocommerce_shipping_fields());

                if ( ! in_array($field_key, $bucket)) continue;
            }

            $title = $custom_field['label_name'];

            $fields[$key] = [
                'definedFieldKey'  => $field_key,
                'definedFieldType' => $definedFieldType,
                'fieldTitle'       => $title . ($woocommerce_field !== false ? (sprintf(' (WC%s)', 'billing' == $woocommerce_field ? 'BA' : 'SA')) : ''),
                'fieldBarTitle'    => $title,
                'label'            => $title,
                'placeholder'      => $title,
                'fieldIcon'        => '<span class="dashicons dashicons-portfolio"></span>',
            ];
        }

        return apply_filters(sprintf('pp_form_builder_defined%s_fields', $woocommerce_field ? "_{$woocommerce_field}" : ''), $fields);
    }

    public function is_drag_drop_page()
    {
        return isset($_GET['view']) && $_GET['view'] == 'drag-drop-builder';
    }

    public function save_form()
    {
        if ( ! current_user_can('manage_options') || ! isset($_POST['pp_form_builder_fields_settings']) || ! ppress_verify_nonce()) return;

        $constants = apply_filters(
            'ppress_form_builder_metabox_field_as_form_meta',
            array_values((new \ReflectionClass('ProfilePress\Core\Classes\FormRepository'))->getConstants())
        );

        $form_settings_meta = [];
        $metabox_settings   = [];
        foreach ($_POST as $key => $value) {
            if (in_array($key, ['wp_csa_nonce', 'pp_form_title', 'pp_form_builder_fields_settings', '_wpnonce', '_wp_http_referer'])) continue;
            if (in_array($key, $constants)) {
                $form_settings_meta[$key] = trim(stripslashes($value));
                continue;
            }

            $metabox_settings[$key] = is_array($value) ? array_map('stripslashes', $value) : trim(stripslashes($value));
        }

        if (empty($_POST['pp_form_title'])) {
            add_settings_error(
                'pp_drag_drop_builder_notice',
                'form_title_empty',
                esc_html__('Form title cannot empty')
            );

            return;
        }

        FR::update_form(
            $this->form_id,
            $this->form_type,
            sanitize_text_field($_POST['pp_form_title']),
            array_merge($form_settings_meta, [
                FR::FORM_BUILDER_FIELDS_SETTINGS  => stripslashes($_POST['pp_form_builder_fields_settings']),
                FR::METABOX_FORM_BUILDER_SETTINGS => $metabox_settings
            ])
        );

        add_settings_error(
            'pp_drag_drop_builder_notice',
            'changes_saved',
            esc_html__('Changes saved'),
            'success'
        );
    }

    private function is_custom_field_enabled()
    {
        return EM::is_enabled(EM::CUSTOM_FIELDS);
    }

    public function form_fields_json()
    {
        printf(
            '<script type="text/javascript">
                    var pp_form_builder_standard_fields = %1$s;
                    // extend copies over the new attribute to pp_form_builder_standard_fields
                    // hence the need to duplicate this
                    var old_pp_form_builder_standard_fields = %1$s;
                    var pp_form_builder_extra_fields = %2$s;
                    var pp_form_builder_defined_fields = %3$s;
                    var pp_form_builder_wc_billing_fields = %4$s;
                    var pp_form_builder_wc_shipping_fields = %5$s;
                    var pp_form_builder_combined_fields = _.extend(
                        old_pp_form_builder_standard_fields,
                        pp_form_builder_extra_fields,
                        pp_form_builder_defined_fields,
                        pp_form_builder_wc_billing_fields,
                        pp_form_builder_wc_shipping_fields  
                    );
                    var pp_form_builder_fields_settings =  %6$s;
                    var pp_form_builder_fields_multiple_addition =  %7$s;
                    </script>',
            json_encode($this->standard_fields()),
            $this->is_custom_field_enabled() ? json_encode($this->extra_fields()) : '{}',
            $this->is_custom_field_enabled() ? json_encode($this->defined_fields()) : '{}',
            $this->is_custom_field_enabled() ? json_encode($this->defined_fields('billing')) : '{}',
            $this->is_custom_field_enabled() ? json_encode($this->defined_fields('shipping')) : '{}',
            FR::dnd_form_fields_json($this->form_id, $this->form_type, call_user_func([$this->theme_class_instance, 'default_fields_settings'])),
            json_encode(apply_filters('ppress_form_builder_fields_multiple_addition', ['profile-cpf', 'pp-custom-html']))
        );
    }

    public function icon_picker_template()
    {
        $icons = [
            '3d_rotation',
            'ac_unit',
            'access_alarm',
            'access_alarms',
            'access_time',
            'accessibility',
            'accessible',
            'account_balance',
            'account_balance_wallet',
            'account_box',
            'account_circle',
            'adb',
            'add',
            'add_a_photo',
            'add_alarm',
            'add_alert',
            'add_box',
            'add_circle',
            'add_circle_outline',
            'add_location',
            'add_shopping_cart',
            'add_to_photos',
            'add_to_queue',
            'adjust',
            'airline_seat_flat',
            'airline_seat_flat_angled',
            'airline_seat_individual_suite',
            'airline_seat_legroom_extra',
            'airline_seat_legroom_normal',
            'airline_seat_legroom_reduced',
            'airline_seat_recline_extra',
            'airline_seat_recline_normal',
            'airplanemode_active',
            'airplanemode_inactive',
            'airplay',
            'airport_shuttle',
            'alarm',
            'alarm_add',
            'alarm_off',
            'alarm_on',
            'album',
            'all_inclusive',
            'all_out',
            'android',
            'announcement',
            'apps',
            'archive',
            'arrow_back',
            'arrow_downward',
            'arrow_drop_down',
            'arrow_drop_down_circle',
            'arrow_drop_up',
            'arrow_forward',
            'arrow_upward',
            'art_track',
            'aspect_ratio',
            'assessment',
            'assignment',
            'assignment_ind',
            'assignment_late',
            'assignment_return',
            'assignment_returned',
            'assignment_turned_in',
            'assistant',
            'assistant_photo',
            'attach_file',
            'attach_money',
            'attachment',
            'audiotrack',
            'autorenew',
            'av_timer',
            'backspace',
            'backup',
            'battery_alert',
            'battery_charging_full',
            'battery_full',
            'battery_std',
            'battery_unknown',
            'beach_access',
            'beenhere',
            'block',
            'bluetooth',
            'bluetooth_audio',
            'bluetooth_connected',
            'bluetooth_disabled',
            'bluetooth_searching',
            'blur_circular',
            'blur_linear',
            'blur_off',
            'blur_on',
            'book',
            'bookmark',
            'bookmark_border',
            'border_all',
            'border_bottom',
            'border_clear',
            'border_color',
            'border_horizontal',
            'border_inner',
            'border_left',
            'border_outer',
            'border_right',
            'border_style',
            'border_top',
            'border_vertical',
            'branding_watermark',
            'brightness_1',
            'brightness_2',
            'brightness_3',
            'brightness_4',
            'brightness_5',
            'brightness_6',
            'brightness_7',
            'brightness_auto',
            'brightness_high',
            'brightness_low',
            'brightness_medium',
            'broken_image',
            'brush',
            'bubble_chart',
            'bug_report',
            'build',
            'burst_mode',
            'business',
            'business_center',
            'cached',
            'cake',
            'call',
            'call_end',
            'call_made',
            'call_merge',
            'call_missed',
            'call_missed_outgoing',
            'call_received',
            'call_split',
            'call_to_action',
            'camera',
            'camera_alt',
            'camera_enhance',
            'camera_front',
            'camera_rear',
            'camera_roll',
            'cancel',
            'card_giftcard',
            'card_membership',
            'card_travel',
            'casino',
            'cast',
            'cast_connected',
            'center_focus_strong',
            'center_focus_weak',
            'change_history',
            'chat',
            'chat_bubble',
            'chat_bubble_outline',
            'check',
            'check_box',
            'check_box_outline_blank',
            'check_circle',
            'chevron_left',
            'chevron_right',
            'child_care',
            'child_friendly',
            'chrome_reader_mode',
            'class',
            'clear',
            'clear_all',
            'close',
            'closed_caption',
            'cloud',
            'cloud_circle',
            'cloud_done',
            'cloud_download',
            'cloud_off',
            'cloud_queue',
            'cloud_upload',
            'code',
            'collections',
            'collections_bookmark',
            'color_lens',
            'colorize',
            'comment',
            'compare',
            'compare_arrows',
            'computer',
            'confirmation_number',
            'contact_mail',
            'contact_phone',
            'contacts',
            'content_copy',
            'content_cut',
            'content_paste',
            'control_point',
            'control_point_duplicate',
            'copyright',
            'create',
            'create_new_folder',
            'credit_card',
            'crop',
            'crop_16_9',
            'crop_3_2',
            'crop_5_4',
            'crop_7_5',
            'crop_din',
            'crop_free',
            'crop_landscape',
            'crop_original',
            'crop_portrait',
            'crop_rotate',
            'crop_square',
            'dashboard',
            'data_usage',
            'date_range',
            'dehaze',
            'delete',
            'delete_forever',
            'delete_sweep',
            'description',
            'desktop_mac',
            'desktop_windows',
            'details',
            'developer_board',
            'developer_mode',
            'device_hub',
            'devices',
            'devices_other',
            'dialer_sip',
            'dialpad',
            'directions',
            'directions_bike',
            'directions_boat',
            'directions_bus',
            'directions_car',
            'directions_railway',
            'directions_run',
            'directions_subway',
            'directions_transit',
            'directions_walk',
            'disc_full',
            'dns',
            'do_not_disturb',
            'do_not_disturb_alt',
            'do_not_disturb_off',
            'do_not_disturb_on',
            'dock',
            'domain',
            'done',
            'done_all',
            'donut_large',
            'donut_small',
            'drafts',
            'drag_handle',
            'drive_eta',
            'dvr',
            'edit',
            'edit_location',
            'eject',
            'email',
            'enhanced_encryption',
            'equalizer',
            'error',
            'error_outline',
            'euro_symbol',
            'ev_station',
            'event',
            'event_available',
            'event_busy',
            'event_note',
            'event_seat',
            'exit_to_app',
            'expand_less',
            'expand_more',
            'explicit',
            'explore',
            'exposure',
            'exposure_neg_1',
            'exposure_neg_2',
            'exposure_plus_1',
            'exposure_plus_2',
            'exposure_zero',
            'extension',
            'face',
            'fast_forward',
            'fast_rewind',
            'favorite',
            'favorite_border',
            'featured_play_list',
            'featured_video',
            'feedback',
            'fiber_dvr',
            'fiber_manual_record',
            'fiber_new',
            'fiber_pin',
            'fiber_smart_record',
            'file_download',
            'file_upload',
            'filter',
            'filter_1',
            'filter_2',
            'filter_3',
            'filter_4',
            'filter_5',
            'filter_6',
            'filter_7',
            'filter_8',
            'filter_9',
            'filter_9_plus',
            'filter_b_and_w',
            'filter_center_focus',
            'filter_drama',
            'filter_frames',
            'filter_hdr',
            'filter_list',
            'filter_none',
            'filter_tilt_shift',
            'filter_vintage',
            'find_in_page',
            'find_replace',
            'fingerprint',
            'first_page',
            'fitness_center',
            'flag',
            'flare',
            'flash_auto',
            'flash_off',
            'flash_on',
            'flight',
            'flight_land',
            'flight_takeoff',
            'flip',
            'flip_to_back',
            'flip_to_front',
            'folder',
            'folder_open',
            'folder_shared',
            'folder_special',
            'font_download',
            'format_align_center',
            'format_align_justify',
            'format_align_left',
            'format_align_right',
            'format_bold',
            'format_clear',
            'format_color_fill',
            'format_color_reset',
            'format_color_text',
            'format_indent_decrease',
            'format_indent_increase',
            'format_italic',
            'format_line_spacing',
            'format_list_bulleted',
            'format_list_numbered',
            'format_paint',
            'format_quote',
            'format_shapes',
            'format_size',
            'format_strikethrough',
            'format_textdirection_l_to_r',
            'format_textdirection_r_to_l',
            'format_underlined',
            'forum',
            'forward',
            'forward_10',
            'forward_30',
            'forward_5',
            'free_breakfast',
            'fullscreen',
            'fullscreen_exit',
            'functions',
            'g_translate',
            'gamepad',
            'games',
            'gavel',
            'gesture',
            'get_app',
            'gif',
            'golf_course',
            'gps_fixed',
            'gps_not_fixed',
            'gps_off',
            'grade',
            'gradient',
            'grain',
            'graphic_eq',
            'grid_off',
            'grid_on',
            'group',
            'group_add',
            'group_work',
            'hd',
            'hdr_off',
            'hdr_on',
            'hdr_strong',
            'hdr_weak',
            'headset',
            'headset_mic',
            'healing',
            'hearing',
            'help',
            'help_outline',
            'high_quality',
            'highlight',
            'highlight_off',
            'history',
            'home',
            'hot_tub',
            'hotel',
            'hourglass_empty',
            'hourglass_full',
            'http',
            'https',
            'image',
            'image_aspect_ratio',
            'import_contacts',
            'import_export',
            'important_devices',
            'inbox',
            'indeterminate_check_box',
            'info',
            'info_outline',
            'input',
            'insert_chart',
            'insert_comment',
            'insert_drive_file',
            'insert_emoticon',
            'insert_invitation',
            'insert_link',
            'insert_photo',
            'invert_colors',
            'invert_colors_off',
            'iso',
            'keyboard',
            'keyboard_arrow_down',
            'keyboard_arrow_left',
            'keyboard_arrow_right',
            'keyboard_arrow_up',
            'keyboard_backspace',
            'keyboard_capslock',
            'keyboard_hide',
            'keyboard_return',
            'keyboard_tab',
            'keyboard_voice',
            'kitchen',
            'label',
            'label_outline',
            'landscape',
            'language',
            'laptop',
            'laptop_chromebook',
            'laptop_mac',
            'laptop_windows',
            'last_page',
            'launch',
            'layers',
            'layers_clear',
            'leak_add',
            'leak_remove',
            'lens',
            'library_add',
            'library_books',
            'library_music',
            'lightbulb_outline',
            'line_style',
            'line_weight',
            'linear_scale',
            'link',
            'linked_camera',
            'list',
            'live_help',
            'live_tv',
            'local_activity',
            'local_airport',
            'local_atm',
            'local_bar',
            'local_cafe',
            'local_car_wash',
            'local_convenience_store',
            'local_dining',
            'local_drink',
            'local_florist',
            'local_gas_station',
            'local_grocery_store',
            'local_hospital',
            'local_hotel',
            'local_laundry_service',
            'local_library',
            'local_mall',
            'local_movies',
            'local_offer',
            'local_parking',
            'local_pharmacy',
            'local_phone',
            'local_pizza',
            'local_play',
            'local_post_office',
            'local_printshop',
            'local_see',
            'local_shipping',
            'local_taxi',
            'location_city',
            'location_disabled',
            'location_off',
            'location_on',
            'location_searching',
            'lock',
            'lock_open',
            'lock_outline',
            'looks',
            'looks_3',
            'looks_4',
            'looks_5',
            'looks_6',
            'looks_one',
            'looks_two',
            'loop',
            'loupe',
            'low_priority',
            'loyalty',
            'mail',
            'mail_outline',
            'map',
            'markunread',
            'markunread_mailbox',
            'memory',
            'menu',
            'merge_type',
            'message',
            'mic',
            'mic_none',
            'mic_off',
            'mms',
            'mode_comment',
            'mode_edit',
            'monetization_on',
            'money_off',
            'monochrome_photos',
            'mood',
            'mood_bad',
            'more',
            'more_horiz',
            'more_vert',
            'motorcycle',
            'mouse',
            'move_to_inbox',
            'movie',
            'movie_creation',
            'movie_filter',
            'multiline_chart',
            'music_note',
            'music_video',
            'my_location',
            'nature',
            'nature_people',
            'navigate_before',
            'navigate_next',
            'navigation',
            'near_me',
            'network_cell',
            'network_check',
            'network_locked',
            'network_wifi',
            'new_releases',
            'next_week',
            'nfc',
            'no_encryption',
            'no_sim',
            'not_interested',
            'note',
            'note_add',
            'notifications',
            'notifications_active',
            'notifications_none',
            'notifications_off',
            'notifications_paused',
            'offline_pin',
            'ondemand_video',
            'opacity',
            'open_in_browser',
            'open_in_new',
            'open_with',
            'pages',
            'pageview',
            'palette',
            'pan_tool',
            'panorama',
            'panorama_fish_eye',
            'panorama_horizontal',
            'panorama_vertical',
            'panorama_wide_angle',
            'party_mode',
            'pause',
            'pause_circle_filled',
            'pause_circle_outline',
            'payment',
            'people',
            'people_outline',
            'perm_camera_mic',
            'perm_contact_calendar',
            'perm_data_setting',
            'perm_device_information',
            'perm_identity',
            'perm_media',
            'perm_phone_msg',
            'perm_scan_wifi',
            'person',
            'person_add',
            'person_outline',
            'person_pin',
            'person_pin_circle',
            'personal_video',
            'pets',
            'phone',
            'phone_android',
            'phone_bluetooth_speaker',
            'phone_forwarded',
            'phone_in_talk',
            'phone_iphone',
            'phone_locked',
            'phone_missed',
            'phone_paused',
            'phonelink',
            'phonelink_erase',
            'phonelink_lock',
            'phonelink_off',
            'phonelink_ring',
            'phonelink_setup',
            'photo',
            'photo_album',
            'photo_camera',
            'photo_filter',
            'photo_library',
            'photo_size_select_actual',
            'photo_size_select_large',
            'photo_size_select_small',
            'picture_as_pdf',
            'picture_in_picture',
            'picture_in_picture_alt',
            'pie_chart',
            'pie_chart_outlined',
            'pin_drop',
            'place',
            'play_arrow',
            'play_circle_filled',
            'play_circle_outline',
            'play_for_work',
            'playlist_add',
            'playlist_add_check',
            'playlist_play',
            'plus_one',
            'poll',
            'polymer',
            'pool',
            'portable_wifi_off',
            'portrait',
            'power',
            'power_input',
            'power_settings_new',
            'pregnant_woman',
            'present_to_all',
            'print',
            'priority_high',
            'public',
            'publish',
            'query_builder',
            'question_answer',
            'queue',
            'queue_music',
            'queue_play_next',
            'radio',
            'radio_button_checked',
            'radio_button_unchecked',
            'rate_review',
            'receipt',
            'recent_actors',
            'record_voice_over',
            'redeem',
            'redo',
            'refresh',
            'remove',
            'remove_circle',
            'remove_circle_outline',
            'remove_from_queue',
            'remove_red_eye',
            'remove_shopping_cart',
            'reorder',
            'repeat',
            'repeat_one',
            'replay',
            'replay_10',
            'replay_30',
            'replay_5',
            'reply',
            'reply_all',
            'report',
            'report_problem',
            'restaurant',
            'restaurant_menu',
            'restore',
            'restore_page',
            'ring_volume',
            'room',
            'room_service',
            'rotate_90_degrees_ccw',
            'rotate_left',
            'rotate_right',
            'rounded_corner',
            'router',
            'rowing',
            'rss_feed',
            'rv_hookup',
            'satellite',
            'save',
            'scanner',
            'schedule',
            'school',
            'screen_lock_landscape',
            'screen_lock_portrait',
            'screen_lock_rotation',
            'screen_rotation',
            'screen_share',
            'sd_card',
            'sd_storage',
            'search',
            'security',
            'select_all',
            'send',
            'sentiment_dissatisfied',
            'sentiment_neutral',
            'sentiment_satisfied',
            'sentiment_very_dissatisfied',
            'sentiment_very_satisfied',
            'settings',
            'settings_applications',
            'settings_backup_restore',
            'settings_bluetooth',
            'settings_brightness',
            'settings_cell',
            'settings_ethernet',
            'settings_input_antenna',
            'settings_input_component',
            'settings_input_composite',
            'settings_input_hdmi',
            'settings_input_svideo',
            'settings_overscan',
            'settings_phone',
            'settings_power',
            'settings_remote',
            'settings_system_daydream',
            'settings_voice',
            'share',
            'shop',
            'shop_two',
            'shopping_basket',
            'shopping_cart',
            'short_text',
            'show_chart',
            'shuffle',
            'signal_cellular_4_bar',
            'signal_cellular_connected_no_internet_4_bar',
            'signal_cellular_no_sim',
            'signal_cellular_null',
            'signal_cellular_off',
            'signal_wifi_4_bar',
            'signal_wifi_4_bar_lock',
            'signal_wifi_off',
            'sim_card',
            'sim_card_alert',
            'skip_next',
            'skip_previous',
            'slideshow',
            'slow_motion_video',
            'smartphone',
            'smoke_free',
            'smoking_rooms',
            'sms',
            'sms_failed',
            'snooze',
            'sort',
            'sort_by_alpha',
            'spa',
            'space_bar',
            'speaker',
            'speaker_group',
            'speaker_notes',
            'speaker_notes_off',
            'speaker_phone',
            'spellcheck',
            'star',
            'star_border',
            'star_half',
            'stars',
            'stay_current_landscape',
            'stay_current_portrait',
            'stay_primary_landscape',
            'stay_primary_portrait',
            'stop',
            'stop_screen_share',
            'storage',
            'store',
            'store_mall_directory',
            'straighten',
            'streetview',
            'strikethrough_s',
            'style',
            'subdirectory_arrow_left',
            'subdirectory_arrow_right',
            'subject',
            'subscriptions',
            'subtitles',
            'subway',
            'supervisor_account',
            'surround_sound',
            'swap_calls',
            'swap_horiz',
            'swap_vert',
            'swap_vertical_circle',
            'switch_camera',
            'switch_video',
            'sync',
            'sync_disabled',
            'sync_problem',
            'system_update',
            'system_update_alt',
            'tab',
            'tab_unselected',
            'tablet',
            'tablet_android',
            'tablet_mac',
            'tag_faces',
            'tap_and_play',
            'terrain',
            'text_fields',
            'text_format',
            'textsms',
            'texture',
            'theaters',
            'thumb_down',
            'thumb_up',
            'thumbs_up_down',
            'time_to_leave',
            'timelapse',
            'timeline',
            'timer',
            'timer_10',
            'timer_3',
            'timer_off',
            'title',
            'toc',
            'today',
            'toll',
            'tonality',
            'touch_app',
            'toys',
            'track_changes',
            'traffic',
            'train',
            'tram',
            'transfer_within_a_station',
            'transform',
            'translate',
            'trending_down',
            'trending_flat',
            'trending_up',
            'tune',
            'turned_in',
            'turned_in_not',
            'tv',
            'unarchive',
            'undo',
            'unfold_less',
            'unfold_more',
            'update',
            'usb',
            'verified_user',
            'vertical_align_bottom',
            'vertical_align_center',
            'vertical_align_top',
            'vibration',
            'video_call',
            'video_label',
            'video_library',
            'videocam',
            'videocam_off',
            'videogame_asset',
            'view_agenda',
            'view_array',
            'view_carousel',
            'view_column',
            'view_comfy',
            'view_compact',
            'view_day',
            'view_headline',
            'view_list',
            'view_module',
            'view_quilt',
            'view_stream',
            'view_week',
            'vignette',
            'visibility',
            'visibility_off',
            'voice_chat',
            'voicemail',
            'volume_down',
            'volume_mute',
            'volume_off',
            'volume_up',
            'vpn_key',
            'vpn_lock',
            'wallpaper',
            'warning',
            'watch',
            'watch_later',
            'wb_auto',
            'wb_cloudy',
            'wb_incandescent',
            'wb_iridescent',
            'wb_sunny',
            'wc',
            'web',
            'web_asset',
            'weekend',
            'whatshot',
            'widgets',
            'wifi',
            'wifi_lock',
            'wifi_tethering',
            'work',
            'wrap_text',
            'youtube_searched_for',
            'zoom_in',
            'zoom_out',
            'zoom_out_map'
        ];
        ?>

        <script type="text/html" id="tmpl-pp-form-builder-material-icon">
            <# if(typeof data.icon !== "undefined" && data.icon !== "") { #>
            <i class="pp-form-material-icons">{{data.icon}}</i>
            <# } #>
        </script>
        <?php

        printf('<div id="pp-form-material-icon-picker-tmpl-title" style="display:none;"><h2>%s</h2></div>', esc_html__('Select Icon', 'wp-user-avatar'));

        echo '<div id="pp-form-material-icon-picker-tmpl" style="text-align: center;display: none">';
        ?>
        <div class="pp-form-material-icon-wrap" data-material-icon="">
            <span class="pp-form-material-icon" style="display: inline-block;"><i class="pp-form-material-icons" style="width: 24px;height: 24px;"></i></span>
        </div>
        <?php
        foreach ($icons as $icon) {
            ?>
            <div class="pp-form-material-icon-wrap" data-material-icon="<?= $icon ?>">
                <i class="pp-form-material-icons"><?= $icon ?></i>
            </div>
            <?php
        }

        echo '</div>';
    }

    public function print_template()
    {
        $this->sidebar_fields_block_tmpl();
    }

    public function sidebar_fields_block_tmpl()
    {
        global $wp_version;

        $standard_field_title = esc_html__('Standard Fields', 'wp-user-avatar');
        $extra_field_title    = esc_html__('Extra Fields', 'wp-user-avatar');

        $defined_custom_field_title = esc_html__('Custom Fields', 'wp-user-avatar');
        $woo_billing_field_title    = esc_html__('WooCommerce Billing Address', 'wp-user-avatar');
        $woo_shipping_field_title   = esc_html__('WooCommerce Shipping Address', 'wp-user-avatar');

        $metabox_btn = '<button type="button" class="handlediv pp-metabox-handle"><span class="toggle-indicator"></span></button>';
        ?>
        <script type="text/html" id="tmpl-pp-form-builder-sidebar-fields-block">
            <# var id = 'pp-form-builder-' + data.fieldsBlockType + '-fields' #>
            <# var title = data.fieldsBlockType == 'standard' ? '<?= $standard_field_title; ?>' : ''; #>
            <# title = data.fieldsBlockType == 'extra' ? '<?= $extra_field_title; ?>' : title; #>
            <# title = data.fieldsBlockType == 'defined' ? '<?= $defined_custom_field_title; ?>' : title; #>
            <# title = data.fieldsBlockType == 'wc_billing' ? '<?= $woo_billing_field_title; ?>' : title; #>
            <# title = data.fieldsBlockType == 'wc_shipping' ? '<?= $woo_shipping_field_title; ?>' : title; #>
            <div class="postbox pp-postbox-wrap closed" id="{{ id }}">
                <div class="postbox-header">
                    <?php if (version_compare($wp_version, '5.5', '<')) echo $metabox_btn; ?>
                    <h2 class="hndle is-non-sortable">
                        <span>{{ title }}</span></h2>
                    <?php if (version_compare($wp_version, '5.5', '>=')) echo $metabox_btn; ?>
                </div>
                <div class="inside">
                    <ol class="pp-form-builder-field-type">
                        <# if(_.isEmpty(data.fields)) { #>
                        <div>
                            <?= sprintf(
                                esc_html__('No custom field available. %sClick here to create one%s.'),
                                '<a target="_blank" href="' . PPRESS_CUSTOM_FIELDS_SETTINGS_PAGE . '">', '</a>'
                            ) ?>
                        </div>
                        <# } else { #>
                        <# _.each(data.fields, function(field, key) {#>
                        <li class="pp-draggable-field">
                            <a href="#" class="button" data-field-category="{{{data.fieldsBlockType}}}" data-field-type="{{{ key }}}">{{{field.fieldTitle}}}</a>
                        </li>
                        <# }); #>
                        <# } #>
                    </ol>
                </div>
            </div>
        </script>
        <?php
    }

    public function builder_header()
    {
        settings_errors('pp_drag_drop_builder_notice');
        $title = FR::get_name($this->form_id, $this->form_type);
        ?>
        <div id="titlediv">
            <div id="titlewrap">
                <label class="screen-reader-text" id="title-prompt-text" for="title"><?php _e('Enter title here', 'wp-user-avatar'); ?></label>
                <input type="text" name="pp_form_title" size="30" value="<?= $title ?>" id="title">
                <a class="pp-form-save-changes button button-primary button-large" style="margin: 2px 0 0 10px;text-align: center;" href="#"><?php _e('Save Changes', 'wp-user-avatar'); ?></a>
            </div>
        </div>
        <?php
    }

    public function sidebar_section()
    {
        if ( ! $this->is_drag_drop_page()) return '';
        ob_start();
        ?>
        <div id="postbox-container-1" class="postbox-container">
            <div id="side-sortables" class="meta-box-sortables">
                <div class="pp-form-builder-sidebar-wrap">
                    <div id="pp-form-builder-sidebar-fields-block"></div>
                    <div class="pp-builder-action-btn-block">
                        <a class="pp-form-save-changes button button-primary button-large" style="margin: 0 10px 0 0;vertical-align: middle;" href="#">
                            <?php _e('Save Changes', 'wp-user-avatar'); ?>
                        </a>
                        <a href="<?php echo FormList::delete_url($this->form_id, $this->form_type) ?>" class="pp-form-delete button-link button-link-delete">
                            <?php _e('Delete Form', 'wp-user-avatar'); ?>
                        </a>
                    </div>
                </div>
            </div>

            <div class="pp-form-builder-field-settings-wrap">
                <!--  sidebar field settings goes here  -->
            </div>
        </div>
        <?php

        return ob_get_clean();
    }

    public function admin_page()
    {
        $theme_class_instance = FR::forge_class($this->form_id, $this->form_class, $this->form_type);

        if ( ! $theme_class_instance) {
            wp_safe_redirect(add_query_arg('form-type', $this->form_type, PPRESS_FORMS_SETTINGS_PAGE));
            exit;
        }

        $this->theme_class_instance = $theme_class_instance;

        ?>
        <div id="post-body-content">
            <?php $this->builder_header(); ?>
            <div id="pp-form-builder">
                <div class="pp-form-builder-body">
                    <div class="pp-builder-form-content"></div>
                </div>
            </div>
            <input id="pp-form-builder-fields-settings" type="hidden" name="pp_form_builder_fields_settings">
            <?= ppress_nonce_field(); ?>
        </div>
        <?php

        $this->meta_box();

        do_action('ppress_drag_drop_builder_admin_page');
    }

    public function registration_settings()
    {
        $wp_roles = ppress_get_editable_roles();
        $wp_roles = array_reduce(array_keys($wp_roles), function ($carry, $item) use ($wp_roles) {
            $carry[$item] = $wp_roles[$item]['name'];

            return $carry;
        }, []);

        $registration_metabox_settings = wp_list_sort(
            apply_filters('ppress_form_builder_meta_box_registration_settings', [
                [
                    'id'       => FR::SUCCESS_MESSAGE,
                    'type'     => 'text',
                    'label'    => esc_html__('Success Message', 'wp-user-avatar'),
                    'priority' => 5
                ],
                [
                    'id'          => FR::REGISTRATION_USER_ROLE,
                    'type'        => 'select',
                    'label'       => esc_html__('New User Role', 'wp-user-avatar'),
                    'description' => esc_html__('Role users registered through this form will be assigned.', 'wp-user-avatar'),
                    'options'     => $wp_roles,
                    'priority'    => 10
                ],
                [
                    'id'             => FR::DISABLE_USERNAME_REQUIREMENT,
                    'type'           => 'checkbox',
                    'label'          => esc_html__('Username Requirement', 'wp-user-avatar'),
                    'checkbox_label' => esc_html__('Check to disable username requirement', 'wp-user-avatar'),
                    'description'    => esc_html__('Disable requirement for users to enter a username during registration. Usernames will automatically be generated from their email addresses.', 'wp-user-avatar'),
                    'priority'       => 15
                ]
            ]),
            ['priority' => 'ASC']
        );

        $registration_metabox_settings['tab_title'] = esc_html__('Registration Settings', 'wp-user-avatar');

        add_filter('ppress_form_builder_meta_box_settings', function ($settings) use ($registration_metabox_settings) {
            $settings['registration_settings'] = $registration_metabox_settings;

            return $settings;
        });
    }

    public function edit_profile_settings()
    {
        $edit_profile_metabox_settings = wp_list_sort(
            apply_filters('ppress_form_builder_meta_box_edit_profile_settings', [
                [
                    'id'       => FR::SUCCESS_MESSAGE,
                    'type'     => 'text',
                    'label'    => esc_html__('Success Message', 'wp-user-avatar'),
                    'priority' => 5
                ]
            ]),
            ['priority' => 'ASC']
        );

        $edit_profile_metabox_settings['tab_title'] = esc_html__('Edit Profile Settings', 'wp-user-avatar');

        add_filter('ppress_form_builder_meta_box_settings', function ($settings) use ($edit_profile_metabox_settings) {
            $settings['edit_profile_settings'] = $edit_profile_metabox_settings;

            return $settings;
        });
    }

    public function login_settings()
    {
        $metabox_settings = wp_list_sort(
            apply_filters('ppress_form_builder_meta_box_login_settings', [
                [
                    'id'             => FR::PASSWORDLESS_LOGIN,
                    'type'           => 'checkbox',
                    'label'          => esc_html__('Passwordless Login', 'wp-user-avatar'),
                    'checkbox_label' => esc_html__('Check to make this a passwordless login form.', 'wp-user-avatar'),
                    'priority'       => 5
                ]
            ]),
            ['priority' => 'ASC']
        );

        $metabox_settings['tab_title'] = esc_html__('Login Settings', 'wp-user-avatar');

        add_filter('ppress_form_builder_meta_box_settings', function ($settings) use ($metabox_settings) {
            $settings['login_settings'] = $metabox_settings;

            return $settings;
        });
    }

    public function password_reset_settings()
    {
        $metabox_settings = wp_list_sort(
            apply_filters('ppress_form_builder_meta_box_password_reset_settings', [
                [
                    'id'       => FR::SUCCESS_MESSAGE,
                    'type'     => 'text',
                    'label'    => esc_html__('Success Message', 'wp-user-avatar'),
                    'priority' => 5
                ]
            ]),
            ['priority' => 'ASC']
        );

        $metabox_settings['tab_title'] = esc_html__('Password Reset Settings', 'wp-user-avatar');

        add_filter('ppress_form_builder_meta_box_settings', function ($settings) use ($metabox_settings) {
            $settings['password_reset_settings'] = $metabox_settings;

            return $settings;
        });
    }

    public function meta_box()
    {
        $method = str_replace('-', '_', $_GET['form-type']) . '_settings';
        if (method_exists($this, $method)) {
            $this->$method();
        }

        $submit_button_metabox_settings = wp_list_sort(
            apply_filters('ppress_form_builder_meta_box_submit_button_settings', [
                [
                    'id'       => 'submit_button_text',
                    'type'     => 'text',
                    'label'    => esc_html__('Label', 'wp-user-avatar'),
                    'priority' => 5
                ],
                [
                    'id'       => 'submit_button_processing_label',
                    'type'     => 'text',
                    'label'    => esc_html__('Processing Label', 'wp-user-avatar'),
                    'priority' => 10
                ]
            ]),
            ['priority' => 'ASC']
        );

        $submit_button_metabox_settings['tab_title'] = esc_html__('Submit Button', 'wp-user-avatar');

        $appearance_metabox_settings = wp_list_sort(
            apply_filters('ppress_form_builder_meta_box_appearance_settings', []),
            ['priority' => 'ASC']
        );

        $appearance_metabox_settings['tab_title'] = esc_html__('Appearance', 'wp-user-avatar');

        $colors_metabox_settings = wp_list_sort(
            apply_filters('ppress_form_builder_meta_box_colors_settings', []),
            ['priority' => 'ASC']
        );

        $colors_metabox_settings['tab_title'] = esc_html__('Colors', 'wp-user-avatar');

        $this->meta_box_settings = apply_filters('ppress_form_builder_meta_box_settings', [
            'appearance'    => $appearance_metabox_settings,
            'colors'        => $colors_metabox_settings,
            'submit_button' => $submit_button_metabox_settings
        ], $this->form_type, $this);

        if (in_array($this->form_type, [FR::USER_PROFILE_TYPE, FR::MEMBERS_DIRECTORY_TYPE])) {
            unset($this->meta_box_settings['submit_button']);
        }

        (new Metabox($this->meta_box_settings, $this->theme_class_instance, $this))->build();
    }

    public function js_wp_editor_enqueue()
    {
        wp_enqueue_script(
            'pp-wp-editor',
            PPRESS_ASSETS_URL . '/js/pp-wp-editor.js',
            ['jquery'],
            false,
            true
        );

        wp_localize_script('pp-wp-editor', 'ppWPEditor_globals', array(
            'url'                      => get_home_url(),
            'includes_url'             => includes_url(),
            'wpeditor_texttab_label'   => __('Text', 'wp-user-avatar'),
            'wpeditor_visualtab_label' => __('Visual', 'wp-user-avatar'),
            'wpeditor_addmedia_label'  => __('Add Media', 'wp-user-avatar')
        ));
    }

    public function js_wp_editor()
    {
        // Enable rich editing for this view (Overrides 'Disable the visual editor when writing' option for current user)
        add_filter('user_can_richedit', '__return_true');
        wp_enqueue_editor();
        wp_enqueue_editor();

        if ( ! empty($GLOBALS['post'])) {
            wp_enqueue_media(array('post' => $GLOBALS['post']->ID));
        } else {
            wp_enqueue_media();
        }
    }

    public static function get_instance()
    {
        static $instance = null;

        if (is_null($instance)) {
            $instance = new self();
        }

        return $instance;
    }
}