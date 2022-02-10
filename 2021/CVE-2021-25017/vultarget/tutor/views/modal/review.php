<form class="tutor-component-popup-container tutor-course-review-popup-form">  
    <input type="hidden" name="tutor_course_id" value="<?php echo $course_id; ?>">          
    <div class="tutor-component-popup-40">                
        <div class="tutor-component-content-container tutor-star-rating-container">
            <h3><?php _e('How would you rate this course?', 'tutor'); ?></h3>
            <p><?php _e('Select Rating', 'tutor'); ?></p>

            <div class="tutor-form-group tutor-stars">
                <?php
                    tutor_utils()->star_rating_generator(tutor_utils()->get_rating_value());
                ?>
            </div>
            <div class="tutor-form-group">
                <textarea name="review" placeholder="<?php _e('Tell us about your own personal experience taking this course. Was it a good match for you?', 'tutor'); ?>"></textarea>
            </div>

            <div class="tutor-component-button-container">
                <button type="button" class="tutor-button tutor-button-secondary tutor_cancel_review_btn">
                    <?php _e('Cancel', 'tutor'); ?>
                </button>
                <button type="submit" class="tutor-button tutor-button-primary tutor_submit_review_btn">
                    <?php _e('Submit', 'tutor'); ?>
                </button>
            </div>        
        </div>        
    </div>        
</form>