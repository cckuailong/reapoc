<?php
/**
 * @package TutorLMS/Templates
 * @version 1.4.3
 */

if ( ! defined( 'ABSPATH' ) )
	exit;

$enable_show_reviews_wrote = tutor_utils()->get_option('students_own_review_show_at_profile');
if ( ! $enable_show_reviews_wrote){
    return;
}

$user_name = sanitize_text_field(get_query_var('tutor_student_username'));
$get_user = tutor_utils()->get_user_by_login($user_name);
$user_id = $get_user->ID;


$reviews = tutor_utils()->get_reviews_by_user($user_id);

if ( ! is_array($reviews) || ! count($reviews)){ ?>
    <div>
		<p><?php _e("No review yet." , 'tutor'); ?></p>
    </div>
    <?php
	return;
}
?>

<div class=" tutor-course-reviews-wrap">
    <div class="tutor-reviews-list">
		<?php
		foreach ($reviews as $review){
			$profile_url = tutor_utils()->profile_url($review->user_id);
			?>
            <div class="tutor-review-individual-item tutor-review-<?php echo $review->comment_ID; ?>">
                <div class="review-left">
                    <div class="review-avatar">
                        <a href="<?php echo $profile_url; ?>">
		                    <?php echo tutor_utils()->get_tutor_avatar($review->user_id); ?>
                        </a>
                    </div>

                    <div class="review-time-name">

                        <p> <a href="<?php echo $profile_url; ?>">  <?php echo $review->display_name; ?> </a> </p>
                        <p class="review-meta">
                            <?php echo sprintf(__('%s ago', 'tutor'), human_time_diff(strtotime($review->comment_date))) ?>
                        </p>
                    </div>
                </div>

                <div class="review-content review-right">

                    <div class="individual-review-course-name">
                        <?php _e('On', 'tutor'); ?>
                        <a href="<?php echo get_the_permalink($review->comment_post_ID); ?>"><?php echo get_the_title
                        ($review->comment_post_ID);
                        ?></a>
                    </div>

                    <div class="individual-review-rating-wrap">
						<?php tutor_utils()->star_rating_generator($review->rating); ?>
                    </div>
					<?php echo wpautop($review->comment_content); ?>
                </div>
            </div>
			<?php
		}
		?>
    </div>
</div>