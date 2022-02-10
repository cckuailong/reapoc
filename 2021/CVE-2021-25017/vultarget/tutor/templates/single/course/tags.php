<?php
/**
 * Template for displaying course tags
 *
 * @since v.1.0.0
 *
 * @author Themeum
 * @url https://themeum.com
 *
 * @package TutorLMS/Templates
 * @version 1.4.3
 */

do_action('tutor_course/single/before/tags');

$course_tags = get_tutor_course_tags();
if(is_array($course_tags) && count($course_tags)){ ?>
    <div class="tutor-single-course-segment">
        <div class="course-benefits-title">
            <h4 class="tutor-segment-title"><?php esc_html_e('Tags', 'tutor') ?></h4>
        </div>
        <div class="tutor-course-tags">
            <?php
                foreach ($course_tags as $course_tag){
                    $tag_link = get_term_link($course_tag->term_id);
                    echo "<a href='$tag_link'> $course_tag->name </a>";
                }
            ?>
        </div>
    </div>
<?php
}

do_action('tutor_course/single/after/tags'); ?>