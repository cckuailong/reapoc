<?php

/**
 * CF7 Extension
 *
 * @package NotificationX\Extensions
 */

namespace NotificationX\Extensions\WPF;

use NotificationX\Core\Rules;
use NotificationX\GetInstance;
use NotificationX\Extensions\Extension;
use NotificationX\Extensions\GlobalFields;

/**
 * WPForms Extension
 */
class WPForms extends Extension {
    /**
     * Instance of WPForms
     *
     * @var WPForms
     */
    use GetInstance;

    public $priority        = 10;
    public $id              = 'wpf';
    public $img             = '';
    public $doc_link        = 'https://notificationx.com/docs/contact-form-submission-alert/';
    public $types           = 'form';
    public $module          = 'modules_wpf';
    public $module_priority = 9;
    public $class           = '\WPForms_Form_Handler';

    /**
     * Initially Invoked when initialized.
     */
    public function __construct() {
        $this->title = __('WPForms', 'notificationx');
        $this->module_title = __('WPForms', 'notificationx');
        parent::__construct();
    }

    public function init() {
        parent::init();
        add_action('wpforms_process_complete', array($this, 'save_new_records'), 10, 4);
    }

    public function init_fields(){
        parent::init_fields();
        add_filter('nx_form_list', [$this, 'nx_form_list'], 9);

    }

    /**
     * This functions is hooked
     *
     * @return void
     */
    public function admin_actions() {
        parent::admin_actions();
        add_filter("nx_can_entry_{$this->id}", array($this, 'can_entry'), 10, 3);
    }
    /**
     * This functions is hooked
     *
     * @hooked nx_public_action
     * @return void
     */
    public function public_actions() {
        parent::public_actions();

        add_filter("nx_filtered_data_{$this->id}", array($this, 'filter_by_form'), 11, 3);
    }

    public function source_error_message($messages) {
        if (!$this->class_exists()) {
            $url = admin_url('plugin-install.php?s=wp+form&tab=search&type=term');
            $messages[$this->id] = [
                'message' => sprintf( '%s <a href="%s" target="_blank">%s</a> %s',
                    __( 'You have to install', 'notificationx' ),
                    $url,
                    __( 'WP Forms', 'notificationx' ),
                    __( 'plugin first.', 'notificationx' )
                ),
                'html' => true,
                'type' => 'error',
                'rules' => Rules::is('source', $this->id),
            ];
        }
        return $messages;
    }

    public function nx_form_list($forms) {
        $_forms = GlobalFields::get_instance()->normalize_fields($this->get_forms(), 'source', $this->id);
        return array_merge($_forms, $forms);
    }

    public function get_forms() {
        $args = array(
            'post_type' => 'wpforms',
            'order' => 'ASC',
            'posts_per_page' => -1,
        );
        $the_query = get_posts($args);
        $forms = [];
        if (!empty($the_query)) {
            foreach ($the_query as $form) {
                $key = $this->key($form->ID);
                $forms[$key] = $form->post_title;
            }
        }
        wp_reset_postdata();
        return $forms;
    }

    public function restResponse($args) {
        if (isset($args['form_id'])) {
            $form_id = intval($args['form_id']);

            $form = get_post($form_id);

            $keys = $this->keys_generator($form->post_content);

            $returned_keys = array();

            if (is_array($keys) && !empty($keys)) {
                foreach ($keys as $key => $value) {
                    $returned_keys[] = array(
                        // 'text' => ucwords( str_replace( '_', ' ', str_replace( '-', ' ', $key ) ) ),
                        'label' => $value,
                        'value' => "tag_$key",
                    );
                }

                return $returned_keys;
            }
        }
        wp_send_json_error([]);
    }

    public function check_label($field) {
        $returned_field = '';
        if (isset($field['label']) && !empty($field['label'])) {
            $returned_field = $field['label'];
            return $returned_field;
        }
        if (isset($field['type'])) {
            $returned_field = ucfirst($field['type']);
            return $returned_field;
        }
        return $returned_field;
    }

