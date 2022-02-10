<?php
/**
 * Template for displaying frontend dashboard
 *
 * @since v.1.0.0
 *
 * @author Themeum
 * @url https://themeum.com
 *
 * @package TutorLMS/Templates
 * @version 1.4.3
 */

$is_by_short_code = isset($is_shortcode) && $is_shortcode===true;
if(!$is_by_short_code && !defined( 'OTLMS_VERSION' )) {
    get_header();
}

global $wp_query;

$dashboard_page_slug = '';
$dashboard_page_name = '';
if (isset($wp_query->query_vars['tutor_dashboard_page']) && $wp_query->query_vars['tutor_dashboard_page']) {
    $dashboard_page_slug = $wp_query->query_vars['tutor_dashboard_page'];
    $dashboard_page_name = $wp_query->query_vars['tutor_dashboard_page'];
}
/**
 * Getting dashboard sub pages
 */
if (isset($wp_query->query_vars['tutor_dashboard_sub_page']) && $wp_query->query_vars['tutor_dashboard_sub_page']) {
    $dashboard_page_name = $wp_query->query_vars['tutor_dashboard_sub_page'];
    if ($dashboard_page_slug){
        $dashboard_page_name = $dashboard_page_slug.'/'.$dashboard_page_name;
    }
}

$user_id = get_current_user_id();
$user = get_user_by('ID', $user_id);
$enable_profile_completion = tutils()->get_option('enable_profile_completion');

