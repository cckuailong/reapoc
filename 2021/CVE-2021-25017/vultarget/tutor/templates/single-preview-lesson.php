<?php
/**
 * Template for displaying single lesson
 *
 * @since v.1.0.0
 *
 * @author Themeum
 * @url https://themeum.com
 *
 * @package TutorLMS/Templates
 * @version 1.4.3
 */

/*
get_header();

global $post;
$currentPost = $post;
*/?><!--
<?php /*do_action('tutor_lesson/single/before/wrap'); */?>
    <div <?php /*tutor_post_class('tutor-single-lesson-wrap tutor-page-wrap'); */?>>
        <div class="tutor-container">
            <div class="tutor-row">
                <div class="tutor-col-12">
                    <?php /*tutor_lesson_video(); */?>
                    <?php /*the_content(); */?>
                    <?php /*get_tutor_posts_attachments(); */?>
                </div>
            </div>
        </div>
    </div>
--><?php /*do_action('tutor_lesson/single/after/wrap');

get_footer();*/



get_tutor_header();

global $post;
$currentPost = $post;

$enable_spotlight_mode = tutor_utils()->get_option('enable_spotlight_mode');
?>

<?php do_action('tutor_lesson/single/before/wrap'); ?>
<div class="tutor-single-lesson-wrap <?php echo $enable_spotlight_mode ? "tutor-spotlight-mode" : ""; ?>">
    <div class="tutor-lesson-sidebar">


        <!-- Start: Sidebar -->

        <?php
            $course_id = 0;
            if ($post->post_type === 'tutor_quiz'){
                $course = tutor_utils()->get_course_by_quiz(get_the_ID());
                $course_id = $course->ID;
            }elseif($post->post_type === 'tutor_assignments'){
                $course_id = tutor_utils()->get_course_id_by('assignment', $post->ID);
            } else{
                $course_id = tutor_utils()->get_course_id_by('lesson', $post->ID);
            }
        ?>

	    <?php do_action('tutor_lesson/single/before/lesson_sidebar'); ?>

        <div class="tutor-sidebar-tabs-wrap">
            <div class="tutor-tabs-btn-group">
                <a href="#tutor-lesson-sidebar-tab-content" class="active"> <i class="tutor-icon-education"></i> <span> <?php esc_html_e('Lesson List', 'tutor'); ?></span></a>
            </div>

            <div class="tutor-sidebar-tabs-content">

                <div id="tutor-lesson-sidebar-tab-content" class="tutor-lesson-sidebar-tab-item">
				    <?php
				    $topics = tutor_utils()->get_topics($course_id);
				    if ($topics->have_posts()){
					    while ($topics->have_posts()){ $topics->the_post();
						    $topic_id = get_the_ID();
						    $topic_summery = get_the_content();
						    ?>

                            <div class="tutor-topics-in-single-lesson tutor-topics-<?php echo $topic_id; ?>">
                                <div class="tutor-topics-title <?php echo $topic_summery ? 'has-summery' : ''; ?>">
                                    <h3>
									    <?php
									    the_title();
									    if($topic_summery) {
										    echo "<span class='toggle-information-icon'>&quest;</span>";
									    }
									    ?>
                                    </h3>
                                    <button class="tutor-single-lesson-topic-toggle"><i class="tutor-icon-plus"></i></button>
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

                                <div class="tutor-lessons-under-topic" style="display: none">
								    <?php
								    do_action('tutor/lesson_list/before/topic', $topic_id);

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

                                            <div class="tutor-course-lesson <?php echo ( $currentPost->ID === get_the_ID() ) ? 'active' : ''; ?>">
                                                <h5>
												    <?php
												    $lesson_title = "<i class='$lesson_icon'></i>";

												    $lesson_title .= get_the_title();
												    $lesson_title .= $play_time ? "<span class='tutor-lesson-duration'>".tutor_utils()->get_optimized_duration($play_time)."</span>" : '';
												    echo apply_filters('tutor_course/contents/lesson/title', $lesson_title, get_the_ID());
												    ?>
                                                </h5>
                                            </div>

										    <?php
									    }
									    $lessons->reset_postdata();
								    }
								    ?>

								    <?php do_action('tutor/lesson_list/after/topic', $topic_id); ?>
                                </div>
                            </div>

						    <?php
					    }
					    $topics->reset_postdata();
					    wp_reset_postdata();
				    }
				    ?>
                </div>

            </div>

        </div>

	    <?php do_action('tutor_lesson/single/after/lesson_sidebar'); ?>


        <!-- END: Sidebar -->




    </div>
    <div id="tutor-single-entry-content" class="tutor-lesson-content tutor-single-entry-content tutor-single-entry-content-<?php the_ID(); ?>">

		<?php //tutor_lesson_content(); ?>

        <?php

        $jsonData = array();
        $jsonData['post_id'] = get_the_ID();
        $jsonData['best_watch_time'] = 0;
        $jsonData['autoload_next_course_content'] = (bool) get_tutor_option('autoload_next_course_content');

        ?>

	    <?php do_action('tutor_lesson/single/before/content'); ?>

        <div class="tutor-single-page-top-bar">
            <div class="tutor-topbar-item tutor-hide-sidebar-bar">
                <a href="javascript:;" class="tutor-lesson-sidebar-hide-bar"><i class="tutor-icon-angle-left"></i> </a>
			    <?php $course_id = tutor_utils()->get_course_id_by('lesson', get_the_ID()); ?>
                <a href="<?php echo get_the_permalink($course_id); ?>" class="tutor-topbar-home-btn">
                    <i class="tutor-icon-home"></i> <?php echo __('Go to Course Home', 'tutor') ; ?>
                </a>
            </div>
            <div class="tutor-topbar-item tutor-topbar-content-title-wrap">
			    <?php
			    tutor_utils()->get_lesson_type_icon(get_the_ID(), true, true);
			    the_title(); ?>
            </div>
        </div>


        <div class="tutor-lesson-content-area">
            <input type="hidden" id="tutor_video_tracking_information" value="<?php echo esc_attr(json_encode($jsonData)); ?>">
		    <?php tutor_lesson_video(); ?>
		    <?php the_content(); ?>
		    <?php get_tutor_posts_attachments(); ?>
		    <?php tutor_next_previous_pagination(); ?>
        </div>

	    <?php do_action('tutor_lesson/single/after/content'); ?>






    </div>
</div>
<?php do_action('tutor_lesson/single/after/wrap');

get_tutor_footer();