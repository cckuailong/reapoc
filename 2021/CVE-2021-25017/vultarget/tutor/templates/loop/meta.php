<?php
/**
 * @package TutorLMS/Templates
 * @version 1.4.3
 */

global $post, $authordata;

$profile_url = tutor_utils()->profile_url($authordata->ID);
?>



<div class="tutor-course-loop-meta">
    <?php
    $course_duration = get_tutor_course_duration_context();
    $course_students = tutor_utils()->count_enrolled_users_by_course();
    $disable_total_enrolled = (int) tutor_utils()->get_option( 'disable_course_total_enrolled' );
    ?>
    <?php if ( ! $disable_total_enrolled ) : ?>
    <div class="tutor-single-loop-meta">
        <i class='tutor-icon-user'></i><span><?php echo $course_students; ?></span>
    </div>
    <?php endif; ?>
    <?php
    if(!empty($course_duration)) { ?>
        <div class="tutor-single-loop-meta">
            <i class='tutor-icon-clock'></i> <span><?php echo $course_duration; ?></span>
        </div>
    <?php } ?>
</div>


<div class="tutor-loop-author">
    <div class="tutor-single-course-avatar">
        <a href="<?php echo $profile_url; ?>"> <?php echo tutor_utils()->get_tutor_avatar($post->post_author); ?></a>
    </div>
    <div class="tutor-single-course-author-name">
        <span><?php _e('by', 'tutor'); ?></span>
        <a href="<?php echo $profile_url; ?>"><?php echo get_the_author(); ?></a>
    </div>

    <div class="tutor-course-lising-category">
        <?php
        $course_categories = get_tutor_course_categories();
        if(!empty($course_categories) && is_array($course_categories ) && count($course_categories)){
            ?>
            <span><?php esc_html_e('In', 'tutor') ?></span>
            <?php
            foreach ($course_categories as $course_category){
                $category_name = $course_category->name;
                $category_link = get_term_link($course_category->term_id);
                echo "<a href='$category_link'>$category_name </a>";
            }
        }
        ?>
    </div>
</div>