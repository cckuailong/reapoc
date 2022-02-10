<?php
/**
 * Template for displaying course instructors/ instructor
 *
 * @since v.1.0.0
 *
 * @author Themeum
 * @url https://themeum.com
 *
 * @package TutorLMS/Templates
 * @version 1.4.3
 */



do_action('tutor_course/single/enrolled/before/instructors');

$instructors = tutor_utils()->get_instructors_by_course();
if ($instructors){
	$count = is_array($instructors) ? count($instructors) : 0;
	
	?>
	<h4 class="tutor-segment-title"><?php $count>1 ? _e('About the instructors', 'tutor') : _e('About the instructor', 'tutor'); ?></h4>

	<div class="tutor-course-instructors-wrap tutor-single-course-segment" id="single-course-ratings">
		<?php
		foreach ($instructors as $instructor){
		    $profile_url = tutor_utils()->profile_url($instructor->ID);
			?>
			<div class="single-instructor-wrap">
				<div class="single-instructor-top">
                    <div class="tutor-instructor-left">
                        <div class="instructor-avatar">
                            <a href="<?php echo $profile_url; ?>">
                                <?php echo tutor_utils()->get_tutor_avatar($instructor->ID); ?>
                            </a>
                        </div>

                        <div class="instructor-name">
                            <h3><a href="<?php echo $profile_url; ?>"><?php echo $instructor->display_name; ?></a> </h3>
                            <?php
                            if ( ! empty($instructor->tutor_profile_job_title)){
                                echo "<h4>{$instructor->tutor_profile_job_title}</h4>";
                            }
                            ?>
                        </div>
                    </div>
					<div class="instructor-bio">
						<?php echo $instructor->tutor_profile_bio ?>
					</div>
				</div>

                <?php
                $instructor_rating = tutor_utils()->get_instructor_ratings($instructor->ID);
                ?>

				<div class="single-instructor-bottom">
					<div class="ratings">
						<span class="rating-generated">
							<?php tutor_utils()->star_rating_generator($instructor_rating->rating_avg); ?>
						</span>

						<?php
						echo " <span class='rating-digits'>{$instructor_rating->rating_avg}</span> ";
						echo " <span class='rating-total-meta'>({$instructor_rating->rating_count} ".__('ratings', 'tutor').")</span> ";
						?>
					</div>

					<div class="courses">
						<p>
							<i class='tutor-icon-mortarboard'></i>
							<?php echo tutor_utils()->get_course_count_by_instructor($instructor->ID); ?> <span class="tutor-text-mute"> <?php _e('Courses', 'tutor'); ?></span>
						</p>
					</div>

					<div class="students">
						<?php
						$total_students = tutor_utils()->get_total_students_by_instructor($instructor->ID);
						?>

						<p>
							<i class='tutor-icon-user'></i>
							<?php echo $total_students; ?>
							<span class="tutor-text-mute">  <?php _e('students', 'tutor'); ?></span>
						</p>
					</div>
				</div>
			</div>
			<?php
		}
		?>
	</div>
	<?php
}

do_action('tutor_course/single/enrolled/after/instructors');
