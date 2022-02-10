<?php
$course_id = get_the_ID();

// Extract: $duration, $durationHours, $durationMinutes, $durationSeconds
extract(tutor_utils()->get_course_duration($course_id, true));

$benefits = get_post_meta($course_id, '_tutor_course_benefits', true);
$requirements = get_post_meta($course_id, '_tutor_course_requirements', true);
$target_audience = get_post_meta($course_id, '_tutor_course_target_audience', true);
$material_includes = get_post_meta($course_id, '_tutor_course_material_includes', true);
?>


<?php do_action('tutor_course_metabox_before_additional_data'); ?>

<div class="tutor-option-field-row">
    <div class="tutor-option-field-label">
        <label for=""><?php _e('Total Course Duration', 'tutor'); ?></label>
    </div>
    <div class="tutor-option-field">
        <div class="tutor-option-gorup-fields-wrap">
            <div class="tutor-lesson-video-runtime">

                <div class="tutor-option-group-field">
                    <input type="number" value="<?php echo $durationHours ? $durationHours : '00'; ?>" name="course_duration[hours]">
                    <p class="desc"><?php _e('HH', 'tutor'); ?></p>
                </div>
                <div class="tutor-option-group-field">
                    <input type="number" class="tutor-number-validation" data-min="0" data-max="59" value="<?php echo $durationMinutes ? $durationMinutes : '00'; ?>" name="course_duration[minutes]">
                    <p class="desc"><?php _e('MM', 'tutor'); ?></p>
                </div>

                <div class="tutor-option-group-field">
                    <input type="number" class="tutor-number-validation" data-min="0" data-max="59" value="<?php echo $durationSeconds ? $durationSeconds : '00'; ?>" name="course_duration[seconds]">
                    <p class="desc"><?php _e('SS', 'tutor'); ?></p>
                </div>

            </div>
        </div>

    </div>
</div>



<div class="tutor-option-field-row">
	<div class="tutor-option-field-label">
		<label for="">
            <?php _e('Benefits of the course', 'tutor'); ?>
        </label>
	</div>
	<div class="tutor-option-field tutor-option-tooltip">
		<textarea name="course_benefits" rows="2"><?php echo $benefits; ?></textarea>
		<p class="desc">
			<?php _e('List the knowledge and skills that students will learn after completing this course. (One per line)
', 'tutor'); ?>
		</p>
	</div>
</div>

<div class="tutor-option-field-row">
    <div class="tutor-option-field-label">
        <label for="">
			<?php _e('Requirements/Instructions', 'tutor'); ?> <br />
        </label>
    </div>
    <div class="tutor-option-field tutor-option-tooltip">
        <textarea name="course_requirements" rows="2"><?php echo $requirements; ?></textarea>

        <p class="desc">
			<?php _e('Additional requirements or special instructions for the students (One per line)', 'tutor'); ?>
        </p>
    </div>
</div>

<div class="tutor-option-field-row">
    <div class="tutor-option-field-label">
        <label for="">
			<?php _e('Targeted Audience', 'tutor'); ?> <br />
        </label>
    </div>
    <div class="tutor-option-field tutor-option-tooltip">
        <textarea name="course_target_audience" rows="2"><?php echo $target_audience; ?></textarea>

        <p class="desc">
			<?php _e('Specify the target audience that will benefit the most from the course. (One line per target audience.)', 'tutor'); ?>
        </p>
    </div>
</div>


<div class="tutor-option-field-row">
    <div class="tutor-option-field-label">
        <label for="">
			<?php _e('Materials Included', 'tutor'); ?> <br />
        </label>
    </div>
    <div class="tutor-option-field tutor-option-tooltip">
        <textarea name="course_material_includes" rows="2"><?php echo $material_includes; ?></textarea>

        <p class="desc">
			<?php _e('A list of assets you will be providing for the students in this course (One per line)', 'tutor'); ?>
        </p>
    </div>
</div>

<input type="hidden" name="_tutor_course_additional_data_edit" value="true" />

<?php do_action('tutor_course_metabox_after_additional_data'); ?>
