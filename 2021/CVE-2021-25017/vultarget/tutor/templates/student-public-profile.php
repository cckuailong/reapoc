<?php
/**
 * Template for displaying student & instructor Public Profile.
 * It is used for both of instructor and student. Separate file has not been introduced due to complicacy and backward compatibility. -JK
 *
 * @since v.1.0.0
 *
 * @author Themeum
 * @url https://themeum.com
 *
 * @package TutorLMS/Templates
 * @version 1.4.3
 */

$user_name = sanitize_text_field(get_query_var('tutor_student_username'));
$get_user = tutor_utils()->get_user_by_login($user_name);

if(!is_object($get_user) || !property_exists($get_user, 'ID')){
    wp_redirect(get_home_url().'/404');
    exit;
}

$user_id = $get_user->ID;
$is_instructor = tutor_utils()->is_instructor($user_id);

$profile_layout = tutor_utils()->get_option(($is_instructor ? 'instructor' : 'student').'_public_profile_layout', 'pp-circle');
!$is_instructor ? $profile_layout='pp-circle' : 0; // For now

$tutor_user_social_icons = tutor_utils()->tutor_user_social_icons();

foreach ($tutor_user_social_icons as $key => $social_icon){
    $url = get_user_meta($user_id, $key, true);
    $tutor_user_social_icons[$key]['url']=$url;
}

get_header();
?>

<?php
    // Rating content 
    ob_start();
    if($is_instructor){
        $instructor_rating = tutor_utils()->get_instructor_ratings($user_id);
        ?>
        <div class="tutor-rating-container">      
            <div class="ratings">
                <span class="rating-generated">
                    <?php tutor_utils()->star_rating_generator($instructor_rating->rating_avg); ?>
                </span>

                <?php
                echo " <span class='rating-digits'>{$instructor_rating->rating_avg}</span> ";
                echo " <span class='rating-total-meta'>({$instructor_rating->rating_count})</span> ";
                ?>
            </div>
        </div>
        <?php
    }
    $rating_content=ob_get_clean();


    // Social media content
    ob_start();
    foreach ($tutor_user_social_icons as $key => $social_icon){
        $url = $social_icon['url'];
        echo !empty($url) ? '<a href="'.$url.'" target="_blank" rel="noopener noreferrer nofollow" class="'.$social_icon['icon_classes'].'" title="'.$social_icon['label'].'"></a>' : '';
    }
    $social_media=ob_get_clean();
?>

<?php do_action('tutor_student/before/wrap'); ?>

    <div <?php tutor_post_class('tutor-full-width-student-profile tutor-page-wrap tutor-user-public-profile tutor-user-public-profile-'.$profile_layout); ?>>
        <div class="tutor-container photo-area">
            <div class="cover-area">
                <div style="background-image:url(<?php echo tutor_utils()->get_cover_photo_url($user_id); ?>)"></div>
                <div></div>
            </div>
            <div class="pp-area">
                <div class="profile-pic" style="background-image:url(<?php echo get_avatar_url($user_id, array('size' => 150)); ?>)"></div>
                
                <div class="profile-name">
                    <div class="profile-rating-media content-for-mobile">
                        <?php echo $rating_content; ?>
                        <div class="tutor-social-container content-for-desktop">
                            <?php echo $social_media; ?>
                        </div>
                    </div>

                    <h3><?php echo $get_user->display_name; ?></h3>
                    <?php
                        if($is_instructor){
                            $course_count = tutor_utils()->get_course_count_by_instructor($user_id);
                            $student_count = tutor_utils()->get_total_students_by_instructor($user_id);
                            ?>
                            <span>
                                <span><?php echo $course_count; ?></span> 
                                <?php $course_count>1 ? _e('Courses', 'tutor') : _e('Course', 'tutor'); ?>
                            </span>
                            <span><span>•</span></span>
                            <span>
                                <span><?php echo $student_count;?></span> 
                                <?php $student_count>1 ? _e('Students', 'tutor') : _e('Student', 'tutor'); ?>
                            </span>
                            <?php
                        }
                        else{                            
                            $enrolled_course = tutor_utils()->get_enrolled_courses_by_user($user_id);
                            $enrol_count = is_object($enrolled_course) ? $enrolled_course->found_posts : 0;

                            $complete_count = tutor_utils()->get_completed_courses_ids_by_user($user_id);
                            $complete_count = $complete_count ? count($complete_count) : 0;
                            ?>
                            <span>
                                <span><?php echo $enrol_count; ?></span> 
                                <?php $enrol_count>1 ? _e('Courses Enrolled', 'tutor') : _e('Course Enrolled', 'tutor'); ?>
                            </span>
                            <span><span>•</span></span>
                            <span>
                                <span><?php echo $complete_count; ?></span> 
                                <?php $complete_count>1 ? _e('Courses Completed', 'tutor') : _e('Course Completed', 'tutor'); ?>
                            </span>
                            <?php
                        }
                    ?>
                </div>

                <div class="tutor-social-container content-for-mobile">
                    <?php echo $social_media; ?>
                </div>
                
                <div class="profile-rating-media content-for-desktop">
                    <?php echo $rating_content; ?>
                    <div class="tutor-social-container content-for-desktop">
                        <?php    
                            foreach ($tutor_user_social_icons as $key => $social_icon){
                                $url = $social_icon['url'];
                                echo !empty($url) ? '<a href="'.$url.'" target="_blank" rel="noopener noreferrer nofollow" class="'.$social_icon['icon_classes'].'" title="'.$social_icon['label'].'"></a>' : '';
                            }
                        ?>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="tutor-container" style="overflow:auto">
            <div class="tutor-user-profile-sidebar">
                <?php // tutor_load_template('profile.badge', ['profile_badges'=>(new )]); ?>
            </div>
            <div class="tutor-user-profile-content">
            
                <h3><?php _e('Biography', 'tutor'); ?></h3>
                <?php tutor_load_template('profile.bio'); ?>
                
                <?php
                    if($is_instructor){
                        ?>
                            <h3><?php _e('Courses', 'tutor'); ?></h3>
                            <?php 
                                add_filter('courses_col_per_row', function(){
                                    return 3;
                                });

                                tutor_load_template('profile.courses_taken'); 
                            ?>

                            <?php 
                                /* if(tutor_utils()->get_option('show_courses_completed_by_student')){
                                    echo '<h3>',__('Course Completed', 'tutor'),'</h3>';
                                    tutor_load_template('profile.enrolled_course');
                                } */
                            ?>

                            <?php 
                                /* if(tutor_utils()->get_option('students_own_review_show_at_profile')){
                                    echo '<h3>',__('Reviews Wrote', 'tutor'),'</h3>';
                                    tutor_load_template('profile.reviews_wrote');
                                } */
                            ?>
                        <?php
                    }
                ?>
            </div>
        </div>
    </div>
<?php do_action('tutor_student/after/wrap'); ?>

<?php
get_footer();
