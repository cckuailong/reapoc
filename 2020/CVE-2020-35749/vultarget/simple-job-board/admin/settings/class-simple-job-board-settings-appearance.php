<?php
if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly
/**
 * Simple_Job_Board_Settings_Appearance Class
 * 
 * This is used to define job board appearance settings.
 *
 * This file contains the frontend appearance settings for job listing content, 
 * listing view, job listing and job detail page typography. User can change the
 * job listing layout and content.
 *
 * @link        https://wordpress.org/plugins/simple-job-board
 * @since       2.2.3
 * @since       2.4.0   Revised Inputs and Outputs Sanitization & Escaping
 * @since       2.4.3   Added option for SJB Fonts
 * @since       2.8.0   Removed List View Settings Section as now user can manage through shortcode/SJB Listing block
 *
 * @package     Simple_Job_Board
 * @subpackage  Simple_Job_Board/admin/settings
 * @author      PressTigers <support@presstigers.com>
 */

class Simple_Job_Board_Settings_Appearance
{

    /**
     * Initialize the class and set its properties.
     *
     * @since   2.2.3
     */
    public function __construct()
    {

        // Filter -> Add Settings Appearance Tab
        add_filter('sjb_settings_tab_menus', array($this, 'sjb_add_settings_tab'), 30);

        // Action -> Add Settings Appearance Section 
        add_action('sjb_settings_tab_section', array($this, 'sjb_add_settings_section'), 30);

        // Action -> Save Settings Appearance Section 
        add_action('sjb_save_setting_sections', array($this, 'sjb_save_settings_section'));
    }

    /**
     * Add Settings Appearance Tab.
     *
     * @since    2.2.3
     * 
     * @param    array  $tabs  Settings Tab
     * @return   array  $tabs  Merge array of Settings Tab with Appearance Tab.
     */
    public function sjb_add_settings_tab($tabs)
    {
        $tabs['appearance'] = esc_html__('Appearance', 'simple-job-board');
        return $tabs;
    }

