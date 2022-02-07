<?php
if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly
/**
 * Simple_Job_Board_Meta_Box_Job_Application Class
 * 
 * This meta box is designed to create user defined application form that is 
 * for indvidual job post.
 *
 * @link        https://wordpress.org/plugins/simple-job-board
 * @since       2.2.3
 * @since       2.3.2   Added Application Form Labels' Editing Feature.
 * @since       2.4.0   Select Application Name Column on SJB Form Builder & Improved Sanitization & Escaping of Form Fields' Inputs & Outputs
 * @since       2.4.5   Fixed the job application form builder issue with multilingual characters.
 * 
 * @package     Simple_Job_Board
 * @subpackage  Simple_Job_Board/admin/partials/meta-boxes
 * @author      PressTigers <support@presstigers.com>
 */

class Simple_Job_Board_Meta_Box_Job_Application {

    /**
     * Meta box for Job Application Form.
     * 
     * @since   2.2.3
     */
    public static function sjb_meta_box_output($post) {

        global $jobfields;

        // Add a nonce field so we can check for it later.
        wp_nonce_field('sjb_jobpost_meta_box', 'jobpost_meta_box_nonce');
        ?>

        <div class="meta_option_panel jobpost_fields">
            <ul id="app_form_fields" class="job_application_list">
                <?php
                // Form Field Types
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

                /**
                 * Filter -> Modify Form Field Types
                 * 
                 * @since   2.3.0
                 */
                $field_types = apply_filters('sjb_form_field_types', $field_types);

                $keys = get_post_custom_keys($post->ID);

                // Getting setting page saved options
                $jobapp_settings_options = maybe_unserialize(get_option('jobapp_settings_options'));

                //check Array differnce when $keys is not NULL
                if (NULL == $keys) {

                    // "Add New" job Check
                    $jobapp_removed_options = $jobapp_settings_options;
                } elseif (NULL == $jobapp_settings_options) {
                    $jobapp_removed_options = '';
                } else {
                    // Remove the same option from post meta and options
                    $jobapp_removed_options = array_diff_key($jobapp_settings_options, get_post_meta($post->ID));
                }

                // Display Job Application Meta
                if (NULL != $keys):
                    foreach ($keys as $key):
                        if (substr($key, 0, 7) == 'jobapp_'):
                            $val = get_post_meta($post->ID, $key, TRUE);
                            $val = maybe_unserialize($val);
                            $val = ( is_array($val) ) ? array_map('esc_attr', $val) : esc_attr($val);
                            $key = preg_replace('/[^\p{L} 0-9]/u', '_', $key);

                            $fields = NULL;
                            foreach ($field_types as $field_key => $field_val) {
                                if ($val['type'] == $field_key)
                                    $fields .= '<option value="' . sanitize_key($field_key) . '" selected>' . esc_attr($field_val) . '</option>';
                                else
                                    $fields .= '<option value="' . sanitize_key($field_key) . '" >' . esc_attr($field_val) . '</option>';
                            }

                            /**
                             * New Label Index Insertion:
                             * 
                             * - Addition of new index "label"
                             * - Data Legacy Checking  
                             */
                            $label = isset($val['label']) ? $val['label'] : esc_html__(ucwords(str_replace('_', ' ', substr($key, 7))), 'simple-job-board');

                            echo '<li class="' . $key . '">'
                            . '<label class="sjb-editable-label" for="">' . $label . '</label>'
                            . '<input type="hidden" name="' . $key . '[label]" value="' . $label . '"/>'
                            . '<select class="jobapp_field_type" name="' . $key . '[type]">'
                            . $fields
                            . '</select>';

                            // Show Options for Dropdown, Checkbox & Radio Buttons
                            if ('checkbox' === $val['type'] || 'dropdown' === $val['type'] || 'radio' === $val['type']):
                                echo '<input type="text" name="' . $key . '[options]" class="jobapp-field-options" value="' . $val['options'] . '" placeholder="Option1, option2, option3" />';
                            else:
                                echo '<input type="text" name="' . $key . '[options]" class="jobapp-field-options" placeholder="Option1, option2, option3" style="display:none;"  />&nbsp;';
                            endif;

                            // Set Fields as Optional or Required
                            $is_required = isset($val['optional']) ? $val['optional'] : 'checked';
                            echo '<input type="checkbox" class="jobapp-required-field" value="' . $is_required . '" ' . $is_required . ' />' . esc_html__('Required', 'simple-job-board') . ' &nbsp; ';
                            echo '<input type="hidden" class="jobapp-optional-field" name="' . $key . '[optional]" value="' . $is_required . '"/>';

                            // Delete Button
                            $button = '<div class="button removeField">' . esc_html__('Delete', 'simple-job-board') . '</div>';
                            echo $button . ' &nbsp; ';

                            /**
                             * Set Applicant Name Field
                             * 
                             * Select field to show column under Applicant section
                             * 
                             * @since   2.4.0
                             */
                            $is_applicant_column = isset($val['applicant_column']) ? $val['applicant_column'] : 'unchecked';

                            echo '<input type="radio" class="applicant-columns" name="[applicant_column]" ' . $is_applicant_column . '/>' . esc_html__('Expose in Applicant Listing', 'simple-job-board') . ' &nbsp; ';
                            echo '<input type="hidden" class="jobapp-applicant-column" name="' . $key . '[applicant_column]" value="' . $is_applicant_column . '"/>';

                            echo '</li>';
                        endif;
                    endforeach;
                endif;

                                /**
                 * Settings Job Application Form        
                 */
                if ( NULL != $jobapp_removed_options ):
                    if ( !isset( $_GET['action'] ) ):
                        foreach ($jobapp_removed_options as $jobapp_field_name => $val):
                            if (isset($val['type']) && isset($val['option'])):
                                if (substr($jobapp_field_name, 0, 7) == 'jobapp_'):
                                    $val = ( is_array( $val ) )? array_map( 'esc_attr', $val ) : esc_attr( $val );                                    
                                    $jobapp_field_name = preg_replace('/[^\p{L} 0-9]/u', '_', $jobapp_field_name);                                    
                                    
                                    $fields = NULL;
                                    foreach ($field_types as $field_key => $field_val) {
                                        
                                        // Sanitize Key
                                        $field_key = preg_replace('/[^\p{L} 0-9]/u', '_', $field_key);
                                        if ($val['type'] == $field_key)
                                            $fields .= '<option value="' .  $field_key  . '" selected>' . esc_attr( $field_val ) . '</option>';
                                        else
                                            $fields .= '<option value="' . $field_key . '" >' . esc_attr( $field_val ) . '</option>';
                                    }
                                    
                                    /**
                                     * Label Insertion:
                                     * 
                                     * - Addition of new Field Labels
                                     * - Data Legacy Checking  
                                     */
                                    $label = isset( $val['label'] ) ? $val['label'] : ucwords(str_replace('_', ' ', substr($key, 7)));
                                    
                                    echo '<li class="' . $jobapp_field_name . '"><label class="sjb-editable-label" for="">' . $label . '</label>'
                                        . '<input type="hidden" name="' . $jobapp_field_name . '[label]" value="' . $label . '"/>'
                                        . '<select class="jobapp_field_type" name="' . $jobapp_field_name . '[type]">' . $fields . '</select>';
                                    
                                    // Show Options for Dropdown, Checkbox & Radio Buttons
                                    if ( 'checkbox' === $val['type'] || 'dropdown' === $val['type'] || 'radio' === $val['type'] ):
                                        echo '<input type="text" name="' . $jobapp_field_name . '[options]" class="jobapp-field-options" value="' . $val['option'] . '"  placeholder="Option1, option2, option3" />';
                                    else:
                                        echo '<input type="text" name="' . $jobapp_field_name . '[options]" class="jobapp-field-options" placeholder="Option1, option2, option3" style="display:none;"  />';

                                    endif;
                                    
                                    // Set Fields as Optional or Required
                                    $is_required =  isset( $val['optional'] ) ? $val['optional'] : 'checked'; 
                                    echo '<input type="checkbox" class="jobapp-required-field" value="' . $is_required . '" ' . $is_required . ' />' . esc_html__('Required', 'simple-job-board') . ' &nbsp; ';
                                    echo '<input type="hidden"   class="jobapp-optional-field" name="' . $jobapp_field_name . '[optional]" value="' . $is_required . '"/> &nbsp;';
                                    
                                    echo '<div class="button removeField">' . esc_html__('Delete', 'simple-job-board') . '</div> &nbsp;';
                                    
                                    /**
                                     * Set Applicant Name Field
                                     * 
                                     * Select field to show column under Applicant section
                                     * 
                                     * @since   2.4.0
                                     */                            
                                    $is_applicant_column = isset( $val['applicant_column'] ) ? $val['applicant_column']: 'unchecked';

                                    echo '<input type="radio" class="applicant-columns" name="[applicant_column]" '. $is_applicant_column . ' />' . esc_html__( 'Expose in Applicant Listing', 'simple-job-board' ) . ' &nbsp; ';
                                    echo '<input type="hidden" class="jobapp-applicant-column" name="' . $jobapp_field_name . '[applicant_column]" value="' . $is_applicant_column . '" /><li>';
                                endif;
                            endif;
                        endforeach;
                    endif;
                endif;
                ?>
            </ul>
        </div>
        <div class="clearfix clear"></div>

        <!-- Add Job Application Form -->
        <table id="jobapp_form_fields" class="alignleft">
            <thead>
                <tr>
                    <th><label for="metakeyselect"><?php esc_html_e('Field', 'simple-job-board'); ?></label></th>
                    <th><label for="metavalue"><?php esc_html_e('Type', 'simple-job-board'); ?></label></th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="left" id="newmetaleft"><input type="text" id="jobapp_name" /></td>
                    <td>
                        <select id="jobapp_field_type">
                            <?php
                            foreach ($field_types as $key => $val):
                                $key = preg_replace('/[^\p{L} 0-9]/u', '_', $key);
                                echo '<option value="' . $key . '" class="' . sanitize_html_class($key) . '">' . esc_attr($val) . '</option>';
                            endforeach;
                            ?>
                        </select>
                        <input id="jobapp_field_options" class="jobapp_field_type" type="text" style="display: none;" placeholder="Option1, Option2, Option3" />
                    </td>
                    <td>
                        <span class="sjb-form-group"><input type="checkbox" id="jobapp_required_field" checked="checked" /></span>
                        <label for="jobapp_required_field"><?php esc_html_e('Required', 'simple-job-board'); ?></label></td>
                    <td>
                        <span class="sjb-form-group"><input type="radio" id="jobapp-applicant-columns" /></span>
                        <label for="jobapp-applicant-columns"><?php esc_html_e('Expose in Applicant Listing', 'simple-job-board'); ?></label>
                    </td>
                    <td><div class="button" id="addField"><?php esc_html_e('Add Field', 'simple-job-board'); ?></div></td>
                </tr>
            </tbody>
        </table>
        <div class="clearfix clear"></div> 
        <?php
    }

    /**
     * Save job application meta box.
     * 
     * @since   2.2.3
     * 
     * @param   int     $post_id    Post id
     * @return  void
     */
    public static function sjb_save_jobpost_meta($post_id) {

        // Delete previous stored fields
        $old_keys = get_post_custom_keys($post_id);

        if ($old_keys) {
            foreach ($old_keys as $key => $val):
                if (substr($val, 0, 7) == 'jobapp_') {
                    delete_post_meta($post_id, $val); //Remove meta from the db.
                }
            endforeach;
        }

        // Sanitize $_POST Data Array
        $POST_data = filter_input_array(INPUT_POST);

        // Add new Value
        foreach ($POST_data as $key => $val):
            if (substr($key, 0, 7) == 'jobapp_') {

                $key = preg_replace('/[^\p{L} 0-9]/u', '_', $key);

                $data = $val;

                update_post_meta($post_id, $key, $data); // Add new value.
            }
        endforeach;
    }
}