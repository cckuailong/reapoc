<?php if (!defined('ABSPATH')) { exit; } // Exit if accessed directly
/**
 * Simple_Job_Board_Settings_Application_Form_Fields Class
 *
 * This file used to define the settings for the job application form. User can create 
 * generic job application form that will only add to the newly created job.
 * 
 * @link        https://wordpress.org/plugins/simple-job-board
 * @since       2.2.3
 * @since       2.4.0   Added Application Name Column Option on SJB Form Builder & Revised Sanitization & Escaping of Form Fields' Inputs & Outputs
 * @since       2.5.0   Added before & after action hooks for application form fields section.
 *
 * @package     Simple_Job_Board
 * @subpackage  Simple_Job_Board/admin/settings
 * @author      PressTigers <support@presstigers.com>
 */

class Simple_Job_Board_Settings_Application_Form_Fields {

    /**
     * Initialize the class and set its properties.
     *
     * @since   2.2.3
     */
    public function __construct() {

        // Filter -> Add Settings Application Form Fields Tab
        add_filter('sjb_settings_tab_menus', array($this, 'sjb_add_settings_tab'), 50);

        // Action -> Add Settings Application Form Fields Section 
        add_action('sjb_settings_tab_section', array($this, 'sjb_add_settings_section'), 50);

        // Action -> Save Settings Application Form Fields Section 
        add_action('sjb_save_setting_sections', array($this, 'sjb_save_settings_section'));
    }

    /**
     * Add Settings Application Form Fields Tab.
     *
     * @since    2.2.3
     * 
     * @param    array  $tabs  Settings Tab
     * @return   array  $tabs  Merge array of Settings Tab with Application Form Fields Tab.
     */
    public function sjb_add_settings_tab($tabs) {
        $tabs['application_form_fields'] = esc_html__('Application Form Fields', 'simple-job-board');
        return $tabs;
    }

