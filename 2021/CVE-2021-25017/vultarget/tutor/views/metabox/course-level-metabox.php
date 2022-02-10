
<?php
    $course_id = get_the_ID();
    $levels = tutor_utils()->course_levels();
    $course_level = get_post_meta($course_id, '_tutor_course_level', true);
?>
<div class="tutor-option-field-row">
    <div class="tutor-option-field-label">
        <label for="">
            <?php _e('Difficulty Level', 'tutor'); ?> <br />
        </label>
    </div>
    <div class="tutor-option-field tutor-course-level-meta">
        <?php
        foreach ($levels as $level_key => $level){
            ?>
            <label> <input type="radio" name="course_level" value="<?php echo $level_key; ?>" <?php ($course_level ? checked($level_key,
                    $course_level) : $level_key === 'intermediate') ? checked(1, 1): ''; ?> > <?php
                echo
                $level; ?> </label>
            <?php
        }
        ?>
    </div>
</div>