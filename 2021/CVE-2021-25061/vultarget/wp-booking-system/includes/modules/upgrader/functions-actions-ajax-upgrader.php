<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Handles the migration of the calendars from the old plugins to the new structure
 *
 */
function wpbs_action_ajax_migrate_calendars()
{

    if (empty($_POST['token']) || !wp_verify_nonce($_POST['token'], 'wpbs_upgrader')) {
        echo json_encode(array('success' => 0));
        wp_die();
    }

    $do_upgrade = wpbs_process_upgrade_from();

    if (false == $do_upgrade) {
        echo json_encode(array('success' => 0));
        wp_die();
    }

    /**
     * Verify for the existance of calendars
     *
     */
    $calendars = wpbs_get_calendars();

    if (!empty($calendars)) {
        echo json_encode(array('success' => 0));
        wp_die();
    }

    /**
     * Handle upgrade from old premium version
     *
     */

    global $wpdb;

    $old_calendar_ids = $wpdb->get_results("SELECT calendarID FROM {$wpdb->prefix}bs_calendars", ARRAY_N);

    if (!empty($old_calendar_ids)) {

        // Because we do the upgrade in steps we need to preserve the old legend item
        // keys with the new one's ids
        $legend_items_relationships = array();

        foreach ($old_calendar_ids as $calendar_id) {

            $calendar_id = $calendar_id[0];
            $calendar = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}bs_calendars WHERE calendarID = '{$calendar_id}'", ARRAY_A);

            if (is_null($calendar)) {
                continue;
            }

            /**
             * Handle Calendar
             *
             */
            $calendar_data = array(
                'id' => $calendar['calendarID'],
                'name' => $calendar['calendarTitle'],
                'date_created' => date('Y-m-d H:i:s', $calendar['createdDate']),
                'date_modified' => date('Y-m-d H:i:s', $calendar['modifiedDate']),
                'status' => 'active',
                'ical_hash' => (!empty($calendar['calendarHash']) ? $calendar['calendarHash'] : wpbs_generate_ical_hash()),
            );

            $calendar_id = wpbs_insert_calendar($calendar_data);

            /**
             * Handle Legend Items
             *
             */
            $old_legend_items = json_decode($calendar['calendarLegend'], true);
            $old_legend_items = (!empty($old_legend_items) && is_array($old_legend_items) ? $old_legend_items : array());

            // Legend items order
            $legend_items_order = array();

            // Legend items that should be synced as booked
            $legend_items_booked = array();

            foreach ($old_legend_items as $key => $old_legend_item) {

                // Set color
                $color = array();

                // Add primary color
                if (!empty($old_legend_item['color'])) {
                    $color[] = $old_legend_item['color'];
                }

                // Add secondary color
                if (!empty($old_legend_item['splitColor'])) {
                    $color[] = $old_legend_item['splitColor'];
                }

                if ((!empty($old_legend_item['auto-pending']) && $old_legend_item['auto-pending'] == 'yes') || $old_legend_item['name']['default'] == 'Booked') {
                    $auto_pending = 'booked';
                } elseif ($old_legend_item['name']['default'] == 'Changeover 1') {
                    $auto_pending = 'changeover_start';
                } elseif ($old_legend_item['name']['default'] == 'Changeover 2') {
                    $auto_pending = 'changeover_end';
                } else {
                    $auto_pending = '';
                }

                $legend_item_data = array(
                    'name' => $old_legend_item['name']['default'],
                    'type' => (empty($old_legend_item['splitColor']) ? 'single' : 'split'),
                    'color' => $color,
                    'is_default' => ($key == 'default' ? 1 : 0),
                    'is_visible' => (empty($old_legend_item['hide']) ? 1 : 0),
                    'calendar_id' => $calendar_id,
                    'is_bookable' => ((!empty($old_legend_item['bookable']) && $old_legend_item['bookable'] == 'yes') ? 1 : 0),
                    'auto_pending' => $auto_pending,
                );

                $legend_item_id = wpbs_insert_legend_item($legend_item_data);

                if ($old_legend_item['name']['default'] == 'Available') {
                    // Add default translations
                    $translations = array(
                        'en' => 'Available',
                        'bg' => 'Свободен',
                        'ca' => 'Disponible',
                        'hr' => 'Dostupno',
                        'cz' => 'Dostupný',
                        'da' => 'Ledig',
                        'nl' => 'Beschikbaar',
                        'et' => 'Saadaval',
                        'fi' => 'Käytettävissä',
                        'fr' => 'Disponible',
                        'gl' => 'Dispoñible',
                        'de' => 'Verfügbar',
                        'el' => 'Διαθέσιμος',
                        'hu' => 'Elérhető',
                        'it' => 'Disponibile',
                        'jp' => '利用可能',
                        'lt' => 'Yra',
                        'no' => 'Tilgjengelig',
                        'pl' => 'Dostępny',
                        'pt' => 'Disponível',
                        'ro' => 'Disponibil',
                        'ru' => 'Доступный',
                        'sr' => 'Доступан',
                        'sk' => 'Dostupný',
                        'sl' => 'Veljaven',
                        'es' => 'Disponible',
                        'sv' => 'Tillgängliga',
                        'tr' => 'Mevcut',
                        'ua' => 'Доступні',
                    );
                    foreach ($translations as $language_code => $legend_translation) {
                        wpbs_add_legend_item_meta($legend_item_id, 'translation_' . $language_code, $legend_translation);
                    }
                }

                if ($old_legend_item['name']['default'] == 'Booked') {
                    // Add default translations
                    $translations = array(
                        'en' => 'Booked',
                        'bg' => 'Резервирано',
                        'ca' => 'Reservat',
                        'hr' => 'Rezerviran',
                        'cz' => 'Rezervováno',
                        'da' => 'Reserveret',
                        'nl' => 'Geboekt',
                        'et' => 'Broneeritud',
                        'fi' => 'Varattu',
                        'fr' => 'Réservé',
                        'gl' => 'Reservado',
                        'de' => 'Gebucht',
                        'el' => 'Κράτηση',
                        'hu' => 'Foglalt',
                        'it' => 'Riservato',
                        'jp' => '予約済み',
                        'lt' => 'Užsakyta',
                        'no' => 'Bestilt',
                        'pl' => 'Zarezerwowane',
                        'pt' => 'Reservado',
                        'ro' => 'Rezervat',
                        'ru' => 'Бронирования',
                        'sr' => 'Резервисан',
                        'sk' => 'Rezervovaný',
                        'sl' => 'Rezervirano',
                        'es' => 'Reservado',
                        'sv' => 'Bokad',
                        'tr' => 'Rezervasyon',
                        'ua' => 'Забронювали',
                    );
                    foreach ($translations as $language_code => $legend_translation) {
                        wpbs_add_legend_item_meta($legend_item_id, 'translation_' . $language_code, $legend_translation);
                    }
                }

                if ($legend_item_id) {

                    // Push to legend items order
                    $legend_items_order[] = $legend_item_id;

                    // Push to sync as booked
                    if (!empty($old_legend_item['sync']) && $old_legend_item['sync'] == 'yes') {
                        $legend_items_booked[] = $legend_item_id;
                    }

                    $legend_items_relationships[$calendar_id][$key] = $legend_item_id;

                }

            }

            // Save the legend items relations ships into a transient
            set_transient('wpbs_upgrader_legend_items_relationships', $legend_items_relationships, HOUR_IN_SECONDS);

            /**
             * Handle Calendar Metadata
             *
             */
            $calendar_options = (!empty($calendar['calendarOptions']) ? json_decode($calendar['calendarOptions'], true) : array());
            $calendar_options = (!empty($calendar_options) && is_array($calendar_options) ? $calendar_options : array());

            // Add legend items order
            wpbs_update_calendar_meta($calendar_id, 'legend_items_sort_order', $legend_items_order);

            // Add legend items order
            wpbs_update_calendar_meta($calendar_id, 'ical_export_legend_items', $legend_items_booked);

            // Add calendar link options
            if (!empty($calendar_options['calendarHyperlinks']['type'])) {
                wpbs_update_calendar_meta($calendar_id, 'calendar_link_type', sanitize_text_field($calendar_options['calendarHyperlinks']['type']));
            }

            if (!empty($calendar_options['calendarHyperlinks']['type']) && $calendar_options['calendarHyperlinks']['type'] == 'internal' && !empty($calendar_options['calendarHyperlinks']['link'])) {
                wpbs_update_calendar_meta($calendar_id, 'calendar_link_internal', absint($calendar_options['calendarHyperlinks']['link']));
            }

            if (!empty($calendar_options['calendarHyperlinks']['type']) && $calendar_options['calendarHyperlinks']['type'] == 'external' && !empty($calendar_options['calendarHyperlinks']['link'])) {
                wpbs_update_calendar_meta($calendar_id, 'calendar_link_external', $calendar_options['calendarHyperlinks']['link']);
            }

            // Add user premissions
            $calendar_users = (!empty($calendar['calendarUsers']) && is_array(json_decode($calendar['calendarUsers'], true)) ? json_decode($calendar['calendarUsers'], true) : array());

            foreach ($calendar_users as $user_id) {

                wpbs_add_calendar_meta($calendar_id, 'user_permission', absint($user_id));

            }

        }

    }

    echo json_encode(array('success' => 1));
    wp_die();

}
add_action('wp_ajax_wpbs_action_ajax_migrate_calendars', 'wpbs_action_ajax_migrate_calendars');