    /**
     * Add Settings Appearance Section.
     *
     * @since    2.2.3
     */
    public function sjb_add_settings_section()
    {

        // Enqueue Alpha Color Picker Script
        wp_enqueue_script('wp-color-picker-alpha');

        wp_enqueue_script('media-upload');
        wp_enqueue_script('thickbox');
        wp_enqueue_style('thickbox');
?>

        <!-- Appearance -->
        <div data-id="settings-appearance" class="sjb-admin-settings tab">
            <form method="post" id="appearance_options_form">
                <?php
                // Get Appearance Options
                if (FALSE !== get_option('job_board_container_class')) {
                    $container_class = get_option('job_board_container_class');
                } else {
                    $container_class = 'container sjb-container';
                }

                // Get Container Id
                if (FALSE !== get_option('job_board_container_id')) {
                    $container_ids = explode(" ", get_option('job_board_container_id'));
                    $container_id = $container_ids[0];
                } else {
                    $container_id = 'container';
                }

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
                    elseif('job_post_layout_version_two' === $jobpost_layout_option){
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

                // Get Settings Job Board Typography
                if (FALSE !== get_option('job_board_typography')) {
                    $job_board_typography = get_option('job_board_typography');
                }

                // Get Settings Job Board Typography
                if (FALSE !== get_option('sjb_fonts')) {
                    $sjb_fonts = get_option('sjb_fonts');
                } else {
                    $sjb_fonts = 'enable-fonts';
                }

                // Get Settings Loader Image
                if (FALSE !== get_option('sjb_loader_image')) {
                    $image_url = get_option('sjb_loader_image');
                } else {
                    $image_url = plugin_dir_url(dirname(dirname(__FILE__))) . 'public/images/loader.gif';
                }

                /**
                 * Action -> Add new section before job pages layout.  
                 * 
                 * @since 2.2.2 
                 */
                do_action('sjb_theme_layout_before');
                ?>

                <h4 class="first"><?php esc_html_e('Job Pages Layout', 'simple-job-board'); ?></h4>
                <div class="sjb-section sjb-appearance">
                    <div class="sjb-content">
                        <?php
                        /**
                         * Action -> Add new fields at start of of Content Wrapper.  
                         * 
                         * @since 2.2.2 
                         */
                        do_action('sjb_content_wrapper_styling_start');
                        ?>
                        <div class="col-md-4 col-lg-5">
                            <div class="sjb-form-group">
                                <input type="radio" name="job_pages_layout" value="sjb-layout" id="sjb-layout" <?php echo esc_attr($sjbpage_layout); ?> />
                                <label for="sjb-layout"><?php esc_html_e('SJB Layout', 'simple-job-board'); ?></label>
                            </div>
                            <img src="<?php echo plugin_dir_url(dirname(dirname(__FILE__))) . 'admin/images/sjb-layout.jpg' ?>" class="sjb-img-responsive">
                        </div>
                        <div class="col-md-4 col-lg-5">
                            <div class="sjb-form-group">
                                <input type="radio" name="job_pages_layout" value="theme-layout" id="theme-layout" <?php echo esc_attr($theme_layout); ?> />
                                <label for="theme-layout"><?php esc_html_e('Theme Layout', 'simple-job-board'); ?></label>
                            </div>
                            <img src="<?php echo plugin_dir_url(dirname(dirname(__FILE__))) . 'admin/images/theme-layout.jpg' ?>" class="sjb-img-responsive">
                        </div>
                        <?php
                        /**
                         * Action -> Add new fields at the end of Content Wrapper.  
                         * 
                         * @since 2.2.2 
                         */
                        do_action('sjb_theme_layout_end');
                        ?>
                    </div>
                </div>

                <?php
                /**
                 * Action -> Add new section after content wrapper styling.  
                 * 
                 * @since 2.2.2 
                 */
                do_action('sjb_theme_layout_after');

                /**
                 * Action -> Add new section before content wrapper styling.  
                 * 
                 * @since 2.2.2 
                 */
                do_action('sjb_content_wrapper_styling_before');
                ?>

                <h4 class="first"><?php esc_html_e('Content Wrapper Styling', 'simple-job-board'); ?></h4>
                <div class="sjb-section sjb-content-wrap">
                    <div class="sjb-content">

                        <?php
                        /**
                         * Action -> Add new fields at start of of Content Wrapper.  
                         * 
                         * @since 2.2.2 
                         */
                        do_action('sjb_content_wrapper_styling_start');
                        ?>
                        <div class="sjb-form-group">
                            <div class="col-md-12">
                                <input type="checkbox" name="sjb_fonts" value="enable-fonts" id="sjb-fonts" <?php checked('enable-fonts', esc_attr($sjb_fonts)); ?> />
                                <input type="hidden" name="no_fonts" value="sjb-fonts" />
                                <label for="sjb-fonts"><?php _e('Enable SJB Fonts', 'simple-job-board'); ?></label>
                            </div>
                        </div>
                        <div class="sjb-form-group">
                            <div class="col-md-3">
                                <label><?php esc_html_e('Job Board Container Id:', 'simple-job-board'); ?></label>
                            </div>
                            <div class="col-md-4">
                                <input type="text" name="container_id" value="<?php echo esc_attr($container_id); ?>" size="30" />
                                <span><?php _e('Please use ID name without #', 'simple-job-board') ?></span>
                            </div>
                        </div>
                        <div class="sjb-form-group">
                            <div class="col-md-3">
                                <label><?php esc_html_e('Job Board Container Class:', 'simple-job-board'); ?></label>
                            </div>
                            <div class="col-md-4">
                                <input type="text" name="container_class" value="<?php echo esc_attr($container_class); ?>" size="30">
                                <span><?php _e('Add classes seprated by space or comma e.g. container sjb-container or container,sjb-container', 'simple-job-board'); ?></span>
                                <span></span>
                            </div>
                        </div>

                        <?php
                        /**
                         * Action -> Add new fields at the end of Content Wrapper.  
                         * 
                         * @since 2.2.2 
                         */
                        do_action('sjb_content_wrapper_styling_end');
                        ?>
                    </div>
                </div>

                <?php
                /**
                 * Action -> Add new section after appearance listing content.  
                 * 
                 * @since   2.2.0 
                 */
                do_action('sjb_appearance_listing_content_after');
                ?>

                <h4><?php echo apply_filters('sjb_job_post_theme_options_title', esc_html__('Theme Options', 'simple-job-board')); ?></h4>
                <div class="sjb-section">
                    <div class="sjb-content">

                        <?php
                        /**
                         * Action -> Add new fields at start of job content.  
                         * 
                         * @since   2.3.2 
                         */
                        do_action('sjb_job_layout_start');
                        ?>
                        <div class="sjb-form-group">
                            <input type="radio" name="job_post_layout_settings" value="job_post_layout_version_one" id="sjb-version-one" <?php echo esc_attr($job_post_layout_version_one); ?> />
                            <label for="sjb-version-one"><?php _e('Classic Layout', 'simple-job-board'); ?></label>
                        </div>
                        <img src="<?php echo plugin_dir_url(dirname(dirname(__FILE__))) . 'admin/images/sjb-classic-layout.png' ?>" class="sjb-img-responsive">
                        <div class="sjb-form-group">
                            <input type="radio" name="job_post_layout_settings" value="job_post_layout_version_two" id="sjb-version-two" <?php echo esc_attr($job_post_layout_version_two); ?> />
                            <label for="sjb-version-two"><?php _e('Modern Layout', 'simple-job-board'); ?></label>
                        </div>
                        <img src="<?php echo plugin_dir_url(dirname(dirname(__FILE__))) . 'admin/images/sjb-modern-layout.png' ?>" class="sjb-img-responsive">

                        <?php
                        /**
                         * Action -> Add new fields at the end of job content.  
                         * 
                         * @since   2.3.2
                         */
                        do_action('sjb_job_layout_end');
                        ?>
                    </div>
                </div>

                <?php
                /**
                 * Action -> Add new section after appearance listing view .  
                 * 
                 * @since   2.2.0 
                 */
                do_action('sjb_appearance_listing_view_after');
                ?>

                <h4><?php echo apply_filters('sjb_job_listing_content_title', esc_html__('Job Listing Contents', 'simple-job-board')); ?></h4>
                <div class="sjb-section ">
                    <div class="sjb-content ">

                        <?php
                        /**
                         * Action -> Add new fields at start of job content.  
                         * 
                         * @since   2.2.0 
                         */
                        do_action('sjb_listing_content_start');
                        ?>
                        <div class="sjb-form-group">
                            <input type="radio" name="job_listing_content_settings" value="logo-detail" id="logo-detail" <?php echo esc_attr($logo_detail); ?> />
                            <label for="logo-detail"><?php _e('Display Job Listing with Company Logo and Detail', 'simple-job-board'); ?></label>
                        </div>
                        <img src="<?php echo plugin_dir_url(dirname(dirname(__FILE__))) . 'admin/images/list-company-logo.jpg' ?>" class="sjb-img-responsive">
                        <div class="sjb-form-group">
                            <input type="radio" name="job_listing_content_settings" value="without-logo-detail" id="without-logo-detail" <?php echo esc_attr($without_logo_detail); ?> />
                            <label for="without-logo-detail"><?php _e('Display Job Listing without Company Logo and Detail', 'simple-job-board'); ?></label>
                        </div>
                        <img src="<?php echo plugin_dir_url(dirname(dirname(__FILE__))) . 'admin/images/list-heading.jpg' ?>" class="sjb-img-responsive">
                        <div class="sjb-form-group">
                            <input type="radio" name="job_listing_content_settings" value="without-logo" id="without-logo" <?php echo esc_attr($without_logo); ?> />
                            <label for="without-logo"><?php _e('Display Job Listing without Company Logo', 'simple-job-board'); ?></label>
                        </div>
                        <img src="<?php echo plugin_dir_url(dirname(dirname(__FILE__))) . 'admin/images/list-detail.jpg' ?>">
                        <div class="sjb-form-group">
                            <input type="radio" name="job_listing_content_settings" value="without-detail" id="without-detail" <?php echo esc_attr($without_detail); ?> />
                            <label for="without-detail"><?php _e('Display Job Listing without Company Detail', 'simple-job-board'); ?></label>
                        </div>
                        <img src="<?php echo plugin_dir_url(dirname(dirname(__FILE__))) . 'admin/images/list-logo.jpg' ?>" class="sjb-img-responsive">

                        <?php
                        /**
                         * Action -> Add new fields at the end of job content.  
                         * 
                         * @since   2.2.0 
                         */
                        do_action('sjb_listing_content_end');
                        ?>
                    </div>
                </div>

                <?php
                /**
                 * Action -> Add new section after appearance listing content.  
                 * 
                 * @since   2.2.0 
                 */
                do_action('sjb_appearance_listing_content_after');
                ?>

                <h4><?php echo apply_filters('sjb_job_post_content_title', esc_html__('Job Post Content', 'simple-job-board')); ?></h4>
                <div class="sjb-section">
                    <div class="sjb-content">

                        <?php
                        /**
                         * Action -> Add new fields at start of job content.  
                         * 
                         * @since   2.3.2 
                         */
                        do_action('sjb_job_post_content_start');
                        ?>
                        <div class="sjb-form-group">
                            <input type="radio" name="job_post_content_settings" value="with-logo" id="job-logo-detail" <?php echo esc_attr($jobpost_logo); ?> />
                            <label for="job-logo-detail"><?php _e('Display Job Post with Company Logo', 'simple-job-board'); ?></label>
                        </div>
                        <img src="<?php echo plugin_dir_url(dirname(dirname(__FILE__))) . 'admin/images/single-details.png' ?>" class="sjb-img-responsive">
                        <div class="sjb-form-group">
                            <input type="radio" name="job_post_content_settings" value="without-logo" id="job-without-logo-detail" <?php echo esc_attr($jobpost_without_logo); ?> />
                            <label for="job-without-logo-detail"><?php _e('Display Job Post without Company Logo', 'simple-job-board'); ?></label>
                        </div>
                        <img src="<?php echo plugin_dir_url(dirname(dirname(__FILE__))) . 'admin/images/single-company-logo.jpg' ?>" class="sjb-img-responsive">

                        <?php
                        /**
                         * Action -> Add new fields at the end of job content.  
                         * 
                         * @since   2.3.2
                         */
                        do_action('sjb_job_post_content_end');
                        ?>
                    </div>
                </div>
                <?php
                /**
                 * Action -> Add new section after appearance listing content.  
                 * 
                 * @since   2.3.2 
                 */
                do_action('sjb_loader_image_sec_after');
                ?>

                <h4><?php echo apply_filters('sjb_job_color_options_title', esc_html__('Color Options', 'simple-job-board')); ?></h4>
                <div class="sjb-section">

                    <?php
                    /**
                     * Action -> Add new fields at start of job listing typography section.  
                     * 
                     * @since   2.2.0
                     * @since   2.4.0   Rename Action
                     */
                    do_action('sjb_job_color_options_start');
                    ?>

                    <ul class="sjb-typography">
                        <li class="sjb-typography-label">
                            <label><?php _e('Job Filters Background Color', 'simple-job-board'); ?></label>
                        </li>
                        <li class="sjb-typography-input">
                            <input type="text" value="<?php echo isset($job_board_typography['filters_background_color']) ? esc_attr($job_board_typography['filters_background_color']) : '#f2f2f2'; ?>" name="job_board_typography[filters_background_color]" class="sjb-color-picker" data-alpha="true" data-default-color="#f2f2f2" />
                        </li>
                    </ul>
                    <ul class="sjb-typography">
                        <li class="sjb-typography-label">
                            <label><?php _e('Job Title Color', 'simple-job-board'); ?></label>
                        </li>
                        <li class="sjb-typography-input">
                            <input type="text" value="<?php echo isset($job_board_typography['job_listing_title_color']) ? esc_attr($job_board_typography['job_listing_title_color']) : '#3b3a3c'; ?>" name="job_board_typography[job_listing_title_color]" class="sjb-color-picker" data-alpha="true" data-default-color="#3b3a3c" />
                        </li>
                    </ul>
                    <ul class="sjb-typography">
                        <li class="sjb-typography-label">
                            <label><?php _e('Headings Color', 'simple-job-board'); ?></label>
                        </li>
                        <li class="sjb-typography-input">
                            <input type="text" value="<?php echo isset($job_board_typography['headings_color']) ? esc_attr($job_board_typography['headings_color']) : '#3297fa'; ?>" name="job_board_typography[headings_color]" class="sjb-color-picker" data-alpha="true" data-default-color="#3297fa" />
                        </li>
                    </ul>
                    <ul class="sjb-typography">
                        <li class="sjb-typography-label">
                            <label><?php _e('Font Awesome Icon Color', 'simple-job-board'); ?></label>
                        </li>
                        <li class="sjb-typography-input">
                            <input type="text" value="<?php echo isset($job_board_typography['fontawesome_icon_color']) ? esc_attr($job_board_typography['fontawesome_icon_color']) : '#3b3a3c'; ?>" name="job_board_typography[fontawesome_icon_color]" class="sjb-color-picker" data-alpha="true" data-default-color="#3b3a3c" />
                        </li>
                    </ul>
                    <ul class="sjb-typography">
                        <li class="sjb-typography-label">
                            <label><?php _e('Font Awesome Text Color', 'simple-job-board'); ?></label>
                        </li>
                        <li class="sjb-typography-input">
                            <input type="text" value="<?php echo isset($job_board_typography['fontawesome_text_color']) ? esc_attr($job_board_typography['fontawesome_text_color']) : '#3297fa'; ?>" name="job_board_typography[fontawesome_text_color]" class="sjb-color-picker" data-alpha="true" data-default-color="#3297fa" />
                        </li>
                    </ul>
                    <ul class="sjb-typography">
                        <li class="sjb-typography-label">
                            <label><?php _e('Buttons Background Color', 'simple-job-board'); ?></label>
                        </li>
                        <li class="sjb-typography-input">
                            <input type="text" value="<?php echo isset($job_board_typography['job_submit_button_background_color']) ? esc_attr($job_board_typography['job_submit_button_background_color']) : '#3297fa'; ?>" name="job_board_typography[job_submit_button_background_color]" class="sjb-color-picker" data-alpha="true" data-default-color="#3297fa" />
                        </li>
                    </ul>
                    <ul class="sjb-typography">
                        <li class="sjb-typography-label">
                            <label><?php _e('Buttons Text Color', 'simple-job-board'); ?></label>
                        </li>
                        <li class="sjb-typography-input">
                            <input type="text" value="<?php echo isset($job_board_typography['job_submit_button_text_color']) ? esc_attr($job_board_typography['job_submit_button_text_color']) : '#fff'; ?>" name="job_board_typography[job_submit_button_text_color]" class="sjb-color-picker" data-alpha="true" data-default-color="#fff" />
                        </li>
                    </ul>
                    <ul class="sjb-typography">
                        <li class="sjb-typography-label">
                            <label><?php _e('Pagination Background Color', 'simple-job-board'); ?></label>
                        </li>
                        <li class="sjb-typography-input">
                            <input type="text" value="<?php echo isset($job_board_typography['pagination_background_color']) ? esc_attr($job_board_typography['pagination_background_color']) : '#164e91'; ?>" name="job_board_typography[pagination_background_color]" class="sjb-color-picker" data-alpha="true" data-default-color="#3297fa" />
                        </li>
                    </ul>
                    <ul class="sjb-typography">
                        <li class="sjb-typography-label">
                            <label><?php _e('Pagination Text Color', 'simple-job-board'); ?></label>
                        </li>
                        <li class="sjb-typography-input">
                            <input type="text" value="<?php echo isset($job_board_typography['pagination_text_color']) ? esc_attr($job_board_typography['pagination_text_color']) : '#fff'; ?>" name="job_board_typography[pagination_text_color]" class="sjb-color-picker" data-alpha="true" data-default-color="#fff" />
                        </li>
                    </ul>

                    <?php
                    /**
                     * Action -> Add new fields at the end of job listing page typography section.  
                     * 
                     * @since   2.2.0
                     * @since   2.4.0   Rename Action
                     */
                    do_action('sjb_job_color_options_end');
                    ?>
                </div>

                <?php
                /**
                 * Action -> Add new section after appearance listing typography content.  
                 * 
                 * @since   2.3.2 
                 */
                do_action('sjb_appearance_color_options_after');


                /**
                 * Action -> Add new section after appearance listing content.  
                 * 
                 * @since   2.3.2 
                 */
                ?>

                <h4><?php echo apply_filters('sjb_loader_image_sec_title', esc_html__('Loader Image', 'simple-job-board')); ?></h4>
                <div class="sjb-section sjb-loading-ui">
                    <div class="sjb-content">

                        <?php
                        /**
                         * Action -> Add new fields at start of job content.  
                         * 
                         * @since   2.3.2 
                         */
                        do_action('sjb_loader_image_sec_start');
                        ?>
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

                        <?php
                        /**
                         * Action -> Add new fields at the end of job content.  
                         * 
                         * @since   2.3.2
                         */
                        do_action('sjb_loader_image_sec_end');
                        ?>
                    </div>
                </div>

                <input type="hidden" value="1" name="admin_notices" />
                <input type="submit" name="job_general_options" id="job_general_options" class="button button-primary" value="<?php echo esc_html__('Save Changes', 'simple-job-board'); ?>" />
            </form>
        </div>
<?php
    }

    /**
     * Save Settings Appearance Section.
     * 
     * This function save the settings for job listing views, content and typography.
     *
     * @since   2.2.3
     */
    public function sjb_save_settings_section()
    {

        // Apearance Settings Paramerters
        $job_layout_settings = isset($_POST['job_pages_layout']) ? sanitize_text_field($_POST['job_pages_layout']) : '';
        $job_listing_content_settings = isset($_POST['job_listing_content_settings']) ? sanitize_text_field($_POST['job_listing_content_settings']) : '';
        $job_post_content_settings = isset($_POST['job_post_content_settings']) ? sanitize_text_field($_POST['job_post_content_settings']) : '';


        $job_post_layout_settings = isset($_POST['job_post_layout_settings']) ? sanitize_text_field($_POST['job_post_layout_settings']) : '';







        $container_class = isset($_POST['container_class']) ? sanitize_text_field($_POST['container_class']) : '';
        $container_id = isset($_POST['container_id']) ? sanitize_text_field($_POST['container_id']) : '';
        $job_board_typography = isset($_POST['job_board_typography']) ? array_map('sanitize_text_field', $_POST['job_board_typography']) : '';
        $sjb_fonts = isset($_POST['sjb_fonts']) ? sanitize_text_field($_POST['sjb_fonts']) : '';
        $no_fonts = isset($_POST['no_fonts']) ? sanitize_text_field($_POST['no_fonts']) : '';

        // Admin Email
        $image_url = filter_input(INPUT_POST, 'image_url');

        if (!empty($image_url)) {
            update_option('sjb_loader_image', $image_url);
        } elseif (isset($image_url) && '' === $image_url) {
            update_option('sjb_loader_image', '');
        }

        $fonts = 0;

        // Save Job Pages Layout
        if (!empty($job_layout_settings)) {
            update_option('job_board_pages_layout', $job_layout_settings);
        }

        // Save Job Listing Content
        if (!empty($job_listing_content_settings)) {
            update_option('job_board_listing', $job_listing_content_settings);
        }

        // Save Job Post Content
        if (!empty($job_post_content_settings)) {
            update_option('job_board_jobpost_content', $job_post_content_settings);
        }

        // Save Job Post layout
        if (!empty($job_post_layout_settings)) {
            update_option('job_post_layout_settings', $job_post_layout_settings);
        }

        // Save Container Class
        if (!empty($container_class)) {
            update_option('job_board_container_class', $container_class);
        }

        // Save Container Id
        if (!empty($container_id)) {
            update_option('job_board_container_id', $container_id);
        }

        // Save Job Board Typography
        if (!empty($job_board_typography)) {
            update_option('job_board_typography', $job_board_typography);
        }

        // Save Fonts Settings
        if (!empty($no_fonts)) {
            if (!empty($sjb_fonts)) {
                update_option('sjb_fonts', $sjb_fonts);
                $fonts = 1;
            }

            if (0 === $fonts) {
                update_option('sjb_fonts', 'disable-fonts');
            }
        }
    }
}