    public function keys_generator($fieldsString) {
        $fields = array();
        $fieldsdata = json_decode($fieldsString, true);
        if (!empty($fieldsdata) && isset($fieldsdata['fields']) && !empty($fieldsdata['fields'])) {
            foreach ($fieldsdata['fields'] as $key => $fielditem) {
                // if (NotificationX_Helper::filter_contactform_key_names($fielditem['label'])){
                if (isset($fielditem['type']) && $fielditem['type'] === 'name') {
                    $format = explode('-',  $fielditem['format']);
                    foreach ($format as $fKey) {
                        $fields[$key . '_' . $fKey . '_name'] = ucfirst($fKey) . ' Name';
                    }
                }
                $fields[$key . "_" . $fielditem['type']] = $this->check_label($fielditem);
                // }
            }
        }
        return $fields;
    }

    public function save_new_records($fields, $entry, $form_data, $entry_id) {
        foreach ($fields as $field) {
            if ($field['type'] === 'checkbox') {
                continue;
            }
            if ($field['type'] === 'name') {
                if (!empty($entry['fields'][$field['id']]) && is_array($entry['fields'][$field['id']])) {
                    foreach ($entry['fields'][$field['id']] as $nKey => $n) {
                        $data[$field['id'] . '_' . $nKey . '_name'] = $n;
                    }
                }
            }
            if ($field['type'] === 'email') {
                $data['email'] = $field['value'];
            }
            $data[$field['id'] . '_' . $field['type']] = $field['value'];
        }
        $data['title'] = $form_data['settings']['form_title'];
        $data['timestamp'] = time();
        $data['id'] = $form_data['id'];
        if (!empty($data)) {
            $key = $this->key($form_data['id']);
            $this->save([
                'source'    => $this->id,
                'entry_key' => $key,
                'data'      => $data,
            ]);
            return true;
        }
        return false;
    }

    public function key($key) {
        $key = $this->id . '_' . $key;
        return $key;
    }

    /**
     * Limit entry by selected form in 'Select a Form';
     *
     * @param bool $return
     * @param array $entry
     * @param array $settings
     * @return boolean
     */
    public function can_entry($return, $entry, $settings){
        if(!empty($settings['form_list']) && !empty($entry['entry_key'])){
            $selected_form = $settings['form_list'];
            $form_id = $entry['entry_key'];
            if($selected_form != $form_id){
                return false;
            }

        }
        return $return;
    }
    public function filter_by_form($data, $settings){
        if( empty( $settings['form_list'] )) {
            return $data;
        }

        $new_data = [];

        if( ! empty( $data ) ) {
            foreach( $data as $key => $entry ) {
                $selected_form = $settings['form_list'];
                $form_id = $entry['entry_key'];
                if($selected_form == $form_id){
                    $new_data[] = $entry;
                }
            }
        }
        return $new_data;
    }

    public function doc() {
        return sprintf(__('<p>Make sure that you have <a target="_blank" href="%1$s">WPForms installed & configured</a>  to use its campaign & form subscriptions data. For further assistance, check out our step by step <a target="_blank" href="%2$s">documentation</a>.</p>
		<p>🎦 <a target="_blank" href="%3$s">Watch video tutorial</a> to learn quickly</p>
		<p>👉 NotificationX <a target="_blank" href="%4$s">Integration with WPForms</a></p>
		<p><strong>Recommended Blogs:</strong></p>
		<p>🔥Hacks to Increase Your <a target="_blank" href="%5$s">WordPress Contact Forms Submission Rate</a> Using NotificationX</p>', 'notificationx'),
        'https://wordpress.org/plugins/wpforms-lite/',
        'https://notificationx.com/docs/wpforms/',
        'https://www.youtube.com/watch?v=8tk7_ZawJN8',
        'https://notificationx.com/integrations/wpforms/',
        'https://notificationx.com/blog/wordpress-contact-forms/'
        );
    }
}