/**
 * Handles the migration of the forms from the old plugins to the new structure
 *
 */
function wpbs_action_ajax_migrate_forms()
{

    if (empty($_POST['token']) || !wp_verify_nonce($_POST['token'], 'wpbs_upgrader')) {
        echo json_encode(array('success' => 0));
        wp_die();
    }

    $do_upgrade = wpbs_process_upgrade_from();

    if (false == $do_upgrade) {
        echo json_encode(array('success' => 0));
        wp_die();
    }

    /**
     * Verify for the existance of calendars
     *
     */
    $calendars = wpbs_get_forms();

    if (!empty($calendars)) {
        echo json_encode(array('success' => 0));
        wp_die();
    }

    /**
     * Handle upgrade from old version
     *
     */

    global $wpdb;

    $old_form_ids = $wpdb->get_results("SELECT formID FROM {$wpdb->prefix}bs_forms", ARRAY_N);

    if (!empty($old_form_ids)) {

        foreach ($old_form_ids as $form_id) {

            $form_id = $form_id[0];
            $form = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}bs_forms WHERE formID = '{$form_id}'", ARRAY_A);

            if (is_null($form)) {
                continue;
            }

            // Get old form data
            $old_form_data = json_decode($form['formData'], true);

            if (empty($old_form_data)) {
                continue;
            }

            $new_form_data = [];

            foreach ($old_form_data as $old_field) {

                $field = [];

                // Add field types
                $field['type'] = $old_field['fieldType'];
                $field['id'] = $old_field['fieldId'];

                $field['values']['default']['label'] = $old_field['fieldName'];
                $field['values']['default']['required'] = (($old_field['fieldRequired'] == 'true') ? 'on' : '');

                // Handle translations
                if (isset($old_field['fieldLanguages']) && !empty($old_field['fieldLanguages'])) {
                    foreach ($old_field['fieldLanguages'] as $language => $translation) {
                        $field['values'][$language]['label'] = $translation;
                    }
                }

                // Handle Option Values
                if (in_array($old_field['fieldType'], array('checkbox', 'radio', 'dropdown'))) {
                    if (isset($old_field['fieldOptions']) && !empty($old_field['fieldOptions'])) {
                        $fieldOptions = $old_field['fieldOptions'];
                        $fieldOptions = explode('|', $fieldOptions);
                        foreach ($fieldOptions as $option) {
                            $field['values']['default']['options'][] = trim($option);
                        }
                    }

                    // Handle Option Values Translations
                    if (isset($old_field['fieldOptionsLanguages']) && !empty($old_field['fieldOptionsLanguages'])) {
                        foreach ($old_field['fieldOptionsLanguages'] as $language => $translation) {
                            $fieldOptions = $translation;
                            $fieldOptions = explode('|', $fieldOptions);
                            $field['values'][$language]['options'] = [];
                            foreach ($fieldOptions as $option) {
                                $field['values'][$language]['options'][] = trim($option);
                            }
                        }
                    }
                }

                // Handle HTML Fields
                if ($old_field['fieldType'] == 'html') {
                    $field['values']['default']['value'] = $old_field['fieldHTML'];
                }

                $new_form_data[] = $field;

            }

            /**
             * Handle Form
             *
             */
            $form_data = array(
                'id' => $form['formID'],
                'name' => $form['formTitle'],
                'date_created' => date('Y-m-d H:i:s', time()),
                'date_modified' => date('Y-m-d H:i:s', time()),
                'status' => 'active',
                'fields' => $new_form_data,
            );

            $form_id = wpbs_insert_form($form_data);

            // Update Field Index
            wpbs_add_form_meta($form_id, 'wpbs_form_field_id_index', (count($new_form_data) + 1));

            // Get form settings
            $old_form_settings = json_decode($form['formOptions'], true);

            // Add Button label
            if (isset($old_form_settings['submitLabel']) && !empty($old_form_settings['submitLabel'])) {

                foreach ($old_form_settings['submitLabel'] as $language => $button_label) {
                    if ($language == 'default') {
                        wpbs_add_form_meta($form_id, 'submit_button_label', $button_label);
                    } else {
                        wpbs_add_form_meta($form_id, 'submit_button_label_translation_' . $language, $button_label);
                    }
                }
            }

            // Add Thank You Message
            wpbs_add_form_meta($form_id, 'form_confirmation_type', 'message');
            if (isset($old_form_settings['confirmationMessage']) && !empty($old_form_settings['confirmationMessage'])) {
                wpbs_add_form_meta($form_id, 'form_confirmation_message', $old_form_settings['confirmationMessage']);
            } else {
                wpbs_add_form_meta($form_id, 'form_confirmation_message', 'The form was successfully submitted.');
            }

            // Admin Notification
            if (isset($old_form_settings['sendTo']) && !empty($old_form_settings['sendTo'])) {
                wpbs_add_form_meta($form_id, 'admin_notification_enable', 'on');
                wpbs_add_form_meta($form_id, 'admin_notification_send_to', $old_form_settings['sendTo']);
                wpbs_add_form_meta($form_id, 'admin_notification_subject', 'New Booking');
                wpbs_add_form_meta($form_id, 'admin_notification_message', '{All Fields}');
            }

        }

    }

    echo json_encode(array('success' => 1));
    wp_die();

}
add_action('wp_ajax_wpbs_action_ajax_migrate_forms', 'wpbs_action_ajax_migrate_forms');

