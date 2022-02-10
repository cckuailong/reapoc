<?php
/**
 * Template for displaying lead info
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

global $post, $authordata;
$profile_url = tutor_utils()->profile_url($authordata->ID);
?>

<div class="tutor-single-course-segment tutor-single-course-lead-info">

	<?php
	$disable = get_tutor_option('disable_course_review');
	if ( ! $disable){
		?>
        <div class="tutor-leadinfo-top-meta">
        <span class="tutor-single-course-rating">
            <?php
            $course_rating = tutor_utils()->get_course_rating();
            tutor_utils()->star_rating_generator($course_rating->rating_avg);
            ?>
            <span class="tutor-single-rating-count">
                <?php
                echo $course_rating->rating_avg;
                echo '<i>('.$course_rating->rating_count.')</i>';
                ?>
            </span>
        </span>
        </div>
	<?php } ?>

	<?php do_action('tutor_course/single/title/before'); ?>
    <h1 class="tutor-course-header-h1"><?php the_title(); ?></h1>

	<?php do_action('tutor_course/single/title/after'); ?>
	<?php do_action('tutor_course/single/lead_meta/before'); ?>

    <div class="tutor-single-course-meta tutor-meta-top">
        <?php
            $disable_course_author = get_tutor_option('disable_course_author');
            $disable_course_level = get_tutor_option('disable_course_level');
            $disable_course_share = get_tutor_option('disable_course_share');
        ?>
        <ul>
            <?php if ( !$disable_course_author){ ?>
                <li class="tutor-single-course-author-meta">
                    <div class="tutor-single-course-avatar">
                        <a href="<?php echo $profile_url; ?>"> <?php echo tutor_utils()->get_tutor_avatar($post->post_author); ?></a>
                    </div>
                    <div class="tutor-single-course-author-name">
                        <span><?php _e('by', 'tutor'); ?></span>
                        <a href="<?php echo tutor_utils()->profile_url($authordata->ID); ?>"><?php echo get_the_author(); ?></a>
                    </div>
                </li>
            <?php } ?>

            <?php if ( !$disable_course_level){ ?>
                <li class="tutor-course-level">
                    <strong><?php _e('Course level:', 'tutor'); ?></strong>
                    <?php echo get_tutor_course_level(); ?>
                </li>
            <?php } ?>

            <?php if ( !$disable_course_share ){ ?>
                <li class="tutor-social-share">
                    <?php tutor_social_share(); ?>
                </li>
            <?php } ?>
        </ul>

    </div>

    <div class="tutor-single-course-meta tutor-lead-meta">
        <ul>
			<?php
			$course_categories = get_tutor_course_categories();
			if(is_array($course_categories) && count($course_categories)){
				?>
                <li>
                    <span><?php esc_html_e('Categories', 'tutor') ?></span>
					<?php
					foreach ($course_categories as $course_category){
						$category_name = $course_category->name;
						$category_link = get_term_link($course_category->term_id);
						echo "<a href='$category_link'>$category_name</a>";
					}
					?>
                </li>
			<?php } ?>

            <?php
            $disable_course_duration = get_tutor_option('disable_course_duration');
            $disable_total_enrolled = get_tutor_option('disable_course_total_enrolled');
            $disable_update_date = get_tutor_option('disable_course_update_date');
            $course_duration = get_tutor_course_duration_context();
            
			if( !empty($course_duration) && !$disable_course_duration){ ?>
                <li>
                    <span><?php esc_html_e('Duration', 'tutor') ?></span>
                    <?php echo $course_duration; ?>
                </li>
            <?php }
            
            if( !$disable_total_enrolled){ ?>
                <li>
                    <span><?php esc_html_e('Total Enrolled', 'tutor') ?></span>
                    <?php echo (int) tutor_utils()->count_enrolled_users_by_course(); ?>
                </li>
            <?php }

            if( !$disable_update_date){ ?>
                <li>
                    <span><?php esc_html_e('Last Update', 'tutor') ?></span>
                    <?php echo esc_html(get_the_modified_date()); ?>
                </li>
            <?php } ?>
        </ul>
    </div>

	<?php do_action('tutor_course/single/lead_meta/after'); ?>
	<?php do_action('tutor_course/single/excerpt/before'); ?>

	<?php
	$excerpt = tutor_get_the_excerpt();
    $disable_about = get_tutor_option('disable_course_about');
	if (! empty($excerpt) && ! $disable_about){
		?>
        <div class="tutor-course-summery">
            <h4  class="tutor-segment-title"><?php esc_html_e('About Course', 'tutor') ?></h4>
			<?php echo $excerpt; ?>
        </div>
		<?php
	}
	?>

	<?php do_action('tutor_course/single/excerpt/after'); ?>

</div>