    /**
     * Add Settings Application Form Fields Section.
     *
     * @since    2.2.3
     */
    public function sjb_add_settings_section() {
        ?>
        <!-- Application Form Fields -->
        <div data-id="settings-application_form_fields" class="sjb-admin-settings tab">
            <?php
            $field_types = array(
                'section_heading' => esc_html__('Section Heading', 'simple-job-board'),
                'text' => esc_html__('Text Field', 'simple-job-board'),
                'text_area' => esc_html__('Text Area', 'simple-job-board'),
                'email' => esc_html__('Email', 'simple-job-board'),
                'phone' => esc_html__('Phone', 'simple-job-board'),
                'date' => esc_html__('Date', 'simple-job-board'),
                'checkbox' => esc_html__('Check Box', 'simple-job-board'),
                'dropdown' => esc_html__('Drop Down', 'simple-job-board'),
                'radio' => esc_html__('Radio', 'simple-job-board'),
            );

            $field_types = apply_filters( 'sjb_form_field_types', $field_types );
            ?>
            <h4 class="first"><?php esc_html_e('Default Application Form Fields', 'simple-job-board'); ?></h4>
            <div class="sjb-section settings-fields features-short-form">
                
                <?php
                /**
                 * Action -> Add new section before application fields section.  
                 * 
                 * @since   2.5.0 
                 */
                do_action('sjb_jobapp_fields_before');
                ?>
                <form method="post" id="job_app_form">
                    <ul id="settings_app_form_fields">
                        <?php
                        // Get Application Form Data
                        $jobapp_fields = maybe_unserialize(get_option('jobapp_settings_options'));

                        // Display Job Application From DB
                        if (NULL != $jobapp_fields):
                            foreach ($jobapp_fields as $key => $val):
                                if (isset($val['type']) && isset($val['option'])):
                                    if (substr($key, 0, 7) == 'jobapp_'):

                                        // Escaping all array value
                                        $val = ( is_array($val) ) ? array_map('esc_attr', $val) : esc_attr($val);
                                        $key = preg_replace('/[^\p{L} 0-9]/u', '_', $key);

                                        $select_option = NULL;

                                        // Job Application Form Select Options
                                        foreach ($field_types as $field_key => $field_val) {
                                            if ($val['type'] == $field_key) {
                                                $select_option .= '<option value="' . $field_key . '" selected="selected">' . $field_val . '</option>';
                                            } else {
                                                $select_option .= '<option value="' . $field_key . '" >' . $field_val . '</option>';
                                            }
                                        }

                                        // Retrieve Radio Button, Checkbox & Dropdown Option's Value
                                        $val['option'] = isset($val['option']) ? $val['option'] : '';

                                        // Options for [Checkbox],[Radio],[Drop Down] Fields
                                        if ('checkbox' === $val['type'] or 'radio' === $val['type'] or 'dropdown' === $val['type']):
                                            $field_options = '<input type="text" class="settings-field-options"  name="' . $key . '[option]" value="' . $val['option'] . '"  placeholder="Option1, option2, option3" />';
                                        else:
                                            $field_options = '<input type="text" class="settings-field-options"  name="' . $key . '[option]" value="' . $val['option'] . '" placeholder="Option1, option2, option3" style="display:none;" />';

                                        endif;                                        

                                        /**
                                         * New Label Index Insertion:
                                         * 
                                         * - Addition of new index "label"
                                         * - Data Legacy Checking  
                                         */
                                        $label = isset($val['label']) ? $val['label'] : ucwords(str_replace('_', ' ', substr($key, 7)));

                                        // List Application Form Fields
                                        echo '<li class="' . $key . '">'
                                        . '<div class="col-lg-2 col-md-2">'
                                        . '<label class="sjb-editable-label" for="">' . $label . '</label>'
                                        . '<input type="hidden" name="' . $key . '[label]" value="' . $label . '" />'
                                        . '</div><div class="col-lg-2 col-md-2">'
                                        . '<select class="settings_jobapp_field_type"  name="' . $key . '[type]">' . $select_option . '</select>'
                                        . '' . $field_options . ''
                                        . '</div>';

                                        // Set Fields as Optional or Required
                                        $val['optional'] = isset($val['optional']) ? $val['optional'] : 'checked';

                                        echo '<div class="col-lg-5 col-md-5"><label>'
                                        . '<span class="sjb-form-group"><input type="checkbox" value="' . $val['optional'] . '" class="settings-jobapp-required-field"  ' . $val['optional'] . ' /></span>' . esc_html__('Required', 'simple-job-board') . ' &nbsp; '
                                        . '<input type="hidden" name="' . $key . '[optional]" value="' . $val['optional'] . '" class="settings-jobapp-optional-field" />'
                                        . '</label>';

                                        echo '&nbsp;&nbsp;<div class="button removeField">' . esc_html__('Delete', 'simple-job-board') . '</div>&nbsp;&nbsp;';

                                        /**
                                         * Set Applicant Name Field
                                         * 
                                         * Select field to show column under Applicant section
                                         * 
                                         * @since   2.4.0
                                         */
                                        $is_applicant_column = isset($val['applicant_column']) ? $val['applicant_column'] : 'unchecked';

                                        echo '<label class="sjb-expose-listing">'
                                        . '<input type="radio" class="settings-applicant-columns" name="[applicant_column]" ' . $is_applicant_column . ' />' . esc_html__('Expose in Applicant Listing', 'simple-job-board') . ' &nbsp; '
                                        . '<input type="hidden" class="settings-jobapp-applicant-column" name="' . $key . '[applicant_column]" value="' . $is_applicant_column . '" />'
                                        . '</label>'
                                        . '<div>'
                                        . '</li>';
                                    endif;
                                endif;
                            endforeach;
                        endif;
                        ?>
                    </ul>
                    <input type="hidden" name="empty_jobapp" value="empty_jobapp">
                    <input type="hidden" value="1" name="admin_notices">
                </form>
            </div>
            <div class="clearfix clear"></div>

            <!-- Add Application Form Fields -->
            <div class="sjb-section sjb-input-data">
                <div class="col-lg-2 col-md-2">
                    <label class="sjb-featured-label"><?php esc_html_e('Field', 'simple-job-board'); ?></label>
                    <input type="text" id="setting_jobapp_name">
                </div>
                <div class="col-lg-2 col-md-2">
                    <label class="sjb-featured-label"><?php esc_html_e('Type', 'simple-job-board'); ?></label>
                    <select id="settings-jobapp-field-types">
                        <?php
                        foreach ($field_types as $key => $val):
                            echo '<option value="' . $key . '" class="' . $key . '">' . esc_attr($val) . '</option>';
                        endforeach;
                        ?>
                    </select>
                    <input id="settings_jobapp_field_options" class="jobapp_field_type" type="text" style="display: none;" placeholder="Option1, Option2, Option3" />
                </div>
                <div class="col-lg-5 col-md-5">
                    <div class="sjb-required">
                        <label>
                            <span class="sjb-form-group"><input type="checkbox" id="settings-jobapp-required-field" checked="checked" /></span>                           
                            <span><?php esc_html_e('Required', 'simple-job-board'); ?></span>
                        </label>
                        <label class="sjb-expose-listing">
                            <input type="radio" id="settings-jobapp-applicant-columns" />
                            <span><?php esc_html_e('Expose in Applicant Listing', 'simple-job-board'); ?></span>
                        </label>
                        <input type="submit" class="button" id="app_add_field" value="<?php esc_html_e('Add Field', 'simple-job-board'); ?>">    
                    </div>
                </div>
            </div>
            <?php
            /**
             * Action -> Add new section after application fields section.  
             * 
             * @since   2.5.0 
             */
            do_action('sjb_jobapp_fields_after');
            ?>
            <input type="submit" name="jobapp_submit" id="jobapp_btn" class="button button-primary" value="<?php echo esc_html__('Save Changes', 'simple-job-board'); ?>" />
        </div>
        <?php
    }

    /**
     * Save Settings Application Form Fields Section.
     * 
     * This function is used to save the generic job application form. This form
     * is visible to job detail page in admin on creation of new job.
     *
     * @since    2.2.3
     */
    public function sjb_save_settings_section() {

        $POST_data = filter_input_array(INPUT_POST);
        $empty_jobapp = filter_input(INPUT_POST, 'empty_jobapp');

        if (!empty($POST_data) && ( NULL != $empty_jobapp )) {

            $job_data = $POST_data;

            // Save Application Form in WP Options || Add Option if not exist.
            ( FALSE !== get_option('jobapp_settings_options') ) ?
                            update_option('jobapp_settings_options', $job_data) :
                            add_option('jobapp_settings_options', $job_data, '', 'no');
        }
        
        
    }

}