/**
 * Handles the migration of the bookings from the old plugins to the new structure
 *
 */
function wpbs_action_ajax_migrate_bookings()
{

    // if (empty($_POST['token']) || !wp_verify_nonce($_POST['token'], 'wpbs_upgrader')) {
    //     echo json_encode(array('success' => 0));
    //     wp_die();
    // }

    // $do_upgrade = wpbs_process_upgrade_from();

    // if (false == $do_upgrade) {
    //     echo json_encode(array('success' => 0));
    //     wp_die();
    // }

    /**
     * Verify for the existance of bookings
     *
     */
    $bookings = wpbs_get_bookings();

    if (!empty($bookings)) {
        echo json_encode(array('success' => 0));
        wp_die();
    }

    /**
     * Handle upgrade from old version
     *
     */

    global $wpdb;

    $old_booking_ids = $wpdb->get_results("SELECT bookingID FROM {$wpdb->prefix}bs_bookings", ARRAY_N);

    if (!empty($old_booking_ids)) {

        foreach ($old_booking_ids as $booking_id) {

            $booking_id = $booking_id[0];
            $booking = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}bs_bookings WHERE bookingID = '{$booking_id}'", ARRAY_A);

            if (is_null($booking)) {
                continue;
            }

            $form_id = $booking['formID'];

            $old_booking_data = json_decode($booking['bookingData'], true);

            $form = wpbs_get_form($form_id);

            $form_fields = $form->get('fields');

            $submitted_language = isset($old_booking_data['submittedLanguage']) ? $old_booking_data['submittedLanguage'] : 'default';

            $new_booking_data = [];
            foreach ($old_booking_data as $field_id => $user_value) {

                $new_booking_field = null;

                foreach ($form_fields as $field) {

                    if (isset($field['values'][$submitted_language]) && $field['values'][$submitted_language]['label'] == $field_id) {
                        $new_booking_field = $field;
                        break;
                    } elseif ($field['values']['default']['label'] == $field_id) {
                        $new_booking_field = $field;
                        break;
                    }

                }

                if ($new_booking_field !== null) {
                    $new_booking_field['user_value'] = $user_value;
                    $new_booking_data[] = $new_booking_field;
                }

            }

            /**
             * Handle Booking
             *
             */
            $booking_data = array(
                'id' => $booking['bookingID'],
                'calendar_id' => $booking['calendarID'],
                'form_id' => $booking['formID'],
                'start_date' => date('Y-m-d H:i:s', $booking['startDate']),
                'end_date' => date('Y-m-d H:i:s', $booking['endDate']),
                'fields' => $new_booking_data,
                'status' => $booking['bookingStatus'],
                'is_read' => $booking['bookingRead'],
                'date_created' => date('Y-m-d H:i:s', $booking['createdDate']),
                'date_modified' => date('Y-m-d H:i:s', $booking['createdDate']),

            );

            $booking_id = wpbs_insert_booking($booking_data);

        }

    }

    echo json_encode(array('success' => 1));
    wp_die();

}
add_action('wp_ajax_wpbs_action_ajax_migrate_bookings', 'wpbs_action_ajax_migrate_bookings');

