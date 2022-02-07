<?php

/**
 * Template for job appearance wizard page
 *
 * @author      PressTigers
 * @package     Simple_Job_Board
 * @subpackage  Simple_Job_Board/admin/wizard/wizard-job-data
 */
?>

<!-- Appearance -->
<div class="sjb-admin-settings tab">
    <?php

    // Select Job Listing View                   
    $logo_detail = $without_logo_detail = $without_logo = $without_detail = $sjbpage_layout = $list_view = $grid_view = $jobpost_logo = $jobpost_without_logo = $sjb_layout = $theme_layout = '';

    // Select job pages layout
    if (FALSE !== get_option('job_board_pages_layout')) {
        $sjb_layout = get_option('job_board_pages_layout');

        if ('sjb-layout' === $sjb_layout)
            $sjbpage_layout = 'checked';

        if ('theme-layout' === $sjb_layout)
            $theme_layout = 'checked';
    } else {
        $sjbpage_layout = 'checked';
    }

    // Select job post content with or without company logo & job detail
    if (FALSE !== get_option('job_board_jobpost_content')) {
        $jobpost_content = get_option('job_board_jobpost_content');
        if ('with-logo' === $jobpost_content)
            $jobpost_logo = 'checked';

        if ('without-logo' === $jobpost_content)
            $jobpost_without_logo = 'checked';
    } else {
        $jobpost_logo = 'checked';
    }

    // Select job post layout for version one or version two
    if (FALSE !== get_option('job_post_layout_settings')) {
        $jobpost_layout_option = get_option('job_post_layout_settings');
        if ('job_post_layout_version_one' === $jobpost_layout_option){
            $job_post_layout_version_one = 'checked';
            $job_post_layout_version_two = '';
        }
        elseif ('job_post_layout_version_two' === $jobpost_layout_option){
            $job_post_layout_version_two = 'checked';
            $job_post_layout_version_one = '';
        }
    } else {
        $job_post_layout_version_one = 'checked';
        $job_post_layout_version_two = '';
    }

    // Select job listing with or without company logo & job detail
    if (FALSE !== get_option('job_board_listing')) {
        $list_contents = get_option('job_board_listing');

        if ('logo-detail' === $list_contents)
            $logo_detail = 'checked';

        if ('without-logo-detail' === $list_contents)
            $without_logo_detail = 'checked';

        if ('without-logo' === $list_contents)
            $without_logo = 'checked';

        if ('without-detail' === $list_contents)
            $without_detail = 'checked';
    } else {
        $logo_detail = 'checked';
    }

    // Get Settings Loader Image
    if (FALSE !== get_option('sjb_loader_image')) {
        $image_url = get_option('sjb_loader_image');
    } else {
        $image_url = plugin_dir_url(dirname(dirname(__FILE__))) . 'public/images/loader.gif';
    }
    ?>

    <h4 class="wiz-style"><?php esc_html_e('Appearance', 'simple-job-board'); ?></h4>
    <div class="sjb-section sjb-wiz-appearance">
        <div class="sjb-content">
            <div class="sjb-form-group">
                <input type="radio" name="job_pages_layout" value="sjb-layout" id="sjb-layout" <?php echo esc_attr($sjbpage_layout); ?> />
                <label for="sjb-layout"><?php esc_html_e('SJB Layout', 'simple-job-board'); ?></label>
            </div>
            
            <div class="sjb-form-group">
                <input type="radio" name="job_pages_layout" value="theme-layout" id="theme-layout" <?php echo esc_attr($theme_layout); ?> />
                <label for="theme-layout"><?php esc_html_e('Theme Layout', 'simple-job-board'); ?></label>
            </div>
        </div>
    </div>

    <h4 class="wiz-style"><?php echo apply_filters('sjb_job_listing_content_title', esc_html__('Job Listing Contents', 'simple-job-board')); ?></h4>
    <div class="sjb-section sjb-wiz-job-listing">
        <div class="sjb-content">
            <div class="sjb-form-group">
                <input type="radio" name="job_listing_content_settings" value="logo-detail" id="logo-detail" <?php echo esc_attr($logo_detail); ?> />
                <label for="logo-detail"><?php _e('Display Job Listing with Company Logo and Detail', 'simple-job-board'); ?></label>
            </div>
            <img src="<?php echo plugin_dir_url(dirname(dirname(__FILE__))) . 'admin/images/list-company-logo.jpg' ?>">
            <div class="sjb-form-group">
                <input type="radio" name="job_listing_content_settings" value="without-logo-detail" id="without-logo-detail" <?php echo esc_attr($without_logo_detail); ?> />
                <label for="without-logo-detail"><?php _e('Display Job Listing without Company Logo and Detail', 'simple-job-board'); ?></label>
            </div>
            <img src="<?php echo plugin_dir_url(dirname(dirname(__FILE__))) . 'admin/images/list-heading.jpg' ?>">
            <div class="sjb-form-group">
                <input type="radio" name="job_listing_content_settings" value="without-logo" id="without-logo" <?php echo esc_attr($without_logo); ?> />
                <label for="without-logo"><?php _e('Display Job Listing without Company Logo', 'simple-job-board'); ?></label>
            </div>
            <img src="<?php echo plugin_dir_url(dirname(dirname(__FILE__))) . 'admin/images/list-detail.jpg' ?>">
            <div class="sjb-form-group">
                <input type="radio" name="job_listing_content_settings" value="without-detail" id="without-detail" <?php echo esc_attr($without_detail); ?> />
                <label for="without-detail"><?php _e('Display Job Listing without Company Detail', 'simple-job-board'); ?></label>
            </div>
            <img src="<?php echo plugin_dir_url(dirname(dirname(__FILE__))) . 'admin/images/list-logo.jpg' ?>">
        </div>
    </div>

    <h4 class="wiz-style"><?php echo apply_filters('sjb_job_post_theme_options_title', esc_html__('Theme Options', 'simple-job-board')); ?></h4>
    <div class="sjb-section sjb-wiz-theme-options">
        <div class="sjb-content">
            <div class="sjb-form-group">
                <input type="radio" name="job_post_layout_settings" value="job_post_layout_version_one" id="sjb-version-one" <?php echo esc_attr($job_post_layout_version_one); ?> />
                <label for="sjb-version-one"><?php _e('Classic Layout', 'simple-job-board'); ?></label>
            </div>
            <img src="<?php echo plugin_dir_url(dirname(dirname(__FILE__))) . 'admin/images/sjb-modern-layout.png' ?>">
            <div class="sjb-form-group">
                <input type="radio" name="job_post_layout_settings" value="job_post_layout_version_two" id="sjb-version-two" <?php echo esc_attr($job_post_layout_version_two); ?> />
                <label for="sjb-version-two"><?php _e('Modern Layout', 'simple-job-board'); ?></label>
            </div>
            <img src="<?php echo plugin_dir_url(dirname(dirname(__FILE__))) . 'admin/images/sjb-classic-layout.png' ?>">
        </div>
    </div>


    <h4 class="wiz-style"><?php echo apply_filters('sjb_job_post_content_title', esc_html__('Job Post Content', 'simple-job-board')); ?></h4>
    <div class="sjb-section sjb-wiz-job-post">
        <div class="sjb-content">
            <div class="sjb-form-group">
                <input type="radio" name="job_post_content_settings" value="with-logo" id="job-logo-detail" <?php echo esc_attr($jobpost_logo); ?> />
                <label for="job-logo-detail"><?php _e('Display Job Post with Company Logo', 'simple-job-board'); ?></label>
            </div>
            <img src="<?php echo plugin_dir_url(dirname(dirname(__FILE__))) . 'admin/images/list-logo.jpg' ?>">
            <div class="sjb-form-group">
                <input type="radio" name="job_post_content_settings" value="without-logo" id="job-without-logo-detail" <?php echo esc_attr($jobpost_without_logo); ?> />
                <label for="job-without-logo-detail"><?php _e('Display Job Post without Company Logo', 'simple-job-board'); ?></label>
            </div>
            <img src="<?php echo plugin_dir_url(dirname(dirname(__FILE__))) . 'admin/images/list-logo.jpg' ?>">
        </div>
    </div>
    <h4 class="wiz-style"><?php echo apply_filters('sjb_loader_image_sec_title', esc_html__('Loader Image', 'simple-job-board')); ?></h4>
    <div class="sjb-section sjb-wiz-loader">
        <div class="sjb-content">
            <div class="sjb-form-group">
                <div class="sjb-loader-sec">
                    <div class="file_url">
                        <?php
                        if ($image_url) {
                            echo '<img src="' . esc_url($image_url) . ' " class="upload_field"/>';
                        } else {
                            echo '<img src="" class="upload_field"/>';
                        }
                        ?>
                        <input type="hidden" name="image_url" value="<?php echo esc_url($image_url); ?>" class="image_upload_field" />
                    </div>
                    <div class="add-remove-btns">
                        <button type="button" class="sjb-loader-image"><?php esc_html_e('Upload', 'simple-job-board'); ?></button>
                        <button type="button" class="remove-loader-image"><?php esc_html_e('Remove', 'simple-job-board'); ?></button>
                    </div>
                    <span class="invalid-loader-image"></span>
                    <div class="loader-img-info">
                        <label class="image-extensions"><?php esc_html_e('Only supported formate is .gif', 'simple-job-board'); ?></label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="sjb-stripe"></div>
<button type="button" class="action-button previous previous_button"><?php echo esc_html__('Back', 'simple-job-board'); ?></button>
<button type="button" class="next action-button"><?php echo esc_html__('Continue', 'simple-job-board'); ?></button>