<?php

/**
 * Template for job application data wizard page
 *
 * @author      PressTigers
 * @package     Simple_Job_Board
 * @subpackage  Simple_Job_Board/admin/wizard/wizard-job-application-data
 */
?>

<h3><?php echo esc_html__('Job Application Form', 'simple-job-board'); ?></h3>

<!-- Application Form Fields -->
<div class="sjb-admin-settings tab">
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

    $field_types = apply_filters('sjb_form_field_types', $field_types);
    ?>
    <h4 class="wiz-style"><?php esc_html_e('Default Application Form Fields', 'simple-job-board'); ?></h4>
    <div class="sjb-section settings-fields features-short-form">

        <ul id="settings_app_form_fields">
            <?php
            // Get Application Form Data
            $jobapp_fields = maybe_unserialize(get_option('jobapp_settings_options'));

            // Display Job Application From DB
            if (NULL != $jobapp_fields) :
                foreach ($jobapp_fields as $key => $val) :
                    if (isset($val['type']) && isset($val['option'])) :
                        if (substr($key, 0, 7) == 'jobapp_') :

                            // Escaping all array value
                            $val = (is_array($val)) ? array_map('esc_attr', $val) : esc_attr($val);
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
                            if ('checkbox' === $val['type'] or 'radio' === $val['type'] or 'dropdown' === $val['type']) :
                                $field_options = '<input type="text" class="settings-field-options"  name="' . $key . '[option]" value="' . $val['option'] . '"  placeholder="Option1, option2, option3" />';
                            else :
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
                                . '<span class="sjb-form-group"><input type="radio" class="settings-applicant-columns" name="[applicant_column]" ' . $is_applicant_column . ' /></span>' . esc_html__('Expose in Applicant Listing', 'simple-job-board') . ' &nbsp; '
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
    </div>

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
                foreach ($field_types as $key => $val) :
                    echo '<option value="' . $key . '" class="' . $key . '">' . esc_attr($val) . '</option>';
                endforeach;
                ?>
            </select>
            <input id="settings_jobapp_field_options" class="jobapp_field_type" type="text" style="display: none;" placeholder="Option1, Option2, Option3" />
        </div>
        <div class="col-lg-5 col-md-5">
            <div class="sjb-required">
                <div class="sjb-form-group">
                    <span class="sjb-form-group"><input type="checkbox" id="settings-jobapp-required-field" checked="checked" /></span>
                    <label for="settings-jobapp-required-field"><span><?php esc_html_e('Required', 'simple-job-board'); ?></span></label>
                </div>
                <div class="sjb-form-group">
                    <span class="sjb-form-group"><input type="radio" id="settings-jobapp-applicant-columns" /></span>
                    <label for="settings-jobapp-applicant-columns" class="sjb-expose-listing"><span><?php esc_html_e('Expose in Applicant Listing', 'simple-job-board'); ?></span></label>
                </div>
                
            </div>
        </div>
        <input type="button" class="button" id="app_add_field" value="<?php esc_html_e('Add Field', 'simple-job-board'); ?>">
    </div>
</div>
<div class="sjb-stripe"></div>
<button type="button" class="action-button previous previous_button"><?php echo esc_html__('Back', 'simple-job-board'); ?></button>
<input type="hidden" value="1" name="admin_notices">
<input type="submit" name="sjb-wiz-id" class="next action-button" value="<?php echo esc_html__('Save Changes', 'simple-job-board'); ?>">