/**
 * Handles the migration of the events from the old plugins to the new structure
 *
 */
function wpbs_action_ajax_migrate_events()
{

    if (empty($_POST['token']) || !wp_verify_nonce($_POST['token'], 'wpbs_upgrader')) {
        echo json_encode(array('success' => 0));
        wp_die();
    }

    $do_upgrade = wpbs_process_upgrade_from();

    if (false == $do_upgrade) {
        echo json_encode(array('success' => 0));
        wp_die();
    }

    /**
     * Handle upgrade from old version
     *
     */

    global $wpdb;

    // Get legend items relationships
    $legend_items_relationships = get_transient('wpbs_upgrader_legend_items_relationships');

    // Get old calendars
    $old_calendar_ids = $wpdb->get_results("SELECT calendarID FROM {$wpdb->prefix}bs_calendars", ARRAY_N);

    if (!empty($old_calendar_ids)) {

        foreach ($old_calendar_ids as $calendar_id) {

            $calendar_id = $calendar_id[0];
            $calendar = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}bs_calendars WHERE calendarID = '{$calendar_id}'", ARRAY_A);

            if (is_null($calendar)) {
                continue;
            }

            // Check to see if the new calendar was created with the same id
            $new_calendar = wpbs_get_calendar($calendar_id);

            if (is_null($new_calendar)) {
                continue;
            }

            // Set calendar data
            $calendar_data = json_decode($calendar['calendarData'], true);

            foreach ($calendar_data as $year => $months) {

                foreach ($months as $month => $date_values) {

                    /**
                     * Handle the adding of the Events based on the legend item. It also adds the
                     * description for that particular date.
                     *
                     */
                    foreach ($date_values as $key => $old_legend_item_id) {

                        if (false !== strpos($key, 'description')) {
                            continue;
                        }

                        $event_data = array(
                            'calendar_id' => $calendar_id,
                            'date_year' => $year,
                            'date_month' => $month,
                            'date_day' => $key,
                            'description' => (isset($date_values['description-' . $key]) ? _wpbs_replace_custom($date_values['description-' . $key]) : ''),
                            'tooltip' => (isset($date_values['description-' . $key]) ? _wpbs_replace_custom($date_values['description-' . $key]) : ''),
                            'legend_item_id' => (!empty($legend_items_relationships[$calendar_id][$old_legend_item_id]) ? $legend_items_relationships[$calendar_id][$old_legend_item_id] : 0),
                        );

                        // Insert the event
                        $event_id = wpbs_insert_event($event_data);

                        // Unset the legend item value from the old data
                        unset($date_values[$key]);

                        if (isset($date_values['description-' . $key])) {
                            unset($date_values['description-' . $key]);
                        }

                    }

                    /**
                     * Adds the events that only have the description available
                     * At this moment only events with description should remain in the array
                     *
                     */
                    if (!empty($date_values)) {

                        foreach ($date_values as $key => $old_legend_item_id) {

                            if (false === strpos($key, 'description')) {
                                continue;
                            }

                            $event_data = array(
                                'calendar_id' => $calendar_id,
                                'date_year' => $year,
                                'date_month' => $month,
                                'date_day' => absint(str_replace('description-', '', $key)),
                                'description' => _wpbs_replace_custom($date_values[$key]),
                                'tooltip' => _wpbs_replace_custom($date_values[$key]),
                                'legend_item_id' => 0,
                            );

                            // Insert the event
                            $event_id = wpbs_insert_event($event_data);

                        }

                    }

                }

            }

        }

    }

    echo json_encode(array('success' => 1));
    wp_die();

}
add_action('wp_ajax_wpbs_action_ajax_migrate_events', 'wpbs_action_ajax_migrate_events');

