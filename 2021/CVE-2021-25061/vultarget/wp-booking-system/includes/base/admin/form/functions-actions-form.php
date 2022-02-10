<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Validates and handles the adding of the new form in the database
 *
 */
function wpbs_action_add_form()
{

    // Verify for nonce
    if (empty($_POST['wpbs_token']) || !wp_verify_nonce($_POST['wpbs_token'], 'wpbs_add_form')) {
        return;
    }

    // Verify for form name
    if (empty($_POST['form_name'])) {

        wpbs_admin_notices()->register_notice('form_name_missing', '<p>' . __('Please add a name for your new form.', 'wp-booking-system') . '</p>', 'error');
        wpbs_admin_notices()->display_notice('form_name_missing');

        return;

    }

    // Prepare form data to be inserted
    $form_data = array(
        'name' => sanitize_text_field($_POST['form_name']),
        'date_created' => current_time('Y-m-d H:i:s'),
        'date_modified' => current_time('Y-m-d H:i:s'),
        'status' => 'active',
        'fields' => array(),
    );

    // Insert form into the database
    $form_id = wpbs_insert_form($form_data);

    // If the form could not be inserted show a message to the user
    if (!$form_id) {

        wpbs_admin_notices()->register_notice('form_insert_false', '<p>' . __('Something went wrong. Could not create the form. Please try again.', 'wp-booking-system') . '</p>', 'error');
        wpbs_admin_notices()->display_notice('form_insert_false');

        return;

    }

    wpbs_add_form_meta($form_id, 'submit_button_label', __('Submit', 'wp-booking-system'));
    wpbs_add_form_meta($form_id, 'form_confirmation_type', 'message');
    wpbs_add_form_meta($form_id, 'form_confirmation_message', __('The form was successfully submitted.', 'wp-booking-system'));

    // Redirect to the edit page of the form with a success message
    wp_redirect(add_query_arg(array('page' => 'wpbs-forms', 'subpage' => 'edit-form', 'form_id' => $form_id, 'wpbs_message' => 'form_insert_success'), admin_url('admin.php')));
    exit;

}
add_action('wpbs_action_add_form', 'wpbs_action_add_form', 50);

/**
 * Validates and handles the editing of an existing form
 *
 */
function wpbs_action_edit_form()
{
    // Verify for nonce
    if (empty($_POST['wpbs_token']) || !wp_verify_nonce($_POST['wpbs_token'], 'wpbs_edit_form')) {
        return;
    }

    $_POST = stripslashes_deep($_POST);

    if (empty($_POST['form_id'])) {

        wpbs_admin_notices()->register_notice('form_update_failed', '<p>' . __('Something went wrong. Could not update the form.', 'wp-booking-system') . '</p>', 'error');
        wpbs_admin_notices()->display_notice('form_update_failed');

        return;

    }

    $settings = get_option('wpbs_settings', array());
    $active_languages = (!empty($settings['active_languages']) ? $settings['active_languages'] : array());

    /**
     * Prepare variables
     *
     */
    $form_id = absint($_POST['form_id']);
    $form_name = sanitize_text_field($_POST['form_name']);

    if (!empty($_POST['form_fields'])) {
        $form_fields = $_POST['form_fields'];
        $form_fields = stripslashes_deep($form_fields);

        foreach ($form_fields as &$form_field) {
            if ($form_field['type'] == 'html') {
                $form_field = _wpbs_array_wp_kses_post($form_field);
                $form_field = _wpbs_array_esc_attr_textarea_field($form_field);
            } else {
                $form_field = _wpbs_array_sanitize_text_field($form_field);
                $form_field = _wpbs_array_esc_attr_text_field($form_field);
            }
        }

    } else {
        $form_fields = array();
    }

    /**
     * Handle form object data
     *
     */

    // Get form
    $form = wpbs_get_form($form_id);

    // Update form
    $update_data = array(
        'name' => (!empty($form_name) ? $form_name : $form->get('name')),
        'date_modified' => current_time('Y-m-d H:i:s'),
        'fields' => $form_fields,
    );

    wpbs_update_form($form_id, $update_data);

    /**
     * Handle form meta
     *
     */

    // Form Fields ID Index
    wpbs_update_form_meta($form_id, 'wpbs_form_field_id_index', absint($_POST['wpbs_form_field_id_index']));

    // Form Meta Fields
    $meta_fields = array(
        'submit_button_label' => array('translations' => true, 'sanitization' => 'sanitize_text_field'),
        'admin_notification_enable' => array('translations' => false, 'sanitization' => 'sanitize_text_field'),
        'admin_notification_send_to' => array('translations' => false, 'sanitization' => 'sanitize_text_field'),
        'admin_notification_from_name' => array('translations' => false, 'sanitization' => 'sanitize_text_field'),
        'admin_notification_from_email' => array('translations' => false, 'sanitization' => 'sanitize_text_field'),
        'admin_notification_reply_to' => array('translations' => false, 'sanitization' => 'sanitize_text_field'),
        'admin_notification_subject' => array('translations' => false, 'sanitization' => 'sanitize_text_field'),
        'admin_notification_message' => array('translations' => false, 'sanitization' => 'sanitize_textarea_field'),
        'form_confirmation_type' => array('translations' => false, 'sanitization' => 'sanitize_text_field'),
        'form_confirmation_message' => array('translations' => true, 'sanitization' => 'sanitize_textarea_field'),
        'form_confirmation_redirect_url' => array('translations' => false, 'sanitization' => 'sanitize_text_field'),
    );

    // Add in strings
    foreach(wpbs_form_default_strings() as $key => $default){
        $meta_fields['form_strings_' . $key] = array('translations' => true, 'sanitization' => 'sanitize_text_field');
    }

    foreach ($meta_fields as $meta_field => $options) {
        if (!empty($_POST[$meta_field])) {
            wpbs_update_form_meta($form_id, $meta_field, $options['sanitization']($_POST[$meta_field]));
        } else {
            wpbs_delete_form_meta($form_id, $meta_field);
        }

        if ($options['translations'] == true) {
            foreach ($active_languages as $code) {

                if (!empty($_POST[$meta_field . '_translation_' . $code])) {
                    wpbs_update_form_meta($form_id, $meta_field . '_translation_' . $code, $options['sanitization']($_POST[$meta_field . '_translation_' . $code]));
                } else {
                    wpbs_delete_form_meta($form_id, $meta_field . '_translation_' . $code);
                }

            }
        }
    }

    /**
     * Action hook to save extra form form data
     *
     * @param array $_POST
     *
     */
    do_action('wpbs_save_form_data', $_POST);

    /**
     * Success redirect
     *
     */
    wp_redirect(add_query_arg(array('page' => 'wpbs-forms', 'subpage' => 'edit-form', 'form_id' => $form_id, 'wpbs_message' => 'form_edit_success'), admin_url('admin.php')));
    exit;

}
add_action('wpbs_action_edit_form', 'wpbs_action_edit_form', 50);

