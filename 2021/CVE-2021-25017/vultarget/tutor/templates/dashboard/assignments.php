<?php

/**
 * Template for displaying Assignments
 *
 * @since v.1.3.4
 *
 * @author Themeum
 * @url https://themeum.com
 *
 * @package TutorLMS/Templates
 * @version 1.4.3
 */

global $wpdb;

$per_page           = 10;
$current_page       = max(1, tutor_utils()->avalue_dot('current_page', $_GET));
$offset             = ($current_page - 1) * $per_page;

$course_id          = isset($_GET['course-id']) ? sanitize_text_field($_GET['course-id']) : '';
$order_filter       = isset($_GET['order']) ? $_GET['order'] : 'DESC';
$date_filter        = isset($_GET['date']) ?  $_GET['date'] : '';

$current_user       = get_current_user_id();
$assignments        = tutor_utils()->get_assignments_by_instructor(null,  compact('course_id', 'order_filter', 'date_filter', 'per_page', 'offset'));
$courses            = (current_user_can('administrator')) ? tutils()->get_courses() : tutils()->get_courses_by_instructor();

?>

<div class="tutor-dashboard-announcement-sorting-wrap">
    <div class="tutor-form-group">
        <label for="">
            <?php _e('Courses', 'tutor'); ?>
        </label>
        <select class="tutor-report-category tutor-announcement-course-sorting ignore-nice-select">

            <option value=""><?php _e('All', 'tutor'); ?></option>

            <?php if ($courses) : ?>
                <?php foreach ($courses as $course) : ?>
                    <option value="<?php echo esc_attr($course->ID) ?>" <?php selected($course_id, $course->ID, 'selected') ?>>
                        <?php echo $course->post_title; ?>
                    </option>
                <?php endforeach; ?>
            <?php else : ?>
                <option value=""><?php _e('No course found', 'tutor'); ?></option>
            <?php endif; ?>
        </select>
    </div>
    <div class="tutor-form-group">
        <label><?php _e('Sort By', 'tutor'); ?></label>
        <select class="tutor-announcement-order-sorting ignore-nice-select">
            <option <?php selected($order_filter, 'ASC'); ?>><?php _e('ASC', 'tutor'); ?></option>
            <option <?php selected($order_filter, 'DESC'); ?>><?php _e('DESC', 'tutor'); ?></option>
        </select>
    </div>
    <div class="tutor-form-group tutor-announcement-datepicker">
        <label><?php _e('Create Date', 'tutor'); ?></label>
        <input type="text" class="tutor_date_picker tutor-announcement-date-sorting"  value="<?php echo $date_filter !== '' ? tutor_get_formated_date( get_option( 'date_format' ), $date_filter ) : ''; ?>" placeholder="<?php echo get_option( 'date_format' ); ?>" autocomplete="off" />
        <i class="tutor-icon-calendar"></i>
    </div>
</div>

<?php

if ($assignments->count) { ?>

    <div class="tutor-announcement-table-wrap">
        <table class="tutor-dashboard-announcement-table" width="100%">
            <thead>
                <tr>
                    <th><?php _e('Course Name', 'tutor') ?></th>
                    <th width="15%"><?php _e('Total Points', 'tutor') ?></th>
                    <th width="15%"><?php _e('Total Submits', 'tutor') ?></th>
                    <th width="10%">&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                <?php
                
                $submitted_url = tutor_utils()->get_tutor_dashboard_page_permalink('assignments/submitted');

                foreach ($assignments->results as $item) {
                    $max_mark = tutor_utils()->get_assignment_option($item->ID, 'total_mark');
                    $course_id = tutor_utils()->get_course_id_by('assignment', $item->ID);
                    $comment_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(comment_ID) FROM {$wpdb->comments} WHERE comment_type = 'tutor_assignment' AND comment_post_ID = %d", $item->ID));
                    // @TODO: assign post_meta is empty if user don't click on update button (http://prntscr.com/oax4t8) but post status is publish
                ?>
                    <tr>
                        <td>
                            <h4><?php echo esc_html($item->post_title); ?></h4>
                            <p><?php echo __('Course: ', 'tutor'); ?><a href='<?php echo get_the_permalink($course_id) ?>' target="_blank"><?php echo get_the_title($course_id); ?> </a></p>
                        </td>
                        <td><?php echo $max_mark ?></td>
                        <td><?php echo $comment_count ?></td>
                        <td>
                            <a href="<?php echo esc_url($submitted_url . '?assignment=' . $item->ID); ?>" class="tutor-btn bordered-btn tutor-announcement-details">
                                <?php _e('Details', 'tutor'); ?>
                            </a>
                        </td>
                    </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
    </div>

    <div class="tutor-pagination">
        <?php

        echo paginate_links(array(
            'format' => '?current_page=%#%',
            'current' => $current_page,
            'total' => ceil($assignments->count / $per_page)
        ));
        ?>
    </div>

<?php } else {
    echo '<p>' . __('No assignment available', 'tutor') . '</p>';
}
