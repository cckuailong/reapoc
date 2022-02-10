<?php
if (!defined('ABSPATH'))
    exit;

/**
 * Since 1.7.9
 * configure query with get params
 */
$per_page           = 10;
$paged              = (isset($_GET['paged']) && is_numeric($_GET['paged']) && $_GET['paged']>=1) ? $_GET['paged'] : 1;

$order_filter       = (isset($_GET['order']) && strtolower($_GET['order'])=='asc') ? 'ASC' : 'DESC';
$search_filter      = sanitize_text_field( tutor_utils()->array_get('search', $_GET, '') );
//announcement's parent
$course_id          = sanitize_text_field( tutor_utils()->array_get('course-id', $_GET, '') );
$date_filter        = sanitize_text_field( tutor_utils()->array_get('date', $_GET, '') );

$year               = date('Y', strtotime($date_filter));
$month              = date('m', strtotime($date_filter));
$day                = date('d', strtotime($date_filter));

$args = array(
    'post_type'         => 'tutor_announcements',
    'post_status'       => 'publish',
    's'                 => $search_filter,
    'post_parent'       => $course_id,
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
?>

<div class="tutor-admin-search-box-container">

    <div>
        <div class="menu-label"><?php _e('Search', 'tutor'); ?></div>
        <div>
            <input type="text" class="tutor-report-search tutor-announcement-search-field" value="<?php echo $search_filter; ?>" autocomplete="off" placeholder="<?php _e('Search Announcements', 'tutor'); ?>" />
            <button class="tutor-report-search-btn tutor-announcement-search-sorting"><i class="tutor-icon-magnifying-glass-1"></i></button>
        </div>
    </div>

    <div>
        <div class="menu-label"><?php _e('Courses', 'tutor'); ?></div>
        <div>
            <?php
            //get courses
            $courses = (current_user_can('administrator')) ? tutils()->get_courses() : tutils()->get_courses_by_instructor();
            ?>

            <select class="tutor-report-category tutor-announcement-course-sorting">
               
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
    </div>

    <div>
        <div class="menu-label"><?php _e('Sort By', 'tutor'); ?></div>
        <div>
            <select class="tutor-report-sort tutor-announcement-order-sorting">
                <option <?php selected($order_filter, 'ASC'); ?>>ASC</option>
                <option <?php selected($order_filter, 'DESC'); ?>>DESC</option>
            </select>
        </div>
    </div>

    <div>
        <div class="menu-label"><?php _e('Date', 'tutor'); ?></div>
        <div class="date-range-input">
            <input type="text" class="tutor_date_picker tutor-announcement-date-sorting" id="tutor-announcement-datepicker" placeholder="<?php _e( get_option( 'date_format' ), 'tutor' );?>" value="<?php echo '' !== $date_filter ? tutor_get_formated_date( get_option( 'date_format' ), $date_filter ) : ''; ?>" autocomplete="off" />
            <i class="tutor-icon-calendar"></i>
        </div>
    </div>
</div>

<div class="tutor-list-wrap tutor-report-course-list">
    <div class="tutor-list-header tutor-announcements-header">
        <div class="heading"><?php _e('Announcements', 'tutor'); ?></div>
        <button type="button" class="tutor-btn bordered-btn tutor-announcement-add-new">
            <?php _e('Add new', 'tutor'); ?>
        </button>
    </div>

    <table class="tutor-list-table tutor-announcement-table">
        <thead>
            <tr>
                <th style="width:20%"><?php _e('Date', 'tutor'); ?></th>
                <th><?php _e('Announcements', 'tutor'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if ($the_query->have_posts()) : ?>
                <?php foreach ($the_query->posts as $post) : ?>
                    <?php
                    $course = get_post($post->post_parent);
                    $dateObj = date_create($post->post_date);
                    $date_format = date_format($dateObj, 'F j, Y, g:i a');
                    ?>
                    <tr id="tutor-announcement-tr-<?php echo $post->ID; ?>">
                        <td class="tutor-announcement-date"><?php echo esc_html($date_format); ?></td>
                        <td class="tutor-announcement-content-wrap">
                            <div class="tutor-announcement-content">
                                <span>
                                    <?php echo esc_html($post->post_title); ?>
                                </span>
                                <p>
                                    <?php echo $course ? $course->post_title : ''; ?>
                                </p>
                            </div>
                            <div class="tutor-announcement-buttons">

                                <button type="button" announcement-title="<?php echo esc_attr($post->post_title); ?>" announcement-summary="<?php echo esc_attr( $post->post_content ); ?>" course-id="<?php echo $post->post_parent; ?>" announcement-id="<?php echo $post->ID; ?>" class="tutor-btn bordered-btn tutor-announcement-edit">
                                    <?php _e('Edit', 'tutor'); ?>
                                </button>
                                <button type="button" class="tutor-btn bordered-btn tutor-announcement-delete" announcement-id="<?php echo $post->ID; ?>">
                                    <?php _e('Delete', 'tutor'); ?>
                                </button>

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
<div class="tutor-announcement-pagination">
    <?php
    $big = 999999999; // need an unlikely integer

    echo paginate_links(array(
        'base'      => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
        'format'    => '?current_page=%#%',
        'current'   => $paged,
        'total'     => $the_query->max_num_pages
    ));
    ?>
</div>
<!--pagination end-->

<!--create announcements modal-->
<div class="tutor-modal-wrap tutor-announcements-modal-wrap tutor-announcement-create-modal" id="tutor-annoucement-backend-create-modal">
    <div class="tutor-modal-content">
        <div class="modal-header">
            <div class="modal-title">
                <h1><?php _e('Create New Announcement', 'tutor'); ?></h1>
            </div>
            <div class="tutor-announcements-modal-close-wrap">
                <a href="#" class="tutor-announcement-close-btn">
                    <i class="tutor-icon-line-cross"></i>
                </a>
            </div>
        </div>
        <div class="modal-container">
            <form action="" class="tutor-announcements-form">
                <?php tutor_nonce_field(); ?>
                <div class="tutor-option-field-row">
                    <label for="tutor_announcement_course">
                        <?php _e('Select Course', 'tutor'); ?>
                    </label>

                    <div class="tutor-announcement-form-control">
                        <select name="tutor_announcement_course" id="" required>
                            <?php if ($courses) : ?>
                                <?php foreach ($courses as $course) : ?>

                                    <option value="<?php echo esc_attr($course->ID) ?>">
                                        <?php echo $course->post_title; ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <option value="">No course found</option>
                            <?php endif; ?>
                        </select>

                    </div>
                </div>

                <div class="tutor-option-field-row">
                    <label for="tutor_announcement_course">
                        <?php _e('Announcement Title', 'tutor'); ?>
                    </label>

                    <div class="tutor-announcement-form-control">
                        <input type="text" name="tutor_announcement_title" value="" placeholder="<?php _e('Announcement title', 'tutor'); ?>" required>
                    </div>
                </div>

                <div class="tutor-option-field-row">
                    <label for="tutor_announcement_course">
                        <?php _e('Summary', 'tutor'); ?>
                    </label>

                    <div class="tutor-announcement-form-control">
                        <textarea rows="6" type="text" name="tutor_announcement_summary" value="" placeholder="<?php _e('Summary...', 'tutor'); ?>" required></textarea>
                    </div>
                </div>
                
                <?php do_action('tutor_announcement_editor/after'); ?>

                <div class="tutor-option-field-row">
                    <div class="tutor-announcements-create-alert"></div>
                </div>

                <div class="modal-footer">
                    <div class="tutor-quiz-builder-modal-control-btn-group">
                        <div class="quiz-builder-btn-group-left">
                            <button type="submit" class="tutor-btn"><?php _e('Publish', 'tutor') ?></button>
                        </div>
                        <div class="quiz-builder-btn-group-right">
                            <button type="button" class="quiz-modal-tab-navigation-btn  quiz-modal-btn-cancel tutor-announcement-close-btn"><?php _e('Cancel', 'tutor') ?></button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!--create announcements modal end-->

<!--update announcements modal-->
<div class="tutor-modal-wrap tutor-announcements-modal-wrap tutor-accouncement-update-modal">
    <div class="tutor-modal-content">
        <div class="modal-header">
            <div class="modal-title">
                <h1><?php _e('Update Announcement', 'tutor'); ?></h1>
            </div>
            <div class="tutor-announcements-modal-close-wrap">
                <a href="#" class="tutor-announcement-close-btn">
                    <i class="tutor-icon-line-cross"></i>
                </a>
            </div>
        </div>

        <div class="modal-container">
            <form action="" class="tutor-announcements-update-form">
                <?php tutor_nonce_field(); ?>
                <input type="hidden" name="announcement_id" id="announcement_id">
                <div class="tutor-option-field-row">
                    <label for="tutor_announcement_course">
                        <?php _e('Select Course', 'tutor'); ?>
                    </label>

                    <div class="tutor-announcement-form-control">
                        <select name="tutor_announcement_course" id="tutor-announcement-course-id" required>
                            <?php if ($courses) : ?>
                                <?php foreach ($courses as $course) : ?>

                                    <option value="<?php echo esc_attr($course->ID) ?>">
                                        <?php echo $course->post_title; ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <option value="">No course found</option>
                            <?php endif; ?>
                        </select>

                    </div>
                </div>

                <div class="tutor-option-field-row">
                    <label for="tutor_announcement_course">
                        <?php _e('Announcement Title', 'tutor'); ?>
                    </label>

                    <div class="tutor-announcement-form-control">
                        <input type="text" name="tutor_announcement_title" id="tutor-announcement-title" value="" placeholder="<?php _e('Announcement title', 'tutor'); ?>" required>
                    </div>
                </div>

                <div class="tutor-option-field-row">
                    <label for="tutor_announcement_course">
                        <?php _e('Summary', 'tutor'); ?>
                    </label>

                    <div class="tutor-announcement-form-control">
                        <textarea rows="6" type="text" id="tutor-announcement-summary" name="tutor_announcement_summary" value="" placeholder="<?php _e('Summary...', 'tutor'); ?>" required></textarea>
                    </div>
                </div>

                <?php do_action('tutor_announcement_editor/after'); ?>

                <div class="tutor-option-field-row">
                    <div class="tutor-announcements-update-alert"></div>
                </div>

                <div class="modal-footer">
                    <div class="tutor-quiz-builder-modal-control-btn-group">
                        <div class="quiz-builder-btn-group-left">
                            <button type="submit" class="tutor-btn"><?php _e('Update', 'tutor') ?></button>
                        </div>
                        <div class="quiz-builder-btn-group-right">
                            <button type="button" class="quiz-modal-tab-navigation-btn  quiz-modal-btn-cancel tutor-announcement-close-btn"><?php _e('Cancel', 'tutor') ?></button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!--update announcements modal end-->