/**
 * Handles the trash form action, which changes the status of the form from active to trash
 *
 */
function wpbs_action_trash_form()
{

    // Verify for nonce
    if (empty($_GET['wpbs_token']) || !wp_verify_nonce($_GET['wpbs_token'], 'wpbs_trash_form')) {
        return;
    }

    if (empty($_GET['form_id'])) {
        return;
    }

    $form_id = absint($_GET['form_id']);

    $form_data = array(
        'status' => 'trash',
    );

    $updated = wpbs_update_form($form_id, $form_data);

    if (!$updated) {
        return;
    }

    // Redirect to the current page
    wp_redirect(add_query_arg(array('page' => 'wpbs-forms', 'form_status' => 'active', 'wpbs_message' => 'form_trash_success'), admin_url('admin.php')));
    exit;

}
add_action('wpbs_action_trash_form', 'wpbs_action_trash_form', 50);

/**
 * Handles the restore form action, which changes the status of the form from trash to active
 *
 */
function wpbs_action_restore_form()
{

    // Verify for nonce
    if (empty($_GET['wpbs_token']) || !wp_verify_nonce($_GET['wpbs_token'], 'wpbs_restore_form')) {
        return;
    }

    if (empty($_GET['form_id'])) {
        return;
    }

    $form_id = absint($_GET['form_id']);

    $form_data = array(
        'status' => 'active',
    );

    $updated = wpbs_update_form($form_id, $form_data);

    if (!$updated) {
        return;
    }

    // Redirect to the current page
    wp_redirect(add_query_arg(array('page' => 'wpbs-forms', 'form_status' => 'trash', 'wpbs_message' => 'form_restore_success'), admin_url('admin.php')));
    exit;

}
add_action('wpbs_action_restore_form', 'wpbs_action_restore_form', 50);

/**
 * Handles the delete form action, which removes all form data, legend items and events data
 * associated with the form
 *
 */
function wpbs_action_delete_form()
{

    // Verify for nonce
    if (empty($_GET['wpbs_token']) || !wp_verify_nonce($_GET['wpbs_token'], 'wpbs_delete_form')) {
        return;
    }

    if (empty($_GET['form_id'])) {
        return;
    }

    $form_id = absint($_GET['form_id']);

    /**
     * Delete the form
     *
     */
    $deleted = wpbs_delete_form($form_id);

    if (!$deleted) {
        return;
    }

    foreach (wpbs_get_form_meta($form_id) as $key => $value) {
        wpbs_delete_form_meta($form_id, $key);
    }

    // Redirect to the current page
    wp_redirect(add_query_arg(array('page' => 'wpbs-forms', 'form_status' => 'trash', 'wpbs_message' => 'form_delete_success'), admin_url('admin.php')));
    exit;

}
add_action('wpbs_action_delete_form', 'wpbs_action_delete_form', 50);
