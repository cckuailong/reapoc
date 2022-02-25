<ul class="survey_optional_settings">
  <li>
    <label for="question-box-is-required-<?php echo $question['question_id']; ?>">
      <input id="question-box-is-required-<?php echo $question['question_id']; ?>" name="ps_questions[<?php echo $question['question_id']; ?>][required]" type="checkbox" value="1" <?php echo $question['required'] ? 'checked' : ''; ?>>
      <?php _e('Required', 'perfect-survey') ?>
    </label>
  </li>
</ul>
<ul class="survey_optional_settings">
  <li>
    <label for="question-box-add-media-<?php echo $question['question_id']; ?>">
      <img class="survey_image-block-settings" src="<?php echo!empty($question['image']) ? get_post($question['image'])->guid : '#'; ?>" style="display:<?php echo $question['image'] ? 'block' : 'none'; ?>; max-width: 130px; max-height: 130px;" />
      <input id="question-box-add-media-<?php echo $question['question_id']; ?>" name="ps_questions[<?php echo $question['question_id']; ?>][image]" type="hidden" value="<?php echo $question['image']; ?>" class="process_custom_images">
      <input id="question-box-add-media-<?php echo $question['question_id']; ?>-properties" name="ps_questions[<?php echo $question['question_id']; ?>][image_properties]" type="hidden" value="<?php echo htmlspecialchars(is_array($question['image_properties']) ? json_encode($question['image_properties']) : $question['image_properties']); ?>" class="process_custom_images_properties">
      <div class="ps_image_loader hidden"></div>
      <button class="button button-primary button-large answer-set-image-btn"><?php _e('Load image', 'perfect-survey') ?></button>
      <button class="button button-large answer-del-image-btn <?php if(empty($question['image'])) { ?>hidden<?php } ?>"><?php _e('Delete image', 'perfect-survey') ?></button>
    </label>
  </li>
</ul>
