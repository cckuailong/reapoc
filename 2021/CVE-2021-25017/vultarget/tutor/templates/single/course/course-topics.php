<?php
/**
 * Template for displaying single course
 *
 * @since v.1.0.0
 *
 * @author Themeum
 * @url https://themeum.com
 *
 * @package TutorLMS/Templates
 * @version 1.4.3
 */

if ( ! defined( 'ABSPATH' ) )
	exit;

$topics = tutor_utils()->get_topics();
$course_id = get_the_ID();
$is_enrolled = tutor_utils()->is_enrolled($course_id);

?>


<?php do_action('tutor_course/single/before/topics'); ?>

<?php if($topics->have_posts()) { ?>
    <div class="tutor-single-course-segment  tutor-course-topics-wrap">
        <div class="tutor-course-topics-header">
            <div class="tutor-course-topics-header-left">
                <h4 class="tutor-segment-title"><?php _e('Topics for this course', 'tutor'); ?></h4>
            </div>
            <div class="tutor-course-topics-header-right">
				<?php
				$tutor_lesson_count = tutor_utils()->get_lesson_count_by_course($course_id);
				$tutor_course_duration = get_tutor_course_duration_context($course_id);

				if($tutor_lesson_count) {
					echo '<span>' . $tutor_lesson_count . ' ' . __('Lessons', 'tutor') . '</span>';
				}
				if($tutor_course_duration){
					echo "<span>$tutor_course_duration</span>";
				}
				?>
            </div>
        </div>
        <div class="tutor-course-topics-contents">
			<?php

			$index = 0;

			if ($topics->have_posts()){
				while ($topics->have_posts()){ $topics->the_post();
					$topic_summery = get_the_content();
					$index++;
					?>

                    <div class="tutor-course-topic tutor-topics-in-single-lesson <?php if($index == 1) echo "tutor-active"; ?>">
                        <div class="tutor-course-title <?php echo $topic_summery ? 'has-summery' : ''; ?>">
                            <h4> 
								<i class="tutor-icon-plus"></i> 
								<?php
								the_title();
								if($topic_summery) {
									echo "<span class='toggle-information-icon'>&quest;</span>";
								}
								?>
							</h4>
                        </div>

						<?php
						if ($topic_summery){
							?>
							<div class="tutor-topics-summery">
								<?php echo $topic_summery; ?>
							</div>
							<?php
						}
						?>

                        <div class="tutor-course-lessons" style="<?php echo $index > 1 ? 'display: none' : ''; ?>">

							<?php
							$lessons = tutor_utils()->get_course_contents_by_topic(get_the_ID(), -1);
							
							if ($lessons->have_posts()){
								while ($lessons->have_posts()){ $lessons->the_post();
									global $post;

									$video = tutor_utils()->get_video_info();

									$play_time = false;
									if ($video){
										$play_time = $video->playtime;
									}

									$lesson_icon = $play_time ? 'tutor-icon-youtube' : 'tutor-icon-document-alt';

									if ($post->post_type === 'tutor_quiz'){
										$lesson_icon = 'tutor-icon-doubt';
									}
									if ($post->post_type === 'tutor_assignments'){
										$lesson_icon = 'tutor-icon-clipboard';
									}
									?>

                                    <div class="tutor-course-lesson">
                                        <h5>
											<?php
											$lesson_title = '';
											if (has_post_thumbnail()){
												$thumbnail_url = get_the_post_thumbnail_url(get_the_ID(), 'thumbnail');
												$lesson_title .= "<i style='background:url({$thumbnail_url})' class='tutor-lesson-thumbnail-icon $lesson_icon'></i>";
											}else{
												$lesson_title .= "<i class='$lesson_icon'></i>";
											}

											$countdown = '';
											if ($post->post_type === 'tutor_zoom_meeting'){
												$lesson_title = '<i class="zoom-icon"><img src="'.TUTOR_ZOOM()->url . 'assets/images/zoom-icon-grey.svg"></i>';
												
												$zoom_meeting = tutor_zoom_meeting_data($post->ID);
												$countdown = '<div class="tutor-zoom-lesson-countdown tutor-lesson-duration" data-timer="'.$zoom_meeting->countdown_date.'" data-timezone="'.$zoom_meeting->timezone.'"></div>';
											}

											
											// Show clickable content if enrolled
											// Or if it is public and not paid, then show content forcefully
											if ($is_enrolled || (get_post_meta($course_id, '_tutor_is_public_course', true)=='yes' && !tutor_utils()->is_course_purchasable($course_id))){
												$lesson_title .= "<a href='".get_the_permalink()."'> ".get_the_title()." </a>";

												$lesson_title .= $play_time ? "<span class='tutor-lesson-duration'>".tutor_utils()->get_optimized_duration($play_time)."</span>" : '';

												if ($countdown) {
													if ($zoom_meeting->is_expired) {
														$lesson_title .= '<span class="tutor-zoom-label">'.__('Expired', 'tutor').'</span>';
													} else if ($zoom_meeting->is_started) {
														$lesson_title .= '<span class="tutor-zoom-label tutor-zoom-live-label">'.__('Live', 'tutor').'</span>';
													}
													$lesson_title .= $countdown;
												}

												echo $lesson_title;
											}else{
												$lesson_title .= get_the_title();
												$lesson_title .= $play_time ? "<span class='tutor-lesson-duration'>".tutor_utils()->get_optimized_duration($play_time)."</span>" : '';
												echo apply_filters('tutor_course/contents/lesson/title', $lesson_title, get_the_ID());
											}

											?>
                                        </h5>
                                    </div>

									<?php
								}
								$lessons->reset_postdata();
							}
							?>
                        </div>
                    </div>
					<?php
				}
				$topics->reset_postdata();
			}
			?>
        </div>
    </div>
<?php } ?>


<?php do_action('tutor_course/single/after/topics'); ?>