/**
 * Handles the migration of the general settings from the old plugins to the new structure
 *
 */
function wpbs_action_ajax_migrate_general_settings()
{

    if (empty($_POST['token']) || !wp_verify_nonce($_POST['token'], 'wpbs_upgrader')) {
        echo json_encode(array('success' => 0));
        wp_die();
    }

    $do_upgrade = wpbs_process_upgrade_from();

    if (false == $do_upgrade) {
        echo json_encode(array('success' => 0));
        wp_die();
    }

    /**
     * Get old settings
     *
     */
    $old_settings = get_option('wpbs-options', array());

    if (!empty($old_settings)) {
        $old_settings = stripslashes_deep(json_decode($old_settings, true));
    }

    /**
     * Get old language settings
     *
     */
    $old_languages = get_option('wpbs-languages', array());

    if (!empty($old_languages)) {
        $old_languages = stripslashes_deep(json_decode($old_languages, true));
    }

    /**
     * Set new settings
     *
     */
    $new_settings = array();

    // User roles
    if (!empty($old_settings['role_slug'])) {

        $editable_roles = array_reverse(array_keys(get_editable_roles()));

        if (in_array($old_settings['role_slug'], $editable_roles)) {

            foreach ($editable_roles as $key => $role) {

                if ($role == $old_settings['role_slug']) {
                    break;
                }

                unset($editable_roles[$key]);

            }

        }

        $new_settings['user_role_permissions'] = array_reverse($editable_roles);

    }

    // Languages
    if (!empty($old_languages)) {
        $new_settings['active_languages'] = array_keys($old_languages);
    }

    // Selection Color
    if (!empty($old_settings['selectedColor']) && $old_settings['selectedColor'] != '#3399cc') {
        $new_settings['booking_selection_hover_color'] = $old_settings['selectedColor'];
    }

    // Captcha
    if (!empty($old_settings['recaptcha_public'])) {
        $new_settings['recaptcha_v2_site_key'] = $old_settings['recaptcha_public'];
    }

    if (!empty($old_settings['recaptcha_secret'])) {
        $new_settings['recaptcha_v2_secret_key'] = $old_settings['recaptcha_secret'];
    }

    $new_settings['ical_refresh_times'] = 'hourly';

    // Add the settings
    update_option('wpbs_settings', $new_settings);

    echo json_encode(array('success' => 1));
    wp_die();

}
add_action('wp_ajax_wpbs_action_ajax_migrate_general_settings', 'wpbs_action_ajax_migrate_general_settings');

/**
 * Handles the migration of the general settings from the old plugins to the new structure
 *
 */
function wpbs_action_ajax_migrate_finishing_up()
{

    if (empty($_POST['token']) || !wp_verify_nonce($_POST['token'], 'wpbs_upgrader')) {
        echo json_encode(array('success' => 0));
        wp_die();
    }

    // Add the option that the upgrader has migrated the data
    update_option('wpbs_upgrade_5_0_0', 1);

    echo json_encode(array('success' => 1));
    wp_die();

}
add_action('wp_ajax_wpbs_action_ajax_migrate_finishing_up', 'wpbs_action_ajax_migrate_finishing_up');
