<?php
/**
 * Reviews received
 *
 * @since v.1.2.13
 *
 * @author Themeum
 * @url https://themeum.com
 * @package Tutor
 *
 * @package TutorLMS/Templates
 * @version 1.4.3
 */

if(!tutor_utils()->is_instructor()) {
    include __DIR__ . '/reviews/given-reviews.php'; 
    return;
}

//Pagination Variable
$per_page = tutils()->get_option('pagination_per_page', 20);
$current_page = max( 1, tutor_utils()->avalue_dot('current_page', $_GET) );
$offset = ($current_page-1)*$per_page;

$reviews = tutor_utils()->get_reviews_by_instructor(get_current_user_id(), $offset, $per_page);
$given_count = tutor_utils()->get_reviews_by_user(0, 0, 0, true)->count;
?>

    <div class="tutor-dashboard-content-inner">
		<?php
		if (current_user_can(tutor()->instructor_role)){
			?>
            <div class="tutor-dashboard-inline-links">
                <ul>
                    <li class="active"><a href="<?php echo tutor_utils()->get_tutor_dashboard_page_permalink('reviews'); ?>"> <?php _e('Received', 'tutor'); ?> (<?php echo $reviews->count; ?>)</a> </li>
                    <?php if($given_count): ?>
                        <li> <a href="<?php echo tutor_utils()->get_tutor_dashboard_page_permalink('reviews/given-reviews'); ?>"> <?php _e('Given', 'tutor'); ?> (<?php echo $given_count; ?>)</a> </li>
                    <?php endif; ?>
                </ul>
            </div>
		<?php } ?>

        <div class="tutor-dashboard-reviews-wrap">

			<?php
			if ($reviews->count){
				?>
                <div class="tutor-dashboard-reviews">
                    <p class="tutor-dashboard-pagination-results-stats">
						<?php
						echo sprintf(__('Showing results %d to %d out of %d', 'tutor'), $offset +1, min($reviews->count, $offset +1+tutor_utils()->count($reviews->results)), $reviews->count) ;
						?>
                    </p>

					<?php
					foreach ($reviews->results as $review){
						$profile_url = tutor_utils()->profile_url($review->user_id);
						?>
                        <div class="tutor-dashboard-single-review tutor-review-<?php echo $review->comment_ID; ?>">
                            <div class="tutor-dashboard-review-header">

                                <div class="tutor-dashboard-review-heading">
                                    <div class="tutor-dashboard-review-title">
										<?php _e('Course: ', 'tutor'); ?>
                                        <a href="<?php echo get_the_permalink($review->comment_post_ID); ?>"><?php echo get_the_title($review->comment_post_ID); ?></a>
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
			<?php }else{
				?>
                <div class="tutor-dashboard-content-inner">
                    <p><?php _e("Sorry, but you are looking for something that isn't here." , 'tutor'); ?></p>
                </div>
				<?php
			} ?>

        </div>
    </div>

<?php
if ($reviews->count){
	?>
    <div class="tutor-pagination">
		<?php
		echo paginate_links( array(
			'format' => '?current_page=%#%',
			'current' => $current_page,
			'total' => ceil($reviews->count/$per_page)
		) );
		?>
    </div>
	<?php
}
