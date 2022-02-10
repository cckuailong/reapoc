<?php
if (!defined('ABSPATH'))
    exit;
/**
 * Template for displaying Announcements
 *
 * @since v.1.7.9
 *
 * @author Themeum
 * @url https://themeum.com
 *
 * @package TutorLMS/Templates
 * @version 1.7.9
 */
$per_page           = 10;
$paged              = max(1, tutor_utils()->avalue_dot('current_page', $_GET));

$order_filter       = isset($_GET['order']) ? sanitize_text_field($_GET['order']) : 'DESC';
$search_filter      = isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '';
//announcement's parent
$course_id          = isset($_GET['course-id']) ? sanitize_text_field($_GET['course-id']) : '';
$date_filter        = isset($_GET['date']) ? sanitize_text_field($_GET['date']) : '';

$year               = date('Y', strtotime($date_filter));
$month              = date('m', strtotime($date_filter));
$day                = date('d', strtotime($date_filter));

$args = array(
    'post_type'         => 'tutor_announcements',
    'post_status'       => 'publish',
    's'                 => sanitize_text_field($search_filter),
    'post_parent'       => sanitize_text_field($course_id),
    'posts_per_page'    => sanitize_text_field($per_page),
    'paged'             => sanitize_text_field($paged),
    'orderBy'           => 'ID',
    'order'             => sanitize_text_field($order_filter),

);
if (!empty($date_filter)) {
    $args['date_query'] = array(
        array(
            'year'      => $year,
            'month'     => $month,
            'day'       => $day
        )
    );
}
if (!current_user_can('administrator')) {
    $args['author'] = get_current_user_id();
}
$the_query = new WP_Query($args);

//get courses
$courses = (current_user_can('administrator')) ? tutils()->get_courses() : tutils()->get_courses_by_instructor();
$image_base = tutor()->url . '/assets/images/';
?>

<div class="tutor-dashboard-content-inner">
    <h4><?php echo __('Announcement', 'tutor'); ?></h4>
    <!--notice-->
    <div class="tutor-component-three-col-action new-announcement-wrap">
        <div class="tutor-announcement-big-icon">
            <i class="tutor-icon-speaker"></i>
        </div>
        <div>
            <small><?php _e('Create Announcement', 'tutor'); ?></small>
            <p>
                <strong>
                    <?php _e('Notify all students of your course', 'tutor'); ?>
                </strong>
            </p>
        </div>
        <div class="new-announcement-button">
            <button type="button" class="tutor-btn tutor-announcement-add-new">
                <?php _e('Add New Announcement', 'tutor'); ?>
            </button>
        </div>
    </div>
    <!--notice end-->
</div>
<!--sorting-->
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
        <label><?php _e('Date', 'tutor'); ?></label>
        <input type="text" class="tutor_date_picker tutor-announcement-date-sorting" id="tutor-announcement-datepicker" value="<?php echo $date_filter !== '' ? tutor_get_formated_date( get_option( 'date_format' ), $date_filter ) : ''; ?>" placeholder="<?php echo get_option( 'date_format' ); ?>" autocomplete="off" />
        <i class="tutor-icon-calendar"></i>
    </div>
</div>
<!--sorting end-->
<div class="tutor-announcement-table-wrap">
    <table class="tutor-dashboard-announcement-table" width="100%">
        <thead>
            <tr>
                <th style="width:24%"><?php _e('Date', 'tutor'); ?></th>
                <th style="text-align:left"><?php _e('Announcements', 'tutor'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if ($the_query->have_posts()) : ?>
                <?php foreach ($the_query->posts as $post) : ?>
                    <?php
                    $course = get_post($post->post_parent);
                    $dateObj = date_create($post->post_date);
                    $date_format = date_format($dateObj, 'j M, Y,<\b\r>h:i a'); 
                    ?>
                    <tr id="tutor-announcement-tr-<?php echo $post->ID; ?>">
                        <td class="tutor-announcement-date"><?php echo $date_format; ?></td>
                        <td class="tutor-announcement-content-wrap">
                            <div class="tutor-announcement-content">
                                <h4><?php echo esc_html($post->post_title); ?></h4>
                                <p><?php echo $course ? $course->post_title : ''; ?></p>
                            </div>
                            <div class="tutor-announcement-buttons">
                                <li>
                                    <button type="button" course-name="<?php echo esc_attr($course->post_title) ?>" announcement-date="<?php echo esc_attr($date_format) ?>" announcement-title="<?php echo esc_attr($post->post_title); ?>" announcement-summary="<?php echo esc_attr($post->post_content); ?>" course-id="<?php echo esc_attr($post->post_parent); ?>" announcement-id="<?php echo esc_attr($post->ID); ?>" class="tutor-btn bordered-btn tutor-announcement-details">
                                        <?php _e('Details', 'tutor'); ?>
                                    </button>
                                </li>
                                <li class="tutor-dropdown">
                                    <i class="tutor-icon-action"></i>
                                    <ul class="tutor-dropdown-menu">
                                        <li announcement-title="<?php echo $post->post_title; ?>" announcement-summary="<?php echo $post->post_content; ?>" course-id="<?php echo $post->post_parent; ?>" announcement-id="<?php echo $post->ID; ?>" class="tutor-announcement-edit">
                                            <i class="tutor-icon-pencil"></i>
                                            <?php _e('Edit', 'tutor'); ?>
                                        </li>
                                        <li class="tutor-announcement-delete" announcement-id="<?php echo $post->ID; ?>">
                                            <i class="tutor-icon-garbage"></i>
                                            <?php _e('Delete', 'tutor'); ?>
                                        </li>
                                    </ul>
                                </li>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="2">
                        <?php _e('Announcements not found', 'tutor'); ?>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

</div>

<!--pagination-->
<div class="tutor-pagination">
    <?php
        $big = 999999999; // need an unlikely integer
        
        echo paginate_links( array(
           
            'format'    => '?current_page=%#%',
            'current'   => $paged,
            'total'     => $the_query->max_num_pages
        ) );
      
    ?>
</div>
<!--pagination end-->

<?php
include 'announcements/create.php';
include 'announcements/update.php';
include 'announcements/details.php';
?>
