<?php

/**
 * CF7 Extension
 *
 * @package NotificationX\Extensions
 */

namespace NotificationX\Extensions\NJF;

use NotificationX\Core\Helper;
use NotificationX\Core\Rules;
use NotificationX\GetInstance;
use NotificationX\Extensions\Extension;
use NotificationX\Extensions\GlobalFields;

/**
 * NinjaForms Extension
 */
class NinjaForms extends Extension {
    /**
     * Instance of NinjaForms
     *
     * @var NinjaForms
     */
    use GetInstance;

    public $priority        = 15;
    public $id              = 'njf';
    public $img             = '';
    public $doc_link        = 'https://notificationx.com/docs/contact-form-submission-alert/';
    public $types           = 'form';
    public $module          = 'modules_njf';
    public $module_priority = 10;
    public $class           = 'Ninja_Forms';

    /**
     * Initially Invoked when initialized.
     */
    public function __construct() {
        $this->title = __('Ninja Forms', 'notificationx');
        $this->module_title = __('Ninja Forms', 'notificationx');
        parent::__construct();
    }

    public function init() {
        parent::init();

        add_action('ninja_forms_after_submission', array($this, 'save_new_records'));
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

    }

    public function source_error_message($messages) {
        if (!$this->class_exists()) {
            $url = admin_url('plugin-install.php?s=ninja+forms&tab=search&type=term');
            $messages[$this->id] = [
                'message' => sprintf( '%s <a href="%s" target="_blank">%s</a> %s',
                    __( 'You have to install', 'notificationx' ),
                    $url,
                    __( 'Ninja Forms', 'notificationx' ),
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
        $forms = [];
        if (!class_exists('Ninja_Forms')) {
            return [];
        }
        global $wpdb;
        $form_result = $wpdb->get_results('SELECT id, title FROM `' . $wpdb->prefix . 'nf3_forms` ORDER BY title');
        if (!empty($form_result)) {
            foreach ($form_result as $form) {
                $key = $this->key($form->id);
                $forms[$key] = $form->title;
            }
        }

        return $forms;
    }

    public function restResponse($args) {
        if (!class_exists('Ninja_Forms')) {
            return [];
        }

        if (isset($args['form_id'])) {
            global $wpdb;
            $form_id = intval($args['form_id']);
            $queryresult = $wpdb->get_results('SELECT meta_value FROM `' . $wpdb->prefix . 'nf3_form_meta` WHERE parent_id = ' . $form_id . ' AND meta_key = "formContentData"');

            if(isset($queryresult[0]) && isset($queryresult[0]->meta_value)){
                $formdata = $queryresult[0]->meta_value;

                $keys = $this->keys_generator($formdata);

                $returned_keys = array();

                if (is_array($keys) && !empty($keys)) {
                    foreach ($keys as $key) {
                        $returned_keys[] = array(
                            'label' => ucwords(str_replace('_', ' ', str_replace('-', ' ', $key))),
                            'value' => "tag_$key",
                        );
                    }

                    return $returned_keys;
                }
            }
        }
        wp_send_json_error([]);
    }

    public function keys_generator($fieldsString) {
        $fields = array();
        $fieldsdata = unserialize($fieldsString);
        if (!empty($fieldsdata)) {
            foreach ($fieldsdata as $field) {
                if(!is_string($field)){
                    $field = !empty($field['cells'][0]['fields'][0]) ? $field['cells'][0]['fields'][0] : null;
                }
                if ($field && Helper::filter_contactform_key_names($field)) {
                    $fields[] = Helper::rename_contactform_key_names($field);
                }
            }
        }
        return $fields;
    }

    public function save_new_records($form_data) {
        foreach ($form_data['fields'] as $field) {
            $arr = Helper::rename_contactform_key_names($field['key']);
            $data[$arr] = $field['value'];
        }
        $data['title'] = $form_data['settings']['title'];
        $data['timestamp'] = time();

        if (!empty($data)) {
            $key = $this->key($form_data['form_id']);
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
     * @param [type] $return
     * @param [type] $entry
     * @param [type] $settings
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

    public function doc() {
        return sprintf(__('<p>Make sure that you have <a target="_blank" href="%1$s">Ninja Forms installed & configured</a> to use its campaign & form subscriptions data. For further assistance, check out our step by step <a target="_blank" href="%2$s">documentation</a>.</p>
		<p>🎦 <a target="_blank" href="%3$s">Watch video tutorial</a> to learn quickly</p>
		<p>👉 NotificationX <a target="_blank" href="%4$s">Integration with Ninja Forms</a></p>
		<p><strong>Recommended Blog:</strong></p>
		<p>🔥 Hacks to Increase Your <a target="_blank" href="%5$s">WordPress Contact Forms Submission Rate</a> Using NotificationX</p>', 'notificationx'),
        'https://wordpress.org/plugins/ninja-forms/',
        'https://notificationx.com/docs/ninja-forms/',
        'https://www.youtube.com/watch?v=Ibv84iGcBHE',
        'https://notificationx.com/integrations/ninja-forms/',
        'https://notificationx.com/blog/wordpress-contact-forms/'
        );
    }
}
