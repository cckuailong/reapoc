<?php
/**
 * @package TutorLMS/Templates
 * @version 1.4.3
 */

?>

<h3><?php _e('Completed Course', 'tutor'); ?></h3>

<div class="tutor-dashboard-content-inner">

    <div class="tutor-dashboard-inline-links">
        <ul>
            <li><a href="<?php echo tutor_utils()->get_tutor_dashboard_page_permalink('enrolled-courses'); ?>"> <?php _e('All Courses', 'tutor'); ?></a> </li>
            <li><a href="<?php echo tutor_utils()->get_tutor_dashboard_page_permalink('enrolled-courses/active-courses'); ?>"> <?php _e('Active Courses', 'tutor'); ?> </a> </li>
            <li class="active"><a href="<?php echo tutor_utils()->get_tutor_dashboard_page_permalink('enrolled-courses/completed-courses'); ?>">
                    <?php _e('Completed Courses', 'tutor'); ?> </a> </li>
        </ul>
    </div>

	<?php
	$completed_courses = tutor_utils()->get_courses_by_user();

	if ($completed_courses && $completed_courses->have_posts()):
		while ($completed_courses->have_posts()):
			$completed_courses->the_post();

            $avg_rating = tutor_utils()->get_course_rating()->rating_avg;
            $tutor_course_img = get_tutor_course_thumbnail_src();
            ?>
            <div class="tutor-mycourse-wrap tutor-mycourse-<?php the_ID(); ?>">

                <a class="tutor-stretched-link" href="<?php the_permalink(); ?>"><span class="sr-only"><?php the_title(); ?></span></a>
                <div class="tutor-mycourse-thumbnail" style="background-image: url(<?php echo esc_url($tutor_course_img); ?>)"></div>

                <div class="tutor-mycourse-content">

                    <div class="tutor-mycourse-rating">
                        <?php tutor_utils()->star_rating_generator($avg_rating); ?>
                    </div>

                    <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                    
                    <div class="tutor-meta tutor-course-metadata">
                        <?php
                        $total_lessons = tutor_utils()->get_lesson_count_by_course();
                        $completed_lessons = tutor_utils()->get_completed_lesson_count_by_course();
                        ?>
                        <ul>
                            <li>
                                <?php
                                _e('Total Lessons:', 'tutor');
                                echo "<span>$total_lessons</span>";
                                ?>
                            </li>
                            <li>
                                <?php
                                _e('Completed Lessons:', 'tutor');
                                echo "<span>$completed_lessons / $total_lessons</span>";
                                ?>
                            </li>
                        </ul>
                    </div>
                    <?php tutor_course_completing_progress_bar(); ?>
                </div>

            </div>

			<?php
		endwhile;

		wp_reset_postdata();

	else:
        echo "<div class='tutor-mycourse-wrap'><div class='tutor-mycourse-content'>".__('There\'s no completed course', 'tutor')."</div></div>";
	endif;

	?>
</div>
