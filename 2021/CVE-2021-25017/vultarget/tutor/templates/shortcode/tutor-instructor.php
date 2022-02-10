<div class="tutor-courses tutor-courses-loop-wrap tutor-courses-layout-<?php echo $column_count; ?>">
    <?php
        foreach($instructors as $instructor){

            $course_count = tutor_utils()->get_course_count_by_instructor($instructor->ID);
            $instructor_rating = tutor_utils()->get_instructor_ratings($instructor->ID);
            ?>
            <div class="tutor-course-col-<?php echo $column_count; ?>">
                <a href="<?php echo tutor_utils()->profile_url($instructor->ID); ?>" class="tutor-course tutor-course-loop tutor-instructor-list tutor-instructor-list-<?php echo $layout; ?> tutor-instructor-list-<?php echo $instructor->ID; ?>">
                    <div class="tutor-instructor-cover-photo" style="background-image:url(<?php echo tutor_utils()->get_cover_photo_url($instructor->ID); ?>)"></div>
                    <div class="tutor-instructor-profile-photo" style="background-image:url(<?php echo get_avatar_url($instructor->ID, array('size'=>500)); ?>)"></div>                    
                    <div class="tutor-instructor-rating">
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
                    <h4 class="tutor-instructor-name"><?php echo $instructor->display_name; ?></h4>
                    <div class="tutor-instructor-course-count">
                        <span><?php echo $course_count; ?></span>
                        <span><?php $course_count>1 ? _e('Courses', 'tutor') : _e('Course', 'tutor'); ?></span>
                    </div>
                </a>
            </div>
            <?php
        }

        if(!count($instructors)){
            echo '<div>', __('No Instructor Found', 'tutor'), '</div>';
        }
    ?>
</div>

<?php
    if($previous_page || $next_page) {
        $prev_url = !$show_filter ? '?instructor-page='.$previous_page : '#';
        $next_url = !$show_filter ? '?instructor-page='.$next_page : '#';
        ?>
        <div class="tutor-pagination-wrap">
            <?php 
                echo $previous_page ? '<a class="page-numbers" href="'.$prev_url.'" data-page_number="'.$previous_page.'">« '.__('Previous', 'tutor').'</a>' : '';
                echo $next_page ? '&nbsp; <a class="next page-numbers" href="'.$next_url.'" data-page_number="'.$next_page.'">'.__('Next', 'tutor').' »</a>' : '';
            ?> 
        </div>
        <?php
    }
?>