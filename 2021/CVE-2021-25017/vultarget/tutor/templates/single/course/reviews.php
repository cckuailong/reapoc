<?php
/**
 * Template for displaying course reviews
 *
 * @since v.1.0.0
 *
 * @author Themeum
 * @url https://themeum.com
 *
 * @package TutorLMS/Templates
 * @version 1.4.5
 */


do_action('tutor_course/single/enrolled/before/reviews');

$disable = get_tutor_option('disable_course_review');
if ($disable){
    return;
}

$reviews = tutor_utils()->get_course_reviews();
if ( ! is_array($reviews) || ! count($reviews)){
	return;
}
?>

<div class="tutor-single-course-segment">
    <div class="course-student-rating-title">
        <h4 class="tutor-segment-title"><?php _e('Student Feedback', 'tutor'); ?></h4>
    </div>
    <div class="tutor-course-reviews-wrap">
        <div class="tutor-course-student-rating-wrap">
            <div class="course-avg-rating-wrap">
                <div class="tutor-row tutor-align-items-center">
                    <div class="tutor-col-auto">
                        <p class="course-avg-rating">
							<?php
							$rating = tutor_utils()->get_course_rating();
							echo number_format($rating->rating_avg, 1);
							?>
                        </p>
                        <p class="course-avg-rating-html">
		                    <?php tutor_utils()->star_rating_generator($rating->rating_avg);?>
                        </p>
                        <p class="tutor-course-avg-rating-total">Total <span><?php echo $rating->rating_count;?></span> Ratings</p>

                    </div>
                    <div class="tutor-col">
                        <div class="course-ratings-count-meter-wrap">
							<?php
							foreach ($rating->count_by_value as $key => $value){
							    $rating_count_percent = ($value > 0) ? ($value  * 100 ) / $rating->rating_count : 0;
							    ?>
                                <div class="course-rating-meter">
                                    <div class="rating-meter-col"><i class="tutor-icon-star-full"></i></div>
                                    <div class="rating-meter-col"><?php echo $key; ?></div>
                                    <div class="rating-meter-col rating-meter-bar-wrap">
                                        <div class="rating-meter-bar">
                                            <div class="rating-meter-fill-bar" style="width: <?php echo $rating_count_percent; ?>%;"></div>
                                        </div>
                                    </div>
                                    <div class="rating-meter-col rating-text-col">
                                        <?php
                                        echo $value.' ';
                                        echo $value > 1 ? __('ratings', 'tutor') : __('rating', 'tutor'); ?>
                                    </div>
                                </div>
							<?php } ?>
                        </div>
                    </div>

                </div>

            </div>
        </div>


        <div class="tutor-course-reviews-list">
			<?php
			foreach ($reviews as $review){
				$profile_url = tutor_utils()->profile_url($review->user_id);
				?>
                <div class="tutor-review-individual-item tutor-review-<?php echo $review->comment_ID; ?>">
                    <div class="review-left">
                        <div class="review-avatar">
                            <a href="<?php echo $profile_url; ?>"> <?php echo tutor_utils()->get_tutor_avatar($review->user_id); ?> </a>
                        </div>
                        <div class="tutor-review-user-info">
                            <div class="review-time-name">
                                <p> <a href="<?php echo $profile_url; ?>">  <?php echo $review->display_name; ?> </a> </p>
                                <p class="review-meta">
                                    <?php echo sprintf(__('%s ago', 'tutor'), human_time_diff(strtotime($review->comment_date))); ?>
                                </p>
                            </div>
                            <div class="individual-review-rating-wrap">
								<?php tutor_utils()->star_rating_generator($review->rating); ?>
                            </div>
                        </div>

                    </div>

                    <div class="review-content review-right">
						<?php echo wpautop(stripslashes($review->comment_content)); ?>
                    </div>
                </div>
				<?php
			}
			?>
        </div>
    </div>
</div>

<?php do_action('tutor_course/single/enrolled/after/reviews'); ?>
