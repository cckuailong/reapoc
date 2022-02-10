<div class="tutor-option-field-row">
    <div class="tutor-option-field-label">
        <label for=""><?php _e('Select Course', 'tutor'); ?></label>
    </div>
    <div class="tutor-option-field">
        <?php
        $courses = tutor_utils()->get_courses_for_instructors();
        ?>

        <select name="selected_course" class="tutor_select2">
            <option value=""><?php _e('Select a course', 'tutor'); ?></option>

	        <?php
            $course_id = tutor_utils()->get_course_id_by('lesson', get_the_ID());
	        foreach ($courses as $course){
		        echo "<option value='{$course->ID}' ".selected($course->ID, $course_id)." >{$course->post_title}</option>";
	        }
	        ?>
        </select>

        <p class="desc">
            <?php _e('Choose the course for this lesson', 'tutor'); ?>
        </p>
    </div>
</div>