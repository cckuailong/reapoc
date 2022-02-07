<?php
/**
 * Display the job application form.
 *
 * Override this template by copying it to yourtheme/simple_job_board/v2/single-jobpost/job-application.php
 *
 * @author  PressTigers
 * @package     Simple_Job_Board
 * @subpackage  Simple_Job_Board/Templates
 * @version     1.0.0
 * @since       2.1.0
 * @since       2.2.2   Added more @hooks in application form.
 * @since       2.3.0   Added "sjb_job_application_template" filter & "sjb_job_application_form_fields" filter.
 * @since       2.7.0   Revised the application HTML & added loader to application
 */
ob_start();
global $post;

/**
 * Fires on job detail page before displaying job application section.
 *                  
 * @since   2.1.0                   
 */
do_action('sjb_job_application_before');
?>

<!-- Start Job Application Form
================================================== -->
<form class="jobpost-form" id="sjb-application-form" name="c-assignments-form"  enctype="multipart/form-data">
    <h3><?php echo apply_filters('sjb_job_application_form_title', esc_html__('Apply Online', 'simple-job-board')); ?></h3>    
    <div class="row">
        <?php
        /**
         * Fires on job detail page at start of job application form. 
         *                 
         * @since   2.3.0                   
         */
        do_action('sjb_job_application_form_fields_start');

        $keys = get_post_custom_keys(get_the_ID());
        $section_no = 1;
        $total_sections = 0;

        $enable_apps = get_post_meta(get_the_ID(), 'enable_job_apps', TRUE);

        if($enable_apps == 'jobapps' || $enable_apps == '' ){

            // Get total sections
            if (NULL != $keys):
                foreach ($keys as $key):
                    if (substr($key, 0, 7) == 'jobapp_'):
                        $val = get_post_meta(get_the_ID(), $key, TRUE);
                        $val = maybe_unserialize($val);
                        if ('section_heading' == $val['type']) {
                            $total_sections++;
                        }
                    endif;
                endforeach;
            endif;
            if (0 < $total_sections) {
                echo '<div class="col-md-12">';
            }

            if (NULL != $keys):
                foreach ($keys as $key):
                    if (substr($key, 0, 7) == 'jobapp_'):
                        $val = get_post_meta(get_the_ID(), $key, TRUE);
                        $val = maybe_unserialize($val);
                        $is_required = isset($val['optional']) ? "checked" === $val['optional'] ? 'required="required"' : "" : 'required="required"';
                        $required_class = isset($val['optional']) ? "checked" === $val['optional'] ? "sjb-required" : "sjb-not-required" : "sjb-required";
                        $required_field_asterisk = isset($val['optional']) ? "checked" === $val['optional'] ? '<span class="required">*</span>' : "" : '<span id="sjb-required">*</span>';
                        $id = preg_replace('/[^\p{L}\p{N}\_]/u', '_', $key);
                        $name = preg_replace('/[^\p{L}\p{N}\_]/u', '_', $key);
                        $label = isset($val['label']) ? $val['label'] : ucwords(str_replace('_', ' ', substr($key, 7)));

                        // Field Type Meta
                        $field_type_meta = array(
                            'id' => $id,
                            'name' => $name,
                            'label' => $label,
                            'type' => $val['type'],
                            'is_required' => $is_required,
                            'required_class' => $required_class,
                            'required_field_asterisk' => $required_field_asterisk,
                        );

                        /**
                         * Fires on job detail page at start of job application form. 
                         *                 
                         * @since   2.3.0                   
                         */
                        do_action('sjb_job_application_form_fields', $field_type_meta);

                        switch ($val['type']) {
                            case 'section_heading':
                                if (1 < $section_no) {
                                    echo '</div>';
                                }
                                echo '<div class="form-box">'
                                . '<h3>' . $label . '</h3>';
                                $section_no++;
                                break;
                            case 'text':
                                echo '<div class="col-md-3 col-xs-12">'
                                . '<label for="' . $key . '">' . $label . $required_field_asterisk . '</label>'
                                . '</div>'
                                . '<div class="col-md-9 col-xs-12">'
                                . '<div class="form-group">'
                                . '<input type="text" name="' . $name . '" class="form-control ' . $required_class . '" id="' . $id . '" ' . $is_required . '>'
                                . '</div>'
                                . '</div>'
                                . '<div class="clearfix"></div>';
                                break;
                            case 'text_area':
                                echo '<div class="col-md-3 col-xs-12">'
                                . '<label for="' . $key . '">' . $label . $required_field_asterisk . '</label>'
                                . '</div>'
                                . '<div class="col-md-9 col-xs-12">'
                                . '<div class="form-group">'
                                . '<textarea name="' . $name . '" class="form-control ' . $required_class . '" id="' . $id . '" ' . $is_required . '  cols="30" rows="5"></textarea>'
                                . '</div>'
                                . '</div>'
                                . '<div class="clearfix"></div>';
                                break;
                            case 'email':
                                echo '<div class="col-md-3 col-xs-12">'
                                . '<label for="' . $key . '">' . $label . $required_field_asterisk . '</label>'
                                . '</div>'
                                . '<div class="col-md-9 col-xs-12">'
                                . '<div class="form-group">'
                                . '<input type="email" name="' . $name . '" class="form-control sjb-email-address ' . $required_class . '" id="' . $id . '" ' . $is_required . '><span class="sjb-invalid-email validity-note">' . esc_html__('A valid email address is required.', 'simple-job-board') . '</span>'
                                . '</div>'
                                . '</div>'
                                . '<div class="clearfix"></div>';
                                break;
                            case 'phone':
                                echo '<div class="col-md-3 col-xs-12">'
                                . '<label for="' . $key . '">' . $label . $required_field_asterisk . '</label>'
                                . '</div>'
                                . '<div class="col-md-9 col-xs-12">'
                                . '<div class="form-group">'
                                . '<input type="tel" name="' . $name . '" class="form-control sjb-phone-number sjb-numbers-only ' . $required_class . '" id="' . $id . '" ' . $is_required . '><span class="sjb-invalid-phone validity-note" id="' . $id . '-invalid-phone">' . esc_html__('A valid phone number is required.', 'simple-job-board') . ' </span>'
                                . '</div>'
                                . '</div>'
                                . '<div class="clearfix"></div>';
                                break;
                            case 'date':
                                echo '<div class="col-md-3 col-xs-12">'
                                . '<label for="' . $key . '">' . $label . $required_field_asterisk . '</label>'
                                . '</div>'
                                . '<div class="col-md-9 col-xs-12">'
                                . '<div class="form-group">'
                                . '<input type="text" name="' . $name . '" class="form-control sjb-datepicker ' . $required_class . '" id="' . $id . '" ' . $is_required . ' maxlength="10">'
                                . '</div>'
                                . '</div>'
                                . '<div class="clearfix"></div>';
                                break;
                            case 'radio':
                                if ($val['options'] != '') {
                                    echo '<div class="col-md-3 col-xs-12">'
                                    . '<label class="sjb-label-control" for="' . $key . '">' . $label . $required_field_asterisk . '</label>'
                                    . '</div>'
                                    . '<div class="col-md-9 col-xs-12">'
                                    . '<div class="form-group">';
                                    $options = explode(',', $val['options']);
                                    $i = 0;
                                    foreach ($options as $option) {
                                        echo '<label class="small"><input type="radio" name="' . $name . '" class=" ' . $required_class . '" id="' . $id . '" value="' . $option . '"  ' . sjb_is_checked($i) . ' ' . $is_required . '>' . $option . ' </label> ';
                                        $i++;
                                    }
                                    echo '</div></div>'
                                    . '<div class="clearfix"></div>';
                                }
                                break;
                            case 'dropdown':
                                if ($val['options'] != '') {
                                    echo '<div class="col-md-3 col-xs-12">'
                                    . '<label for="' . $key . '">' . $label . $required_field_asterisk . '</label>'
                                    . '</div>'
                                    . ' <div class="col-md-9 col-xs-12">'
                                    . '<div class="form-group">'
                                    . '<select class="form-control" name="' . $name . '" id="' . $id . '" ' . $is_required . '>';
                                    $options = explode(',', $val['options']);
                                    foreach ($options as $option) {
                                        echo '<option class="' . $required_class . '" value="' . $option . '" >' . $option . ' </option>';
                                    }
                                    echo '</select>'
                                    . '</div>'
                                    . '</div>'
                                    . '<div class="clearfix"></div>';
                                }
                                break;
                            case 'checkbox' :
                                if ($val['options'] != '') {
                                    echo '<div class="col-md-3 col-xs-12">'
                                    . '<label for="' . $key . '">' . $label . $required_field_asterisk . '</label>'
                                    . '</div>'
                                    . '<div class="col-md-9 col-xs-12">'
                                    . '<div class="form-group">';
                                    $options = explode(',', $val['options']);
                                    $i = 0;

                                    foreach ($options as $option) {
                                        echo '<label class="small"><input type="checkbox" name="' . $name . '[]" class="' . $required_class . '" id="' . $id . '" value="' . $option . '"  ' . $i . ' ' . $is_required . '>' . $option . ' </label>';
                                        $i++;
                                    }
                                    echo '</div></div>'
                                    . '<div class="clearfix"></div>';
                                }
                                break;
                        }
                    endif;
                endforeach;
                if ($total_sections > 0 && $total_sections + 1 == $section_no) {
                    echo '</div>';
                    echo '<div class="clearfix"></div>';
                }
            endif;

            /**
             * Modify the output of file upload button. 
             * 
             * @since   2.2.0 
             * 
             * @param   string  $sjb_attach_resume  Attach resume button.
             */
            if (0 < $total_sections) {
                echo '<div class="row">';
            }
        }
        else{
             // Getting setting page saved options
            $jobapp_removed_options = maybe_unserialize(get_option('jobapp_settings_options'));

            if (NULL == $jobapp_removed_options) {
                $jobapp_removed_options = '';
            }

            // Get total sections
            if (NULL != $jobapp_removed_options):
                foreach ($jobapp_removed_options as $key => $val):
                    if (substr($key, 0, 7) == 'jobapp_'):
                        $val = maybe_unserialize($val);
                        if ('section_heading' == $val['type']) {
                            $total_sections++;
                        }
                    endif;
                endforeach;
            endif;
            if (0 < $total_sections) {
                echo '<div class="col-md-12">';
            }

            if (NULL != $jobapp_removed_options):
                foreach ($jobapp_removed_options as $key => $val):
                    if (substr($key, 0, 7) == 'jobapp_'):
                        
                        $is_required = isset($val['optional']) ? "checked" === $val['optional'] ? 'required="required"' : "" : 'required="required"';
                        $required_class = isset($val['optional']) ? "checked" === $val['optional'] ? "sjb-required" : "sjb-not-required" : "sjb-required";
                        $required_field_asterisk = isset($val['optional']) ? "checked" === $val['optional'] ? '<span class="required">*</span>' : "" : '<span id="sjb-required">*</span>';
                        $id = preg_replace('/[^\p{L}\p{N}\_]/u', '_', $key);
                        $name = preg_replace('/[^\p{L}\p{N}\_]/u', '_', $key);
                        $label = isset($val['label']) ? $val['label'] : ucwords(str_replace('_', ' ', substr($key, 7)));

                        // Field Type Meta
                        $field_type_meta = array(
                            'id' => $id,
                            'name' => $name,
                            'label' => $label,
                            'type' => $val['type'],
                            'is_required' => $is_required,
                            'required_class' => $required_class,
                            'required_field_asterisk' => $required_field_asterisk,
                        );

                        /**
                         * Fires on job detail page at start of job application form. 
                         *                 
                         * @since   2.3.0                   
                         */
                        do_action('sjb_job_application_form_fields', $field_type_meta);

                        switch ($val['type']) {
                            case 'section_heading':
                                if (1 < $section_no) {
                                    echo '</div>';
                                }
                                echo '<div class="form-box">'
                                . '<h3>' . $label . '</h3>';
                                $section_no++;
                                break;
                            case 'text':
                                echo '<div class="col-md-3 col-xs-12">'
                                . '<label for="' . $key . '">' . $label . $required_field_asterisk . '</label>'
                                . '</div>'
                                . '<div class="col-md-9 col-xs-12">'
                                . '<div class="form-group">'
                                . '<input type="text" name="' . $name . '" class="form-control ' . $required_class . '" id="' . $id . '" ' . $is_required . '>'
                                . '</div>'
                                . '</div>'
                                . '<div class="clearfix"></div>';
                                break;
                            case 'text_area':
                                echo '<div class="col-md-3 col-xs-12">'
                                . '<label for="' . $key . '">' . $label . $required_field_asterisk . '</label>'
                                . '</div>'
                                . '<div class="col-md-9 col-xs-12">'
                                . '<div class="form-group">'
                                . '<textarea name="' . $name . '" class="form-control ' . $required_class . '" id="' . $id . '" ' . $is_required . '  cols="30" rows="5"></textarea>'
                                . '</div>'
                                . '</div>'
                                . '<div class="clearfix"></div>';
                                break;
                            case 'email':
                                echo '<div class="col-md-3 col-xs-12">'
                                . '<label for="' . $key . '">' . $label . $required_field_asterisk . '</label>'
                                . '</div>'
                                . '<div class="col-md-9 col-xs-12">'
                                . '<div class="form-group">'
                                . '<input type="email" name="' . $name . '" class="form-control sjb-email-address ' . $required_class . '" id="' . $id . '" ' . $is_required . '><span class="sjb-invalid-email validity-note">' . esc_html__('A valid email address is required.', 'simple-job-board') . '</span>'
                                . '</div>'
                                . '</div>'
                                . '<div class="clearfix"></div>';
                                break;
                            case 'phone':
                                echo '<div class="col-md-3 col-xs-12">'
                                . '<label for="' . $key . '">' . $label . $required_field_asterisk . '</label>'
                                . '</div>'
                                . '<div class="col-md-9 col-xs-12">'
                                . '<div class="form-group">'
                                . '<input type="tel" name="' . $name . '" class="form-control sjb-phone-number sjb-numbers-only ' . $required_class . '" id="' . $id . '" ' . $is_required . '><span class="sjb-invalid-phone validity-note" id="' . $id . '-invalid-phone">' . esc_html__('A valid phone number is required.', 'simple-job-board') . ' </span>'
                                . '</div>'
                                . '</div>'
                                . '<div class="clearfix"></div>';
                                break;
                            case 'date':
                                echo '<div class="col-md-3 col-xs-12">'
                                . '<label for="' . $key . '">' . $label . $required_field_asterisk . '</label>'
                                . '</div>'
                                . '<div class="col-md-9 col-xs-12">'
                                . '<div class="form-group">'
                                . '<input type="text" name="' . $name . '" class="form-control sjb-datepicker ' . $required_class . '" id="' . $id . '" ' . $is_required . ' maxlength="10">'
                                . '</div>'
                                . '</div>'
                                . '<div class="clearfix"></div>';
                                break;
                            case 'radio':
                                if ($val['option'] != '') {
                                    echo '<div class="col-md-3 col-xs-12">'
                                    . '<label class="sjb-label-control" for="' . $key . '">' . $label . $required_field_asterisk . '</label>'
                                    . '</div>'
                                    . '<div class="col-md-9 col-xs-12">'
                                    . '<div class="form-group">';
                                    $options = explode(',', $val['option']);
                                    $i = 0;
                                    foreach ($options as $option) {
                                        echo '<label class="small"><input type="radio" name="' . $name . '" class=" ' . $required_class . '" id="' . $id . '" value="' . $option . '"  ' . sjb_is_checked($i) . ' ' . $is_required . '>' . $option . ' </label> ';
                                        $i++;
                                    }
                                    echo '</div></div>'
                                    . '<div class="clearfix"></div>';
                                }
                                break;
                            case 'dropdown':
                                if ($val['option'] != '') {
                                    echo '<div class="col-md-3 col-xs-12">'
                                    . '<label for="' . $key . '">' . $label . $required_field_asterisk . '</label>'
                                    . '</div>'
                                    . ' <div class="col-md-9 col-xs-12">'
                                    . '<div class="form-group">'
                                    . '<select class="form-control" name="' . $name . '" id="' . $id . '" ' . $is_required . '>';
                                    $options = explode(',', $val['option']);
                                    foreach ($options as $option) {
                                        echo '<option class="' . $required_class . '" value="' . $option . '" >' . $option . ' </option>';
                                    }
                                    echo '</select>'
                                    . '</div>'
                                    . '</div>'
                                    . '<div class="clearfix"></div>';
                                }
                                break;
                            case 'checkbox' :
                                if ($val['option'] != '') {
                                    echo '<div class="col-md-3 col-xs-12">'
                                    . '<label for="' . $key . '">' . $label . $required_field_asterisk . '</label>'
                                    . '</div>'
                                    . '<div class="col-md-9 col-xs-12">'
                                    . '<div class="form-group">';
                                    $options = explode(',', $val['option']);
                                    $i = 0;

                                    foreach ($options as $option) {
                                        echo '<label class="small"><input type="checkbox" name="' . $name . '[]" class="' . $required_class . '" id="' . $id . '" value="' . $option . '"  ' . $i . ' ' . $is_required . '>' . $option . ' </label>';
                                        $i++;
                                    }
                                    echo '</div></div>'
                                    . '<div class="clearfix"></div>';
                                }
                                break;
                        }
                    endif;
                endforeach;
                if ($total_sections > 0 && $total_sections + 1 == $section_no) {
                    echo '</div>';
                    echo '<div class="clearfix"></div>';
                }
            endif;

            /**
             * Modify the output of file upload button. 
             * 
             * @since   2.2.0 
             * 
             * @param   string  $sjb_attach_resume  Attach resume button.
             */
            if (0 < $total_sections) {
                echo '<div class="row">';
            }
        }

        $sjb_attach_resume = '<div class="col-md-3 col-xs-12">'
                . '<label for="applicant_resume">' . apply_filters('sjb_resume_label', __('Attach Resume', 'simple-job-board')) . '<span class="sjb-required required">*</span></label>'
                . '</div>'
                . '<div class="col-md-9 col-xs-12">
                                    <div class="form-group">'
                . '<input type="file" name="applicant_resume" id="applicant-resume" class="sjb-attachment form-control "' . apply_filters('sjb_resume_required', 'required="required"') . '>'
                . '<span class="sjb-invalid-attachment validity-note" id="file-error-message"></span>'
                . '</div>'
                . '</div>'
                . '<div class="clearfix"></div>';
        echo apply_filters('sjb_attach_resume', $sjb_attach_resume);

        if (0 < $total_sections) {
            echo '</div>';
        }

        /**
         * GDPR Part
         * 
         * @since 2.6.0
         */
        //Enable GDPR Settings
        $sjb_gdpr_settings = get_option('job_board_privacy_settings');

        $privacy_policy_label = get_option('job_board_privacy_policy_label', '');
        $privacy_policy_content = get_option('job_board_privacy_policy_content', '');
        $term_conditions_label = get_option('job_board_term_conditions_label', '');
        $term_conditions_content = get_option('job_board_term_conditions_content', '');

        $allowed_tags = sjb_get_allowed_html_tags();

        $privacy_policy_content = wp_kses($privacy_policy_content, $allowed_tags);
        $term_conditions_content = wp_kses($term_conditions_content, $allowed_tags);

        if ('yes' == $sjb_gdpr_settings) {
            ?>
            <?php
            if ($privacy_policy_content) {
                if (0 === $total_sections) {
                    echo '<div class="col-md-12 col-xs-12">';
                }
                ?>
                <div class="form-group">
                    <?php if ($privacy_policy_label) { ?>
                        <label class="sjb-privacy-policy-label"><?php echo esc_attr($privacy_policy_label); ?></label>
                    <?php } ?>  
                    <p class="sjb-privacy-policy"><?php echo stripslashes_deep(trim($privacy_policy_content)); ?></p>
                </div>
                <?php
                if (0 === $total_sections) {
                    echo '</div>';
                }
            }
            ?>
            <?php
            if ($term_conditions_content) {
                if (0 < $total_sections) {
                    ?>
                    <div class="row"> 
                    <?php } ?>
                    <div class="form-group ">

                        <?php if ($term_conditions_label) { ?>
                            <div class="col-md-3 col-xs-12">
                                <label for="jobapp_tc"><?php echo $term_conditions_label; ?></label>
                            </div>
                            <div class="col-md-9 col-xs-12">
                                <div id="jobapp-tc">
                                    <label class="small"><input type="checkbox" class="sjb-required" name="jobapp_tc" id="jobapp-tc" value="<?php echo esc_attr($term_conditions_content); ?>" required="required"><?php echo stripslashes_deep(trim($term_conditions_content)); ?><span class="required">*</span></label>
                                </div>
                            </div>
                        <?php } else { ?>
                            <div class="col-md-12 col-xs-12">
                                <div id="jobapp-tc">
                                    <label class="small"><input type="checkbox" class="sjb-required" name="jobapp_tc" id="jobapp-tc" value="<?php echo esc_attr($term_conditions_content); ?>" required="required"><?php echo stripslashes_deep(trim($term_conditions_content)); ?><span class="required">*</span></label>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                    <?php if (0 < $total_sections) { ?>
                    </div>
                <?php } ?>
                <?php
            }
        }

        /**
         * Fires on job detail page before job submit button. 
         *                 
         * @since   2.2.0                   
         */
        do_action('sjb_job_application_form_fields_end');
        ?>
        <input type="hidden" name="job_id" value="<?php the_ID(); ?>" >
        <input type="hidden" name="action" value="process_applicant_form" >
        <input type="hidden" name="wp_nonce" value="<?php echo wp_create_nonce('jobpost_security_nonce') ?>" >
        <div class="clearfix"></div> 
        <?php if (0 === $total_sections) { ?>
            <div class="col-md-12 col-xs-12">
            <?php } ?>

            <div class="form-group" id="sjb-form-padding-button">
                <button class="btn btn-primary app-submit"><?php esc_html_e('Submit', 'simple-job-board'); ?></button>           
            </div>
            <?php if ( 0 === $total_sections ) { ?>
            </div>
        <?php } ?>

        <?php
        if (0 < $total_sections) {
            echo '</div>';
        }
        ?>
        <div class="clearfix"></div>
    </div>
    <?php
    /**
     * Template -> Loader Overlay Template
     * 
     * @since   2.7.0
     */
    get_simple_job_board_template('single-jobpost/loader.php');
    ?>
</form>

<div class="clearfix"></div>

<?php
/**
 * Fires on job detail page after displaying job application form.
 *                  
 * @since 2.1.0                   
 */
do_action('sjb_job_application_end');
?>

<div id="jobpost_form_status"></div>
<!-- ==================================================
End Job Application Form -->

<?php
/**
 * Fires on job detail page after displaying job application section.
 *                  
 * @since   2.1.0                   
 */
do_action('sjb_job_application_after');

$html_job_application = ob_get_clean();

/**
 * Modify the Job Applicatin Form Template. 
 *                                       
 * @since   2.3.0
 * 
 * @param   html    $html_job_application   Job Application Form HTML.                   
 */
echo apply_filters('sjb_job_application_template', $html_job_application);