do_action('tutor_dashboard/before/wrap');
?>

    <div class="tutor-wrap tutor-dashboard tutor-dashboard-student">
        <div class="tutor-container">
            <div class="tutor-row">
                <div class="tutor-col-12">
                    <div class="tutor-dashboard-header">
                        <div class="tutor-dashboard-header-avatar">
                            <img src="<?php echo get_avatar_url($user_id, array('size' => 150)); ?>" />
                        </div>
                        <div class="tutor-dashboard-header-info">
                            <div class="tutor-dashboard-header-display-name">
                                <h4><strong><?php echo $user->display_name; ?></strong> </h4>
                            </div>
                            <?php $instructor_rating = tutils()->get_instructor_ratings($user->ID); ?>
                            <?php
                            if (current_user_can(tutor()->instructor_role)){
                                ?>
                                <div class="tutor-dashboard-header-stats">
                                    <div class="tutor-dashboard-header-ratings">
                                        <?php tutils()->star_rating_generator($instructor_rating->rating_avg); ?>
                                        <span><?php echo esc_html($instructor_rating->rating_avg);  ?></span>
                                        <span> (<?php echo sprintf(__('%d Ratings', 'tutor'), $instructor_rating->rating_count); ?>) </span>
                                    </div>
                                    <!--<div class="tutor-dashboard-header-notifications">
                                        <?php /*_e('Notification'); */?> <span>9</span>
                                    </div>-->
                                </div>
                            <?php } ?>
                        </div>

                        <div class="tutor-dashboard-header-button">
                            <?php
                            do_action( 'tutor_dashboard/before_header_button' );
                            $instructor_status = tutor_utils()->instructor_status();
                            $instructor_status = is_string($instructor_status) ? strtolower($instructor_status) : '';
                            $rejected_on = get_user_meta($user->ID , '_is_tutor_instructor_rejected', true);
                            $info_style = 'vertical-align: middle; margin-right: 7px;';
                            $info_message_style = 'display:inline-block; color:#7A7A7A; font-size: 15px;';

                            ob_start();
                            if (tutils()->get_option('enable_become_instructor_btn')) {
                                ?>
                                    <a id="tutor-become-instructor-button" style="vertical-align:middle" class="tutor-btn bordered-btn" href="<?php echo esc_url(tutils()->instructor_register_url()); ?>">
                                        <i class="tutor-icon-man-user"></i> &nbsp; <?php _e("Become an instructor", 'tutor'); ?>
                                    </a>
                                <?php
                            }
                            $become_button = ob_get_clean();

                            if(current_user_can(tutor()->instructor_role)){
                                $course_type = tutor()->course_post_type;
                                ?>
                                <a class="tutor-btn bordered-btn" href="<?php echo apply_filters('frontend_course_create_url', admin_url("post-new.php?post_type=".tutor()->course_post_type)); ?>">
                                    <i class="tutor-icon-checkbox-pen-outline"></i> &nbsp; <?php _e('Add A New Course', 'tutor'); ?>
                                </a>
                                <?php
                            }
                            else if($instructor_status=='pending'){
                                $on = get_user_meta($user->ID, '_is_tutor_instructor', true);
                                $on =  date('d F, Y', $on);
                                echo '<span style="'.$info_message_style.'">
                                        <i class="dashicons dashicons-info" style="color:#E53935; '.$info_style.'"></i>', 
                                        __('Your Application is pending from', 'tutor'), ' <b>', $on, '</b>',
                                    '</span>';
                            }
                            else if($rejected_on || $instructor_status!=='blocked'){
                                echo $become_button;
                            }
                            ?>
                        </div>

                        <?php 
                            if(
                                    $instructor_status != 'approved' && 
                                    $instructor_status != 'pending' && 
                                    $rejected_on && 
                                    get_user_meta( get_current_user_id(), 'tutor_instructor_show_rejection_message', true )
                                ) {
                                ?>
                                <div class="tutor-instructor-rejection-notice">
                                    <?php 
                                        $on = date('d F, Y', $rejected_on);
                                        echo '<span>
                                            <i class="dashicons dashicons-info"></i>', 
                                            __('Your application to become an instructor was rejected on', 'tutor') . ' ' . $on .
                                        '</span>
                                        <a href="?tutor_action=hide_instructor_notice">✕</a>';
                                    ?>
                                </div>
                                <?php
                            }
                        ?>
                    </div>
                </div>
                <?php do_action('tutor_dashboard/notification_area'); ?>
            </div>

            <div class="tutor-row">
                <div class="tutor-col-3 tutor-dashboard-left-menu">
                    <ul class="tutor-dashboard-permalinks">
                        <?php
                        $dashboard_pages = tutils()->tutor_dashboard_nav_ui_items();
                        foreach ($dashboard_pages as $dashboard_key => $dashboard_page) {
                            $menu_title = $dashboard_page;
                            $menu_link = tutils()->get_tutor_dashboard_page_permalink($dashboard_key);
                            $separator = false;
                            if (is_array($dashboard_page)){
                                $menu_title = tutils()->array_get('title', $dashboard_page);
                                //Add new menu item property "url" for custom link
                                if (isset($dashboard_page['url'])) {
                                    $menu_link = $dashboard_page['url'];
                                }
                                if (isset($dashboard_page['type']) && $dashboard_page['type'] == 'separator') {
                                    $separator = true;
                                }
                            }
                            if ($separator) {
                                echo '<li class="tutor-dashboard-menu-divider"></li>';
                                if ($menu_title) {
                                    echo "<li class='tutor-dashboard-menu-divider-header'>{$menu_title}</li>";
                                }
                            } else {
                                $li_class = "tutor-dashboard-menu-{$dashboard_key}";
                                if ($dashboard_key === 'index')
                                    $dashboard_key = '';
                                $active_class = $dashboard_key == $dashboard_page_slug ? 'active' : '';
                                echo "<li class='{$li_class}  {$active_class}'><a href='".$menu_link."'> {$menu_title} </a> </li>";
                            }
                        }
                        ?>
                    </ul>
                </div>

                <div class="tutor-col-9">
                    <div class="tutor-dashboard-content">
                        <?php
        
                        if ($dashboard_page_name){
                            do_action('tutor_load_dashboard_template_before', $dashboard_page_name);

                            /**
                             * Load dashboard template part from other location
                             * 
                             * this filter is basically added for adding templates from respective addons
                             * 
                             * @since version 1.9.3
                             */
                            $other_location = '';
                            $from_other_location = apply_filters( 'load_dashboard_template_part_from_other_location', $other_location );
                            
                            if ( $from_other_location == '' ) {
                                tutor_load_template("dashboard.".$dashboard_page_name);
                            } else {
                                //load template from other location full abspath
                                include_once $from_other_location;
                            }
                            
                            do_action('tutor_load_dashboard_template_before', $dashboard_page_name);
                        } else {
                            tutor_load_template("dashboard.dashboard");
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php do_action('tutor_dashboard/after/wrap'); ?>

<?php
if(!$is_by_short_code && !defined( 'OTLMS_VERSION' )) {
    get_footer();
}