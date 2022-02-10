<?php
/**
 * My Own reviews
 *
 * @since v.1.1.2
 *
 * @author Themeum
 * @url https://themeum.com
 *
 * @package TutorLMS/Templates
 * @version 1.4.3
 */

$reviews = tutor_utils()->get_reviews_by_user(0, 0, 150, true);
$review_count = $reviews->count;
$reviews = $reviews->results;
$received_count = tutor_utils()->get_reviews_by_instructor(0, 0, 0)->count;
?>

<div class="tutor-dashboard-content-inner">

	<?php
	if (current_user_can(tutor()->instructor_role)){
		?>
        <div class="tutor-dashboard-inline-links">
            <ul>
                <li><a href="<?php echo tutor_utils()->get_tutor_dashboard_page_permalink('reviews'); ?>"> <?php _e('Received', 'tutor'); ?> (<?php echo $received_count; ?>)</a> </li>
                <li class="active"> <a href="<?php echo tutor_utils()->get_tutor_dashboard_page_permalink('reviews/given-reviews'); ?>"> <?php _e('Given', 'tutor'); ?> (<?php echo $review_count; ?>)</a> </li>
            </ul>
        </div>
	<?php } ?>

    <div class="tutor-dashboard-reviews-wrap">
		<?php
		if ( ! is_array($reviews) || ! count($reviews)){ ?>
            <div class="tutor-dashboard-content-inner">
                <p><?php _e("Sorry, but you are looking for something that isn't here." , 'tutor'); ?></p>
            </div>
			<?php
		}
		?>

        <div class="tutor-dashboard-reviews">
			<?php
			foreach ($reviews as $review){
				$profile_url = tutor_utils()->profile_url($review->user_id);
				?>
                <div class="tutor-dashboard-single-review tutor-review-<?php echo $review->comment_ID; ?>">
                    <div class="tutor-dashboard-review-header">

                        <div class="tutor-dashboard-review-heading">
                            <div class="tutor-dashboard-review-title">
								<?php _e('Course: ', 'tutor'); ?>
                                <a href="<?php echo get_the_permalink($review->comment_post_ID); ?>"><?php echo get_the_title($review->comment_post_ID); ?></a>
                            </div>

                            <div class="tutor-dashboard-review-links">
                                <a href="javascript:;" class="open-tutor-edit-review-modal" data-review-id="<?php echo $review->comment_ID; ?>">
                                    <i class="tutor-icon-pencil"></i> <span><?php _e('Edit Feedback', 'tutor'); ?></span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="individual-dashboard-review-body">
                        <div class="individual-star-rating-wrap">
							<?php tutor_utils()->star_rating_generator($review->rating); ?>
                            <p class="review-meta"><?php  echo sprintf(__('%s ago', 'tutor'), human_time_diff(strtotime($review->comment_date)));  ?></p>
                        </div>

						<?php echo wpautop(stripslashes($review->comment_content)); ?>
                    </div>

                </div>
				<?php
			}
			?>
        </div>
    </div>
</div>

<div class="tutor-modal-wrap tutor-edit-review-modal-wrap">
    <div class="tutor-modal-content">
        <div class="modal-header">
            <div class="modal-title">
                <h1><?php _e('Edit Review', 'tutor'); ?></h1>
            </div>
            <div class="modal-close-wrap">
                <a href="javascript:;" class="modal-close-btn"><i class="tutor-icon-line-cross"></i> </a>
            </div>
        </div>
        <div class="modal-container"></div>
    </div>
</div>